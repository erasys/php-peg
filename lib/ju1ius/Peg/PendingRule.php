<?php

namespace ju1ius\Peg;

/**
 * Handles storing of information for an expression that applys to the <i>next</i> token,
 * and deletion of that information after applying
 *
 * @author Hamish Friedlander
 */
class PendingRule
{
  public function __construct()
  {
		$this->what = NULL;
	}

  public function set($what, $val = TRUE)
  {
		$this->what = $what;
		$this->val = $val;
	}

  public function apply_if_present($on)
  {
		if ($this->what !== NULL) {
			$what = $this->what;
			$on->$what = $this->val;

			$this->what = NULL;
		}
	}
}
