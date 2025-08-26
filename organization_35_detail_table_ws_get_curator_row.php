<?php			

include "db_connect.php";
include "scrp_config.php";
include "session_handler.php";

$file_url = "https://ejob.dep.go.th/ejob/hire_docfile/";

//echo "----";
//print_r($_POST);
//echo "asdasd"; exit();
if($_GET){	
	$post_row = $_GET;
}else{
	$_GET = $_POST;
	$post_row = $_GET;
}

if(is_numeric($_GET["curator_id"]) && !$_POST["do_cancel_edit"]){
		
	//pre-fill curator
	$is_edit_curator = 1;


	$popup_35_table_name = "curator";

	if($_GET[extra]){
		$popup_35_table_name = "curator_extra";
	}

	//yoes 20160218 --- extra condition for company
	if($sess_accesslevel == 4){
		$popup_35_table_name = "curator_company";
	}


	$curator_id_to_fill = $_GET["curator_id"];

	$the_sql = "select 
					* 
				from 
					$popup_35_table_name a

						left join curator_meta b
							on a.curator_id = b.meta_curator_id
							and
							meta_for = 'child_of' 

				where 
					curator_id = '$curator_id_to_fill' 

					";

	//echo $the_sql;


	$curator_row_to_fill = getFirstRow($the_sql);


	//yoes 20160120 ---> also get curator's usee
	$the_sql = "select * from $popup_35_table_name where curator_parent = '$curator_id_to_fill'";					
	$usee_row_to_fill = getFirstRow($the_sql);

	//echo $the_sql;

	//echo $curator_row_to_fill[0];

	if($curator_row_to_fill["curator_parent"] == 0){
		$is_curator_parent = 1;
	}

}


//echo "rly?";
$return_array[curator_user] = $curator_row_to_fill;
$return_array[curator_usee] = $usee_row_to_fill;

echo json_encode($return_array);	

exit();

if(is_numeric($_GET["leid"])){
												
	//if have leid then populate defaul value.....

	if($sess_accesslevel == 4){

		$leid_row = getFirstRow("select 
				* 
				from 
				lawful_employees_company
				where le_id = '".doCleanInput($_GET["leid"])."'");

	}else{

		$leid_row = getFirstRow("


				select 
					* 
				from 
					lawful_employees a
						left join
							lawful_employees_meta b
								on a.le_id = b.meta_leid and meta_for = 'child_of'																	

				where 
					le_id = '".doCleanInput($_GET["leid"])."'


				");

	}

	// append attach files
	$sql = "
		select 
			* 
			from 
				files 
			where 
				file_for = '".doCleanInput($_GET["leid"])."'
				and
				(
				
					file_type = 'docfile_33_1'																						
					or
					file_type = 'docfile_33_2'
					or
					file_type = 'docfile_33_71'
				)		
	
	";
	$ff = mysql_query($sql);
	while($f = mysql_fetch_array($ff)){
		$fArr = preg_split("/\d+_/",$f['file_name']);
		$leid_row[$f['file_type']] = '<a href="'.$file_url.substr($f['file_name'],5).'" target=_blank>'.$fArr[1].'</a>';
	
	}


	// append 	is_extra_33
	$sql = "
	select 
		meta_value 
	from 
		lawful_employees_meta 
	where 
		meta_for = 'is_extra_33' and meta_leid = '".$leid_row["le_id"]."'"	
	;
	$is_extra_33 = getFirstItem($sql);
	$leid_row['is_extra_33'] = $is_extra_33['meta_value'];


	// append le_33_parent
	$sql = "														
	select
		*
	from
		lawful_employees
	where
		le_end_date != '0000-00-00'
		and le_cid = '".$leid_row['le_cid']."'
		and le_year = '".$leid_row['le_year']."'
		and
	
		(
			le_id not in (
			
				select
					meta_value
				from
					lawful_employees_meta
				where
					meta_for = 'child_of'
					
			
			)
			or
			le_id in (
			
				select
					meta_value
				from
					lawful_employees_meta
				where
					meta_for = 'child_of'
					and
					meta_leid = '".($leid_row["le_id"]*1)."'
			
			)
		)
		
		and
		le_id != '".($leid_row["le_id"]*1)."'

	";	
	$ll = mysql_query($sql);
	$retStr = "<option value='0' left_date='0000-00-00'>-- ไม่ได้เป็นการรับแทน --</option>";
	while($l = mysql_fetch_array($ll)){
		$retStr .="<option left_date='".$l['le_end_date']."' value='".$l['le_id']."'";
		if($leid_row["meta_value"] == $l['le_id']) $retStr .= " selected=selected";
		$retStr .= ">".$l['le_code']." : ". $l['le_name']." : จ้างงานวันที่ ".formatDateThaiShort($l['le_start_date'],0)." ถึง ".formatDateThaiShort($l['le_end_date'],0);
		$retStr .= "</option>";

	}
	$leid_row['le_33_parent'] = $retStr;

	
	echo json_encode($leid_row);
	exit();

}


//yoes 20150118 -- extra records
if(is_numeric($_GET["leidex"])){

		$leid_row = getFirstRow("select 
				* 
				from 
				lawful_employees_extra
				where le_id = '".doCleanInput($_GET["leidex"])."'");

		$leid_row["is_extra_row"] = 1;
	
	
		echo json_encode($$leid_row);
		exit();


}