<?php

namespace ju1ius\Peg;

use ju1ius\Peg\Compiler\Builder;

/**
 * PEG Generator - A PEG Parser for PHP
 *
 * @author Hamish Friedlander / SilverStripe
 *
 * See README.md for documentation
 * 
 */
class Compiler
{

	public static $parsers = array();
	
	public static $debug = false;

  public static $currentClass = null;

	public static $rx = '~^
    ([\x20\t]*)                   # indent
    /\*!\*                        # ruleset begin marker
    (?:
      [\x20\t]*
      (!?\w*)?                    # pragma or parser name
      [\x20\t]*
      (                           # parser flags
        (?:@\w+[\x20\t]*)*
      )
    )?
    ((?:[^*]|\*[^/])*)            # Any amount of "a character that isnt a star, or a star not followed by a /
    \*/                           # ruleset end marker
	~mx';

  public static function create_parser($match)
  {
		/* We allow indenting of the whole rule block, but only to the level of the comment start's indent */
		$indent = $match[1];
		
    $name_or_pragma = trim($match[2]);
		
		/* Check for pragmas */
		if (0 === strpos($name_or_pragma, '!')) {

			switch ($name_or_pragma) {

				case '!silent':
					// NOP - dont output
					return '';

				case '!insert_autogen_warning':
					return $indent . implode(PHP_EOL.$indent, array(
						'/**',
            ' * WARNING: This file has been machine generated.',
            ' * Do not edit it, or your changes will be overwritten next time it is compiled.',
						' **/'
					)) . PHP_EOL;

				case '!debug':
					self::$debug = true;
					return '';
			}
			
			throw new \RuntimeException("Unknown pragma $class encountered when compiling parser");
		}

		/* Get the parser name for this block */
    if ($name_or_pragma) {
      $class = self::$currentClass = $name_or_pragma;
    } else if (self::$currentClass) {
      $class = self::$currentClass;
    } else {
      $class = self::$currentClass = 'Anonymous Parser';
    }
    if (!isset(self::$parsers[$class])) {
      self::$parsers[$class] = new RuleSet();
    }
    $ruleset = self::$parsers[$class];

    // Handle flags for this block
    $flags = preg_split('/[\x20\t]+/', trim($match[3]), null, PREG_SPLIT_NO_EMPTY);
    foreach ($flags as $flag) {
      switch (strtolower($flag)) {
        case '@unicode':
          $ruleset->setFlag(RuleSet::F_UNICODE);
          break;
        case '@case_insensitive':
          $ruleset->setFlag(RuleSet::F_CASE_INSENSITIVE);
          break;
        case '@normalize_literals':
          $ruleset->setFlag(RuleSet::F_NORMALIZE_LITERALS);
          break;
        default:
          user_error("Ignoring unknown flag: ".$flag);
          break;
      }
    }

    $rules = trim($match[4]);

		return $ruleset->compile($indent, $rules);
	}

  public static function compile($string)
  {
		return preg_replace_callback(self::$rx, array('ju1ius\Peg\Compiler', 'create_parser'), $string);
	}

}
