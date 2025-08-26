<?php
	
	$the_url2 = "https://wsfund.dep.go.th/index.php";
	
	
?>JOB - >Try file_get_contents() from <?php echo $the_url2;?>:  <font color=green><?php

	$html = file_get_contents("$the_url2");
	
	echo $html; ?></font>