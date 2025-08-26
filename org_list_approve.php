<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if($_GET["mode"]=="search"){
		$mode = "search";
		
	}elseif($_GET["mode"]=="letters"){
		$mode = "letters";
	}elseif($_GET["mode"]=="announce"){
		$mode = "announce";
	}elseif($_GET["mode"]=="payment"){
	
	
		$mode = "payment";
		
		if($_GET["payback"]=="1"){
			$sub_mode = "payback";
		}
		
	}elseif($_GET["mode"]=="add_company_payment"){
		$mode = "add_company_payment";
		$this_id = $_GET["id"]*1;
		$receipt_number = getFirstItem("select ReceiptNo from receipt where RID='$this_id'");
		$book_number = getFirstItem("select BookReceiptNo from receipt where RID='$this_id'");
	}elseif($_GET["mode"]=="add_company_announce"){
		$mode = "add_company_announce";
		$this_id = $_GET["id"]*1;
		$this_gov_doc_id = $this_id;
		$GovDocNo = getFirstItem("select GovDocNo from announcement where AID='$this_id'");
	
	
	}elseif($_GET["mode"]=="email"){
		
		//		NEW AS OF 20140215
	
		$mode = "email";
	
	
	}else{
		$mode = "normal";
	}
	
	if(is_numeric($_GET["search_id"])){						
		$have_search_id = 1;	
		
		$company_name_row = getFirstRow("select CompanyNameThai,CompanyTypeCode from company where CID = '".$_GET["search_id"]."'");
		
		$company_name_to_use = formatCompanyName($company_name_row["CompanyNameThai"],$company_name_row["CompanyTypeCode"]);
		
		if(strlen($_GET["for_year"])==4){
			$for_year = $_GET["for_year"];
		}
	}
	//print_r($_POST);
	
	//yoes 20151025
	if($_GET["lawful_submitted"]){	
		$_POST[lawful_submitted] = $_GET["lawful_submitted"]*1;
	
	}
	if($_GET["ddl_year"]){	
		$_POST[ddl_year] = $_GET["ddl_year"]*1;
	
	}



	if($_GET["type"] == "hold"){
	
		//withholding letters
		$type = "hold";
	
	}

?>
<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                   
                    
                    <h2 class="default_h1" style="margin:0; padding:0;"  >
                    <?php if($mode == "search"){ ?>
                    	ค้นหารายชื่อ<?php echo $the_company_word;?>
                    <?php }elseif($mode == "letters") { ?>
                    
                    	<?php if($type == "hold"){?>
	                    	การแจ้งอายัด
                        <?php }else{?>
                        	การส่งจดหมายแจ้ง<?php echo $the_company_word;?>
                        <?php }?>
                        
                        
					<?php }elseif($sub_mode == "payback") { ?>
                        การขอเงินคืนจากกองทุนฯ                    
                    <?php }elseif($mode == "payment") { ?>
                    	การส่งเงินเข้ากองทุนฯ
                    <?php }elseif($mode == "add_company_payment") { ?>
                    	เพิ่ม<?php echo $the_company_word;?>สำหรับใบเสร็จรับเงิน: <?php echo $receipt_number;?>
                     <?php }elseif($mode == "add_company_announce") { ?>
                    	การแจ้งผ่านสื่อเลขที่: <?php echo $GovDocNo;?>
					<?php }elseif($mode == "announce") { ?>
                    	เพิ่มการประกาศผ่านสื่อ
                        
                        
                        
                   	<?php }elseif($mode == "email") { /// NEW AS OF 20140215?>
                    	
                        
                        ระบบส่งจดหมายแจ้งสถานประกอบการออนไลน์ (Email)                         
                        
                        
					
		     		<?php }else { ?>
						สถานประกอบการยื่นแบบออนไลน์
                    <?php } ?>
                    
                  </h2>
                    
                    <?php if($mode == "payment" && $have_search_id) { ?>
                    
                   	 <div style="padding:5px 0 10px 2px">
                     <?php if($sess_accesslevel !=4){?>
                     <a href="org_list.php">ค้นหารายชื่อ<?php echo $the_company_word;?></a> >
                     <?php } ?>
                     
                     
                     <?php if($sub_mode == "payback") { ?>
                     
                     	<a href="organization.php?id=<?php echo $_GET["search_id"];?>&focus=lawful&year=<?php echo $for_year;?>"><?php echo $company_name_to_use;?></a> > การขอเงินคืนจากกองทุนฯ</div>
                     
                     <?PHP }else{ ?>
                      <a href="organization.php?id=<?php echo $_GET["search_id"];?>&focus=lawful&year=<?php echo $for_year;?>"><?php echo $company_name_to_use;?></a> > การส่งเงินเข้ากองทุนฯ</div>
                      
                      <?php }?>
                     
                     
                    <?php }elseif($mode == "letters" && $have_search_id) { ?>
                            <div style="padding:5px 0 10px 2px"><a href="org_list.php">ค้นหารายชื่อ<?php echo $the_company_word;?></a> > <a href="organization.php?id=<?php echo $_GET["search_id"];?>&focus=official&year=<?php echo $for_year;?>"><?php echo $company_name_to_use;?></a> > <?php if($type == "hold"){?>
                                    การแจ้งอายัด
                                <?php }else{?>
                                    การส่งจดหมายแจ้ง<?php echo $the_company_word;?>
                                <?php }?>
                        
                        
                    </div>
                        
                        
                    <?php }elseif($mode == "announce" && $have_search_id) { ?>
                    	<div style="padding:5px 0 10px 2px"><a href="org_list.php">ค้นหารายชื่อ<?php echo $the_company_word;?></a> > <a href="organization.php?id=<?php echo $_GET["search_id"];?>&focus=official&year=<?php echo $for_year;?>"><?php echo $company_name_to_use;?></a> > การส่งจดหมายแจ้ง<?php echo $the_company_word;?></div>
                        
                        
                    <?php }elseif($mode == "add_company_payment" ) { ?>
                    	<div style="padding:5px 0 10px 2px"><a href="payment_list.php">ใบเสร็จรับเิงินทั้งหมด</a> > <a href="view_payment.php?id=<?php echo $this_id;?>">ใบเสร็จเลขที่ <?php echo $receipt_number;?></a> > เพิ่ม<?php echo $the_company_word;?>สำหรับใบเสร็จ</div>
                        
                    <?php }elseif($mode == "add_company_announce" ) { ?>
                    	<div style="padding:5px 0 10px 2px"><a href="announce_list.php">การประกาศผ่านสื่อทั้งหมด</a> > <a href="view_announce.php?id=<?php echo $this_id;?>">หนังสือเลขที่ <?php echo $GovDocNo;?></a> > เพิ่ม<?php echo $the_company_word;?>สำหรับการแจ้งผ่านสื่อ</div>
                    <?php }?>
                    
                    <form method="post" action="#org_list" <?php if($mode == "letters" && $type != "hold") {?>onsubmit="return validate_province(this);"<?php }?> id="search_form">
                    <?php if($mode == "letters") {?>
                    <script language='javascript'>
						<!--
						function validate_province(frm) {
							
							//----
							ddl_value = frm.Province.options[frm.Province.selectedIndex].value;
							//alert(ddl_value);
							if(ddl_value == "")
							{
								alert("กรุณาเลือกจังหวัด");
								frm.Province.focus();
								return (false);
							}
							
							//----
							return(true);									
						
						}
						-->
					
					</script>
                    <?php }?>
                    <?php if($mode == "search"){ 
						//advanced search and normal search?>
                    <table style=" padding:10px 0 0px 0; " id="general">
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:0 0 5px 0;">ข้อมูลทั่วไป</div></td>
                      </tr>
                      <tr>
                        <td >ประจำปี:</td>
                        <td><?php include "ddl_year.php";?></td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td>สถานะ</td>
                        <td><select name="LawfulFlag" id="LawfulFlag_search" onChange="doToggleNoRecipientFlag();">
    <option value="" selected="selected">-- all --</option>
    <option value="1" <?php if($_POST["LawfulFlag"] == "1"){echo "selected='selected'";}?>>ทำตามกฏหมาย</option>
    <option value="0" <?php if($_POST["LawfulFlag"] == "0"){echo "selected='selected'";}?>>ไม่ทำตามกฏหมาย</option>
    <option value="2" <?php if($_POST["LawfulFlag"] == "2"){echo "selected='selected'";}?>>ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน</option>
    <option value="3" <?php if($_POST["LawfulFlag"] == "3"){echo "selected='selected'";}?>>ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?></option>
</select></td>
                        <td class="td_left_pad"><span id="NoRecipientFlag"><input name="NoRecipientFlag" id="NoRecipientFlag2"  type="checkbox" value="1" <?php if($_POST["NoRecipientFlag"]){echo "checked";}?>>
ไม่มีคนรับเอกสาร</span></td><script>						
														
	function doToggleNoRecipientFlag(){
	
		the_lawful = document.getElementById("LawfulFlag_search").value;
	
		//alert(the_lawful);
	
		document.getElementById("NoRecipientFlag").style.display = "none";
		document.getElementById("NoRecipientFlag2").checked = false;
		
		if(the_lawful == "0"){
			document.getElementById("NoRecipientFlag").style.display = "";
		}
	}	
	
	doToggleNoRecipientFlag();							
	
	
</script>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td><?php echo $the_code_word;?>: </td>
                        <td><label>
                          <input type="text" name="CompanyCode" value="<?php echo $_POST["CompanyCode"];?>" />
                        </label></td>
                        
                         <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
                       
                        <?php }else{?>
                             <td class="td_left_pad">
                            
                             เลขที่ประจำตัวผู้เสียภาษีอากร: 
                            
                            </td>
                            <td><input type="text" name="TaxID" value="<?php echo $_POST["TaxID"];?>" /></td>
                            
                        <?php }?>
                       
                        
                      </tr>
                      
                        <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
                       
                        <?php }else{?>
                             <tr>
                          
                              
                                <td>เลขที่สาขา:</td>
                                <td><input type="text" name="BranchCode" value="<?php echo $_POST["BranchCode"];?>" /></td>
                                
                                
                                <td class="td_left_pad">&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                        <?php }?>
                     
                      
                      
                      <tr>
                        <td > 
                        
                        <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
                        ประเภทหน่วยงาน:
                        <?php }else{?>
                        ประเภทธุรกิจ:
                        <?php }?>
                        
                        </td>
                        <td><?php include "ddl_org_type.php";?></td>
                        
                        <?php if($sess_accesslevel == 6 ||  $sess_accesslevel == 7){?> 
                        
                        
                        <?php }else{?>
                            <td class="td_left_pad"> ประเภทกิจการ:</td>
                            <td><?php include "ddl_bus_type.php";?></td>
                        
                        <?php }?>
                        
                        
                      </tr>
                      <tr>
                        <td>
                        
                       
                        
                         <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
                        ชื่อหน่วยงาน (ภาษาไทย): 
                        <?php }else{?>
                             ชื่อบริษัท (ภาษาไทย): 
                        <?php }?>
                        
                        </td>
                        <td><input type="text" name="CompanyNameThai" value="<?php echo $_POST["CompanyNameThai"];?>" /></td>
                        <td class="td_left_pad">
                        
                        
                         <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
                        ชื่อหน่วยงาน (ภาษาอังกฤษ): 
                        <?php }else{?>
                             ชื่อบริษัท (ภาษาอังกฤษ): 
                        <?php }?>
                         
                         
                         </td>
                        <td><input type="text" name="CompanyNameEng" value="<?php echo $_POST["CompanyNameEng"];?>" /></td>
                      </tr>
                      <tr>
                        <td > จำนวน<?php echo $the_employees_word;?>:</td>
                        <td><input type="text" name="Employees" value="<?php echo $_POST["Employees"];?>" />
                          คน</td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="4"><div style="font-weight: bold; padding:5px 0 5px 0;">ที่อยู่</div><a id="org_list"></a></td>
                      </tr>
                      <tr>
                        <td>สถานที่ตั้งเลขที: </td>
                        <td><label>
                          <input type="text" name="Address1" value="<?php echo $_POST["Address1"];?>" />
                        </label></td>
                        <td class="td_left_pad">ซอย: </td>
                        <td><input type="text" name="Soi" value="<?php echo $_POST["Soi"];?>" /></td>
                      </tr>
                      <tr>
                        <td>หมู่:</td>
                        <td><input type="text" name="Moo" value="<?php echo $_POST["Moo"];?>" /></td>
                        <td class="td_left_pad"> ถนน:</td>
                        <td><input type="text" name="Road" value="<?php echo $_POST["Road"];?>" /></td>
                      </tr>
                      <tr>
                        <td>ตำบล/แขวง: </td>
                        <td><input type="text" name="Subdistrict" value="<?php echo $_POST["Subdistrict"];?>" /></td>
                        <td class="td_left_pad"> อำเภอ/เขต:</td>
                        <td><input type="text" name="District" value="<?php echo $_POST["District"];?>" /></td>
                      </tr>
                      <tr>
                        <td>จังหวัด: </td>
                        <td><?php include "ddl_org_province.php"?></td>
                        <td class="td_left_pad"> รหัสไปรษณีย์:</td>
                        <td><input type="text" name="Zip" value="<?php echo $_POST["Zip"];?>" /></td>
                      </tr>
                      <tr>
                        <td>โทรศัพท์:</td>
                        <td><input type="text" name="Telephone" value="<?php echo $_POST["Telephone"];?>" /></td>
                        <td class="td_left_pad">email:</td>
                        <td><input type="text" name="email" value="<?php echo $_POST["email"];?>" /></td>
                      </tr>
                      <tr>
                        <td>เวปไซต์:</td>
                        <td><input type="text" name="org_website" value="<?php echo $_POST["org_website"];?>" /></td>
                        <td class="td_left_pad">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td colspan="4">
                          <div align="center">
                          	<hr />
                            <input type="submit" name="mini_search" id="button" value="แสดง" />
                            <input name="advanced_search" type="hidden" value="1">
                          </div>                        </td>
                      </tr>
              </table>
                       
                    <?php 
					
						}elseif(!$have_search_id) { 
						
						//normal search
						
						?>
                      
                            
                     <a id="org_list"></a>
                    
                    <?php if( $mode == "normal") { ?>
	                    <div style="padding-top:10px; font-weight: bold;">1. ค้นหา<?php echo $the_company_word;?></div>
                   
                   
                    <?php }elseif($mode == "email") { ?>
                   
                   			
                                <div style="padding-top:10px; font-weight: bold;">
                            
                              1. ค้นหา<?php echo $the_company_word;?>ที่ต้องการส่ง email
                           
                           
                            
                            
                            </div>
                   
                   
                   
                   
                    <?php }elseif($mode == "letters") { ?>
                    	
                        
                        <div style="padding-top:10px; font-weight: bold;">
                        
                        <?php if($type == "hold"){?>
                             1. ค้นหา<?php echo $the_company_word;?>ที่ต้องการแจ้งอายัด
						<?php }else{?>
                             1. ค้นหา<?php echo $the_company_word;?>ที่ต้องการส่งจดหมายแจ้ง
                        <?php }?>
                       
                        
                        
                        </div>
                        
                        
                        
                    <?php }elseif($mode == "payment" || $mode == "add_company_payment") { ?>
                    	<div style="padding-top:10px; font-weight: bold;">1. ค้นหา<?php echo $the_company_word;?>ที่ต้องสร้างข้อมูลใบเสร็จรับเงิน</div>
                    <?php }elseif($mode == "announce" || $mode == "add_company_announce") { ?>
                    	<div style="padding-top:10px; font-weight: bold;">1. ค้นหา<?php echo $the_company_word;?>ที่ต้องการประกาศผ่านสื่อ</div>
                    <?php } ?>
                     
                    <table style=" padding:10px 0 0px 0;">
                    
                    <tr>
                    	  <td bgcolor="#efefef">ประจำปี:</td>
                    	  <td><?php include "ddl_year.php";?></td>
                    	  <td >&nbsp;</td>
                    	  <td>&nbsp;</td>
                  	  </tr>
                    
                    
                    	<tr>
                    	  <td bgcolor="#efefef">สถานะ: </td>
                          <td ><select name="LawfulFlag" id="LawfulFlag_search" onChange="doToggleNoRecipientFlag();">
    <option value="" selected="selected">-- all --</option>
    <option value="1" <?php if($_POST["LawfulFlag"] == "1"){echo "selected='selected'";}?>>ทำตามกฏหมาย</option>
    <option value="0" <?php if($_POST["LawfulFlag"] == "0"){echo "selected='selected'";}?>>ไม่ทำตามกฏหมาย</option>
    <option value="2" <?php if($_POST["LawfulFlag"] == "2"){echo "selected='selected'";}?>>ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน</option>
    <option value="3" <?php if($_POST["LawfulFlag"] == "3"){echo "selected='selected'";}?>>ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?></option>
</select></td>
                          <td colspan="2" ><span id="NoRecipientFlag"><input name="NoRecipientFlag" id="NoRecipientFlag2"  type="checkbox" value="1" <?php if($_POST["NoRecipientFlag"]){echo "checked";}?>>
ไม่มีคนรับเอกสาร</span></td><script>						
														
	function doToggleNoRecipientFlag(){
	
		the_lawful = document.getElementById("LawfulFlag_search").value;
	
		//alert(the_lawful);
	
		document.getElementById("NoRecipientFlag").style.display = "none";
		document.getElementById("NoRecipientFlag2").checked = false;
		
		if(the_lawful == "0"){
			document.getElementById("NoRecipientFlag").style.display = "";
		}
	}	
	
	doToggleNoRecipientFlag();							
	
	
</script>
                   	  </tr>
                      
                      
                      
                    	<tr>
                        	
                            
                            
                    	  <td bgcolor="#efefef">ชื่อ:  </td>
                    	  <td>
                          	                     <input type="text" name="CompanyNameThai" value="<?php echo $_POST["CompanyNameThai"];?>" />     </td>
                        	<td bgcolor="#efefef">
                            <?php echo $the_code_word;?>:</td>
                            <td>
                                <input type="text" name="CompanyCode" value="<?php echo $_POST["CompanyCode"];?>" />                      </td>
                      </tr>
                    	<tr>
                    	  <td bgcolor="#efefef">
                          
                           <?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
                        ประเภทหน่วยงาน:
                        <?php }else{?>
                        ประเภทธุรกิจ:
                        <?php }?>
                          
                          </td>
                    	  <td><?php include "ddl_org_type.php";?>                          </td>
                          
                            <?php if($sess_accesslevel == 6 ||  $sess_accesslevel == 7){?> 
                        
                        
                        	<?php }else{?>
                                  <td bgcolor="#efefef"> ประเภทกิจการ:</td>
                                  <td><?php include "ddl_bus_type.php";?></td>
                                  
							<?php }?>                                  
                          
                          
                   	  </tr>
                    	<tr>
                    	  <td bgcolor="#efefef"> จังหวัด: </td>
                    	  <td><?php include "ddl_org_province.php";?>                          </td>
                    	  <td >&nbsp;</td>
                    	  <td>&nbsp;</td>
                  	  </tr>
                      
                      
                      
                      
                      
                      <?php if($sess_accesslevel == 1 || $sess_accesslevel == 3 ){?>
                    	<tr>
                    	  <td bgcolor="#efefef">ข้อมูลจากสถานประกอบการ</td>
                    	  <td colspan="3">
                          
                          
                          <select name="lawful_submitted" id="lawful_submitted" >
                                <option value="" selected="selected">-- all --</option>
                                <option value="2" <?php if($_POST["lawful_submitted"] == "2"){echo "selected='selected'";}?>>ข้อมูลจากสถานประกอบการ บันทึกเข้าระบบแล้ว</option>
                                <option value="1" <?php if($_POST["lawful_submitted"] == "1"){echo "selected='selected'";}?>>ข้อมูลจากสถานประกอบการ ยังไม่บันทึกเข้าระบบ </option>
                                
                            </select>
                          
                          </td>
                    	  
                  	  </tr>
                      
                      
                      <?php 
					  
					  $cur_company_year = date("Y"); 
							
						if(isset($_POST['ddl_year'])){
							$cur_company_year = $_POST['ddl_year'];
						}
											  
					  	//how many out-standing un-saved data?
						$unsaved_company_info = getFirstItem("select 
														count(*)
													from 
														lawfulness_company 
													where 
														Year = '$cur_company_year' 
														and 
														
														(lawful_submitted = 1)
														
														");
					  
					  	
						if($unsaved_company_info && 1==0){
					  
					  ?>
                      
                          <tr>
                              <td bgcolor=""></td>
                              <td colspan="3">
                              
                              <font color="#990000">*** มีข้อมูลของสถานประกอบการ <?php echo $unsaved_company_info; ?> แห่งที่ยังไม่ได้บันทึกเข้าระบบ</font>
                              
                              </td>
                              
                          </tr>
                      
                      
                      	<?php }?>
                      
                    <?php }?>
                        
                        
                    	<tr>
                    	  <td colspan="6" align="right">
                          
                           
                            <input type="submit" value="แสดง" name="mini_search"/>
                            
                           
                          <hr />
                          
                          </td>
                   	  </tr>
                      
                      
                    </table>
                    <?php } ?>
                   
                   <?php 
						if($_GET["mode"]=="payment" && $_GET["duped_key"]=="duped_key"){
					?>							
                      <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <a href="view_payment.php?id=<?php echo $_GET["pay_id"]?>">ใบเสร็จเล่มที่ <?php echo $_GET["book_num"]?> ใบเสร็จเลขที่ <?php echo $_GET["pay_num"]?></a> มีอยู่ในระบบแล้ว กรุณาใส่เล่มที่/ใบเสร็จเลขที่ใหม่</div>
                         <hr />
                    <?php
						}					
					?>
                    
                    <?php 
						if($_GET["mode"]=="letter" && $_GET["duped_key"]=="duped_key"){
					?>							
                         <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <a href="view_letter.php?id=<?php echo $_GET["doc_id"]?>">หนังสือเลขที่ <?php echo $_GET["doc_no"]?> ครั้งที่ <?php echo $_GET["doc_seq"]?></a> มีอยู่ในระบบแล้ว กรุณาใส่หนังสือเลขที่/ครั้งที่ใหม่</div>
                         <hr />
                    <?php
						}					
					?>
                  	
                	
                    <?php 
						if($_GET["letter_added"]=="letter_added"){
					?>							
                         <div style="color:#006600; padding:5px 0 5px 0; font-weight: bold;">* บันทึกข้อมูลการส่งจดหมายแจ้ง<?php echo $the_company_word;?>เรียบร้อยแล้ว</div>
                         <hr />
                    <?php
						}					
					?>
                    
                    <?php
					
					$condition_sql = "";
					if(strlen($_SESSION["org_list_condition"]) > 0){
						//$condition_sql = $_SESSION["org_list_condition"];
					}
					
					
					
					
					
					
					
					////////////////////////
					////////////////
					////////////////		START BUILDING CONDITIONS
					/////////////////
					/////////////////////////////
					
					
					
					$input_fields = array(
						'Employees'
						,'CompanyCode'
						
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
						
						
						,'NoRecipientFlag'
						
						
						
						);
					//if has $_post then do filter
					//print_r($_POST);
					$use_condition = 0;
					$condition_sql = " and 1=1";
					
					
					///yoes 20130730 - add GOV only filter
					if($sess_accesslevel == 6 || $sess_accesslevel == 7){
						
						$condition_sql .= " and z.CompanyTypeCode >= 200  and z.CompanyTypeCode < 300";
						
					}else{
						
						$condition_sql .= " and z.CompanyTypeCode < 200";
						
					}
					
					
					
					
					
					
					for($i = 0; $i < count($input_fields); $i++){
						
						if(strlen($_POST[$input_fields[$i]])>0){
							
							$use_condition = 1;
							
							if($input_fields[$i] == "Province"  ){
								$condition_sql .= " and z.$input_fields[$i] like '".doCleanInput($_POST[$input_fields[$i]])."'";
							}elseif($input_fields[$i] == "Employees" ){
								$condition_sql .= " and y.$input_fields[$i] >= '".doCleanInput($_POST[$input_fields[$i]])."'";
							}else{
								$condition_sql .= " and z.$input_fields[$i] like '%".doCleanInput($_POST[$input_fields[$i]])."%'";
							}
							
						}
					}	
					
					//special search condition for company name th
					//make it so it filter as %LIKE% instead
					if(strlen($_POST["CompanyNameThai"]) > 0){
						
						
						$name_exploded_array = explode(" ",doCleanInput($_POST["CompanyNameThai"]));
						
						//print_r($name_exploded_array);
						for($i=0; $i<count($name_exploded_array);$i++){
						
							if(strlen(trim($name_exploded_array[$i]))>0){
								//echo $name_exploded_array[$i];
								$use_condition = 1;
								$condition_sql .= " and z.CompanyNameThai like '%".doCleanInput($name_exploded_array[$i])."%'";
								
							}
						
						}
						
					}
					
					if(strlen($_POST["LawfulFlag"]) > 0){
					
						//added Yoes april 03
						$lawful_condition = " and y.LawfulStatus = '".$_POST["LawfulFlag"]."'";

						//if non-lawful then also show records that didn't have lawfulness
						if($_POST["LawfulFlag"] == "0"){
							$lawful_condition = " and (y.LawfulStatus = '0' or y.LawfulStatus is null)";
						}
					
					}
					
										
					//
					if($have_search_id){
						$use_condition = 1;
						$condition_sql .= " and z.CID = '".doCleanInput($_GET["search_id"])."'";
					}
					
					//echo $condition_sql;
					//save condition to session
					//$_SESSION["org_list_condition"] = $condition_sql;
					
					if($mode == "add_company_announce"){
						
						$get_announce_org_sql = "select * from announcecomp where AID = '$this_gov_doc_id'";
						
						$announce_org_result = mysql_query($get_announce_org_sql);
						
						while ($announce_org_post_row = mysql_fetch_array($announce_org_result)) {
							$the_cid = $announce_org_post_row["CID"];
							$condition_sql .= " and CID != '$the_cid'";
						}						
						
					}
					
					$cur_year = date("Y"); 
						
					if(isset($_POST['ddl_year'])){
						$cur_year = $_POST['ddl_year'];
					}
					
					if($for_year){
						$cur_year = $for_year;
					}
																	
					//echo $cur_year;
					
					
					//YEAR - BRANCH thing
					//echo $cur_year;
					if($cur_year >= 2013){
					
						//show main branch only
						$condition_sql .= " and BranchCode < 1";
						$is_2013 = 1;
					
					}
					
					
					if($_POST["advanced_search"] == 1){
					
						//$the_outer_join = " left outer ";
					
					}
					
					
					
					////////////////////////
					////////////////
					////////////////		END BUILDING CONDITIONS
					/////////////////
					/////////////////////////////
					
					if($_POST["lawful_submitted"]){
						
						$the_lawful_submitted = doCleanInput($_POST["lawful_submitted"]);
						
						$lawfulness_company_join = "left join lawfulness_company xxx 
											on 
											z.CID = xxx.CID ";
						
						$lawfulness_company_condition = "and xxx.Year = '$cur_year' 
												and (xxx.lawful_submitted = '$the_lawful_submitted')";
						
					}else{
					
						//default for thi spage -> just show everything
						$lawfulness_company_join = "left join lawfulness_company xxx 
											on 
											z.CID = xxx.CID ";
						
						$lawfulness_company_condition = "and xxx.Year = '$cur_year' 
												and (xxx.lawful_submitted = '1' or xxx.lawful_submitted = '2')";
						
					}
					
					
					$the_sql = "
										SELECT count(z.CID)
										FROM company z
										
										
										LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
										LEFT outer JOIN provinces c ON z.Province = c.province_id
										$the_outer_join JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year'
										
										$lawfulness_company_join
										
										
										where
											1=1														
											$condition_sql
											
											$lawful_condition
											
											$lawfulness_company_condition
										
									";
					
				
					
					
					if($is_2013){
						
							$the_sql = "SELECT 
						
											count(*)
											
						
										FROM company z
										
										
										LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
										LEFT outer JOIN provinces c ON z.province = c.province_id
										$the_outer_join JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year' 
										
										
										$lawfulness_company_join
											
										
										
										where
											1=1														
											$condition_sql
											
											$lawful_condition
										
											$lawfulness_company_condition							
											
										
										
										";
						
						
						}
						
					//yoes 20141014 -> if is provincial the -> only show related provine
					if($sess_meta && $sess_accesslevel == 3){
						$the_sql .= " and z.Province like '".$sess_meta."'";
					}
						
						
					//echo $the_sql ;
					
					
					if($is_2013){
						$record_count_all = (getFirstItem($the_sql));
					}else{			
						$record_count_all = getFirstItem($the_sql);
					}
								

					?>
                    
                    <?php
						//pagination stuffs
						
						$per_page = 20;
						$num_page = ceil($record_count_all/$per_page);
						//echo $num_page;
						
						$cur_page = 1;
						if(is_numeric($_POST["start_page"]) && $_POST["start_page"] <= $num_page && $_POST["start_page"] > 0){
							$cur_page = $_POST["start_page"];
						}
						
						$starting_index = 0;
						if($cur_page > 1){
							$starting_index = ($cur_page-1) * $per_page;						
						}
					?>
                    
                    
                    <table border="0" width="100%" >
                    	<?php 
						
							if(
								
								(
								$mode == "letters" 
								|| $mode == "payment" || $mode == "add_company_payment" 
								|| $mode == "announce" || $mode == "add_company_announce" 
								|| $mode == "normal" || $mode == "search"
								
								
								//NEW AS OF 20140215
								|| $mode == "email"
								
								
								)
																
								&& !isset($_POST["mini_search"])								
								
								&& !$have_search_id
								
								&& !isset($_POST["start_page"])
							
							){
								//$do_hide_company_list =1;
							}
							
							if(
							
								$have_search_id	
								||
								$do_hide_company_list
							){
						
								//dont show this if have above conditions
									
							}else{
						
								//else show everything
							
						?>
                    	<tr>
                        	
                            
                        	<td align="left">
                            
                            <?php if($mode == "letters") { ?>
                            
                            
                            
								<?php if($type == "hold"){?>
                                <div style="padding:10px 0 10px 0; font-weight: bold;">2. เลือก<?php echo $the_company_word;?>ที่ต้องการแจ้งอายัด</div>
                                <?php }else{?>
                                    <div style="padding:10px 0 10px 0; font-weight: bold;">2. เลือก<?php echo $the_company_word;?>ที่ต้องการส่งจดหมายแจ้งถึง</div>
                                <?php }?>                        
                            
                            
                            <?php }elseif($mode == "email") { ?>
                            
                            
                            		<div style="padding:10px 0 10px 0; font-weight: bold;">2. เลือก<?php echo $the_company_word;?>ที่ต้องการส่ง email แจ้งถึง</div>
                            
                            <?php }elseif($mode == "payment" || $mode == "add_company_payment") { ?>
                            
                            
                            <div style="padding:10px 0 10px 0; font-weight: bold;">2. เลือก<?php echo $the_company_word;?>เพื่อเพิ่มข้อมูลใบเสร็จ</div>
                            <?php }elseif($mode == "announce" || $mode == "add_company_announce") { ?>
                            <div style="padding:10px 0 10px 0; font-weight: bold;">2. เลือก<?php echo $the_company_word;?>เพื่อเพิ่มข้อมูลการประกาศผ่านสื่อ</div>
                      	  <?php } ?>
                            
                             <font color="#006699">แสดงข้อมูล <?php echo $starting_index+1;?>-<?php echo ($record_count_all < $starting_index+$per_page) ? $record_count_all : $starting_index+$per_page;?> จากทั้งหมด <?php echo $record_count_all; ?> รายการ</font> 
                            </td>
                            <td align="right" valign="bottom">
                            <?php 
								//if($use_condition == "0"){
								if(1==1){
							?>
                            <div style="padding:5px 0 0px 0;" align="right">
                            แสดงข้อมูล:
                            
                            <select name="start_page" onchange="this.form.submit()">
                            	<?php 
									for($i = 1; $i <= $num_page; $i++){
								?>
                            	<option value="<?php echo $i;?>" <?php if($_POST["start_page"]==$i){echo "selected='selected'";}?>>หน้าที่ <?php echo $i;?></option>
    							<?php
                                    }
								?> 
                            </select>
							
							</div>
                             <?php }?>
                            </td>
                        </tr>
                        <?php } //$have_search_id?>
                  </form>
              <tr>
                    	  <td colspan="2" align="left" style="padding-bottom:5px" valign="middle">
                          
                          
                          
                          
                          <?php if(!$do_hide_company_list && $sess_accesslevel != 4){ ?>
                          
                          
                          	<table border="0" style="color: #006699">
                            	<tr>
                                	<td >
                                    	<img src="decors/green.gif" alt="ทำตามกฎหมาย" title="ทำตามกฎหมาย">
                                    </td>
                                    <td valign="middle">
                                    	= ทำตามกฎหมาย 
                                    </td>
                                    <td >
                                    	<img src="decors/red.gif" alt="ไม่ทำตามกฎหมาย" title="ไม่ทำตามกฎหมาย">
                                    </td>
                                    <td valign="middle">
                                    	= ไม่ทำตามกฎหมาย 
                                    </td>
                                    <td >
                                    	<img src="decors/yellow.gif" alt="ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน" title="ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน">
                                    </td>
                                    <td valign="middle">
                                    	= ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน 
                                    </td>
                                    <td >
                                    	<img src="decors/blue.gif" alt="ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?>" title="ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?>">
                                    </td>
                                    <td valign="middle">
                                    	= ไม่เข้าข่ายจำนวน<?php echo $the_employees_word;?> 
                                    </td>
                                </tr>
                            </table>
                            
                            
									
                                    
                                        <table border="0" style="color: #006699">
                                            <tr>
                                                <td >
                                                    <img src="decors/a_green.jpg" alt="บันทึกระบบแล้ว" title="บันทึกระบบแล้ว">
                                                </td>
                                                <td valign="middle">
                                                    = ข้อมูลจากสถานประกอบการ บันทึกเข้าระบบแล้ว 
                                                </td>
                                                <td >
                                                    <img src="decors/b_red.jpg" alt="ยังไม่บันทึกเข้าระบบ" title="ยังไม่บันทึกเข้าระบบ">
                                                </td>
                                                <td valign="middle">
                                                    = ข้อมูลจากสถานประกอบการ ยังไม่บันทึกเข้าระบบ 
                                                </td>
                                                
                                            </tr>
                                        </table>
                                        
                                   
                            
                            <?php } ?>
                            
                          </td>
           	  </tr>
            </table>
                   
					<?php if($mode == "letters"){?>
	                  <form method="post" action="scrp_generate_letters.php" onsubmit="return validateLetterForm(this);">
                      
                      <?php if($have_search_id == 1){?>
                      	<input name="search_id" type="hidden" value="<?php echo $_GET["search_id"];?>">
                      <?php } ?>
                      
                   <?php }elseif($mode == "payment"){ ?>
           			 <form method="post" action="scrp_generate_receipt.php" <?php if($sess_accesslevel !=4){?>onsubmit="return validatePaymentForm(this);"<?php }?> enctype="multipart/form-data">
                     	<?php if($sess_accesslevel !=4){?>
                        <script>
							
							function validatePaymentForm(frm) {
								
								
								if(frm.BookReceiptNo.value.length ==0)
								{
									alert("กรุณาใส่ข้อมูล: เล่มที่");
									frm.BookReceiptNo.focus();
									return (false);
								}
								if(frm.ReceiptNo.value.length == 0)
								{
									alert("กรุณาใส่ข้อมูล: ใบเสร็จเลขที่");
									frm.ReceiptNo.focus();
									return (false);
								}
								
								return(true);									
							
							}
						</script>
                        <?php } ?>
                     
            		<?php }elseif($mode == "add_company_payment"){ ?>
         			   <form method="post" action="scrp_add_company_payment.php" onsubmit="" enctype="multipart/form-data">
                    <?php }elseif($mode == "add_company_announce"){ ?>
         			   <form method="post" action="scrp_add_company_announce.php" onsubmit="" enctype="multipart/form-data">   
					<?php }elseif($mode == "announce"){ ?>
         			   <form method="post" action="scrp_add_announcement.php" onsubmit="return validateAnnounceForm(this);" enctype="multipart/form-data">                       
                       
                       <script>
							
							function validateAnnounceForm(frm) {
								
								
								if(frm.GovDocNo.value.length ==0)
								{
									alert("กรุณาใส่ข้อมูล: เลขที่หนังสือประกาศ");
									frm.GovDocNo.focus();
									return (false);
								}
								
								
								return(true);									
							
							}
						</script>
                       
                   <?php } ?>
                 
                  <?php if(!$do_hide_company_list){ ?>
               	  <table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse; <?php if($sess_accesslevel == 4){?>display:none;<?php }?>">
                    	<tr bgcolor="#9C9A9C" align="center" >
                        	<?php if($mode == "letters" || $mode == "payment" 
									|| $mode == "add_company_payment"
									|| $mode == "add_company_announce"
									|| $mode == "announce"
									){?>
                                <td >
                              	 
                              	    <input name="chk_all" id="chk_all" type="checkbox" value="1" onclick="checkOrUncheck();" 
                                 
                                  <?php
								  if($have_search_id){
									  echo "checked='checked'";
								  }
								  ?>
                                  
                                  />
               	          </td>
                            <?php } ?>
                            
                            
                             <td >
                       	  <div align="center"><span class="column_header">วันที่ยื่นแบบฟอร์ม </span>                       	        </div></td>
                            
                            
           	  <td >
                       	  <div align="center"><span class="column_header"><?php echo $the_code_word;?> </span>                       	        </div></td>
                            
                            
                            <td>
                           	<div align="center"><span class="column_header">
                            
                            ชื่อ
							<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
                            
                            <?php }else{?>                            
                            นายจ้างหรือ
                            <?php }?>
							
							<?php echo $the_company_word;?></span>                       	        </div></td>
                            <td>
                           	<div align="center"><span class="column_header">จังหวัด</span>                       	        </div></td>
                          
                          
                            <td>
                           	
                            <div align="center"><span class="column_header">
                            
                            ประจำปี
                            
							</span></div>
                            
                            
                            </td>
                            
                            
                              <?php if(($mode == "normal" || $mode == "search")&& ($sess_accesslevel == 1 || $sess_accesslevel == 3)){ ?>
                           	
                            
                            
                            		
                                         <td>
                                            
                                            <div align="center"><span class="column_header">สถาน<br>ประกอบการ<br>ส่งข้อมูล</span></div>
                                          
                                        </td>
                            		
                            
                            <?php } ?>
                            
                            <td>
                           	<div align="center"><span class="column_header">สถานะ</span>                       	        </div></td>
                           
                           
                          
                        </tr>
                        <?php
					
					
						//yoes 20141014 -> if is provincial the -> only show related provine
						if($sess_meta && $sess_accesslevel == 3){
							$meta_sql = " and z.Province like '".$sess_meta."'";
						}
						
						
						$the_limit = "limit $starting_index, $per_page";
						
						$get_org_sql = "SELECT 
						
											z.employees as company_employees
											, z.CID
											, Province
											, CompanyCode
											, CompanyTypeName
											, CompanyNameThai
											, province_name
											, LawfulFlag
											, y.LawfulStatus as lawfulness_status
											, y.Employees as lawful_employees
											, y.Year
											
										FROM company z
										
										
										LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
										LEFT outer JOIN provinces c ON z.province = c.province_id
										$the_outer_join JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year' 
										
										$lawfulness_company_join
										
										where
											1=1														
											$condition_sql
											
											$lawful_condition
											
											$lawfulness_company_condition
											
											$meta_sql
											
										order by lawful_submitted_on desc
										$the_limit
										";
						
						
						

						if($is_2013){
						
							$get_org_sql = "SELECT 
						
											z.CID
											, Province
											, CompanyCode
											, CompanyTypeName
											, CompanyNameThai
											, province_name
											, LawfulFlag
											, y.LawfulStatus as lawfulness_status
											, y.Employees as lawful_employees
											, y.Year
											
										FROM company z
										
										
										LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
										LEFT outer JOIN provinces c ON z.province = c.province_id
										$the_outer_join JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year' 
										
										$lawfulness_company_join
										
										where
											1=1														
											$condition_sql
											
											$lawful_condition
										
											$lawfulness_company_condition
											
											$meta_sql
										
										order by lawful_submitted_on desc
										$the_limit
										";
						
						
						}
						
						//echo "<br>".$get_org_sql;
						//echo "<br><br>".$condition_sql;
						//echo "<br><br>".$lawful_condition;
						
						
						$org_result = mysql_query($get_org_sql);
					
						//total records 
						$total_records = 0;
					
						while ($post_row = mysql_fetch_array($org_result)) {
					
							$total_records++;
							$this_province = $post_row["Province"];
							
							//if lawful employee = 0 -> use company employee instead
							
							
							
							
							//main branch thing
							
							
							
							$employee_to_use = $post_row["lawful_employees"];
							
							
							if($employee_to_use == 0){
							
								$employee_to_use = $post_row["company_employees"];
								
								if($is_2013){
								
									//sum mployees from all brances
								
									$sum_sql = "select sum(Employees) from company where CompanyCode = '".$post_row["CompanyCode"]."'";
								
									//echo $sum_sql;
								
									$employee_to_use = getFirstItem($sum_sql);
									
								}
								
							}
							
							
							
							
							
							
						?>     
                        <tr bgcolor="#ffffff" align="center" >
                        	<?php if($mode == "letters" || $mode == "payment" 
									|| $mode == "add_company_payment"
									|| $mode == "add_company_announce"
									|| $mode == "announce"){
							
								//js to controls check boxes
								$js_do_check .= "document.getElementById('chk_$total_records').checked = true;";
								$js_do_uncheck .= "document.getElementById('chk_$total_records').checked = false;";
							
							?>
                                <td >
                                
                              	  <input name="chk_<?php echo $total_records; ?>" id="chk_<?php echo $total_records; ?>" type="checkbox" value="<?php echo doCleanOutput($post_row["CID"]);?>"
                                  
                                  <?php
								  if($have_search_id){
									  echo "checked='checked'";
								  }
								  ?>
                                  
                                   />
                               
                            <?php } ?>
                            
                            <td>
                            
                            <div align="center">
                            
                            <?php echo 
							
							
							formatDateThai(
								getFirstItem("
										select 
											lawful_submitted_on 
										from 
											lawfulness_company 
										where 
											CID = '".$post_row["CID"]."' 
											and 
											Year = '$cur_year'")
							);
							
							?>
                            </div>
                            </td>
                            
                            
                            
                       	  <td >
                           	<a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?><?php
                            
							
								if(!$is_2013){
									echo "&all_tabs=1";
								}
							
								if($mode == "payment"){
									echo "&auto_post=1";
								}
							
							?>&year=<?php echo $cur_year;?>"><?php echo doCleanOutput($post_row["CompanyCode"]);?></a>                          </td>
                            <td>
                            	<?php echo doCleanOutput($post_row["CompanyNameThai"]);?></td>
                          <td>
                            	<?php echo doCleanOutput($post_row["province_name"]);?>                          </td>
                            <td align="right">
                            	<div align="center">
								
                                <?php echo $post_row["Year"]+543;?>
                                
                                </div>                          </td>
                                
                                
                                
                                <?php if(($mode == "normal" || $mode == "search") && ($sess_accesslevel == 1 || $sess_accesslevel == 3)){ ?>
                          
                                
                               
                                
                                    <td>                                
                                    
                                    <?php 
                                    
                                    //see if this company has send some information in..
                                    
                                    $count_info = getCompanyInfo($post_row["CID"], $cur_year);
                                    
                                    if($count_info == 1){
                                    
                                    ?>
                                    
                                    <div align="center">
                                        <a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?>&year=<?php echo $cur_year;?>&focus=input"
                                             title="<?php echo $the_company_word;?>ส่งข้อมูลเข้ามาแล้ว ยังไม่บันทึกเข้าระบบ">
                                             <img src="decors/b_red.jpg" border="0" height="20">
                                        </a>
                                    </div>    
                                    
                                    <?php }else{?>
                                    
                                    
                                     <div align="center">
                                        <a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?>&year=<?php echo $cur_year;?>&focus=lawful"
                                             title="<?php echo $the_company_word;?>ส่งข้อมูลเข้ามาแล้ว บันทึกเข้าระบบแล้ว">
                                             <img src="decors/a_green.jpg" border="0" height="20">
                                        </a>
                                    </div>  
                                    
                                    
                                    <?php }?>
                                    
                                    </td>
                                
                            <?php } ?>
                                
                                
                                
                                
                           <td>
                            	<div align="center"><?php //echo $post_row["lawfulness_status"]; 
								
								if($_POST["advanced_search"]){
								
									//latest lawfulness
									//echo getLawfulImage(getFirstItem("select LawfulFlag from company where cid = '".$post_row["CID"]."'"));
									echo getLawfulImage(($post_row["lawfulness_status"]));
								
								}else{
								
									//lawfulness of this year
									echo getLawfulImage(($post_row["lawfulness_status"]));
									
								}
								
								?></div>                         </td>
                         
                         
                            
                        </tr>
                        <?php } //end loop to generate rows?>
				  </table>                        
               <?php }//if(!$do_hide_company_list) ?>
              
               
                <input name="total_records" type="hidden" value="<?php echo $total_records; ?>" />
                
                
                
                
                
                
                
                
                	<?php 
					
						
						/////////
						/////
						/////		NEW AS OF 20140215
						/////
						/////////
					
						if($mode == "email" && !$do_hide_company_list){
						
					?>
                        
                        
                        
                        <hr />
                        <strong>
                        
                        
                             3. ใส่ข้อมูล email
						
                        
                        </strong>
                        
                        
                        <?php if($record_count_all > 20){?>
                        <div style="padding:5px 0 0 0">
                        <input name="cur_year" type="hidden" value="<?php echo $cur_year?>">
                        	<input name="send_to_all" type="checkbox" value="<?php 
							$the_all_condition_sql = $condition_sql . " " . $lawful_condition;
							echo $the_all_condition_sql
							
							;?>"> <strong style="color:#006699">ส่งแจ้งทั้ง <?php echo $record_count_all; ?> <?php echo $the_company_word;?></strong>
							<?php //echo $the_all_condition_sql ;?>
                        </div>
                        <?php } ?>
                        
                    <table style=" padding:10px 0 0px 0;">
                    	<tr>
                    	  <td bgcolor="#efefef">หัวข้อ email: </td>
                    	  <td colspan="5"><input name="" type="text" style="width:500px;"></td>
                   	  </tr>
                      <tr>
                    	  <td bgcolor="#efefef">เนื้อหา email: </td>
                    	  <td colspan="5"><textarea name="" cols="75" rows="15"></textarea></td>
                   	  </tr>
                    	
                      
                    </table>
                        
                        
                         <div style="padding-top: 10px;" >
                    <input type="submit" value="ส่ง email ทันที" />
                    </div>
                        
                        
                     <?php } //if($mode == "email" && !$do_hide_company_list){?>
                     
                     
                     
                     
                     
                     
                     
                     
                     
               	 
               
               	 	<?php 
					
						if($mode == "letters" && !$do_hide_company_list){
						
						?>
                         <script language='javascript'>
							<!--
							function validateLetterForm(frm) {
								
								
								if(frm.RequestNum.value.length ==0)
								{
									alert("กรุณาใส่ข้อมูล: ครั้งที่");
									frm.RequestNum.focus();
									return (false);
								}
								if(frm.GovDocumentNo.value.length == 0)
								{
									alert("กรุณาใส่ข้อมูล: หนังสือเลขที่");
									frm.GovDocumentNo.focus();
									return (false);
								}
								
								return(true);									
							
							}
							-->
						
						</script>
                        
                        <hr />
                        <strong>
                        
                         <?php if($type == "hold"){?>
                             3. ใส่ข้อมูลการแจ้งอายัด
						<?php }else{?>
                             3. เลือกเอกสารที่ต้องการส่งแจ้ง
                        <?php }?>
                        
                        </strong>
                        
                        <?php if($record_count_all > 20){?>
                        <div style="padding:5px 0 0 0">
                        <input name="cur_year" type="hidden" value="<?php echo $cur_year?>">
                        	<input name="send_to_all" type="checkbox" value="<?php 
							$the_all_condition_sql = $condition_sql . " " . $lawful_condition;
							echo $the_all_condition_sql
							
							;?>"> <strong style="color:#006699">ส่งแจ้งทั้ง <?php echo $record_count_all; ?> <?php echo $the_company_word;?></strong>
							<?php //echo $the_all_condition_sql ;?>
                        </div>
                        <?php } ?>
                        
                    <table style=" padding:10px 0 0px 0;">
                    	<tr>
                    	  <td bgcolor="#efefef">ประจำปี: </td>
                    	  <td colspan="5"><?php include "ddl_year.php";?></td>
                   	  </tr>
                    	<tr>
                        	
                            
                            
                    	  <td bgcolor="#efefef">วันที่: </td>
                    	  <td>
                           
                            <?php
											   
							   $selector_name = "RequestDate";
							   
							   $this_date_time = date("Y-m-d");
							 
							   if($this_date_time != "0000-00-00"){
								   $this_selected_year = date("Y", strtotime($this_date_time));
								   $this_selected_month = date("m", strtotime($this_date_time));
								   $this_selected_day = date("d", strtotime($this_date_time));
							   }
							   
							   include ("date_selector.php");
							   
							   ?>                          </td>
                        	<td bgcolor="#efefef">
                            ครั้งที่:                            </td>
                            <td><input name="RequestNum" type="text" id="RequestNum" value="" /></td>
                             <td bgcolor="#efefef">หนังสือเลขที่: </td>
                    	  <td>
                          	<input name="GovDocumentNo" type="text" id="GovDocumentNo" value="" />                          </td>
                      <?php if($type == "hold"){?>
                      </tr>
                    	<tr>
                    	  <td valign="top" bgcolor="#efefef">รายละเอียด:</td>
                    	  <td colspan="5"><label>
                    	    <textarea name="hold_details" cols="50" rows="5" id="hold_details"></textarea>
                            
                    	  </label></td>
                   	  </tr>
                      <?php }?>
                      
                    </table>
                    
                    	<?php 
						
						if($have_search_id == "1" && $this_province == "1"){
							//see if the selected company is in bangkok
							$is_bangkok =1;
						}
						
						?>
                    
                      <?php if(($_POST["Province"] == "1" || $is_bangkok) && $type != "hold"){?>
                      <div style="padding:5px 0 0px 5px; line-height: 20px;"><u><strong>การส่งจดหมายแจ้งให้กับ<?php echo $the_company_word;?>ใน กทม.</strong></u><br>
                      </div>
                      <input id="chk_all_letters" name="" type="checkbox" value="1" checked="checked" onClick="checkOrUncheckLetter();">
                      เอกสารขอความร่วมมือปฏิบัติตามกฎหมายในการจ้างงานคนพิการ<br>
                      <input name="DocBKK1" id="DocBKK1" type="checkbox" value="1" checked="checked">
                      จพ 0-1 แบบการรายงานผลของ<?php echo $the_company_word;?><br>
                      <input name="DocBKK2" id="DocBKK2"type="checkbox"  value="1" checked="checked">
                      จพ 0-2 แบบรายงานผลปฏิบัติตามกฎหมายในการจ้างคนพิการ<br>
                      <input name="DocBKK3"id="DocBKK3" type="checkbox"  value="1" checked="checked">
                      จพ 0-3 แบบรายงานการส่งเงินเข้ากองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ<br>            
                      
                      <input type="hidden" name="DocBKK4" value="0" >        
                      
                      <script>
					  function checkOrUncheckLetter(){
							if(document.getElementById('chk_all_letters').checked == true){
								document.getElementById('DocBKK1').checked = true;
								document.getElementById('DocBKK2').checked = true;
								document.getElementById('DocBKK3').checked = true;
								
							}else{
								document.getElementById('DocBKK1').checked = false;
								document.getElementById('DocBKK2').checked = false;
								document.getElementById('DocBKK3').checked = false;
								
							}
						}
					  </script>
                      
                      <?php }elseif($type != "hold"){ ?>
                      
                       <div style="padding:5px 0 0px 5px; line-height: 20px;"><u><strong>การส่งจดหมายแจ้งให้กับ<?php echo $the_company_word;?>ในส่วนภูมิภาค (ตจว.)</strong></u><br>
                      </div>
                      
                      <input type="checkbox" id="chk_all_letters" value="1" checked="checked" onClick="checkOrUncheckLetter();">
                      เอกสารขอความร่วมมือปฏิบัติตามกฎหมายในการจ้างงานคนพิการ<br>
                      
                      <input type="checkbox" name="DocPro1" id="DocPro1" value="1" checked="checked">
                      จพ 1-1 แบบการรายงานผลของ<?php echo $the_company_word;?><br>
                      <input type="checkbox" name="DocPro2" id="DocPro2" value="1" checked="checked">
                      จพ 1-2 แบบรายงานผลปฏิบัติตามกฎหมายในการจ้างคนพิการ<br>
                    
                      <input type="checkbox" name="DocPro3" id="DocPro3" value="1" checked="checked">
                      จพ 1-3 แบบรายงานการส่งเงินเข้ากองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ<br> 
                      <input type="hidden" name="DocPro4" id="DocPro4" value="0" >
                     
                      <input type="hidden" name="DocPro5" id="DocPro5" value="0" >
                  
                      <input type="hidden" name="DocPro6" id="DocPro6" value="0" >
                      
                      <input type="hidden" name="DocPro7" id="DocPro7" value="0" >
                     
                     <script>
					  function checkOrUncheckLetter(){
							if(document.getElementById('chk_all_letters').checked == true){
								
								document.getElementById('DocPro1').checked = true;
								document.getElementById('DocPro2').checked = true;
								document.getElementById('DocPro3').checked = true;
								
								
								
							}else{
								document.getElementById('DocPro1').checked = false;
								document.getElementById('DocPro2').checked = false;
								document.getElementById('DocPro3').checked = false;
								
								
							}
						}
					  </script>
                   
                    
                   	  <?php }else{ // type == "hold" ?>
                      
                      	
                        	<input name="is_hold_letter" type="hidden" value="1">
                      
                      <?php } ?>
                    
                    <div style="padding-top: 10px;">
                    <input type="submit" value="สร้างจดหมาย" />
                    </div>
                    <?php } //end mode == letters?>
                    
                    <?php 
					
						if($mode == "payment" && !$do_hide_company_list){
						
						?>

                        <?php if($sess_accesslevel != 4){?>
                  	  <hr />
                      <strong>3. ข้อมูลการจ่ายเงิน</strong><br />
                      
					  <?php if($record_count_all > 20){?>
                      <div style="padding:5px 0 0 0">
                        	<input name="send_to_all" type="checkbox" value="<?php echo $condition_sql;?>"> <strong style="color:#006699">ส่งแจ้งทั้ง <?php echo $record_count_all; ?> <?php echo $the_company_word;?></strong>
                      </div>
                      <?php } ?>
                      
                      <?php }else{ ?>
                      
                      <strong>1. ข้อมูลการจ่ายเงิน</strong>
                      
                      <?php } ?>
<table border="0" cellpadding="0">
                          <tr>
                            <td><table border="0" style="padding:10px 0 0 50px;" >
                            
                              <?php if($sess_accesslevel !=4){?>
                              <tr>
                                <td colspan="4" ><?php 
						if($_GET["duped_key"]=="duped_key"){
					?>							
                         <div style="color:#FF3300; padding:0px 0 5px 0; font-weight: bold;">* <a href="view_payment.php?id=<?php echo $_GET["pay_id"]?>">ใบเสร็จเล่มที่ <?php echo $_GET["book_num"]?> ใบเสร็จเลขที่ <?php echo $_GET["pay_num"]?></a> มีอยู่ในระบบแล้ว กรุณาใส่เล่มที่/ใบเสร็จเลขที่ใหม่</div>
                         <?php
						}					
					?><span style="font-weight: bold">ข้อมูลใบเสร็จ</span></td>
                                
                              </tr>
                              <?php } ?>
                              
                              <?php if($sess_accesslevel != 4){?>
                                  <tr>
                                    <td>สำหรับปี</td>
                                    <td><?php include "ddl_year.php";?></td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                  </tr>
                              <?php }else{?>    
                                 	<input name="ddl_year" type="hidden" value="<?php echo date('Y');?>">
                              <?php }?>
                              
                              
                              <?php if($sess_accesslevel != 4){?>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                
                                <?php if($sub_mode == "payback") { ?>
                                    เลขที่หนังสือ                    
                                <?php }else{ ?>
                                    ใบเสร็จเล่มที่
                                <?php }?>
                                
                                </span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="BookReceiptNo" type="text" id="BookReceiptNo" value="<?php echo $lawful_values["check_number"];?>"  />
                                </span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                
                               
                                <?php if($sub_mode == "payback") { ?>
                                   ลงวันที่                 
                                <?php }else{ ?>
                                     ใบเสร็จเลขที่
                                <?php }?>
                                
                                </span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="ReceiptNo" type="text" id="ReceiptNo" value="<?php echo $lawful_values["check_number"];?>"  />
                                </span></td>
                              </tr>
                              
                              <?php } //$sess_accesslevel != 4?>
                              
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">วันที่จ่าย</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <?php
											   
											   $selector_name = "the_date";
											   $this_date_time = $lawful_values["the_date"];
											   
											   include ("date_selector.php");
											   
											   ?>
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">จำนวนเงิน</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="Amount" type="text" id="Amount" style="text-align:right;" value="<?php echo default_value($lawful_values["cash_amount"],0);?>" onchange="addCommas('Amount');"/>
                                  <?php
								  	
									include "js_format_currency.php";
								  
								  ?>
                                  <span class="style86" style="padding: 10px 0 10px 0;">บาท</span></span></span></td>
                                <td>จ่ายโดย</td>
                                <td><label>
                                 <?php include "ddl_pay_type.php";?>
                                </label>                                </td>
                              </tr>
                              
                              <tr>
                                <td colspan="4"><table id="cash_table" border="0" style="padding:0px 0 0 50px;" >
                                  <tr>
                                    <td><span style="font-weight: bold">ข้อมูลการจ่ายเงินสด</span></td>
                                    <td>&nbsp;</td>
                                  </tr>

                                </table>
                                  <table id="cheque_table" border="0" style="padding:0px 0 0 50px;"  >
                                    <tr>
                                      <td><span style="font-weight: bold">ข้อมูลการจ่ายเช็ค</span></td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td><span class="style86" style="padding: 10px 0 10px 0;">ธนาคาร</span></td>
                                      <td colspan="3"><?php
											  include "ddl_bank.php";
											  ?>                                      </td>
                                      
                                    </tr>
                                     <tr>
                                     
                                      <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่เช็ค</span></td>
                                      <td colspan="3"><span class="style86" style="padding: 10px 0 10px 0;">
                                        <input name="Cheque_ref_no" type="text" id="Cheque_ref_no" value="<?php echo $lawful_values["check_number"];?>"  />
                                      </span></td>
                                    </tr>
                                    
                                  </table>
                                  <table id="note_table" border="0" style="padding:0px 0 0 50px; " >
                                    <tr>
                                      <td><span style="font-weight: bold">ข้อมูลการจ่ายธนาณัติ</span></td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td>เลขที่ธนาณัติ</td>
                                      <td><span class="style86" style="padding: 10px 0 10px 0;">
                                        <input name="Note_ref_no" type="text" id="Note_ref_no" value="<?php echo $lawful_values["note_number"];?>"  />
                                      </span></td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                    </tr>
                                  </table></td>
                              </tr>
                              <tr>
                                <td valign="top">หมายเหตุ</td>
                                <td colspan="3"><label>
                                <textarea name="ReceiptNote" cols="50" rows="4" id="ReceiptNote"></textarea>
                                </label></td>
                              </tr>
                              <tr>
                                <td>เอกสารประกอบ</td>
                                <td colspan="3"><?php if(strlen($lawful_values["note_docfile"]) > 0 ){?>
                                    <a href="<?php echo "$hire_docfile_relate_path/".$lawful_values["note_docfile"];?>"><?php echo end(explode("_",$lawful_values["note_docfile"],2));?></a> ||
                                  <?php }?>
                                    <input type="file" name="receipt_docfile" id="receipt_docfile" /></td>
                              </tr>

                            </table></td>
                          </tr>
                          <tr>
                            <td><hr />
                              <div align="center">
                                <input type="submit" value="เพิ่มข้อมูล" />
                                <?php if($have_search_id == 1){ ?>
                                <input type="hidden" name="back_to" value="lawfulness_tab" />
                                <?php } ?>
                                
                                <?php if($sub_mode == "payback"){ ?>
                                <input name="is_payback" type="hidden" value="1">
                                <?Php }?>
                            </div></td>
                          </tr>
                        </table>
                        <script>
									
														
							function doToggleMethod(){
							
								the_method = document.getElementById("PaymentMethod").value;
							
								document.getElementById("cash_table").style.display = "none";
								document.getElementById("cheque_table").style.display = "none";
								document.getElementById("note_table").style.display = "none";
								
								if(the_method == "Cash"){
									//document.getElementById("cash_table").style.display = "";
								}else if(the_method == "Cheque"){
									document.getElementById("cheque_table").style.display = "";
								}else if(the_method == "Note"){
									document.getElementById("note_table").style.display = "";
								}
							}	
							
							doToggleMethod();							
							
							
						</script>
                        <?php } //end mode == payment?>
                    
                    	<?php 
					
						if($mode == "add_company_payment" && !$do_hide_company_list){
						
						?>
                         <hr />
                      
                       
						<table border="0" cellpadding="0" align="center">
                            <tr>
                                <td>
                                	<div align="center">
                                      <strong>3. เพิ่ม<?php echo $the_company_word;?>สำหรับใบเสร็จรับเงิน: ใบเสร็จเล่มที่ <?php echo $book_number;?> เลขที่ <?php echo $receipt_number;?></strong>
                                     <?php if(1==0){ ?><div style="padding:5px 0 0 0">
                                        <input name="send_to_all" type="checkbox" value="<?php echo $condition_sql;?>"> <strong style="color:#006699">เพิ่มทั้ง <?php echo $record_count_all; ?> <?php echo $the_company_word;?></strong>
                                  </div><?php } ?>
                                      <br><input name="" type="submit" value="เพิ่ม<?php echo $the_company_word;?>" />
                                      <input name="RID" type="hidden" value="<?php echo $this_id?>" />
                                  </div>
                                </td>
                            </tr>
                        </table>
                        
                        
                        <?php
						}//end mode == add_company_payment
						?>
                        
                        
                        <?php 
					
						if($mode == "announce" && !$do_hide_company_list){
						
						?>
                         <hr />
                         <table border="0" cellpadding="0">
                           <tr>
                             <td><table border="0" style="padding:10px 0 0 50px;" >
                                 <tr>
                                   <td><span style="font-weight: bold">3. การประกาศผ่านสื่อ</span>
                                   <div style="padding:5px 0 0 0">
                                   <input name="cur_year" type="hidden" value="<?php echo $cur_year?>">
                                        <input name="send_to_all" type="checkbox" value="<?php echo $condition_sql . " " .$lawful_condition;?>"> <strong style="color:#006699">ส่งแจ้งทั้ง <?php echo $record_count_all; ?> <?php echo $the_company_word;?></strong>
                                  </div>
                                   </td>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                 </tr>
                                 <tr>
                                   <td><span class="style86" style="padding: 10px 0 10px 0;">เลขที่หนังสือประกาศ</span></td>
                                   <td><span class="style86" style="padding: 10px 0 10px 0;">
                                     <input name="GovDocNo" type="text" id="GovDocNo" value="<?php echo $lawful_values["cash_amount"];?>" />
                                   </span></td>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                 </tr>
                               <!--
                               <tr>
                                   <td><span class="style86" style="padding: 10px 0 10px 0;">วันที่ประกาศ</span></td>
                                   <td><span class="style86" style="padding: 10px 0 10px 0;">
                                     <?php
											   
											   $selector_name = "announce_date";
											   $this_date_time = $lawful_values["the_date"];
											   
											   include ("date_selector.php");
											   
											   ?>
                                   </span></td>
                                   <td>ครั้งที่</td>
                                   <td><span class="style86" style="padding: 10px 0 10px 0;">
                                     <input name="ANum" type="text" id="ANum" value="<?php echo $lawful_values["check_number"];?>" size="10"  />
                                   </span></td>
                                 </tr>
                                 <tr>
                                   <td><span class="style86" style="padding: 10px 0 10px 0;">สื่อสิ่งพิมพ์</span></td>
                                   <td><?php include "ddl_newspaper.php";?></td>
                                   <td><span class="style86" style="padding: 10px 0 10px 0;">ฉบับวันที่</span></td>
                                   <td><span class="style86" style="padding: 10px 0 10px 0;">
                                     <?php
											   
											   $selector_name = "news_date";
											   $this_date_time = $lawful_values["the_date"];
											   
											   include ("date_selector.php");
											   
											   ?>
                                   </span></td>
                                 </tr>
                                 <tr>
                                   <td>&nbsp;</td>
                                   <td>&nbsp;</td>
                                   <td>สถานะ</td>
                                   <td><label>
                                     <select name="Cancelled" id="Cancelled">
                                     	<option value="0">ประกาศผ่านสื่อ</option>
                                        <option value="1">ยกเลิกการประกาศ</option>
                                     </select>
                                   </label></td>
                                 </tr>
                                 -->

                                
                                 <tr>
                                   <td valign="top">รายละเอียด</td>
                                   <td colspan="3"><label>
                                     <textarea name="Topic" cols="50" rows="4" id="Topic"></textarea>
                                   </label></td>
                                 </tr>
                                 <tr>
                                   <td>เอกสารประกอบ</td>
                                   <td colspan="3"><?php if(strlen($lawful_values["announce_docfile"]) > 0 ){?>
                                       <a href="<?php echo "$announce_docfile_relate_path/".$lawful_values["note_docfile"];?>"><?php echo end(explode("_",$lawful_values["note_docfile"],2));?></a> ||
                                     <?php }?>
                                       <input type="file" name="announce_docfile" id="announce_docfile" /></td>
                                 </tr>
                             </table></td>
                           </tr>
                           <tr>
                             <td><hr />
                                 <div align="center">
                                   <input type="submit" value="เพิ่มข้อมูล" />
                               </div></td>
                           </tr>
                         </table>
                         <?php
						}//end mode == announce
						?>
                    
                    
                   		<?php 
					
						if($mode == "add_company_announce" && !$do_hide_company_list){
						
						?>
                         <hr />
                      
						<table border="0" cellpadding="0" align="center">
                            <tr>
                                <td>
                                	<div align="center">
                                      <strong>เพิ่ม<?php echo $the_company_word;?>สำหรับการประกาศผ่านสื่อ: <?php echo $GovDocNo;?></strong>
                                      <div style="padding:5px 0 0 0">
                                        <input name="send_to_all" type="checkbox" value="<?php echo $condition_sql;?>"> <strong style="color:#006699">เพิ่มทั้ง <?php echo $record_count_all; ?> <?php echo $the_company_word;?></strong>
                                  </div>
                                      <input name="" type="submit" value="เพิ่ม<?php echo $the_company_word;?>" />
                                      <input name="AID" type="hidden" value="<?php echo $this_id?>" />
                                  </div>
                                </td>
                            </tr>
                        </table>
                        <?php
						}//end mode == add_company_announce
						?>
                        
                    
            </form>
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

</div><!--end page cell-->
</td>
</tr>
</table>

<script language="javascript">

function checkOrUncheck(){
	if(document.getElementById('chk_all').checked == true){
		checkAll();
	}else{
		uncheckAll();
	}
}

function checkAll(){
	<?php echo $js_do_check; ?>
}

function uncheckAll(){
	<?php echo $js_do_uncheck; ?>
}
</script>

<script>
	var form = document.getElementById('search_form');
	form.onsubmit = function() {
		form.action = '';
		form.target = '_self';
	};
	
	document.getElementById('get_report').onclick = function() {
		
		if(confirm('การแสดงรายงานทุกมิติอาจใช้เวลานาน ขึ้นอยู่กับจำนวนสถานประกอบการที่เลือก. ต้องการแสดงรายงานทุกมิติ?')){
				
			form.action = 'report_org_list.php';
			form.target = '_blank';
			form.submit();
		}else{
		
			return false;
			
			
		}
	}
</script>

</body>
</html>