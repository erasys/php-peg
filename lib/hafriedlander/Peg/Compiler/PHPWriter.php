<?php

namespace hafriedlander\Peg\Compiler;

/**
 * PHPWriter contains several code generation snippets
 * that are used both by the Token and the Rule compiler
 */
class PHPWriter
{
    public static $varid = 0;

    public function varid()
    {
        return '_' . (self::$varid++);
    }

    public static function unindent($str, $indent="  ")
    {
        if (!is_string($str)) {
            $lines = preg_split('/\r\n|\n|\r/', $str);
        } else if (is_array($str)) {
            $lines = $str;
        }
        $lines = array_map(function($line) use ($indent) {
            return preg_replace('/^'.$indent.'/', $line, '');
        }, $lines);
        return implode(PHP_EOL, $lines);
    }
    public static function indent($str, $indent="  ")
    {
        if (!is_string($str)) {
            $lines = preg_split('/\r\n|\n|\r/', $str);
        } else if (is_array($str)) {
            $lines = $str;
        }
        $lines = array_map(function($line) use ($indent) {
            return $indent . $line;
        }, $lines);
        return implode(PHP_EOL, $lines);
    }

    public function function_name($str)
    {
        return preg_replace(
            array('/-/', '/\$/', '/\*/', '/\W+/'),
            array('_', 'DLR', 'STR', ''),
            $str
        );
    }

    public function save($id)
    {
        return PHPBuilder::build()
            ->l(
                '$res'.$id.' = $result;',
                '$pos'.$id.' = $this->pos;'
            );
    }

    public function restore($id, $remove = FALSE)
    {
        $code = PHPBuilder::build()
            ->l(
                '$result = $res'.$id.';',
                '$this->pos = $pos'.$id.';'
            );

        if ($remove) {
            $code->l(
                'unset($res'.$id.');',
                'unset($pos'.$id.');'
            );
        }

        return $code;
    }

    public function match_fail_conditional($on, $match = NULL, $fail = NULL)
    {
        return PHPBuilder::build()
            ->b(
                'if (' . $on . ')',
                $match,
                'MATCH'
            )
            ->b(
                'else',
                $fail,
                'FAIL'
            );
    }

    public function match_fail_block($code)
    {
        $id = $this->varid();

        return PHPBuilder::build()
            ->l('$'.$id.' = null;')
            ->b(
                'do',
                $code->replace(array(
                    'MBREAK' => '$'.$id.' = true; break;',
                    'FBREAK' => '$'.$id.' = false; break;'
                ))
            )
            ->l('while(0);')
            ->b('if(true === $'.$id.')', 'MATCH')
            ->b('if(false === $'.$id.')', 'FAIL');
    }

}
