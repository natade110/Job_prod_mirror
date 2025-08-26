<?php

	include "db_connect.php";
	include "scrp_config.php";
	include "session_handler.php";
	
		
	//current mode
	if($_GET["mode"] == "new"){
		$mode = "new";	
	}elseif(is_numeric($_GET["id"])){
		$mode = "edit";
		$this_id = $_GET["id"];
		$this_focus = $_GET["focus"];
		$post_row = getFirstRow("select * 
								from 
									company
								where 
									cid  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						'CID'
						,'Employees'
						,'CompanyCode'
						,'CompanyNameThai'
						,'CompanyNameEng'
						,'Address1'
						
						,'Moo'
						,'Soi'
						,'Road'
						,'Subdistrict'
						,'District'
						
						,'Province'
						,'Zip'
						,'Telephone'
						,'email'
						,'TaxID'
						
						,'CompanyTypeCode'
						,'BusinessTypeCode'
						,'BranchCode'
						,'org_website'
						
						,'LawfulFlag'
						,'Status'
						
						,'NoRecipient'
						,'NoRecipient_remark'
						
						,'ContactPerson1'
						,'ContactPhone1'
						,'ContactEmail1'
						,'ContactPosition1'
						,'ContactPerson2'
						,'ContactPhone2'
						,'ContactEmail2'
						,'ContactPosition2'
						
						);
				
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			$output_values[$output_fields[$i]] .= (doCleanOutput($post_row[$output_fields[$i]]));
		}
				
		
		
	}else{
		header("location: index.php");
	}	





///curator thingie

if($_POST["do_add_curator"]){

	$curator_name = doCleanInput($_POST["curator_name"]);
	$curator_idcard = doCleanInput($_POST["curator_idcard"]);
	$curator_gender = doCleanInput($_POST["curator_gender"]);
	$curator_age = doCleanInput($_POST["curator_age"]);
	$curator_lid = doCleanInput($_POST["curator_lid"]);
	$curator_parent = doCleanInput($_POST["curator_parent"]);
	
	$curator_event = doCleanInput($_POST["curator_event"]);
	$curator_event_desc = doCleanInput($_POST["curator_event_desc"]);

	$curator_disable_desc = doCleanInput($_POST["le_disable_desc"]);	

	$sql = "
			insert into 
				curator(
				
					curator_name
					,curator_idcard
					,curator_gender
					,curator_age
					,curator_lid
					,curator_parent
					
					,curator_event
					,curator_event_desc
					,curator_disable_desc
					
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
				
				)
				
			";
			
	//echo $sql;
	
	$carate = "curate";

	mysql_query($sql) or die(mysql_error());

}







?>
<?php include "header_html.php";?>
<?php include "global.js.php";?>
              <td valign="top">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 0px 0;"  >
                    <?php if($mode == "new"){ ?>
					เพิ่มข้อมูลสถานประกอบการ
                    <?php }elseif($mode == "edit"){ ?>
                    การจ้างงานคนพิการของสถานประกอบการ: <font color="#006699"><?php $company_name_to_use = formatCompanyName($output_values["CompanyNameThai"],$output_values["CompanyTypeCode"]); echo $company_name_to_use;?>
                    <?php } ?>
                    </font></h2>
                    
                    <?php if(strpos($this_page, "mode=new")){ ?>
                    
                    <?php }else{ ?>
                     <div style="padding:5px 0 10px 2px"><a href="org_list.php">รายชื่อสถานประกอบการ</a> > <?php echo $company_name_to_use;?></div>
                     <?php }?>
                    
                <table width="100%" >
                        <tr>
                        <td class="td_bordered">
              <table cellspacing="0">
                                <tr>
                                  
                                  <?php if($mode != "new"){ ?>
                                  <td><a href="#general" onclick="showTab('general'); return false;">
                                  <div id="tab_general_black" class="white_on_black" style="width:120px;" align="center">ข้อมูลทั่วไป/ที่อยู่</div>
                                  <div id="tab_general_grey" class="white_on_grey" style="width:120px; display:none;" align="center">ข้อมูลทั่วไป/ที่อยู่</div>
                                  </a></td>
                                  <td>
									  <?php if($sess_accesslevel != 4){?>
                                          <a href="#official" onclick="showTab('official'); return false;">
                                          <div id="tab_official_black" class="white_on_black" style="width:150px; display:none;" align="center">จดหมายแจ้ง</div>
                                          <div id="tab_official_grey" class="white_on_grey" style="width:150px; " align="center">จดหมายแจ้ง</div>
                                          </a>
                                      <?php }?>
                                  </td>
                                  <td>
                                  <a href="#lawful" onclick="showTab('lawful'); return false;">
                                  <div id="tab_lawful_black" class="white_on_black" style="width:160px; display:none;" align="center">การปฏิบัติตามกฎหมาย</div>
                                  <div id="tab_lawful_grey" class="white_on_grey" style="width:160px; " align="center">การปฏิบัติตามกฎหมาย</div>
                                  </a></td>
                                  <?php }else{?>
                                  <td><a href="#" >
                                  <div id="tab_general_black" class="white_on_black" style="width:120px;" align="center">ข้อมูลทั่วไป/ที่อยู่</div>
                                  </a></td>
                                  <?php }?>
                                </tr>
                          </table>
                         </td>
                    </tr>
                </table>
                  <script>	
				  	function showTab(what){
						//toggle table on/off
						document.getElementById('general').style.display = 'none';
						
						
						
						document.getElementById('lawful').style.display = 'none';
						
						
						document.getElementById('tab_general_black').style.display = 'none';
						document.getElementById('tab_general_grey').style.display = '';

						<?php if($sess_accesslevel != 4){?>
						document.getElementById('official').style.display = 'none';
						document.getElementById('tab_official_black').style.display = 'none';
						document.getElementById('tab_official_grey').style.display = '';
						<?php } ?>
						
						document.getElementById(what).style.display = '';
						
						document.getElementById('tab_lawful_black').style.display = 'none';
						document.getElementById('tab_lawful_grey').style.display = '';
						
						document.getElementById('tab_'+what+'_black').style.display = '';
						document.getElementById('tab_'+what+'_grey').style.display = 'none';
						
					}
					
				  </script>
                     <script language='javascript'>
						<!--
						function validateForm(frm) {
							
							if(frm.CompanyCode.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: เลขที่บัญชีนายจ้าง");
								frm.CompanyCode.focus();
								return (false);
							}
							if(frm.CompanyNameThai.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อบริษัท(ภาษาไทย)");
								frm.CompanyNameThai.focus();
								return (false);
							}
							if(frm.Employees.value.length == 0)
							{
								alert("กรุณาใส่ข้อมูล: จำนวนลูกจ้าง");
								frm.Employees.focus();
								return (false);
							}
							//----
							if(frm.CompanyTypeCode.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: ประเภทธุรกิจ");
								frm.CompanyTypeCode.focus();
								return (false);
							}
							if(frm.BusinessTypeCode.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: ประเภทกิจการ");
								frm.BusinessTypeCode.focus();
								return (false);
							}
							if(frm.Province.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: จังหวัด");
								frm.Province.focus();
								return (false);
							}
							//----
							return(true);									
						
						}
						-->
					
					</script>
                    <?php if($mode == "new"){ ?>
                   
                    
                    <form method="post" action="scrp_add_org.php" onsubmit="return validateForm(this);">
                    <?php }elseif($mode == "edit"){ ?>
                <form method="post" action="scrp_update_org.php" onsubmit="return validateForm(this);">
                    <input name="CID" type="hidden" value="<?php echo $output_values["CID"];?>" />
                    <?php } ?>
                    
                    <?php 
						if($_GET["new_id"]){
					?>							
                         <div style="color:#990000; padding:5px 0 0 0; font-weight: bold;">* ข้อมูลสถานประกอบการเลขที่บัญชีนายจ้าง <a href="organization.php?id=<?php echo $_GET["new_id_link"];?>"><?php echo $_GET["new_id"];?> สาขา <?php echo $_GET["branch"];?></a> มีอยู่ในระบบแล้ว</div>
                    <?php
						}					
					?>
                    
                    <?php 
						if($_GET["added"]=="added"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ข้อมูลสถานประกอบการใหม่ได้ถูกบันทึกลงฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["updated"]=="updated" && !isset($_GET["year"])){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลสถานประกอบการเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["updated"]=="updated" && isset($_GET["year"])){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลสถานประกอบการปี <?php echo formatYear($_GET["year"]);?> เสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["reg"]=="reg"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มรหัสไปรษณีย์ลงทะเบียนเสร็จสิ้น</div>
                    <?php
						}					
					?>
					<?php 
						if($_GET["delletter"]=="delletter"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* จดหมายแจ้งได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["delpayment"]=="delpayment"){
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ข้อมูลการส่งเงินเข้ากองทุนได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
                    <?php
						}					
					?>
                  <table style=" padding:10px 0 0px 0; " id="general">
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:0 0 5px 0;">ข้อมูลทั่วไป</div></td>
                      </tr>
                      <tr>
                        <td>เลขที่บัญชีนายจ้าง: </td>
                        <td><label>
                          <?php 
						  		if($sess_accesslevel == 4){ 
									//company didnt see this textbox
									 echo $output_values["CompanyCode"];
                          		}else{ 
						  ?>
	                          <input type="text" name="CompanyCode" value="<?php echo $output_values["CompanyCode"];?>" />*
                          <?php }?>    
                            </label></td>
                        <td class="td_left_pad"> เลขที่ประจำตัวผู้เสียภาษีอากร: </td>
                        <td><input type="text" name="TaxID" value="<?php echo $output_values["TaxID"];?>" /></td>
                      </tr>
                      <tr>
                        <td>เลขที่สาขา:</td>
                        <td>
                         <?php 
						  		if($sess_accesslevel == 4){ 
									//company didnt see this textbox
									 echo $output_values["BranchCode"];
                          		}else{ 
						  ?>
                        <input type="text" name="BranchCode" value="<?php echo $output_values["BranchCode"];?>" />
                        <?php } ?></td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td > ประเภทธุรกิจ:</td>
                        <td><?php include "ddl_org_type.php";?>
                          * </td>
                        <td class="td_left_pad"> ประเภทกิจการ:</td>
                        <td><?php include "ddl_bus_type.php";?> *</td>
                      </tr>
                      <tr>
                        <td>ชื่อบริษัท (ภาษาไทย): </td>
                        <td><input type="text" name="CompanyNameThai" value="<?php echo ($output_values["CompanyNameThai"]);?>" />
                        *</td>
                        <td class="td_left_pad"> ชื่อบริษัท (ภาษาอังกฤษ):</td>
                        <td><input type="text" name="CompanyNameEng" value="<?php echo $output_values["CompanyNameEng"];?>" /></td>
                      </tr>
                      <tr>
                        <td > จำนวนลูกจ้าง:</td>
                        <td><input type="text" name="Employees" id="Employees2" value="<?php echo formatEmployee($output_values["Employees"]);?>" onchange="addEmployeeCommas('Employees2');"/>
                          คน*<?php include "js_format_employee.php";?></td>
                        <td class="td_left_pad">สถานะของกิจการ</td>
                        <td><?php include "ddl_company_status.php";?></td>
                      </tr>
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ที่อยู่</div></td>
                      </tr>
                      <tr>
                        <td>สถานที่ตั้งเลขที: </td>
                        <td><label>
                          <input type="text" name="Address1" value="<?php echo $output_values["Address1"];?>" />
                        </label></td>
                        <td class="td_left_pad">ซอย: </td>
                        <td><input type="text" name="Soi" value="<?php echo $output_values["Soi"];?>" /></td>
                      </tr>
                      <tr>
                        <td>หมู่:</td>
                        <td><input type="text" name="Moo" value="<?php echo $output_values["Moo"];?>" /></td>
                        <td class="td_left_pad"> ถนน:</td>
                        <td><input type="text" name="Road" value="<?php echo $output_values["Road"];?>" /></td>
                      </tr>
                      <tr>
                        <td>ตำบล/แขวง: </td>
                        <td><input type="text" name="Subdistrict" value="<?php echo $output_values["Subdistrict"];?>" /></td>
                        <td class="td_left_pad"> อำเภอ/เขต:</td>
                        <td><input type="text" name="District" value="<?php echo $output_values["District"];?>" /></td>
                      </tr>
                      <tr>
                        <td>จังหวัด: </td>
                        <td><?php include "ddl_org_province.php"?>
*</td>
                        <td class="td_left_pad"> รหัสไปรษณีย์:</td>
                        <td><input type="text" name="Zip" value="<?php echo $output_values["Zip"];?>" /></td>
                      </tr>
                      <tr>
                        <td>โทรศัพท์:</td>
                        <td><input type="text" name="Telephone" value="<?php echo $output_values["Telephone"];?>" /></td>
                        <td class="td_left_pad">email:</td>
                        <td><input type="text" name="email" value="<?php echo $output_values["email"];?>" /></td>
                      </tr>
                      <tr>
                        <td>เวปไซต์:</td>
                        <td><input type="text" name="org_website" value="<?php echo $output_values["org_website"];?>" /></td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ข้อมูลติดต่อ</div></td>
                      </tr>
                      <tr>
                        <td>ชื่อผู้ติดต่อ 1: </td>
                        <td><label>
                          <input type="text" name="ContactPerson1" value="<?php echo $output_values["ContactPerson1"];?>" />
                        </label></td>
                        <td class="td_left_pad">เบอร์โทรศัพท์: </td>
                        <td><input type="text" name="ContactPhone1" value="<?php echo $output_values["ContactPhone1"];?>" /></td>
                      </tr>
                      <tr>
                        <td>ตำแหน่ง:</td>
                        <td><input type="text" name="ContactEmail1" value="<?php echo $output_values["ContactEmail1"];?>" /></td>
                        <td class="td_left_pad"> อีเมล์:</td>
                        <td><input type="text" name="ContactPosition1" value="<?php echo $output_values["ContactPosition1"];?>" /></td>
                      </tr>
                       <tr>
                        <td>ชื่อผู้ติดต่อ 2: </td>
                        <td><label>
                          <input type="text" name="ContactPerson2" value="<?php echo $output_values["ContactPerson2"];?>" />
                        </label></td>
                        <td class="td_left_pad">เบอร์โทรศัพท์: </td>
                        <td><input type="text" name="ContactPhone2" value="<?php echo $output_values["ContactPhone2"];?>" /></td>
                      </tr>
                      <tr>
                        <td>ตำแหน่ง:</td>
                        <td><input type="text" name="ContactEmail2" value="<?php echo $output_values["ContactEmail2"];?>" /></td>
                        <td class="td_left_pad"> อีเมล์:</td>
                        <td><input type="text" name="ContactPosition2" value="<?php echo $output_values["ContactPosition2"];?>" /></td>
                      </tr>
                      <tr>
                        <td colspan="4">
                          <div align="center">
                          	<hr />
                            
                            <?php if($sess_accesslevel !=5){ //exec can't do these?>
                                
                                <?php if($mode == "new"){?>                            
                                <input type="submit" name="button" id="button" value="เพิ่มรายชื่อสถานประกอบการ" 
                                onclick = "return confirm('ต้องการเพิ่มรายชื่อสถานประกอบการนี้?');"
                                 />
                                
                                <?php }?>
                                <?php if($mode == "edit"){?>
                                <input type="submit" name="button" id="button" value="ปรับปรุงข้อมูลสถานประกอบการ" 
                                onclick = "return confirm('ต้องการปรับปรุงข้อมูลสถานประกอบการนี้?');"
                                />
                                <?php }?>
                                
                             <?php }?>
                          </div>                        </td>
                      </tr>
                      <tr>
                        <td colspan="4">
                          <div align="left">
                          	<hr />
                            <?php if($mode == "edit" && $sess_accesslevel == 1){?>
                            <input type="submit" name="btn_delete" id="btn_delete" value="'ลบ'สถานประกอบการ" 
                             onclick = "return doConfirmDelete();"
                            />
                            <script>
								function doConfirmDelete(){
									confirm_1 = confirm('ต้องการลบข้อมูลสถานประกอบการนี้? ข้อมูลสถานประกอบการที่ถูกลบไปแล้วจะไม่สามารถนำกลับคืนมาได้');
									
									if(confirm_1){
										return confirm('กดยืนยันอีกครั้งเพื่อลบสถานประกอบการนี้');
									}else{
										return false;
									}
								}
							</script>
                            <?php }?>
                          </div>                        </td>
                      </tr>
              </table>
                </form>
              
              	<?php
				
					//try to show last "document_requests" entry
					if($_POST["ddl_year"]){
						//echo "in the loop";
						$this_year = doCleanInput($_POST["ddl_year"]);
						$conditions = " and Year = '$this_year'";
						
					}elseif($_GET["ddl_year"]){
						$this_year = doCleanInput($_GET["ddl_year"]);
						$conditions = " and Year = '$this_year'";
					}elseif($_GET["year"]){
						$this_year = doCleanInput($_GET["year"]);
						$conditions = " and Year = '$this_year'";
					}else{
					
						//$this_year = strtotime(date('Y'),date('Y')."+1 year");
						//$this_year = date ( 'Y', strtotime ( '+123 day' . date('Y') ) );
						
						if(date("m") >= 9){
							$this_year = date("Y")+1; //new year at month 9
						}else{
							$this_year = date("Y");
						}
						
						$conditions = " and Year = '$this_year'";
					}
					
					$this_lawful_year = $this_year;
					
				
					//only show this to non-company
					if($sess_accesslevel !=4){
				?>
              		
                    
                    <table style=" padding:10px 0 0px 0; " id="official">
                      
                      <tr>
                        <td><div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการส่งจดหมายแจ้ง</div> 
                        
                        <?php if($sess_accesslevel != 5){?>
                        <a href="org_list.php?mode=letters&search_id=<?php echo $this_id;?>&for_year=<?php echo $this_lawful_year;?>">+ ส่งจดหมายแจ้ง</a>                         
                        <?php }?>
                        </td>
                      </tr>
                      <tr>
                        <td>
                        
                        <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                    	  <td><div align="center"><span class="column_header">วันที่</span> </div></td>
           	           	    <td>
                            	<div align="center"><span class="column_header">ครั้งที่</span> </div></td>
                            <td ><div align="center"><span class="column_header">หนังสือเลขที่</span> </div></td>
                            <td ><div align="center"><span class="column_header">เลขที่ลงทะเบียน</span> </div></td>
                            <?php if($sess_accesslevel != 5){?>
                            <td >  <div align="center"><span class="column_header">ลบข้อมูล</span> </div></td>
                            <?php }?>
                          </tr>
                             
                         <?php 
							
							
							
							//generate letter history
							$get_letter_sql = "select *
										from documentrequest a, docrequestcompany b
										where
											a.rid = b.rid
										and 
											CID = '$this_id'
										
										$conditions	
										
										and is_hold_letter = '0'
										
										order by RequestDate desc
										
										";
							
							//echo $get_letter_sql;
							
							$letter_result = mysql_query($get_letter_sql);
							
							while ($post_row = mysql_fetch_array($letter_result)) {
							?>
                        <tr bgcolor="#ffffff" align="center" >
                          <td> <?php echo formatDateThai($post_row["RequestDate"]);?></td>
                       	    <td>

                            	<?php echo doCleanOutput($post_row["RequestNum"]);?>                          </td>
                            <td ><a href="view_letter.php?id=<?php echo doCleanOutput($post_row["RID"]);?>"><?php echo doCleanOutput($post_row["GovDocumentNo"]);?></a> </td>
                            <td >
                            <?php if($sess_accesslevel != 5){?>
                                <form action="scrp_add_register.php"  method="post">
                            <?Php } ?>
                            <input name="PostRegNum" type="text" value="<?php echo doCleanOutput($post_row["PostRegNum"]);?>" />
                            <input name="DID" type="hidden" value="<?php echo doCleanOutput($post_row["DID"]);?>" />
                            <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                            <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                            
                            <?php if($sess_accesslevel != 5){?>
                           		<input name="add_code" type="submit" value="เพิ่มรหัส" />
							 <?php }?>   
                            </form>  
                            
                                                  </td>
							<?php if($sess_accesslevel != 5){?>
                                <td>
                                        <div align="center"><a href="scrp_delete_doccom.php?id=<?php echo doCleanOutput($post_row["DID"]);?>&cid=<?php echo doCleanOutput($output_values["CID"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div>
                                    
                                </td>
                            <?php }?>
                            
                            
                            
                          </tr>
                          <?php
						  	} //generate letter history?>
                      </table>                       </td>
                      </tr>
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      <!---           แจ้งอายัด -                  -              -->
                      
                      
                      
                      <tr>
                        <td>
                        <hr /><div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการแจ้งอายัด</div>
                        
                        <?php if($sess_accesslevel != 5){?>
                        <a href="org_list.php?mode=letters&search_id=<?php echo $this_id;?>&for_year=<?php echo $this_lawful_year;?>&type=hold">+ แจ้งอายัด</a>                         
                        <?php }?>
                        
                        </td>
                      </tr>
                       <tr>
                        <td>
                        
                            <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                                <tr bgcolor="#9C9A9C" align="center" >
                                  <td><div align="center"><span class="column_header">วันที่</span> </div></td>
                                    <td>
                                        <div align="center"><span class="column_header">ครั้งที่</span> </div></td>
                                    <td ><div align="center"><span class="column_header">หนังสือเลขที่</span> </div></td>
                                    <td ><div align="center"><span class="column_header">เลขที่ลงทะเบียน</span> </div></td>
                                    <?php if($sess_accesslevel != 5){?>
                                    <td >  <div align="center"><span class="column_header">ลบข้อมูล</span> </div></td>
                                    <?php }?>
                                  </tr>
                                  
                                   <?php 
							
							
							
							//generate letter history
							$get_letter_sql = "select *
										from documentrequest a, docrequestcompany b
										where
											a.rid = b.rid
										and 
											CID = '$this_id'
										
										$conditions	
										
										and is_hold_letter = '1'
										
										order by RequestDate desc
										
										";
							
							//echo $get_letter_sql;
							
							$letter_result = mysql_query($get_letter_sql);
							
							while ($post_row = mysql_fetch_array($letter_result)) {
							?>
                        <tr bgcolor="#ffffff" align="center" >
                          <td> <?php echo formatDateThai($post_row["RequestDate"]);?></td>
                       	    <td>

                            	<?php echo doCleanOutput($post_row["RequestNum"]);?>                          </td>
                            <td ><a href="view_letter.php?id=<?php echo doCleanOutput($post_row["RID"]);?>&type=hold"><?php echo doCleanOutput($post_row["GovDocumentNo"]);?></a> </td>
                            <td >
                            <?php if($sess_accesslevel != 5){?>
                                <form action="scrp_add_register.php"  method="post">
                            <?Php } ?>
                            <input name="PostRegNum" type="text" value="<?php echo doCleanOutput($post_row["PostRegNum"]);?>" />
                            <input name="DID" type="hidden" value="<?php echo doCleanOutput($post_row["DID"]);?>" />
                            <input name="CID" type="hidden" value="<?php echo doCleanOutput($output_values["CID"]);?>" />
                            <input name="this_year" type="hidden" value="<?php echo $this_year;?>" />
                            
                            <?php if($sess_accesslevel != 5){?>
                           		<input name="add_code" type="submit" value="เพิ่มรหัส" />
							 <?php }?>   
                            </form>  
                            
                                                  </td>
							<?php if($sess_accesslevel != 5){?>
                                <td>
                                        <div align="center"><a href="scrp_delete_doccom.php?id=<?php echo doCleanOutput($post_row["DID"]);?>&cid=<?php echo doCleanOutput($output_values["CID"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div>
                                    
                                </td>
                            <?php }?>
                            
                            
                            
                          </tr>
                          <?php
						  	} //generate letter history?>
                                  
                                  
                                  
                              </table>
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      <tr>
                        <td><hr /><div style="font-weight: bold; padding:0 0 5px 0;">เอกสารจากสถานประกอบการ</div></td>
                      </tr>
                      <tr>
                        <td><?php
                        
							//try to show last "document_requests" entry
							//print_r($_POST);
							if($_POST["ddl_year"]){
								$this_docr_year = doCleanInput($_POST["ddl_year"]);
								$doc_conditions = " and docr_year = '$this_docr_year'";
							}elseif($_GET["ddl_year"]){
								$this_docr_year = doCleanInput($_GET["ddl_year"]);
								$doc_conditions = " and docr_year = '$this_docr_year'";
							}else{
								$this_docr_year = date('Y');
							}
							
							$sql = "select * 
								from 
									document_requests
								where 
									docr_org_id  = '$this_id'
									
									$doc_conditions
								
								order by docr_id desc
								limit 0,1";
							//echo $sql;
							$docr_row = getFirstRow($sql);
						
							if($docr_row["docr_status"] == 1){
								$stat_1_checked = 'checked="checked"';
								$stat_2_checked = '';
								$date_to_show = ($docr_row["docr_date"]);
							}else{
								$stat_1_checked = '';
								$stat_2_checked = 'checked="checked"';
								$date_to_show = date("Y-m-d");
							}
						
						?>
                        <table border="0">
                        <form action="?id=<?php echo $this_id;?>&focus=official" method="post">
                         <tr>
                            <td colspan="2">ข้อมูลประจำปี 
                              <?php
                              
							  	$dll_year_name = "docr_year";
							  	include "ddl_year_auto_submit.php";
							  
							  ?></td>
                          </tr>
                        </form>
                        
                        <form method="post" action="scrp_update_org_doc_stat.php">
                          
                         
                          <tr>
                            <td><label>
                            <input type="radio" name="docr_status" id="docr_stat" value="1" <?php echo $stat_1_checked;?> />
                            </label>
                            ได้รับเอกสารครบแล้ว ณ วันที </td>
                            <td>
                            <?php
											   
							   $selector_name = "docr_date";
							   
							   $this_date_time = $docr_row["docr_date"];
							 
							   if($this_date_time != "0000-00-00"){
								   $this_selected_year = date("Y", strtotime($this_date_time));
								   $this_selected_month = date("m", strtotime($this_date_time));
								   $this_selected_day = date("d", strtotime($this_date_time));
							   }else{
								   $this_selected_year = 0;
								   $this_selected_month = 0;
								   $this_selected_day = 0;
							   }
							   
							   include ("date_selector.php");
							   
							   ?>                            </td>
                          </tr>
                          <tr>
                            <td colspan="2"><div style="padding-left:25px;"><textarea name="docr_status_remark" cols="35" rows="3"><?php echo doCleanOutput($docr_row["docr_status_remark"]);?></textarea></div></td>
                          </tr>
                          <tr>
                            <td><input type="radio" name="docr_status" id="docr_stat2" value="0"  <?php echo $stat_2_checked;?>/> 
                              ได้รับเอกสารยังไม่ครบ เอกสารที่ยังขาดคือ </td>
                            <td><label></label></td>
                          </tr>
                          <tr>
                            <td colspan="2"><div style="padding-left:25px;"><textarea name="docr_desc" cols="35" rows="3"><?php echo doCleanOutput($docr_row["docr_desc"]);?></textarea></div></td>
                          </tr>
                          <tr>
                            <td colspan="2"><div align="center">
                              <input name="docr_year" type="hidden" value="<?php echo $this_docr_year;?>" />
                              
                              <?php if($sess_accesslevel != 5){?>
                                  <input type="submit" name="button2" id="button2" value="ปรับปรุงข้อมูล" 
                                  onclick = "return confirm('ต้องการปรับปรุงข้อมูลเอกสารจากสถานประกอบการนี้?');"
                                  />
                              <?php }?>
                              <input name="docr_org_id" type="hidden" value="<?php echo $this_id; ?>" />
                            </div></td>
                          </tr>
                        </table></form></td>
                      </tr>
                      
                    </table>
                  
              		<?php } //end if to show this tab for non-company only?>
              
              
                    <table style=" padding:10px 0 0px 0; " id="lawful">
                    
                      <tr>
                        <td><div style="font-weight: bold; padding:0 0 5px 0;">การปฏิบัติตามกฎหมายของสถานประกอบการ</div></td>
                      </tr>
                      <tr>
                        <td><?php
                        
							
							
							$lawful_row = getFirstRow("select * 
								from 
									lawfulness
								where 
									CID  = '$this_id'
									
									$conditions
									
								order by LID desc
								
								limit 0,1");
						
							$stat_1_checked = '';
							$stat_2_checked = '';
							$stat_3_checked = '';
							$stat_4_checked = '';
							
							$no_recipient_checked = '';
						
							if($lawful_row["LawfulStatus"] == "0"){
								$stat_2_checked = 'checked="checked"';//unlawful
							}elseif($lawful_row["LawfulStatus"] == "1"){
								$stat_1_checked = 'checked="checked"'; //lawful
							}elseif($lawful_row["LawfulStatus"] == "2"){
								$stat_4_checked = 'checked="checked"';//in progress
							}elseif($lawful_row["LawfulStatus"] == "3"){
								$stat_3_checked = 'checked="checked"';//no employee
							}else{
								//blank row

								$stat_2_checked = 'checked="checked"';//unlawful
							}
							
							if($lawful_row["NoRecipient"] == "1"){
								$no_recipient_checked = 'checked="checked"';
							}
							
							$lawful_fields = array(
								'LID'
								,'CID'
								,'lawfulStatus'
								,'Employees'
								
								,'Hire_status'
								,'Hire_NumofEmp'
								,'Hire_docfile'
								
								,'Conc_status'
								
								,'Conc1_status'
								,'Conc1_docfile'
								,'Conc2_status'
								,'Conc2_docfile'
								,'Conc3_status'
								,'Conc3_docfile'
								,'Conc4_status'
								,'Conc4_docfile'
								,'Conc5_status'
								,'Conc5_docfile'
								
								,'pay_status'
						
								,'cash_date'
								,'cash_amount'
								,'cash_docfile'
								
								,'check_bank'
								,'check_number'
								,'check_date'
								,'check_amount'
								,'check_docfile'
								
								,'note_number'
								,'note_date'
								,'note_amount'
								,'note_docfile'
								
								,'NoRecipient'
								,'NoRecipient_remark'
								,'Hire_NewEmp'
								
								
								);
								
							for($i = 0; $i < count($lawful_fields); $i++){
								//clean all inputs
								$lawful_values[$lawful_fields[$i]] .= doCleanOutput($lawful_row[$lawful_fields[$i]]);
							}
						
						?>
                        	<form action="?id=<?php echo $this_id;?>&focus=lawful" method="post">
                            <table>
                            	<tr>
                                  <td colspan="2">ข้อมูลประจำปี
                                  <?php
                              		
									//print_r($_POST);
							  		include "ddl_year_auto_submit.php";
							  
							  	?>
                              	</td>
                                </tr>
                            </table>
                            </form>
                            <script language='javascript'>
							
								
								<!--
								function validateLawfulStat(frm) {
									
									lawful_value = getCheckedValue(frm.lawfulStatus);
									//alert(lawful_value);
									
									<?php if($sess_accesslevel !=4){?>
									if(lawful_value == 0){
										if(frm.Hire_status.checked || frm.pay_status.checked || frm.Conc_status.checked){
											alert("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n ข้อมูลผิดพลาด -> ไม่ปฏิบัติตามกฎหมาย แต่ \"มีการ\":\n1. จ้างคนพิการเข้าทำงาน\n2. ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ\n3. ให้สัมปทานฯ !\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
											return false;
										}
									}
									
									if(lawful_value == 1){
										
										if(!frm.Hire_status.checked && !frm.pay_status.checked  && !frm.Conc_status.checked){
											alert("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n ข้อมูลผิดพลาด -> ปฏิบัติตามกฎหมาย แต่ \"ไม่มีการ\":\n1. จ้างคนพิการเข้าทำงาน\n2. ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ\n3. ให้สัมปทานฯ !\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
											return false;
										}
									}
									<?php } ?>
									
									
									if(frm.Hire_status.checked && frm.Hire_NumofEmp.value < 1){
											alert("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n ข้อมูลผิดพลาด -> มีการจ้างงานคนพิการ แต่ไม่มีจำนวนคนพิการ\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
											return false;
									}
									
									//hire
									if(!frm.Hire_status.checked && frm.Hire_NumofEmp.value != 0){
											alert("!!!! มีจำนวนคนพิการ แต่ไม่ได้ check 'จ้างคนพิการเข้าทำงาน '!!!!");
											return false;
									}
									
									
									//hire
									if(frm.pay_status.checked && frm.have_receipt.value == 0){
											alert("!!!! มีการ check 'ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ' แต่ไม่มีข้อมูลใบเสร็จ!!!!");
											return false;
									}
									
									if(!frm.pay_status.checked && frm.have_receipt.value == 1){
											alert("!!!! มีข้อมูลใบเสร็จ แต่ไม่มีการ check 'ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ' !!!!");
											return false;
									}
									
									//conc
									if((frm.Conc1_status.checked || frm.Conc2_status.checked || frm.Conc3_status.checked || frm.Conc4_status.checked || frm.Conc5_status.checked) && !frm.Conc_status.checked){
											alert("!!!! มีรายละเอียดการให้สัมปทาน แต่ไม่ check ช่อง 'ให้สัมปทานฯ'!!!!");
											return false;
									}
									if((!frm.Conc1_status.checked && !frm.Conc2_status.checked && !frm.Conc3_status.checked && !frm.Conc4_status.checked && !frm.Conc5_status.checked) && frm.Conc_status.checked){
											alert("!!!! มีการ check ช่อง 'ให้สัมปทานฯ' แต่ไม่มีรายละเอียดการให้สัมปทาน!!!!");
											return false;
									}
									
									
									return true;
																								
								
								}
								
								function getCheckedValue(radioObj) {
									if(!radioObj)
										return "";
									var radioLength = radioObj.length;
									if(radioLength == undefined)
										if(radioObj.checked)
											return radioObj.value;
										else
											return "";
									for(var i = 0; i < radioLength; i++) {
										if(radioObj[i].checked) {
											return radioObj[i].value;
										}
									}
									return "";
								}

								-->
							
							</script>
                            <form action="scrp_update_org_lawful_stat.php" method="post" enctype="multipart/form-data" onsubmit="return validateLawfulStat(this);">
                              <table border="0">
                                
                                <?php if($sess_accesslevel !=4){?>
                                
                                
                                <tr>
                                  <td><input type="radio" name="lawfulStatus" id="" value="0"  <?php echo $stat_2_checked;?>/>
                                    ไม่ปฏิบัติตามกฎหมาย</td>
                                  <td><label></label></td>
                                </tr>
                                
                                
                                <tr>
                                  <td colspan="2" style="padding-left: 20px;">
                                  	<input name="NoRecipient" type="checkbox" value="1" <?php echo $no_recipient_checked;?>/> ไม่มีคนรับเอกสาร
                                    
                                  </td>
                                  
                                </tr>
                                <tr>
                                  <td colspan="2">
                                    <div style="padding-left:43px">หมายเหตุ: <input name="NoRecipient_remark" type="text" style="width:250px;" value="<?php echo doCleanOutput($lawful_values["NoRecipient_remark"])?>" /></div>
                                  </td>
                                  
                                </tr>
                                
                                
                                
                               <tr>
                                  <td><input type="radio" name="lawfulStatus" id="" value="3"  <?php echo $stat_3_checked;?>/>
ไม่เข้าข่ายจำนวนลูกจ้าง</td>
                                  <td>&nbsp;</td>
                              </tr>
                               
                                <tr>
                                  <td><input type="radio" name="lawfulStatus" id="" value="2"  <?php echo $stat_4_checked;?>/>
ปฏิบัติตามกฏหมายแต่ไม่ครบตามอัตราส่วน</td>
                                  <td>&nbsp;</td>
                                </tr>
                                
                                
                                
                                 <?php } ?>
                                
                                <tr>
                                  <td>
                                   <?php if($sess_accesslevel !=4){?>
                                  	<label>
                                   
                                    <input type="radio" name="lawfulStatus" id="" value="1" <?php echo $stat_1_checked;?> />
                                    </label>
                                    ปฏิบัติตามกฎหมาย<br />
                                    <?php } ?>
                                    <div class="style86" style="padding: 10px 0 10px 0;">
                                    
                                    <table >
                                    	
                                        
                                    	<tr>
                                        	<td>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    		<input name="Hire_status" type="checkbox" id="Hire_status" value="1" <?php echoChecked($lawful_values["Hire_status"])?> />                                            </td>
                                            <td><span style="font-weight: bold;color:#006600;">
                                             <?php //if($sess_accesslevel ==4){
											 if(1==1){?>
                                             	มาตรา 33 จ้างคนพิการเข้าทำงาน
                                             <?php }else{ ?>
                                            	ปฏิบัติตามมาตรา 33
                                             <?php } ?>
                                            </span></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    	<tr>
                                    	  <td>&nbsp;</td>
                                    	  <td colspan="2" style="padding-left: 30px;"><table border="0">
                                            <tr>
                                              <td>จำนวนลูกจ้าง</td>
                                              <td><input name="Employees" id="Employees" style="width:50px" type="text" value="<?php 
											  	if($lawful_values["Employees"]){
													echo formatEmployee($lawful_values["Employees"]);
													$employee_to_use = $lawful_values["Employees"];
												}else{
													echo formatEmployee($output_values["Employees"]);
													$employee_to_use = $output_values["Employees"];
												}
												
												?>" onchange="reCalculateRatio(); addEmployeeCommas('Employees'); " /> คน </td>
                                            </tr>
                                            
                                            <tr>
                                              <td>อัตราส่วนลูกจ้างต่อคนพิการ</td>
                                              <td><?php 
											  
											  	//what ratio to use?
												$ratio_to_use = default_value(getFirstItem("select var_value 
																	from vars where var_name = 'ratio_$this_lawful_year'"),100);
												
												$half_ratio_to_use = $ratio_to_use/2;
												
												echo ($ratio_to_use);
												
											  
											  ?>:1 = <strong id="employee_ratio"><?php 
											  	//if employee > 200
												
											  	if(($employee_to_use/$ratio_to_use)>1 || $employee_to_use == $ratio_to_use){
												
													//see mod...
													$left_over = $employee_to_use%$ratio_to_use;
													
													if($left_over <= $half_ratio_to_use){
														$final_employee = (floor($employee_to_use/$ratio_to_use));
														
														
													}else{
														$final_employee = (ceil($employee_to_use/$ratio_to_use));
														
													
													}
												
													
												
												}else{
													$final_employee = "0";
												}
												
												echo formatEmployee($final_employee);
												
												?></strong> คน</td>
                                            </tr>
                                            
                                            
                                            
                                            <script>
												function reCalculateRatio(){
													employee_to_use = document.getElementById("Employees").value; 
													employee_to_use = employee_to_use.replace(/,/g,"");
													//alert(employee_to_use);
													
													if(employee_to_use > 0){
														if(employee_to_use > <?php echo $ratio_to_use;?> || employee_to_use == <?php echo $ratio_to_use;?>){
															left_over = employee_to_use%<?php echo $ratio_to_use;?>;
															if(left_over <= <?php echo $half_ratio_to_use;?>){
																ratio_to_use = Math.floor(employee_to_use/<?php echo $ratio_to_use;?>);
															}else{
																ratio_to_use = Math.ceil(employee_to_use/<?php echo $ratio_to_use;?>);
															}
														}else{
															ratio_to_use = 0;
														}
													}
													
													document.getElementById("employee_ratio").innerHTML = ratio_to_use;
												}
											</script>
                                            <tr>
                                              <td>จำนวนคนพิการที่ทำงานในปัจจุบัน</td>
                                              <td>
                                              
                                              <?php 
											  
											  	//now we count number of hired employeed directly from LE list
											  
											  	$hire_numofemp = $lawful_values["Hire_NumofEmp"];
												
												/*$hire_numofemp = getFirstItem("
														SELECT 
															count(*)
														FROM 
															lawful_employees
														where
															le_cid = '$this_id'
															and le_year = '$this_lawful_year'");*/
												
											   ?>
                                              
                                               <strong><?php echo $hire_numofemp;?></strong>
                                              
                                              
                                              <input name="Hire_NumofEmp" type="hidden" id="Hire_NumofEmp" size="10" value="<?php echo formatEmployee(default_value($hire_numofemp,"0"));?>" onchange=" addEmployeeCommas('Hire_NumofEmp');"/> 
                                              
                                              
                                              คน || <a href="" onclick="fireMyPopup('my_popup',1020,160); return false;">ข้อมูลคนพิการที่ได้รับเข้าทำงาน</a></td>
                                            </tr>
                                            
                                            
                                            <tr>
                                              <td>ผู้ใช้สิทธิมาตรา 35</td>
                                              <td>
                                              
                                              
                                              <?php
											  
											  	$curator_user = getFirstItem("select count(*) from curator where curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0");
											  
											  ?>
                                              <strong><?php echo $curator_user;?></strong>
                                              
                                               
                                              คน
                                            </tr>
                                            
                                            
                                             <tr>
                                              <td>จำนวนคนพิการที่รับเพิ่ม</td>
                                              <td>
                                              
                                              <b>
                                              <?php 
											  
											  	$extra_emp = $final_employee - $hire_numofemp - $curator_user;
												
											    echo formatEmployee(default_value($extra_emp,"0"));
											  ?>
                                              </b>
                                              
                                              
                                              <input name="Hire_NewEmp" type="hidden" id="Hire_NewEmp" size="10" value="<?php echo $extra_emp;?>" onchange=" addEmployeeCommas('Hire_NewEmp');" /> 
                                              คน
                                            </tr>
                                            <tr>
                                              <td>เอกสารประกอบ</td>
                                              <td><span class="style86" style="padding: 10px 0 10px 0;">
                                               
                                                <div style="width:400px; padding-bottom:5px;">
												<?php 
                                                    
													//do $this_id swap thing because doc link use LID, but consume $this_id
													//but $this_id on this page is CID and not LID...
													$this_id_temp = $this_id;
													$this_id = $lawful_values["LID"];
													
                                                    $file_type = "Hire_docfile";
                                                
                                                    include "doc_file_links.php";
													
													$this_id = $this_id_temp;
													
                                                ?>
                                                </div>
                                                <input type="file" name="Hire_docfile" id="Hire_docfile" />
                                               </span></td>
                                            </tr>

                                          </table></td>
                                   	  </tr>
                                    	<tr>
                                    	  <td colspan="3">&nbsp;</td>
                                   	  </tr>
                                    </table>

                                    <table>
                                    	
                                    
                                      <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <input name="pay_status" type="checkbox" id="pay_status" value="1" <?php echoChecked($lawful_values["pay_status"])?>/>                                        </td>
                                        <td><span style="font-weight: bold; color:#006600;">
                                       <?php //if($sess_accesslevel ==4){
											 if(1==1){?>
                                             	มาตรา 34 ส่งเงินเข้ากองทุนฯแทนการรับคนพิการ
                                             <?php }else{ ?>
                                            	ปฏิบัติตามมาตรา 34
                                             <?php } ?>
                                        </span></td>
                                        <td>&nbsp;</td>
                                      </tr>

                                      <tr >
                                        <td>&nbsp;</td>
                                        <td colspan="2" style="padding-left: 30px;">
                                        <table border="0" >
                                        	
                                          <tr>
                                            <td>
                                            	
                                                <?php
														
													//$extra_employee = $lawful_values["Hire_NewEmp"];
													$extra_employee = $extra_emp;													
													//if($extra_employee == 0){
													//	$extra_employee = $final_employee - $lawful_values["Hire_NumofEmp"];
														
													//}
													
													$wage_rate = 159;
													$year_date = 365;
													
													

													
													$interest_date = dateDiffTs(strtotime(date("$this_lawful_year-02-01")), strtotime(date("Y-m-d")));
													
													$start_money = $extra_employee*$wage_rate*$year_date;
													
												
													$the_sql = "select sum(receipt.Amount) 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '$this_lawful_year'
														and
														lawfulness.CID = '".$this_id."'
														and
														is_payback != 1
														";
														
													$paid_money = getFirstItem("$the_sql");
													//echo $the_sql;
													
													
													$the_sql = "select sum(receipt.Amount) 
														from payment, receipt , lawfulness
														where 
														receipt.RID = payment.RID
														and
														lawfulness.LID = payment.LID
														and
														ReceiptYear = '$this_lawful_year'
														and
														lawfulness.CID = '".$this_id."'
														and
														is_payback = 1
														";
														
													$payback_money = getFirstItem("$the_sql");
													
													//------
													
													$owned_money = $start_money - $paid_money +$payback_money;
													
													if($owned_money < 0){
														$owned_money = 0;
													}
												
													
												
												?>
                                            
                                            
                                            	<?php //if($start_money > 0){
													if(1 == 1){
													//only show this if has starting money
												?>
                                            	<table>
                                                
                                                	
                                                	<tr>
                                                    	<td>
                                                        เงินที่ต้องส่งเข้ากองทุน:                                                        </td>
                                                        <td>
														<div align="right">
														<?php echo $extra_employee;?> x <?php echo $wage_rate;?> x <?php echo $year_date;?> = </td>
                                                        </div>
                                                        <td>
                                                        <div align="right">
                                                        <?Php echo formatNumber($start_money);?>                                                        </div>
                                                        <td>
                                                        บาท                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td>
                                                        ยอดเงินที่จ่ายเข้ากองทุนแล้ว:
                                                        </td>
                                                        <td>
														<div align="right">
														= </div></td>
                                                        
                                                        <td>
                                                        <div align="right">
                                                        <?Php echo formatNumber($paid_money);?>                                                        </div>
                                                        <td>
                                                        บาท                                                        </td>
                                                    </tr>
                                                    
                                                     <tr>
                                                    	<td>
                                                        ขอเงินคืนจากกองทุนฯ:
                                                        </td>
                                                        <td>
														<div align="right">
														= </div></td>
                                                        
                                                        <td>
                                                        <div align="right">
                                                        <?Php echo formatNumber($payback_money);?>                                                        </div>
                                                        <td>
                                                        บาท                                                        </td>
                                                    </tr>
                                                    
                                                    <tr>
                                                    	<td>
                                                        วันที่จ่ายเงินเข้ากองทุนล่าสุด:                                                        </td>
                                                        <td>
                                                        <div align="right">
                                                       
                                                        </div>
                                                        
                                                        
                                                        </td>
                                                        <td colspan="2">
                                                        <div align="right">
                                                         <?php 
														
														
														$the_sql = "select max(paymentDate) from payment, receipt , lawfulness
															where 
															receipt.RID = payment.RID
															and
															lawfulness.LID = payment.LID
															and
															ReceiptYear = '$this_lawful_year'
															and
															lawfulness.CID = '".$this_id."' 
															
															and
															is_payback != 1
															";
														
														//echo $the_sql ;
														
														$actual_interest_date = getFirstItem($the_sql);
														
														if($actual_interest_date && $actual_interest_date != '0000-00-00 00:00:00'){
															echo formatDateThai($actual_interest_date);
														}else{
															echo "ไม่เคยมีการจ่ายเงิน";
														}
														
														?>                                                        </div>                                                        </td>
                                                         
                                                    </tr>
                                                    
                                                    <?php
													
													//cal culate interest money
													
													
													$interest_date = getInterestDate($actual_interest_date, $this_lawful_year, "Y-m-d");
													
													//echo "<br>$actual_interest_date" . " / ". $this_lawful_year . " / ".  strtotime(date("Y-m-d"))."<br>";
													
													$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
													
													?>
                                                    
                                                    <tr>
                                                    	<td>
                                                        ดอกเบี้ย:                                                        </td>
                                                        <td>
                                                        <div align="right">
                                                        <?php echo formatNumber($owned_money);?> x 7.5/100/<?php echo $year_date;?> x <?php echo $interest_date;?> = 
                                                        </div>
                                                        
                                                        
                                                        </td>
                                                        <td>
                                                        <div align="right">
                                                        <?Php echo formatNumber($interest_money);?>                                                        </div>                                                        </td>
                                                         <td>
                                                        บาท                                                        </td>
                                                    </tr>
                                                    <tr>
                                                    	<td>
                                                        ยอดเงินค้างชำระ:                                                        </td>
                                                        <td>&nbsp;</td>
                                                        <td>
                                                        <div align="right">
                                                        <?Php 
														
															$the_final_money = $owned_money + $interest_money;
															//$the_final_money = $owned_money;
														
															
															if($the_final_money > 0){
																echo "<font color='red'>";
															}else if($the_final_money < 0){
																echo "<font color='green'>";
															}else{
																echo "<font>";
															}
														
															echo formatNumber($the_final_money);
															
															echo "</font>";
															
															?>
                                                         </div>
                                                        </td>
                                                        
                                                         <td>
                                                        บาท                                                        </td>
                                                    </tr>
                                                </table>
                                                
                                                
                                                <?php }//starting_money > 0?>
                                            
                                            </td>
                                          </tr>
                                          <tr>
                                          	<td>
                                            <hr />
                                           <strong> ข้อมูลการส่งเงินเข้ากองทุน</strong>
                                           
										   <?php if($sess_accesslevel!=4 && $sess_accesslevel!=5){?>
                                           <a href="org_list.php?search_id=<?php echo $this_id?>&mode=payment&for_year=<?php echo $this_lawful_year;?>" style="font-weight: bold;">+ เพิ่มข้อมูลการส่งเงินเข้ากองทุน</a>
                                           <?php }?>
                                           
                                            <?php 
												//generate reciept info
												$the_sql = "select * from payment, receipt , lawfulness
															where 
															receipt.RID = payment.RID
															and
															lawfulness.LID = payment.LID
															and
															ReceiptYear = '$this_lawful_year'
															and
															lawfulness.CID = '".$this_id."' 
															
															and
															is_payback != 1
															order by receipt.RID desc";
												
												//echo $the_sql;
												$the_result = mysql_query($the_sql);
												
												$have_receipt = 0;
												while($result_row = mysql_fetch_array($the_result)){
												
													$have_receipt = 1;
													
													//echo "select * from receipt where RID = '".$result_row["RID"]."'";										
													$receipt_row = getFirstRow("select * from receipt where RID = '".$result_row["RID"]."'");
												
												?>
                                               			 <div style="padding:5px">
                                                         <?php if($sess_accesslevel != 4){?>
                                                         	ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <a href="view_payment.php?id=<?php echo $result_row["RID"]?>"><?php echo $receipt_row["ReceiptNo"]?></a> 
															<?php }elseif($sess_accesslevel == 4 && strlen($receipt_row["BookReceiptNo"]) > 0){?>
                                                         	ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <?php echo $receipt_row["ReceiptNo"]?> 
															<?php //}elseif($sess_accesslevel == 4){
																}elseif(1 == 0){																	
															?>
                                                        	 <a href="scrp_delete_receipt.php?id=<?php echo doCleanOutput($result_row["RID"]);?>"  onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');" title="ลบข้อมูลการจ่ายเงินออกจากระบบ"><img src="decors/trashcan_icon.jpg" border="0" /></a>
                                                         	<?php }?>
                                                            
                                                            วันที่จ่าย <?php echo formatDateThai($receipt_row["ReceiptDate"])?> จำนวนเงิน <?php echo formatNumber($receipt_row["Amount"])?> บาท จ่ายโดย <?php echo formatPaymentName($receipt_row["PaymentMethod"]);?> <?php
															
															$paid_for = getFirstItem("select count(*) from payment where payment.rid = '".$result_row["RID"]."'");
															if($paid_for > 1){
																echo "<span style='color:green'>จ่ายให้ $paid_for แห่ง</span>";
															}
															?>
                                                            
                                                            <br />
                                                            
                                                            
                                                            
                                                            จ่ายเป็นดอกเบี้ย 
                                                            
                                                            <?php
															
																														
																														
																//echo "$this_lawful_year-02-01 00:00:00";															
																$interest_date = getInterestDate("$this_lawful_year-02-01 00:00:00", $this_lawful_year, $receipt_row["ReceiptDate"]);
																//$interest_date = getInterestDate("2012-07-13 00:00:00", $this_lawful_year, $receipt_row["ReceiptDate"]);
																
																//echo "<br>2012-07-13 00:00:00" . " / ". $this_lawful_year . " / ". $receipt_row["ReceiptDate"]."<br>";
																
																$owned_money = $owned_money + $receipt_row["Amount"];
																$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
																
																//echo "<br>".$interest_date . " " . $owned_money . " " .$year_date."<br>";
																
																echo formatNumber($interest_money);
																
															?>
                                                            บาท 
                                                            
                                                            
                                                            เป็นเงินต้น 
                                                            
                                                            
                                                            <?php echo formatNumber($receipt_row["Amount"]-$interest_money);?>
                                                            
                                                            
                                                            บาท 
                                                         </div>
                                                         
                                                         <?php if(strlen($receipt_row["ReceiptNote"])>0){ ?>
                                                             <div style="padding:5px">
                                                             ชำระเพื่อ: <?php echo $receipt_row["ReceiptNote"]?>                                                             </div>
                                                         <?php } ?>
                                                         
                                                         
                                                         
                                                <?php
													
													}												
												?>
		                                         <input name="have_receipt" type="hidden" value="<?php echo $have_receipt?>" />                                         	
                                                 
                                                <hr />
                                                
                                                </td>
                                         </tr>
                                          
                                          
                                       
                                          
                                          
                                           <tr>
                                          	<td>
                                               <strong> รายละเอียดขอเงินคืนจากกองทุนฯ</strong>
                                               
                                               <?php if($sess_accesslevel!=4 && $sess_accesslevel!=5){?>
                                           <a href="org_list.php?search_id=<?php echo $this_id?>&mode=payment&for_year=<?php echo $this_lawful_year;?>&payback=1" style="font-weight: bold;">+ เพิ่มรายละเอียดขอเงินคืนจากกองทุนฯ</a>
                                           <?php }?>
                                               
                                               
                                               
                                                 <?php 
												//generate payback info
												$the_sql = "select * from payment, receipt , lawfulness
															where 
															receipt.RID = payment.RID
															and
															lawfulness.LID = payment.LID
															and
															ReceiptYear = '$this_lawful_year'
															and
															lawfulness.CID = '".$this_id."' 
															
															and
															is_payback = 1
															order by receipt.RID desc";
												
												//echo $the_sql;
												$the_result = mysql_query($the_sql);
												
												$have_receipt = 0;
												while($result_row = mysql_fetch_array($the_result)){
												
													$have_receipt = 1;
													
													//echo "select * from receipt where RID = '".$result_row["RID"]."'";										
													$receipt_row = getFirstRow("select * from receipt where RID = '".$result_row["RID"]."'");
												
												?>
                                               			 <div style="padding:5px">
                                                         <?php if($sess_accesslevel != 4){?>
                                                         	ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <a href="view_payment.php?id=<?php echo $result_row["RID"]?>"><?php echo $receipt_row["ReceiptNo"]?></a> 
															<?php }elseif($sess_accesslevel == 4 && strlen($receipt_row["BookReceiptNo"]) > 0){?>
                                                         	ใบเสร็จเล่มที่ <?php echo $receipt_row["BookReceiptNo"]?> เลขที่ <?php echo $receipt_row["ReceiptNo"]?> 
															<?php //}elseif($sess_accesslevel == 4){
																}elseif(1 == 0){																	
															?>
                                                        	 <a href="scrp_delete_receipt.php?id=<?php echo doCleanOutput($result_row["RID"]);?>"  onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');" title="ลบข้อมูลการจ่ายเงินออกจากระบบ"><img src="decors/trashcan_icon.jpg" border="0" /></a>
                                                         	<?php }?>
                                                            
                                                            วันที่จ่าย <?php echo formatDateThai($receipt_row["ReceiptDate"])?> จำนวนเงิน <?php echo formatNumber($receipt_row["Amount"])?> บาท จ่ายโดย <?php echo formatPaymentName($receipt_row["PaymentMethod"]);?> <?php
															
															$paid_for = getFirstItem("select count(*) from payment where payment.rid = '".$result_row["RID"]."'");
															if($paid_for > 1){
																echo "<span style='color:green'>จ่ายให้ $paid_for แห่ง</span>";
															}
															?>
                                                         </div>
                                                         
                                                         <?php if(strlen($receipt_row["ReceiptNote"])>0){ ?>
                                                             <div style="padding:5px">
                                                             ชำระเพื่อ: <?php echo $receipt_row["ReceiptNote"]?>                                                             </div>
                                                         <?php } ?>
                                                         
                                                         
                                                         
                                                <?php
													
													}												
												?>
                                               
                                               
                                               
                                               
                                               
                                               
                                               
                                               
                                               
                                               
                                               </td>
                                           </tr>
                                          
                                          
                                          <tr>
                                            
                                            <td colspan="4"><hr /></td>
                                          </tr>
                                        </table></td>
                                      </tr>
                                    </table>
                                    
<table border="0">

	
  <tr>
    <td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input name="Conc_status" type="checkbox" id="Conc_status" value="1"  <?php echoChecked($lawful_values["Conc_status"])?>/>    </td>
    <td><span style="font-weight: bold;color:#006600;">
    <?php //if($sess_accesslevel ==4){
	 if(1==1){?>
        มาตรา 35 ให้สัมปทานฯ
     <?php }else{ ?>
        ปฏิบัติตามมาตรา 35
     <?php } ?></span></td>
    <td></td>
  </tr>
  <tr>
    <td ></td>
    <td>
    
    - มีผู้ใช้สิทธิ: <strong><?php 
	
			echo $curator_user;
	
		?></strong> คน, ผู้ถูกใช้สิทธิ: <strong><?php 
	
			echo getFirstItem("select 
								count(*) 
							from 
								curator 
							where 
								curator_lid = '".$lawful_values["LID"]."' 
							and curator_parent in(
							
								select curator_id 
								from 
								curator 
								where 
								curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0
							
							)");
	
		?></strong> คน
    
    
    
    </td>
    <td></td>
  </tr>
   <tr>
    <td ></td>
    <td>
    
   
    <a href="#" onclick="doPopSubCurator('3'); fireMyPopup('35_popup',1020,500); return false;">+ ดูรายละเอียด/เพิ่มข้อมูล คลิกที่นี่</a>
    
    </td>
    <td></td>
  </tr>
  
  
  
</table>

<script>
	function doPopSubCurator(parent){				
						
		document.getElementById('curator_parent').value = parent;
		
		if(parent == 3){
		
			document.getElementById('curator_input_forms').style.display = 'none';
		
		}else if(parent == 0){
			//parent -> have events
			document.getElementById('tr_curator_event').style.display = '';
			document.getElementById('tr_curator_disable').style.display = 'none';
			
			document.getElementById('the_parent').style.display = '';
			document.getElementById('the_child').style.display = 'none';
			
			document.getElementById('curator_input_forms').style.display = '';
			
		}else{
		
			document.getElementById('tr_curator_event').style.display = 'none';
			document.getElementById('tr_curator_disable').style.display = '';
			
			document.getElementById('the_parent').style.display = 'none';
			document.getElementById('the_child').style.display = '';
			
			document.getElementById('curator_input_forms').style.display = '';
		
		}
	}
</script>

 


<div align="center">
    	            
    	</div>


<!----------------------->


                                  <td>&nbsp;</td>
                                </tr>
                                
                                
                                
                                <tr>
                                  <td colspan="2"><div align="center">
                                  	<hr />
                                      <input name="Year" type="hidden" value="<?php echo $this_lawful_year;?>" />
                                      
                                      <?php if($sess_accesslevel == 5){ //exec can do nothing?>
                                      
                                      <?php }elseif($sess_accesslevel !=4){ ?>
                                      <input type="submit" name="update_lawful" id="update_lawful" value="ปรับปรุงข้อมูล" 
                                      onclick = "return confirm('ต้องการปรับปรุงข้อมูลการปฏิบัติตามกฎหมายนี้?');"
                                      />
                                      <?php }else{ ?>
                                      
                                      <!--<input type="submit" name="save_lawful" id="save_lawful" value="บันทึกข้อมูล" 
                                      onclick = "return confirm('บันทึกข้อมูลการปฏิบัติตามกฎหมายนี้?');"
                                      />-->
                                      
                                      <input type="submit" name="submit_lawful" id="submit_lawful" value="บันทึกข้อมูล" 
                                      onclick = "return confirm('บันทึกข้อมูลการปฏิบัติตามกฎหมายนี้?');"
                                      />
                                      
                                      <input type="button" name="cancel_lawful" id="cancel_lawful" value="ยกเลิกการแก้ไขข้อมูล" 
                                      onclick = "doConfirmCancel();"
                                      />
                                      
                                      <script>
									  	function doConfirmCancel(){
											if(confirm('ยกเลิกการแก้ไขข้อมูล และกลับไปหน้าแรก?')){
												document.location = "organization.php?id=<?php echo $this_id;?>";
											}else{
												return false;
											}
										}
									  </script>
                                      
                                      <?php } ?>
                                      
                                      <input name="CID" type="hidden" value="<?php echo $this_id; ?>" />
                                      <input name="LID" type="hidden" value="<?php echo $lawful_values["LID"]; ?>" />
                                  </div></td>
                                </tr>
                          </form>
                          
                          
                          <?php if($sess_accesslevel != 4){ ?>
                      <tr>
                        <td><hr />
                        <div style="font-weight: bold; padding: 5px; background-color:#efefef">การประกาศผ่านสื่อ</div>
                        
                      				  <table border="0" >
                                        	
                                          <tr>
                                          	<td>
                                            <?php 
												//generate reciept info
												$the_sql = "select * from announcecomp where CID = '".$this_id."' order by AID desc";
												
												$the_result = mysql_query($the_sql);
												
												while($result_row = mysql_fetch_array($the_result)){
													//echo "select * from receipt where RID = '".$result_row["RID"]."'";										
													$announcement_row = getFirstRow("select * from announcement where AID = '".$result_row["AID"]."'");
												
												?>
                                               			 <div style="padding:5px">ประกาศผ่านสื่อเลขที่ <a href="view_announce.php?id=<?php echo $announcement_row["AID"]?>"><?php echo $announcement_row["GovDocNo"]?></a> <!--ครั้งที่ <?php echo $announcement_row["ANum"]?> วันที่ <?php echo formatDateThai($announcement_row["ADate"])?> ประกาศทาง <?php echo getFirstItem("select newspaper_name from newspaper where newspaper_id = '".$announcement_row["newspaper_id"]."'");?>--></div>
                                                <?php
													
													}												
												?>
		                                         
                                         	</td>
                                         </tr>
                                          
                                          <tr>
                                            
                                            <td colspan="4"><hr />
                                            
                                             <?php if($sess_accesslevel!=5){?> 
                                            <a href="org_list.php?search_id=<?php echo $this_id?>&mode=announce&for_year=<?php echo $this_lawful_year;?>" style="font-weight: bold;">+ เพิ่มข้อมูลการแจ้งผ่านสื่อ</a>
                                            <?php }?>
                                            
                                            
                                            </td>
                                          </tr>
                                          
                                          <tr>
                                            
                                            <td colspan="4"><hr /></td>
                                          </tr>
                          </table>
                        
                        </td>
                      </tr>
                            <?php }// <?php if($sess_accesslevel != 4){ ?>   
                               
                  </table>
                </td>
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
        
    </td>
  </tr>
    
</table> 


  
      


 <div id="35_popup" style=" position:absolute; padding:3px; background-color:#006699; width: 1000px; display: none;  " >
  
 
 	<form  method="post">
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
                	<a href="#" onclick="fadeOutMyPopup('35_popup'); return false;">ปิดหน้าต่าง X </a>
                    </div>
                </td>
            </tr>                     
        
        </table>
        
         <div align="center">
        		 <table id="curator_input_forms" style="display:none;">
                        <tr bgcolor="#efefef">
                            <td colspan="10">
                                
                                <strong id="the_parent" style="display:none;">เพิ่มผู้ใช้สิทธิ</strong>        
                                <strong id="the_child" style="display:none;">เพิ่มผู้ถูกใช้สิทธิ</strong>        
                                
                                
                                </td>
                        </tr>
                        
                    <tr>
                            <td>
                            
                                เลขที่บัตรประชาชน        </td>
                            <td>
                            
                                <input type="text" name="curator_idcard" id="curator_idcard" maxlength="13" />
                                
                                <?php if(1 == 0){?>
	                                <input id="btn_get_data" type="button" value="ดึงข้อมูล" />        
                                <?php }?>
                                
                                </td>
                            <td>
                            
                                ชื่อ-นามสกุล        </td>
                            <td>
                            
                                <input type="text" name="curator_name" id="curator_name" />        </td>
                        </tr>
                        
                        
                        <tr>
                            <td>
                            
                                เพศ        </td>
                            <td>
                            
                                <select name="curator_gender" id="curator_gender">
                                    <option value="m">ชาย</option>
                                    <option value="f">หญิง</option>
                                  </select>        </td>
                            <td>
                            
                                อายุ        </td>
                            <td>
                            
                                <input name="curator_age" type="text" id="curator_age" size="10" />        </td>
                        </tr>
                        
                        
                        <tr id="tr_curator_event" style="display: none;">
                          <td valign="top">กิจกรรมตามมาตรา 35</td>
                          <td colspan="3">
                          
                          	<select name="curator_event" id="curator_event">
                                    <option value="การให้สัมปทาน">การให้สัมปทาน</option>
                                    <option value="จัดสถานที่จำหน่ายสินค้าหรือบริการ">จัดสถานที่จำหน่ายสินค้าหรือบริการ</option>
                                    <option value="จัดจ้างเหมาช่วงงาน">จัดจ้างเหมาช่วงงาน</option>
                                    <option value="ฝึกงาน">ฝึกงาน</option>
                                    <option value="การให้ความช่วยเหลืออื่นใด">การให้ความช่วยเหลืออื่นใด</option>
                            </select>
							
                            <br />
                            <textarea name="curator_event_desc" cols="40" rows="4"></textarea>                          </td>
                        </tr>
                        
                        
                        
                        <tr id="tr_curator_disable" style="display: none;">
                          <td valign="top">ลักษณะความพิการ</td>
                          <td colspan="3"><?php $do_hide_blank_dis = 1; include "ddl_disable_type.php";?></td>
                        </tr>
                        
                        <tr>
                            <td colspan="4">
                                <div align="center">
                                 
                                 
                                 <?php if($sess_accesslevel != 5){//exec can't do all these?>
                               <input name="do_add_curator" type="submit" value="เพิ่มข้อมูล" />
                                <?php }?>
                                <input name="" type="button" value="ปิดหน้าต่าง" onclick="fadeOutMyPopup('35_popup'); return false;" />
                                 
                                 
                                 <input name="curator_lid" type="hidden" value="<?php echo $lawful_values["LID"]; ?>" />
                                  <input name="curator_parent" id="curator_parent" type="hidden" value="0" />
                                </div>                            </td>
                        </tr>
                      </table>
        </div>
      
        
        <table width="700">
        	<tr>
            	<td colspan="5">
              	<strong>ผู้ใช้สิทธิ</strong> <a href="#" onclick="doPopSubCurator('0'); fireMyPopup('35_popup',1020,500); return false;">
                                	+ เพิ่มผู้ใช้สิทธิ
                                </a>
                </td>
            </tr>
             <tr bgcolor="#efefef">
            	 <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
                  <td><div align="center">ชื่อ</div></td>
                  <td><div align="center">เพศ</div></td>
                  <td><div align="center">อายุ</div></td>
                  <td><div align="center">เลขที่บัตรประชาชน</div></td>
                  <td><div align="center">กิจกรรม</div></td>
                  <td><div align="center"></div></td>
                  
			</tr>                                              
            
            
            <?php
			
				//get main curator
				$sql = "select * from curator where curator_lid = '".$lawful_values["LID"]."' and curator_parent = 0";
				//echo $sql;
				
				$org_result = mysql_query($sql);
				$total_records = 0;
				while ($post_row = mysql_fetch_array($org_result)) {			
					
					$total_records++;
			
			?>
                 <tr >
                  <td style="border-top:1px solid #999999; "><div align="center"><strong>#<?php echo $total_records;?></strong></div></td>
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
                                            
                                            $sql = "select * from lawful_employees where le_code = '$this_curator_idcard'
													and le_year = '$this_lawful_year'
													";
                                          
                                          	//echo $sql;
                                          
                                            $le_result = mysql_query($sql);
                                            
                                            while ($le_row = mysql_fetch_array($le_result)) {
										
                                          
                                          ?>
                                          
                                          <div>
                                            <a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">! พบในมาตรา 33</a>
                                          </div>
                                          
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
                                        
                                            $lawfulness_row = getFirstRow("select cid,year from lawfulness where lid = '".$le_row["curator_lid"]."'");
                                            
                                            $this_company_id = $lawfulness_row["CID"];
                                            $this_the_year = $lawfulness_row["Year"];
                                          
                                          ?>
                                          
                                          <div>
                                            <a href="organization.php?id=<?php echo $this_company_id;?>&curate=curate&focus=lawful&year=<?php echo $this_the_year;?>" style="color:#006600; text-decoration:underline;" target="_blank">! พบในสถานประกอบการอื่น</a>
                                          </div>
                                          
                                          <?php }?>
                  
                  </td>
                   <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_event"]);?></td>
                    <td style="border-top:1px solid #999999;"><?php echo doCleanOutput($post_row["curator_event_desc"]);?></td>
                    
                    <?php if($sess_accesslevel != 5 ){?>
                      <td><div align="center"><a href="scrp_delete_curator.php?id=<?php echo doCleanOutput($post_row["curator_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                  <?php }?>
                 
                </tr>                                  
                
                <tr>
                	<td colspan="7">
                    
                    	<div align="center">
                    	<table width="650">
                        	
                            <tr>
                                <td colspan="5">
                                <strong>ผู้ถูกใช้สิทธิ</strong> 
                                <a href="#" onclick="doPopSubCurator('<?php echo $post_row["curator_id"];?>'); fireMyPopup('35_popup',1020,500); return false;">
                                	+ เพิ่มผู้ถูกใช้สิทธิ
                                </a>
                                
                                </td>
                               	  
                                <?php 
								
									//get sub-curator
									$sql = "select 
												* 
											from 
												curator 
											where curator_parent = '".$post_row["curator_id"]."'";
									//echo $sql;
									
									$sub_result = mysql_query($sql);
									$total_sub = 0;
									while ($sub_row = mysql_fetch_array($sub_result)) {			
								
										$total_sub++;
									
								?>
                                 
                                 <?php if($total_sub == 1){?>
                                 
                                 <tr bgcolor="#efefef">
                                     <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
                                      <td><div align="center">ชื่อ</div></td>
                                      <td><div align="center">เพศ</div></td>
                                      <td><div align="center">อายุ</div></td>
                                      <td><div align="center">เลขที่บัตรประชาชน</div></td>
                                      <td><div align="center">ลักษณะความพิการ</div></td>
                                      
                                </tr> 
                                 
                                 <?php }?>
                                 
							
                                 <tr>
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
                                          
                                          <div>
                                            <a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">! พบในมาตรา 33</a>
                                          </div>
                                          
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
                                        
                                            $lawfulness_row = getFirstRow("select cid,year from lawfulness where lid = '".$le_row["curator_lid"]."'");
                                            
                                            $this_company_id = $lawfulness_row["CID"];
                                            $this_the_year = $lawfulness_row["Year"];
                                          
                                          ?>
                                          
                                          <div>
                                            <a href="organization.php?id=<?php echo $this_company_id;?>&curate=curate&focus=lawful&year=<?php echo $this_the_year;?>" style="color:#006600; text-decoration:underline;" target="_blank">! พบในสถานประกอบการอื่น</a>
                                          </div>
                                          
                                          <?php }?>
                                  
                                  </td>
                                  <td  valign="top"><?php echo doCleanOutput($sub_row["curator_disable_desc"]);?></td>
                                  <?php if($sess_accesslevel != 5 ){?>
                                      <td  valign="top"><div align="center"><a href="scrp_delete_curator.php?id=<?php echo doCleanOutput($sub_row["curator_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                  <?php }?>
                                  
                                </tr>  
                                
                                <?php } ?>
                                
                                
                            </tr>
						</table>                            
                    	</div>
                    
                    </td>
                </tr>
            
            <?php
				}
			?>
            
		</table>
        
        
        </div>
        
        
        <hr />
        
       
        
        
        
        </td>
	</tr>
            
    
   
	</table>
    </form>
</div>                

<?php

?>










  
 <div id="my_popup" style="position: absolute;  padding:3px; background-color:#006699; width: 1000px; display:none;   " >
                                  <table  bgcolor="#FFFFFF" width="1000" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;  ">
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
										
										
										var checkOK = "1234567890";
									   var checkStr = frm.le_code.value;
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
										 frm.le_code.focus();
										 return (false);
									   }
										
										
										if(frm.le_code.value.length != 13)
										{
											alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
											frm.le_code.focus();
											return (false);
										}
										
										
										
										//----
										return(true);									
									
									}
									-->
								
								</script>
                                    <form method="post" action="scrp_add_lawful_employee.php" onsubmit="return doValidateEmployeeInfo(this);">
                                    <tr bgcolor="#efefef">
                                    	<td colspan="10">
                                        <strong>ข้อมูลคนพิการที่ได้รับเข้าทำงาน</strong>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                      <td colspan="10">
                                      	<table border="0" align="center" bgcolor="#FFFFFF">
                                        	<tr>
                                            	<td colspan="2">
                                                <?php 
													if($_GET["delle"]=="delle"){
												?>							
													 <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* ข้อมูลได้ถูกลบออกจากฐานข้อมูลแล้ว</div>
												<?php
													}					
												?>
                                                <?php 
													if($_GET["le"]=="le"){
												?>							
													 <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มข้อมูลเรียบร้อย</div>
												<?php
													}					
												?>
                                                </td>
                                            </tr>
                                          <tr>
                                            <td>เลขที่บัตรประชาชน</td>
                                            <td>
                                            <input type="text" name="le_code" id="le_code" maxlength="13" />
                                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 4 ){//company and exec can't do all these?>
                                            <input id="btn_get_data" type="button" value="ดึงข้อมูล" onclick="return doGetData();" />
                                            <?php }?>
                                            
                                            <img id="img_get_data" src="decors/loading.gif" width="10" height="10" style="display:none;" />
                                            
                                            <script>
											
												function doGetData(){
												
													var the_id = document.getElementById('le_code').value;
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
													 document.getElementById('le_code').focus();
													 return (false);
												   }
													
													
													if(the_id.length != 13)
													{
														alert("เลขที่บัตรประชาชนต้องเป็นเลข 13 หลักเท่านั้น");
														document.getElementById('le_code').focus();
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
																
																document.getElementById('le_full_name').value =  someVar.PREFIX_NAME_ABBR + someVar.FIRST_NAME_THAI + " " + someVar.LAST_NAME_THAI;
																if(someVar.SEX_CODE == 'F'){
																	document.getElementById('le_gender').selectedIndex  = 1;
																}
																
																
																if(someVar.DEFORM_ID == 1 || someVar.DEFORM_ID == 6){
																	document.getElementById('le_disable_desc').selectedIndex  = 1;
																}
																if(someVar.DEFORM_ID == 2 || someVar.DEFORM_ID == 7){
																	document.getElementById('le_disable_desc').selectedIndex  = 2;
																}
																if(someVar.DEFORM_ID == 3 || someVar.DEFORM_ID == 8){
																	document.getElementById('le_disable_desc').selectedIndex  = 3;
																}
																if(someVar.DEFORM_ID == 4 || someVar.DEFORM_ID == 9){
																	document.getElementById('le_disable_desc').selectedIndex  = 4;
																}
																if(someVar.DEFORM_ID == 5 || someVar.DEFORM_ID == 10){
																	document.getElementById('le_disable_desc').selectedIndex  = 5;
																}
																if(someVar.DEFORM_ID == 6 || someVar.DEFORM_ID == 11){
																	document.getElementById('le_disable_desc').selectedIndex  = 6;
																}
																
																document.getElementById('le_age').value = someVar.BIRTH_DATE;
															
															}
															//
															
														} else {
															alert('การเชื่อมต่อผิดพลาด โปรดลองอีกครั้ง');
														}
													}
												
												}
											
											</script>
                                            
                                            </td>
                                            <td class="td_left_pad">ชื่อ-นามสกุล</td>
                                            <td><label>
                                              <input type="text" name="le_name" id="le_full_name" />
                                            </label></td>
                                          </tr>
                                          <tr>
                                            <td>เพศ</td>
                                            <td><label>
                                              <select name="le_gender" id="le_gender">
                                                <option value="m">ชาย</option>
                                                <option value="f">หญิง</option>
                                              </select>
                                            </label></td>
                                            <td class="td_left_pad">อายุ</td>
                                            <td><input name="le_age" type="text" id="le_age" size="10" /></td>
                                          </tr>
                                          <tr>
                                            <td>ลักษณะความพิการ</td>
                                            <td><?php include "ddl_disable_type.php";?></td>
                                            <td class="td_left_pad">เริ่มบรรจุงาน</td>
                                            <td>
                                            
                                            <?php
											
											$selector_name = "le_date";
											
											
											include ("date_selector_employee.php");
											
											?>
                                            
                                            </td>
                                          </tr>
                                          <tr>
                                            <td>ค่าจ้าง</td>
                                            <td><input name="le_wage" type="text" id="le_wage" size="10"  style="text-align:right;" onchange="addCommas('le_wage');"/> <?php
								  	
												include "js_format_currency.php";
											  
											  ?></td>
                                            <td class="td_left_pad">ตำแหน่งงาน</td>
                                            <td><input type="text" name="le_position" id="le_position" /></td>
                                          </tr>
                                          <tr>
                                            <td colspan="4"><div align="center">
                                            	<?php if($sess_accesslevel != 5){//exec can't do all these?>
                                                <input type="submit" name="button4" id="button4" value="เพิ่มข้อมูล" />
                                                <?php }?>
                                                <input name="" type="button" value="ปิดหน้าต่าง" onclick="fadeOutMyPopup('my_popup'); return false;" />
                                                
                                            </div></td>
                                          </tr>
                                          <input name="le_year" type="hidden" value="<?php echo $this_lawful_year;?>" />
                                          <input name="le_cid" type="hidden" value="<?php echo $this_id; ?>" />
                                         
                                    </form>
                                      </table>
                                      
                                      </td>
                                    </tr>
                                    
                                    
                                    
                                    <?php
					
						
										$get_org_sql = "SELECT *
														FROM lawful_employees
														
														
														
														where
															le_cid = '$this_id'
															and le_year = '$this_lawful_year'
														order by le_id asc
														";
										//echo $get_org_sql;
										$org_result = mysql_query($get_org_sql);
										$total_records = 1;
										while ($post_row = mysql_fetch_array($org_result)) {
									
											if($total_records == 1){
											?>
                                            
                                            <tr bgcolor="#efefef">
                                              <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
                                              <td><div align="center">ชื่อ</div></td>
                                              <td><div align="center">เพศ</div></td>
                                              <td><div align="center">อายุ</div></td>
                                              <td><div align="center">เลขที่บัตรประชาชน</div></td>
                                              <td width="140px"><div align="center">ลักษณะความพิการ</div></td>
                                              <td><div align="center">เริ่มบรรจุงาน </div></td>
                                              <td><div align="center">ค่าจ้าง </div></td>
                                              <td ><div align="center">ตำแหน่งงาน</div></td>
                                              <?php if($sess_accesslevel != 5 ){?>
                                              <td><div align="center">ลบข้อมูล</div></td>
                                              <?php }?>
                                            </tr>
                                            
                                            <?php
											
											}											
										
										?>     
                                    <tr>
                                      <td valign="top"><div align="center"><?php echo $total_records;?></div></td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_name"]);?></td>
                                      <td valign="top"><?php echo formatGender($post_row["le_gender"]);?></td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_age"]);?></td>
                                      <td valign="top">
									  <?php echo doCleanOutput($post_row["le_code"]);?>
                                      
										  <?php 
                                          
                                            //see if this le_id already in another ID
                                            
                                            $this_le_code = $post_row["le_code"];
                                            $this_le_id = $post_row["le_id"];
                                            $this_le_cid = $post_row["le_cid"];
                                            $this_le_year = $post_row["le_year"];
                                            
                                            $sql = "select * from lawful_employees where le_code = '$this_le_code'
                                                        and le_id != '$this_le_id' and le_year = '$this_lawful_year'";
                                          
                                          
                                          
                                            $le_result = mysql_query($sql);
                                            
                                            while ($le_row = mysql_fetch_array($le_result)) {
                                        
                                          
                                          ?>
                                          
                                          <div>
                                            <a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">! พบในสถานประกอบการอื่น</a>
                                          </div>
                                          
                                          <?php }?>
                                          
                                          
                                          <?php 
                                            
											   
											  $sql = "select 
													* 
													from 
													curator a, lawfulness b
													
													where 
													a.curator_lid 	= b.LID
													
													and
													curator_idcard = '$this_le_code'
												
													and
													year = '$this_lawful_year'
												";
                                          
                                          	//echo $sql;
										  
                                            $le_result = mysql_query($sql);
                                            
                                            while ($le_row = mysql_fetch_array($le_result)) {
                                        
                                           
                                            
                                            $this_company_id = $le_row["CID"];
                                            $this_the_year = $le_row["Year"];
                                          
                                          ?>
                                          
                                          <div>
                                            <a href="organization.php?id=<?php echo $this_company_id;?>&curate=curate&focus=lawful&year=<?php echo $this_the_year;?>" style="color:#006600; text-decoration:underline;" target="_blank">! พบในมาตรา 35</a>
                                          </div>
                                          
                                          <?php }?>
                                      
                                      </td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_disable_desc"]);?></td>
                                      <td valign="top"><?php echo formatDateThai($post_row["le_start_date"],0);?></td>
                                      <td valign="top"><div align="right"><?php echo formatNumber($post_row["le_wage"]);?></div></td>
                                      <td valign="top"><?php echo doCleanOutput($post_row["le_position"]);?></td>
                                     
                                     <?php if($sess_accesslevel != 5 ){?>
                                          <td valign="top"><div align="center"><a href="scrp_delete_lawful_employee.php?id=<?php echo doCleanOutput($post_row["le_id"]);?>&cid=<?php echo $this_id;?>&year=<?php echo $this_lawful_year;?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถแก้ไขการลบข้อมูลได้');"><img src="decors/cross_icon.gif" alt="" border="0" /></a></div></td>
                                      <?php }?>
                                      
                                      
                                    </tr>
                                    <?php $total_records++;}?>
                                    
                                    <?php if($total_records == 1){?>
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
</div>
<script>

<?php if($_GET["le"] == "le" || $_GET["delle"] == "delle"){ ?>
fireMyPopup('my_popup',1020,160);
<?php }?>

<?php 
if($this_focus == "official" || $this_focus == "lawful" || $this_focus == "general"){
?>
	showTab('<?php echo $this_focus;?>');
<?php
}else{
//if($mode=="new"){
?>


document.getElementById('lawful').style.display = 'none';

<?php if($sess_accesslevel !=4){ ?>
document.getElementById('official').style.display = 'none';
<?php } ?>

<?php
}
?>
</script>
<script>
										  	
											
	function alertContents() {
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			//alert(http_request.responseText.trim()); 
			document.getElementById("loading_"+http_request.responseText.trim()).style.display = 'none';
		} else {
			//alert('There was a problem with the request.');
		}
	}
}
  </script>
  
   <?php if($_GET["curate"] == "curate" || 	$carate == "curate"){?>
	<script>
    
		
        doPopSubCurator('3');
		fireMyPopup('35_popup',1020,160);
   		// alert("what");
    </script>
    <?php }?>
</body>
</html>
