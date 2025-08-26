<?php

session_start();

$current_path = basename($_SERVER['PHP_SELF']);

if($law_access || $_SESSION['law_access']){

	//law access ครั้งแรก blind ค่าไว้สำหรับ header เมื่อมีเปลี่ยน url
	if($law_access){
		$_SESSION['law_year'] = $the_year;
		$_SESSION['law_cid'] = $the_cid;
	}
	//law access แล้ว
	$_SESSION['law_access'] = 1;
	$sess_accesslevel = 8;
	if($_SESSION['law_access'] == 1 && $current_path !== 'add_invoice_pro.php' && $current_path !== 'scrp_delete_invoice.php'){
		header("Location: add_invoice_pro.php?search_id=" . $_SESSION['law_cid'] . "&mode=payment&for_year=" . $_SESSION['law_year'] . "&law_access=1");
	}
}else{

	if(isset($_SESSION['sess_userid'])){
		$sess_userid = $_SESSION['sess_userid'];
	}
	if(!isset($sess_userid)){
		if(isset($_GET["id"])){
			$back_to = $_GET["id"];
			$header_to_use = 'location: index.php?cont='.$back_to;
		}else{
			$header_to_use = "location: index.php";
		}
		header($header_to_use);
	}


}
