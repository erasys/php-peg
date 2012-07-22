<?php

namespace ju1ius\Peg\Token;

class Whitespace extends Terminal
{
  public function __construct($optional)
  {
		parent::__construct('whitespace', $optional);
	}

	/* Call recursion indirectly */
  public function match_code($value)
  {
		$code = parent::match_code('');
		return $value ? $code->replace(array('FAIL' => NULL)) : $code;
	}
}


