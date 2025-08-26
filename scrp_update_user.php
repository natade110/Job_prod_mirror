<?php

	include "db_connect.php";
	include "scrp_config.php";
	//print_r($_POST);
	
	
	
	
	
	//table name
	$table_name = "users";
	$this_id = doCleanInput($_POST["user_id"]);
	
	doUsersFullLog($sess_userid, $this_id, "scrp_update_user.php");
	
	if($this_id == "new"){
		$mode = "new";
	}else{
	
		//edit mode
		if($this_id == $sess_userid){
			
			//self-fix
			$is_owner = 1;
			
		}elseif($sess_accesslevel == 3){
			
			//yoes 20161201 --> allow user 3 to use this page
			
		
		}elseif($this_id != $sess_userid && $sess_accesslevel != 1 && $sess_accesslevel != 2 && !$sess_can_manage_user){
			//yoes 20160118 -- allow พก to edit this
			
			//not "new", not "admin" and "not self-fix"
			//yoes 20141007 -- add now พมจ who can do this
			header("location: index.php");
			exit();
		}
		
	}
		
	if($mode == "new" && ($sess_accesslevel == 1 || $sess_can_manage_user)){ //yoes 20141007 ---> also $sess_can_manage_user
	
		//insert new, check if user name already existed...
		$user_count = getFirstItem("select count(user_id) from users where user_name = '".doCleanInput($_POST["user_name"])."'");
		
		if($user_count > 0){			
			//redirect back and exit
			header("location: view_user.php?mode=add&duped=duped");
			exit();
		}else{			
			//continue doing w/e
			
			//specify all posts fields
			$input_fields = array(
	
						'user_name'						
						,'AccessLevel'
						,'Department'
						,'FirstName'
						,'LastName'
						
						,'user_email'
						,'user_position'
						,'user_telephone'
						
						,'user_enabled'
						, 'user_can_manage_user'
						
						
						);
			
			if($_POST["AccessLevel"] == 3){
				$special_fields = array(	
									'user_meta'		
									, 'user_password'
									
								); 
				$special_values = array(	
									"'".$_POST["Province"]."'"
									, "md5('".$_POST["user_password"]."')"
									); 		
			}elseif($_POST["AccessLevel"] == 4){
				$special_fields = array(	
									'user_meta'	
									, 'user_password'									
								); 
				$special_values = array(	
									"'".$_POST["cid"]."'"	
									, "md5('".$_POST["user_password"]."')"
									); 		
			}else{
				
				$special_fields = array(	
									'user_password'
								); 
				$special_values = array(	
									"md5('".$_POST["user_password"]."')"
								); 		
				
			}
			
			$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values);				
			//echo $the_sql; exit();
			mysql_query($the_sql);	
			$this_id = mysql_insert_id();
			header("location: view_user.php?id=$this_id&user_added=user_added");
		}
		
	}else{//edit mode
	
		//edit other people (only admin can do this)
		//yoes 20141007 --> $$sess_can_manage_user can also do this
		//yoes 20160118 --- also allow พก to edit this
		//yoes 20161201 --- also allow pmj to edit this
		if(!$is_owner && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_can_manage_user || $sess_accesslevel == 3)){
			
			
			
			//yoes 20151117
			//try getting an old value firs
			$old_user_row = getFirstRow("select * from users where user_id = '$this_id'");
			
			//unlink zone if role or meta changes
			if($old_user_row[AccessLevel] != $_POST[AccessLevel]){
			
				mysql_query("delete from zone_user where user_id = '$this_id'");	
				
			}
			
			
			$input_fields = array(
		
							'AccessLevel'
							,'Department'
							,'FirstName'
							,'LastName'
							
							,'user_email'
							,'user_position'
							,'user_telephone'
							
							,'user_enabled'
							, 'user_can_manage_user'
							
							, 'reopen_case_password' //new as of 20160118
							
							, 'FirstName_2'
							, 'LastName_2'
							, 'user_position_2'
							, 'user_telephone_2'
							
							);
							
							
			$special_fields = array();
			$special_values = array();
			
			if($_POST["AccessLevel"] == 3){
				$special_fields = array(	
									'user_meta'			
																
								); 
				$special_values = array(	
									"'".$_POST["Province"]."'"	
																
									); 		
									
				//yoes 20151117
				//unlink zone if role or meta changes
				if($old_user_row[user_meta] != $_POST[Province]){
				
					mysql_query("delete from zone_user where user_id = '$this_id'");	
					
				}
									
									
			}elseif($_POST["AccessLevel"] == 4){
				$special_fields = array(	
									'user_meta'		
									
								); 
				$special_values = array(	
									"'".$_POST["cid"]."'"	
										
									); 		
			}
			
			if($_POST["user_password"]){
					
				array_push($special_fields, 'user_password'	); 
				array_push($special_values, "md5('".$_POST["user_password"]."')"); 				
				//print_r($special_fields); exit();
				
			}
			
				
			
			
			
			//also enable photo upload
			
			
			
			//yoes20141106 --> also add file attachment
			//---> handle attached files
			$file_fields = array(
								"register_employee_card"
								,"register_id_card"
								, "register_doc_1"
								, "register_doc_2"
								, "register_doc_3"
								, "register_doc_4"
								
								
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
			
			
			
			
			
			
			
			
			//yoes 20141013 -- send out approval email
			//yoes 20210127 -- also send out approval email if user email is not approved
			if($_POST["AccessLevel"] == 4 && $_POST["user_enabled"] == 1 && (!$_POST["user_enabled_origin"] || $_POST["user_enabled_origin"] == 9)){			
				
				//yoes 20160816 --> also update commercial code into company meta
				$user_cid = getFirstItem("select user_meta from users where user_id = '$this_id'");
				$user_commercial_code = getFirstItem("select user_commercial_code from users where user_id = '$this_id'");
				if($user_commercial_code){
					
					$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$user_cid','commercial_code','$user_commercial_code')";	
					mysql_query($sql);
					
				}
				
				
				//yoes 20170415
				//also add field for กรรมการบริษัท
				/*
				$user_2_rows = getFirstItem("select FirstName_2, LastName_2, user_telephone_2, user_position_2 from users where user_id = '$this_id'");
				if($user_2_rows[FirstName_2]){
					
					$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$user_cid','FirstName_2','".$user_2_rows[FirstName_2]."')";	
					mysql_query($sql);
					
				}
				if($user_2_rows[LastName_2]){
					
					$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$user_cid','LastName_2','".$user_2_rows[LastName_2]."')";	
					mysql_query($sql);
					
				}
				if($user_2_rows[user_telephone_2]){
					
					$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$user_cid','user_telephone_2','".$user_2_rows[user_telephone_2]."')";	
					mysql_query($sql);
					
				}
				if($user_2_rows[user_position_2]){
					
					$sql = "replace into company_meta(meta_cid, meta_for, meta_value) values('$user_cid','user_position_2','".$user_2_rows[user_position_2]."')";	
					mysql_query($sql);
					
				}
				*/
				
				//yoes 20151019 -- also mark users table flag so we know what date is a first-approval date
				//yoes 20151021 -- also update approver
				$approved_date_sql = "
						update 
							users 
						set 
							user_approved_date = now() 
							, user_approved_by = '$sess_userid'
						where 
							user_id = '$this_id'
							
							"; 
				mysql_query($approved_date_sql);
				
				
				//echo "mailout now";
				//exit();
				$mail_address = doCleanInput($_POST["user_email"]);
				
				$the_header = "ระบบรายงานผลการจ้างงานคนพิการ: ผู้ดูแลระบบได้ทำการตรวจสอบข้อมูลและอนุมัติ user account ของคุณแล้ว";
			
				$the_body = "<table><tr><td>เรียนคุณ ".doCleanInput($_POST["FirstName"])."<br><br>";
	
				$the_body .= "เจ้าหน้าที่ได้ทำการตรวจสอบข้อมูลและอนุมัติ user account ของคุณแล้ว <br>";
				$the_body .= "คุณจะสามารถเข้าใช้ระบบได้โดยไปที่ url http://ejob.dep.go.th/ejob และทำการ login ด้วย user name และ password ที่ได้ลงทะเบียนไว้<br><br>";

				
				
				$the_body .= "ขอแสดงความนับถือ<br>";
	

				$the_body .= "กองกองทุนและส่งเสริมความเสมอภาคคนพิการ<br>";
				$the_body .= "โทรศัพท์ 02-106-9300, 02-106-9327-31<br>";
				//$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
				
				
				if ($server_ip == "203.146.215.187"){
					//ictmerlin.com use default mail
					mail($mail_address, $the_header, $the_body);
				}elseif ($server_ip == "127.0.0.1" || 1==0){
				
					//donothin	
				
				}else{
					//use smtp
					doSendMail($mail_address, $the_header, $the_body);	
				}
				
				
			}elseif($_POST["AccessLevel"] == 4 && $_POST["user_enabled"] == 2 && !$_POST["user_enabled_origin"]){
				
				
				//echo "mailout now";
				//exit();
				$mail_address = doCleanInput($_POST["user_email"]);
				
				$the_header = "ระบบรายงานผลการจ้างงานคนพิการ: เจ้าหน้าที่ไม่อนุมัติ user account ของคุณ";
			
				$the_body = "<table><tr><td>เรียนคุณ ".doCleanInput($_POST["FirstName"])."<br><br>";
	
				$the_body .= "เจ้าหน้าที่ได้ทำการตรวจสอบข้อมูล user account ของคุณแล้ว  <br>";
				
				$the_body .= "<br>เจ้าหน้าที่ไม่อนุมัติ user account ของคุณ เนื่องจากข้อมูลไม่ครบถ้วน หรือเอกสารแนบไม่ครบ <br>";
				
				$the_body .= "<br>กรุณาเข้าใช้ระบบโดยไปที่ url http://ejob.dep.go.th/ejob และทำการ login ด้วย user name และ password ที่ได้ลงทะเบียนไว้ และทำการแก้ไขข้อมูล user account ให้ครบถ้วนอีกครั้ง<br>";

				
				
				$the_body .= "<br>ขอแสดงความนับถือ<br>";
	

				$the_body .= "กองกองทุนและส่งเสริมความเสมอภาคคนพิการ<br>";
				$the_body .= "โทรศัพท์ 02-106-9300, 02-106-9327-31<br>";
				//$the_body .= ", ผู้ดูแลระบบรายงานผลการจ้างงานคนพิการ</td></tr></table>";
				
				
				if ($server_ip == "203.146.215.187"){
					//ictmerlin.com use default mail
					mail($mail_address, $the_header, $the_body);
				}elseif ($server_ip == "127.0.0.1" || 1==0){
				
					//donothin	
				
				}else{
					//use smtp
					doSendMail($mail_address, $the_header, $the_body);	
				}
				
				
			}else{
				//echo "mail not out now";
				//exit();
			}
			
			
			
			
			
		}elseif($is_owner){
			
			//non-admin can only change password
			
			//yoes 20141007 -- but admin owner can change own's data
			if($sess_accesslevel == 1 || $sess_can_manage_user){
				$input_fields = array(
			
								'Department'
								,'FirstName'
								,'LastName'
								
								,'user_email'
								,'user_position'
								,'user_telephone'
								
								
								);
			}
			
			
			
			//validate/old new pass
			$old_pass = doCleanInput($_POST["user_password_old"]);
			$count_old_pass = getFirstItem("select count(*) from users where user_id = '$sess_userid' and user_password = md5('$old_pass')");
			
			if($count_old_pass < 1){
				//old password incorrect
				header("location: view_user.php?id=$sess_userid&oldpass=oldpass");
				exit();
			}
			
			$special_fields = array(
							'user_password'
							);
			$special_values = array(
							"md5('".doCleanInput($_POST["user_password_new_1"])."')"
							);
		
		}
	
		$the_sql = generateUpdateSQL($_POST,$table_name,$input_fields,$special_fields,$special_values, " where user_id = '$this_id'");
		
		//echo $the_sql; exit();
		mysql_query($the_sql);	
				
		
		
		header("location: view_user.php?id=$this_id&updated=updated");
		
	}
	
	

?>