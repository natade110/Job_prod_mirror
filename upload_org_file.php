<?php

	include "db_connect.php";
	include "session_handler.php";
	
	error_reporting(1);
	ini_set('max_execution_time', 600);
	ini_set("memory_limit","256M");
	
	
	
	//first check if current batach is running....
	$have_current_batch = getFirstItem("select var_value from vars where var_name = 'upload_org_file' and var_value > now()");
	//echo $have_current_batch;
	if($have_current_batch){
		header("location: import_org_new.php");
		exit();	
	}
	
	include "scrp_do_delete_import_org_file.php";
	
	

	if($_POST["upload_file"]){
	
		//echo "whag";	
		$file_size = $_FILES["input_file"]['size'];
		$file_type = $_FILES["input_file"]['type'];		
		$file_name = $_FILES["input_file"]['name'];
		$new_file_name = date("ymdhis").rand(00,99)."_".$file_name;
		$file_new_path = $upload_folder.$new_file_name;
		
		//echo $file_size; exit();
		
		
		//validation
		if($file_type != "application/octet-stream"){		
			//none zip = error
			doUploadOrgLog($sess_userid, "ไฟล์ที่อัพโหลด ไม่ใช่ .zip", $new_file_name);	
			header ("location: import_org_new.php?zip=no");	 exit();
		}
		if($file_size > 25000000){		
			//none zip = error
			doUploadOrgLog($sess_userid, "ไฟล์ที่อัพโหลด มีขนาดเกิน 25mb", $new_file_name);	
			header ("location: import_org_new.php?filesize=no");	 exit();
		}
		
		
		
		if(move_uploaded_file($_FILES["input_file"]['tmp_name'], $file_new_path)){
			
			//echo "what"; exit();
			
			//echo "uploaded";
			doUploadOrgLog($sess_userid, "อัพโหลด .zip ไฟล์เสร็จสิ้น", $new_file_name);	
			
			$zip = new ZipArchive;
			$res = $zip->open($file_new_path);
			if ($res === TRUE) {
			  $zip->extractTo($upload_folder);
			  $zip->close();
			  doUploadOrgLog($sess_userid, "unzip ไฟล์เสร็จสิ้น", $new_file_name);
			  
			  
			  //check how many file is in dir?
			  // integer starts at 0 before counting
				$i = 0; 
				$dir = $upload_folder;
				if ($handle = opendir($dir)) {
					while (($file = readdir($handle)) !== false){
						if (!in_array($file, array('.', '..')) && !is_dir($dir.$file)) 
							$i++;
					}
				}
				// prints out how many were in the directory
				//echo "There were $i files";exit();
				if($i > 2){
					doUploadOrgLog($sess_userid, "zip ไฟล์มีไฟล์อยู่ข้างในมากกว่า 1 ไฟล์", $new_file_name);
					header ("location: import_org_new.php?multiplezip=no");	 exit();
				}
				
				
				$files = glob($upload_folder . '*.txt');
				
				//yoes 20190212 --> add CSV support
				$files_csv = glob($upload_folder . '*.csv');
				
				if ( $files !== false || $files_csv !== false)
				{
					$filecount = count( $files );
					//echo $filecount;
				}
				else
				{
					doUploadOrgLog($sess_userid, "ไม่พบไฟล์ .txt หรือ .csv ใน zip", $new_file_name);
					header ("location: import_org_new.php?notext=no");	 exit();
				}
			  
			  	
			 
			  doUploadOrgLog($sess_userid, "อัพโหลดไฟล์เสร็จสิ้น", $new_file_name);	
			  header ("location: import_org_new.php?ok=ok");	 exit();
			  
			} else {
			  doUploadOrgLog($sess_userid, "unzip ไฟล์ไม่ได้", $new_file_name);	
			  header ("location: import_org_new.php?unzip=no");	 exit();
			}
		}else{
			 doUploadOrgLog($sess_userid, "Move-upload ไฟล์ไม่ได้", $new_file_name);	
			  header ("location: import_org_new.php?moveupload=1");	 exit();
		}
		
	}
?>