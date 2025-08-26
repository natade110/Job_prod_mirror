<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	$table_name = "announcement";

	$input_fields = array(
						'ANum'
						,'GovDocNo'
						,'newspaper_id'
						,'Topic'
						,'Cancelled'
						
						);

	$announce_date = $_POST["announce_date_year"]."-".$_POST["announce_date_month"]."-".$_POST["announce_date_day"];
	$news_date = $_POST["news_date_year"]."-".$_POST["news_date_month"]."-".$_POST["news_date_day"];			
							
	$special_fields = array("ADate","NewspaperDate");
	$special_values = array("'$announce_date'","'$news_date'");							
						
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	//echo $the_sql; exit();	
	mysql_query($the_sql);
	$this_AID = mysql_insert_id();					
	
	///----------------------------------------------
	//add announcement to all selected company
	///----------------------------------------------
	
	$post_records = $_POST["total_records"]*1;
	
	
	$table_name = "announcecomp";
				
	
	
	if($_POST["send_to_all"]){
	
		//echo "do send to all";exit();
		$condition = ($_POST["send_to_all"]);
		
		//echo $condition; exit();
		$cur_year = $_POST["cur_year"];
		
		$get_org_sql = "SELECT z.CID as the_cid
						FROM company z
										
										
										LEFT JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
										LEFT JOIN provinces c ON z.province = c.province_id
										JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year' 
										
										where
											1=1																	
							$condition
						
						";
						
		
		if($cur_year > 2012){
		
			$get_org_sql = "SELECT z.CID as the_cid
						FROM company z
										
										
										LEFT JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
										LEFT JOIN provinces c ON z.province = c.province_id
										JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year' 
										,(
											SELECT 
									
												CompanyCode as the_sub_companycode
												,sum(Employees) as the_sub_sum
									
											 FROM 
												company
												
											group by 
												
												CompanyCode	
												
											having 
												sum(Employees) > 99	
										)e
										where
											1=1				
											and
											z.CompanyCode = e.the_sub_companycode														
							$condition
						
						";
		
		
		
		}
						
						
						
						
		//echo $get_org_sql;
		$org_result = mysql_query(stripslashes($get_org_sql));
		
		while ($post_row = mysql_fetch_array($org_result)) {
			//echo "1";
			
			$selected_company = $post_row["the_cid"];
				
			$special_fields = array("AID"
										,"CID"
														
									);
			$special_values = array("'$this_AID'"
								,"'$selected_company'"
								
								);	
									
			$the_sql = generateInsertSQL($_POST,$table_name,$input_fields_blank,$special_fields,$special_values,"replace") . ";";
			

			mysql_query($the_sql);									
			
			
		}
		//echo $the_sql;
		//exit();
	}else{
	
		//for each selected companies, do insert
		for($i=1 ; $i<=$post_records ; $i++){
			if($_POST["chk_$i"]){
				
				$selected_company = $_POST["chk_$i"];
				
				
				$special_fields = array("AID"
										,"CID"
														
										);
				$special_values = array("'$this_AID'"
									,"'$selected_company'"
									
									);	
				
				
				
				$the_sql = generateInsertSQL($_POST,$table_name,$input_fields_blank,$special_fields,$special_values,"replace");
				
				//insert each "main" letters to db
				//echo $the_sql;exit();
				mysql_query($the_sql);
				//exit();
				
			}
		}
	}
	
	//---> handle attached files
	$file_fields = array(
						"announce_docfile"
						);
						
	for($i = 0; $i < count($file_fields); $i++){
	
		//echo "filesize: ".$hire_docfile_size;
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
				
				$sql = "insert into files(
						file_name
						, file_for
						, file_type)
					values(
						'".$new_hire_docfile_name.".".$hire_docfile_extension."'
						,'$this_AID'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
			
			
		}
	
	}
	
	//then redirect to whatever page
	//header("location: org_list.php?mode=lettersl&letter_added=letter_added");
	header("location: view_announce.php?id=$this_AID&payment_added=payment_added");

?>