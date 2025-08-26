<?php			

include "db_connect.php";
include "scrp_config.php";
include "session_handler.php";

$file_url = "https://ejob.dep.go.th/ejob/hire_docfile/";

//print_r($_POST);
//echo "asdasd"; exit();
if($_GET){	
	$post_row = $_GET;
}else{
	$_GET = $_POST;
	$post_row = $_GET;
}

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
	
	//$leid_row['le_33_parent'] = $retStr;
	//yoes 20211103
	$leid_row['le_33_parent'] = $leid_row["meta_value"];

	
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