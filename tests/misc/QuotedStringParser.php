<?php

use hafriedlander\Peg\Parser;

class QuotedString extends Parser
{
/* string: / ([^"']|\\\\"|\\\\')* / */
protected $match_string_typestack = array('string');
function match_string ($stack = array()) {
	$matchrule = "string";
	$result = $this->construct($matchrule, $matchrule, null);
	if (($subres = $this->rx('/ ([^"\']|\\\\\\\\"|\\\\\\\\\')* /')) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* quoted_string: q:/["']/ string "$q" */
protected $match_quoted_string_typestack = array('quoted_string');
function match_quoted_string ($stack = array()) {
	$matchrule = "quoted_string";
	$result = $this->construct($matchrule, $matchrule, null);
	$_4 = NULL;
	do {
		$stack[] = $result;
		$result = $this->construct($matchrule, "q"); 
		if (($subres = $this->rx('/["\']/xS')) !== FALSE) {
			$result["text"] .= $subres;
			$subres = $result;
			$result = array_pop($stack);
			$this->store($result, $subres, 'q');
		}
		else {
			$result = array_pop($stack);
			$_4 = FALSE; break;
		}
		$matcher = 'match_'.'string'; $key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if ($subres !== FALSE) { $this->store($result, $subres); }
		else { $_4 = FALSE; break; }
		$expr_value = ''.$this->expression($result, $stack, 'q').'';
		$expr_len = strlen($expr_value);
		$subres = substr($this->string, $this->pos, $expr_len);
		if ($expr_value === $subres) {
			$this->pos += $expr_len;
			$result["text"] .= $subres;
		}
		else { $_4 = FALSE; break; }
		$_4 = TRUE; break;
	}
	while(0);
	if($_4 === TRUE) { return $this->finalise($result); }
	if($_4 === FALSE) { return FALSE; }
}


/* word: ( /[a-zA-Z]+/ ) | quoted_string */
protected $match_word_typestack = array('word');
function match_word ($stack = array()) {
	$matchrule = "word";
	$result = $this->construct($matchrule, $matchrule, null);
	$_11 = NULL;
	do {
		$res_6 = $result;
		$pos_6 = $this->pos;
		$_8 = NULL;
		do {
			if (($subres = $this->rx('/[a-zA-Z]+/xS')) !== FALSE) { $result["text"] .= $subres; }
			else { $_8 = FALSE; break; }
			$_8 = TRUE; break;
		}
		while(0);
		if($_8 === TRUE) { $_11 = TRUE; break; }
		$result = $res_6;
		$this->pos = $pos_6;
		$matcher = 'match_'.'quoted_string'; $key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if ($subres !== FALSE) {
			$this->store($result, $subres);
			$_11 = TRUE; break;
		}
		$result = $res_6;
		$this->pos = $pos_6;
		$_11 = FALSE; break;
	}
	while(0);
	if($_11 === TRUE) { return $this->finalise($result); }
	if($_11 === FALSE) { return FALSE; }
}



}

$str_p = '@
    (["\'])             # A string delimiter
    (
      (?:               # 0 or more:
        \\\\ .         # backslash followed by the delimiter
        |               # or
        (?: (?!\1). )   # not the delimiter followed by anything
      )*
    )
    \1                  # the closing delimiter
    (                   # optionally:
      !!                # the cancel-all flag
      |                 # or
      (?: !? [a-z] )*   # any number of positive or negative flags
    )?
  @xiU';
preg_match($str_p, 'obs_qp: "\\\\" ( "\x00" | obs_NO_WS_CTL | LF | CR )', $matches);
var_dump($matches);


