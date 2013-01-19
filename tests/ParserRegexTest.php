<?php

require_once 'ParserTestBase.php';

class ParserRegexTest extends ParserTestBase
{
  public function testMatchPos()
  {
    $grammar = <<<EOS
/*!* MatchPos
hello_country: hello> ("World"i | country)
hello: "hello"i
country: /[a-z\s-]+/iu
*/
EOS;
    $parser = $this->buildParser($grammar);
    $parser->assertMatches('hello_country', 'Hello France');
    $parser->assertMatches('hello_country', 'Hello United Kingdom');
    $parser->assertDoesntMatch('hello_country', 'Hello US-3');
  }
}
