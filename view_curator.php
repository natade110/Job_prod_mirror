<?php

	include "db_connect.php";
	include "scrp_config.php";
	
//table name
$table_name = "curator";
$lawful_table_name = "lawfulness";

$auto_post = 1;

//for company, record this to company table instead
if($sess_accesslevel == 4){
		$table_name = "curator_company";
		$lawful_table_name = "lawfulness_company";
		
		$auto_post = 0;
}	


if($_GET[extra] || $_POST[case_closed]){
		$table_name = "curator_extra";
		$lawful_table_name = "lawfulness";
		
		$auto_post = 0;
		
		$case_closed = 1;
}	


	
//check if this is "select_child"
if($_POST["select_child"]){

	
	$selected_parent = doCleanInput($_POST["parent_id"]);
	$selected_child = doCleanInput($_POST["child_id"]);
	
	//delete unwanted child from this curator
	$sql = "delete from $table_name where curator_parent = '$selected_parent' and curator_id != '$selected_child'";
	//echo $sql; exit();
	
	mysql_query($sql);
	
	//exit();

}


//check if button is pressed
if($_POST["do_add_curator"]){

	$curator_id = $_POST["curator_id"]*1;

	if($_POST["curator_idcard"]){
		$curator_idcard = doCleanInput($_POST["curator_idcard"]);
	}else{
	
		for($i=1;$i<=13;$i++){
			$curator_idcard .= $_POST["id_".$i];
		}
	}
	
	//echo $curator_idcard; exit();

	$curator_name = doCleanInput($_POST["curator_name"]);
	
	$curator_gender = doCleanInput($_POST["curator_gender"]);
	$curator_age = doCleanInput($_POST["curator_age"]);
	$curator_lid = doCleanInput($_POST["curator_lid"]);
	$curator_parent = doCleanInput($_POST["curator_parent"]);
	
	$curator_event = doCleanInput($_POST["curator_event"]);
	$curator_event_desc = doCleanInput($_POST["curator_event_desc"]);

	$curator_disable_desc = doCleanInput($_POST["le_disable_desc"]);	
	
	$curator_value = doCleanInput(deleteCommas($_POST["curator_value"]));
	
	$curator_is_disable = doCleanInput($_POST["curator_is_disable"]);	
	
	if($curator_parent > 0){
	
		//if curator has parent then this is disabled curator
		$curator_is_disable = 1;
	
	}
	
	
	$curator_start_date = $_POST["curator_start_date_year"]."-".$_POST["curator_start_date_month"]."-".$_POST["curator_start_date_day"];
	$curator_end_date = $_POST["curator_end_date_year"]."-".$_POST["curator_end_date_month"]."-".$_POST["curator_end_date_day"];


	if($curator_id > 0){
	
		//have post curator id -> do update instead
		$sql = "
			update $table_name set 
				
					curator_name = '$curator_name'
					,curator_idcard = '$curator_idcard'
					,curator_gender = '$curator_gender'
					,curator_age = '$curator_age'
					,curator_lid = '$curator_lid'
					,curator_parent = '$curator_parent'
					
					,curator_event = '$curator_event'
					,curator_event_desc = '$curator_event_desc'
					,curator_disable_desc = '$curator_disable_desc'
					
					, curator_is_disable = '$curator_is_disable'
					, curator_start_date = '$curator_start_date'
					, curator_end_date = '$curator_end_date'
					
					, curator_value = '$curator_value'
					
					, curator_is_dummy_row = 0
					
				where
					
					curator_id = '$curator_id'
				
			";
	
	
	}else{
	
		//no input curator id -> do update
		$sql = "
			insert into 
				$table_name(
				
					curator_name
					,curator_idcard
					,curator_gender
					,curator_age
					,curator_lid
					,curator_parent
					
					,curator_event
					,curator_event_desc
					,curator_disable_desc
					
					, curator_is_disable
					, curator_start_date
					, curator_end_date
					
					, curator_value
					
				)values(
				
				
					'$curator_name'
					,'$curator_idcard'
					,'$curator_gender'
					,'$curator_age'
					,'$curator_lid'
					,'$curator_parent'
					
					,'$curator_event'
					,'$curator_event_desc'
					,'$curator_disable_desc'
					
					, '$curator_is_disable'
					, '$curator_start_date'
					, '$curator_end_date'
					
					, '$curator_value'
				
				)
				
			";
			
	}
	
	//echo $sql; exit();
	
	$curate = "curate";
	
	mysql_query($sql) or die(mysql_error());
	
	//end add curator
	//if($this_year >= 2013 && $sess_accesslevel != 4){
	
	//yoes 20151222 --> allow this for all years
	if($sess_accesslevel != 4){
	
		//only do auto post if >= year 2013
		$_GET["auto_post"] = 1;
		
		//also add curator flag
		$_GET["curate"] = "curate";
		
	}


	$inserted_id = mysql_insert_id();
	
		
	$updated_done = 1;
	
	if($inserted_id){
		//$_GET["curator_id"] = $inserted_id;
		$file_for = $inserted_id;
	}else{
		//$_GET["curator_id"] = $curator_id;
		$file_for = $curator_id;
	}

	///
	//---> handle attached files
	$file_fields = array(
						"curator_docfile"
						);
						
	for($i = 0; $i < count($file_fields); $i++){
	
		//echo "filesize: ".$hire_docfile_size;
		$hire_docfile_size = $_FILES[$file_fields[$i]]['size'];
		if($hire_docfile_size > 0){
			
			//echo "what";
		
			$hire_docfile_type = $_FILES[$file_fields[$i]]['type'];
			$hire_docfile_name = $_FILES[$file_fields[$i]]['name'];
			$hire_docfile_exploded = explode(".", $hire_docfile_name);
			$hire_docfile_file_name = $hire_docfile_exploded[0]; 
			$hire_docfile_extension = $hire_docfile_exploded[1]; 
			
			//new file name
			$new_hire_docfile_name = date("dmyhis").rand(00,99)."_".$hire_docfile_file_name; //extension
			$hire_docfile_path = $hire_docfile_relate_path . $new_hire_docfile_name . "." . $hire_docfile_extension; 
			//echo $hire_docfile_path; exit();
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
						,'$file_for'
						,'".$file_fields[$i]."'
					)";
			
				//echo $sql; exit();
				mysql_query($sql);
				
			}
		}else{
			
			
			
		
		}
	
	}		


}//end insert curataor
	//echo $curator_id;
	
//current mode
if(is_numeric($_GET["curator_id"])){
	

	 
	$curator_id = $_GET["curator_id"];
	//$this_lawful_year = "2013";
	
	//get company and lawfulness
	$mode = $_GET["mode"];
	
	
}else{
	header("location: index.php");
}	

//echo $curator_id;

?>

<?php 
		
		
		
		
		if(is_numeric($_GET["curator_id"]) && $mode == "add_curator_usee"){
		
			//add child curator to this curator id
			$curator_row_to_fill["curator_is_disable"] = 1;
			$curator_row_to_fill["curator_parent"] = $curator_id;
			//$curator_row_to_fill["curator_name"] = getFirstItem("select curator_name from curator where curator_id = '$curator_id'");
			
			$this_lid = getFirstItem("select curator_lid from $table_name where curator_id = '$curator_id'");
		
			if(!$inserted_id){
			
				$inserted_id = $_GET["curator_id"];
			
			}
		
			if($inserted_id){
			
			
				$the_sql = "select * from $table_name where curator_id = '$inserted_id'";
			
				$inserted_curator_row = getFirstRow($the_sql);
				
				//echo $the_sql;
				$this_lid = $inserted_curator_row["curator_lid"];
				
				//echo $curator_row_to_fill[0];
				$this_lawful_year = getFirstItem("select Year from lawfulness where LID = '$this_lid'");
				$this_cid = getFirstItem("select CID from lawfulness where LID = '$this_lid'");
				$this_cid_name = getFirstItem("select CompanyNameThai from company where CID = '$this_cid'");
				$this_cid_type = getFirstItem("select CompanyTypeCode from company where CID = '$this_cid'");
				
				$company_name_to_use = formatCompanyName($this_cid_name,$this_cid_type);
			
			}
			
			//this is a usee record => this means this one already have usee
			$count_usee = 1;
		
		
		}elseif(is_numeric($_GET["curator_id"]) && $mode != "add_curator_usee"){
			
			//edit curator
			

			//pre-fill curator
			$is_edit_curator = 1;
			
			//edit curator usee
			if(is_numeric($_GET["edit_id"])){
				$curator_id_to_fill = $_GET["edit_id"];		
			}else{
				$curator_id_to_fill = $_GET["curator_id"];
			}

			
			$the_sql = "select * from $table_name where curator_id = '$curator_id_to_fill'";
			
			$curator_row_to_fill = getFirstRow($the_sql);
			
			
			//echo $the_sql;
			$this_lid = $curator_row_to_fill["curator_lid"];
			
			//yoes 20160118 -- also check if case's closed
			$this_lawful_row = getFirstRow("select close_case_date, reopen_case_date from lawfulness where LID = '$this_lid'");
			if($this_lawful_row[close_case_date] > $this_lawful_row[reopen_case_date]){
				$case_closed = 1;					
				//echo "--> $case_closed <--";			
			}
			
			//echo $curator_row_to_fill[0];
			$this_lawful_year = getFirstItem("select Year from lawfulness where LID = '$this_lid'");
			$this_cid = getFirstItem("select CID from lawfulness where LID = '$this_lid'");
			$this_cid_name = getFirstItem("select CompanyNameThai from company where CID = '$this_cid'");
			$this_cid_type = getFirstItem("select CompanyTypeCode from company where CID = '$this_cid'");
			
			$company_name_to_use = formatCompanyName($this_cid_name,$this_cid_type);
			//echo $this_lawful_year . " " . $this_cid;
			
			if($curator_row_to_fill["curator_parent"] == 0){
				$is_curator_parent = 1;
				
				//but but - first see if this curator has more than one usee			
				//is parent and is not disabled...
				if($curator_row_to_fill["curator_is_disable"] == 0 || $curator_row_to_fill["curator_is_disable"] == ""){
				
					$count_usee = getFirstItem("select count(*) from $table_name where curator_parent = '$curator_id_to_fill'");									
					$parent_is_disabled = 0;
					
					if($count_usee == 0){
					
						$redirect_to = "view_curator.php?curator_id=".$curator_id_to_fill."&mode=add_curator_usee";
					
						if($_GET["do_auto_post"]){
							$redirect_to .= "&do_auto_post=1";
						}
						
						if($_GET["extra"]){
							$redirect_to .= "&extra=extra";
						}
					
						//no usee, force add uses
						header("location: $redirect_to");
					}
					
					//echo $count_usee;
				}else{
					$count_usee = 1; //this parent is already a disabled person - assume to have 1 usee
					$parent_is_disabled = 1;
				}
				
				
			}else{
			
				//this is a usee record => this means this one already have usee
				$count_usee = 1;
			
			}

		}
?>


<?php 
	include "header_html.php";
	include "global.js.php";
	
	//echo $curator_id;
?>
              <td valign="top">
                	
                    
                    
                <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                    
                ผู้ใช้สิทธิมาตรา 35: <?php echo $curator_row_to_fill["curator_name"];?>
				
				<!--<?php  echo $company_name_to_use;?>, ปี <?php echo formatYear($this_lawful_year);?>-->
                </h2>
                    
                    <div style="padding:5px 0 10px 2px">ผู้ใช้สิทธิมาตรา 35 > <a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $this_lawful_year;?>" 
                    
                    onclick="return validateCuratorForm(document.getElementById('curator_form'));"
                                
                    
                    ><?php echo $company_name_to_use;?> ประจำปี <?php echo formatYear($this_lawful_year);?></a></div>
                    
                    
                    
                    
                    <?php if($count_usee > 1){?>
                    
                    	<div style="padding:10px 0; font-size:16px;"><font color="#009900"><strong>*** ดึงข้อมูลผู้ถูกใช้สิทธิเสร็จสิ้น <br />
แต่ข้อมูลผู้ใช้สิทธิ มีผู้ถูกใช้สิทธิมากกว่า 1 คน กรุณาเลือกผู้ถูกใช้สิทธิที่ต้องการจากรายชื่อด้านล่าง</strong></font></div>
                        
                    <?php }?>
                    
                    
                    <form name="curator_form" id="curator_form"  method="post" enctype="multipart/form-data" 
                    onsubmit="return validateCuratorForm(this);"     
                    
                    <?php if($mode == "add_curator_usee"){?>
                    action="view_curator.php?curator_id=<?php echo $curator_id;?>"
                    <?php }?>
                    
                    
                    <?php if($count_usee > 1){ //won't allow to edit curator until count usee <= 1?>
                    style="display:none;" 
                    <?php }?> ><!--- curator information just get posted into this page-->
                    
                     <table id="curator_input_forms"  align="center">
                        <tr bgcolor="#efefef">
                            <td colspan="10">
                                
                                <strong id="the_parent" style="display:none;">เพิ่มผู้ใช้สิทธิ</strong>        
                                <strong id="the_child" style="display:none;">เพิ่มผู้ถูกใช้สิทธิ</strong>                                </td>
                        </tr>
                        
                        <tr>
                          <td colspan="4">
                          
                          <?php if($mode == "add_curator_usee"){?>
                          <span style="font-size: 16px; font-weight: bold;">
                          เพิ่มผู้ถูกใช้สิทธิ                          </span>
                          
                          
                           <div style="font-size: 16px; font-weight: bold; color:#009900; padding-top:7px;">
                          ผู้ใช้สิทธิคนนี้ ยังไม่มีข้อมูลผู้ถูกใช้สิทธิ กรุณาเพิ่มข้อมูลผู้ถูกใช้สิทธิ</div>
                          
                          <?php }elseif($_GET["edit_id"]){?>
                          
                          <span style="font-size: 16px; font-weight: bold;">
                         แก้ไขข้อมูลผู้ถูกใช้สิทธิ                          </span>
                          
                          <?php }else{?>
                          
                           <span style="font-size: 16px; font-weight: bold;">
		                          แก้ไขข้อมูลผู้ใช้สิทธิ             
                           </span>
                           
		                           <?php if($curator_row_to_fill[curator_is_dummy_row] ){?>
                                    
                                            <div style="color: #F60; font-weight: bold; padding: 10px 0;">                                               
                                               
                                              
                                               กรุณากรอกข้อมูลคนพิการที่ได้รับเข้าทำงานให้ครบถ้วน
                                            </div>
                                        
                                    
                                    <?php }?>
                          
                          <?php }?>
                          
                          <?php if($_GET["curate"]){?>
                          <div style="padding:10px 0;"><font color="#009900"><strong>เพิ่มข้อมูลผู้ถูกใช้สิทธิเสร็จสิ้น</strong></font></div>
                          <?php }?>
                          
                          <?php if($updated_done){?>
                          <div style="padding:10px 0;"><font color="#009900"><strong>ข้อมูลได้รับการบันทึกแล้ว</strong></font></div>
                          <?php }?>
                          <?php if($_GET["del"]){?>
                          <div style="padding:10px 0;"><font color="#009900"><strong>ข้อมูลผู้ถูกใช้สิทธิ ถูกลบแล้ว</strong></font></div>
                          <?php }?>                          </td>
                        </tr>
                       <tr>
                            <td>
                            
                                เลขที่บัตรประชาชน        </td>
                            <td colspan="3">
                            
                                <input type="text" name="curator_idcard__" id="curator_idcard__" maxlength="13"
                                
                                value="<?php echo $curator_row_to_fill["curator_idcard"];?>"
                                
                                
                                style="display: none;"
                                 /> 
                                 
                                 
                                 
                                 <?php 
								 	$id_form_name = "curator_form";
									$id_form_to_show = $curator_row_to_fill["curator_idcard"];
									
									$txt_id_card_prefix = "";
									
								 	include "txt_id_card.php";
									
									$txt_id_card_prefix = "";
								?>
                                 
                                 <?php if($mode == "add_curator_usee" && $sess_accesslevel != 4 && $sess_accesslevel != 5 && $sess_accesslevel != 8 && !$case_closed){?>
                                 <input id="btn_get_data" type="button" value="ดึงข้อมูลคนพิการ" onClick="return doGetData();" />
                                 <img id="img_get_data" src="decors/loading.gif" width="10" height="10" style="display:none;" />
                                 
                                 <script>
											
												function doGetData(){
												
													var the_id = "";
													
													//
													<?php for($i=1;$i<=13;$i++){?>
													the_id = the_id + document.getElementById('id_<?php echo $i;?>').value;
													<?php }?>
													
													//alert(the_id);
												
													var checkOK = "1234567890";
												   var checkStr = the_id;
												   var allValid = true;
												   for (i = 0;  i < checkStr.length;  i++)
												   {
													 ch = checkStr.charAt(i);
													 for (j = 0;  j < checkOK.length;  j++)
													   if (ch == checkOK.charAt(j))
														 break;
													 if (j == checkOK.length)
													 {
													   allValid = false;
													   break;
													 }
												   }
												   if (!allValid)
												   {
													 alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
													 document.getElementById('id_1').focus();
													 return (false);
												   }
													
													
													if(the_id.length != 13)
													{
														alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
														document.getElementById('id_1').focus();
														return (false);
													}
												
													//alert("do get data");
													document.getElementById('btn_get_data').style.display = 'none';
													document.getElementById('img_get_data').style.display = '';
													
													
													
													var parameters = "the_id="+the_id;
													//alert(parameters);
													//return false;
													//send requests
													http_request = false;
													 if (window.XMLHttpRequest) { // Mozilla, Safari,...
														 http_request = new XMLHttpRequest();
														 if (http_request.overrideMimeType) {										
															http_request.overrideMimeType('text/html');
														 }
													  } else if (window.ActiveXObject) { // IE
														 try {
															http_request = new ActiveXObject("Msxml2.XMLHTTP");
														 } catch (e) {
															try {
															   http_request = new ActiveXObject("Microsoft.XMLHTTP");
															} catch (e) {}
														 }
													  }
													  if (!http_request) {
														 alert('Cannot create XMLHTTP instance');
														 return false;
													  }
													
													http_request.onreadystatechange = alertContents3;
													http_request.open('POST', "./ajax_get_des_person.php", true);
													http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;");
													http_request.setRequestHeader("Content-length", parameters.length);
													http_request.setRequestHeader("Connection", "close");
													
													http_request.send(parameters);
													
													return true;
												
												}
												
												function alertContents3(){
													
													if (http_request.readyState == 4) {
													
														if (http_request.status == 200) {
															
															//alert("response recieved");
															document.getElementById('btn_get_data').style.display = '';
															document.getElementById('img_get_data').style.display = 'none';
														
															//alert(http_request.responseText);
															//return false;
															
															if(http_request.responseText == "no_result"){
															
																alert("ไม่พบข้อมูลคนพิการ");
																//no result
																//document.getElementById("none_to_rate").style.display = "block";
																//document.getElementById("have_to_rate").style.display = "none";
																//document.getElementById("rate_me_table").style.display = "none";
																
															}else{
															
																var JSONFile = http_request.responseText;  
																eval(JSONFile); 										
																//alert(someVar.color); // Outputs 'blue' 
																
																//alert(someVar.DEFORM_ID);
																
																document.getElementById('curator_name').value =  someVar.PREFIX_NAME_ABBR + someVar.FIRST_NAME_THAI + " " + someVar.LAST_NAME_THAI;
																if(someVar.SEX_CODE == 'M'){
																	document.getElementById('curator_gender').selectedIndex  = 0;
																}
																if(someVar.SEX_CODE == 'F'){
																	document.getElementById('curator_gender').selectedIndex  = 1;
																}
																
																
																if(someVar.DEFORM_ID == 1 || someVar.DEFORM_ID == 6 || someVar.DEFORM_ID == 12){
																	document.getElementById('le_disable_desc').selectedIndex  = 0;
																}
																if(someVar.DEFORM_ID == 2 || someVar.DEFORM_ID == 7 || someVar.DEFORM_ID == 13){
																	document.getElementById('le_disable_desc').selectedIndex  = 1;
																}
																if(someVar.DEFORM_ID == 3 || someVar.DEFORM_ID == 8 || someVar.DEFORM_ID == 14){
																	document.getElementById('le_disable_desc').selectedIndex  = 2;
																}
																if(someVar.DEFORM_ID == 4 || someVar.DEFORM_ID == 9 || someVar.DEFORM_ID == 15){
																	document.getElementById('le_disable_desc').selectedIndex  = 3;
																}
																if(someVar.DEFORM_ID == 5 || someVar.DEFORM_ID == 10 || someVar.DEFORM_ID == 16){
																	document.getElementById('le_disable_desc').selectedIndex  = 4;
																}
																if(someVar.DEFORM_ID == 6 || someVar.DEFORM_ID == 11 || someVar.DEFORM_ID == 17){
																	document.getElementById('le_disable_desc').selectedIndex  = 5;
																}
																if(someVar.DEFORM_ID == 18){
																	document.getElementById('le_disable_desc').selectedIndex  = 6;
																}
																
																document.getElementById('curator_age').value = someVar.BIRTH_DATE;
															
															}
															//
															
														} else {
															alert('การเชื่อมต่อผิดพลาด โปรดลองอีกครั้ง');
														}
													}
												
												}
											
											</script>
                                 
                                 
                                 <?php }?>
                                
                                 
                                *                                </td>
                        </tr>
                       <tr>
                         <td> ชื่อ-นามสกุล </td>
                         <td><input type="text" name="curator_name" id="curator_name" 
                                
                                 value="<?php echo $curator_row_to_fill["curator_name"];?>"
                                
                                />
                           * </td>
                         <td>&nbsp;</td>
                         <td>&nbsp;</td>
                       </tr>
                        
                        
                        <tr>
                            <td>
                            
                                เพศ        </td>
                            <td>
                            
                                <select name="curator_gender" id="curator_gender">
                                    <option value="m" 
                                    
                                    <?php if($curator_row_to_fill["curator_gender"] == "m"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >ชาย</option>
                                    <option value="f"
                                    
                                     <?php if($curator_row_to_fill["curator_gender"] == "f"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >หญิง</option>
                                  </select> 
                                *        </td>
                            <td>
                            
                                อายุ        </td>
                            <td>
                            
                                <input name="curator_age" type="text" id="curator_age" size="10"  value="<?php echo $curator_row_to_fill["curator_age"];?>" /></td>
                        </tr>
                        
                 
                 		<tr <?php if($curator_row_to_fill["curator_parent"] != "0"){echo 'style="display:none"';}?>>
                            <td>
                            
                                <span id="curator_is_disable_text">ผู้ใช้สิทธิเป็น</span>                                </td>
                            <td colspan="3">
                            <div id="curator_is_disable">
                                <input name="curator_is_disable" type="radio" value="0" onClick="document.getElementById('tr_curator_disable').style.display = 'none';" checked="checked" 
                                
                                <?php if($curator_row_to_fill["curator_is_disable"] == "0" || $curator_row_to_fill["curator_is_disable"] == ""){ echo 'checked="checked"'; }?>
                                
                                
                                /> ผู้ดูแลคนพิการ
                                   
                                <input name="curator_is_disable" type="radio" value="1" onClick="document.getElementById('tr_curator_disable').style.display = '';"
                                
                                <?php if($curator_row_to_fill["curator_is_disable"] == "1"){?>
                                checked="checked"
                                <?php }?>
                                
                                /> คนพิการ                            </div>                            </td>
                        </tr>
                        
                         <tr id="tr_curator_disable" <?php if($curator_row_to_fill["curator_is_disable"] == "0" || $curator_row_to_fill["curator_is_disable"] == ""){echo 'style="display:none"';}?>>
                          <td valign="top">ลักษณะความพิการ</td>
                          <td colspan="3"><?php 
						  	
							$do_hide_blank_dis = 1; 
							
							include "ddl_disable_type.php";
							
							?></td>
                        </tr>
                        
                       
                        
                        
                        <tr <?php if($curator_row_to_fill["curator_parent"] != "0"){echo 'style="display:none"';}?>>
                            <td>
                            
                            	<span id="curator_start_date_text">วันเริ่มต้นสัญญา</span>                                </td>
                            <td>
                            <?php
											   
							   $selector_name = "curator_start_date";
							   $this_date_time = $curator_row_to_fill["curator_start_date"];
							   
							   include ("date_selector.php");
							   
							   ?> *                               </td>
                            <td>
                            	<span id="curator_end_date_text">
                            	วันสิ้นสุดสัญญา</span>                            </td>
                            <td>
                            
                            	 <?php
											   
							   $selector_name = "curator_end_date";
							   $this_date_time = $curator_row_to_fill["curator_end_date"];
							   
							   include ("date_selector_plus_ten.php");
							   
							   //reset this_date_time just in case
							   $this_date_time = "0000-00-00";
							   
							   ?> *                             </td>
                        </tr>
                 
                 
                 
                 
                 		<tr id="tr_curator_event" <?php if($curator_row_to_fill["curator_parent"] != "0"){echo 'style="display:none"';}?>>
                          <td valign="top">กิจกรรมตามมาตรา 35</td>
                          <td >
                          
                          	<select name="curator_event" id="curator_event">
                                    <option value="การให้สัมปทาน" 
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "การให้สัมปทาน"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >การให้สัมปทาน</option>
                                    <option value="จัดสถานที่จำหน่ายสินค้าหรือบริการ"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "จัดสถานที่จำหน่ายสินค้าหรือบริการ"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >จัดสถานที่จำหน่ายสินค้าหรือบริการ</option>
                                    <option value="จัดจ้างเหมาช่วงงาน"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "จัดจ้างเหมาช่วงงาน"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >จัดจ้างเหมาช่วงงาน</option>
                                    <option value="ฝึกงาน"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "ฝึกงาน"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >ฝึกงาน</option>
                                    <option value="การให้ความช่วยเหลืออื่นใด"
                                    
                                    <?php if($curator_row_to_fill["curator_event"] == "การให้ความช่วยเหลืออื่นใด"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >การให้ความช่วยเหลืออื่นใด</option>
                            </select>                                                    </td>
                                                    
                            
                            <td><div align="right">มูลค่า</div></td>
                            <td><input name="curator_value" id="curator_value" style="text-align:right;"  type="text" size="10" 
                            
                             value="<?php echo formatMoney($curator_row_to_fill["curator_value"]);?>"
                            
                            onChange="addCommas('curator_value');"/> 
                            บาท
                            
                            
                            
                            <?php include "js_format_currency.php";?>                            </td>
                        </tr>
                        
                        <tr id="tr_curator_event_2" <?php if($curator_row_to_fill["curator_parent"] != "0"){echo 'style="display:none"';}?> >
                          <td valign="top">รายละเอียด</td>
                          <td colspan="3">
                          
                          	
                            <textarea name="curator_event_desc" cols="40" rows="4"><?php echo $curator_row_to_fill["curator_event_desc"];?></textarea>                          </td>
                        </tr>
                        
                        <tr id="tr_curator_docfile" <?php if($curator_row_to_fill["curator_parent"] != "0"){echo 'style="display:none"';}?>>
                         <td valign="top">เอกสารประกอบ</td>
                          <td colspan="3">
							<input type="file" name="curator_docfile" id="curator_docfile" />                            </td>
                        </tr>
                        
                        
                        
                       
                        
                        
                        <tr>
                            <td colspan="4">
                                <div align="center">
                                 
                                  <input name="case_closed" type="text" value="<?php echo  default_value($case_closed, $case_closed); ?>" />
                                  
                                 <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 ){//exec can't do all these?>
                                 
                                 	 <?php if($is_edit_curator){?>
		                               <input name="do_add_curator" type="submit" value="บันทึก" style="font-size:18px; font-weight: bold;" />
                                       <input name="do_cancel_edit" type="submit" value="ยกเลิกการแก้ไข" style="display: none;" />
                                     <?php }else{ ?>
                                     
                                     	<input name="do_add_curator" type="submit" value="บันทึก" style="font-size:18px; font-weight: bold;" />
                                     <?php }?>  
                                       
                                <?php }?>
                                 
                                 <?php if($is_edit_curator){?>
                                 
                                  <input name="curator_id" type="hidden" value="<?php echo $curator_row_to_fill["curator_id"];?>" />
                                 <?php }?>
                                 
                                 <input name="curator_lid" type="hidden" value="<?php echo $this_lid; ?>" />
                                  
                                   <?php if($mode == "add_curator_usee"){?>
    	                              <input name="curator_parent" id="curator_parent" type="hidden" value="<?php echo $curator_id;?>" />
                                  <?php }elseif($is_edit_curator){?>
	                                  <input name="curator_parent" id="curator_parent" type="hidden" value="<?php echo $curator_row_to_fill["curator_parent"];?>" />                             
								  <?php }else{?>
        	                          <input name="curator_parent" id="curator_parent" type="hidden" value="0" />
                                  <?php }?>
                                  
                                  <hr />
                                  
                                  
                                  <?php if($mode != "add_curator_usee"){ //dont show this link if this is an "add" page?>
                                <a href="organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $this_lawful_year;?>" style="font-size:12px;"
                                
                                onclick="return validateCuratorForm(document.getElementById('curator_form'));"
                                
                                >>> กลับไปหน้าปฏิบัติตามกฏหมาย<?php echo $company_name_to_use;?> <<</a>                                
                                
                                <?PHp }?>
                                
                                </div>                           
                                
                                
                                 </td>
                        </tr>
                  </table>
                    	
                    
                    
                </form>
                    
                    <script language='javascript'>
						<!--
						function validateCuratorForm(frm) {
							
							/*if(frm.curator_idcard.value.length < 13)
							{
								alert("กรุณาใส่ข้อมูล: เลขที่บัตรประชาชน");
								frm.curator_idcard.focus();
								return (false);
							}*/
							
							var checkOK = "1234567890";
   
							
							<?php for($i=1;$i<=13;$i++){?>
							if(frm.id_<?php echo $i;?>.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: เลขที่บัตรประชาชน");
								frm.id_<?php echo $i;?>.focus();
								return (false);
							}
							
							var checkStr = frm.id_<?php echo $i;?>.value;
							var allValid = true;
							for (i = 0;  i < checkStr.length;  i++)
						   {
							 ch = checkStr.charAt(i);
							 for (j = 0;  j < checkOK.length;  j++)
							   if (ch == checkOK.charAt(j))
								 break;
							 if (j == checkOK.length)
							 {
							   allValid = false;
							   break;
							 }
						   }
						   if (!allValid)
						   {
							 alert("เลขบัตรประชาชนต้องเป็นตัวเลขเท่านั้น");
							 frm.id_<?php echo $i;?>.focus();
							 return (false);
						   }
							<?php }?>
							
							
							if(frm.curator_name.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อ-นามสกุล");
								frm.curator_name.focus();
								return (false);
							}
							if(frm.curator_age.value.length == 0)
							{
								//alert("กรุณาใส่ข้อมูล: อายุ");
								//frm.curator_age.focus();
								//return (false);
							}
							
							//age
							var checkOK = "1234567890";
							
							var checkStr = frm.curator_age.value;
							var allValid = true;
							for (i = 0;  i < checkStr.length;  i++)
						   {
							 ch = checkStr.charAt(i);
							 for (j = 0;  j < checkOK.length;  j++)
							   if (ch == checkOK.charAt(j))
								 break;
							 if (j == checkOK.length)
							 {
							   allValid = false;
							   break;
							 }
						   }
						   if (!allValid)
						   {
							 alert("อายุต้องเป็นตัวเลขเท่านั้น");
							 frm.curator_age.focus();
							 return (false);
						   }
						   //end age
						   
							//----
							
							 <?php if($curator_row_to_fill["curator_parent"] == "0"){ //these are for parents only?>
							if(frm.curator_start_date_day.selectedIndex == 0 || frm.curator_start_date_month.selectedIndex == 0 || frm.curator_start_date_year.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: วันเริ่มต้นสัญญา");
								//frm.CompanyTypeCode.focus();
								return (false);
							}
							if(frm.curator_end_date_day.selectedIndex == 0 || frm.curator_end_date_month.selectedIndex == 0 || frm.curator_end_date_year.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: วันสิ้นสุดสัญญา");
								//frm.CompanyTypeCode.focus();
								return (false);
							}
							
							if(frm.curator_value.value.length == 0 || frm.curator_value.value < 1)
							{
								//alert("กรุณาใส่ข้อมูล: มูลค่า");
								//frm.curator_value.focus();
								//return (false);
							}
							
							
							
							<?php }?>
							//----
							return(true);									
						
						}
						-->
					
					</script>
                    
                    
                    
                    
                     <table width="750" border="1" cellspacing="0" cellpadding="3" style="border-collapse:collapse" align="center">
                        <tr>
                            <td colspan="14" style="padding:8px 5px;">
                            	<strong><span style="font-size:16px;  margin-top: 5px;">ข้อมูลผู้ใช้สิทธิ</span></strong>
                            </td>
                        </tr>
                         <tr bgcolor="#efefef">
                             <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
                              <td><div align="center">ชื่อ-นามสกุล</div></td>
                              <td><div align="center">เพศ</div></td>
                              <td><div align="center">อายุ</div></td>
                              <td><div align="center">เลขที่บัตรประชาชน</div></td>
                              <td><div align="center">ผู้ใช้สิทธิเป็น</div></td>
                              <td><div align="center">วันเริ่มต้นสัญญา</div></td>
                              <td><div align="center">วันสิ้นสุดสัญญา</div></td>
                              <td><div align="center">ระยะเวลา</div></td>
                              <td><div align="center">กิจกรรม</div></td>
                              <td><div align="center">มูลค่า (บาท)</div></td>
                              <td><div align="center">รายละเอียด</div></td>
                              
                               <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$case_closed && $count_usee <= 1 ){?>
                             
                              <td><div align="center">ลบข้อมูล</div></td>
                              <td><div align="center">แก้ไขข้อมูล</div></td>
                              <?php }?>
                              
                        </tr> 
                        
                                    
                        <?php
                       
                            //get main curator
                            $sql = "select * from $table_name where curator_id = '$curator_id'";
                            //echo $sql;
                            
                            $org_result = mysql_query($sql);
                            $total_records = 0;
							
                            while ($post_row = mysql_fetch_array($org_result)) {			
                                
                                $total_records++;
                        
                        ?>
                             <tr >
                              <td style="border-top:1px solid #999999; "><div align="center"><strong><?php echo $total_records;?></strong></div></td>
                              <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_name"]);?></td>
                              <td style="border-top:1px solid #999999;"><?php echo formatGender($post_row["curator_gender"]);?></td>
                              <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_age"]);?></td>
                              
                              <td style="border-top:1px solid #999999;">
                              
                              
                              <?php echo doCleanOutput($post_row["curator_idcard"]);?>
                              
                               <?php 
                                                      
                                                        //see if this le_id already in another ID
                                                        
                                                        $this_curator_idcard = $post_row["curator_idcard"];
                                                        $this_curator_id = $post_row["curator_id"];
                                                        $this_le_cid = $post_row["le_cid"];
                                                        $this_le_year = $post_row["le_year"];
                                                        
                                                        $sql = "select * from lawful_employees
															 	where 
																le_code = '$this_curator_idcard'
                                                                and 
																le_year = '$this_lawful_year'
																and
																le_is_dummy_row = 0
                                                                ";
                                                      
                                                        //echo $sql;
                                                      
                                                        $le_result = mysql_query($sql);
                                                        
                                                        while ($le_row = mysql_fetch_array($le_result)) {
                                                    
                                                      
                                                      ?>
                                                      
                                                      
                                                      	 <?php 
												
															//yoes 20151118 -- make it so company can see link
															if($sess_accesslevel == 4){
															
															?>
															
															
															
                                                                <span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
                                                                ! คนพิการนี้มีการทำมาตรา 33 แล้ว <br />
                                                                </span>
														  
															
															
															<?php }else{ ?>
														  
                                                              <div>
                                                                <a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">! พบในมาตรา 33</a>
                                                              </div>
														  
														  
														  <?php }?>
                                                      
                                                      <?php }?>
                                                      
                                                      
                                                      <?php 
                                                        
                                                        $sql = "select 
                                                            * 
                                                            from 
                                                            curator a, lawfulness b
                                                            
                                                            where 
                                                            a.curator_lid 	= b.LID
                                                            and
                                                            curator_idcard = '$this_curator_idcard'
                                                            and
                                                            curator_id != '$this_curator_id'
                                                            and
                                                            year = '$this_lawful_year'
															and
															curator_is_dummy_row = 0
                                                        ";
                                                      
                                                      
                                                      
                                                        $le_result = mysql_query($sql);
                                                        
                                                        while ($le_row = mysql_fetch_array($le_result)) {
                                                    
                                                        $lawfulness_row = getFirstRow("select CID,Year from lawfulness where lid = '".$le_row["curator_lid"]."'");
                                                        
                                                        $this_company_id = $lawfulness_row["CID"];
                                                        $this_the_year = $lawfulness_row["Year"];
                                                      
                                                      ?>
                                                      
                                                      		   <?php 
												
															//yoes 20151118 -- make it so company can see link
															if($sess_accesslevel == 4){
															
															?>
															
															
															
                                                                    <span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
                                                                    ! พบในสถานประกอบการอื่น <br />
                                                                    </span>
														  
															
															
															<?php }else{ ?>
														  
                                                      
                                                      
                                                      
                                                                  <div>
                                                                    <a href="organization.php?id=<?php echo $this_company_id;?>&curate=curate&focus=lawful&year=<?php echo $this_the_year;?>" style="color:#006600; text-decoration:underline;" target="_blank">! พบในสถานประกอบการอื่น</a>
                                                                  </div>
                                                      
                                                      
		                                                      <?php }?>
                                                      
                                                      <?php }?>
                              
                              </td>
                              
                              
                              
                              <td style="border-top:1px solid #999999;">
                              <?php if($post_row["curator_is_disable"] == 1){
                                
                                    echo "<font color='green'>คนพิการ : " . $post_row["curator_disable_desc"]. "</font>";
                                    
                                }else{
                                
                                    echo "<font color='blue'>ผู้ดูแลคนพิกา</font>ร";
                                    
                                }?>
                              
                              </td>
                              
                              
                              <td style="border-top:1px solid #999999;"><?php echo formatDateThai($post_row["curator_start_date"]);?></td>
                                <td style="border-top:1px solid #999999;"><?php echo formatDateThai($post_row["curator_end_date"]);?></td>
                                
                                <td style="border-top:1px solid #999999;"><?php 
                                
                                
                                //echo $post_row["curator_start_date"];
                                //echo $post_row["curator_end_date"];
                                echo number_format(dateDiffTs(strtotime($post_row["curator_start_date"]), strtotime($post_row["curator_end_date"])),0);
                                
                                ?> วัน</td>
                                
                               
                              
                               <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_event"]);?></td>
                               
                               <td style="border-top:1px solid #999999;"><div align="right"><?php echo formatNumber($post_row["curator_value"]);?></div></td>
                               
                                <td style="border-top:1px solid #999999;"><?php 
                                
                                        echo doCleanOutput($post_row["curator_event_desc"]);
                                        
                                        //also see if there are any attached files
                                        $curator_file_path = mysql_query("select 
                                                                                * 
                                                                           from 
                                                                                 files 
                                                                            where 
                                                                                file_for = '".$post_row["curator_id"]."'
                                                                                and
                                                                                file_type = 'curator_docfile'
                                                                                ");
                                                                                
                                        while ($file_row = mysql_fetch_array($curator_file_path)) {
                                        
                                        ?>
                                            <a href="hire_docfile/<?php echo $file_row["file_name"];?>" target="_blank">ไฟล์แนบ</a>
                                            
                                            <?php if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3){?>
                                            <a href="scrp_delete_curator_file.php?id=<?php echo $file_row["file_id"];?>&curator_id=<?php echo $curator_id;?>" title="ลบไฟล์แนบ" onClick="return confirm('คุณแน่ใจหรือว่าจะลบไฟล์แนบ? การลบไฟล์ถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบไฟล์ได้');"><img src="decors/cross_icon.gif" alt="" height="10"  border="0" /></a>
                                            <?php }?>
        
                                            <!--<a href="force_load_file.php?file_for=<?php echo $file_row["curator_id"];?>&file_type=curator_docfile" target="_blank">ไฟล์แนบ</a>-->
                                        <?php
                                        
                                        
                                        }
                                        
                                        
                                        
                                        
                                        ?></td>
                                
                                <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$case_closed && $count_usee <= 1 ){?>
                                
                                  <td><div align="center"><a href="scrp_delete_curator_new.php?id=<?php echo doCleanOutput($post_row["curator_id"]);?>&cid=<?php echo $this_cid;?>&year=<?php echo $this_lawful_year;?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูลผู้ใช้สิทธิ? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                  
                                  <td>
                                  <div align="center"><a href="view_curator.php?curator_id=<?php echo $curator_id;?>" title="แก้ไขข้อมูล"><img src="decors/create_user.gif" alt="" border="0" /></a></div>
                                  </td>
                                  
                                  
                              <?php }?>
                             
                            </tr>      
                        
                        
                      <?php }//end loop for curator?>
                        
                      </table>
                      
                      
                      
                      <table width="650" align="center" border="1" cellspacing="0" cellpadding="3" style="border-collapse:collapse; <?php if($post_row["curator_is_disable"]){echo "display:none;";}?>">
                        	
                            <tr>
                                <td colspan="8" >
                               
                                
                                 
                                
                                <?php if($count_usee > 1){?>
                    
                                    <div style="padding:10px 0; font-size:16px;"><font color="#009900"><strong>กรุณาเลือกผู้ถูกใช้สิทธิ</strong></font></div>
                                    
                                <?php }else{?>
                                
                                	<?php if(!$parent_is_disabled){?>
                                   		 <strong>ผู้ถูกใช้สิทธิ</strong> 
                                    <?php }?>
                                    
                                    <?php if($count_usee < 1){?>
                                    <a href="view_curator.php?curator_id=<?php echo $curator_id;?>&mode=add_curator_usee" >
                                        +<span style="font-size: 16px">เพิ่มผู้ถูกใช้สิทธิ</span>
                                    </a>
                                    <?php }?>
                                    
                                <?php }?>
                                
                                
                                </td>
                               	  
                                <?php 
								
									//get sub-curator
									$sql = "select 
												* 
											from 
												$table_name 
											where curator_parent = '".$curator_id."'";
									//echo $sql;
									
									$sub_result = mysql_query($sql);
									$total_sub = 0;
									while ($sub_row = mysql_fetch_array($sub_result)) {			
								
										$total_sub++;
									
								?>
                                 
                                 <?php if($total_sub == 1){?>
                                 
                                 <tr bgcolor="#efefef">
                                 	<?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$case_closed && $count_usee > 1){?>
                                    <td></td>
                                    <?php }?>
                                     <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
                                      <td><div align="center">ชื่อ-นามสกุล</div></td>
                                      <td><div align="center">เพศ</div></td>
                                      <td><div align="center">อายุ</div></td>
                                      <td><div align="center">เลขที่บัตรประชาชน</div></td>
                                      <td><div align="center">ลักษณะความพิการ</div></td>
                                      <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$case_closed && $count_usee <= 1){?>
                                      <td><div align="center">ลบ</div></td>
                                      <td><div align="center">แก้ไข</div></td>
                                      <?php }?>
                                </tr> 
                                 
                                 <?php }?>
                                 
							
                                 <tr>
                                 
                                  <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$case_closed && $count_usee > 1){?>
                                  <td >
                                  	<div align="center">
                                        <form method="post">
                                        
                                        	<input name="parent_id" type="hidden" value="<?php echo $curator_id_to_fill;?>" />
                                        	<input name="child_id" type="hidden" value="<?php echo $sub_row["curator_id"];?>" />
                                    	    <input name="select_child" style="font-weight: bold; color:#006699" value="เลือก" type="submit" onclick="return confirm('เลือกคนพิการนี้ เป็นผู้ถูกใช้สิทธิ?');" />                                    
                                        
                                        </form>
                                    </div>
                                  </td>
                                  <?php }?>
                                 
                                  <td valign="top"><div align="center"><?php echo $total_sub;?></div></td>
                                  <td valign="top"><?php echo doCleanOutput($sub_row["curator_name"]);?></td>
                                  <td valign="top"><?php echo formatGender($sub_row["curator_gender"]);?></td>
                                  <td valign="top"><?php echo doCleanOutput($sub_row["curator_age"]);?></td>
                                  <td valign="top">
								  
								  <?php echo doCleanOutput($sub_row["curator_idcard"]);?>
                                  
                                  
                                   <?php 
                                          
                                            //see if this le_id already in another ID
                                            
                                            $this_curator_idcard = $sub_row["curator_idcard"];
                                            $this_curator_id = $sub_row["curator_id"];
                                            $this_le_cid = $sub_row["le_cid"];
                                            $this_le_year = $sub_row["le_year"];
                                            
                                            $sql = "select * from lawful_employees where le_code = '$this_curator_idcard'
													and le_year = '$this_lawful_year'
													";
                                          
                                          	//echo $sql;
                                          
                                            $le_result = mysql_query($sql);
                                            
                                            while ($le_row = mysql_fetch_array($le_result)) {
										
                                          
                                          ?>
                                          
                                          		 <?php 
												
												//yoes 20151118 -- make it so company can see link
												if($sess_accesslevel == 4){
												
												?>
												
												
												
													<span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
													! คนพิการนี้มีการทำมาตรา 33 แล้ว <br />
													</span>
											  
												
												
												<?php }else{ ?>
                                          
                                          
                                                      <div>
                                                        <a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">! พบในมาตรา 33</a>
                                                      </div>
                                                      
                                                  <?php }?>
                                          
                                          <?php }?>
                                          
                                          
                                          <?php 
                                            
                                             $sql = "select 
												* 
												from 
												curator a, lawfulness b
												
												where 
												a.curator_lid 	= b.LID
												and
												curator_idcard = '$this_curator_idcard'
												and
												curator_id != '$this_curator_id'
												and
												year = '$this_lawful_year'
											";
                                          
                                          
                                          
                                            $le_result = mysql_query($sql);
                                            
                                            while ($le_row = mysql_fetch_array($le_result)) {
                                        
                                            $lawfulness_row = getFirstRow("select CID, Year from lawfulness where lid = '".$le_row["curator_lid"]."'");
                                            
                                            $this_company_id = $lawfulness_row["CID"];
                                            $this_the_year = $lawfulness_row["Year"];
                                          
                                          ?>
                                          
                                         		 <?php 
												
												//yoes 20151118 -- make it so company can see link
												if($sess_accesslevel == 4){
												
												?>
												
												
												
														<span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
														! พบในสถานประกอบการอื่น<br />
														</span>
											  
												
												
												<?php }else{ ?>
                                          
                                          
                                          
                                                  <div>
                                                    <a href="organization.php?id=<?php echo $this_company_id;?>&curate=curate&focus=lawful&year=<?php echo $this_the_year;?>" style="color:#006600; text-decoration:underline;" target="_blank">! พบในสถานประกอบการอื่น</a>
                                                  </div>
                                                  
                                                  <?php }?>
                                          
                                          <?php }?>
                                  
                                  </td>
                                  <td  valign="top"><?php echo doCleanOutput($sub_row["curator_disable_desc"]);?></td>
                                  <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$case_closed && $count_usee <= 1){?>
                                      <td  valign="top"><div align="center"><a href="scrp_delete_curator_new.php?id=<?php echo doCleanOutput($sub_row["curator_id"]);?>&return_id=<?php echo $curator_id;?>" title="ลบข้อมูล" onClick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                      
                                      <td>
                      <div align="center"><a href="view_curator.php?curator_id=<?php echo $curator_id;?>&edit_id=<?php echo $sub_row["curator_id"];?>" title="แก้ไขข้อมูล"><img src="decors/create_user.gif" alt="" border="0" /></a></div>
                      </td>
                                  <?php }?>
                                  
                                 
                                  
                                  
                                </tr>  
                                
                                <?php } ?>
                                
                                
                            </tr>
						</table>
                    
                    
                    
                    
                   
                        
                   
                   
                   
              </td>
            </tr>
            
             <tr>
                <td align="right" colspan="2">
                    <?php include "bottom_menu.php";?>
                </td>
            </tr>  
            
		</table>                            
        
		
		<?php 
		
			if($_GET["do_auto_post"]){
		
			
			
				//echo $this_cid;
				//echo $this_lawful_year;
		
		?>
        <iframe width="1" height="1" src="./organization.php?id=<?php echo $this_cid;?>&focus=lawful&year=<?php echo $this_lawful_year;?>&auto_post=1"></iframe>
        
        <?php }?>
        
        </td>
    </tr>
    
</table>    

</body>
</html>
