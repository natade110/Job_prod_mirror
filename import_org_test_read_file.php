<?php

	$file="uploads/nay301.txt";
	$linecount = 0;
	$handle = fopen($file, "r");
	while(!feof($handle)){
	  $line = fgets($handle);
	  $linecount++;
	}
	
	fclose($handle);
	
	echo $linecount;

?>