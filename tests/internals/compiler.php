<?php

require_once __DIR__.'/../autoload.php';

use ju1ius\Peg\Compiler;

$str = <<<EOS
/*!* FooBar @unicode @case_insensitive 

rule: "Foo"{0,1}
rule2: "Bar"!u

*/
EOS;

//var_dump(Compiler::compile($str));
Compiler::compile($str);

