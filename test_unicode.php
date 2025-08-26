<?php 

	$string = "\u0E19\u0E07\u0E04\u0E23\u0E32\u0E0D";
	$string = "U+0E19U+0E07U+0E04U+0E23U+0E32U+0E0D";
	
	echo utf8_decode ( $string );
	echo "<br>";
	echo utf8_encode ( $string );
	echo "<br>";
	echo html_entity_decode(preg_replace("/U\+([0-9A-F]{4})/", "&#x\\1;", $string), ENT_NOQUOTES, 'UTF-8');
	echo "<br>";
	echo unicodeToUtf8($string);
	
	
	function unicodeToUtf8($what){
		
		$string = str_replace("\u", "U+", $what);
		
		return html_entity_decode(preg_replace("/U\+([0-9A-F]{4})/", "&#x\\1;", $string), ENT_NOQUOTES, 'UTF-8');
		
	}

?>