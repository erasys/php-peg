<?php

require_once __DIR__.'/../autoloader.php';

use hafriedlander\Peg\Compiler;

$str = <<<EOS
/*!* FooBar 

optional: "Foo"{0,1}
zero_or_more: "Bar"{0,}
one_or_more: "Bar"{1,}
strict: "Baz"{2,4} one
one: "Baz"
*/
EOS;

eval('use ju1ius\Peg\Parser;
class Qparser extends Parser {
'.Compiler::compile($str).'
}');
$p = new Qparser("BazBazBazBazBaz");
var_dump($p->match_strict());

//Compiler::compile($str);
