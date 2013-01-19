<?php

use hafriedlander\Peg\Parser;

class Calculator extends Parser\Basic
{

/* int: / [-]?[1-9][0-9]* / */
protected $match_int_typestack = array('int');
public function match_int ($stack = array()) {
	$matchrule = "int";
	$result = $this->construct($matchrule, $matchrule, null);
	if (false !== ($subres = $this->rx('/ [-]?[1-9][0-9]* /xS'))) {
		$result["_text"] .= $subres;
		return $this->finalise($result);
	}
	else { return false; }
}




/* float: / [-]?[0-9]*\.[0-9]+ / */
protected $match_float_typestack = array('float');
public function match_float ($stack = array()) {
	$matchrule = "float";
	$result = $this->construct($matchrule, $matchrule, null);
	if (false !== ($subres = $this->rx('/ [-]?[0-9]*\.[0-9]+ /xS'))) {
		$result["_text"] .= $subres;
		return $this->finalise($result);
	}
	else { return false; }
}




/* num: int | float */
protected $match_num_typestack = array('num');
public function match_num ($stack = array()) {
	$matchrule = "num";
	$result = $this->construct($matchrule, $matchrule, null);
	$_5 = null;
	do {
		$res_2 = $result;
		$pos_2 = $this->pos;
		$matcher = 'match_'.'int';
		$key = $matcher; $pos = $this->pos;
		$indent = str_repeat("    ", $this->depth);
		$this->depth++;
		$debug_sub = (strlen($this->string) - $this->pos > 20)
		? (substr($this->string, $this->pos, 20) . " [...]")
		: substr($this->string, $this->pos);
		$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $debug_sub);
		printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);
		$subres = $this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos,
		$this->$matcher(array_merge($stack, array($result)))
		);
		if (false !== $subres) {
			printf("%sMATCH\n", $indent);
			$this->depth--;
			$this->store($result, $subres);
			$_5 = true; break;
		}
		else {
			printf("%sFAIL\n", $indent);
			$this->depth--;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		$matcher = 'match_'.'float';
		$key = $matcher; $pos = $this->pos;
		$indent = str_repeat("    ", $this->depth);
		$this->depth++;
		$debug_sub = (strlen($this->string) - $this->pos > 20)
		? (substr($this->string, $this->pos, 20) . " [...]")
		: substr($this->string, $this->pos);
		$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $debug_sub);
		printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);
		$subres = $this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos,
		$this->$matcher(array_merge($stack, array($result)))
		);
		if (false !== $subres) {
			printf("%sMATCH\n", $indent);
			$this->depth--;
			$this->store($result, $subres);
			$_5 = true; break;
		}
		else {
			printf("%sFAIL\n", $indent);
			$this->depth--;
		}
		$result = $res_2;
		$this->pos = $pos_2;
		$_5 = false; break;
	}
	while(0);
	if(true === $_5) { return $this->finalise($result); }
	if(false === $_5) { return false; }
}




/* fact: num > | '(' > expr > ')' > */
protected $match_fact_typestack = array('fact');
public function match_fact ($stack = array()) {
	$matchrule = "fact";
	$result = $this->construct($matchrule, $matchrule, null);
	$_20 = null;
	do {
		$res_7 = $result;
		$pos_7 = $this->pos;
		$_10 = null;
		do {
			$matcher = 'match_'.'num';
			$key = $matcher; $pos = $this->pos;
			$indent = str_repeat("    ", $this->depth);
			$this->depth++;
			$debug_sub = (strlen($this->string) - $this->pos > 20)
			? (substr($this->string, $this->pos, 20) . " [...]")
			: substr($this->string, $this->pos);
			$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $debug_sub);
			printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);
			$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos,
			$this->$matcher(array_merge($stack, array($result)))
			);
			if (false !== $subres) {
				printf("%sMATCH\n", $indent);
				$this->depth--;
				$this->store($result, $subres);
			}
			else {
				printf("%sFAIL\n", $indent);
				$this->depth--;
				$_10 = false; break;
			}
			if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
			$_10 = true; break;
		}
		while(0);
		if(true === $_10) { $_20 = true; break; }
		$result = $res_7;
		$this->pos = $pos_7;
		$_18 = null;
		do {
			$subres = substr($this->string, $this->pos, 1);
			$indent = str_repeat("    ", $this->depth);
			$this->depth++;
			$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $subres);
			printf("%sMatching literal '(' against '%s'\n", $indent, $debug_sub);
			if ('(' === $subres) {
				printf("%sMATCH\n", $indent);
				$this->depth--;
				$this->pos += 1;
				$result["_text"] .= $subres;
			}
			else {
				printf("%sFAIL\n", $indent);
				$this->depth--;
				$_18 = false; break;
			}
			if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
			$matcher = 'match_'.'expr';
			$key = $matcher; $pos = $this->pos;
			$indent = str_repeat("    ", $this->depth);
			$this->depth++;
			$debug_sub = (strlen($this->string) - $this->pos > 20)
			? (substr($this->string, $this->pos, 20) . " [...]")
			: substr($this->string, $this->pos);
			$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $debug_sub);
			printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);
			$subres = $this->packhas($key, $pos)
			? $this->packread($key, $pos)
			: $this->packwrite($key, $pos,
			$this->$matcher(array_merge($stack, array($result)))
			);
			if (false !== $subres) {
				printf("%sMATCH\n", $indent);
				$this->depth--;
				$this->store($result, $subres);
			}
			else {
				printf("%sFAIL\n", $indent);
				$this->depth--;
				$_18 = false; break;
			}
			if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
			$subres = substr($this->string, $this->pos, 1);
			$indent = str_repeat("    ", $this->depth);
			$this->depth++;
			$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $subres);
			printf("%sMatching literal ')' against '%s'\n", $indent, $debug_sub);
			if (')' === $subres) {
				printf("%sMATCH\n", $indent);
				$this->depth--;
				$this->pos += 1;
				$result["_text"] .= $subres;
			}
			else {
				printf("%sFAIL\n", $indent);
				$this->depth--;
				$_18 = false; break;
			}
			if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
			$_18 = true; break;
		}
		while(0);
		if(true === $_18) { $_20 = true; break; }
		$result = $res_7;
		$this->pos = $pos_7;
		$_20 = false; break;
	}
	while(0);
	if(true === $_20) { return $this->finalise($result); }
	if(false === $_20) { return false; }
}



public function fact_num (&$res, $sub) {
        $res['val'] = floatval($sub['_text']);
    }

public function fact_expr (&$res, $sub) {
        $res['val'] = $sub['val'];
    }

/* term: a:fact > ( ('*' > mul:fact >) | ('/' > div:fact >) )* */
protected $match_term_typestack = array('term');
public function match_term ($stack = array()) {
	$matchrule = "term";
	$result = $this->construct($matchrule, $matchrule, null);
	$_41 = null;
	do {
		$matcher = 'match_'.'fact';
		$key = $matcher; $pos = $this->pos;
		$indent = str_repeat("    ", $this->depth);
		$this->depth++;
		$debug_sub = (strlen($this->string) - $this->pos > 20)
		? (substr($this->string, $this->pos, 20) . " [...]")
		: substr($this->string, $this->pos);
		$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $debug_sub);
		printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);
		$subres = $this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos,
		$this->$matcher(array_merge($stack, array($result)))
		);
		if (false !== $subres) {
			printf("%sMATCH\n", $indent);
			$this->depth--;
			$this->store($result, $subres, "a");
		}
		else {
			printf("%sFAIL\n", $indent);
			$this->depth--;
			$_41 = false; break;
		}
		if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
		while (true) {
			$res_40 = $result;
			$pos_40 = $this->pos;
			$_39 = null;
			do {
				$_37 = null;
				do {
					$res_24 = $result;
					$pos_24 = $this->pos;
					$_29 = null;
					do {
						$subres = substr($this->string, $this->pos, 1);
						$indent = str_repeat("    ", $this->depth);
						$this->depth++;
						$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $subres);
						printf("%sMatching literal '*' against '%s'\n", $indent, $debug_sub);
						if ('*' === $subres) {
							printf("%sMATCH\n", $indent);
							$this->depth--;
							$this->pos += 1;
							$result["_text"] .= $subres;
						}
						else {
							printf("%sFAIL\n", $indent);
							$this->depth--;
							$_29 = false; break;
						}
						if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
						$matcher = 'match_'.'fact';
						$key = $matcher; $pos = $this->pos;
						$indent = str_repeat("    ", $this->depth);
						$this->depth++;
						$debug_sub = (strlen($this->string) - $this->pos > 20)
						? (substr($this->string, $this->pos, 20) . " [...]")
						: substr($this->string, $this->pos);
						$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $debug_sub);
						printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);
						$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos,
						$this->$matcher(array_merge($stack, array($result)))
						);
						if (false !== $subres) {
							printf("%sMATCH\n", $indent);
							$this->depth--;
							$this->store($result, $subres, "mul");
						}
						else {
							printf("%sFAIL\n", $indent);
							$this->depth--;
							$_29 = false; break;
						}
						if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
						$_29 = true; break;
					}
					while(0);
					if(true === $_29) { $_37 = true; break; }
					$result = $res_24;
					$this->pos = $pos_24;
					$_35 = null;
					do {
						$subres = substr($this->string, $this->pos, 1);
						$indent = str_repeat("    ", $this->depth);
						$this->depth++;
						$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $subres);
						printf("%sMatching literal '/' against '%s'\n", $indent, $debug_sub);
						if ('/' === $subres) {
							printf("%sMATCH\n", $indent);
							$this->depth--;
							$this->pos += 1;
							$result["_text"] .= $subres;
						}
						else {
							printf("%sFAIL\n", $indent);
							$this->depth--;
							$_35 = false; break;
						}
						if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
						$matcher = 'match_'.'fact';
						$key = $matcher; $pos = $this->pos;
						$indent = str_repeat("    ", $this->depth);
						$this->depth++;
						$debug_sub = (strlen($this->string) - $this->pos > 20)
						? (substr($this->string, $this->pos, 20) . " [...]")
						: substr($this->string, $this->pos);
						$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $debug_sub);
						printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);
						$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos,
						$this->$matcher(array_merge($stack, array($result)))
						);
						if (false !== $subres) {
							printf("%sMATCH\n", $indent);
							$this->depth--;
							$this->store($result, $subres, "div");
						}
						else {
							printf("%sFAIL\n", $indent);
							$this->depth--;
							$_35 = false; break;
						}
						if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
						$_35 = true; break;
					}
					while(0);
					if(true === $_35) { $_37 = true; break; }
					$result = $res_24;
					$this->pos = $pos_24;
					$_37 = false; break;
				}
				while(0);
				if(false === $_37) { $_39 = false; break; }
				$_39 = true; break;
			}
			while(0);
			if(false === $_39) {
				$result = $res_40;
				$this->pos = $pos_40;
				unset($res_40);
				unset($pos_40);
				break;
			}
		}
		$_41 = true; break;
	}
	while(0);
	if(true === $_41) { return $this->finalise($result); }
	if(false === $_41) { return false; }
}



public function term_a (&$res, $sub) {
        $res['val'] = $sub['val'];
    }

public function term_mul (&$res, $sub){
        $res['val'] *= $sub['val'];
    }

public function term_div (&$res, $sub){
        $res['val'] /= $sub['val'];
    }

/* expr: a:term > ( ('+' > plus:term >) | ('-' > minus:term >) )* */
protected $match_expr_typestack = array('expr');
public function match_expr ($stack = array()) {
	$matchrule = "expr";
	$result = $this->construct($matchrule, $matchrule, null);
	$_62 = null;
	do {
		$matcher = 'match_'.'term';
		$key = $matcher; $pos = $this->pos;
		$indent = str_repeat("    ", $this->depth);
		$this->depth++;
		$debug_sub = (strlen($this->string) - $this->pos > 20)
		? (substr($this->string, $this->pos, 20) . " [...]")
		: substr($this->string, $this->pos);
		$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $debug_sub);
		printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);
		$subres = $this->packhas($key, $pos)
		? $this->packread($key, $pos)
		: $this->packwrite($key, $pos,
		$this->$matcher(array_merge($stack, array($result)))
		);
		if (false !== $subres) {
			printf("%sMATCH\n", $indent);
			$this->depth--;
			$this->store($result, $subres, "a");
		}
		else {
			printf("%sFAIL\n", $indent);
			$this->depth--;
			$_62 = false; break;
		}
		if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
		while (true) {
			$res_61 = $result;
			$pos_61 = $this->pos;
			$_60 = null;
			do {
				$_58 = null;
				do {
					$res_45 = $result;
					$pos_45 = $this->pos;
					$_50 = null;
					do {
						$subres = substr($this->string, $this->pos, 1);
						$indent = str_repeat("    ", $this->depth);
						$this->depth++;
						$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $subres);
						printf("%sMatching literal '+' against '%s'\n", $indent, $debug_sub);
						if ('+' === $subres) {
							printf("%sMATCH\n", $indent);
							$this->depth--;
							$this->pos += 1;
							$result["_text"] .= $subres;
						}
						else {
							printf("%sFAIL\n", $indent);
							$this->depth--;
							$_50 = false; break;
						}
						if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
						$matcher = 'match_'.'term';
						$key = $matcher; $pos = $this->pos;
						$indent = str_repeat("    ", $this->depth);
						$this->depth++;
						$debug_sub = (strlen($this->string) - $this->pos > 20)
						? (substr($this->string, $this->pos, 20) . " [...]")
						: substr($this->string, $this->pos);
						$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $debug_sub);
						printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);
						$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos,
						$this->$matcher(array_merge($stack, array($result)))
						);
						if (false !== $subres) {
							printf("%sMATCH\n", $indent);
							$this->depth--;
							$this->store($result, $subres, "plus");
						}
						else {
							printf("%sFAIL\n", $indent);
							$this->depth--;
							$_50 = false; break;
						}
						if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
						$_50 = true; break;
					}
					while(0);
					if(true === $_50) { $_58 = true; break; }
					$result = $res_45;
					$this->pos = $pos_45;
					$_56 = null;
					do {
						$subres = substr($this->string, $this->pos, 1);
						$indent = str_repeat("    ", $this->depth);
						$this->depth++;
						$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $subres);
						printf("%sMatching literal '-' against '%s'\n", $indent, $debug_sub);
						if ('-' === $subres) {
							printf("%sMATCH\n", $indent);
							$this->depth--;
							$this->pos += 1;
							$result["_text"] .= $subres;
						}
						else {
							printf("%sFAIL\n", $indent);
							$this->depth--;
							$_56 = false; break;
						}
						if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
						$matcher = 'match_'.'term';
						$key = $matcher; $pos = $this->pos;
						$indent = str_repeat("    ", $this->depth);
						$this->depth++;
						$debug_sub = (strlen($this->string) - $this->pos > 20)
						? (substr($this->string, $this->pos, 20) . " [...]")
						: substr($this->string, $this->pos);
						$debug_sub = preg_replace(["/\r/", "/\n/"], ['\r', '\n'], $debug_sub);
						printf("%sMatching against %s (%s)\n", $indent, $matcher, $debug_sub);
						$subres = $this->packhas($key, $pos)
						? $this->packread($key, $pos)
						: $this->packwrite($key, $pos,
						$this->$matcher(array_merge($stack, array($result)))
						);
						if (false !== $subres) {
							printf("%sMATCH\n", $indent);
							$this->depth--;
							$this->store($result, $subres, "minus");
						}
						else {
							printf("%sFAIL\n", $indent);
							$this->depth--;
							$_56 = false; break;
						}
						if (false !== ($subres = $this->whitespace())) { $result["_text"] .= $subres; }
						$_56 = true; break;
					}
					while(0);
					if(true === $_56) { $_58 = true; break; }
					$result = $res_45;
					$this->pos = $pos_45;
					$_58 = false; break;
				}
				while(0);
				if(false === $_58) { $_60 = false; break; }
				$_60 = true; break;
			}
			while(0);
			if(false === $_60) {
				$result = $res_61;
				$this->pos = $pos_61;
				unset($res_61);
				unset($pos_61);
				break;
			}
		}
		$_62 = true; break;
	}
	while(0);
	if(true === $_62) { return $this->finalise($result); }
	if(false === $_62) { return false; }
}



public function expr_a (&$res, $sub) {
        $res['val'] = $sub['val'];
    }

public function expr_plus (&$res, $sub){
        $res['val'] += $sub['val'];
    }

public function expr_minus (&$res, $sub){
        $res['val'] -= $sub['val'];
    }



}
