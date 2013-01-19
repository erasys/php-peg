<?php

require __DIR__.'/../autoloader.php';

use hafriedlander\Peg\Compiler;

class ParserTestWrapper
{
	
  public function __construct($testcase, $class)
  {
		$this->testcase = $testcase;
		$this->class = $class;
	}

  public function match($method, $string, $allowPartial = false)
  {
		$class = $this->class;
		$func = 'match_'.$method;
		
		$parser = new $class($string);
		$res = $parser->$func();
		return ($allowPartial || $parser->pos == strlen($string)) ? $res : false;
	}
	
  public function matches($method, $string, $allowPartial = false)
  {
		return $this->match($method, $string, $allowPartial) !== false;
	}
	
  public function assertMatches($method, $string, $message = null)
  {
		$this->testcase->assertTrue($this->matches($method, $string), $message ? $message : "Assert parser method $method matches string $string");
	}
	
  public function assertDoesntMatch($method, $string, $message = null)
  {
		$this->testcase->assertFalse($this->matches($method, $string), $message ? $message : "Assert parser method $method doesn't match string $string");
	}
}

class ParserTestBase extends PHPUnit_Framework_TestCase
{
	
  public function buildParser($grammar)
  {
		$class = 'Parser_' . sha1($grammar);
    $code = Compiler::compile(<<<EOS
use hafriedlander\Peg\Parser;

class $class extends Parser\Basic
{
$grammar
}
EOS
    );
    eval($code);

		return new ParserTestWrapper($this, $class);
	}

}
