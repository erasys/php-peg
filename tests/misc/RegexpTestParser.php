<?php

use hafriedlander\Peg\Parser;

class RegexpTestParser extends Parser
{

/* hello_world: hello> world */
protected $match_hello_world_typestack = array('hello_world');
function match_hello_world ($stack = array()) {
	$matchrule = "hello_world";
	$result = $this->construct($matchrule, $matchrule, null);
	$_3 = NULL;
	do {
		$matcher = 'match_'.'hello'; $key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if ($subres !== FALSE) { $this->store($result, $subres); }
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


/* hello: /Hello/i */
protected $match_hello_typestack = array('hello');
function match_hello ($stack = array()) {
	$matchrule = "hello";
	$result = $this->construct($matchrule, $matchrule, null);
	if (($subres = $this->rx('/\GHello/ixS')) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* world: /World!?/i */
protected $match_world_typestack = array('world');
function match_world ($stack = array()) {
	$matchrule = "world";
	$result = $this->construct($matchrule, $matchrule, null);
	if (($subres = $this->rx('/\GWorld!?/ixS')) !== FALSE) {
		$result["text"] .= $subres;
		return $this->finalise($result);
	}
	else { return FALSE; }
}




}
