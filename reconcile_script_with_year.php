<?php 

	include "db_connect.php";
	
	$sql = "select cid, year from company_to_reconcile_with_year where reconciled = 0 limit 0,1";
	
	//$reconcile_row = array();
	
	$reconcile_row = getFirstRow($sql);
	
	//print_r($reconcile_row);
	
	$cid_to_reconcile = $reconcile_row["cid"];
	$year_to_reconcile = $reconcile_row["year"];
	
	echo "cid to reconcile: " . $cid_to_reconcile . "<br>";
	echo "year to reconcile: " . $year_to_reconcile . "<br>";


	if(!$cid_to_reconcile){echo "all company reconciled successfully"; exit();}

?>

Reconcile ID <?php echo $cid_to_reconcile;?>


<br /><br />
Openning reconcile window..

<script>

	window.open("organization.php?id=<?php echo $cid_to_reconcile; ?>&year=<?php echo $year_to_reconcile;?>&auto_post=1&reconcile_by_bot=1","reconcile_windows_<?php echo $cid_to_reconcile % 20;?>");

</script>


<br /><br />
Reconciled - update reconcile flag

<?php 

	//mysql_query("update company_to_reconcile_with_year set reconciled = 1 where cid = '".$cid_to_reconcile."' and year = '". $year_to_reconcile ."'");

?>

