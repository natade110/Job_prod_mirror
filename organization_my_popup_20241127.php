<div id="my_popup" class="modal bs-example-modal-lg-m33" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none; overflow: scroll;">


									
									
                                  <table id="myTable_form" bgcolor="#FFFFFF" width="1000" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;   ">
                                  <script language='javascript'>
									<!--
									
									function doValidateId(){
									
									
										var the_id = document.getElementById('le_code').value;
									
										alert(the_id);
									
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
										 document.getElementById('le_code').focus();
										 return (false);
									   }
										
										
										if(the_id.length != 13)
										{
											alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
											document.getElementById('le_code').focus();
											return (false);

										}
										
										//return true;
									
									
									}
									
									
									function doValidateEmployeeInfo(frm) {
										
										
										//check if submitted
										if($("#le_form_submitted").val() == 1){
											return false;	
										}
										
										
										var checkOK = "1234567890";
										
										
										<?php for($i=1;$i<=13;$i++){?>
										if(frm.leid_<?php echo $i;?>.value.length < 1)
										{
											alert("กรุณาใส่ข้อมูล: เลขที่บัตรประชาชน");
											frm.leid_<?php echo $i;?>.focus();
											return (false);
										}
										
										var checkStr = frm.leid_<?php echo $i;?>.value;
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
										 frm.leid_<?php echo $i;?>.focus();
										 return (false);
									   }
										<?php }?>
										
										
										if(frm.le_name.value.length < 1)
										{
											alert("กรุณาใส่ข้อมูล: ชื่อ-นามสกุล");
											frm.le_name.focus();
											return (false);
										}
										
										if(frm.le_age.value.length < 1)
										{
											alert("กรุณาใส่ข้อมูล: อายุ");
											frm.le_age.focus();
											return (false);
										}
										
										
										//check number a hardway
										var checkStr = frm.le_age.value;
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
										 frm.le_age.focus();
										 return (false);
									   }
									   
									   
									   //validate dropdown a hardway
										var e = document.getElementById("le_disable_desc_hire");
										var strValue = e.options[e.selectedIndex].value;
										
										if(strValue.length < 1)
										{
											alert("กรุณาใส่ข้อมูล: ลักษณะความพิการ");
											frm.le_disable_desc_hire.focus();
											return (false);
										}
										
										
										
										 //validate dropdown a hardway
										var e = document.getElementById("le_date_day");
										var strValue = e.options[e.selectedIndex].value;
										
										if(strValue == 0)
										{
											alert("กรุณาใส่ข้อมูล: วันที่เริ่มบรรจุงาน");
											frm.le_date_day.focus();
											return (false);
										}
										
										
										le_start_date = strValue;
										
										 //validate dropdown a hardway
										var e = document.getElementById("le_date_month");
										var strValue = e.options[e.selectedIndex].value;
										
										if(strValue == 0)
										{
											alert("กรุณาใส่ข้อมูล: วันที่เริ่มบรรจุงาน");
											frm.le_date_month.focus();
											return (false);
										}
										
										le_start_date = strValue + "-" + le_start_date;
										
										
										 //validate dropdown a hardway
										var e = document.getElementById("le_date_year");
										var strValue = e.options[e.selectedIndex].value;
										
										if(strValue == 0)
										{
											alert("กรุณาใส่ข้อมูล: วันที่เริ่มบรรจุงาน");
											frm.le_date_year.focus();
											return (false);
										}
										
										le_start_date = strValue + "-" + le_start_date;
										
										
										
										//
										if(frm.le_wage.value.length < 1 || frm.le_wage.value == "0.00")
										{
											alert("กรุณาใส่ข้อมูล: ค่าจ้าง");
											frm.le_wage.focus();
											return (false);
										}
										
										
										var checkOK = "1234567890.,";
										
										//check number a hardway
										var checkStr = frm.le_wage.value;
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
										 alert("ค่าจ้างต้องเป็นตัวเลขเท่านั้น");
										 frm.le_wage.focus();
										 return (false);
									   }
										
										
										
										
										if(frm.le_position.value.length < 1 )
										{
											alert("กรุณาใส่ข้อมูล: ตำแหน่งงาน");
											frm.le_position.focus();
											return (false);
										}
										
										
										if(frm.le_education.value.length < 1 )
										{
											alert("กรุณาใส่ข้อมูล: การศึกษา");
											frm.le_education.focus();
											return (false);
										}
										
										
										//more validation
										if(frm.le_education.value == 10 && frm.le_education_other.value.trim() == ""){
											alert("กรุณาใส่ข้อมูล: การศึกษา");
											frm.le_education_other.focus();
											return (false);
										}
										
										
										
										
										
										//----
										
										//yoes 20180220 -> validate parent/child
										var le_parent_end_date = $("#le_33_parent option:selected").attr("left_date");
										
										//alert(le_parent_end_date); 
										//alert(le_start_date); 
										
										
										if(le_parent_end_date != '0000-00-00'){
											 
											//alert("moomin");  
											//alert(le_start_date > le_parent_end_date); 
											if(le_start_date <= le_parent_end_date){
												
												alert("วันที่เริ่มบรรจุงาน เป็นวันก่อนที่คนพิการคนเดิมออกจากงาน");
												frm.le_33_parent.focus();
												return(false);
												
											}
											
										}
													
																					
										
										//enableObjects(['button4']);
										<?php if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3){ ?>
											formSubmit();	
											return(false);
										<?php
											}
										?>
										
										
										//$("#le_form_submitted").val(1);
										
										//return(true);									
									
									}
									-->
									
									
									
								
								</script>
                                    <form id="le_form" name="le_form" method="post" action="scrp_add_lawful_employee.php" 
                                    onSubmit="return doValidateEmployeeInfo(this);" enctype="multipart/form-data">
                                    
                                    <input type="hidden" value="0" id="le_form_submitted" />
                                    
                                  
								  
								    <tr bgcolor="#efefef">
										<td colspan="13">
										
											<div class="modal-header">
												<h4 class="modal-title" id="myLargeModalLabel" style="color: blue;">ข้อมูลคนพิการที่ได้รับเข้าทำงาน</h4>
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true">ปิดหน้าต่าง ×</button>
											</div>
											
										</td>
                                    </tr>
		
                                    
								
                                    
                                    <tr>
                                      <td colspan="13">
                                      
                                      	<?php
											
												if(is_numeric($_GET["leid"])){
												
													//if have leid then populate defaul value.....
													
													if($sess_accesslevel == 4){
													
														$leid_row = getFirstRow("select 
																* 
																from 
																lawful_employees_company
																where le_id = '".doCleanInput($_GET["leid"])."'");
															
													}else{
														
														$leid_row = getFirstRow("
														
														
																select 
																	* 
																from 
																	lawful_employees a
																		left join
																			lawful_employees_meta b
																				on a.le_id = b.meta_leid and meta_for = 'child_of'																	
																	
																where 
																	le_id = '".doCleanInput($_GET["leid"])."'
																
																
																");

													}
												
												}
												
												
												//yoes 20150118 -- extra records
												if(is_numeric($_GET["leidex"])){
														
														$leid_row = getFirstRow("select 
																* 
																from 
																lawful_employees_extra
																where le_id = '".doCleanInput($_GET["leidex"])."'");
														
														$leid_row["is_extra_row"] = 1;
													
												
												}
											
											?>
                                      
                                      
                                      	<table border="0" align="center" bgcolor="#FFFFFF" 
                                        
                                        <?php if($sess_accesslevel == 5 || $sess_accesslevel == 18 || $is_read_only){?>
                                        style="display: none;"
                                        <?php }?>
                                         >
                                        
                                        	<tr>
                                            	<td colspan="2">
                                                <?php 
													if($_GET["delle"]=="delle" ){
												?>							
													 <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ข้อมูลได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
												<?php
													}					
												?>
                                                <?php 
													if($_GET["le"]=="le" && !$_GET["leid"] ){
												?>							
													 <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มข้อมูลเรียบร้อย</div>
												<?php
													}					
												?>
                                                
                                                <?php 
													if($_GET["le"]=="le" && $_GET["leid"] ){
												?>							
													 <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">
	                                                     แก้ไขข้อมูลคนพิการที่ได้รับเข้าทำงาน
                                                     </div>
												<?php
													}					
												?>
                                                
                                                
                                                <?php if($leid_row[le_is_dummy_row]){?>
                                                
                                                		<span style="color: #F60; font-weight: bold;">
	                                                        กรุณากรอกข้อมูลคนพิการที่ได้รับเข้าทำงานให้ครบถ้วน
                                                        </span>
                                                	
                                                
                                                <?php }?>
                                                
                                                </td>
                                            </tr>
                                          <tr>
                                            <td>
                                            
                                             
                                            
                                            เลขที่บัตรประชาชน </td>
                                            <td>
                                            <input name="le_id"  id="le_id" type="hidden" value="<?php echo $leid_row["le_id"];?>" />
                                            
                                            
                                            <?php 
												$id_form_name = "le_form";
												$id_form_to_show = $leid_row["le_code"];
												
												$txt_id_card_prefix = "le";
												
												include "txt_id_card.php";
												
												$txt_id_card_prefix = "";
											?>
                                            
                                            <input type="text" name="le_code" id="le_code" style="display: none;" maxlength="13" value="<?php echo $leid_row["le_code"]?>"  />
                                            
                                           
                                            
                                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && $sess_accesslevel != 4 && !$case_closed){//company and exec can't do all these?>
                                            <input id="btn_get_data" type="button" value="ดึงข้อมูล" onClick="return doGetData();" />
                                            <?php }?>
                                            
                                             
                                            
                                            <img id="img_get_data" src="decors/loading.gif" width="10" height="10" style="display:none;" />
                                            
                                            <font color="red">*</font>
                                            
                                            <script>
											
												function doGetData(){
												
													var the_id = "";
													
													//
													<?php for($i=1;$i<=13;$i++){?>
													the_id = the_id + document.getElementById('leid_<?php echo $i;?>').value;
													<?php }?>
												
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
													 document.getElementById('leid_1').focus();
													 return (false);
												   }
													
													
													if(the_id.length != 13)
													{
														alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
														document.getElementById('leid_1').focus();
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
																//doGetSSOData();
																
															}else if(http_request.responseText == "json_error"){
																
																alert("ระบบไม่สามารถเชื่อมต่อกับระบบออกบัตรฯได้ กรุณาติดต่อผู้ดูแลระบบ");
																
															}else{
															
																var JSONFile = http_request.responseText;  
																eval(JSONFile); 										
																//alert(someVar.color); // Outputs 'blue' 
																
																//alert(someVar.DEFORM_ID);
																
																document.getElementById('le_full_name').value =  someVar.PREFIX_NAME_ABBR + someVar.FIRST_NAME_THAI + " " + someVar.LAST_NAME_THAI;
																if(someVar.SEX_CODE == 'M'){
																	document.getElementById('le_gender').selectedIndex  = 0;
																}
																if(someVar.SEX_CODE == 'F'){
																	document.getElementById('le_gender').selectedIndex  = 1;
																}
																
																
																if(someVar.DEFORM_ID == 1 || someVar.DEFORM_ID == 6 || someVar.DEFORM_ID == 12){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 1;
																}
																if(someVar.DEFORM_ID == 2 || someVar.DEFORM_ID == 7 || someVar.DEFORM_ID == 13){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 2;
																}
																if(someVar.DEFORM_ID == 3 || someVar.DEFORM_ID == 8 || someVar.DEFORM_ID == 14){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 3;
																}
																if(someVar.DEFORM_ID == 4 || someVar.DEFORM_ID == 9 || someVar.DEFORM_ID == 15){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 4;
																}
																if(someVar.DEFORM_ID == 5 || someVar.DEFORM_ID == 10 || someVar.DEFORM_ID == 16){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 5;
																}
																if(someVar.DEFORM_ID == 6 || someVar.DEFORM_ID == 11 || someVar.DEFORM_ID == 17){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 6;
																}
																if(someVar.DEFORM_ID == 18){
																	document.getElementById('le_disable_desc_hire').selectedIndex  = 7;
																}
																
																document.getElementById('le_age').value = someVar.BIRTH_DATE;
																
																
																//alert(someVar.ISSUE_DATE);
																//alert(someVar.EXP_DATE);
																//alert(someVar.PERMIT_DATE);
																
																if (someVar.ISSUE_DATE_DESC) {
																	$("#txt_card_issue_date_desc").html(someVar.ISSUE_DATE_DESC);
																	$("#txt_card_issue_date_desc").show();
																	//alert(someVar.ISSUE_DATE_DESC);
																}else{
																	$("#txt_card_issue_date_desc").hide();
																}
																
																//yoes 20170916 --> select ประวัติการทำงาน from sso
																doGetSSOData();
															
															}
															//
															
														} else {
															alert('การเชื่อมต่อผิดพลาด โปรดลองอีกครั้ง');
														}
													}
												
												}
											
											</script>
                                            
                                            </td>
                                            
                                            
                                            
                                            
                                            <td class="td_left_pad">
                                            
                                           
                                            
                                            
                                            ชื่อ-นามสกุล</td>
                                            <td><label>
                                              <input type="text" name="le_name" id="le_full_name" value="<?php echo $leid_row["le_name"]?>" /> <font color="red">*</font>
                                            </label></td>
                                          </tr>
                                          <tr>
                                            <td>เพศ</td>
                                            <td><label>
                                              <select name="le_gender" id="le_gender">
                                                <option value="m" <?php if($leid_row["le_gender"]=="m"){?>selected="selected"<?php }?>>ชาย</option>
                                                <option value="f" <?php if($leid_row["le_gender"]=="f"){?>selected="selected"<?php }?>>หญิง</option>
                                              </select> <font color="red">*</font>
                                            </label></td>
											
											<td ></td>
											<td >
												
												<?php if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3){?>
													<font id="txt_card_issue_date_desc" color=blue style='font-size: 12px; display:none;'>
														วันที่ออกบัตร: xxxx-xx-xx วันที่บัตรหมดอายุ: xxxx-xx-xx
														<br>
														(ออกบัตรครั้งแรกวันที่: xxxx-xx-xx)
													</font>
												<?php }?>
											
											</td>
                                           
                                            
                                          </tr>
										  
										  <tr>
										   <td >อายุ</td>
											<td><input name="le_age" type="text" id="le_age" size="10" value="<?php echo $leid_row["le_age"]?>" maxlength="2"/> <font color="red">*</font></td>
											
											<!-- <td class="td_left_pad">วัน-เดือน-ปีเกิด</td> -->
                                            <!-- <td>
											
											<?php
											
											// $selector_name = "le_dob";
											
											// if($leid_row["le_dob"]){
											// 	$this_date_time = $leid_row["le_dob"];
											// }
											
											// include ("date_selector_employee.php");
											
											?> 
											
											</td> -->
										  
										  </tr>
										  
										  
                                          <tr>
                                            <td>ลักษณะความพิการ</td>
                                            <td><?php 
											
												$dis_type_suffix = "_hire";
												include "ddl_disable_type.php";
												$dis_type_suffix = "";
												
												?> <font color="red">*</font></td>
												
                                          </tr>
                                          
                                          
                                           <tr>
                                          	<td>
                                            </td>
                                          	<td colspan="3">
                                            
                                            	<span id="sso_result">
                                                
                                                </span>
                                            
                                            </td>
                                         </tr>
                                         
                                          
                                          <tr>
                                          	
                                            <td >เริ่มบรรจุงาน</td>
                                            <td>
                                            
                                            <?php
											
											$selector_name = "le_date";
											
											if($leid_row["le_start_date"]){
												$this_date_time = $leid_row["le_start_date"];
											}
											
											include ("date_selector_employee.php");
											
											?> <font color="red">*</font> 
                                            
                                            
                                            
                                            
                                            
                                            
                                            <?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && !$is_read_only && !$case_closed){ ?>
                                           
                                           
                                            <?php if($sess_accesslevel == 1){ ?>
                                            <input id="btn_get_sso" type="button" value="ดึงข้อมูลการทำงาน" onClick="return doGetSSOData();" />                                            
                                            <?php }?>
                                           
                                            <script>
											
												function doGetSSOData(){
													
													var the_id = "";
													
													//
													<?php for($i=1;$i<=13;$i++){?>
													the_id = the_id + document.getElementById('leid_<?php echo $i;?>').value;
													<?php }?>
													
													
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
													 document.getElementById('leid_1').focus();
													 return (false);
												   }
													
													
													if(the_id.length != 13)
													{
														alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
														document.getElementById('leid_1').focus();
														return (false);
													}
												
													$.ajax({
													  method: "POST",
													  //url: "http://203.154.94.108/ajax_get_sso.php",
													  url: "https://wsfund.dep.go.th/ajax_get_sso.php",
													  //dataType: 'jsonp',
													  //url: "ajax_get_sso.php",
													  data: { name: "John", location: "Boston", the_id: the_id, the_user: <?php echo $sess_userid;?>, CompanyCode: "<?php echo getFirstItem("select companycode from company where cid = '$this_id' ");?>"}
													})
													  .done(function( html ) {
														//alert( "Data Saved: " + msg );
														$( "#sso_result" ).html( html);
														
														$('#sso_result').show();
													  });
												}
												
											</script>
                                            <?php }?>
                                            
                                            
                                            
                                            </td>
                                            
                                            
                                            <!-- <td class="td_left_pad">วันที่ออกจากงาน</td>
                                            <td>
                                            
                                            <?php
											
											// $selector_name = "le_end_date";
											
											// if($leid_row["le_end_date"]){
											// 	$this_date_time = $leid_row["le_end_date"];
											// }
											
											// include ("date_selector_employee.php");
											
											?> 
                                            
                                            </td> -->
                                          
                                          </tr>
										  
										  <?php if($this_lawful_year >= 2018 && $this_lawful_year <= 2050){ ?>
										  <tr>
											<td>เป็นการรับทำงานเข้าแทน...</td>
											<td colspan="3">
											
												<select id="le_33_parent" name="le_33_parent" onChange='doAlertSub33();'>
													<option value='0' left_date='0000-00-00'>-- ไม่ได้เป็นการรับแทน --</option>
													
													<?php
													
														//select 33 of this company that has left and not a parent of any other 33 yet
														$sub_33_sql = "
														
															select
																*
															from
																lawful_employees
															where
																le_end_date != '0000-00-00'
																and le_cid = '$this_id'
																and le_year = '$this_lawful_year'
																and
															
																(
																	le_id not in (
																	
																		select
																			meta_value
																		from
																			lawful_employees_meta
																		where
																			meta_for = 'child_of'
																			
																	
																	)
																	or
																	le_id in (
																	
																		select
																			meta_value
																		from
																			lawful_employees_meta
																		where
																			meta_for = 'child_of'
																			and
																			meta_leid = '".($_GET["leid"]*1)."'
																	
																	)
																)
																
																and
																le_id != '".($_GET["leid"]*1)."'

														
														";
														
														$sub_33_result = mysql_query($sub_33_sql);
																											
														
														while($sub_33_row = mysql_fetch_array($sub_33_result)){
														
														
														?>
														
														<option left_date='<?php echo $sub_33_row['le_end_date'];?>' value='<?php echo $sub_33_row['le_id']?>' <?php if($leid_row["meta_value"] == $sub_33_row['le_id']){echo "selected=selected";}?>>
															<?php echo $sub_33_row['le_code']." : ". $sub_33_row['le_name']." : จ้างงานวันที่ ".formatDateThaiShort($sub_33_row['le_start_date'],0)." ถึง ".formatDateThaiShort($sub_33_row['le_end_date'],0)?>
														</option>
														
														<?php
															
														}
														
														
													
													?>													
													
													
													
												</select>
												
												<script>
												
												function doAlertSub33(){
													//alert($("#le_33_parent option:selected").attr("left_date"));
												}
												
												
												function doValidateSub33(){
													
												}
												
												</script>
												
												<?php // echo $sub_33_sql; ?>
											
											</td>
										  </tr>
										  <?php } //ends if($this_lawful_year >= 2018 && $this_lawful_year <= 2050){ ?>
                                         
                                         
                                          <tr>
                                            <td>ค่าจ้าง</td>
                                            <td><input name="le_wage" type="text" id="le_wage" size="10"  style="text-align:right;" onChange="addCommas('le_wage');" value="<?php echo formatMoney($leid_row["le_wage"])?>"/> <?php
								  	
												include "js_format_currency.php";
											  
											  ?>
                                              
                                              <select name="le_wage_unit" id="le_wage_unit">
                                              	
                                                <option <?php if($leid_row["le_wage_unit"] == 0){?>selected="selected"<?php }?> value="0">บาท/เดือน</option>
                                                <option <?php if($leid_row["le_wage_unit"] == 1){?>selected="selected"<?php }?> value="1">บาท/วัน</option>
                                                <option <?php if($leid_row["le_wage_unit"] == 2){?>selected="selected"<?php }?> value="2">บาท/ชม.</option>
                                              
                                              </select>
                                              
                                              <!-- <font color="red">*</font> -->
                                              
                                              </td>
                                            <td class="td_left_pad">ประเภท</td>
                                            <td>
                                            
                                            
                                             <?php 
											
											//yoes 20160118 -- decide whether to show textbox or dropdown list
											
											//see if inputted value is in "education" list
											$position_in_list = getFirstItem("
													select 
														group_id
													from 
														position_group
													where 
														group_id = '".$leid_row["le_position"]."'
														or
														group_name = '".$leid_row["le_position"]."'
													");
													
											?>
                                            
                                            
                                            <?php if(!$position_in_list && strlen($leid_row["le_position"]) > 0){?>
                                            	<?php 
												
													$origin_position = $leid_row["le_position"];
													$leid_row["le_position"] = 23;
													include "ddl_position_group.php";
													$leid_row["le_position"] = $origin_position;
												?>
                                                <input type="text" name="le_position_other" id="le_position_other" value="<?php echo $leid_row["le_position"]?>"/>
                                            <?php }else{?>
	                                           <?php include "ddl_position_group.php";?>
                                               <input type="text" name="le_position_other" id="le_position_other" value=""/>
                                            <?php }?>
                                            
                                             <script>
												function checkPositionList(){
													//alert($('#le_education').val());
													if($('#le_position').val() == 23){
														$('#le_position_other').show();
													}else{
														$('#le_position_other').hide();
													}
												}
												
												checkPositionList();
											</script>
                                            
                                            
                                           
                                            
                                            <font color="red">*</font>
                                            </td>
                                          </tr>
                                          
                                           <tr>
                                            <td>การศึกษา</td>
                                            <td>
                                            
                                            
                                            <?php 
											
											//yoes 20160118 -- decide whether to show textbox or dropdown list
											
											//see if inputted value is in "education" list
											$edu_in_list = getFirstItem("
													select 
														edu_id
													from 
														education_level 
													where 
														edu_id = '".$leid_row["le_education"]."'
														or
														edu_name = '".$leid_row["le_education"]."'
													");
													
											?>
                                            
                                            
                                            <?php if(!$edu_in_list && strlen($leid_row["le_education"]) > 0){?>
                                                <?php 
												
												$origin_edu = $leid_row["le_education"];
												$leid_row["le_education"] = 10;
												
												include "ddl_edu_level.php";
												
												$leid_row["le_education"] = $origin_edu;
												
												?>                                            	
                                                <input type="text" name="le_education_other" id="le_education_other" value="<?php echo $leid_row["le_education"]?>"/>
                                            <?php }else{?>
	                                            <?php include "ddl_edu_level.php";?>
                                                <input type="text" name="le_education_other" id="le_education_other" value=""/>
                                            <?php }?>
                                            
                                            
                                            
                                            
                                            <script>
												function checkEduList(){
													//alert($('#le_education').val());
													if($('#le_education').val() == 10){
														$('#le_education_other').show();
													}else{
														$('#le_education_other').hide();
													}
												}
												
												checkEduList();
											</script>
                                            
                                            
                                            <font color="red">*</font>
                                              
                                              </td>
                                            <td class="td_left_pad">ตำแหน่ง</td>
                                            <td><input type="text" name="le_position_01" id="le_position_01" value=""/></td>
                                          </tr>
                                          
                                          
                                          <tr>
                                          
                                          		<td <?php /*yoes 20230130 --> hide this for 2023 and up*/ if($this_lawful_year >= 2023 && $this_lawful_year < 2500){ ?>style="display:none;"<?php }?>>
                                            	สำเนาสัญญาจ้าง</td>
                                               
                                                <td  <?php /*yoes 20230130 --> hide this for 2023 and up*/ if($this_lawful_year >= 2023 && $this_lawful_year < 2500){ ?>style="display:none;"<?php }?>>
                                                
                                                	<?php 
                                                  
												  	
												  
														$this_id_temp = $this_id;
														$this_id = $leid_row["le_id"]; 														
														$file_type = "docfile_33_1";
														include "doc_file_links.php";
														$this_id = $this_id_temp;
													 
													?>
                                                
                                               		<input type="file" name="docfile_33_1" id="docfile_33_1" />
                                                
                                                </td>
                                                
                                                 <td <?php /*yoes 20230130 --> hide this for 2023 and up*/ if($this_lawful_year >= 2023 && $this_lawful_year < 2500){}else{ ?>class="td_left_pad" <?php }?>>
                                                 สำเนาบัตรประจำตัวคนพิการ</td>
                                                 
                                           		 <td>
                                                 	<?php 
                                                  
												  	
												  
														$this_id_temp = $this_id;
														$this_id = $leid_row["le_id"]; 														
														$file_type = "docfile_33_2";
														include "doc_file_links.php";
														$this_id = $this_id_temp;
													 
													?>
                                                	 <input type="file" name="docfile_33_2" id="docfile_33_2" />
                                                 
                                                 </td>
                                            	
                                          
                                          </tr>
										  
										  
										  <tr>
                                          
                                          		<td>
                                            	เป็นการจ้าง ม33 เกินอัตราส่วน</td>
                                               
                                                <td colspan=3  >
													<input name="is_extra_33" 
													
													<?php
														$is_extra_33 = getFirstItem("
															select 
																meta_value 
															from 
																lawful_employees_meta 
															where 
																meta_for = 'is_extra_33' and meta_leid = '".$leid_row["le_id"]."' and meta_leid != ''");														
													
	
													
													?>
													
													 <?php if($is_extra_33){?>checked="checked"<?php }?>
													
													type="checkbox" value="1" 
													
													/>
													
													<?php /*echo "
															select 
																meta_value 
															from 
																lawful_employees_meta 
															where 
																meta_for = 'is_extra_33' and meta_leid = '".$leid_row["le_id"]."'";*/?>
													
                                                </td>
                                                
                                                 
                                            	
                                          
                                          </tr>
                                          
                                          
                                          <tr>
                                            <td colspan="4"><div align="center">
                                            	
												
												<?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $optoutma != 1){//yoes 20211108 64 opt-in ?>
												
													<input type="submit" value="<?php
													
														if($leid_row["le_id"]){
														
															echo "แก้ไขข้อมูล";
														
														}else{
															echo "เพิ่มข้อมูล";
														
														}
													
													?>"
														
														
														
														onClick="if(doValidateEmployeeInfo(this.form)){formSubmit();}else{return false;}";

														/> |
												
												
												<?php }else{?>
												
												
													<?php if($sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && !$case_closed){//exec can't do all these?>
													<input type="submit" name="button4" id="button4" value="<?php
													
														if($leid_row["le_id"]){
														
															echo "แก้ไขข้อมูล";
														
														}else{
															echo "เพิ่มข้อมูล";
														
														}
													
													?>" /> 
													<?php }?>
													
												<?php } //ends else swtich mode?>
                                                
                                                 <input name="" type="button" value="ปิดหน้าต่าง"  data-dismiss="modal" onCกกกlick="fadeOutMyPopup('my_popup'); return false;" />
                                                
												
												<?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2) && $optoutma != 1 && 1==0){//yoes 20211108 64 opt-in ?>
												
													
													<br>|
														<a style="font-weight: normal" 
															href="organization.php?id=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>&focus=lawful&optoutma=1"
															>(กำลังใช้งานหน้าจอ 33 ในแบบใหม่ - click ที่นี่เพื่อใช้หน้าจอ 33 แบบดั้งเดิม)</a>
													
												
												<?php 
												//yoes 20220210
												}elseif(($sess_accesslevel == 1 || $sess_accesslevel == 2) && 1==0){?>
													
													
													<br>|
														<a style="font-weight: normal" 
														href="organization.php?id=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>&focus=lawful&optinma=1"
														>(click ที่นี่เพื่อใช้หน้าจอ 33 แบบใหม่)</a>
													
												
												<?php } ?>
												
												
                                            </div></td>
                                          </tr>
                                          <input name="le_year" type="hidden" value="<?php echo $this_lawful_year;?>" />
                                          <input name="le_cid" type="hidden" value="<?php echo $this_id; ?>" />
                                          
                                          <input name="case_closed" type="hidden" value="<?php echo  default_value($leid_row["is_extra_row"], $case_closed); ?>" />
                                         
                                    </form>
                                    
                                      </table>
                                      
                                      
                                      <?php if($sess_accesslevel == 5 || $sess_accesslevel == 18 || $is_read_only){?>
                                      <div align="center">
                                      <table>
                                             <tr>
                                                <td colspan="2">
                                                
                                                <input name="" type="button" value="ปิดหน้าต่าง" data-dismiss="modal" onClick="fadeOutMyPopup('my_popup'); return false;" />
                                                
                                                </td>
                                            </tr>
                                        </table>
                                        </div>
                                        <?php }?>
                                      
                                      </td>
                                    </tr>
                                    
                                    </table>
									
									
									
									
									
									<?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && $optoutma != 1){//yoes 20211108 64 opt-in ?>
									
									
										<!-- table for "new 33" -->
										<div id="myTable_div">
									
									
										
										</div><!-- ends myTable_div -->
										
									
									<?php }else{ //normal mode?>
									
										 <table id="old_33_table"  bgcolor="#FFFFFF" width="1000" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;   ">
										
										<?php
						
							
											//YOES 20160615
											if($is_merged){
												$this_lawful_year += 1000;	
											}
											
											
							
											if($sess_accesslevel == 4){
												
												$get_org_sql = "SELECT *
																FROM 
																
																lawful_employees_company
																
																where
																	le_cid = '$this_id'
																	and le_year = '$this_lawful_year'
																order by le_id asc
																";
												
											}else{
							
												
												$get_org_sql = "
												
																SELECT 
																	a.*
																	
																	, b.meta_leid as child_meta_leid
																	, b.meta_for as child_meta_for
																	, b.meta_value as child_meta_value
																	
																	, c.meta_leid as parent_meta_leid
																	, c.meta_for as parent_meta_for
																	, c.meta_value as parent_meta_value
																	
																	, d.meta_for as sso_failed
																FROM 
																
																	lawful_employees a
																		left join
																			lawful_employees_meta b
																				on a.le_id = b.meta_leid and b.meta_for = 'child_of'
																		left join
																			lawful_employees_meta c
																				on a.le_id = c.meta_value and c.meta_for = 'child_of'
																				
																		left join
																			lawful_employees_meta d
																				on a.le_id = d.meta_leid and d.meta_for = 'sso_failed'
																
																
																where
																	le_cid = '$this_id'
																	and le_year = '$this_lawful_year'
																	
																order by 
																	le_id asc
																	
																";
												//echo $get_org_sql;
																
												//yoes 20160118 --> extra lawful_employees
												$get_org_sql_extra = "
												
																SELECT 
																	*
																	, '1' as is_extra_row
																FROM 
																
																	lawful_employees_extra
																
																
																where
																	le_cid = '$this_id'
																	and le_year = '$this_lawful_year'
																	
																order by le_id asc
																";
															
												
											}
											
											
											//echo $get_org_sql;										
											$org_result = array();
											array_push($org_result,mysql_query($get_org_sql));
											
											if($sess_accesslevel != 4){
												//yoes 20160118 -- non company see extra rows
												array_push($org_result,mysql_query($get_org_sql_extra));
											}
											
											
											//$total_records = 1;
											
											
											$post_row_parent_array = array();										
											$post_row_child_array = array();
											$post_row_array = array();	
											
											
											
											for($result_count = 0; $result_count < count($org_result); $result_count++){
											
												while ($post_row = mysql_fetch_array($org_result[$result_count])) {
																							
													//for parent -> push to parent
													if(!$post_row['child_meta_value']){
														array_push($post_row_parent_array,$post_row);
													}else{
														
														//for child -> push to child
														$post_row_child_array[$post_row['child_meta_leid']] = $post_row;
													
													}
													
													
																							
												
												} //end while $post row
											}//end for result count 
										
										
											
										
										//print_r($post_row_parent_array);
										//print_r($post_row_child_array);
										
										//yoes 20180119									
										//sort array by group
										for($result_count = 0; $result_count < count($post_row_parent_array); $result_count++){
											
											$group_count++; //group count for painting colors
											$post_row_parent_array[$result_count]['group_count'] = $group_count;
											array_push($post_row_array,$post_row_parent_array[$result_count]);
											
											$this_child = $post_row_parent_array[$result_count]['parent_meta_leid'];
											while($this_child){
												
												$post_row_child_array[$this_child]['group_count'] = $group_count;
												array_push($post_row_array,$post_row_child_array[$this_child]);
												$this_child = $post_row_child_array[$this_child]['parent_meta_leid'];
												
												
											}
											
										}
										
										
										$total_records = 1;
										
										
										
										for($result_count = 0; $result_count < count($post_row_array); $result_count++){
											
											$post_row = $post_row_array[$result_count];
											
											//include "organization_33_detailed_rows.php";
											include "organization_33_detailed_rows.php";
											
											$total_records++;
											
										}
										
										
										
										?>
										
										
										
										<?php 
										
										//print_r($post_row_array);
										
										
										?>
																				
										
										
										<?php if($total_records == 1 && $sess_accesslevel != 4 && $sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && !$case_closed){?>
										 <tr >
											<td colspan="10">
											
												<div align="center">
													<form method="post" action="scrp_import_last_lawful_employee.php"  onsubmit="return confirm('ต้องการนำเข้าข้อมูลคนพิการที่ได้รับเข้าทำงานจากปีที่แล้วมาใส่ในปีนี้?');">
														<input name="le_year" type="hidden" value="<?php echo $this_lawful_year;?>" />
														<input name="le_cid" type="hidden" value="<?php echo $this_id; ?>" />
														<input name="import_last_le" type="submit" value="นำเข้าข้อมูลจากปีที่แล้ว" />
													</form>  
												</div>
											</td>
										 </tr>
										<?php }?>
										
									  </table>
								  
								  <?php } //ends else{ //normal mode?>
                                  
                                  
</div>


<script>							
	
	 <?php
											
		//vars for JS
		$get33Table_row = array();
		$get33Table_row[this_id] = $this_id;
		$get33Table_row[this_lawful_year] = $this_lawful_year;
		$get33Table_row[is_read_only] = $is_read_only;
		$get33Table_row[leid] = $post_row[leid];
		$get33Table_row[meta_value] = $leid_row["meta_value"];
													
	?>
	

	function getLeidForm(leid){

		/*$.ajax({
		  method: "POST",
		  url: "organization_my_popup_ws_get_leidrow.php",
		  data: {leid: leid}
		})
		  .done(function( html ) {				

			le_input_form[le_name] = 'yoes';
		  });*/

		//alert('ssss');
		//json = JSON.stringify({leid: leid});
		axios
			.post('organization_my_popup_ws_get_leidrow.php', "leid=" + leid)//{ step: ""+what+"", the_id: ""+id+""}) "leid=" + leid
			.then(response => {

				var mm;		
				mm = response.data;																																																
				if(mm.le_name){
					//le_input_form["le_name"] = mm.le_name; 	//--> vue style
					$("#le_full_name").val(mm.le_name);			//--> jquery style
					setSelectedVal("#le_gender",mm.le_gender);
					$("#le_age").val(mm.le_age);
					$("#le_wage").val(mm.le_wage);
					setSelectedVal("#le_wage_unit",mm.le_wage_unit);
					setSelectedVal("#le_disable_desc_hire",mm.le_disable_desc);
					setSelectedVal("#le_education",mm.le_education);	
					
					if(isNaN(mm.le_position)){
						setSelectedVal("#le_position",23);	
						$("#le_position_other").val(mm.le_position);	
						$("#le_position_other").show();
					}else{
						setSelectedVal("#le_position",mm.le_position);	
						$("#le_position_other").hide();
					}
					
					//$("#le_33_parent").html(mm.le_33_parent);																	
					//yoes 20211103
					getLe33ParentDdl({"this_id":"<?php echo $this_id;?>","this_lawful_year":"<?php echo $this_lawful_year;?>","is_read_only":null,"leid":leid,"meta_value":mm.le_33_parent});
					setFileLink("#docfile_33_1_link",mm.docfile_33_1);
					setFileLink("#docfile_33_2_link",mm.docfile_33_2);
					setPersonalIdValue("#leid_",mm.le_code);
					setDateValue("#le_date",mm.le_start_date);
					setDateValue("#le_end_date",mm.le_end_date);
					setDateValue("#le_dob",mm.le_dob);

					// hidden val
					$("input[name=le_id]").val(mm.le_id);
					$("input[name=le_code]").val(mm.le_code);
					$("input[name=le_year]").val(mm.le_year);
					$("input[name=le_cid]").val(mm.le_cid);
					//$("input[name=case_closed]").val(mm.case_closed); ??
					
					if(mm.is_extra_33 == "1"){
						$("input[name=is_extra_33]").prop('checked', true);
					}else{
						$("input[name=is_extra_33]").prop('checked', false);
					}
					
					//Update some text title
					$("#txt_form_title").html('แก้ไขข้อมูลคนพิการที่ได้รับเข้าทำงาน');
					$("#button4").val("แก้ไขข้อมูล");
					
					//yoes 20211103 --> add parent leid
					//$("#le_parent_leid").val(438711);
					

				}

				//dang to do the test

			})


	}

function resetForm(){			
	$('#docfile_33_1_link').hide();
	$('#docfile_33_2_link').hide();
	$('#sso_result').hide();
	$("input[name=le_id]").val("");
	$('#le_33_parent').html('<option value="0" left_date="0000-00-00">-- ไม่ได้เป็นการรับแทน --</option>');													
	$("#le_form")[0].reset();

	//Update some text title
	$("#txt_form_title").html('* เพิ่มข้อมูล');
	$("#button4").val("เพิ่มข้อมูล");


	getLe33ParentDdl({"this_id":"<?php echo $this_id;?>","this_lawful_year":"<?php echo $this_lawful_year;?>","is_read_only":null,"leid":null,"meta_value":null});
	
	doGet34Table();

}

function setDateValue(objId,val){
	var dd = val.split("-");													
	setSelectedVal(objId+"_day",dd[2]);
	setSelectedVal(objId+"_month",dd[1]);
	setSelectedVal(objId+"_year",dd[0]);

}

function setPersonalIdValue(objId,val){
	var ids = val.split("");
	for(var i=0;i<13;i++)
		$(objId+(i+1).toString()).val(ids[i]);

}

function setSelectedVal(objId,val){													
	$(objId+" option[value='"+val+"']").prop('selected', true);
}

function setFileLink(objId,f){
	if(f){
		$(objId).html(f);
		$(objId).show();
	} else {
		$(objId).hide();
	}
}


function formSubmit(){
	var form = $("#le_form")[0];
	var formData = new FormData(form);
	event.preventDefault();
	$.ajax({
		type:'post',
		url: 'scrp_add_lawful_employee_ws.php',
		processData: false,
		contentType: false,
		data: formData,
		//beforeSend:function(){
		//	launchpreloader();
		//},
		//complete:function(){
		//	stopPreloader();
		//},
		success:function(result){
			
			
			get33Table(<?php echo json_encode($get33Table_row); ?>);
			resetForm();
		}
		/*done: function(msg){ 
			get33Table(<?php echo json_encode($get33Table_row); ?>);
			resetForm();
		}*/

	});
}

function updateListTable(leid,append=false){			
	if(append){
		$('#myTable tr:last').after('<tr class="bb" id="'+leid+'_main" bgcolor="#ffffff"><td></td></tr>');
	}

	axios
		.post('organization_my_popup_ws_get_leidrow.php', "leid=" + leid)
		.then(response => {
			var json = response.data;
			json.this_id =  <?php echo $this_id; ?>;
			json.this_lawful_year = <?php echo $this_lawful_year; ?>;
			json.this_lid= <?php echo $this_lid; ?>;
			getLeTds(leid,json,true);															
		});
}	

function deleteFile(id,curator_id,return_id)	{
	var r = confirm('คุณแน่ใจหรือว่าจะลบไฟล์แนบ? การลบไฟล์ถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบไฟล์ได้');
	if(r){
		$.ajax({
			type: 'get',
			url: 'scrp_delete_curator_file.php?id='+id+'&curator_id='+curator_id+'&return_id='+return_id,
			success:function(result){
				//updateListTable(curator_id);
				//get33Table(<?php echo json_encode($get33Table_row); ?>);
				get33Table(<?php echo json_encode($get33Table_row); ?>);
			}

		});
	} else {

	}
	return false;

}

function deletePerson(id,cid,year){
	var r = confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');
	if(r){
		$.ajax({
			type: 'get',
			url: 'scrp_delete_lawful_employee.php?id='+id+'&cid='+cid+'&year='+year,
			success:function(result){
				//$('#'+id+'_main').hide();															
				get33Table(<?php echo json_encode($get33Table_row); ?>);
				resetForm();
			}

		});
	} else {

	}
	return false;

}

	

</script>


<script id="vue_my_popup">

	//$("#myTable_div").empty();

	/*var my_popups = new Vue({
	  el: '#my_popup',
	  data: {
		xxyyzz: 0
		<?php echo $le_vue_data;?>
		  
		  }
	})*/
	
	function get33Table(json){
		//alert('asdasd');
		$.ajax({
		  method: "POST",
		  url: "https://job.dep.go.th/organization_my_popup_md_mytable.php",
		  data: json
		 
		})
		  .done(function( html ) {				
				//alert(html);
				$("#myTable_div").html(html);

		  });
	}
	
	
	get33Table(<?php echo json_encode($get33Table_row); ?>);
	
	function getFiltered33Table(){
		
		//alert('<?php echo json_encode($get33Table_row);?>');
		
		//const obj1 = JSON.parse(<?php echo json_encode($get33Table_row);?>);
		
		//alert(obj1);
		
		const obj1 = JSON.parse('<?php echo json_encode($get33Table_row);?>');
		const obj2 = JSON.parse('{"search_string":"'+$("#myTable_search").val()+'"}');		
		const mergedObj = Object.assign(obj1, obj2);
		const jsonStr = JSON.stringify(mergedObj);
		
		//alert(jsonStr);
		get33Table(mergedObj);
		//alert(jsonStr);
		
	}
	
	
	function getLe33ParentDdl(json){
		//alert('asdasd');
		$.ajax({
		  method: "POST",
		  url: "organization_my_popup_md_ddl_le_33_parent.php",
		  data: json
		 
		})
		  .done(function( data ) {	

				//alert(data);
				
				var min = JSON.parse(data);
				
				/*console.log(min[438717]);
				console.log(min[438717].the_value);*/
				
				var $el = $("#le_33_parent");
				$el.empty(); 
				$el.append($("<option></option>").attr("value", "0").text("-- ไม่ได้เป็นการรับแทน --"));
				
				$.each(min, function (i) {
					
					if(min[i].the_selected == "selected"){
						$el.append($("<option></option>").attr("value", min[i].the_value).attr("left_date", min[i].the_left_date).attr("selected", min[i].the_selected).text(min[i].the_text));
					}else{
						$el.append($("<option></option>").attr("value", min[i].the_value).attr("left_date", min[i].the_left_date).text(min[i].the_text));
					}
				});

		  });
	}
	
	//getLe33ParentDdl(<?php echo json_encode($get33Table_row); ?>);
	getLe33ParentDdl({"this_id":"<?php echo $this_id;?>","this_lawful_year":"<?php echo $this_lawful_year;?>","is_read_only":null,"leid":null,"meta_value":null});
	
	<?php //echo $le_vue_call;?>
	
	function getLeTds(id, json, isFade=false){
								
		$.ajax({
		  method: "POST",
		  url: "https://job.dep.go.th/organization_33_detailed_rows_modal.php",
		  data: json
		})
		  .done(function( html ) {				
			//alert(html);
			//my_popup["content_"+id] = "<tr><td>--"+html+"--</td></tr>";
			//$("#content_"+id).html("<tr><td>--"+html+"--</td></tr>");
			//$("#content_"+id).append(html);
			//$("#content_"+id).html("<tr><td>--ทดสอบ--</td></tr>");
			//$("#content_"+id).html("<tr><td>--"+html+"--</td></tr>");			
			if(isFade){
				// backup cell prop
				var rowColor = $("#"+id+"_main").attr('bgcolor');
				var rowNo = $("#"+id+"_main").find("td:eq(0)").html();				
				
				$("#"+id+"_main").replaceWith(html);

				// recover cell prop
				$("#"+id+"_main").attr('bgcolor',rowColor);
				$("#"+id+"_main").find("td:eq(0)").html(rowNo);

				$("#"+id+"_main").hide();
				$("#"+id+"_main").fadeIn("slow");
			} else  {
				$("#td_"+id).parent().replaceWith(html);
			}
			
			
			$("#import_previous_33").hide();

		  });
		
		//my_popup["content_"+id] = "<tr><td>"+id+"</td></tr>";

	}

	//$("#span_le_row_437642").html('<td colspan="13"><div align="center">...* กำลังดึงข้อมูล  *...</div></td>');

</script>