<?php 

	$upload_folder = "./to_import/";
	
	
	//first see how many file we have now	
	$files = glob($upload_folder . '*.zip');
					
	foreach ($files as $filename) {
		$old_zip_file = $filename;			
		//echo ($filename);
		//echo "<br>".$upload_folder."/old_files/" .str_replace($upload_folder,"",$old_zip_file);
		//move_uploaded_file ( $old_zip_file ,$upload_folder."old_files/" .str_replace($upload_folder,"",$old_zip_file));
		rename ( $old_zip_file ,$upload_folder."old_files/" .str_replace($upload_folder,"",$old_zip_file));
		doUploadOrgLog($sess_userid, "ย้ายไฟล์ .zip เดิมไปไว้ที่ backup", str_replace($upload_folder,"",$old_zip_file));	
	}	
	
	///then unlink all files
	$files = glob($upload_folder . '*.*');
					
	foreach ($files as $filename) {
		unlink($filename);
	}	
	
	 //delete current temp table to prepare file import
	  $sql = "truncate table company_temp_all";
	  mysql_query($sql) or die(mysql_error());
	  doUploadOrgLog($sess_userid, "เตรียมการนำเข้าข้อมูล", "");	
	  
?>