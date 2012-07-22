<?php

namespace ju1ius\Peg\Token;

use ju1ius\Peg\Token;
use ju1ius\Peg\Compiler\Builder;

class Sequence extends Token
{
  public function __construct($value)
  {
		parent::__construct('sequence', $value);
	}

  public function match_code($value)
  {
		$code = Builder::build();
		foreach($value as $token) {
			$code->l(
				$token->compile()->replace(array(
					'MATCH' => NULL,
					'FAIL' => 'FBREAK'
				))
			);
		}
		$code->l('MBREAK');

		return $this->match_fail_block($code);
	}
}
