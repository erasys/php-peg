<?php
require_once __DIR__.'/../autoload.php';

use ju1ius\Peg\Compiler;

$peg = __DIR__.'/../../examples/Rfc822.peg.inc';
$output = __DIR__.'/Rfc822Parser.php';

$code = Compiler::compile(file_get_contents($peg));
file_put_contents($output, $code);
require $output;

//$p = new Rfc822(<<<EOS
//Mary Smith <mary@x.test>, jdoe@example.org, Who? <one@y.test>, <boss@nil.test>, "Giant; \"Big\" Box" <sysservices@example.net>, A Group:Ed Jones <c@a.test>, Undisclosed recipients:, Pete <pete@silly.example>
//EOS
//);
$p = new Rfc822(<<<EOS
A Group:Ed Jones <c@a.test>;, Undisclosed recipients:;, Pete <pete@silly.example>
EOS
);
print_r( $p->match_address_header() ) ;
