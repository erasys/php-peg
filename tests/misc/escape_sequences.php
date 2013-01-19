<?php

function escapeSpecialSequences($str)
{
  $str = preg_replace_callback(
    "@(?<!\\\\)\\\\((?:\\\\\\\\)*)  # odd number of backslashes
      (?:                           # followed by
        x([0-9a-fA-F]{1,2})         # hex escape sequence
        |([nrtvef])                 # or control char escape sequence
        |([0-7]{1,3})               # or octal escape sequence
      )@x",
    function($matches) {
      $res = str_repeat('\\', strlen($matches[1])/2);
      //var_dump($matches);
      if (isset($matches[4])) {
        $res .= chr(octdec($matches[4]));
      } else if (isset($matches[3])) {
        switch($matches[3]) {
          case 'n':
            $res .= "\n";
            break;
          case 'r':
            $res .= "\r";
            break;
          case 't':
            $res .= "\t";
            break;
          case 'v':
            $res .= "\v";
            break;
          case 'e':
            $res .= "\e";
            break;
          case 'f':
            $res .= "\f";
            break;
        }
      } else if (isset($matches[2])) {
        $res .= chr(hexdec($matches[2]));
      } else {
        $res = $matches[0];
      }
      return $res;
    },
    $str
  );
  

  $str = preg_replace_callback(
    "@(?<!\\\\)((?:\\\\\\\\)+)    # even number of backslashes
      (                           # followed by
        x[0-9a-fA-F]{1,2}         # hex sequence
        |[nrtvef]                 # or ctrl char
        |[0-7]{1,3}               # or octal sequence
      )@x",
    function($matches) {
      return str_repeat('\\', strlen($matches[1])/2) . $matches[2];
    },
    $str
  );

  return $str;
}

$tests = array(
  'foo\nbar',
  'foo\tbar',
  'foo\x20bar',
  '\x30',
  '\\x30',
  '\\\x30',
  '\\\\x30',
  '\\\\\x30',
  '\\\\\\x30',
  '\\\\\\\x30',
  '\\\\\\\\x30'
);
foreach($tests as $test) {
  echo $test . ' => ' . escapeSpecialSequences($test) . PHP_EOL;
}
