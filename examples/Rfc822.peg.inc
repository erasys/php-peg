<?php

use ju1ius\Peg\Parser;

/**
 * This parser strictly matches the RFC822 standard. No characters outside the ASCII range 0-127 are allowed
 * @author Hamish Friedlander
 */
class Rfc822 extends Parser
{

/*!* Rfc822

crlf: /\r\n/

lwsp_char: " " | "\t"

linear_white_space: (crlf? lwsp_char)+

atom: /[^\x00-\x1F\x20()<>@,;:\\".\[\]\x80-\xFF]+/

qtext_chars: /[^"\\\x0D]+/

qtext: linear_white_space | qtext_chars

quoted_pair: /\\[\x00-\x7F]/

quoted_string: .'"' ( quoted_pair | qtext )* .'"'

word: atom | quoted_string

phrase: (word>)+

dtext_chars: /[^\[\]\\\r]+/

dtext: linear_white_space | dtext_chars

domain_literal: "[" ( dtext | quoted_pair )* "]"

domain_ref: atom

sub_domain: domain_ref | domain_literal

domain: sub_domain ("." sub_domain)*

route: "@" domain ("," "@" domain)* ":"

route_addr: "<" route? addr_spec ">"
	function addr_spec ( &$self, $sub ) {
		$self['addr_spec'] = $sub['text'] ;
	}

local_part: word ("." word)*

addr_spec: local_part "@" domain

mailbox: ( addr_spec | phrase? route_addr ) >
	function __construct( &$self ) {
		$self['phrase'] = NULL ;
		$self['address'] = NULL ;
	}
	function phrase ( &$self, $sub ) {
		$self['phrase'] = $sub['text'] ;
	}
	function addr_spec ( &$self, $sub ) {
		$self['address'] = $sub['text'] ;
	}
	function route_addr ( &$self, $sub ) {
		$self['address'] = $sub['addr_spec'] ;
	}

group: phrase ":" ( mailbox ("," mailbox)* )? ";"
  function __construct(&$self) {
    $self['phrase'] = null;
    $self['addresses'] = array();
  }
	function phrase ( &$self, $sub ) {
		$self['phrase'] = $sub['text'] ;
	}
  function mailbox(&$self, $sub) {
    $self['addresses'][] = $sub;
  }

address: :mailbox | :group

address_header: address (<","> address)*
	function __construct( &$self ) {
		$self['addresses'] = array() ;
	}
	function address( &$self, $sub ) {
		$self['addresses'][] = isset($sub['mailbox']) ? $sub['mailbox'] : $sub['group'] ;
	}

*/

}

//$p = new Rfc822(<<<EOS
//Mary Smith <mary@x.test>, jdoe@example.org, Who? <one@y.test>, <boss@nil.test>, "Giant; \"Big\" Box" <sysservices@example.net>, A Group:Ed Jones <c@a.test>, Undisclosed recipients:, Pete <pete@silly.example>
//EOS
//);
$p = new Rfc822(<<<EOS
A Group:Ed Jones <c@a.test>;, Undisclosed recipients:;, Pete <pete@silly.example>
EOS
);
print_r( $p->match_address_header() ) ;
