<?php 

	include "db_connect.php";
	
	$sql = "SELECT reconciled as the_type, count(reconciled) as the_count FROM `company_to_reconcile_with_year` group by reconciled";
	
	$the_result = mysql_query($sql);
	
	while ($post_row = mysql_fetch_array($the_result)) {
		
		echo "<br>".$post_row["the_type"]." - " . $post_row["the_count"];
		
	}
	
	
?>

