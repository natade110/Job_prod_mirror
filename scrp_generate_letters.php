<?php

	include "db_connect.php";
	
	//print_r($_POST);
	
	//FIRST, see how many checkboxes are checked
	$post_records = $_POST["total_records"]*1;
	
	//then generate "main" letter
	$table_name = "documentrequest";
			
	$input_fields = array(
						
						
						'GovDocumentNo' 
						,'RequestNum' 

						,'is_hold_letter'
						,'hold_details'

						);
						
	$request_date = $_POST["RequestDate_year"]."-".$_POST["RequestDate_month"]."-".$_POST["RequestDate_day"];
	
	
	$special_fields = array("ModifiedDate", "ModifiedBy", "Year", "RequestDate");
	$special_values = array("NOW()", "'$sess_userid'", "'".$_POST["ddl_year"]."'", "'$request_date'");		
	
	
	//check if name/seq already existed...
	
	$sql = "select RID from documentrequest where 
				(
					RequestNum = '".doCleanInput($_POST["RequestNum"])."' 
					and 
					GovDocumentNo = '".doCleanInput($_POST["GovDocumentNo"])."'
				)
			 limit 0,1";
	$existed = getFirstItem($sql);
	
	//echo $sql; exit();
	
	
	if($existed){
		
		if(isset($_POST["search_id"])){
		
			header("location: org_list.php?mode=letters&id=$this_id&duped_key=duped_key&doc_no=".$_POST["GovDocumentNo"]."&doc_seq=".$_POST["RequestNum"]."&doc_id=".$existed."&search_id=".$_POST["search_id"]."&for_year=".$_POST["ddl_year"]);
			exit();
		
		}else{		
			header("location: org_list.php?mode=letter&id=$this_id&duped_key=duped_key&doc_no=".$_POST["GovDocumentNo"]."&doc_seq=".$_POST["RequestNum"]."&doc_id=".$existed."");
			exit();
		}
		
	}
				
						
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	
	//insert each "main" letters to db
	//echo $the_sql;exit();
	mysql_query($the_sql);
	//exit();
	$new_letter_id = mysql_insert_id();
	
	//-----> what doc to insert to child table (DocRequestCompany)
	
	//try to generate insert statement for each org...
	$table_name = "docrequestcompany";
	
	$input_fields = array(
							
							'DocBKK1' 
							,'DocBKK2' 
							,'DocBKK3' 
							,'DocBKK4' 
							,'DocPro1' 
							,'DocPro2' 
							,'DocPro3' 
							,'DocPro4' 
							,'DocPro5' 
							,'DocPro6' 
							,'DocPro7' 
	
							);
									
	$special_fields = array(
							
							'RID'
							,'CID'
							,'ModifiedDate'
							,'ModifiedBy'
							);
	
	if($_POST["send_to_all"]){
	
		//echo "do send to all";exit();
		$condition = ($_POST["send_to_all"]);
		
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
		
		
		
			$get_org_sql = "SELECT z.CID as the_cid
						
						
								FROM company z
										
										
										LEFT JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
										LEFT JOIN provinces c ON z.province = c.province_id
										JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year' 
										
										
										
										where
											1=1		
																								
											
											$condition
						
						";
		
		}
						
						
		//echo $get_org_sql; exit();
		
		
		$org_result = mysql_query(stripslashes($get_org_sql));
		while ($post_row = mysql_fetch_array($org_result)) {
			//echo "1";
			
			$org_id = $post_row["the_cid"];
				
			$special_values = array(
									"'".$new_letter_id."'"
									, "'".$org_id."'"
									, "NOW()"
									, "'$sess_userid'");	
									
			$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace") . ";";
			
			
			//echo $the_sql;
			mysql_query($the_sql) or die (mysql_error());									
			
			
		}
		
		//exit();
	}else{
		
		for($i=1 ; $i<=$post_records ; $i++){
			if($_POST["chk_$i"]){
				
				$org_id = $_POST["chk_$i"];
				
				$special_values = array(
										"'".$new_letter_id."'"
										, "'".$org_id."'"
										, "NOW()"
										, "'primary_user'");	
									
				$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace");
				
				//insert each "checked" letters to db
				//echo "<br>".$the_sql;exit();
				mysql_query($the_sql);
				//$new_id = mysql_insert_id();
				
				//---> finished main "letters", insert child letters
				
			}
		}
	}
	
	
	//then redirect to whatever page
	//header("location: org_list.php?mode=lettersl&letter_added=letter_added");
	
	if($_POST["is_hold_letter"]){
		header("location: view_letter.php?id=$new_letter_id&letter_added=letter_added&type=hold");
	}else{
	
		header("location: view_letter.php?id=$new_letter_id&letter_added=letter_added");
	}

?>