<?php

namespace hafriedlander\Peg\Compiler\Token;

use hafriedlander\Peg\Compiler;
use hafriedlander\Peg\Compiler\Token;
use hafriedlander\Peg\Compiler\PHPBuilder;

class Recurse extends Token
{
    public function __construct($value)
    {
        parent::__construct('recurse', $value);
    }

    public function match_function($value)
    {
        return "'".$this->function_name($value)."'";
    }

    public function match_code($value)
    {
        $function = $this->match_function($value);


        $debug_header = Compiler::debug_header();
        $debug_match = Compiler::debug_match();
        $debug_fail = Compiler::debug_fail();
        if (Compiler::$debug) {
            $debug_header->l(
                '$debug_sub = (strlen($this->string) - $this->pos > 20)',
                '  ? (substr($this->string, $this->pos, 20) . " [...]")',
                '  : substr($this->string, $this->pos);',
                Compiler::debug_escape_nl('$debug_sub', '$debug_sub'),
                'printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);'
            );
        }

        /**
         * Storage logic
         * if silent flag is applied, callbacks and result text are ignored
         **/
        $storage = null;
        if (!$this->silent) {
            $storetag = $this->function_name(
                $this->tag ? $this->tag : $function
            );
            $storage = false === $this->tag
                ? '$this->store($result, $subres);'
                : '$this->store($result, $subres, "'.$storetag.'");';
        }

        return PHPBuilder::build()->l(
            '$matcher = \'match_\'.'.$function.';',
            '$key = $matcher; $pos = $this->pos;',
            $debug_header,
            '$subres = $this->packhas($key, $pos)',
            '  ? $this->packread($key, $pos)',
            '  : $this->packwrite($key, $pos,',
            '      $this->$matcher(array_merge($stack, array($result)))',
            '    );',
            $this->match_fail_conditional(
                'false !== $subres',
                PHPBuilder::build()->l(
                    $debug_match,
                    $storage
                ),
                PHPBuilder::build()->l($debug_fail)
            ));
    }
}
