<?php

namespace ju1ius\Peg\Token;

use ju1ius\Peg\Token;
use ju1ius\Peg\Compiler\Builder;

class Option extends Token
{
  public function __construct($opt1, $opt2)
  {
		parent::__construct('option', array($opt1, $opt2));
	}

  public function match_code($value)
  {
		$id = $this->varid();
		$code = Builder::build()
			->l(
			$this->save($id)
			);

		foreach ($value as $opt) {
			$code->l(
				$opt->compile()->replace(array(
					'MATCH' => 'MBREAK',
					'FAIL' => NULL
				)),
				$this->restore($id)
			);
		}
		$code->l('FBREAK');

		return $this->match_fail_block($code);
	}
}
