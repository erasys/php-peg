<?php

use hafriedlander\Peg\Parser;

class Calculator extends Parser\Basic
{

/*!* Calculator

int: / [-]?[1-9][0-9]* /
float: / [-]?[0-9]*\.[0-9]+ /
num: int | float

fact: num > | '(' > expr > ')' >
    function num(&$res, $sub) {
        $res['val'] = floatval($sub['_text']);
    }
    function expr(&$res, $sub) {
        $res['val'] = $sub['val'];
    }

term: a:fact > ( ('*' > mul:fact >) | ('/' > div:fact >) )*
    function a(&$res, $sub) {
        $res['val'] = $sub['val'];
    }
    function mul(&$res, $sub){
        $res['val'] *= $sub['val'];
        var_dump($res, $sub);
    }
    function div(&$res, $sub){
        $res['val'] /= $sub['val'];
    }

expr: a:term > ( ('+' > plus:term >) | ('-' > minus:term >) )*
    function a(&$res, $sub) {
        $res['val'] = $sub['val'];
    }
    function plus(&$res, $sub){
        $res['val'] += $sub['val'];
    }
    function minus(&$res, $sub){
        $res['val'] -= $sub['val'];
    }

*/

}
