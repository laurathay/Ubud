<?php

class SPP_SimplePie_Parser_Ext extends SimplePie_Parser {
	public function parse(&$data, $encoding) {
		
		// Cut whitespace from start of raw data
		$whitespace = strspn($data, "\x09\x0A\x0D\x20");
		if( $whitespace > 0 ) {
			$data = substr( $data, $whitespace );
		}
		
		return parent::parse($data, $encoding);
	}
}
