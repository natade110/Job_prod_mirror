<?php
include_once  "db_connect.php";
include_once  "session_handler.php";
require_once 'c2x_include.php';
require_once 'scrp_sequestration.php';
require_once 'ThaiFormat.php';
$interestRate = $DEBT_INTEREST_RATE;

if(!hasCreateRoleSequestration()){
	header ("location: index.php");
}


$taskName = (isset($_POST["HiddTaskName"]))? $_POST["HiddTaskName"] : "";
$errorMessage = "";
$dupedKey = false;
$dupedSID = 0;
$model = new Sequestration();
$canSave = ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี  || $sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ);
$manage = new ManageSequestration();
$condition_sql = "";
$searchID = 0;


if($taskName == "save"){	
	$lawStatus = $COMPANY_LAW_STATUS->อายัดทรัพย์สิน;	
			
	$saveResult = $manage->saveSequestration($_POST, $_FILES, 0, $sess_userfullname, $lawStatus, $SEQUESTRATION_TYPE, $the_company_word, $hire_docfile_relate_path);	
	$model = $saveResult->Data;

	
	if($saveResult->IsComplete == true){		
		header("location: holding_edit.php?id=$model->SID&added=added");
	}else if($saveResult->DupedKey == true){
		$dupedKey = $saveResult->DupedKey;
		$dupedSID = $saveResult->DupedSID;
	}else{
		$errorMessage = $saveResult->Message;
	}	
}else{
	$model = $manage->getSequestrationInput(0, $_POST);	
	if(is_numeric($_GET["search_id"])){		
		$searchID = $_GET["search_id"];
		$taskName = ($taskName == "")? "search" : $taskName;
		$condition_sql .= " and c.CID = $searchID";
	}
	
	if($taskName == "search"){
		$model->DocumentDate = date("Y-m-d");
	}
}

if($sess_accesslevel == 6 || $sess_accesslevel == 7){

	$condition_sql .= " and c.CompanyTypeCode >= 200  and c.CompanyTypeCode < 300";

}else{

	$condition_sql .= " and c.CompanyTypeCode < 200";

}

/*สร้างได้เฉพาะกรุงเทพ*/
if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี){
	$condition_sql .= " AND p.province_code = ".BANGKOK_PROVINCE_CODE;

}else if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
	$condition_sql .= " AND c.Province = ".$sess_meta;
	
	$zone_user = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
	
	if($zone_user != null){
		$condition_sql .= " AND
		(
	
		c.District in (
	
		select
		district_name
		from
		districts
		where
		district_area_code
		in (
	
		select
		district_area_code
		from
		zone_district
		where
		zone_id = '$zone_user'
			
		)
			
		)
		or
		c.district_cleaned in (
	
		select
		district_name
		from
		districts
		where
		district_area_code
		in (
	
		select
		district_area_code
		from
		zone_district
		where
		zone_id = '$zone_user'
			
		)
	
		)
		)";
	}

}

$input_fields = array(	
		 'Province'	
		,'CompanyTypeCode'
		,'BusinessTypeCode'
		,'CompanyCode'
);

for($i = 0; $i < count($input_fields); $i++){

	if(strlen($_POST[$input_fields[$i]])>0){
			
		$use_condition = 1;
			
		if($input_fields[$i] == "Province"  ){
			$condition_sql .= " and c.$input_fields[$i] like '".mysql_real_escape_string($_POST[$input_fields[$i]])."'";		
		}else{
			$condition_sql .= " and c.$input_fields[$i] like '%".mysql_real_escape_string($_POST[$input_fields[$i]])."%'";
		}
			
	}
}



$lawful_condition = " and ((l.LawfulStatus = '0' or l.LawfulStatus is null) or (l.LawfulStatus = '2')) and c.LawStatus = ".$COMPANY_LAW_STATUS->แจ้งโนติส;


if(strlen($_POST["CompanyNameThai"]) > 0){

	$name_exploded_array = explode(" ",mysql_real_escape_string($_POST["CompanyNameThai"]));

	for($i=0; $i<count($name_exploded_array);$i++){

		if(strlen(trim($name_exploded_array[$i]))>0){
			$condition_sql .= " and c.CompanyNameThai like '%".mysql_real_escape_string($name_exploded_array[$i])."%'";
		}
	}
}


/// get lawful year
$lawfulyear_condition = "";
$theEndYear = 0;
if(date("m") >= 9){
	$theEndYear = date("Y")+1; //new year at month 9
}else{
	$theEndYear = date("Y");
}

//this default year
$lawfulYear = $theEndYear - 4;

if($lawfulYear >= 2013){
	$lawfulyear_condition = " and ((l.Year in(2011, 2012) or (l.Year >= 2013 and l.Year <= ".$lawfulYear." and (c.BranchCode < 1))))";
}else{
	$lawfulyear_condition = " and ((l.Year >= 2011) and (l.Year <= ".$lawfulYear."))";
}



$compareReceivedDate = date('Y-m-d',strtotime(date("Y-m-d") . "-30 days"));
//$compareReceivedDate = date_format($compareReceivedDate, 'Y-m-d');
$receiveddate_condition = " and n.ReceivedDate <= '$compareReceivedDate' ";
/// END BUILDING CONDITIONS


// Start Crate Sql
// Paginator
$per_page = 5;
$record_count_all = 0;
$num_page = 0;
$cur_page = 1;

$get_org_sql = "";
$org_result = null;

if($condition_sql != ""){
	$condition_sql = substr($condition_sql, 4);
	
	$count_org_sql = "
	SELECT count(*)
	FROM (
	SELECT
	c.CID
	, Province
	, CompanyCode
	, BranchCode
	, CompanyTypeName
	, CompanyNameThai
	, province_name,
	n.ReceivedDate
	FROM company c
	
	LEFT outer JOIN companytype ct ON c.CompanyTypeCode = ct.CompanyTypeCode
	LEFT outer JOIN provinces p ON c.province = p.province_id
	JOIN lawfulness l ON c.CID = l.CID
	JOIN (
      SELECT CID, MAX(ReceivedDate) AS ReceivedDate  FROM noticedocument GROUP BY CID
    )n ON c.CID = n.CID 
	WHERE 
	
	
	$condition_sql
	
	$lawful_condition
	
	$lawfulyear_condition
	
	$receiveddate_condition
	
	
	Group BY  c.CID , Province , CompanyCode , BranchCode , CompanyTypeName , CompanyNameThai , province_name
	
	ORDER BY CompanyNameThai asc
	) tmp
	";
	
	$record_count_all = getFirstItem($count_org_sql);
	$num_page = ceil($record_count_all/$per_page);
	
	
	if(is_numeric($_POST["start_page"]) && $_POST["start_page"] <= $num_page && $_POST["start_page"] > 0){
		$cur_page = $_POST["start_page"];
	}
	
	$starting_index = 0;
	if($cur_page > 1){
		$starting_index = ($cur_page-1) * $per_page;
	}
	// \Paginator
	
	$the_limit = "limit $starting_index, $per_page";
	
	
	
	$get_org_sql = "SELECT
	  c.CID
	, Province
	, CompanyCode
	, BranchCode
	, CompanyTypeName
	, CompanyNameThai
	, province_name
	, n.ReceivedDate
	FROM company c
		
	LEFT outer JOIN companytype ct ON c.CompanyTypeCode = ct.CompanyTypeCode
	LEFT outer JOIN provinces p ON c.province = p.province_id
	JOIN lawfulness l ON c.CID = l.CID 
	JOIN (
      SELECT CID, MAX(ReceivedDate) AS ReceivedDate  FROM noticedocument GROUP BY CID
    )n ON c.CID = n.CID 
	WHERE

	$condition_sql
		
	$lawful_condition
	
	$lawfulyear_condition
		
	$receiveddate_condition
	
	Group BY  c.CID , Province , CompanyCode , BranchCode , CompanyTypeName , CompanyNameThai , province_name  
	
	ORDER BY CompanyNameThai asc
	
	$the_limit
	";
	

}
// End Creat Sql
?>
<?php include "header_html.php";?>
				<td valign="top" style="padding-left:5px;"> <!-- td top --> 
					<script type="text/javascript" src="./scripts/manage.holdingcreate.js"></script>
					<style type="text/css" >
						#DdlProvince{
							width:173px;
						}					
						.ddl-bank-width select{
							width:357px;
						}
					</style>
                    <h2 class="default_h1" style="margin:0; padding:0;"  >การแจ้งอายัด</h2>                   
                    <div style="padding-top:10px; font-weight: bold;">
                        1. ค้นหาสถานประกอบการที่ต้องการแจ้งอายัด
					</div>
					
					<form method="post" action="" id="search_form" enctype="multipart/form-data">
						<input type="hidden" id="TotalAmount" name="TotalAmount" value="<?php echo $model->TotalAmount?>"/>
						<input type="hidden" id="HiddTaskName" name="HiddTaskName" value="<?php echo $taskName;?>"> 
						<input type="hidden" id="HiddCID" name="HiddCID" value="<?php echo $model->CID?>"/>
						<input type="hidden" id="HiddCompanyCode" name="HiddCompanyCode" value="<?php echo $model->CompanyCode?>"/>
						<input type="hidden" id="HiddBranchCode" name="HiddBranchCode" value="<?php echo $model->BranchCode?>"/>						  
						<input type="hidden" id="NoticeDate" name="NoticeDate" value="<?php echo $model->NoticeDate; ?>"> 
						<input type="hidden" id="Start2011InterestDate" name="Start2011InterestDate" value="<?php echo $model->Start2011InterestDate; ?>">  
						<input type="hidden" id="SequestrationPayments" name="SequestrationPayments" />
						<table style=" padding:10px 0 0px 0; " id="general">							
	                   	    <tr>	                        	
	                    	   <td bgcolor="#efefef">ชื่อ:  </td>
	                    	   <td>
	                          	   <input type="text" name="CompanyNameThai" value="<?php echo writeHtml($_POST["CompanyNameThai"]);?>" />     
	                           </td>
	                           <td bgcolor="#efefef"><?php echo $the_code_word;?>:</td>
	                           <td>
	                                <input type="text" name="CompanyCode" value="<?php echo writeHtml($_POST["CompanyCode"]);?>" />                      
	                           </td>
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
	                    	  <td>
	                    	  <?php 
				               $provinceList = getProvinceMapping();
				               $defaultLabel = ($provinceList != null && (count($provinceList) > 1))? "-- select --" : null;
				               echo createDropDownListFromMapping("Province", $provinceList, $_POST["Province"], $defaultLabel);
				               ?>
	                    	  </td>
	                    	  <td >&nbsp;</td>
	                    	  <td>&nbsp;</td>
	                  	  </tr>
	                  	 
	                  	  <tr>
	                  	  	<td colspan="4">
	                  	  		 <input type="submit" value="แสดง" name="mini_search" onclick="return c2xHolding.setTask('search')"/>
	                  	  	</td>
	                  	  </tr>
	                  	  
						</table>
						<hr />
						
				<?php if($taskName != ""){?>
						<div style="padding:10px 0 10px 0; font-weight: bold;">2. เลือก<?php echo $the_company_word;?>ที่ต้องการแจ้งอายัด(ค้างชำระ 4 ปี)</div>
						<table style="width: 100%">
							<tr>
								<td>
									<font color="#006699">แสดงข้อมูล <?php echo $starting_index+1;?>-<?php echo ($record_count_all < $starting_index+$per_page) ? $record_count_all : $starting_index+$per_page;?> จากทั้งหมด <?php echo $record_count_all; ?> รายการ</font> 
								</td>
								<td>
									<div style="padding:5px 0 0px 0;" align="right">
						                            แสดงข้อมูล:						                            
			                            <select name="start_page" onchange="c2xHolding.changePagination()">
			                            	<?php 
												for($i = 1; $i <= $num_page; $i++){
											?>
			                            	<option value="<?php echo $i;?>" <?php if($_POST["start_page"]==$i){echo "selected='selected'";}?>>หน้าที่ <?php echo $i;?></option>
			    							<?php
			                                    }
											?> 
			                            </select>									
									</div>
								</td>
							</tr>
						</table>					
						
						<table class="nep-grid">
							<tr>
								<th></th>								
								<th width="120">เลขที่บัญชีนายจ้าง</th>
								<th width="130">ประเภทกิจการ</th>
								<th width="300">ชื่อ นายจ้างหรือ สถานประกอบการ </th>
								<th width="100">จังหวัด</th>
								<th width="120">วันที่รับแจ้งโนติส</th>								
							</tr>
							
							<?php 
							$org_result = mysql_query($get_org_sql);
							$thaiFormat = new ThaiFormat();
							$cid = 0;
							while ($post_row = mysql_fetch_array($org_result)) {	
								$receivedDate = (!is_null($post_row["ReceivedDate"]))? new DateTime($post_row["ReceivedDate"]) : null;
								$receivedDateFormat = $thaiFormat->date_format($receivedDate, "j F Y", true);
								$noticeDateFormat = $receivedDate->format("Y-m-d");
								$start2011InterestDate =  new DateTime($post_row["ReceivedDate"]);
								$start2011InterestDate = $start2011InterestDate->modify("+30 day");
								$start2011InterestDateFormate = $start2011InterestDate->format("Y-m-d");
								
								$cid = $post_row["CID"];
								?>
								<tr>
									<td>
										<input type="checkbox" class="org-checkbox" onclick="c2xHolding.onSelectedOrg(this)" 
											company-id="<?php  writeHtml($cid);?>"
											company-code="<?php  writeHtml($post_row["CompanyCode"]);?>" 
											branch-code="<?php  writeHtml($post_row["BranchCode"]);?>"
											notice-date="<?php  writeHtml($noticeDateFormat)?>" 
											start-2011-interest-date="<?php  writeHtml($start2011InterestDateFormate)?>"
										/> 
									</td>									
									<td>										
			                           	<a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?>"><?php echo doCleanOutput($post_row["CompanyCode"]);?></a>  
									</td>
									<td><?php  writeHtml($post_row["CompanyTypeName"]);?></td>
									<td><?php  writeHtml($post_row["CompanyNameThai"]);?></td>
			                        <td><?php  writeHtml($post_row["province_name"]);?></td>
			                        <td><?php echo $receivedDateFormat;?></td>	                            	
								</tr>
							<?php }?>
						</table><!-- Search Result -->
						
						
						
						
				<?php }?> <!--  \\ ข้อ 2. -->
				
				<?php if(($taskName != "") && ($record_count_all > 0)){?>
					<?php 
						if($dupedKey == true){
					?>		
						 <hr />					
                         <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <a href="holding_edit.php?id=<?php echo $dupedSID?>">หนังสือเลขที่ <?php echo $model->GovDocumentNo?></a> มีอยู่ในระบบแล้ว กรุณาใส่หนังสือเลขที่ใหม่</div>
                         
                    <?php
						}else if(!empty($errorMessage)){?>
					     <hr />
						 <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <?php echo nl2br($errorMessage);?></div>
                        
					<?php 	
						}
				     ?>
				
					<hr />
	                <strong>3. ใส่ข้อมูลการแจ้งอายัด</strong>
	                <table style=" padding:10px 0 0px 0;">
	                	<tr>	                		 
	                		 <td bgcolor="#efefef">วันที่: </td>
	                    	 <td>
	                           
	                            <?php
												   
								   $selector_name = "DocumentDate";
								   
								   $this_date_time = (!is_null($model->DocumentDate))? $model->DocumentDate : "0000-00-00";
								 
								   if($this_date_time != "0000-00-00"){
									   $this_selected_year = date("Y", strtotime($this_date_time));
									   $this_selected_month = date("m", strtotime($this_date_time));
									   $this_selected_day = date("d", strtotime($this_date_time));
								   }
								   
								   include ("date_selector.php");
								   
								   ?>     
								   <span> *</span>                     
							</td>
							
							<td bgcolor="#efefef">หนังสือเลขที่: </td>
	                    	<td><input name="GovDocumentNo" type="text" id="GovDocumentNo" maxlength="25" value="<?php echo writeHtml($model->GovDocumentNo)?>" /> *</td>
	                	</tr>
	                	
	                	<tr>
		               		<td bgcolor="#efefef">ผู้ดำเนินการ:</td>
		               		<td colspan="5">
		               			<?php echo $sess_userfullname;?>
		               		</td>
		               	</tr>
	                	
	                	<tr>
		                    <td valign="top" bgcolor="#efefef">จำนวนเงินต้นที่อายัด:</td>
		                    <td colspan="3">		                    	 
		                    	 <div id="OrgDebtBlock" style="display: none; margin-top: 0px;">
		                    	 	
		                    	 </div>
		                    	 <div id="Org2011DebtBlock" style="display: none; margin-top: 7px;">
		                    	 	
		                    	 </div>
		                    </td>
		              	</tr>
		              	<tr>
		                    <td valign="top" bgcolor="#efefef">ประเภทการอายัด:</td>
		                    <td colspan="3">
		                    	 <input type="checkbox" id="ChkSequesterTypeMoney" name="ChkSequesterTypeMoney" 
		                    	 	onclick="c2xHolding.showSequesterType(this, 'money')">ธนาคาร &nbsp;&nbsp;&nbsp;&nbsp;
		                    	 <input type="checkbox" id="ChkSequesterTypeProperty" name="ChkSequesterTypeProperty"  
		                    	 	onclick="c2xHolding.showSequesterType(this, 'property')">ที่ดิน&nbsp;&nbsp;&nbsp;&nbsp;
		                    	 <input type="checkbox" id="ChkSequesterTypeCar" name="ChkSequesterTypeCar"  
		                    	 	onclick="c2xHolding.showSequesterType(this, 'car')">รถยนต์&nbsp;&nbsp;&nbsp;&nbsp;
		                    	 <input type="checkbox" id="ChkSequesterTypeOther" name="ChkSequesterTypeOther"  
		                    	 	onclick="c2xHolding.showSequesterType(this, 'other')">อื่นๆ&nbsp;&nbsp;&nbsp;&nbsp;
		                    	 <span> *</span>
		                    	 <div>
		                    	 	<div id="SequesterTypeMoneyContainer" style="display: none; margin-left: 20px; margin-top:20px; margin-right:10px;"><!-- บัญชี -->
		                    	 		<input type="hidden" id="HiddSequesterTypeMoneyData" name="HiddSequesterTypeMoneyData" /> 	
		                    	 		<input type="hidden" id="HiddSequesterTypeMoneyUID" /> 
		                    	 		<strong>ข้อมูลบัญชีธนาคาร</strong>	                    	 		
		                    	 		<table>
		                    	 			<tr>
		                    	 				<td>เลขบัญชี:</td>		                    	 				
		                    	 				<td><input id="AccountNo" name="AccountNo" maxlength="25"/> *</td>
		                    	 				
		                    	 				<td>ประเภทบัญชี:</td>
		                    	 				<td>
		                    	 					<?php
												   
													   echo createDropDownListFromMapping("AccountType", getAcountTypeMapping(), "", "-- select --")
													   
													   ?>  *
		                    	 				</td>
		                    	 			</tr>
		                    	 			<tr>
		                    	 			 	<td>ธนาคาร: </td>
		                    	 			 	<td colspan="2" class="ddl-bank-width">
		                    	 			 		<?php include "ddl_bank.php"; ?> *
		                    	 			 	</td>
		                    	 			 	
		                    	 			</tr>
		                    	 			<tr>
		                    	 				<td>สาขา</td>
		                    	 				<td colspan="2">
		                    	 					<input id="BankBranch" name="BankBranch" style="width:355px;" maxlength="255"/> *
		                    	 				</td>
		                    	 				<td>
		                    	 			 		<a href="javascript:void(0)" class="icon icon-save" title="บันทึก" onclick="c2xHolding.saveSequesterTypeMoney()" ></a>
		                    	 			 		<a href="javascript:void(0)" class="icon icon-cancel" title="ยกเลิก" onclick="c2xHolding.clearSequesterTypeMoneyForm()" ></a>
		                    	 			 	</td>
		                    	 			</tr>
		                    	 		</table>		                    	 		
	                    	 			<table id="SequesterTypeMoneyGrid" class="nep-grid" style="width:100%; margin-left: 20px; margin-top: 10px; display: none">
	                    	 				<thead>
	                    	 					<tr>
			                    	 				<th>เลขบัญชี</th>				                    	 				
			                    	 				<th>ประเภทบัญชี</th>
			                    	 				<th width="120">ธนาคาร</th>
			                    	 				<th width="90">สาขา</th>
			                    	 				<th width="55">		                    	 					
			                    	 				</th>		                    	 				
			                    	 			</tr>
	                    	 				</thead>
	                    	 				<tbody>		                    	 					
	                    	 				</tbody>
		                    	 		</table>
		                    	 	</div><!-- //บัญชี -->
		                    	 	
		                    	 	<div id="SequesterTypePropertyContainer" style="display: none;  margin-left: 20px; margin-top: 20px;  margin-right:10px;"><!-- ที่ดิน -->
		                    	 		<input type="hidden" id="HiddSequesterTypePropertyData" name="HiddSequesterTypePropertyData" /> 	
		                    	 		<input type="hidden" id="HiddSequesterTypePropertyUID" name="HiddSequesterTypePropertyUID" />
		                    	 		<strong>ข้อมูลอสังหาริมทรัพย์</strong>	                    	 		
		                    	 		<table>		                    	 			
		                    	 			<tr>
		                    	 				<td>เลขที่โฉนด:</td>		                    	 				
		                    	 				<td><input id="DocumentNo" name="DocumentNo" maxlength="25"/> *</td>
		                    	 				
		                    	 				<td>จังหวัด: </td>
		                    	 			 	<td class="ddl-org-province-sequesterProperty-type"><?php $ddl_selector_name = "DdlProvince"; include "ddl_org_province_code.php"; ?>*</td>
		                    	 				<td></td>
		                    	 				
		                    	 			</tr>
		                    	 			<tr>
		                    	 				<td>อำเภอ/เขต:</td>
		                    	 				<td>
		                    	 					<select id="DdlDistrict"  style="width: 173px;">
		                    	 						<option  value="">-- select --</option>		                    	 							                    	 						
		                    	 					</select> *
		                    	 				</td>
		                    	 				
		                    	 				<td>ตำบล/แขวง:</td>
		                    	 				<td>
		                    	 					<select id="DdlSubDistrict"  style="width: 173px;">
		                    	 						<option value="">-- select --</option>		                    	 						
		                    	 					</select> *
		                    	 				</td>
		                    	 				
		                    	 			 	
		                    	 			 	<td>
		                    	 			 		<a href="javascript:void(0)" class="icon icon-save" title="บันทึก" onclick="c2xHolding.saveSequesterTypeProperty()" ></a>
		                    	 			 		<a href="javascript:void(0)" class="icon icon-cancel" title="ยกเลิก" onclick="c2xHolding.clearSequesterTypePropertyForm()" ></a>
		                    	 			 	</td>
		                    	 			</tr>
		                    	 		</table>
		                    	 		<table id="SequesterTypePropertyGrid" class="nep-grid" style="width:100%; margin-left: 20px; margin-top: 10px; display: none">
		                    	 			<thead>
		                    	 				<tr>
			                    	 				<th>เลขที่โฉนด</th>
			                    	 				<th>ตำบล/แขวง</th>
			                    	 				<th>อำเภอ/เขต</th>
			                    	 				<th>จังหวัด</th>
			                    	 				<th width="55">		                    	 					
			                    	 				</th>		                    	 				
			                    	 			</tr>
		                    	 			</thead>
		                    	 		    <tbody></tbody>                   	 			
		                    	 			
		                    	 		</table>
		                    	 	</div><!-- // ที่ดิน -->
		                    	 
		                    	 	<div id="SequesterTypeCarContainer" style="display: none; margin-left: 20px; margin-top: 20px;  margin-right:10px;"><!-- รถยนต์ -->
		                    	 		<input type="hidden" id="HiddSequesterTypeCarData" name="HiddSequesterTypeCarData" /> 	
		                    	 		<input type="hidden" id="HiddSequesterTypeCarUID" /> 
		                    	 		<strong>ข้อมูลรถยนต์</strong>	                    	 		
		                    	 		<table>
		                    	 			<tr>
		                    	 				<td>ทะเบียนรถยนต์:</td>		                    	 				
		                    	 				<td style="width: 230px"><input id="CarNo" name="CarNo" maxlength="25"/> *</td>
		                    	 				
		                    	 				<td>ปี:</td>
		                    	 				<td>
		                    	 					<input id="CarYear" name="CarYear" class="nep-yearpicker" maxlength="4"/>  *
		                    	 				</td>
		                    	 				<td>
		                    	 			 		<a href="javascript:void(0)" class="icon icon-save" title="บันทึก" onclick="c2xHolding.saveSequesterTypeCar()" ></a>
		                    	 			 		<a href="javascript:void(0)" class="icon icon-cancel" title="ยกเลิก" onclick="c2xHolding.clearSequesterTypeCarForm()" ></a>
		                    	 			 	</td>
		                    	 			</tr>
		                    	 		</table>
		                    	 		
	                    	 			<table id="SequesterTypeCarGrid" class="nep-grid" style="width:100%; margin-left: 20px; margin-top: 10px; display: none">
	                    	 				<thead>
	                    	 					<tr>
			                    	 				<th>ทะเบียนรถยนต์</th>				                    	 				
			                    	 				<th width="60">ปี</th>			                    	 				
			                    	 				<th width="55">		                    	 					
			                    	 				</th>		                    	 				
			                    	 			</tr>
	                    	 				</thead>
	                    	 				<tbody>		                    	 					
	                    	 				</tbody>
		                    	 		</table>
			                    	</div><!-- //รถยนต์ -->
		                      
		                      		<div id="SequesterTypeOtherContainer" style="display: none; margin-left: 20px; margin-top: 20px;  margin-right:10px;"><!-- อื่นๆ-->
		                    	 		<input type="hidden" id="HiddSequesterTypeOtherData" name="HiddSequesterTypeOtherData" /> 	
		                    	 		<input type="hidden" id="HiddSequesterTypeOtherUID" /> 
		                    	 		<strong>ข้อมูลทรัพย์สินอื่นๆ</strong>	                    	 		
		                    	 		<table style="width: 100%">
		                    	 			<tr>
		                    	 				<td>รายละเอียด:</td>		                    	 				
		                    	 				<td style="width: 437px;"><input id="Other" name="Other" maxlength="500" style="width: 98%"/> *</td>	                    	 				
		                    	 				
		                    	 				<td>
		                    	 			 		<a href="javascript:void(0)" class="icon icon-save" title="บันทึก" onclick="c2xHolding.saveSequesterTypeOther()" ></a>
		                    	 			 		<a href="javascript:void(0)" class="icon icon-cancel" title="ยกเลิก" onclick="c2xHolding.clearSequesterTypeOtherForm()" ></a>
		                    	 			 	</td>
		                    	 			</tr>
		                    	 		</table>
		                    	 		
	                    	 			<table id="SequesterTypeOtherGrid" class="nep-grid" style="width:100%; margin-left: 20px; margin-top: 10px; display: none">
	                    	 				<thead>
	                    	 					<tr>
			                    	 				<th>รายละเอียด</th>          	 				
			                    	 				<th width="55">		                    	 					
			                    	 				</th>		                    	 				
			                    	 			</tr>
	                    	 				</thead>
	                    	 				<tbody>		                    	 					
	                    	 				</tbody>
		                    	 		</table>
			                    	</div><!-- //อื่นๆ-->
		                      </div>
		                    </td>
		              	</tr>                       
                    	<tr>
                    	  <td valign="top" bgcolor="#efefef">รายละเอียด:</td>
                    	  <td colspan="3">
                    	    <textarea name="SequestrationDetail" cols="50" rows="5" id="SequestrationDetail" ><?php echo writeHtml($model->SequestrationDetail)?></textarea>
                            
                    	  </td>
                   	  	</tr>                     
                       	<tr>
	                       	<td bgcolor="#efefef">แนบเอกสาร:</td>
	                       	<td colspan="3">
	                       		<div style="padding:5px 0px;">
                       				<font style="font-size: 11px;">(ไฟล์ jpg, gif หรือ pdf เท่านั้น)</font>
                       			</div>
	                       		<div class="input-file-container single-file">	                       			
	                       			<input  name="Sequestration_Docfile[]" multiple="multiple" type="file" accept=".pdf,.jpg,.jpeg,.gif" />	                       				                       			
	                       		</div> 
	                       	</td>
                       </tr>
                       <tr>
                       		<td colspan="4">
                       			<input type="submit" value="แจ้งอายัด" onclick="return c2xHolding.saveHolding()"/>
                       		</td>
                       </tr>
	                </table>
				<?php }?><!-- \\ ข้อ 3. -->
						
					</form> 
				</td>
			 </tr>
             
             <tr>
                <td align="right" colspan="2">
                    <?php include "bottom_menu.php";?>
                </td>
            </tr>     

</div><!--end page cell-->
</td>
</tr>
</table>
<script id='uploadFileTemplate' type='text/x-kendo-template'>
    <span class='k-progress'></span>
    <div class='file-wrapper'>
           <button type='button' class='k-upload-action'></button>   
           <span class='file-name'>#=name#</span>
           <a href='##' target='_blank' class='file-link' >#=name#</a>
    </div>
</script>
<script type="text/javascript">
	$(function(){
		c2xHolding.handleSelecDate();
		c2xHolding.config({
			InterestRate: <?php echo $interestRate;?>,
			TheCompanyWord: "<?php echo $the_company_word;?>",
			SequestrationType: <?php echo json_encode($SEQUESTRATION_TYPE)?>
		});

		var sequesterPayments = <?php echo json_encode($model->SequestrationPayments) ?>;
		c2xHolding.bindOrgDebtFrom({
			SequesterTypeMoney:<?php echo json_encode($model->SequestrationMoneyDetails) ?>,
			SequesterTypeProperty: <?php echo json_encode($model->SequestrationPropertyDetails) ?>,
			SequesterTypeCar: <?php echo json_encode($model->SequestrationCarDetails) ?>,
			SequesterTypeOther: <?php echo json_encode($model->SequestrationOtherDetails) ?>,
			SequesterPayments: sequesterPayments
	    });

		c2x.handleDdlCascadeProvince({
			ProvinceID: "DdlProvince",
			DistrictID: "DdlDistrict",
			SubDistrictID: "DdlSubDistrict"
		});

		var searchID = <?php echo json_encode($searchID)?>;
		if((searchID != null) && (sequesterPayments == null)){
			$('input[company-id="'+searchID+'"').click();
		}

		var yearpicker = $(".nep-yearpicker");
		$.each(yearpicker, function(){
			// create date picker
			$(this).kendoDatePicker({
			   depth:"decade",  
			   start:"decade",            
	           format: "yyyy",
	           parseFormats: ["yyyy-MM-dd"],
	           culture: "th-TH",
	           footer: cale.footerTemplate, 
	           open: cale.onDatePickerOpen,	          
	       });

		   // recheck user entry year  
			$(this).bind("blur", function(){
				var year = $(this).val();
				if(!isNaN(year) && (year > 1900)){
					var id = $(this).attr("id");
					var picker = $("#" + id).data("kendoDatePicker");
					var selectedDate = picker.value();
					if(selectedDate == null){
						year = year - 543;
						selectedDate = new Date(year, 0, 1, 0,0,0,0); 
						picker.value(selectedDate);
					}
				}else{
					$(this).val("");
				}
			});
		});

		

		// for dummy upload file
// 		var sendingLetterExistingFiles = [];
// 		var letterFileControl = $('#sendingLetter');   
// 		letterFileControl.kendoUpload({
// 	        enabled: true,
// 	        multiple: false,
// 	        async: {
// 	            saveUrl: './attachmenthandler/upload.php',
// 	            removeUrl: './attachmenthandler/remove.php',
// 	            autoUpload: true
// 	        },
// 	        files: sendingLetterExistingFiles,
// 	        /*error: onFileUploadError,
// 	        remove: onFileUploadRemove,
// 	        select: onFileUploadSelect,
// 	        success: onFileUploadSuccess,*/
// 	        template: kendo.template(document.getElementById('uploadFileTemplate').innerHTML),
// 	        localization: {
// 	            select: "เลือกไฟล์"
// 	        }        
// 	    });
	
// 		var companyExistingFiles = [];
// 		var companyFileControl = $('#companyFileUpload');   
// 		companyFileControl.kendoUpload({
// 	        enabled: true,
// 	        multiple: true,
// 	        async: {
// 	            saveUrl: './attachmenthandler/upload.php',
// 	            removeUrl: './attachmenthandler/remove.php',
// 	            autoUpload: true
// 	        },
// 	        files: companyExistingFiles,
// 	        /*error: onFileUploadError,
// 	        remove: onFileUploadRemove,
// 	        select: onFileUploadSelect,
// 	        success: onFileUploadSuccess,*/
// 	        template: kendo.template(document.getElementById('uploadFileTemplate').innerHTML),
// 	        localization: {
// 	            select: "เลือกไฟล์"
// 	        }        
// 	    });	
	});	


							   
	
	
</script>		
</body>
</html>