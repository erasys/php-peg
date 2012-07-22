<?php

use ju1ius\Peg\Parser;

class QuotedString extends Parser
{
/*!*

string: / ([^"']|\\\\"|\\\\')* /
quoted_string: q:/["']/ string "$q"
word: ( /[a-zA-Z]+/ ) | quoted_string

*/
}
