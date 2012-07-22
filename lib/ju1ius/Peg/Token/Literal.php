<?php

namespace ju1ius\Peg\Token;

use ju1ius\Peg\Compiler\Builder;

class Literal extends Expressionable
{
  protected $flags = array(
    'i' => false,
    'u' => false,
    'n' => false
  );

  public function __construct($quotechar, $value, array $flags)
  {
    $this->quotechar = $quotechar;
    $this->value = $value;
    $this->flags = $flags;
    if ('"' === $quotechar) {
      $value = addcslashes($value, "'");
    } else {
      //$value = stripcslashes($value);
    }
		//parent::__construct('literal', "'" . substr($value,1,-1) . "'");
    //var_dump($value);
    parent::__construct('literal', "'" . $value . "'");
	}

  public function match_code($value)
  {
    // Convert hex char sequences
    $value = self::convertSpecialSequences($value);

		// We inline raw literal matches for speed
    if (!$this->contains_expression($value)) {
      // strip out quotes
      $literal_value = trim($value, "'");
      $len = eval("return strlen($value);");

      $subres = '$subres = substr($this->string, $this->pos, '.$len.');';
      $cond = $value.' === $subres';
      if ($this->flags['i']) {
        $cond = $this->flags['u']
          ? mb_strtolower($value, "utf-8").' === mb_strtolower($subres, "utf-8")'
          : '0 === strcasecmp('.$value.', $subres)';
      }
      return Builder::build()->l(
        $subres,
        $this->match_fail_conditional(
          $cond,
          Builder::build()->l(
            '$this->pos += '.$len.';',
            $this->set_text($this->flags['n'] ? $value : '$subres')
          )
        )
      );
    }
    // then handle literals containing expressions
    $value = $this->replace_expression($value);
    
    $cond = '$expr_value === $subres';
    if ($this->flags['i']) {
      $cond = $this->flags['u']
        ? 'mb_strtolower($expr_value, "utf-8") === mb_strtolower($subres, "utf-8")'
        : '0 === strcasecmp($expr_value, $subres)';
    }
    // FIXME: is it safe to assume that two strings which differ only in case
    // have the same length ?
    return Builder::build()->l(
      '$expr_value = '.$value.';',
      '$expr_len = strlen($expr_value);',
      '$subres = substr($this->string, $this->pos, $expr_len);',
      $this->match_fail_conditional(
        $cond,
        Builder::build()->l(
          '$this->pos += $expr_len;',
          $this->set_text($this->flags['n'] ? '$expr_value' : '$subres')
        )
      )
    );
	}

  public static function convertSpecialSequences($str)
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
}
