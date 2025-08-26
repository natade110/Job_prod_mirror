<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	
	
	
	
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	
	
	
	
	
	//
	
	$edit_date = date("Y-m-d");;
	
	
	//amounts and remarks
	
	$edit_rid = $_POST['edit_rid']*1;
	//$cancel_pid = $_POST['cancel_pid']*1;
	
	$edit_reason = $_POST['edit_reason'];
	$edit_userid = $_POST['edit_userid'];
				
				
	$selected_year = $_POST["ddl_year"];
	/*
	$sql = "
		
		insert into 
			receipt_edit_requests
		select
			*
			, '$edit_reason'
			, '$edit_userid'
			, '$edit_date'
			, 0
		from
			receipt	
		where
			rid = '$edit_rid'
	
	";*/
	
	
	//yoes 20170116 -- for CANCEL
	if($_POST["do_request_cancel_receipt"]){
		
		$_POST["Amount"] = 0;
		
	}
	
	//print_r($_POST); exit();
	
	///---------------------------------
	//add reciept,
	///---------------------------------
	$table_name = "receipt_edit_request";

	$input_fields = array(
						'PaymentMethod'
						
						,'ReceiptNote'
						
						);
	
	
	
	//yoes 20170115 -- add payment methods
	$payment_method = $_POST["PaymentMethod"];
	$ref_no = $_POST[$payment_method."_ref_no"];
	if($payment_method == "Cheque"){
		$bank_id = $_POST["check_bank"];
	}
	
	
	
	$the_date = $_POST["the_date_year"]."-".$_POST["the_date_month"]."-".$_POST["the_date_day"];			
							
	$special_fields = array("ReceiptYear","ReceiptDate",'Amount'
	
							, 'edit_reason'
							, 'edit_userid'
							, 'edit_date'
							, 'edit_status'
							
							, 'rid'
							
							, 'RefNo'
							, 'bank_id'
							
							);
	$special_values = array("'$selected_year'" ,"'$the_date'" ,"'".deleteCommas($_POST["Amount"])."'"
	
							, "'$edit_reason'"
							, "'$edit_userid'"
							, "now()"
							, "'0'"
							, "'$edit_rid'"
	
							, "'$ref_no'"
							, "'$bank_id'"
							
							);
						
	//$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values," where RID = '$this_id'");
	$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);
	//echo $the_sql; exit();	
	mysql_query($the_sql);
	
	//---> handle attached files
	$file_fields = array(
						"edit_docfile"
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
						,'$this_cancel_id'
						,'".$file_fields[$i]."'
					)";
			
				mysql_query($sql);
				
			}
		}else{
		
		}
	
	}

    //yoes 20180112 --> also add send-email-out here

    //

    $selected_payment_row = getFirstRow(" 
                                        select
                                            *
                                        from
                                          payment a
                                            JOIN 
                                              lawfulness b
                                                on a.LID = b.LID
                                            join company c
                                                on 
                                                b.cid = c.cid
                                            join
                                              receipt d
                                                on
                                                a.rid = d.rid                                            
                                                   
                                        where
                                          a.RID = '".$edit_rid."'
                                          
                                          limit 0,1                                
                                        
                                        ");

    $selected_company = $selected_payment_row["CID"];

    $company_row = getFirstRow("select * from company where CID = '".$selected_company."'");
    $lawful_row = getFirstRow("select * from lawfulness where CID = '".$selected_company."' and year = '".$selected_year."'");

    $vars = array(





        "{company_code}" =>  $company_row["CompanyCode"]
        ,"{company_name}" => $company_row["CompanyNameThai"]
        ,"{the_year}" => $selected_year+543
        ,"{lawfulness}" =>  getLawfulText($lawful_row["LawfulStatus"])
        ,"{book_no}" => $selected_payment_row["BookReceiptNo"]
        ,"{receipt_no}" => $selected_payment_row["ReceiptNo"]
        ,"{pay_date}" => $selected_payment_row["ReceiptDate"]
        ,"{pay_amount}" => number_format(deleteCommas($selected_payment_row['Amount']),2)
        ,"{pay_method}" => formatPaymentName($selected_payment_row["PaymentMethod"])
        ,"{pay_date_new}" => $the_date
        ,"{pay_amount_new}" => number_format(deleteCommas($_POST['Amount']),2)
        ,"{pay_method_new}"  => formatPaymentName($_POST["PaymentMethod"])
        ,"{remarks}" => $edit_reason
        ,"{requester}" => getFirstItem("select user_name from users where user_id = '$edit_userid'")
        ,"{now_date}" => date("Y-m-d")


    );

    //print_r($vars); exit();

    //yoes 20180113 -> edit or delete email?
    if($_POST["do_request_cancel_receipt"]){

        sendMailByEmailId(5, $vars);
    }else{
        sendMailByEmailId(3, $vars);
    }


	
		
	header("location: view_payment.php?id=".$edit_rid );
	exit();
		
	

?>