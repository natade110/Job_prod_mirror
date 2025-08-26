<?php
include_once "db_connect.php";
include_once "session_handler.php";
require_once 'c2x_include.php';
require_once 'scrp_notice.php';

if(!hasCreateRoleSequestration()){
	header ("location: index.php");
}

$interestRate = $DEBT_INTEREST_RATE;

$taskName = (isset($_POST["HiddTaskName"]))? $_POST["HiddTaskName"] : "";
$errorMessage = "";
$dupedKey = false;
$dupedID = 0;
$model = new NoticeDocument();
$manage = new ManageNotice();
$searchCompanyCode = trim($_POST["CompanyCode"]);

$forYear = null;
$searchID = null;
if(is_numeric($_GET["for_year"])){
	$forYear = $_GET["for_year"];
	$taskName = ($taskName == "")? "search" : $taskName;
}else if(is_numeric($_GET["search_id"])){
	$searchID = $_GET["search_id"];
	$taskName = ($taskName == "")? "search" : $taskName;
}


if($taskName == "save"){	
	$lawStatus = $COMPANY_LAW_STATUS->แจ้งโนติส;	
			
	$saveResult = $manage->saveNotice($_POST, $_FILES, 0, $sess_userfullname, $lawStatus, $the_company_word, $hire_docfile_relate_path);	
	$model = $saveResult->Data;

	
	if($saveResult->IsComplete == true){		
		header("location: notice_edit.php?id=$model->NoticeID&added=added");
	}else if($saveResult->DupedKey == true){
		$dupedKey = $saveResult->DupedKey;
		$$dupedID= $saveResult->DupedID;
	}else{
		$errorMessage = $saveResult->Message;
	}	
}else{
	$model = $manage->getNoticeDocumentInput(0, $_POST);
	
	if($taskName == "search"){
		$model->DocumentDate = date("Y-m-d");
	}else if(!empty($_GET["companycode"])){
		$taskName = "search";
		$searchCompanyCode = $_GET["companycode"];
	}
}


/// START BUILDING CONDITIONS
$condition_sql = "";
if($searchID != null){
	$condition_sql .= " and c.CID = ".$searchID;
}else if($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ_สศส || $sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_สศส){

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

if($searchCompanyCode != ""){
	$condition_sql .= " and CompanyCode like '%".mysql_real_escape_string($searchCompanyCode)."%'" ;
}

$lawful_condition = " and ((l.LawfulStatus = '0' or l.LawfulStatus is null) or (l.LawfulStatus = '2')) and c.LawStatus = ".$COMPANY_LAW_STATUS->ยังไม่ดำเนินการ;


if(strlen($_POST["CompanyNameThai"]) > 0){

	$name_exploded_array = explode(" ",mysql_real_escape_string($_POST["CompanyNameThai"]));

	for($i=0; $i<count($name_exploded_array);$i++){

		if(strlen(trim($name_exploded_array[$i]))>0){
			$condition_sql .= " and c.CompanyNameThai like '%".mysql_real_escape_string($name_exploded_array[$i])."%'";
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

if(is_numeric($_POST["SearchYear"])){
	$forYear = $_POST["SearchYear"];
}

//this default year
$lawfulyear_condition = "";
$lawfulYear = $theEndYear - 4;

if(($forYear != null) && ($forYear >= 2013)){
	$lawfulyear_condition =  " and ((l.Year = ".$forYear.") and (c.BranchCode < 1))";
}else if($forYear != null){
	$lawfulyear_condition =  " and (l.Year = ".$forYear.")";
}else if($lawfulYear >= 2013){
	$lawfulyear_condition = " and ((l.Year in(2011, 2012) or (l.Year >= 2013 and l.Year <= ".$lawfulYear." and (c.BranchCode < 1))))";
}else{
	$lawfulyear_condition = " and ((l.Year >= 2011) and (l.Year <= ".$lawfulYear."))";
}



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
	FROM company c
		
	LEFT outer JOIN companytype ct ON c.CompanyTypeCode = ct.CompanyTypeCode
	LEFT outer JOIN provinces p ON c.province = p.province_id
	JOIN lawfulness l ON c.CID = l.CID 
	
	WHERE

	$condition_sql
		
	$lawful_condition
	
	$lawfulyear_condition
	
	
	Group BY  c.CID , Province , CompanyCode , BranchCode , CompanyTypeName , CompanyNameThai , province_name  
	
	ORDER BY CompanyNameThai asc
	
	$the_limit
	";
	

		
}
// End Creat Sql
?>
<?php include "header_html.php";?>
				<td valign="top" style="padding-left:5px;"> <!-- td top --> 
					<script type="text/javascript" src="./scripts/manage.notice.js"></script>
					<style type="text/css" >
						#DdlProvince{
							width:173px;
						}					
						.ddl-bank-width select{
							width:357px;
						}
					</style>
                    <h2 class="default_h1" style="margin:0; padding:0;"  >การแจ้งโนติส</h2>                   
                    <div style="padding-top:10px; font-weight: bold;">
                        1. ค้นหาสถานประกอบการที่ต้องการแจ้งโนติส
					</div>
					
					<form method="post" action="" id="search_form" enctype="multipart/form-data">
						<input type="hidden" id="SearchYear" name="SearchYear" value="<?php echo $forYear?>">
						<input type="hidden" id="TotalAmount" name="TotalAmount" value="<?php echo $model->TotalAmount?>"/>
						<input type="hidden" id="HiddTaskName" name="HiddTaskName" value="<?php echo $taskName;?>"> 
						<input type="hidden" id="HiddCID" name="HiddCID" value="<?php echo $model->CID?>"/>
						<input type="hidden" id="HiddCompanyCode" name="HiddCompanyCode" value="<?php echo $model->CompanyCode?>"/>
						<input type="hidden" id="HiddBranchCode" name="HiddBranchCode" value="<?php echo $model->BranchCode?>"/>	
						<input type="hidden" id="NoticeDetails" name="NoticeDetails" />					  
						<table style=" padding:10px 0 0px 0; " id="general">							
	                   	    <tr>	                        	
	                    	   <td bgcolor="#efefef">ชื่อ:  </td>
	                    	   <td>
	                          	   <input type="text" name="CompanyNameThai" value="<?php echo writeHtml($_POST["CompanyNameThai"]);?>" />     
	                           </td>
	                           <td bgcolor="#efefef"><?php echo $the_code_word;?>:</td>
	                           <td>
	                                <input type="text" name="CompanyCode" value="<?php echo writeHtml($searchCompanyCode);?>" />                      
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
	                  	  		 <input type="submit" value="แสดง" name="mini_search" onclick="return c2xNotice.setTask('search')"/>
	                  	  	</td>
	                  	  </tr>
	                  	  
						</table>
						<hr />
						
				<?php if($taskName != ""){?>
						<div style="padding:10px 0 10px 0; font-weight: bold;">2. เลือก<?php echo $the_company_word;?>ที่ต้องการแจ้งโนติส(ค้างชำระ 4 ปี)</div>
						<table style="width: 100%">
							<tr>
								<td>
									<font color="#006699">แสดงข้อมูล <?php echo $starting_index+1;?>-<?php echo ($record_count_all < $starting_index+$per_page) ? $record_count_all : $starting_index+$per_page;?> จากทั้งหมด <?php echo $record_count_all; ?> รายการ</font> 
								</td>
								<td>
									<div style="padding:5px 0 0px 0;" align="right">
						                            แสดงข้อมูล:						                            
			                            <select name="start_page" onchange="c2xNotice.changePagination()">
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
							$cid = 0;						
							while ($post_row = mysql_fetch_array($org_result)) {	
								$cid = $post_row["CID"];
								?>
								<tr>
									<td>
										<input type="checkbox" class="org-checkbox" onclick="c2xNotice.onSelectedOrg(this)" 
											company-id="<?php  writeHtml($cid);?>"
											company-code="<?php  writeHtml($post_row["CompanyCode"]);?>" 
											branch-code="<?php  writeHtml($post_row["BranchCode"]);?>"
																			
										 /> 
									</td>									
									<td>										
			                           	<a href="organization.php?id=<?php writeHtml($post_row["CID"]);?>"><?php echo doCleanOutput($post_row["CompanyCode"]);?></a>  
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
                         <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <a href="notice_edit.php?id=<?php echo $dupedID?>">หนังสือเลขที่ <?php echo $model->GovDocumentNo?></a> มีอยู่ในระบบแล้ว กรุณาใส่หนังสือเลขที่ใหม่</div>
                         
                    <?php
						}else if(!empty($errorMessage)){?>
					     <hr />
						 <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <?php echo nl2br($errorMessage);?></div>
                        
					<?php 	
						}
				     ?>
				
					<hr />
	                <strong>3. ใส่ข้อมูลโนติส</strong>
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
		                    </td>
		              	</tr>
		              	
                      	</tr>
                    	<tr>
                    	  <td valign="top" bgcolor="#efefef">รายละเอียด:</td>
                    	  <td colspan="3">
                    	    <textarea name="NoticeDetail" cols="50" rows="5" id="NoticeDetail" ><?php echo writeHtml($model->NoticeDetail)?></textarea>
                            
                    	  </td>
                   	  	</tr>                     
                       	<tr>
	                       	<td bgcolor="#efefef">แนบเอกสาร:</td>
	                       	<td colspan="3">
	                       		<div style="padding:5px 0px;">
                       				<font style="font-size: 11px;">(ไฟล์ jpg, gif หรือ pdf เท่านั้น)</font>
                       			</div>
	                       		<div class="input-file-container single-file">	                       			
	                       			<input  name="NoticeDocument_Docfile[]" multiple="multiple" type="file" accept=".pdf,.jpg,.jpeg,.gif" />	                       				                       			
	                       		</div> 
	                       	</td>
                       </tr>
                       <tr>
                       		<td colspan="4">                       			
                       			<input type="submit" value="แจ้งโนติส" onclick="return c2xNotice.saveNotice()"/>                       			
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
		c2xNotice.handleSelecDate();
		c2xNotice.config({
			InterestRate: <?php echo $interestRate;?>,
			TheCompanyWord: "<?php echo $the_company_word;?>"			
		});

		var noticeDetails = <?php echo json_encode($model->NoticeDetails) ?>;
		c2xNotice.bindOrgDebtFrom({			
			NoticeDetails: noticeDetails
	    });

	    var searchID = <?php echo json_encode($searchID)?>;
	    if((searchID != null) && (noticeDetails == null)){
		    $('input[company-id="'+searchID+'"').click();
	    }
		

		
	});	


							   
	
	
</script>		
</body>
</html>