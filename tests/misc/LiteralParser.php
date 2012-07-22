<?php

use ju1ius\Peg\Parser;

class LiteralParser extends Parser
{

/* hey_mama: "Hey"i> "Mama"> "w00t" */
protected $match_hey_mama_typestack = array('hey_mama');
function match_hey_mama ($stack = array()) {
	$matchrule = "hey_mama";
	$result = $this->construct($matchrule, $matchrule, null);
	$_5 = NULL;
	do {
		$subres = substr($this->string, $this->pos, 3);
		if (0 === strcasecmp('Hey', $subres)) {
			$this->pos += 3;
			$result["text"] .= 'Hey';
		}
		else { $_5 = FALSE; break; }
		if (($subres = $this->whitespace()) !== FALSE) { $result["text"] .= $subres; }
		$subres = substr($this->string, $this->pos, 4);
		if ('Mama' === $subres) {
			$this->pos += 4;
			$result["text"] .= 'Mama';
		}
		else { $_5 = FALSE; break; }
		if (($subres = $this->whitespace()) !== FALSE) { $result["text"] .= $subres; }
		$subres = substr($this->string, $this->pos, 4);
		if ('w00t' === $subres) {
			$this->pos += 4;
			$result["text"] .= 'w00t';
		}
		else { $_5 = FALSE; break; }
		$_5 = TRUE; break;
	}
	while(0);
	if($_5 === TRUE) { return $this->finalise($result); }
	if($_5 === FALSE) { return FALSE; }
}


/* hey_mom_dad: "Hey"> mom_dad */
protected $match_hey_mom_dad_typestack = array('hey_mom_dad');
function match_hey_mom_dad ($stack = array()) {
	$matchrule = "hey_mom_dad";
	$result = $this->construct($matchrule, $matchrule, null);
	$_10 = NULL;
	do {
		$subres = substr($this->string, $this->pos, 3);
		if ('Hey' === $subres) {
			$this->pos += 3;
			$result["text"] .= 'Hey';
		}
		else { $_10 = FALSE; break; }
		if (($subres = $this->whitespace()) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'mom_dad'; $key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if ($subres !== FALSE) { $this->store($result, $subres); }
		else { $_10 = FALSE; break; }
		$_10 = TRUE; break;
	}
	while(0);
	if($_10 === TRUE) { return $this->finalise($result); }
	if($_10 === FALSE) { return FALSE; }
}


/* mom_dad: "Mom" | "Dad" */
protected $match_mom_dad_typestack = array('mom_dad');
function match_mom_dad ($stack = array()) {
	$matchrule = "mom_dad";
	$result = $this->construct($matchrule, $matchrule, null);
	$_15 = NULL;
	do {
		$res_12 = $result;
		$pos_12 = $this->pos;
		$subres = substr($this->string, $this->pos, 3);
		if ('Mom' === $subres) {
			$this->pos += 3;
			$result["text"] .= 'Mom';
			$_15 = TRUE; break;
		}
		$result = $res_12;
		$this->pos = $pos_12;
		$subres = substr($this->string, $this->pos, 3);
		if ('Dad' === $subres) {
			$this->pos += 3;
			$result["text"] .= 'Dad';
			$_15 = TRUE; break;
		}
		$result = $res_12;
		$this->pos = $pos_12;
		$_15 = FALSE; break;
	}
	while(0);
	if($_15 === TRUE) { return $this->finalise($result); }
	if($_15 === FALSE) { return FALSE; }
}


/* hello_world: "Hello"i <world */
protected $match_hello_world_typestack = array('hello_world');
function match_hello_world ($stack = array()) {
	$matchrule = "hello_world";
	$result = $this->construct($matchrule, $matchrule, null);
	$_20 = NULL;
	do {
		$subres = substr($this->string, $this->pos, 5);
		if (0 === strcasecmp('Hello', $subres)) {
			$this->pos += 5;
			$result["text"] .= 'Hello';
		}
		else { $_20 = FALSE; break; }
		if (($subres = $this->whitespace()) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'world'; $key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if ($subres !== FALSE) { $this->store($result, $subres); }
		else { $_20 = FALSE; break; }
		$_20 = TRUE; break;
	}
	while(0);
	if($_20 === TRUE) { return $this->finalise($result); }
	if($_20 === FALSE) { return FALSE; }
}


/* world: "World"i */
protected $match_world_typestack = array('world');
function match_world ($stack = array()) {
	$matchrule = "world";
	$result = $this->construct($matchrule, $matchrule, null);
	$subres = substr($this->string, $this->pos, 5);
	if (0 === strcasecmp('World', $subres)) {
		$this->pos += 5;
		$result["text"] .= 'World';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* enclosed_world: w:"Hello"i <world> "$w"i */
protected $match_enclosed_world_typestack = array('enclosed_world');
function match_enclosed_world ($stack = array()) {
	$matchrule = "enclosed_world";
	$result = $this->construct($matchrule, $matchrule, null);
	$_28 = NULL;
	do {
		$stack[] = $result;
		$result = $this->construct($matchrule, "w"); 
		$subres = substr($this->string, $this->pos, 5);
		if (0 === strcasecmp('Hello', $subres)) {
			$this->pos += 5;
			$result["text"] .= 'Hello';
			$subres = $result;
			$result = array_pop($stack);
			$this->store($result, $subres, 'w');
		}
		else {
			$result = array_pop($stack);
			$_28 = FALSE; break;
		}
		if (($subres = $this->whitespace()) !== FALSE) { $result["text"] .= $subres; }
		$matcher = 'match_'.'world'; $key = $matcher; $pos = $this->pos;
		$subres = ($this->packhas($key, $pos) ? $this->packread($key, $pos) : $this->packwrite($key, $pos, $this->$matcher(array_merge($stack, array($result)))));
		if ($subres !== FALSE) { $this->store($result, $subres); }
		else { $_28 = FALSE; break; }
		if (($subres = $this->whitespace()) !== FALSE) { $result["text"] .= $subres; }
		$expr_value = ''.$this->expression($result, $stack, 'w').'';
		$expr_len = strlen($expr_value);
		$subres = substr($this->string, $this->pos, $expr_len);
		if (0 === strcasecmp($expr_value, $subres)) {
			$this->pos += $expr_len;
			$result["text"] .= $expr_value;
		}
		else { $_28 = FALSE; break; }
		$_28 = TRUE; break;
	}
	while(0);
	if($_28 === TRUE) { return $this->finalise($result); }
	if($_28 === FALSE) { return FALSE; }
}


/* foo: "Fooe'd and Bar" */
protected $match_foo_typestack = array('foo');
function match_foo ($stack = array()) {
	$matchrule = "foo";
	$result = $this->construct($matchrule, $matchrule, null);
	$subres = substr($this->string, $this->pos, 14);
	if ('Fooe\'d and Bar' === $subres) {
		$this->pos += 14;
		$result["text"] .= 'Fooe\'d and Bar';
		return $this->finalise($result);
	}
	else { return FALSE; }
}


/* bar: 'Bare\'d and Fooe\'d' */
protected $match_bar_typestack = array('bar');
function match_bar ($stack = array()) {
	$matchrule = "bar";
	$result = $this->construct($matchrule, $matchrule, null);
	$subres = substr($this->string, $this->pos, 17);
	if ('Bare\'d and Fooe\'d' === $subres) {
		$this->pos += 17;
		$result["text"] .= 'Bare\'d and Fooe\'d';
		return $this->finalise($result);
	}
	else { return FALSE; }
}




}
