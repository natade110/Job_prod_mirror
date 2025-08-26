<?php if($_GET["ws"]){
	include "db_connect.php";
	include "scrp_config.php";
	include "session_handler.php";
	//include "functions.php";


	$lawful_values["LID"]=$_GET["lid"];
	$this_lid=$_GET["lid"];
	$this_lawful_year=$_GET["year"];
	
	

	

	}
	
	
	//yoes 20220418 -- move the thing here
	include_once("organization_35_detailed_rows_js.php");

?>

<?php if(!$_GET["ws"]){ ?>

	<div id="35_popup" class="modal bs-example-modal-lg-m35" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none; overflow: scroll;">


<?php } ?>  
 
 	
 
 	<form id="curator_form" name="curator_form"  method="post" enctype="multipart/form-data"  onSubmit="return validateCuratorForm(this);"><!--- curator information just get posted into this page-->
	
    <input type="hidden" value="0" id="curator_form_submitted" />
    
    <table  bgcolor="#FFFFFF" width="1000" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;  ">
    
    <tr>
    	<td>
        
        <div align="center">
        
        <table width="700">
        
        
        	<tr>
            	<td colspan="4">
                    <div style="font-weight: bold;color:#006600; padding-bottom:15px; " >
                    <?php //if($sess_accesslevel ==4){
                     if(1==1){?>
                        มาตรา 35 ให้สัมปทานฯ
                     <?php }else{ ?>
                        ปฏิบัติตามมาตรา 35
                     <?php } ?> </div> 
				</td>
                <td>
                	<div align="right">
						
					<a href="#"  data-dismiss="modal" aria-hidden="true">ปิดหน้าต่าง ×</a>
						
						
                    </div>
                </td>
            </tr>                     
        
        </table>
        
        
        <?php 
				if(is_numeric($_GET["curator_id"]) && !$_POST["do_cancel_edit"]){
		
					//pre-fill curator
					$is_edit_curator = 1;
					
					
					$popup_35_table_name = "curator";
					
					if($_GET[extra]){
						$popup_35_table_name = "curator_extra";
					}
										
					//yoes 20160218 --- extra condition for company
					if($sess_accesslevel == 4){
						$popup_35_table_name = "curator_company";
					}
					
					
					$curator_id_to_fill = $_GET["curator_id"];
					
					$the_sql = "select 
									* 
								from 
									$popup_35_table_name a
									
										left join curator_meta b
											on a.curator_id = b.meta_curator_id
											and
											meta_for = 'child_of' 
											
								where 
									curator_id = '$curator_id_to_fill' 
										
									";
									
					//echo $the_sql;
					
					
					$curator_row_to_fill = getFirstRow($the_sql);
					
					
					//yoes 20160120 ---> also get curator's usee
					$the_sql = "select * from $popup_35_table_name where curator_parent = '$curator_id_to_fill'";					
					$usee_row_to_fill = getFirstRow($the_sql);
					
					//echo $the_sql;
					
					//echo $curator_row_to_fill[0];
					
					if($curator_row_to_fill["curator_parent"] == 0){
						$is_curator_parent = 1;
					}
		
				}
		?>
        
         <div align="center">
        		 <table id="curator_input_forms" style="display: block;">
                        <tr bgcolor="#efefef">
                            <td colspan="10">
                                
                                <strong id="the_parent" style="display:none;">เพิ่มผู้ใช้สิทธิ</strong>        
                                <strong id="the_child" style="display:none;">เพิ่มผู้ถูกใช้สิทธิ</strong>        
                                
                                
                                </td>
                        </tr>
                        
                    <tr>
                          <td colspan="4" style="padding:5px; background-color: #efefef;">
                          
                           <span style="font-size: 16px; font-weight: bold;">
                          ข้อมูลผู้ใช้สิทธิ                        
                          
                            </span>
                          
                          </td>
                   </tr>
                    <tr>
                            <td>
                            
                                เลขที่บัตรประชาชน        </td>
                            <td colspan="3">
                            
                                <input type="text" name="curator_idcard___" id="curator_idcard____" maxlength="13"
                                
                                value="<?php echo $curator_row_to_fill["curator_idcard"];?>" style="display:none;"
                                
                                 />
                                 
                                 <?php 
								 	$id_form_name = "curator_form";
									$id_form_to_show = $curator_row_to_fill["curator_idcard"];
									
									//echo $id_form_to_show;
								 
								 ?>
                                 
                                 <?php include "txt_id_card.php";?> *
                                 
                                 
                                 
                                
                                <?php if($sess_accesslevel != 5 && !$is_read_only && $sess_accesslevel != 4 && !$case_closed){//company and exec can't do all these?>
	                                <input name="btn_get_curator_data" type="button" value="ดึงข้อมูล" 
                                    
                                    onclick="return validateCuratorPersonCode(document.getElementById('curator_form'));"
                                    />        
                                <?php }?>
                                
                                
                                <script>
								
									function validateCuratorPersonCode(frm) {
								
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
										
										doGet35Data();
									
										
									
										return true;
									
									}


                                    function updateDisabledPersonsTable(obj) {
                                        // If there are no children array or only 1 child, keep table hidden
                                        if (!obj.children || obj.children.length <= 1) {
                                            $("#disabled-persons-selector").hide();
                                            return;
                                        }

                                        // Show the table when we have multiple entries
                                        $("#disabled-persons-selector").show();

                                        // Clear existing rows
                                        $("#disabled_persons_list").empty();

                                        // Add each child as a row
                                        obj.children.forEach(function(person) {
                                            var tr = $("<tr>");

                                            // Add cells
                                            tr.append($("<td>").text(person.curator_idcard));
                                            tr.append($("<td>").text(person.curator_name));
                                            tr.append($("<td>").text(person.curator_gender === 'm' ? 'ชาย' : 'หญิง'));
                                            tr.append($("<td>").text(person.curator_age));
                                            tr.append($("<td>").text(person.curator_disable_desc));

                                            // Add radio button cell
                                            var radioTd = $("<td>").attr('align', 'center').append(
                                                $("<input>")
                                                    .attr('type', 'radio')
                                                    .attr('name', 'select_disabled')
                                                    .attr('value', person.curator_idcard)
                                                    .attr('onclick', 'selectDisabledPerson(this)')
                                            );
                                            tr.append(radioTd);

                                            $("#disabled_persons_list").append(tr);
                                        });
                                    }
								
								
									function doGet35Data(){
										
										var the_id = "";										
										<?php for($i=1;$i<=13;$i++){?>											
											the_id = the_id + ($('#id_<?php echo $i;?>').val());											
										<?php }?>
									
										//alert(the_id);
										$.ajax({
											url: 'ajax_get_curator_new.php',
											type: 'GET',
											data: { the_id: the_id} ,
											contentType: 'application/json; charset=utf-8',
											success: function (response) {
												//your success code
												//alert(response);
												if(response == "no_result"){
													alert("ไม่พบข้อมูลผู้ใช้สิทธิ");		
												}else{
													//alert(response);													
													var obj = jQuery.parseJSON( response );
													//alert( obj.curator_name );
													
													$('#curator_name').val(obj.curator_name);
													$('#curator_gender').val(obj.curator_gender);
													$('#curator_age').val(obj.curator_age);

                                                    // Only show disabled persons selector if there are children
                                                    $("#disabled-persons-selector").hide(); // Hide by default
                                                    if(obj.hasOwnProperty('children') && obj.children && obj.children.length > 0) {
                                                        updateDisabledPersonsTable(obj);
                                                    }

													if(obj.curator_is_disable == 1){
														$('#r2').prop("checked", true);
													}else{
														$('#r1').prop("checked", true);
													}
													
													doToggleCuratorDisabled();
													
													$('#le_disable_descconc').val(obj.curator_disable_desc);
													
													//now do child
													
													
													<?php for($i=1;$i<=13;$i++){?>											
													$('#useeid_<?php echo $i?>').val(obj.child_curator_idcard.substr(<?php echo $i-1?>,1));													
													<?php }?>
													$('#usee_name').val(obj.child_curator_name);
													$('#usee_gender').val(obj.child_curator_gender);
													$('#usee_age').val(obj.child_curator_age);
													$('#le_disable_descusee').val(obj.child_curator_disable_desc);
													
													//more
													$('#curator_event_desc').val(obj.curator_event_desc);
													$('#curator_value').val(obj.curator_value);
													
												}
											},
											error: function () {
												//your error code
												alert("เกิดการผิดพลาดในการดึงข้อมูล");
											}
										});
										
									}
								
								</script>
                                
                                
                                 <?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && !$is_read_only && !$case_closed){ ?>
                                            <input id="btn_get_doe_org" type="button" value="ดึงข้อมูลสัญญาของสถานประกอบการ" onClick="return doGetDOEOrgData();" />
                                            <script>
											
												function doGetDOEOrgData(){
													
													
												
													$.ajax({
													  method: "POST",
													  url: "http://203.154.94.108/ajax_get_doe_org.php",
													  data: { name: "John", location: "Boston" }
													})
													  .done(function( html ) {
														//alert( "Data Saved: " + msg );
														$( "#doe_org_result" ).html( html);
													  });
													
												}
												
											</script>
                                            <?php }?>  
                                            
                                   <span id="doe_org_result">
                                   
                                   </span>
                                
                                        
                                </td>
                           
                        </tr>
                        
                        
                        	 <td>
                            
                                ชื่อ-นามสกุล        </td>
                            <td>
                            
                                <input type="text" name="curator_name" id="curator_name" 
                                
                                 value="<?php echo $curator_row_to_fill["curator_name"];?>"
                                
                                />    *    </td>
                        
                        <tr>
                        
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
                                  </select>    *    </td>
                            
                        </tr>
						
						
						<tr>
                            <td>
                            
								อายุ        
							</td>
                            <td>
                            
                                <input name="curator_age" type="text" id="curator_age" size="10" maxlength="2"  value="<?php echo $curator_row_to_fill["curator_age"];?>" />
							</td>
                           
						
                        </tr>
                        
                        
                        
                         <tr>
                            <td>
                            
                                <span id="curator_is_disable_text">ผู้ใช้สิทธิเป็น</span>
                                
                                </td>
                            <td colspan="3">
                            <div id="curator_is_disable">
                                <input id="r1" name="curator_is_disable" type="radio" value="0" onClick="doToggleCuratorDisabled();" checked="checked" 
                                
                                <?php if($curator_row_to_fill["curator_is_disable"] == "0"){echo 'checked="checked"';}?>
                                
                                
                                /> ผู้ดูแลคนพิการ
                                   
                                <input id="r2" name="curator_is_disable" type="radio" value="1" onClick="doToggleCuratorDisabled();"
                                
                                <?php if($curator_row_to_fill["curator_is_disable"] == "1"){?>
                                checked="checked"
                                <?php }?>
                                
                                /> คนพิการ
                                
                                
                            </div>
                            </td>
                           
                        </tr>
                        
                         <tr id="tr_curator_disable">
                          <td valign="top">ลักษณะความพิการ</td>
                          <td colspan="3"><?php 
						  	
							$do_hide_blank_dis = 1; 
							
							$dis_type_suffix = "conc";
							
							include "ddl_disable_type.php";
							
							$dis_type_suffix = "";
							
							?></td>
                        </tr>
                        
                       
                         <tr>
                            <td>
                            
                            	<span id="curator_start_date_text">เลขที่สัญญา</span>
                                
                                </td>
                            <td colspan="3">
                            
                             <input type="text" name="curator_contract_number" id="curator_contract_number" 
                                
                                 value="<?php echo $curator_row_to_fill["curator_contract_number"];?>"
                                
                                />
                                
                                
                                <?php if(($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3) && !$is_read_only && !$case_closed){ ?>
                                            <input id="btn_get_doe" type="button" value="ดึงข้อมูลสัญญาของผู้ใช้สิทธิ" onClick="return doGetDOEData();" />
                                            <script>
											
												function doGetDOEData(){
													
													var the_id = "";										
													<?php for($i=1;$i<=13;$i++){?>											
														the_id = the_id + ($('#id_<?php echo $i;?>').val());											
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
													 document.getElementById('id_1').focus();
													 return (false);
												   }
													
													
													if(the_id.length != 13)
													{
														alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
														document.getElementById('id_1').focus();
														return (false);
													}
												
													$.ajax({
													  method: "POST",
													  url: "http://203.154.94.108/ajax_get_doe.php",
													  data: { name: "John", location: "Boston" }
													})
													  .done(function( html ) {
														//alert( "Data Saved: " + msg );
														$( "#doe_result" ).html( html);
													  });
													
												}
												
											</script>
                                            <?php }?>  
                                            
                                   <span id="doe_result">
                                   
                                   </span>
                            
                        	 </td>
							 
						</tr>
                        
                        <tr>
                            <td>
                            
                            	<span id="curator_start_date_text">วันเริ่มต้นสัญญา</span>
                                
                                </td>
                            <td>
                            <?php
											   
							   $selector_name = "curator_start_date";
							   $this_date_time = $curator_row_to_fill["curator_start_date"];
							   
							   include ("date_selector.php");
							   
							   ?> *
                                </td>
                            <td>
                            	<span id="curator_end_date_text">
                            	วันสิ้นสุดสัญญา</span>
                            </td>
                            <td>
                            
                            	 <?php
											   
							   $selector_name = "curator_end_date";
							   $this_date_time = $curator_row_to_fill["curator_end_date"];
							   
							   include ("date_selector_plus_ten.php");
							   
							   //reset this_date_time just in case
							   $this_date_time = "0000-00-00";
							   
							   ?> *
                            
                              </td>
                        </tr>
                        
						
						<?php if($this_lawful_year >= 2018 && $this_lawful_year <= 2050){ ?>
										  <tr>
											<td>เป็นการทำสัมปทานแทน...</td>
											<td colspan="3">
											
												<select id="le_35_parent" name="le_35_parent" onChange='doAlertSub35();'>
													<option value='0' left_date='0000-00-00'>-- ไม่ได้เป็นการรับแทน --</option>
													
													<?php
													
														//select 35 of this company that has left and not a parent of any other 35 yet
														$sub_35_sql = "
														
															select
																*
															from
																curator
															where
																curator_end_date != '0000-00-00'
																and
																curator_end_date < '$this_lawful_year-12-31'
																and 
																curator_lid = '$this_lid'																																
																and
																curator_id != '".($_GET["leid"]*1)."'
																
																and
																(
																	curator_id not in (
																	
																		select
																			meta_value
																		from
																			curator_meta
																		where
																			meta_for = 'child_of'
																			
																	
																	)
																	or
																	curator_id in (
																	
																		select
																			meta_value
																		from
																			curator_meta
																		where
																			meta_for = 'child_of'
																			and
																			meta_curator_id = '".($_GET["curator_id"]*1)."'
																	
																	)
																)
																
																and
																curator_id != '".($_GET["curator_id"]*1)."'

														
														";
														
														
														
														$sub_35_result = mysql_query($sub_35_sql);
																											
														
														while($sub_35_row = mysql_fetch_array($sub_35_result)){
														
														
														?>
														
														<option left_date='<?php echo $sub_35_row['curator_end_date'];?>' value='<?php echo $sub_35_row['curator_id']?>' <?php if($curator_row_to_fill["meta_value"] == $sub_35_row['curator_id']){echo "selected=selected";}?>>
															<?php echo $sub_35_row['curator_code']." : ". $sub_35_row['curator_name']." : สัมปทานวันที่ ".formatDateThaiShort($sub_35_row['curator_start_date'],0)." ถึง ".formatDateThaiShort($sub_35_row['curator_end_date'],0)?>
														</option>
														
														<?php
															
														}
														
														
													
													?>													
													
													
													
												</select>
												
												<?php //echo $sub_35_sql;?>
												
												<script>
												
												function doAlertSub35(){
													
												}
												
												
												function doValidateSub35(){
													
												}
												
												</script>
												
											
											</td>
										  </tr>
										  <?php } //ends if($this_lawful_year >= 2018 && $this_lawful_year <= 2050){ ?>
						
                        
                        <tr id="tr_curator_event" >
                          <td valign="top">กิจกรรมตามมาตรา 35</td>
                          <td >
                          
                          	<?php 
							
							include "ddl_curator_event.php";
							
							?>                                                    </td>
                                                    
                            
                            <td><div align="right">มูลค่า</div></td>
                            <td><input name="curator_value" id="curator_value" style="text-align:right;"  type="text" size="10" 
                            
                             value="<?php echo formatMoney($curator_row_to_fill["curator_value"]);?>"
                            
                            onChange="addCommas('curator_value');"/> บาท </td>
                        </tr>
                        
                        <tr id="tr_curator_event_2"  >
                          <td valign="top">รายละเอียด</td>
                          <td colspan="3">
                          
                          	
                            <textarea name="curator_event_desc" id="curator_event_desc" cols="40" rows="4"><?php echo $curator_row_to_fill["curator_event_desc"];?></textarea>                          </td>
                        </tr>
                        
						<?php if($this_lawful_year >= 2022 && $this_lawful_year < 2100){?>
						
						
						<?php }else{ ?>
                        <tr id="tr_curator_docfile">

                         <td valign="top">สำเนาหนังสือแจ้งขอใช้สิทธิ</td>
                          <td colspan="3">
                          
                          
                           <?php 
						  
						  		//echo $curator_row_to_fill["curator_id"];
						  
								$this_id_temp = $this_id;
								$this_id = $curator_row_to_fill["curator_id"]; 														
								$file_type = "curator_docfile";
								include "doc_file_links.php";
								$this_id = $this_id_temp;
							 
							?>
                            
							<input type="file" name="curator_docfile" id="curator_docfile" />
                            </td>
                        
                        </tr>
						<?php }?>
                          
                        <tr id="tr_curator_docfile_2">
                         <td valign="top">สำเนาหนังสือแจ้งผลการดำเนินการ <br />(หากไม่นำมา ถือว่ายังไม่ปฏิบัติตามกฎหมาย)</td>
                          <td colspan="3">
                          	
                            <?php 
						  
								$this_id_temp = $this_id;
								$this_id = $curator_row_to_fill["curator_id"]; 														
								$file_type = "curator_docfile_2";
								include "doc_file_links.php";
								$this_id = $this_id_temp;
							 
							?>
                          
							<input type="file" name="curator_docfile_2" id="curator_docfile_2" />
                            </td>
                        
                        </tr>
                          
                        <tr id="tr_curator_docfile_3">
                         <td valign="top">
						 
						 <?php if($this_lawful_year >= 2022 && $this_lawful_year < 2100){?>
						
							สำเนาบัตรประจำตัวคนพิการ
						<?php }else{ ?>
							สำเนาสัญญาสัมปทาน<br />
							และสำเนาบัตรประจำตัวคนพิการ
						<?php }?>
						 
						 </td>
                          <td colspan="3">
                          	
                            <?php 
						  
								$this_id_temp = $this_id;
								$this_id = $curator_row_to_fill["curator_id"]; 														
								$file_type = "curator_docfile_3";
								include "doc_file_links.php";
								$this_id = $this_id_temp;
							 
							?>
                          
							<input type="file" name="curator_docfile_3" id="curator_docfile_3" />
                            </td>
                        
                        </tr>
						
						
						 <tr id="tr_curator_docfile_3">
                         <td valign="top">เป็นการทำ ม35 เกินอัตราส่วน</td>
                          <td colspan="3">
                          	
							<input name="is_extra_35" 
													
													<?php
														$is_extra_35 = getFirstItem("
															select 
																meta_value 
															from 
																curator_meta 
															where 
																meta_for = 'is_extra_35' and meta_curator_id = '".$curator_row_to_fill["curator_id"]."'");														
													
	
													
													?>
													
													 <?php if($is_extra_35){?>checked="checked"<?php }?>
													
													type="checkbox" value="1" 
													
													/>
                           
                           </td>
                        
                        </tr>
                        
                        
                        <?php 
						
						//yoes 20160120 subtable for ผู้ถูกใช้สิทธิ
						
						?>
                        
                       
                        <tr  id="usee_row_1">
                          <td colspan="4" valign="top" style="padding:5px; background-color: #efefef;">
                            <span style="font-size: 16px; font-weight: bold; ">
                          ข้อมูลผู้ถูกใช้สิทธิ
                          <?php //echo $output_values["CID"] . $this_lid;?>
                            </span>
                          </td>
                        </tr>

                     <?php if( $this_lid ==  2050636193 || 1==1){?>

                     <tr id="disabled-persons-selector">
                         <td colspan="4">
                             <div style="margin: 10px 0;">


                                 <table width="100%" border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
                                     <thead>
                                     <tr bgcolor="#f5f5f5">
                                         <th>เลขบัตรประชาชน</th>
                                         <th>ชื่อ-นามสกุล</th>
                                         <th>เพศ</th>
                                         <th>อายุ</th>
                                         <th>ลักษณะความพิการ</th>
                                         <th width="80">เลือก</th>
                                     </tr>
                                     </thead>
                                     <tbody id="disabled_persons_list">
                                     <tr>
                                         <td>1234567890123</td>
                                         <td>นายสมชาย ใจดี</td>
                                         <td>ชาย</td>
                                         <td>35</td>
                                         <td>ความพิการทางการเห็น</td>
                                         <td align="center">
                                             <input type="radio" name="select_disabled" value="1" onclick="selectDisabledPerson(this)"/>
                                         </td>
                                     </tr>
                                     <tr>
                                         <td>9876543210987</td>
                                         <td>นางสาวสมหญิง รักดี</td>
                                         <td>หญิง</td>
                                         <td>28</td>
                                         <td>ความพิการทางการได้ยินหรือสื่อความหมาย</td>
                                         <td align="center">
                                             <input type="radio" name="select_disabled" value="2" onclick="selectDisabledPerson(this)"/>
                                         </td>
                                     </tr>
                                     </tbody>
                                 </table>
                             </div>

                             <script>
                                 function selectDisabledPerson(radio) {
                                     var $row = $(radio).closest('tr');
                                     var cells = $row.find('td');

                                     // Auto-fill the form fields with selected person's data
                                     $('#usee_name').val($(cells[1]).text());

                                     // Set gender
                                     var gender = $(cells[2]).text();
                                     $('#usee_gender').val(gender === 'ชาย' ? 'm' : 'f');

                                     // Set age
                                     $('#usee_age').val($(cells[3]).text());

                                     // Set disability type
                                     $('#le_disable_descusee').val($(cells[4]).text());

                                     // Loop through ID card number inputs and fill them
                                     var idCard = $(cells[0]).text();
                                     for(var i = 1; i <= 13; i++) {
                                         $('#useeid_' + i).val(idCard.charAt(i-1));
                                     }
                                 }
                             </script>
                         </td>
                     </tr>

                     <?php } //if( $this_cid == 71462){?>

                     <script>
                         $(document).ready(function() {
                             // Initially hide the selector table
                             $("#disabled-persons-selector").hide();
                         });
                     </script>


                        <tr  id="usee_row_2">
                          <td valign="top">เลขที่บัตรประชาชน</td>
                          <td colspan="3">
                          <?php 
								$id_form_name = "curator_form";
								$id_form_to_show = $usee_row_to_fill["curator_idcard"];
								
								$txt_id_card_prefix = "usee";
								
								include "txt_id_card.php";
								
								$txt_id_card_prefix = "";
							?> *
                          </td>
                        </tr>
                        <tr  id="usee_row_3">
                          <td valign="top">ชื่อ-นามสกุล</td>
                          <td colspan="3">
                          <input type="text" name="usee_name" id="usee_name" 
                                
                                 value="<?php echo $usee_row_to_fill["curator_name"];?>"
                                
                                />
                           *
                          </td>
                        </tr>
                        <tr  id="usee_row_4">
                          <td valign="top">เพศ</td>
                          <td>
                          <select name="usee_gender" id="usee_gender">
                                    <option value="m" 
                                    
                                    <?php if($usee_row_to_fill["curator_gender"] == "m"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >ชาย</option>
                                    <option value="f"
                                    
                                     <?php if($usee_row_to_fill["curator_gender"] == "f"){?>
                                    selected="selected"
                                    <?php }?>
                                    
                                    >หญิง</option>
                                  </select> 
                                *
                          </td>
                          <td>อายุ</td>
                          <td>
                          <input name="usee_age" type="text" id="usee_age" size="10"  value="<?php echo $usee_row_to_fill["curator_age"];?>" />
                          </td>
                        </tr>
                        <tr  id="usee_row_5">
                          <td valign="top">ลักษณะความพิการ</td>
                          <td colspan="3">
                          <?php 
						  	
							$do_hide_blank_dis = 1; 
							$dis_type_suffix = "usee";
							$selected_value = $usee_row_to_fill["curator_disable_desc"];
							
							include "ddl_disable_type.php";
							$dis_type_suffix = "";
							$selected_value = "";
							?>
                            
                            </td>
                        </tr>
                        </div>
                        
                         
                        <?php if(!$_GET["ws"]) {  ?>
                        <script>
							//hide these rows if not used
							$( "#usee_row_1" ).toggle();
							$( "#usee_row_2" ).toggle();
							$( "#usee_row_3" ).toggle();
							$( "#usee_row_4" ).toggle();
							$( "#usee_row_5" ).toggle();
						
						</script>
                        <?php }  ?>
                        
                        
                        
                        <?php 
						//yoes 20160120 subtable for ผู้ถูกใช้สิทธิ
						
						?>
                        
                        
                        
                        
                        
                       
                        
                        
                        <tr>
                            <td colspan="4">
                                <div align="center">
                                
                                
                                <input name="year_curator" type="hidden" value="<?php echo $this_lawful_year;?>" />
                               
                                <input name="case_closed" type="hidden" value="<?php echo  default_value($case_closed, $case_closed); ?>" />
                                 
                                 
                                 <?php if($sess_accesslevel != 5 && !$is_read_only ){//exec can't do all these?>
                                 
                                 	 <?php if($is_edit_curator){?>


		                               <input name="do_add_curator" type="submit" value="แก้ไขข้อมูล" />
                                       <!---<input name="do_cancel_edit" type="submit" value="ยกเลิกการแก้ไข" />--->
									   <input name="" type="button" value="ยกเลิกการแก้ไข" data-dismiss="modal" aria-hidden="true" <?php /*//yoes 20220418*/if(1 == 1){?>onClick="getCuratorForm(-1);"<?php }?>  />
                                       
									   <?php if(!$_GET["ws"]) { ?>
                                       <script>
												$(".bs-example-modal-lg-m35").modal('toggle');
											
									   </script>
									   <?php } ?>
                                       
                                     <?php }else{ ?>
                                     
                                     	<input name="do_add_curator" type="submit" value="บันทึก" style="font-size:18px; font-weight: bold;" 
                                        
                                        />
                                     <?php }?>  
                                       
                                <?php }?>
                                
                                
                                 <?php if(!$is_edit_curator){?>
                                
								
									<input name="" type="button" value="ปิดหน้าต่าง" data-dismiss="modal" aria-hidden="true"  />
									
                                <?php }?>
                                 
                                 <?php if($is_edit_curator){?>
                                 
                                  <input name="curator_id" type="hidden" value="<?php echo $curator_row_to_fill["curator_id"];?>" />
                                  <input name="usee_curator_id" type="hidden" value="<?php echo $usee_row_to_fill["curator_id"];?>" />
                                  
                                 <?php }?>
                                 
                                 <input name="curator_lid" type="hidden" value="<?php echo $lawful_values["LID"]; ?>" />
                                 
                                  
                                   <?php if($is_edit_curator){?>
                                  <input name="curator_parent" id="curator_parent" type="hidden" value="<?php echo $curator_row_to_fill["curator_parent"];?>" />
                                  <?php }else{?>
                                  <input name="curator_parent" id="curator_parent" type="hidden" value="0" />
                                  <?php }?>
                                  
                                  
                                </div>                            </td>
                        </tr>
                      </table>
        </div>
      
        
        
        
        
        </div>
        
        
        
       
        
        
        
        </td>
	</tr>
            
    
   
	</table>
    </form>

	<script>doToggleCuratorDisabled();</script>

	<?php if($_GET["ws"]) exit; ?>	
	
		</div>
    
   
                       
    
    <script language='javascript'>
						
						
						function moomin(){
							alert("moomin");
							return false;	
						}
						
						
						function validateCuratorForm(frm) {
							
							
							//alert('what'); return false;
							
							if($("#curator_form_submitted").val() == 1){
								return false;	
							}
							
							
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
							
							
							var e = document.getElementById("curator_start_date_year");
							
							
							strValue = e.options[e.selectedIndex].value;
							
							var e = document.getElementById("curator_start_date_month");
							
							strValue = strValue + "-" + e.options[e.selectedIndex].value;
							
							var e = document.getElementById("curator_start_date_day");
							
							strValue = strValue + "-" + e.options[e.selectedIndex].value;
							
							var curator_start_date = strValue;
							
							//
							if(frm.curator_value.value.length < 1 || frm.curator_value.value == "0.00")
							{
								alert("กรุณาใส่ข้อมูล: มูลค่า");
								frm.curator_value.focus();
								return (false);
							}
							
							
							var checkOK = "1234567890.,";
							
							//check number a hardway
							var checkStr = frm.curator_value.value;
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
							 alert("มูลค่าต้องเป็นตัวเลขเท่านั้น");
							 frm.curator_value.focus();
							 return (false);
						   }
							
							
							//yoes 20160120 do extra validation if this one is ผู้ดูแลคนพิการ
							if(document.getElementById('r1').checked){
								
									//alert("was?");
								
									var checkOK = "1234567890";
									
									<?php for($i=1;$i<=13;$i++){?>
									if(frm.useeid_<?php echo $i;?>.value.length < 1)
									{
										alert("กรุณาใส่ข้อมูล: เลขที่บัตรประชาชน");
										frm.useeid_<?php echo $i;?>.focus();
										return (false);
									}
									
									var checkStr = frm.useeid_<?php echo $i;?>.value;
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
									 frm.useeid_<?php echo $i;?>.focus();
									 return (false);
								   }
									<?php }?>
									
									
									
									if(frm.usee_name.value.length < 1)
									{
										alert("กรุณาใส่ข้อมูล: ชื่อ-นามสกุล");
										frm.usee_name.focus();
										return (false);
									}
								
							}
							
							//---yoes 20181107 --> add more validation
							//----

							//yoes 20180220 -> validate parent/child
							var curator_parent_end_date = $("#le_35_parent option:selected").attr("left_date");

							//alert(curator_parent_end_date); 
							//alert(curator_start_date); 
							
							//return(false);


							if(curator_parent_end_date != '0000-00-00'){
								 
								//alert("moomin");  
								//alert(curator_start_date > curator_parent_end_date); 
								if(curator_start_date <= curator_parent_end_date){
									
									alert("วันที่เริ่มสัมปทาน เป็นวันก่อนที่สัมปทานเดิมสิ้นสุด");
									frm.le_35_parent.focus();
									return(false);
									
								}
								
							}
							
							
							
							
							//----							
							$(".bs-example-modal-lg-m35").modal('toggle');
							setSpinner('organization_35_details_table_64_div');
							$("#curator_form_submitted").val(1);
							curatorFormSubmit();
							
							<?php /*//yoes 20220418*/if(1 == 1){?>getCuratorForm(-1);<?php }?>
							
							return(false);

							$("#curator_form_submitted").val(1);
							
							return(true);									
						
						}
						
		function updateCuratorList() {
			var param = window.location.href.split("?")[1];
			var lid = $("input[name=the_lid]").val();
			var year = $("input[name=the_year]").val();
			axios
				.get('organization_35_details_table_64.php?'+param+'&lid='+lid+'&ws=1&search_string='+encodeURI($('#myTable35_search').val()))
				.then(response => {
					$("#organization_35_details_table_64_div").html(response.data);
					
				});

		}				
		
		function curatorFormSubmit(){
			var curator_id = $("input[name=curator_id]").val();
			var form = $("#curator_form")[0];
			var formData = new FormData(form);
			formData.append("do_add_curator","Submit from Ajax");
			event.preventDefault();
			$.ajax({
				type:'post',
				url: window.location.href+'&curator_id='+curator_id,
				processData: false,
				contentType: false,
				data: formData,
				success:function(result){
					updateCuratorList();
				}

			});
		}	
		
		function deleteCurator(id,year,isExtra=false){
			var isConfirm = confirm('คุณแน่ใจหรือว่าจะลบข้อมูลผู้ใช้สิทธิ? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');
			var myUrl = 'scrp_delete_curator_new.php?id='+id+'&year='+year 
			if(isExtra) url = url+'&extra=1';
			if(isConfirm){
				$.ajax({
				type:'get',
				url: myUrl,
				success:function(result){
					updateCuratorList();
					}

				});	
				
			}
			return false;

		}

		function deleteCuratorFile(id,return_id){
			var isConfirm = confirm('คุณแน่ใจหรือว่าจะลบไฟล์แนบ? การลบไฟล์ถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบไฟล์ได้');
			if(isConfirm){
				$.ajax({
				type:'get',
				url: 'scrp_delete_curator_file.php?id='+id+'&return_id='+return_id,
				success:function(result){
					updateCuratorList();
					}

				});							
			}
			return false;

		}		

		function doToggleCuratorDisabled(){
			if(document.getElementById('r1').checked){
				document.getElementById('tr_curator_disable').style.display = 'none';
				
				document.getElementById('usee_row_1').style.display = '';
				document.getElementById('usee_row_2').style.display = '';
				document.getElementById('usee_row_3').style.display = '';
				document.getElementById('usee_row_4').style.display = '';
				document.getElementById('usee_row_5').style.display = '';
				
			}
			if(document.getElementById('r2').checked){
				document.getElementById('tr_curator_disable').style.display = '';
				
				document.getElementById('usee_row_1').style.display = 'none';
				document.getElementById('usee_row_2').style.display = 'none';
				document.getElementById('usee_row_3').style.display = 'none';
				document.getElementById('usee_row_4').style.display = 'none';
				document.getElementById('usee_row_5').style.display = 'none';
			}
			
			
		}
		
		doToggleCuratorDisabled();
					
		</script>