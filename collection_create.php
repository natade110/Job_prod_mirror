<?php
include_once "db_connect.php";
include_once "session_handler.php";
require_once 'c2x_include.php';
require_once 'scrp_add_collection.php';

if(!hasCreateRoleCollection()){
	header ("location: index.php");
}


$taskName = (isset($_POST["HiddTaskName"]))? $_POST["HiddTaskName"] : "";
$errorMessage = "";
$dupedKey = false;
$dupedCollectionID = 0;
$model = new Collection();
$manage = new ManageCollection();
$searchCompanyCode = trim($_POST["CompanyCode"]);


$condition_sql = "";
if(is_numeric($_GET["search_id"])){
	$have_search_id = 1;
	$taskName = ($taskName == "")?"search" : $taskName;

	$condition_sql .= "and z.CID = ".$_GET["search_id"];
	//$company_name_row = getFirstRow("select CompanyNameThai,CompanyTypeCode from company where CID = '".$_GET["search_id"]."'");

	//$company_name_to_use = formatCompanyName($company_name_row["CompanyNameThai"],$company_name_row["CompanyTypeCode"]);
	
}

if(strlen($_GET["for_year"])==4){
	$for_year = $_GET["for_year"];
	$taskName = ($taskName == "")?"search" : $taskName;
}

if(is_numeric($_GET["LawfulFlag"])){
	$_POST["LawfulStatus"] = $_GET["LawfulFlag"];
	$taskName = ($taskName == "")?"search" : $taskName;
}

if($taskName == "save"){
	$saveResult = $manage->saveCollection($_POST, $_FILES, 0, $sess_username, $sess_userfullname, $the_company_word, $hire_docfile_relate_path);	
	$model = $saveResult->Data;
	
	if($saveResult->IsComplete == true){		
		header("location: collection_edit.php?id=$model->CollectionID&added=added");
	}else if($saveResult->DupedKey == true){
		$dupedKey = $saveResult->DupedKey;
		$dupedCollectionID = $saveResult->DupedCollectionID;
	}else{
		$errorMessage = $saveResult->Message;
	}	
}else{
	$model = $manage->getCollectionInput(0, $_POST);
	
	if($taskName == "search"){
		$model->RequestDate = date("Y-m-d");	
	}else if(!empty($_GET["companycode"])){
		$taskName = "search";
		$searchCompanyCode = $_GET["companycode"];
	}
}


/// START BUILDING CONDITIONS


//--- ประจำปี
$cur_year = date("Y");

if(isset($_POST['ddl_year'])){
	$cur_year = $_POST['ddl_year'];
}
 
if($for_year){
	$cur_year = $for_year;
}

if($cur_year >= 2013){
	$condition_sql .= " and BranchCode < 1";
	$is_2013 = 1;
}

if($sess_accesslevel == 6 || $sess_accesslevel == 7){

	$condition_sql .= " and z.CompanyTypeCode >= 200  and z.CompanyTypeCode < 300";

}else{

	$condition_sql .= " and z.CompanyTypeCode < 200";

}

$zone = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
if((($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พก) || ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ)) && ($zone != null)){
	$zone = getFirstItem("select zone_id from zone_user where user_id = '$sess_userid'");
	$condition_sql .= " AND
	(
	
		z.District in (			
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
				zone_id = '$zone'		
			)			
		)
		or
		z.district_cleaned in (
			
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
				zone_id = '$zone'
				
			)			
		)
	)";

}

if($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พมจ){
	$condition_sql .= " and Province = ".$sess_meta;

}





$input_fields = array(	
		'LawfulStatus'
		,'Province'	
		,'CompanyTypeCode'
		,'BusinessTypeCode'
);

for($i = 0; $i < count($input_fields); $i++){

	if(strlen($_POST[$input_fields[$i]])>0){
			
		$use_condition = 1;
			
		if($input_fields[$i] == "Province"  ){
			$condition_sql .= " and z.$input_fields[$i] like '".mysql_real_escape_string($_POST[$input_fields[$i]])."'";		
		}else if($input_fields[$i] == "LawfulStatus"  ){
			$condition_sql .= " and y.$input_fields[$i] like '%".mysql_real_escape_string($_POST[$input_fields[$i]])."%'";
		}else{
			$condition_sql .= " and z.$input_fields[$i] like '%".mysql_real_escape_string($_POST[$input_fields[$i]])."%'";
		}
			
	}
}


if($searchCompanyCode != ""){
	$condition_sql .= " and CompanyCode like '%".mysql_real_escape_string($searchCompanyCode)."%'" ;
}


$lawful_condition = " and ((y.LawfulStatus = '0' or y.LawfulStatus is null) or (y.LawfulStatus = '2')) ";


if(strlen($_POST["CompanyNameThai"]) > 0){

	$name_exploded_array = explode(" ",mysql_real_escape_string($_POST["CompanyNameThai"]));

	for($i=0; $i<count($name_exploded_array);$i++){

		if(strlen(trim($name_exploded_array[$i]))>0){
			$condition_sql .= " and z.CompanyNameThai like '%".mysql_real_escape_string($name_exploded_array[$i])."%'";
		}
	}
}


$lawfulyear_condition = " and (y.Year >= 2011) ";


// Start Crate Sql
// Paginator
$per_page = 20;
$record_count_all = 0;
$num_page = 0;
$cur_page = 1;

$get_org_sql = "";
$org_result = null;

if($condition_sql != ""){
	$condition_sql = substr($condition_sql, 4);
	
	$count_org_sql = "
	SELECT count(z.CID)
	FROM company z
	LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
	LEFT outer JOIN provinces c ON z.province = c.province_id
	JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year'
	WHERE 
	$condition_sql
	$lawful_condition
	$lawfulyear_condition
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
					, email
				FROM company z
				LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
				LEFT outer JOIN provinces c ON z.province = c.province_id
				JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year'
				where

	$condition_sql
		
	$lawful_condition
	
	$lawfulyear_condition
		
	ORDER BY CompanyNameThai asc
	
	$the_limit
	";
	

		
}
// End Creat Sql
?>
<?php include "header_html.php";?>
<td valign="top" style="padding-left:5px;"> <!-- td top --> 
<!-- <script type="text/javascript" src="./scripts/manage.holdingcreate.js"></script> 
	<style type="text/css" >
		#DdlProvince{
			width:173px;
		}					
		
	</style>-->
     <h2 class="default_h1" style="margin:0; padding:0;"  >การส่งจดหมายทวงถาม</h2>                   
     <div style="padding-top:10px; font-weight: bold;">
         1. ค้นหาสถานประกอบการที่ต้องการส่งจดหมายทวงถาม
	</div>
					
	<form method="post" action="" id="search_form" enctype="multipart/form-data">
		<input type="hidden" id="HiddTaskName" name="HiddTaskName" value="<?php echo $taskName;?>"> 
		<input type="hidden" id="HiddCompanyCode" name="HiddCompanyCode" value="<?php echo $model->CompanyCode?>"/>
	<table style=" padding:10px 0 0px 0; " id="general">	
		<tr>
        	<td bgcolor="#efefef">ประจำปี:</td>
            <td><?php include "ddl_year.php";?></td>
 	        <td bgcolor="#efefef">สถานะ:  </td>           
     		<td>
     			<select name="LawfulStatus" id="LawfulFlag_search">
    				<option value="" selected="selected">-- all --</option>
					<option value="0" <?php if($_POST["LawfulStatus"] == "0"){echo "selected='selected'";}?>>ไม่ทำตามกฏหมาย</option>
				    <option value="2" <?php if($_POST["LawfulStatus"] == "2"){echo "selected='selected'";}?>>ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน</option>
				</select>
			</td>	            	
     	</tr>						
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
              <td><?php include "ddl_org_type.php";?>  </td>
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
               //$defaultLabel = ($provinceList != null && (count($provinceList) > 1))? "-- select --" : null;
               $defaultbankok = ($_POST["Province"]== null)? getBangkokProvinceIDByCode():$_POST["Province"];
               echo createDropDownListFromMapping("Province", $provinceList, $defaultbankok, null);
               ?>
               </td>
               <td >&nbsp;</td>
               <td>&nbsp;</td>
          </tr>
          
          <tr>
               <td colspan="4">
               <input type="submit" value="แสดง" name="mini_search" onclick="return setTask('search')"/>
               </td>
          </tr>
    </table>
		<hr />
<?php if($taskName != ""){?>
	<div style="padding:10px 0 10px 0; font-weight: bold;">2. เลือก<?php echo $the_company_word;?>ที่ต้องการส่งจดหมายทวงถาม</div>
	<table style="width: 100%">
		<tr>
			<td>
				<font color="#006699">แสดงข้อมูล <?php echo $starting_index+1;?>-<?php echo ($record_count_all < $starting_index+$per_page) ? $record_count_all : $starting_index+$per_page;?> จากทั้งหมด <?php echo $record_count_all; ?> รายการ</font> 
			</td>
			<td>
				<div style="padding:5px 0 0px 0;" align="right">
			                            แสดงข้อมูล:						                            
                            <select name="start_page" onchange="changePagination()">
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
	<table border="0" style="color: #006699">
	   <tr>
	      	<td>
	         	<img src="decors/red.gif" alt="ไม่ทำตามกฎหมาย" title="ไม่ทำตามกฎหมาย">
	      	</td>
          	<td valign="middle">= ไม่ทำตามกฎหมาย </td>
          	<td>
              	<img src="decors/yellow.gif" alt="ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน" title="ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน">
          	</td>
          	<td valign="middle">= ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน </td>
        </tr>
    </table>						
	<table class="nep-grid" width="100%">
		<tr bgcolor="#9C9A9C" align="center" >
			<th >
				<input class="chk-collection" name="chk_all" id="chk_all" type="checkbox" value="1" onclick="checkOrUncheck();" 
				<?php
					if($have_search_id){
						echo "checked='checked'";
					}
				?>
				/>
			</th>
			<th >
				<div align="center"><span class="column_header"><?php echo $the_code_word;?> </span></div>
			</th>
			<th>
				<div align="center">
					<span class="column_header">
					<?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
						ประเภท
					<?php }else{ ?>
						ประเภทกิจการ
					<?php }?>
					</span>
				</div>
			</th>
			<th>
				<div align="center">
					<span class="column_header"> ชื่อ
						<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){?>
							
						<?php }else{?>                            
							นายจ้างหรือ
						<?php }?>
						<?php echo $the_company_word;?>
					</span>
				</div>
			</th>
			<th>
				<div align="center"><span class="column_header">จังหวัด</span></div>
			</th>
			<th>
				<div align="center">
					<span class="column_header">
						<?Php if($is_2013){?>
							<?php if(($sess_accesslevel == 6 || $sess_accesslevel == 7)){?>
								จำนวน<?php echo $the_employees_word;?>
							<?php }else{ ?>
								จำนวน<?php echo $the_employees_word;?><br>รวมทุกสาขา
							<?php }?>
						<?php }else{?>
							จำนวน<?php echo $the_employees_word;?>
						<?php }?>
					</span>
				</div>
			</th>
			<th>
				<div align="center"><span class="column_header">สถานะ</span></div>
			</th>
        </tr>
		
	   <!-- generate rows -->
        <?php 
      
		$org_result = mysql_query($get_org_sql);
		
		
		
		while ($post_row = mysql_fetch_array($org_result)) {
			$total_records++;
			$this_province = $post_row["Province"];
			$employee_to_use = $post_row["lawful_employees"];
			
			if($employee_to_use == 0){
				$employee_to_use = $post_row["company_employees"];
			
				if($is_2013){
					//sum employees from all brances
					$sum_sql = "select sum(Employees) from company where CompanyCode = '".$post_row["CompanyCode"]."'";
					$employee_to_use = getFirstItem($sum_sql);
				}
			}	
		
		?>
			<tr>
			<?php 
				$js_do_check .= "document.getElementById('chk_$total_records').checked = true;";
				$js_do_uncheck .= "document.getElementById('chk_$total_records').checked = false;";
			?>
				<td >
					<input class="chk-collection" name="chk_<?php echo $total_records; ?>" id="chk_<?php echo $total_records; ?>" type="checkbox" value="<?php echo doCleanOutput($post_row["CID"]);?>"
						<?php
							if($have_search_id){
								echo "checked='checked'";
							}
						?>
					/>
				</td>
				<td >
					<a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?><?php
							if(!$is_2013){
								echo "&all_tabs=1";
							}
					?>&year=<?php echo $year;?>"><?php echo doCleanOutput($post_row["CompanyCode"]);?></a>                          
				</td>
				<td><?php echo doCleanOutput($post_row["CompanyTypeName"]);?></td>
				<td><?php echo doCleanOutput($post_row["CompanyNameThai"]);?></td>
				<td><?php echo doCleanOutput($post_row["province_name"]);?></td>
				<td align="right"><div align="right"><?php echo number_format(doCleanOutput(default_value($employee_to_use,0)));?></div></td>
				<td>
					<div align="center"><?php echo getLawfulImage(($post_row["lawfulness_status"])); ?></div>                         
				</td>                            	
			</tr>
		<?php }?>
	</table><!-- Search Result -->
<?php }?> 

<!--  \\ ข้อ 2. -->

<?php if(($taskName != "") && ($record_count_all > 0)){?>
<?php 
	if($dupedKey == true){
?>		
	<hr />					
    <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <a href="view_collection_doc.php?id=<?php echo $dupedCollectionID?>">หนังสือเลขที่ <?php echo $model->GovDocumentNo?></a> มีอยู่ในระบบแล้ว กรุณาใส่หนังสือเลขที่ใหม่</div>
                         
<?php
	}else if(!empty($errorMessage)){?>
    <hr />
	<div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <?php echo nl2br($errorMessage);?></div>
                        
<?php }?>
	<hr />
    <strong>3. ส่งจดหมายทวงถาม</strong>
    <div style="padding:5px 0 0 0">
        <input name="total_records" type="hidden" value="<?php echo $total_records; ?>" />
    	<input name="Year" id="Year" type="hidden" value="<?php echo $cur_year?>">
        <input name="send_to_all" id="send_to_all"  type="checkbox" value="
        <?php 
			$the_all_condition_sql = $condition_sql . " " . $lawful_condition;
			echo $the_all_condition_sql;
		?> 	"> 
	<strong style="color:#006699">ส่งแจ้งทั้ง <?php echo $record_count_all; ?> <?php echo $the_company_word;?></strong>
    </div>
    
 <table style=" padding:10px 0 0px 0;">
    <tr>
        <td bgcolor="#efefef">ประจำปี: </td>
        <td colspan="5"><?php echo formatYear($cur_year)?></td>
	</tr>
    <tr>	                		 
	     <td bgcolor="#efefef">วันที่: </td>
	     <td>
	     <?php
							   
			   $selector_name = "RequestDate";
			   
			   $this_date_time = (!is_null($model->RequestDate))? $model->RequestDate : "0000-00-00";
			 
			   if($this_date_time != "0000-00-00"){
				   $this_selected_year = date("Y", strtotime($this_date_time));
				   $this_selected_month = date("m", strtotime($this_date_time));
				   $this_selected_day = date("d", strtotime($this_date_time));
			   }
			   
			   include ("date_selector.php");
			   
		 ?>     
				 <span> *</span>                     
		 <td bgcolor="#efefef">ครั้งที่: </td>
	     <td><input name="RequestNo" type="text" id="RequestNo" value="" /><span> *</span></td>
	     <td bgcolor="#efefef">หนังสือเลขที่: </td>
	     <td><input name="GovDocumentNo" type="text" id="GovDocumentNo" value="" /><span> *</span></td>
      </tr>
                	
      <tr>
	     <td bgcolor="#efefef">ผู้ดำเนินการ:</td>
	     <td colspan="3">
	     <?php echo $sess_userfullname;?>
		 </td>
	  </tr>
            	
      <tr>
         <td valign="top" bgcolor="#efefef">รายละเอียด:</td>
         <td colspan="3">
      		<textarea name="DocumentDetail" cols="50" rows="5" id="DocumentDetail" ><?php echo writeHtml($model->DocumentDetail)?></textarea>
         </td>
      </tr>                     
      <tr>
	     <td bgcolor="#efefef">แนบเอกสาร:</td>
         <td colspan="3">
            <div style="padding:5px 0px;">
                <font style="font-size: 11px;">(ไฟล์ jpg, gif หรือ pdf เท่านั้น)</font>
            </div>
	            <div class="input-file-container single-file">	                       			
	                <input  name="Collection_docfile[]" multiple="multiple" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.gif" />	                       				                       			
	        </div> 
	     </td>         
      </tr>
      <tr>
         <td colspan="4">
            
             <input type="submit" value="สร้างจดหมาย" onclick="return saveCollectionDoc()"/>
             
          </td>
       </tr>
 </table>
<?php }?><!-- \\ ข้อ 3. -->
						
					</form> 
				</td>
			 </tr>
     	<td align="right" colspan="2">
         	 <?php include "bottom_menu.php";?>
      	</td>
     </tr>     

</div><!--end page cell-->
</td>
</tr>
</table>
<?php include 'global.js.php';?>
<script id='uploadFileTemplate' type='text/x-kendo-template'>
    <span class='k-progress'></span>
    <div class='file-wrapper'>
           <button type='button' class='k-upload-action'></button>   
           <span class='file-name'>#=name#</span>
           <a href='##' target='_blank' class='file-link' >#=name#</a>
    </div>
</script>
<script type="text/javascript">	   
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

	function searchOrgList(){
		var year = '<?php echo $_GET["year"]?>';
		
		if(year != ''){
			var options = $('#ddl_year').find('option');
			var value;
			var isYearSearched = false;
			$.each(options, function(){
				value = $(this).val();
				
				if((value == year) && (!this.selected)){
					$(this).attr('selected', 'selected');		
					isYearSearched = true;			
				}
			});
	
			if(isYearSearched){
				$("[name='mini_search'").click();
			}	
		}		
	}
	function changePagination(){
		$("#HiddTaskName").val("paginator");
		$("form").submit();		
	}

	function setTask(taskName){
    	$("#HiddTaskName").val(taskName);
    	return true;	
	}
	function validateAddCollectionDoc(){
		var isValid = true;
		var isSentAll = document.getElementById('send_to_all').checked ;
		var isSelectedCompany  = getCIDSelected();
		var day = $("#RequestDate_day").val();
		var month = $("#RequestDate_month").val();
		var year = $("#RequestDate_year").val();
		var gov = $("#GovDocumentNo").val();
		gov = $.trim(gov);
		var requestno =$.trim( $("#RequestNo").val());
					
		if(((isSelectedCompany == "") ||(isSelectedCompany == "_")) && (isSentAll == false)){
			isValid = false;
			alert("กรุณาเลือก: สถานประกอบการที่ต้องการแจ้งทวงถาม");
		}else if(day == "00"){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: วัน");
			$("#DocumentDate_day").focus();
		}else if(month == "00"){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: เดือน");
			$("#DocumentDate_month").focus();
		}else if(year == "0000"){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ปี");
			$("#DocumentDate_year").focus();
		}else if(requestno == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: ครั้งที่");
			$("#RequestNo").focus();
		}else if(gov == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: หนังสือเลขที่");
			$("#GovDocumentNo").focus();
		}

		
		return isValid;
	}
	function saveCollectionDoc(){
		var isValid =  validateAddCollectionDoc();
		if(isValid){
			setTask('save');
		}
		return isValid;
	}


	function getCIDSelected(){
		
		var ids = [];
		$(".chk-collection").each(function(){
			if(this.checked){
				ids.push($(this).val());
			}
			
		});	
		return ids.join("_");
	}
	
</script>		
</body>
</html>