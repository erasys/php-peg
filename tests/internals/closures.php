<?php

require_once __DIR__.'/../autoload.php';

use ju1ius\Peg\Compiler;

$str = <<<EOS
/*!* FooBar 

date_field: "Date:"> date_time
date_time: {{
  try {
    $d = new \DateTime($value);
    return $d;
  } catch (\Exception $e) {
    return false;
  }
}}
*/
EOS;

eval('
use ju1ius\Peg\Parser;
class Qparser extends Parser {
'.Compiler::compile($str).'
}');
$p = new Qparser("BazBazBazBazBaz");
var_dump($p->match_strict());

//Compiler::compile($str);
