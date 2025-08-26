<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST); exit();
	include "session_handler.php";
	
	
	//table name
	$table_name = "lawfulness";
	$this_id = doCleanInput($_POST["CID"]);
	$this_year = doCleanInput($_POST["Year"]);
	
	if($this_year >= 2013){
		$is_2013 = 1;	
	}
	
	//yoes 20151222 --> try this auto post thing
	//allow this for all years
	$is_2013 = 1;
		
	//specify all posts fields
	$input_fields = array(
						'CID'
						
						
						
						,'Hire_status'
						
						
						,'Conc_status'
						,'Conc1_status'
						,'Conc2_status'
						,'Conc3_status'
						,'Conc4_status'
						,'Conc5_status'
						
						,'pay_status'
						
						
						
						,'Year'
						,'NoRecipient'
						,'NoRecipient_remark'
						
						,'lawful_order'
						
						
						
						);
	
	if($sess_accesslevel != 4){
		//non-company also update lawful status
				
		//yoes 08 oct 2012 -> try do auto update
		
		if($is_2013){		
		
			$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_$this_year'"),100);
		
			$employee_ratio = getEmployeeRatio(deleteCommas($_POST["Employees"]), $ratio_to_use);
			
			$total_money_needed = $employee_ratio * $_POST["money_per_person"];
			//echo $employee_ratio;exit();
			
			
			$_POST["Hire_status"] = 0;
			$_POST["Conc_status"] = 0;
			$_POST["pay_status"] = 0;
			
			if($_POST["Hire_NumofEmp"] > 0){			
				//has rule 34
				$has_34 = 1;			
				$_POST["Hire_status"] = 1;
			}
			
			if($_POST["have_receipt"] > 0){
				$has_payment = 1;			
				$_POST["pay_status"] = 1;	
			}
			
			if($_POST["curator_usee"] > 0){
				$has_35 = 1;				
				$_POST["Conc_status"] = 1;
			}
			
			
			
			
			//next, check rule 34, payment, rule 35
			//echo $_POST["Hire_NewEmp"]; 
			//echo $employee_ratio;
			//exit();
			
			if($_POST["Hire_NewEmp"] >= $employee_ratio){
			
				//need to hire anymore people? -> if no then -> rule 34 and rule 35 is ok
				$is_34_ok = 1;
				$is_35_ok = 1;				
				
			
			}
			
			//if($_POST["the_final_money"] <= 0 && $_POST["total_paid"] >= $total_money_needed){
				
			//echo $_POST["the_final_money"]; exit();
			if($_POST["the_final_money"] <= 0 ){
			
				//echo $_POST["the_final_money"] ;exit();
				$is_payment_ok = 1;
				
			}
			
			
			//if all condition are satisfied -> then this is "lawful"
			//if($is_34_ok && $is_35_ok && $is_payment_ok){
			
			//if payment is ok then we assume that all other stuffs is ok
			if($is_payment_ok){

				$_POST["lawfulStatus"] = 1;
							
			}elseif($has_34 || $has_payment || $has_35){
				
				//else if have some then -> partial
				$_POST["lawfulStatus"] = 2;
			
			}else{
			
				//else -> this is unlawful
				$_POST["lawfulStatus"] = 0;
			
			}
		
		
		
			//check number of employees -> this is the most simple
			if(deleteCommas($_POST["Employees"]) < $ratio_to_use){
			
				$_POST["lawfulStatus"] = 3;
			
			}
		
		}
		
		array_push($input_fields,'lawfulStatus');
	}
						
	
	//fields not from $_post	
	//$special_fields = array("year");
	//$special_values = array("'".doCleanInput($_POST["ddl_year"])."'");

	$special_fields = array("Employees", "Hire_NumofEmp", "Hire_NewEmp");
	$special_values = array("'".deleteCommas($_POST["Employees"])."'","'".deleteCommas($_POST["Hire_NumofEmp"])."'", "'".deleteCommas($_POST["Hire_NewEmp"])."'");
	
	if($_POST["save_lawful"]){
		//save lawful, as a company
		
		array_push($special_fields,'CompanyActionFlag'); 
		array_push($special_values,'1'); 
		
	}elseif($_POST["submit_lawful"]){
	
		//submit lawful, as a company
		array_push($special_fields,'CompanyActionFlag'); 
		array_push($special_values,'2'); 
		
		//also send email
		$do_sendmail = 1;
	}
		
	//add vars to db
	//update or insert?
	$lawful_id = getFirstItem("select LID from lawfulness where CID = '$this_id' and Year = '$this_year'");
	
	if(strlen($lawful_id)>0){
		//update
		$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values," where LID = '$lawful_id'");
		//echo $the_sql; exit();
		mysql_query($the_sql);
	}else{
		//insert
		$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
		mysql_query($the_sql);
		$lawful_id = mysql_insert_id();
	}
	

	
	
	//---> handle attached files
	$file_fields = array(
						"Hire_docfile"
						,"Conc1_docfile"
						,"Conc2_docfile"
						,"Conc3_docfile"
						,"Conc4_docfile"
						,"Conc5_docfile"
						
						
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
			$hire_docfile_path = $hire_docfile_relate_path . $new_hire_docfile_name . "." . $hire_docfile_extension; 
			//echo $hire_docfile_path;exit();
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
						,'$lawful_id'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
			
			//no new file uploaded, retain old file name in db
			//array_push($special_fields,$file_fields[$i]);
			//array_push($special_values,"'".getFirstItem("select ".$file_fields[$i]." from $table_name where LID = '".doCleanInput($_POST["LID"])."'")."'");
		
		}
	
	}
	///
	//end handle attached file
	//////
	
	
	
		
	//also update lawful flag to org status
	//conditions
	$table_name = "company";
	if($sess_accesslevel != 4){
		//non-company update lawful flag
		$special_fields = array("lawfulflag","NoRecipientFlag","LastModifiedDateTime","LastModifiedBy");
		$special_values = array("'".$_POST["lawfulStatus"]."'","'".$_POST["NoRecipient"]."'","NOW()","'$sess_userid'");
	}else{
		//compnay-user didn't update flag
		$special_fields = array("LastModifiedDateTime");
		$special_values = array("NOW()");
	}
	$condition_sql = "where cid = '".$this_id."' limit 1";
	//add vars to db
	$the_sql = generateUpdateSQL($_POST,$table_name,$blank_fields,$special_fields,$special_values, $condition_sql);
	//echo "$the_sql"; exit();
	mysql_query($the_sql);
	
	
	
	
	
	//if($sess_accesslevel == 4){
	if($do_sendmail){
		//for company user, send notification email to someone
		
		$company_row = getFirstRow("select CompanyNameThai, CompanyTypeCode, province_name from company a, provinces b
									where
									a.CID = '$this_id'
									and
									a.Province = b.province_id");

		$company_name = $company_row["CompanyNameThai"];
		$company_province = $company_row["province_name"];
		$company_typecode = $company_row["CompanyTypeCode"];
		$formatted_name = formatCompanyName($company_name,$company_typecode);
		//echo "$company_name : $company_province"; exit();
		
		$count_type = 0;
		if($_POST["Hire_status"]){
			if($count_type == 0){
				$type .= " - ";
			}else{
				$type .= ", ";
				$count_type++;
			}
			$type .= "จ้างงาน";
		}
		if($_POST["Conc_status"]){
			if($count_type == 0){
				$type .= " - ";
			}else{
				$type .= ", ";
				$count_type++;
			}
			$type .= "ส่งเงิน";
		}
		if($_POST["pay_status"]){
			if($count_type == 0){
				$type .= " - ";
			}else{
				$type .= ", ";
				$count_type++;
			}
			$type .= "สัมปทาน";
		}
		
		
		$headers .= "Content-type: text/plain;charset=utf-8" . "\r\n";
		
		mail("job@nep.go.th"
			, "มีการส่งเอกสารออนไลน์จาก $formatted_name : $company_province"
			, "มีการส่งเอกสารออนไลน์จาก $formatted_name : $company_province $type \n\nกดที่นี่เพื่อดูรายละเอียด (ต้อง login ก่อน): http://thaidrivingspirit.com/organization.php?id=$this_id"
			, $headers);
		//mail("yoes@uklahouse.com","test","test");
	}
	
	/////end sending mails
	
	
	
	//then add this to history
	//$history_sql = "insert into modify_history values('$sess_userid','$this_id',now(),1)";
	//mysql_query($history_sql);
	doAddModifyHistory($sess_userid,$this_id,1,$lawful_id);
	
	//special haeder from auto-post
	if($_POST["le"]){
		$extra_query .= "&le=le";
	}
	if($_POST["delle"]){
		$extra_query .= "&delle=delle";
	}
	if($_POST["curate"]){
		$extra_query .= "&curate=curate";
	}
	if($_POST["curator_id"]){
	
		//just go to curator page
		$extra_query .= "&curator_id=".$_POST["curator_id"]; 
		header("location: view_curator.php?curator_id=$extra_query"); exit();
	}
	
	
	header("location: organization.php?id=$this_id&focus=lawful&updated=updated&year=".$_POST["Year"]."$extra_query"); exit();

?>