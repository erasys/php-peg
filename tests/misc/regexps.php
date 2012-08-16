<?php
require_once __DIR__.'/../autoload.php';

use ju1ius\Peg\Compiler;

$peg = __DIR__.'/regexps.peg.php';
$output = __DIR__.'/RegexpTestParser.php';

$code = Compiler::compile(file_get_contents($peg));
file_put_contents($output, $code);
require $output;


$p = new RegexpTestParser('hello world');
var_dump( $p->match_hello_world() ) ;

