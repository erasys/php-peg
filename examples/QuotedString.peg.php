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

$str_p = '@
    (["\'])             # A string delimiter
    (
      (?:               # 0 or more:
        \\\\ .          # backslash followed by anything
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


