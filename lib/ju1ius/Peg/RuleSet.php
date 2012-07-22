<?php

namespace ju1ius\Peg;


class RuleSet
{
  public $rules = array();

  protected $flags = 0x0;
  const F_NONE = 0x0;
  const F_CASE_INSENSITIVE = 0x1;
  const F_UNICODE = 0x2;
  const F_NORMALIZE_LITERALS = 0x4;

  public function __construct()
  {
    $this->flags = self::F_NONE;
  }

  public function hasFlag($flag)
  {
    return ($this->flags & $flag) === $flag;
  }
  public function setFlags($flags)
  {
    $this->flags = (int) $flags;
  }
  public function setFlag($flag)
  {
    $this->flags |= $flag;
  }
  public function unsetFlag($flag)
  {
    $this->flags &= ~$flag;
  }

  public function addRule($indent, $lines, &$out)
  {
		$rule = new Rule($this, $lines);
		$this->rules[$rule->name] = $rule;
		
		$out[] = $indent . '/* ' . $rule->name . ':' . $rule->rule . ' */' . PHP_EOL;
		$out[] = $rule->compile($indent);
		$out[] = PHP_EOL;
	}
	
  public function compile($indent, $rulestr)
  {
		$indentrx = '@^'.preg_quote($indent).'@';
		
		$out = array();
		$block = array();
		
		foreach (preg_split('/\r\n|\r|\n/', $rulestr) as $line) {
			// Ignore blank lines
			if (!trim($line)) continue;
			// Ignore comments
			if (preg_match('/^[\x20|\t]+#/', $line)) continue;
			
			// Strip off indent
			if (!empty($indent)) { 
				if (strpos($line, $indent) === 0) $line = substr($line, strlen($indent));
				else user_error('Non-blank line with inconsistent index in parser block', E_USER_ERROR);
			}
			
			// Any indented line, add to current set of lines
			if (preg_match('/^\x20|\t/', $line)) $block[] = $line;
			
			// Any non-indented line marks a new block. Add a rule for the current block, then start a new block
			else {
				if (count($block)) $this->addRule($indent, $block, $out);
				$block = array($line);
			}
		}
		
		// Any unfinished block add a rule for
		if (count($block)) $this->addRule($indent, $block, $out);
		
		// And return the compiled version
		return implode('', $out);
	}
}
