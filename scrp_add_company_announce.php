<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	//try to add more company to this receipt
	$AID = $_POST["AID"]*1;
	
	//first get RID information
	
	$this_AID = $AID;					
	
	///----------------------------------------------
	//add payments to all selected company
	///----------------------------------------------
	//prepare "common vars" of every payment
	//FIRST, see how many checkboxes are checked
	$post_records = $_POST["total_records"]*1;
	
	
	$table_name = "announcecomp";
	//$payment_method = $_POST["PaymentMethod"];

	if($_POST["send_to_all"]){
	
		$condition = ($_POST["send_to_all"]);
		
		$get_org_sql = "SELECT *
						FROM company z
						
						
						LEFT JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
						LEFT JOIN provinces c ON z.province = c.province_id
						
						where
							1=1														
							$condition
						order by CompanyNameThai asc
						";
		//echo $get_org_sql;
		$org_result = mysql_query(stripslashes($get_org_sql));
		
		while ($post_row = mysql_fetch_array($org_result)) {
			//echo "1";
			
			$selected_company = $post_row["CID"];
			
			$special_fields = array("AID"
									,"CID"
												
									);
			$special_values = array("'$this_AID'"
								,"'$selected_company'"
								
								);	
			
			$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace");
			
			mysql_query($the_sql);
			
		}
	
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
				
				
				
				$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace");
				
				//insert each "main" letters to db
				//echo $the_sql;exit();
				mysql_query($the_sql);
				//exit();
				
			}
		}
	}
	
	//then redirect to whatever page
	//header("location: org_list.php?mode=lettersl&letter_added=letter_added");
	header("location: view_announce.php?id=$AID&company_added=company_added");

?>