<?php

namespace hafriedlander\Peg\Compiler\Token;

class Closure extends Terminal
{
    public function __construct($name)
    {
        $this->type = $name;
    }
    
    public function match_code($value)
    {
        return parent::match_code('');
    }
    
}
