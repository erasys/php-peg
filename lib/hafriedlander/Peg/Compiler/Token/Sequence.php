<?php

namespace hafriedlander\Peg\Compiler\Token;

use hafriedlander\Peg\Compiler\Token;
use hafriedlander\Peg\Compiler\PHPBuilder;

class Sequence extends Token
{
    public function __construct($value)
    {
        parent::__construct('sequence', $value);
    }

    public function match_code($value)
    {
        $code = PHPBuilder::build();
        foreach($value as $token) {
            $code->l(
                $token->compile()->replace(array(
                    'MATCH' => null,
                    'FAIL' => 'FBREAK'
                ))
            );
        }
        $code->l('MBREAK');

        return $this->match_fail_block($code);
    }
}
