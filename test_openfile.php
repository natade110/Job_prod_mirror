<?php

error_reporting(E_ALL);

$file_handle = fopen("to_import/12091112403574_to_import_2013.csv");
			
			
while (!feof($file_handle) ) {

	
	
	$line_of_text = fgets($file_handle);
	
	echo $line_of_text;

}								

?>