<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	//table name
	$table_name = "announcement";
	$this_id = doCleanInput($_POST["announcement_id"]);
		
	//specify all posts fields
	$input_fields = array(
	
						'ANum'
						,'GovDocNo'
						,'newspaper_id'
						,'Topic'
						,'Cancelled'
						
						);
	
	$special_fields = array(
								'ADate'
								,'NewspaperDate'
								);
	
	$announce_date = $_POST["announce_date_year"]."-".$_POST["announce_date_month"]."-".$_POST["announce_date_day"];
	$news_date = $_POST["news_date_year"]."-".$_POST["news_date_month"]."-".$_POST["news_date_day"];			
	
	
	//echo $cash_date; exit();
	$special_values = array("'$announce_date'"
								,"'$news_date'"
								);
	
	$file_fields = array(						
						"announce_docfile"
						);
					
	for($i = 0; $i < count($file_fields); $i++){
	
		$hire_docfile_size = $_FILES[$file_fields[$i]]['size'];
		if($hire_docfile_size > 0){
			$hire_docfile_type = $_FILES[$file_fields[$i]]['type'];
			$hire_docfile_name = $_FILES[$file_fields[$i]]['name'];
			$hire_docfile_exploded = explode(".", $hire_docfile_name);
			$hire_docfile_file_name = $hire_docfile_exploded[0]; 
			$hire_docfile_extension = $hire_docfile_exploded[1]; 
			
			//new file name
			$new_hire_docfile_name = date("dmyhis").rand(00,99)."_".$hire_docfile_file_name; //extension
			$hire_docfile_path = $announce_docfile_relate_path . $new_hire_docfile_name . "." . $hire_docfile_extension; 
			//echo $hire_docfile_path;
			//
			if(move_uploaded_file($_FILES[$file_fields[$i]]['tmp_name'], $hire_docfile_path)){	
				//move upload file finished
				//array_push($special_fields,$file_fields[$i]);
				//array_push($special_values,"'".$new_hire_docfile_name.".".$hire_docfile_extension."'");
				
				//file uploaded, add record to files table
				$sql = "insert into files(
							file_name
							, file_for
							, file_type)
						values(
							'".$new_hire_docfile_name.".".$hire_docfile_extension."'
							,'$this_id'
							,'".$file_fields[$i]."'
						)";
				
				mysql_query($sql);
			}
		}else{
			
			//no new file uploaded, retain old file name in db
			//array_push($special_fields,$file_fields[$i]);
			//array_push($special_values,"'".getFirstItem("select ".$file_fields[$i]." from $table_name where pay_id = '".doCleanInput($_POST["LID"])."'")."'");
			
			//do nothing
		
		}
	
	}
	
	$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, " where AID = '$this_id'");
	
	//echo $the_sql;exit();
	mysql_query($the_sql);
	
	header("location: view_announce.php?id=$this_id&updated=updated");

?>