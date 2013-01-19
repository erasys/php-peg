<?php

function compare($pattern, $text, $offset)
{
  return 0 === strcasecmp($pattern, substr($text, $offset, strlen($pattern)));  
}
function compare_utf8($pattern, $text, $offset)
{
  return 0 === strcasecmp($pattern, substr($text, $offset, strlen($pattern)));  
}
