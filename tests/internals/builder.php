<?php

require_once __DIR__.'/../autoloader.php';

use hafriedlander\Peg\Compiler\Builder;

$b = new Builder();

var_dump($b->l(
  'foo', 'bar' 
)->render());

var_dump($b->b(
  'if(true)', 'doSomething', 'MATCH' 
)->render());
