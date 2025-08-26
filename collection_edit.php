<?php
include_once "db_connect.php";
include_once "session_handler.php";
require_once 'c2x_include.php';
require_once 'scrp_add_collection.php';

$taskName = (isset($_POST["HiddTaskName"]))? $_POST["HiddTaskName"] : "";
$collectionid_get = (isset($_GET["id"]))? $_GET["id"] : 0;
$errorMessage = "";
$dupedKey = false;
$dupedCollectionID = 0;
$manage = new ManageCollection();
$model = new Collection();
$isUpdated = false;

$canView = hasViewRoleCollection($collectionid_get);
//$canSave = hasCreateRoleCollection($collectionid_get);
$canSave = hasUpdateRoleCollection($collectionid_get);
$canPrint = $canSave;

if(!$canView){
	header ("location: index.php");
}

if(is_numeric($collectionid_get) && ($taskName == "save")){
	error_log("save");
	$saveResult = $manage->saveCollection($_POST, $_FILES, $collectionid_get, $sess_username, $sess_userfullname, $the_company_word, $hire_docfile_relate_path);	
	$model = $saveResult->Data;
	
	if($saveResult->IsComplete == true){		
		$isUpdated = true;	
	}else if($saveResult->DupedKey == true){
		$dupedKey = $saveResult->DupedKey;
	}else{
		$errorMessage = $saveResult->Message;
	}	
	
	$model = $saveResult->Data;
}else if($taskName == "delete"){
	$delResult = $manage->deleteCollection($collectionid_get, $_POST);
	if($delResult->IsComplete){
		header("location: collection_list.php");
	}else{
		$model = $delResult->Data;
		$errorMessage = $delResult->Message;
	}
}else if($taskName == "deletecompany"){
	$delResult = $manage->deleteCollectionCompany($collectionid_get,  $_POST);
	if($delResult->IsComplete){
		
	}else{
		$errorMessage = $delResult->Message;
	}
	$model = $delResult->Data;
		
}else if(is_numeric($collectionid_get)){
	$result = $manage->getCollection($collectionid_get);
	if($result->IsComplete){
		$model = $result->Data;
	}else{
		$errorMessage = $result->Message;
	}
}else{
	$errorMessage = "รหัสไม่ถูกต้อง";
	
}



if(!is_null($model->CollectionID)){
// 	$canPrint = ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี) ||($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ);
// 	$canSave = ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่งานคดี) ||($sess_accesslevel == $USER_ACCESS_LEVEL->ผู้ดูแลระบบ);
	$condition_sql = "c.Collectionid = '".$model->CollectionID."'";
}
?>

<?php include "header_html.php";?>
<td valign="top" style="padding-left:5px;"> <!-- td top --> 
	<h2 class="default_h1" style="margin:0; padding:0 0 0px 0;">
		จดหมายทวงถาม ครั้งที่: <font color="#006699"><?php echo $model->RequestNo;?></font> หนังสือเลขที่: <font color="#006699"><?php echo $model->GovDocumentNo;?></font>
	</h2>
	
	<div style="padding:5px 0 10px 2px">
		<a href="collection_list.php">จดหมายทวงถามทั้งหมด</a> > หนังสือเลขที่:  <?php echo $model->GovDocumentNo?>
	</div>
					
	<form method="post" action="collection_edit.php?id=<?php echo $collectionid_get ?>" enctype="multipart/form-data">
        <?php if($_GET["added"]=="added"){?>							
           <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่่มข้อมูลจดหมายทวงถามเสร็จสิ้น</div>
        <?php }else if($dupedKey == true){?>
           <div style="color:#990000; padding:5px 0 0 0; font-weight: bold;">* <a href="collection_edit.php?sid=<?php echo $dupedCollectionID;?>">หนังสือเลขที่ <?php echo $model->GovDocumentNo?></a> มีอยู่ในระบบแล้ว</div>
        <?php }else if(!empty($errorMessage)){?>
           <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <?php echo nl2br($errorMessage);?></div>
        <?php }else if($isUpdated == true){?>
           <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลจดหมายทวงถามเสร็จสิ้น</div>
        <?php }?>	
        
		<input name="CollectionID" type="hidden" value="<?php echo ($output_values["CollectionID"]);?>"/>	
		<input name="Year" type="hidden" id="Year" value="<?php echo $model->Year?>">		
		<input name="CreatedBy" type="hidden" id="CreatedBy" value="<?php echo $model->CreatedBy?>">		
		<input type="hidden" id="HiddTaskName" name="HiddTaskName" value="<?php echo $taskName;?>"> 
		<input type="hidden" id="HiddCompanyCode" name="HiddCompanyCode" value="<?php echo $model->CompanyCode?>"/>
		
		<table style="padding:10px 0 0px 0;" >
			<tr>
            	<td bgcolor="#efefef"> ประจำปี: </td>
                <td colspan="5"><?php echo formatYear($model->Year)?></td>
            </tr>
            <tr>
                <td bgcolor="#efefef"> วันที่: </td>
                <td>
		           <?php
						$selector_name = "RequestDate";
							$this_date_time = $model->RequestDate;
								if($this_date_time != "0000-00-00"){
									$this_selected_year = date("Y", strtotime($this_date_time));
									$this_selected_month = date("m", strtotime($this_date_time));
									$this_selected_day = date("d", strtotime($this_date_time));
								}
						include ("date_selector.php");
					?>     
					 <span> *</span>   
				</td>
				<td bgcolor="#efefef"> ครั้งที่: </td>
				<td>
					<input name="RequestNo" type="text" id="RequestNo" value="<?php echo writeHtml($model->RequestNo)?>" /> *
				</td>
				<td bgcolor="#efefef"> หนังสือเลขที่: </td>
				<td>
					<input name="GovDocumentNo" type="text" id="GovDocumentNo" value="<?php echo writeHtml($model->GovDocumentNo)?>" /> *
				</td>
            </tr>
            <tr>
	           	<td bgcolor="#efefef">ผู้ดำเนินการ:</td>
	            <td colspan="5">
	               	<?php echo $model->CreatedBy?>
	            </td>
            </tr>
            <tr>
                <td bgcolor="#efefef" valign="top"> รายละเอียด:</td>
                <td colspan="5">
                    <textarea name="DocumentDetail" cols="50" rows="5" id="DocumentDetail"><?php echo writeHtml($model->DocumentDetail)?></textarea>
                </td>
            </tr>
           <tr>
		         <td bgcolor="#efefef">แนบเอกสาร: </td>
		         <td colspan="3">
		         	<div>
		             <?php 
		                  $this_parent_table = "collectionattachment";
		                  $this_id = $collectionid_get;
		                  $file_type = "Collection_docfile";	
		                  $this_cancreate = $canSave;	                       			
		                  include "doc_file_links_for_sequestration.php";
		              ?>
		           	</div>
		           	<div style="padding:5px 0px;">
	                   <font style="font-size: 11px;">(ไฟล์ jpg, gif หรือ pdf เท่านั้น)</font>
	               	</div>
		           	<div class="input-file-container single-file">	                       			
		               <input  name="Collection_docfile[]" multiple="multiple" type="file" accept=".pdf,.jpg,.jpeg,.gif" />	                       				                       			
		           	</div> 
		          </td>
	         </tr>	               
             <tr>
                <td colspan="4">
                   <div  align="right">
                       <?php if($canSave){?>
                   <input type="submit" value="อัพเดทข้อมูล" onclick="return saveCollectionDoc()"/>
                   <input type="button" value="ลบข้อมูล" onclick="return deleteCollection()" />
                       <?php }?>
                   </div>
                 </td>
              </tr>
		</table>
		
		<!-- management page -->
		<?php
			 $the_sql = "select count(com.CID) from collectioncompany c 
						inner join company com on c.CID = com.CID
						where $condition_sql";
			 $record_count_all = getFirstItem($the_sql);
			 	
			//pagination stuffs
			$per_page = 20;
			$num_page = ceil($record_count_all/$per_page);
					
			$cur_page = 1;
			if(is_numeric($_POST["start_page"]) && $_POST["start_page"] <= $num_page && $_POST["start_page"] > 0){
				$cur_page = $_POST["start_page"];
			}
					
			$starting_index = 0;
			if($cur_page > 1){
				$starting_index = ($cur_page-1) * $per_page;						
			}
			?>
		<!-- end management page -->

		<h2 class="default_h1" style="margin:0; padding:0 0 10px 0;">สถานประกอบการในจดหมายทวงถาม</h2>		
		<table border="0" width="100%">
			<tr>
				<td align="left">
					<font color="#006699">แสดงข้อมูล <?php echo $starting_index+1;?>-<?php echo ($record_count_all < $starting_index+$per_page) ? $record_count_all : $starting_index+$per_page;?> จากทั้งหมด <?php echo $record_count_all; ?> รายการ</font>
				</td>
				<td align="right" valign="bottom">
	 				<div style="padding:5px 0 0px 0;" align="right">
	 					แสดงข้อมูล:
		 				<select name="start_page" onchange="this.form.submit()">
	                        <?php for($i = 1; $i <= $num_page; $i++){?>
	                            	<option value="<?php echo $i;?>" <?php if($_POST["start_page"]==$i){echo "selected='selected'";}?>>หน้าที่ <?php echo $i;?></option>
    						<?php }?> 
	                    </select>
	 				</div>
 				</td>
			</tr>
		</table>
		
		<div style="padding:10px 0 10px 0" >
			<a href="export_collection.php?id=<?php echo $this_id;?>">+ export ข้อมูลเป็น excel</a> |
			<?php if($canPrint){?>	                       				
                   <a href="javascript:void()" onclick="printPdf(<?php echo $this_id;?>)" >พิมพ์เอกสาร</a>
            <?php }?>			
		</div>	
		
		<table border="1" width="100%" cellspacing="0" cellpadding="5" style="border-collapse:collapse;">
			<tr bgcolor="#9C9A9C" align="center" >  
				<td >
					<input name="chk_all" id="chk_all" type="checkbox" value="1" onclick="checkOrUncheck();" />
				</td>
				<td><div align="center"><span class="column_header">ลำดับ</span></div></td>
				<td><div align="center"><span class="column_header">รหัส</span></div></td>
				<td><div align="center"><span class="column_header">ชื่อ</span></div></td>
				<td><div align="center"><span class="column_header">สถานะ</span></div></td>
				<?php if($sess_accesslevel != 5){?>
					<td><div align="center"><span class="column_header">ลบข้อมูล</span></div></td>
				<?php }?>
			</tr>
			<?php 
				$total_records = 0;
				$the_limit = "limit $starting_index, $per_page";
				
// 				$get_collecion_sql = "select c.* , l.CID,l.LawfulStatus as lawfulness_status ,com.CompanyCode,com.CompanyNameThai,com.CompanyTypeCode from collectioncompany c 
// 								inner join collectiondocument cd on cd.CollectionID = c.CollectionID 
// 								inner join lawfulness l on c.CID = c.CID  and cd.Year =  l.Year
// 								left join company com on c.CID = com.CID
// 								where $condition_sql
// 								order by com.CompanyNameThai asc $the_limit";
				
				$get_collecion_sql = "select c.* ,c.CCID, c.CID,l.LawfulStatus as lawfulness_status ,com.CompanyCode,com.CompanyNameThai,com.CompanyTypeCode
								from collectioncompany c 
								inner join company com on c.CID = com.CID
								inner join collectiondocument cd on cd.CollectionID = c.CollectionID
								inner join lawfulness l on c.CID = l.CID  and cd.Year =  l.Year	
								where $condition_sql
								order by com.CompanyNameThai asc $the_limit";

				$collection_result = mysql_query($get_collecion_sql);
				// generate rows
				while ($post_row = mysql_fetch_array($collection_result)) {
					$total_records++;
			?>
			
			<tr bgcolor="#ffffff" align="center" >
			<?php 
				$js_do_check .= "document.getElementById('chk_$total_records').checked = true;";
				$js_do_uncheck .= "document.getElementById('chk_$total_records').checked = false;";
			?>		

				<td>
					<input class="chk-collection" name="chk_<?php echo $total_records; ?>" id="chk_<?php echo $total_records; ?>" type="checkbox" value="<?php echo doCleanOutput($post_row["CID"]);?>" />
				</td>
				<td><?php echo $total_records;?></td>
				<td><?php echo doCleanOutput($post_row["CompanyCode"]);?></td>
				<td><?php echo doCleanOutput(formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]));?></td>
				<td>
					<div align="center"><?php echo getLawfulImage(($post_row["lawfulness_status"]));?></div>
				</td>
				<td>
					<div align="center">
					 <?php if($canSave){?>
						<img src="decors/cross_icon.gif" border="0"  onclick="return deleteCollectionCom(<?php echo ($post_row["CCID"]);?>)"/>
                     <?php }?>					
					</div>
				</td>
			</tr>
			<?php } // end generate rows?>
		</table>
		<input name="CCID" id ="CCID"  type="hidden" value=""/>
		
		<input name="total_records" id="total_records" type="hidden" value="<?php echo $total_records; ?>" />					
	</form> 
  </td><!-- End Content Block -->
 </tr>
 
<!-- footer section -->
	<tr>
		<td align="right" colspan="2">
	    	<?php include "bottom_menu.php";?>
		</td>
	</tr>             


</div><!--end page cell-->
</td>
</tr>
</table>
<?php include_once 'global.js.php';?>
<!-- section script -->
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
		var day = $("#RequestDate_day").val();
		var month = $("#RequestDate_month").val();
		var year = $("#RequestDate_year").val();
		var gov = $("#GovDocumentNo").val();
		gov = $.trim(gov);
		var requestno =$.trim( $("#RequestNo").val());
		
		if(day == "00"){
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
	function deleteCollection(){
		var isConfirm = confirm("ยืนยันการลบข้อมูล");
		if(isConfirm){
			setTask('delete');
			$("form").submit();				
		}
		return isConfirm;
	}

	function deleteCollectionCom(CCID){
		$('#CCID').val(CCID);
		var isConfirm = confirm("ยืนยันการลบข้อมูล");
		if(isConfirm){
			setTask('deletecompany');
			$("form").submit();				
		}
		return isConfirm;
	}

	function printPdf(id){
		var url = "generate_pdf_collection.php?id="+ id;
		var cids = getCIDSelected();
		if(cids != ""){
			url += "&cid=" + cids; 
			window.open(url,'_blank');
		}else{
			alert("กรุณาเลือกสถานประกอบการ");
		}
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
</body>
</html>