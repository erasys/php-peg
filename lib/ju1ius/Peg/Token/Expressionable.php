<?php

namespace ju1ius\Peg\Token;

abstract class Expressionable extends Terminal
{
	const EXPRESSION_RX = '/ \$(\w+) | { \$(\w+) } /x';

  public function contains_expression($value)
  {
		return preg_match(self::EXPRESSION_RX, $value);
	}

  public function replace_expression($value)
  {
    return preg_replace_callback(
      self::EXPRESSION_RX,
      array($this, 'expression_replace_cb'),
      $value
    );
  }

  protected function expression_replace_cb($matches)
  {
    return '\'.$this->expression($result, $stack, \''
      . (!empty($matches[1]) ? $matches[1] : $matches[2])
      . "').'";
	}
	
  public function match_code($value)
  {
		$value = $this->replace_expression($value);
		return parent::match_code($value);
	}
}
