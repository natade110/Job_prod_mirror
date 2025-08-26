<?php 

	include "db_connect.php";

	//set "the_year"
	$the_year = "2013";
	
	//
	if($_GET["the_year"]){
		
		$the_year = $_GET["the_year"]*1;
		
	}
	
	$sql = "select cid from company_to_reconcile_02 where reconciled = 0 limit 0,1";
	
	$cid_to_reconcile = getFirstItem($sql);


?>

Reconcile ID <?php echo $cid_to_reconcile;?>


<br /><br />
Openning reconcile window..

<script>

	window.open("organization.php?id=<?php echo $cid_to_reconcile; ?>&year=<?php echo $the_year; ?>&auto_post=1","reconcile_windows_<?php echo $cid_to_reconcile % 20;?>");

</script>


<br /><br />
Reconciled - update reconcile flag

<?php 

	mysql_query("update company_to_reconcile set reconciled = 1 where cid = '".$cid_to_reconcile."'");

?>

