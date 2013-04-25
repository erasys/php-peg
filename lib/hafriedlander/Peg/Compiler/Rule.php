<?php

namespace hafriedlander\Peg\Compiler;

use hafriedlander\Peg\Compiler\Rule\PendingState;
use hafriedlander\Peg\Exception\GrammarException;


/**
 * Rule parsing and code generation
 *
 * A rule is the basic unit of a PEG.
 * This parses one rule, and generates a function that will match on a string
 *
 * @author Hamish Friedlander
 */
class Rule extends PHPWriter
{
    const RULE_RX = '@
        (?<name> [\w-]+)                        # The name of the rule
        (\s+ extends \s+ (?<extends>[\w-]+))?   # The extends word
        (\s* \( (?<arguments>.*) \))?           # Any variable setters
        (
            \s*(?<matchmark>:) |              # Marks the matching rule start
            \s*(?<replacemark>;) |            # Marks the replacing rule start
            \s*$
        )
        (?<rule>[\s\S]*)
        @x';

    const ARGUMENT_RX = '@
        ([^=]+)    # Name
        =            # Seperator
        ([^=,]+)   # Variable
        (,|$)
        @x';

    const REPLACEMENT_RX = '@
        (([^=]|=[^>])+)    # What to replace
        =>                # The replacement mark
        ([^,]+)            # What to replace it with
        (,|$)
        @x';

    const FUNCTION_RX = '@^\s+function\s+([^\s(]+)\s*(.*)@';

    const REGEX_RX = '@\G
        /
        ((?:
        (?:(?:\\\\\\\\)*\\\\/)  # Escaped \/, making sure to catch all the \\ first, so that we dont think \\/ is an escaped /
        |
        [^/]                    # Anything except /
    )*)
    /
    ([imsxADSUJu]*)?          # PCRE only flags
    @xu';

    const LITERAL_RX = '@\G
        (["\'])             # A string delimiter
        (
            (?:               # 0 or more:
            \\\\ .          # backslah followed by anything
            |               # or
            (?: (?!\1). )   # not the delimiter followed by anything
        )*
    )
    \1                  # the closing delimiter
    (                   # optionally:
        !!                # the cancel-all flag
        |                 # or
        (?: !? [a-z] )*   # any number of positive or negative flags
    )?
    @xiu';

    const CLOSURE_RX = '@\G
        \{\{                        # two opening brackets
        (
            (?: (?<! \}\} ) . )*    # anything not preceded by two closing brackets
        )    
        \}\}                        # two closing brackets
    @xs';

    protected $parser;
    protected $lines;

    public $name;
    public $extends;
    public $mode;
    public $rule;

    protected $closure_count = 0;

    public function __construct(RuleSet $parser, $lines)
    {
        $this->parent = $parser;
        $this->lines = $lines;

        $this->closures = array();

        // Find the first line (if any) that's an attached function definition.
        // Can skip first line (unless this block is malformed)
        for ($i = 1; $i < count($lines); $i++) {
            if (preg_match(self::FUNCTION_RX, $lines[$i])) break;
        }

        // Then split into the two parts
        $spec = array_slice($lines, 0, $i);
        $funcs = array_slice($lines, $i);

        // Parse out the spec
        $spec = implode("\n", $spec);
        if (!preg_match(self::RULE_RX, $spec, $specmatch)) {
            throw new GrammarException('Malformed rule spec: ' . $spec);
        }

        $this->name = $specmatch['name'];

        if ($specmatch['extends']) {
            $this->extends = $this->parent->rules[$specmatch['extends']];
            if (!$this->extends) {
                throw new GrammarException(
                    'Extended rule '.$specmatch['extends'].' is not defined before being extended'
                );
            }
        }

        $this->arguments = array();

        if ($specmatch['arguments']) {
            preg_match_all(self::ARGUMENT_RX, $specmatch['arguments'], $arguments, PREG_SET_ORDER);
            foreach ($arguments as $argument){
                $this->arguments[trim($argument[1])] = trim($argument[2]);
            }
        }

        $this->mode = $specmatch['matchmark'] ? 'rule' : 'replace';

        if ('rule' === $this->mode) {

            $this->rule = $specmatch['rule'];
            $this->parse_rule();

        } else {

            if (!$this->extends) {
                throw new GrammarException(
                    'Replace matcher, but not on an extends rule'
                );
            }

            $this->replacements = array();
            preg_match_all(self::REPLACEMENT_RX, $specmatch['rule'], $replacements, PREG_SET_ORDER);

            $rule = $this->extends->rule;

            foreach ($replacements as $replacement) {
                $search = trim($replacement[1]);
                $replace = trim($replacement[3]); if ($replace == "''" || $replace == '""') $replace = "";

                $rule = str_replace($search, ' '.$replace.' ', $rule);
            }

            $this->rule = $rule;
            $this->parse_rule();

        }

        // Parse out the functions

        $this->functions = array();

        $active_function = null;

        foreach($funcs as $line) {
            /* Handle function definitions */
            if (preg_match(self::FUNCTION_RX, $line, $func_match, 0)) {
                $active_function = $func_match[1];
                $this->functions[$active_function] = $func_match[2] . PHP_EOL;
            } else {
                $this->functions[$active_function] .= $line . PHP_EOL;
            }
        }
    }

    /* Manual parsing, because we can't bootstrap ourselves yet */
    public function parse_rule()
    {
        $rule = trim($this->rule);
        $tokens = array();
        $this->tokenize($rule, $tokens);
        $this->parsed = (1 === count($tokens) ? array_pop($tokens) : new Token\Sequence($tokens));
    }

    public function tokenize($str, &$tokens, $o = 0)
    {
        $length = strlen($str);
        $pending = new PendingState();

        while ($o < $length) {
            //$sub = substr($str, $o);

            /* Absorb white-space */
            if (preg_match('/\G\s+/', $str, $match, 0, $o)) {
                $o += strlen($match[0]);
            }
            /* Handle expression labels */
            elseif (preg_match('/\G(\w*):/', $str, $match, 0, $o)) {
                $pending->set('tag', isset($match[1]) ? $match[1] : '');
                $o += strlen($match[0]);
            }
            /* Handle descent token */
            elseif (preg_match('/\G[\w-]+/', $str, $match, 0, $o)) {
                $tokens[] = $t = new Token\Recurse($match[0]);
                $pending->apply_if_present($t);
                $o += strlen($match[0]);
            }
            /* Handle " quoted literals */
            elseif (preg_match(self::LITERAL_RX, $str, $match, 0, $o)) {
                $quotechar = $match[1];
                $literal = $match[2];
                $flags = array(
                    'i' => $this->parent->hasFlag(RuleSet::F_CASE_INSENSITIVE),
                    'u' => $this->parent->hasFlag(RuleSet::F_UNICODE),
                    'n' => $this->parent->hasFlag(RuleSet::F_NORMALIZE_LITERALS)
                );
                $local_flags = null;
                // local flags override globals
                if (isset($match[3])) {
                    if ('!!' === $match[3]) {
                        // unset all global flags
                        foreach($flags as $flag) $flag = false;
                    } else {
                        $local_flags = preg_split(
                            '/(!?[a-z])/i', $match[3], null,
                            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
                        );
                        foreach ($local_flags as $flag) {
                            if (0 === strpos($flag, '!')) {
                                $flags[substr($flag, 1, 1)] = false;
                            } else {
                                $flags[$flag] = true;
                            }
                        }
                    }
                }

                $tokens[] = $t = new Token\Literal($quotechar, $literal, $flags);
                $pending->apply_if_present($t);
                $o += strlen($match[0]);
            }
            /* Handle regexs */
            elseif (preg_match(self::REGEX_RX, $str, $match, 0, $o)) {
                $pattern = $match[1];
                // handle PCRE flags
                $flags = array();
                // local flags
                if (isset($match[2])) {
                    $flags = str_split($match[2]);
                }
                // global flags
                if ($this->parent->hasFlag(RuleSet::F_CASE_INSENSITIVE)) {
                    $flags[] = 'i';
                }
                if ($this->parent->hasFlag(RuleSet::F_UNICODE)) {
                    $flags[] = 'u';
                }
                // ensure x & S options
                array_push($flags, 'x', 'S');
                // ensure unique options
                $flags = implode('', array_unique($flags));

                $tokens[] = $t = new Token\Regex("/$pattern/$flags");
                $pending->apply_if_present($t);
                $o += strlen($match[0]);
            }
            /* Handle $ call literals */
            elseif (preg_match('/\G\$(\w+)/', $str, $match, 0, $o)) {
                $tokens[] = $t = new Token\ExpressionedRecurse($match[1]);
                $pending->apply_if_present($t);
                $o += strlen($match[0]);
            }
            /* Handle flags */
            elseif (preg_match('/\G@(\w+)/', $str, $match, 0, $o)) {
                $l = count($tokens) - 1;
                $o += strlen($match[0]);
                user_error("TODO: Flags not currently supported", E_USER_WARNING);
            }
            /* Handle closures */
            elseif (preg_match(self::CLOSURE_RX, $str, $match, 0, $o)) {
                $name = $this->name . '_closure_' . $this->closure_count++;
                $body = $match[1];
                $this->closures[$name] = $body;
                $tokens[] = $t = new Token\Closure($name);
                $pending->apply_if_present($t);
                $o += strlen($match[0]);
            }
            /* Handle control tokens */
            else {
                $c = substr($str, $o, 1);
                $l = count($tokens) - 1;
                $o += 1;
                switch($c) {
                    case '?':
                        $tokens[$l]->quantifier = array('min' => 0, 'max' => 1);
                        break;
                    case '*':
                        $tokens[$l]->quantifier = array('min' => 0, 'max' => null);
                        break;
                    case '+':
                        $tokens[$l]->quantifier = array('min' => 1, 'max' => null);
                        break;
                    case '{':
                        if (preg_match('/\G\{([0-9]+)(,([0-9]*))?\}/', $str, $matches, 0, $o - 1)) {
                            $min = $max = (int) $matches[1];
                            if(isset($matches[2])) {
                                $max = $matches[3] ? (int) $matches[3] : null;
                            }
                            $tokens[$l]->quantifier = array('min' => $min, 'max' => $max);
                            $o += strlen($matches[0]) - 1;
                        }
                        break;

                    case '&':
                        $pending->set('positive_lookahead');
                        break;
                    case '!':
                        $pending->set('negative_lookahead');
                        break;

                    case '.':
                        $pending->set('silent');
                        break;

                    case '[':
                    case ']':
                        $tokens[] = new Token\Whitespace(false);
                        break;
                    case '<':
                    case '>':
                        $tokens[] = new Token\Whitespace(true);
                        break;

                    case '(':
                        $subtokens = array();
                        $o = $this->tokenize($str, $subtokens, $o);
                        $tokens[] = $t = new Token\Sequence($subtokens);
                        $pending->apply_if_present($t);
                        break;
                    case ')':
                        return $o;

                    case '|':
                        $option1 = $tokens;
                        $option2 = array();
                        $o = $this->tokenize($str, $option2, $o);

                        $option1 = (count($option1) == 1) ? $option1[0] : new Token\Sequence($option1);
                        $option2 = (count($option2) == 1) ? $option2[0] : new Token\Sequence($option2);

                        $pending->apply_if_present($option2);

                        $tokens = array(new Token\Option($option1, $option2));
                        return $o;

                    default:
                        user_error("Can't parse '$c' - attempting to skip", E_USER_WARNING);
                        //var_dump(substr($str, $o, 12), $str);
                        break;
                }
            }
        }

        return $o;
    }

    /**
     * Generate the PHP code for a function to match against a string for this rule
     */
    public function compile($indent)
    {
        $function_name = $this->function_name($this->name);

        // Build the typestack
        $typestack = array(); $class=$this;
        do {
            $typestack[] = $this->function_name($class->name);
        }
        while($class = $class->extends);

        $typestack = "array('" . implode("','", $typestack) . "')";

        // Build an array of additional arguments to add to result node (if any)
        if (empty($this->arguments)) {
            $arguments = 'null';
        } else {
            $arguments = "array(";
            foreach ($this->arguments as $k => $v) {
                $arguments .= "'$k' => '$v'";
            }
            $arguments .= ")";
        }

        $match = PHPBuilder::build();

        $match->l("protected \$match_{$function_name}_typestack = $typestack;");

        $match->b(
            'protected function match_'.$function_name.'($stack = array())',
            '$matchrule = "'.$function_name.'";',
            '$result = $this->construct($matchrule, $matchrule, '.$arguments.');',
            $this->parsed->compile()->replace(array(
                'MATCH' => 'return $this->finalise($result);',
                'FAIL' => 'return false;'
            ))
        );

        $closures = array();
        foreach ($this->closures as $name => $body) {
            $def = "protected function ".$name.'($value="") {';
            $def .= $body;
            $def .= '}';
            $closures[] = $def;
        }
        

        $functions = array();
        foreach($this->functions as $name => $function) {
            $function_name = $this->function_name(
                preg_match('/^_/', $name) ? $this->name.$name : $this->name.'_'.$name
            );
            $functions[] = implode(PHP_EOL, array(
                'public function ' . $function_name . ' ' . $function
            ));
        }

        // print_r($match); return '';
        return $match->render(null, $indent)
            . PHP_EOL . PHP_EOL
            . implode(PHP_EOL, $closures)
            . PHP_EOL . PHP_EOL
            . implode(PHP_EOL, $functions);
    }
}
