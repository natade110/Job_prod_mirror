<?php
	
	
	include "db_connect.php";
	
	//also see if there are any attached files
	$curator_file_path = getFirstItem("select 
											file_name 
									   from 
											 files 
										where 
											file_for = '".$_GET["file_for"]."'
											and
											file_type = 'curator_docfile'
											");

     //BUILD THE FILE INFORMATION
     $file = "/hire_docfile/$curator_file_path";
	 
	 //echo $file;exit();
 
     //CREATE/OUTPUT THE HEADER
     header("Content-type: application/force-download");
     header("Content-Transfer-Encoding: Binary");
     header("Content-length: ".filesize($file));
     header("Content-disposition: attachment; filename=\"".basename($file)."\"");
     readfile($file);
?>