<?php
require_once __DIR__.'/../autoload.php';

use ju1ius\Peg\Compiler;

$peg = __DIR__.'/literal.peg.php';
$output = __DIR__.'/LiteralParser.php';

$code = Compiler::compile(file_get_contents($peg));
file_put_contents($output, $code);
require $output;


$p = new LiteralParser('hello world');
var_dump( $p->match_hello_world() ) ;

$p = new LiteralParser('hello world hello');
var_dump( $p->match_enclosed_world() ) ;
