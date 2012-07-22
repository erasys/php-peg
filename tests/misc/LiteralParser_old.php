<?php

use ju1ius\Peg\Parser;

class LiteralParser extends Parser
{
/* hello_world: "Hello"i <world */
protected $match_hello_world_typestack = array('hello_world');
function match_hello_world ($stack = array()) {
	$matchrule = "hello_world";
	$result = $this->construct($matchrule, $matchrule, null);
	$_3 = NULL;
	do {
		if (($subres = $this->literal('Hello')) !== FALSE) { $result["text"] .= $subres; }
		else { $_3 = FALSE; break; }
		if (($subres = $this->whitespace()) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'world'; $key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if ($subres !== FALSE) { $this->store($result, $subres); }
		else { $_3 = FALSE; break; }
		$_3 = TRUE; break;
	}
	while(0);
	if($_3 === TRUE) { return $this->finalise($result); }
	if($_3 === FALSE) { return FALSE; }
}


/* world: "World"i */
protected $match_world_typestack = array('world');
function match_world ($stack = array()) {
	$matchrule = "world";
	$result = $this->construct($matchrule, $matchrule, null);
	if (($subres = $this->literal('World')) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* enclosed_world: w:(/\w+/) <world> "$w"i */
protected $match_enclosed_world_typestack = array('enclosed_world');
function match_enclosed_world ($stack = array()) {
	$matchrule = "enclosed_world";
	$result = $this->construct($matchrule, $matchrule, null);
	$_13 = NULL;
	do {
		$stack[] = $result;
		$result = $this->construct($matchrule, "w"); 
		$_7 = NULL;
		do {
			if (($subres = $this->rx('/\w+/')) !== FALSE) { $result["text"] .= $subres; }
			else { $_7 = FALSE; break; }
			$_7 = TRUE; break;
		}
		while(0);
		if($_7 === TRUE) {
			$subres = $result;
			$result = array_pop($stack);
			$this->store($result, $subres, 'w');
		}
		if($_7 === FALSE) {
			$result = array_pop($stack);
			$_13 = FALSE; break;
		}
		if (($subres = $this->whitespace()) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'world'; $key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if ($subres !== FALSE) { $this->store($result, $subres); }
		else { $_13 = FALSE; break; }
		if (($subres = $this->whitespace()) !== FALSE) { $result["text"] .= $subres; }
		if (($subres = $this->literal(''.$this->expression($result, $stack, 'w').'')) !== FALSE) { $result["text"] .= $subres; }
		else { $_13 = FALSE; break; }
		$_13 = TRUE; break;
	}
	while(0);
	if($_13 === TRUE) { return $this->finalise($result); }
	if($_13 === FALSE) { return FALSE; }
}



}
