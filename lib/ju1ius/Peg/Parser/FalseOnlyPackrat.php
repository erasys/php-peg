<?php

namespace ju1ius\Peg\Parser;

use ju1ius\Peg\Parser;

/**
 * FalseOnlyPackrat only remembers which results where false.
 * Experimental.
 *
 * @author Hamish Friedlander
 */
class FalseOnlyPackrat extends Parser
{
  protected $packstatebase;
  protected $packstate;

  public function setSource($string)
  {
    parent::setSource($string);

		$this->packstatebase = str_repeat('.', strlen($string));
		$this->packstate = array();
  }

  public function packhas($key, $pos)
  {
		return isset($this->packstate[$key]) && 'F' === $this->packstate[$key][$pos];
	}

  public function packread($key, $pos)
  {
		return false;
	}

  public function packwrite($key, $pos, $res)
  {
    if (!isset($this->packstate[$key])) {
      $this->packstate[$key] = $this->packstatebase;
    }
		if (false === $res) {
			$this->packstate[$key][$pos] = 'F';
		}

		return $res;
	}
}
