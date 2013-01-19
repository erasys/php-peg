<?php

require_once __DIR__.'/../../autoloader.php';

use hafriedlander\Peg\Compiler;

$str = <<<'EOS'
/*!* FooBar 

date_field: "Date:"> date_time

date_time: {{
    $eol = strpos($this->string, "\n", $this->pos);
    if (false === $eol) return false;
    $value = substr($this->string, $this->pos, $eol);
    try {
        $d = new \DateTime($value);
    } catch (\Exception $e) {
        return false;
    }
    $this->pos += strlen($value);
    return $d->format('r');
    }}
*/
EOS;

$code = "use hafriedlander\Peg\Parser;
class ClosureTest extends Parser\Basic
{
" . Compiler::compile($str) . "
}";
eval($code);

//file_put_contents(__DIR__.'/ClosureTest.php','<?php'.$code);

$p = new ClosureTest("Date: 24-12-1979\n");
var_dump($p->match_date_field());

//Compiler::compile($str);
