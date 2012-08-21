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
  protected $prop_name = null;
  protected $prop_val = null;

  public function set($what, $value=true)
  {
		$this->prop_name = $what;
		$this->prop_val = $value;
	}

  public function apply_if_present($on)
  {
    if (null !== $this->prop_name) {

			$what = $this->prop_name;
			$on->$what = $this->prop_val;

      $this->prop_name = null;

		}
	}
}
