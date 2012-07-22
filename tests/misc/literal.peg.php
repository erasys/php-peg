<?php

use ju1ius\Peg\Parser;

class LiteralParser extends Parser
{

/*!* @normalize_literals
  
hey_mama: "Hey"i> "Mama"> "w00t"

hey_mom_dad: "Hey"> mom_dad

mom_dad: "Mom" | "Dad"

hello_world: "Hello"i <world

world: "World"i

enclosed_world: w:"Hello"i <world> "$w"i

foo: "Fooe'd and Bar"
bar: 'Bare\'d and Fooe\'d'

*/

}
