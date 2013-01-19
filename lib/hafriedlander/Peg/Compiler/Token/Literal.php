<?php

namespace hafriedlander\Peg\Compiler\Token;

use hafriedlander\Peg\Compiler;
use hafriedlander\Peg\Compiler\PHPBuilder;

class Literal extends Expressionable
{
    protected $flags = array(
        'i' => false,
        'u' => false,
        'n' => false
    );

    /**
     * Creates a new literal token
     *
     * Available flags:
     * * i => case insensitive literal.
     * * u => unicode literal.
     * * n => normalize literal.
     *        The literal will return the value specified in the grammar,
     *        rather than the value found in the source string.
     *        Useful only in conjunction with the "i" flag.
     * 
     * @param string $quotechar The quote char used by this literal (" or ')
     * @param string $value The value of the literal
     * @param array  $flags The flags for this literal
     *
     **/
    public function __construct($quotechar, $value, array $flags)
    {
        $this->quotechar = $quotechar;
        $this->value = $value;
        $this->flags = $flags;
        if ('"' === $quotechar) {
            $value = addcslashes($value, "'");
        } else {
            //$value = stripcslashes($value);
        }
        //parent::__construct('literal', "'" . substr($value,1,-1) . "'");
        //var_dump($value);
        parent::__construct('literal', "'" . $value . "'");
    }

    public function match_code($value)
    {
        // Convert hex char sequences
        $value = self::convertSpecialSequences($value);

        $debug_header = Compiler::debug_header();
        $debug_match = Compiler::debug_match();
        $debug_fail = Compiler::debug_fail();
        $debug_flags = '';
        if (Compiler::$debug) {
            $debug_flags = implode('', array_keys(array_filter($this->flags)));
            if ($debug_flags) $debug_flags = "($debug_flags)";
        }

        // We inline raw literal matches for speed
        if (!$this->contains_expression($value)) {
            // strip out quotes
            $literal_value = trim($value, "'");
            $len = eval("return strlen($value);");

            $subres = '$subres = substr($this->string, $this->pos, '.$len.');';
            $cond = $value.' === $subres';

            if (Compiler::$debug) {
                $debug_header->l(
                    Compiler::debug_escape_nl('$debug_sub', '$subres'),
                    'printf("%sMatching literal '
                    . addcslashes($value, '"') . $debug_flags
                    . ' against \'%s\'\n", $indent, $debug_sub);'
                );
            }

            if ($this->flags['i']) {
                if ($this->flags['u']) {
                    $cond = sprintf(
                        '\'%s\' ===  mb_strtolower($subres, "utf-8")',
                        mb_strtolower($literal_value, "utf-8")
                    );
                } else {
                    $cond = sprintf('0 === strcasecmp(%s, $subres)', $value);
                }
            }
            return PHPBuilder::build()->l(
                $subres,
                $debug_header,
                $this->match_fail_conditional(
                    $cond,
                    PHPBuilder::build()->l(
                        $debug_match,
                        '$this->pos += '.$len.';',
                        $this->set_text($this->flags['n'] ? $value : '$subres')
                    ),
                    PHPBuilder::build()->l($debug_fail)
                )
            );
        }
        // then handle literals containing expressions
        $value = $this->replace_expression($value);

        $cond = '$expr_value === $subres';
        $expr_len = '$expr_len = strlen($expr_value);';
        $subres = '$subres = substr($this->string, $this->pos, $expr_len);';

        if (Compiler::$debug) {
            $debug_header->l(
                Compiler::debug_escape_nl('$debug_sub', '$subres'),
                'printf("%sMatching literal \'%s\''
                . $debug_flags
                . ' against \'%s\'\n", $indent, $expr_value, $debug_sub);'
            );
        }

        if ($this->flags['i']) {
            if ($this->flags['u']) {
                $cond = 'mb_strtolower($expr_value, "utf-8") === mb_strtolower($subres, "utf-8")';
            } else {
                $cond = '0 === strcasecmp($expr_value, $subres)';
            }
        }
        // FIXME: is it safe to assume that two strings which differ only in case
        // have the same byte length ?
        return PHPBuilder::build()->l(
            '$expr_value = '.$value.';',
            $expr_len,
            $subres,
            $debug_header,
            $this->match_fail_conditional(
                $cond,
                PHPBuilder::build()->l(
                    $debug_match,
                    '$this->pos += $expr_len;',
                    $this->set_text($this->flags['n'] ? '$expr_value' : '$subres')
                ),
                PHPBuilder::build()->l($debug_fail)
            )
        );
    }

    public static function convertSpecialSequences($str)
    {
        $str = preg_replace_callback(
            "@(?<!\\\\)\\\\((?:\\\\\\\\)*)  # odd number of backslashes
            (?:                             # followed by
            x([0-9a-fA-F]{1,2})           # hex escape sequence
            |([nrtvef])                   # or control char escape sequence
            |([0-7]{1,3})                 # or octal escape sequence
        )@x",
        function($matches) {
            $res = str_repeat('\\', strlen($matches[1]) / 2);
            //var_dump($matches);
            if (isset($matches[4])) {
                $res .= chr(octdec($matches[4]));
            } else if (isset($matches[3])) {
                switch($matches[3]) {
                    case 'n':
                        $res .= "\n";
                        break;
                    case 'r':
                        $res .= "\r";
                        break;
                    case 't':
                        $res .= "\t";
                        break;
                    case 'v':
                        $res .= "\v";
                        break;
                    case 'e':
                        $res .= "\e";
                        break;
                    case 'f':
                        $res .= "\f";
                        break;
                }
            } else if (isset($matches[2])) {
                $res .= chr(hexdec($matches[2]));
            } else {
                $res = $matches[0];
            }
            return $res;
        },
            $str
        );


        $str = preg_replace_callback(
            "@(?<!\\\\)((?:\\\\\\\\)+)  # even number of backslashes
            (                           # followed by
                x[0-9a-fA-F]{1,2}         # hex sequence
                |[nrtvef]                 # or ctrl char
                |[0-7]{1,3}               # or octal sequence
            )@x",
            function($matches) {
                return str_repeat('\\', strlen($matches[1]) / 2) . $matches[2];
            },
                $str
            );

        return $str;
    }
}
