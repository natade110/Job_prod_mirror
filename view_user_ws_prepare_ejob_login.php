<?php			

include "db_connect.php";
include "scrp_config.php";
include "session_handler.php";



//print_r($_POST);
//echo "asdasd"; exit();
if($_GET){	
	$post_row = $_GET;
}else{
	$_GET = $_POST;
	$post_row = $_GET;
}

$user_id = $sess_userid*1;
$meta_id = $post_row[meta_id]*1;


$sql = "

	insert into
		user_transaction(		
		
			trans_user_id
			, trans_meta_id
			, trans_code
			, trans_created_datetime
		
		)values(
		
			'$user_id'
			, '$meta_id'
			, MD5(RAND())
			, now()
		
		)

";

mysql_query($sql);


//then get the md5 thing

$trans_code = getFirstItem("select trans_code from user_transaction where trans_id = '".mysql_insert_id()."'");


$result_array = array();

$result_array[trans_code] = $trans_code;

echo json_encode($result_array);

/*echo $sess_userid;
echo "-";
echo $meta_id;*/