<?php
include_once "db_connect.php";
require_once 'c2x_include.php';
require_once 'scrp_proceeding.php';

if(!hasCreateRoleSequestration()){
	header ("location: index.php");
}


$interestRate = $DEBT_INTEREST_RATE;

$taskName = (isset($_POST["HiddTaskName"]))? $_POST["HiddTaskName"] : "";
$errorMessage = "";
$dupedKey = false;
$dupedPID = 0;
$condition_sql = "";
$model = new Proceeding();
$canSave = ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี  || $sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ);
if($taskName == "save"){
	$manage = new ManageProceeding();	
	$saveResult = $manage->saveProceeding($_POST, $_FILES, 0);
	$model = $saveResult->Data;
	if($saveResult->IsComplete == true){
		header("location: proceedings_edit.php?id=$model->PID&added=added");
	}else if($saveResult->DupedKey == true){
		$dupedKey = $saveResult->DupedKey;
		$dupedPID = $saveResult->DupedPID;
	}else{
		$errorMessage = $saveResult->Message;
	}
}else{
	if(is_numeric($_GET["search_id"])){
		$searchID = $_GET["search_id"];
		$taskName = ($taskName == "")? "search" : $taskName;
		$condition_sql .= " and c.CID = $searchID";
	}
	
	if($taskName == "search"){
		$model->RequestDate = date("Y-m-d");
	}
}

/// START BUILDING CONDITIONS


if($sess_accesslevel == 6 || $sess_accesslevel == 7){

	$condition_sql .= " and c.CompanyTypeCode >= 200  and c.CompanyTypeCode < 300";

}else{

	$condition_sql .= " and c.CompanyTypeCode < 200";
}



/*สร้างได้เฉพาะกรุงเทพ*/

if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี){
	$condition_sql .= " AND p.province_code = ".BANGKOK_PROVINCE_CODE;

}else if(($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ) && $sess_islawyer){
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
		'CompanyCode'
		,'Province'	
		,'CompanyTypeCode'
		,'BusinessTypeCode'
);

for($i = 0; $i < count($input_fields); $i++){

	if(strlen($_POST[$input_fields[$i]])>0){
			
		$use_condition = 1;
			
		if($input_fields[$i] == "Province"  ){
			$condition_sql .= " and c.$input_fields[$i] like '".doCleanInput($_POST[$input_fields[$i]])."'";		
		}else{
			$condition_sql .= " and c.$input_fields[$i] like '%".doCleanInput($_POST[$input_fields[$i]])."%'";
		}
			
	}
}

$lawful_condition = " and ((l.LawfulStatus = '0' or l.LawfulStatus is null) or (l.LawfulStatus = '2'))";


if(strlen($_POST["CompanyNameThai"]) > 0){

	$name_exploded_array = explode(" ",doCleanInput($_POST["CompanyNameThai"]));

	for($i=0; $i<count($name_exploded_array);$i++){

		if(strlen(trim($name_exploded_array[$i]))>0){
			$condition_sql .= " and c.CompanyNameThai like '%".doCleanInput($name_exploded_array[$i])."%'";
		}
	}
}


/// get lawful year
$theEndYear = 0;
if(date("m") >= 9){
	$theEndYear = date("Y")+1; //new year at month 9
}else{
	$theEndYear = date("Y");
}
//this default year
$lawfulYear = $theEndYear - 4;

$proceedingTypeMapping = getProceedingCreateTypeMapping();
$pTypeText = "";
$pType = 0;
if($_POST["ProceedingType"] != ""){
	$pType = $_POST["ProceedingType"];
	$pTypeText = $proceedingTypeMapping[$pType];
}else{
	$pType = $PROCEEDING_TYPE->ส่งพนักงานอัยการ;
	$pTypeText = $proceedingTypeMapping[$pType];
}

$lawfulyear_condition = "";
$lawStatus_condition = "";

if($pType == $PROCEEDING_TYPE->ส่งพนักงานอัยการ){
	if($lawfulYear >= 2013){
		$lawfulyear_condition = " and ((l.Year in(2011, 2012) or (l.Year >= 2013 and l.Year <= ".$lawfulYear." and (c.BranchCode < 1))))";
	}else{
		$lawfulyear_condition = " and ((l.Year >= 2011) and (l.Year <= ".$lawfulYear."))";
	}
	
	$lawStatus_condition = " and c.LawStatus in(1,2,3,31)";
}else{
	$lawfulyear_condition = " and ((l.Year in(2011, 2012) or (l.Year >= 2013 and (c.BranchCode < 1))))";
	$lawStatus_condition = " and c.LawStatus not in(6,9)";
}
$model->PType = $pType;



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
	, province_name
	FROM company c
	
	LEFT outer JOIN companytype ct ON c.CompanyTypeCode = ct.CompanyTypeCode
	LEFT outer JOIN provinces p ON c.province = p.province_id
	JOIN lawfulness l ON c.CID = l.CID
	
	WHERE
	
	$condition_sql
	
	$lawful_condition
	
	$lawfulyear_condition   

	$lawStatus_condition
	
	Group BY  c.CID , Province , CompanyCode , BranchCode , CompanyTypeName , CompanyNameThai , province_name
	
	ORDER BY CompanyNameThai asc
	) tmp
	";
	
	
	error_log($count_org_sql);
	
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
	, NoticeReceivedDate
	FROM company c
		
	LEFT outer JOIN companytype ct ON c.CompanyTypeCode = ct.CompanyTypeCode
	LEFT outer JOIN provinces p ON c.province = p.province_id
	JOIN lawfulness l ON c.CID = l.CID 
	LEFT JOIN (
      SELECT CID, MAX(ReceivedDate) AS NoticeReceivedDate  FROM noticedocument GROUP BY CID
    )n ON c.CID = n.CID 
	
	WHERE

	$condition_sql
		
	$lawful_condition
	
	$lawfulyear_condition
	
	$lawStatus_condition
		
	Group BY  c.CID , Province , CompanyCode , BranchCode , CompanyTypeName , CompanyNameThai , province_name  
	
	ORDER BY CompanyNameThai asc
	
	$the_limit
	";
	
	
		
}
// End Creat Sql
?>
<?php include "header_html.php";?>
				<td valign="top" style="padding-left:5px;"> <!-- td top --> 
					<!--<script type="text/javascript" src="./scripts/manage.holdingcreate.js"></script>-->
					<script type="text/javascript" src="./scripts/manage.proceedingscreate.js"></script>
					<style type="text/css" >
						#DdlProvince{
							width:173px;
						}					
						
					</style>
                    <h2 class="default_h1" style="margin:0; padding:0;"  >การส่งพนักงานอัยการ/ยื่นขอรับเงินกรณีล้มละลาย</h2>      
                    
                    
                                 
                    
					
					<form method="post" action="" id="search_form" enctype="multipart/form-data">
						<input type="hidden" id="TotalAmount" name="TotalAmount" value="<?php echo $_POST["TotalAmount"]?>"/>
						<input type="hidden" id="HiddTaskName" name="HiddTaskName" value="<?php echo $taskName;?>">
						<input type="hidden" id="ProceedingPayments" name="ProceedingPayments" />
						<input type="hidden" id="HiddCID" name="HiddCID" value="<?php echo $model->CID?>"/>	
						<input type="hidden" id="HiddBranchCode" name="HiddBranchCode" value="<?php echo $model->BranchCode?>"/>
						<input type="hidden" id="HiddCompanyCode" name="HiddCompanyCode" value="<?php echo $model->CompanyCode?>"/>	
						<input type="hidden" id="CalDate" name="CalDate" value="<?php echo (isset($model->CalDate)? $model->CalDate->format("Y-m-d") :'' )?>">
						<input type="hidden" id="NoticeReceivedDate" name="NoticeReceivedDate" value="<?php echo $model->NoticeReceivedDate; ?>">
						<input type="hidden" id="Start2011InterestDate" name="Start2011InterestDate" value="<?php echo $model->Start2011InterestDate; ?>">
	                    
	                    <div style="padding-top:10px; font-weight: bold;">
	                        1. ค้นหาสถานประกอบการ
						</div>
						
						<table style=" padding:10px 0 0px 0; " id="general">		
							<tr>
		                	
		                		<td bgcolor="#efefef">สร้างกระบวนการ: </td>
		                    	<td>	                    		
	                                <?php
													   
	                                     echo createDropDownListFromMapping("ProceedingType", getProceedingCreateTypeMapping(), $model->PType, null)
														   
									?>  
		                    	</td>	                		
		                	</tr>					
	                   	    <tr>	                        	
	                    	   <td bgcolor="#efefef">ชื่อ:  </td>
	                    	   <td>
	                          	   <input type="text" name="CompanyNameThai" value="<?php echo $_POST["CompanyNameThai"];?>" />     
	                           </td>
	                           <td bgcolor="#efefef"><?php echo $the_code_word;?>:</td>
	                           <td>
	                                <input type="text" name="CompanyCode" value="<?php echo $_POST["CompanyCode"];?>" />                      
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
	                  	  		 <input type="submit" value="แสดง" name="mini_search" onclick="return c2xProceeds.setTask('search')"/>
	                  	  	</td>
	                  	  </tr>
	                  	  
						</table>
						<hr />
						
				<?php if($taskName != ""){?>
						<div style="padding:10px 0 10px 0; font-weight: bold;">2. เลือก<?php echo $the_company_word;?>ที่ต้องการ</div>
						<table style="width: 100%">
							<tr>
								<td>
									<font color="#006699">แสดงข้อมูล <?php echo $starting_index+1;?>-<?php echo ($record_count_all < $starting_index+$per_page) ? $record_count_all : $starting_index+$per_page;?> จากทั้งหมด <?php echo $record_count_all; ?> รายการ</font> 
								</td>
								<td>
									<div style="padding:5px 0 0px 0;" align="right">
						                            แสดงข้อมูล:						                            
			                            <select name="start_page" onchange="c2xProceeds.changePagination();">
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
							</tr>
							
							<?php 
						
							$org_result = mysql_query($get_org_sql);
							$org_year = 0;
							while ($post_row = mysql_fetch_array($org_result)) {	
								$org_year = doCleanOutput($post_row["Year"]);
								$noticeReceivedDate = $post_row["NoticeReceivedDate"];
								
								$start2011InterestDate = ($noticeReceivedDate != null)? new DateTime($noticeReceivedDate) : null;
								$start2011InterestDate = ($start2011InterestDate != null)? $start2011InterestDate->modify("+30 day") : null;
								$start2011InterestDateFormate = ($start2011InterestDate != null)? $start2011InterestDate->format("Y-m-d") : null;
								?>
								<tr>
									<td>
										<input type="checkbox" class="org-checkbox" onclick="c2xProceeds.onSelectedOrg(this)" 
											company-id="<?php writeHtml($post_row["CID"]);?>"
											company-code="<?php writeHtml($post_row["CompanyCode"]);?>" 
											branch-code="<?php writeHtml($post_row["BranchCode"]);?>" 
											start-2011-interest-date="<?php  writeHtml($start2011InterestDateFormate)?>"/> 
									</td>									
									<td>										
			                           	<a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?>"><?php echo doCleanOutput($post_row["CompanyCode"]);?></a>  
									</td>
									<td><?php writeHtml($post_row["CompanyTypeName"]);?></td>
									<td><?php writeHtml($post_row["CompanyNameThai"]);?></td>
			                        <td><?php writeHtml($post_row["province_name"]);?></td>
			                        	                            	
								</tr>
							<?php }?>
						</table><!-- Search Result -->
						
						
						
						
				<?php }?> <!--  \\ ข้อ 2. -->
				
				<?php if(($taskName != "") && ($record_count_all > 0)){?>
					<?php 
						if($dupedKey == true){
					?>		
						 <hr />					
                        <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <a href="proceedings_edit.php?id=<?php echo $dupedPID?>">หนังสือเลขที่ <?php echo $model->GovDocumentNo?></a> มีอยู่ในระบบแล้ว กรุณาใส่หนังสือเลขที่ใหม่</div>
                         
                    <?php
						}else if(!empty($errorMessage)){?>
					     <hr />
						 <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <?php echo $errorMessage;?></div>
                        
					<?php 	
						}
				     ?>
				
					<hr />
	                <strong>3. ใส่ข้อมูลการดำเนินคดี</strong>
	                <table style=" padding:10px 0 0px 0;">
	                	<tr>	                		 
	                		 <td bgcolor="#efefef">วันที่: </td>
	                    	 <td>
	                           
	                            <?php
												   
								   $selector_name = "RequestDate";
								   
								   $this_date_time = (!is_null($model->RequestDate))? ((gettype($model->RequestDate) == "string")? $model->RequestDate : $model->RequestDate->format("Y-m-d")) : "0000-00-00";
								 
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
	                    	<td><input name="GovDocumentNo" type="text" id="GovDocumentNo" value="<?php writeHtml($model->GovDocumentNo)?>" maxlength="25"/> *</td>
	                	</tr>
	                	<tr>
	                		<td bgcolor="#efefef">กระบวนการ: </td>
	                    	<td><span id="ProceedingTypeLabel"><?php writeHtml($pTypeText)?></span></td>	                		
	                	</tr>	
	                	
	                	<tr>
		                    <td valign="top" bgcolor="#efefef">จำนวนเงินต้นที่อายัด:</td>
		                    <td colspan="3">		                    	 
		                    	 <div id="OrgDebtBlock" style="display: none; margin-top: 0px;">
		                    	 	
		                    	 </div>
		                    	 <div id="OrgDebtDate" style="margin-top: 5px; margin-bottom: 3px; display: none"></div>
		                    	 <div id="Org2011DebtBlock" style="display: none; margin-top: 7px;"></div>
		                    </td>
		              	</tr>		              	
                      
                    	<tr>
                    	  <td valign="top" bgcolor="#efefef">รายละเอียด:</td>
                    	  <td colspan="3">
                    	    <textarea name="Detail" cols="50" rows="5" id="Detail" ><?php echo writeHtml($model->Detail)?></textarea>
                            
                    	  </td>
                   	  	</tr>                     
                       	<tr>
	                       	<td bgcolor="#efefef">แนบเอกสาร:</td>
	                       	<td colspan="3">
	                       		<div style="padding:5px 0px;">
                       				<font style="font-size: 11px;">(ไฟล์ jpg, gif หรือ pdf เท่านั้น)</font>
                       			</div>
	                       		<div class="input-file-container single-file">	                       			
	                       			<input  name="Proceedings_Docfile[]" multiple="multiple" type="file" accept=".pdf,.jpg,.jpeg,.gif" />	                       				                       			
	                       		</div> 
	                       	</td>
                       </tr>
                       <tr>
                       		<td colspan="4">                       			
                       			<input type="submit" value="เพิ่มข้อมูล" onclick="return c2xProceeds.saveProceeds()"/>
                       			
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
		c2xProceeds.config({
			InterestRate: <?php echo $interestRate;?>,
			TheCompanyWord: "<?php echo $the_company_word;?>",
			ProceedsTypeChargedType: <?php echo $PROCEEDING_TYPE->ศาลสั่งฟ้อง?>			
		});	

		var proceedingPayments = <?php echo json_encode($model->ProceedingPayments) ?>;
		c2xProceeds.bindOrgDebtFrom({			
			ProceedingPayments: proceedingPayments,
			IsEditPage:false		    
	    });
		
			
		var searchID = <?php echo json_encode($searchID)?>;
		if((searchID != null) && (proceedingPayments == null)){
			$('input[company-id="'+searchID+'"').click();
		}
	
		

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