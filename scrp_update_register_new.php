<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	//table name
	$table_name = "users";
	$this_id = doCleanInput($_POST["register_id"]);
	
	//echo $_POST["register_id"] ; exit();
	
	//print_r($_POST); exit();
	
	if($this_id == "new"){
		
		$mode = "new";
		
	}else{
	
		
		if($this_id == $sess_userid){
			
			//self-fix
			$is_owner = 1;
			
		}elseif($this_id != $sess_userid && $sess_accesslevel != 1){
			//not "new", not "admin" and "not self-fix"
			header("location: index.php");
			exit();
		}
		
	}
		
	if($mode == "new"){
	
		
	
		//insert new, check if user name already existed...
		$user_count = getFirstItem("select count(user_name) from users where user_name = '".doCleanInput($_POST["register_name"])."'");
		
		//yoes 20151102 -- also check for duped email
		$email_count = getFirstItem("select count(user_email) from users where user_email = '".doCleanInput($_POST["register_email"])."'");
		
		
		if($user_count > 0){			
			//redirect back and exit
			header("location: view_register_new.php?mode=add&duped=duped");
			exit();
		}elseif($email_count > 0){			
			//redirect back and exit
			header("location: view_register_new.php?mode=add&mailed=mailed");
			exit();
		}else{			
			//continue doing w/e
			
			//specify all posts fields
			$input_fields = array(
	
						'register_name'
						,'register_password'
						
						,'register_org_code'
						,'register_org_name'
						,'register_contact_name'
						,'register_contact_phone'
						,'register_position'
						,'register_email'

						
						
						
						
						);
			
			$special_fields = array(	
									'register_province'		
									, 'register_registered_date'		 //yoes 20151019		
							); 
			$special_values = array(	
									"'".$_POST["Province"]."'"	
									, "now()"				//yoes 20151019	
							); 	
			
			
			
			//$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);				
			
			//yoes 20151019	- add created DATE
			
			$the_sql = "insert into users(
						
							user_name
							, user_password
							, AccessLevel
							, FirstName
							, LastName
							, user_meta
							
							, user_enabled
							
							, user_email
							, user_position
							, user_telephone
							
							, user_created_date
							, user_commercial_code
							
							, user_ip_address
							
						)values(
						
						
							'".doCleanInput($_POST["register_name"])."'
							, '".doCleanInput($_POST["register_password"])."'
							, '4'
							, '".doCleanInput($_POST["register_contact_name"])."'
							, '".doCleanInput($_POST["register_contact_lastname"])."'
							, '".doCleanInput($_POST["register_cid"])."'
						
							, 0
							
							, '".doCleanInput($_POST["register_email"])."'
							, '".doCleanInput($_POST["register_position"])."'
							, '".doCleanInput($_POST["register_contact_phone"])."'
							
							, now()
							, '".doCleanInput($_POST["user_commercial_code"])."'
							, '".$_SERVER['REMOTE_ADDR']."-----".$_SERVER['HTTP_X_FORWARDED_FOR']."'
							
						)";
			
			
			//echo $the_sql; exit();
			mysql_query($the_sql);	
			$this_id = mysql_insert_id();
			
			//yoes 20141013 --> also send out emails
			//yoes 20151102 --> change wording for emails
			$mail_address = doCleanInput($_POST["register_email"]);
	
			$the_header = "สมัครสมาชิก ระบบรายงานผลการจ้างงานคนพิการ เสร็จสิ้น";
			
			$the_body = "<table><tr><td>เรียนคุณ ".doCleanInput($_POST["register_contact_name"])."<br><br>";
			
			$the_body .= "คุณได้สมัครเข้าใช้งาน ระบบรายงานผลการจ้างงานคนพิการ สำหรับสถานประกอบการ เรียบร้อยแล้ว <br><br>";
			$the_body .= "หลังจากผู้ดูแลระบบได้ทำการตรวจสอบข้อมูลและอนุมัติ user account ของคุณแล้ว <br>";
			$the_body .= "คุณจะสามารถเข้าใช้ระบบได้โดยใช้ username/password ด้านล่าง และเมื่อเจ้าหน้าที่ทําการตรวจสอบเสร็จสิ้นแล้ว<br>";
			$the_body .= "โดยการคุณจะได้รับ email ยืนยันการใช้งานระบบอีกครั้ง<br><br>";
			$the_body .= "username: ".doCleanInput($_POST["register_name"])." <br>";
			$the_body .= "password: ".doCleanInput($_POST["register_password"])." <br><br>";
			$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
			
			
			if ($server_ip == "203.146.215.187"){
				//ictmerlin.com use default mail
				mail($mail_address, $the_header, $the_body);
			
			}elseif ($server_ip == "127.0.0.1"){
				
				//donothin	
				
			}else{
				//use smtp
				doSendMail($mail_address, $the_header, $the_body);	
			}
			
			
			//also update register stat
			$history_sql = "insert into modify_history_register(mod_register_id, mod_date, mod_type) values('$this_id',now(),1)";
			mysql_query($history_sql);
			
			
			
			
			
			
			
			//yoes20141106 --> also add file attachment
			//---> handle attached files
			$file_fields = array(
			
								"register_employee_card"
								,"register_id_card"
								
								
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
								,'$this_id'
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
			
			
			
			
			
			
			
			header("location: view_register_new.php?id=$this_id&user_added=user_added");
		}
		
	}elseif($sess_accesslevel == 1){//edit mode
	
		//edit other people (only admin can do this)
	
		$input_fields = array(
	
						'register_password'
						
						,'register_org_name'
						,'register_contact_name'
						,'register_contact_phone'
						,'register_position'
						,'register_email'
						
						);
		
		
		$special_fields = array(	
								'register_province'						
						); 
		$special_values = array(	
								"'".$_POST["Province"]."'"					
						); 	
			
		
	
		$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, " where register_id = '$this_id'");
		
		//also update register stat
		$history_sql = "insert into modify_history_register(mod_register_id, mod_date, mod_type, mod_desc) values('$this_id',now(),6,'$sess_userid')";
		mysql_query($history_sql);
		
		//echo $the_sql; exit();
		mysql_query($the_sql);	
		header("location: view_register_new.php?id=$this_id&updated=updated");
		
	}
	
	

?>