<?php

	include "db_connect.php";
	include "session_handler.php";
	
	error_reporting(1);
	ini_set('max_execution_time', 600);
	ini_set("memory_limit","256M");
	
	
	
	//first check if current batach is running....
	$have_current_batch = getFirstItem("select var_value from vars where var_name = 'upload_school_file' and var_value > now()");
	//echo $have_current_batch;
	if($have_current_batch){
		header("location: import_school_new.php");
		exit();	
	}
	
	include "scrp_do_delete_import_school_file.php";
	
	

	if($_POST["upload_file"]){
	
		//echo "whag";	
		$file_size = $_FILES["input_file"]['size'];
		$file_type = $_FILES["input_file"]['type'];		
		$file_name = $_FILES["input_file"]['name'];
		$new_file_name = date("ymdhis").rand(00,99)."_".$file_name;
		$file_new_path = $upload_folder.$new_file_name;
		
		//echo $upload_folder; exit();
		
		/*
		echo $file_size;
		echo $file_type;
		echo $file_name;
		exit();
		*/
		
		
		//validation
		if($file_type != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){		
			//none zip = error
			doUploadOrgLog($sess_userid, "ไฟล์ที่อัพโหลด ไม่ใช่ .xlsx", $new_file_name);	
			header ("location: import_org_school.php?xlsx=no");	 exit();
		}
		if($file_size > 25000000){		
			//none zip = error
			doUploadOrgLog($sess_userid, "ไฟล์ที่อัพโหลด มีขนาดเกิน 25mb", $new_file_name);	
			header ("location: import_org_school.php?filesize=no");	 exit();
		}
		
		
		
		if(move_uploaded_file($_FILES["input_file"]['tmp_name'], $file_new_path)){
			
			//echo "what"; exit();
			
			//echo "uploaded";
			doUploadOrgLog($sess_userid, "อัพโหลด .xlsx ไฟล์เสร็จสิ้น", $new_file_name);	
						
		}else{
			 doUploadOrgLog($sess_userid, "Move-upload ไฟล์ไม่ได้", $new_file_name);	
			  header ("location: import_org_school.php?moveupload=1");	 exit();
		}
		
		doUploadOrgLog($sess_userid, "อัพโหลดไฟล์โรงเรียนเสร็จสิ้น", $new_file_name);	
	    header ("location: import_org_school.php?ok=ok");	 exit();
		
	}
?>