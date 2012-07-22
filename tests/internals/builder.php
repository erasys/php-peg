<?php

require_once __DIR__.'/../autoload.php';

use ju1ius\Peg\Compiler\Builder;

$b = new Builder();

var_dump($b->l(
  'foo', 'bar' 
)->render());

var_dump($b->b(
  'if(true)', 'doSomething', 'MATCH' 
)->render());
