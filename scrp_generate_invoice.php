<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	
	
	
	
	//print_r($_POST); exit();

	//get pay_33_** values
	/*for($i = 0; $i < count($_POST) ; $i++){
		
		echo "- - ".$_POST[$i];
		
	}*/
	
	
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	
	
	$table_name = "invoices";
	$item_table_name = "invoice_items";
	
	if($_POST[is_demo]){
		$is_demo = 1;		
		$table_name = "invoices_demo";
		$item_table_name = "invoice_items_demo";
	}
	
	
	//
	$invoice_cid = $_POST["invoice_cid"]*1;
	$invoice_lawful_year = $_POST["invoice_lawful_year"];
	//$invoice_payment_date = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];			
	$invoice_payment_date = $_POST["invoice_payment_date"];			
	$invoice_date = date("Y-m-d");
	$invoice_amount = round(deleteCommas($_POST['invoice_amount']),2)*1;
	
	//amounts and remarks
	
	$invoice_principal_amount = round($_POST['invoice_principal_amount'],2)*1;
	$invoice_interest_amount = round($_POST['invoice_interest_amount'],2)*1;
	$invoice_remarks = $_POST['invoice_remarks'];
	$invoice_userid = $_POST['invoice_userid'];
	
	$invoice_owned_principal = round($_POST['invoice_owned_principal'],2);
	$invoice_owned_interest = round($_POST['invoice_owned_interest'],2);
	
	$invoice_employees = $_POST['invoice_employees'];
	$invoice_33 = $_POST['invoice_33'];
	$invoice_35 = $_POST['invoice_35'];
	
	
	$m33_total_missing = $_POST['m33_total_missing'];
	$m33_total_interests = $_POST['m33_total_interests'];

	$m35_total_missing = $_POST['m35_total_missing'];
	$m35_total_interests = $_POST['m35_total_interests'];
	
	$special_fields = array(
						
						"invoice_cid"
						, "invoice_lawful_year"
						, "invoice_payment_date"
						, "invoice_date"
						, 'invoice_amount'
						
						, 'invoice_principal_amount'
						, 'invoice_interest_amount'
						, 'invoice_remarks'
						, 'invoice_userid'
						, 'invoice_userid_text'
						
						, 'invoice_owned_principal'
						, 'invoice_owned_interest'
						
						, 'invoice_status'
						
						
						, 'invoice_employees' 
						, 'invoice_33' 
						, 'invoice_35'
						
						, 'm33_total_missing'
						, 'm33_total_interests'

						, 'm35_total_missing'
						, 'm35_total_interests'
						
						);


    if($_POST["invoice_userid_text"]){
        $invoice_userid_text = $_POST["invoice_userid_text"];
    }else {
        $invoice_userid_text = getFirstItem("select CONCAT(FirstName, ' ', LastName) from users where user_id = '$invoice_userid'");
    }
	
	$special_values = array(
	
	
						"'$invoice_cid'"
						, "'$invoice_lawful_year'"
						, "'$invoice_payment_date'"
						, "'$invoice_date'"
						, "'".deleteCommas($_POST['invoice_amount'])."'"
						
						, "'$invoice_principal_amount'"
						, "'$invoice_interest_amount'"
						, "'$invoice_remarks'"
						, "'$invoice_userid'"						
						, "'". $invoice_userid_text ."'"
						
						, "$invoice_owned_principal"
						, "$invoice_owned_interest"
						
						, '1'
						
						, "'$invoice_employees'"
						, "'$invoice_33'"
						, "'$invoice_35'"
						
						, "'$m33_total_missing'"
						, "'$m33_total_interests'"

						, "'$m35_total_missing'"
						, "'$m35_total_interests'"
	
						);
						
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	//echo $the_sql; exit();	
	mysql_query($the_sql) or die(mysql_error());
	$this_invoice_id = mysql_insert_id();					
	
	
	//---> handle attached files
	$file_fields = array(
						"invoice_docfile"
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
			$hire_docfile_path = $hire_docfile_relate_path . $new_hire_docfile_name . "." . $hire_docfile_extension; 
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
						,'$this_invoice_id'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
		
		}
	
	}

	$ar_33_meta = array();
	$ar_34_meta = array();
	$ar_35_meta = array();

	$ar_3335_meta = array();

	foreach ($_POST as $key => $value) {
		
		if(substr($key, 0, 7) == "pay_34_"){			
			//echo "<br>".$value;	
			//$ar_33_meta[] = $value;
			
			$ar_key = explode("_",$key);
			$ar_34_meta[$ar_key[3]][$ar_key[2]] = $value;			
		}
		
		//echo "<br>The capital city of {$key} is $value" . '<br>';		
		if(substr($key, 0, 7) == "pay_33_"){			
			//echo "<br>".$value;	
			//$ar_33_meta[] = $value;
			
			$ar_key = explode("_",$key);
			$ar_33_meta[$ar_key[3]][$ar_key[2]] = $value;
			$ar_3335_meta[33][$ar_key[3]][$ar_key[2]] = $value;
		}
		
		if(substr($key, 0, 7) == "pay_35_"){			
			//echo "<br>".$value;	
			//$ar_33_meta[] = $value;
			
			$ar_key = explode("_",$key);
			$ar_35_meta[$ar_key[3]][$ar_key[2]] = $value;
			$ar_3335_meta[35][$ar_key[3]][$ar_key[2]] = $value;
		}
		
	}

	function insertInvoiceItems($this_invoice_id,$ar, $type , $table_name = "invoice_items"){
	
		foreach ($ar as $key => $value) {	
		
			$ar_key = explode("x",$key);
			
			$p_lid = $ar_key[0]*1;
			$p_from = $ar_key[1]*1;
			$p_to = $ar_key[2]*1;
			
			//$ar_value = $value;
			
			$ini_amount = deleteCommas($value[amount])*1;
			$ini_interests = deleteCommas($value[interests])*1;
			$ini_principal = deleteCommas($value[amount])*1-deleteCommas($value[interests])*1;
			
			
			$sql = "
			
				replace into
					$table_name (

						invoice_id
						, ini_type
						, p_lid
						, p_from
						, p_to

						, ini_amount					
						, ini_interests
						, ini_principal


					)values(

						'$this_invoice_id'
						, '$type'
						, '$p_lid'
						, '$p_from'
						, '$p_to'

						, '$ini_amount'
						, '$ini_interests'
						, '$ini_principal'

					)


			";
			
			//echo "<br>";
			//echo $sql; 		
			
			mysql_query($sql);
			
		}
	
	}

	
	insertInvoiceItems($this_invoice_id,$ar_34_meta,34, $item_table_name);
	insertInvoiceItems($this_invoice_id,$ar_33_meta,33, $item_table_name);
	insertInvoiceItems($this_invoice_id,$ar_35_meta,35, $item_table_name);
	
	
	//yoes 20211108
	//update invoice_principal_amount and invoice_interest_amount
	$sum_ini_34_row = getFirstRow("select sum(ini_principal) as sum_principal, sum(ini_interests) as sum_interests from $item_table_name where invoice_id = '$this_invoice_id' and ini_type = 34");
	$sum_ini_33_row = getFirstRow("select sum(ini_principal) as sum_principal, sum(ini_interests) as sum_interests from $item_table_name where invoice_id = '$this_invoice_id' and ini_type = 33");
	$sum_ini_35_row = getFirstRow("select sum(ini_principal) as sum_principal, sum(ini_interests) as sum_interests from $item_table_name where invoice_id = '$this_invoice_id' and ini_type = 35");
	
	if($sum_ini_34_row[sum_principal] || $sum_ini_33_row[sum_principal] || $sum_ini_35_row[sum_principal]){
		
		
		$sql = "
		
			update
				$table_name 
			set
				invoice_principal_amount = '".($sum_ini_34_row[sum_principal]+$sum_ini_33_row[sum_principal]+$sum_ini_35_row[sum_principal])."'
				, invoice_interest_amount = '".($sum_ini_34_row[sum_interests]+$sum_ini_33_row[sum_interests]+$sum_ini_35_row[sum_interests])."'
			where
				invoice_id = '$this_invoice_id'
		
		";
		
		mysql_query($sql);
		
	}

		
	//header("location: add_invoice.php?search_id=".$invoice_cid."&mode=payment&for_year=".$invoice_lawful_year );
	header("location: invoice.php?invoice_id=".$this_invoice_id."&is_demo=".($is_demo*1) );
	exit();
		
	

?>