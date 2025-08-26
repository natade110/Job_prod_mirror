<?php
include_once "db_connect.php";
include_once "session_handler.php";
require_once 'c2x_include.php';
require_once 'scrp_notice.php';

$canView = hasViewRoleSequestration($_GET["id"], $SEQUESTRATION_DOCUMENT_TYPE->แจ้งโนติส);
$canSave = hasCreateRoleSequestration($_GET["id"], $SEQUESTRATION_DOCUMENT_TYPE->แจ้งโนติส);
$canPrint = true;
if(!$canView){
	header ("location: index.php");
}

$interestRate = $DEBT_INTEREST_RATE;

$taskName = (isset($_POST["HiddTaskName"]))? $_POST["HiddTaskName"] : "";
$id_get = (isset($_GET["id"]))? $_GET["id"] : 0;
$errorMessage = "";
$dupedKey = false;
$dupedID = 0;
$manage = new ManageNotice();
$model = new NoticeDocument();
$isUpdated = false;
$lawStatus = $COMPANY_LAW_STATUS->แจ้งโนติส;
if(is_numeric($id_get) && ($taskName == "save")){
	
	$saveResult = $manage->saveNotice($_POST, $_FILES, $id_get, $sess_userfullname, $lawStatus, $the_company_word, $hire_docfile_relate_path);
	
	if($saveResult->IsComplete == true){
		$isUpdated = true;		
	}else if($saveResult->DupedKey == true){
		$dupedKey = $saveResult->DupedKey;
	}else{
		$errorMessage = $saveResult->Message;
	}
	
	$model = $saveResult->Data;
}else if($taskName == "delete"){
	$delResult = $manage->deleteNoticeDocument($id_get, $_POST);
	if($delResult->IsComplete){
		header("location: litigation_list.php?deleted=deleted");
	}else{
		$model = $delResult->Data;
		$errorMessage = $delResult->Message;
	}
	
}else if(is_numeric($id_get)){
	$result = $manage->getNoticeDocument($id_get);
	if($result->IsComplete){
		$model = $result->Data;
	}else{
		$errorMessage = $result->Message;
	}
}else{
	$errorMessage = "รหัสไม่ถูกต้อง";
	
}

if(is_null($model->NoticeID)){
	$canPrint = false;
	$canSave = false;
}



// End Creat Sql
?>
<?php include "header_html.php";?>
				<td valign="top" style="padding-left:5px;"> <!-- td top --> 
					<script type="text/javascript" src="./scripts/manage.notice.js"></script>
					<style type="text/css" >
						
					</style>
                    <h2 class="default_h1" style="margin:0; padding:0;"  >การแจ้งโนติส หนังสือเลขที่: <?php echo writeHtml($model->GovDocumentNo)?></h2> 
                                        
                    <div style="padding:5px 0 10px 2px"><a href="litigation_list.php?">การดำเนินคดีตามกฎหมายทั้งหมด</a> > หนังสือเลขที่: <?php writeHtml($model->GovDocumentNo);?></div>
                    
                    <form method="post" action="notice_edit.php?id=<?php echo $id_get ?>"  enctype="multipart/form-data" >
                     
                    <?php if($_GET["added"]=="added"){?>							
                        <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่่มข้อมูลโนติสเสร็จสิ้น</div>
                    <?php }else if($dupedKey == true){?>
                    	<div style="color:#990000; padding:5px 0 0 0; font-weight: bold;">* <a href="notice_edit.php?id=<?php echo $dupedID;?>">หนังสือเลขที่ <?php writeHtml($model->GovDocumentNo)?></a> มีอยู่ในระบบแล้ว</div>
                    <?php }else if(!empty($errorMessage)){?>
                    	<div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <?php echo nl2br($errorMessage);?></div>
                    <?php }else if($isUpdated == true){?>
                    	<div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลโนติสเสร็จสิ้น</div>
                    <?php }?>
                    				
					
						<input type="hidden" id="TotalAmount" name="TotalAmount" value="<?php echo $model->TotalAmount?>"/>
						<input type="hidden" id="HiddTaskName" name="HiddTaskName" value="<?php echo $taskName;?>" /> 						
						<input type="hidden" id="HiddCID" name="HiddCID" value="<?php echo $model->CID?>"/>
						<input type="hidden" id="HiddCompanyCode" name="HiddCompanyCode" value="<?php echo $model->CompanyCode?>"/>
						<input type="hidden" id="HiddBranchCode" name="HiddBranchCode" value="<?php echo $model->BranchCode?>"/>
						<input type="hidden" id="CreatedBy" name="CreatedBy" value="<?php echo $model->CreatedBy?>"/>
						<input type="hidden" id="CompanyName" name="CompanyName" value="<?php echo $model->CompanyName?>"/>
						<input type="hidden" id="CompanyTypeCode" name="CompanyTypeCode" value="<?php echo $model->CompanyTypeCode?>"/>
						<input type="hidden" id="NoticeDetails" name="NoticeDetails" />		
						
		                <table style=" padding:10px 0 0px 0;">
		                	<tr>	                		 
		                		 <td bgcolor="#efefef">วันที่: </td>
		                    	 <td>
		                           
		                            <?php
													   
									   $selector_name = "DocumentDate";
									   
									   $this_date_time = $model->DocumentDate;
									 
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
		                		<td><?php echo $model->CreatedBy?></td>
		                	</tr>		                	
		                	<tr>
		                		<td bgcolor="#efefef">ชื่อ<?php echo $the_company_word?>: </td>
		                		<td><?php $companyNameToUse = formatCompanyName($model->CompanyName,$model->CompanyTypeCode); echo $companyNameToUse; ?></td>
		                	</tr>
		                	<tr>
			                    <td valign="top" bgcolor="#efefef">จำนวนเงินต้นที่อายัด: </td>
			                    <td colspan="3">		                    	 
			                    	 <div id="OrgDebtBlock" style="display: none; margin-top: 0px;">
			                    	 	
			                    	 </div>
			                    </td>
			              	</tr>			              	
	                     
	                      	<tr>
		                		<td bgcolor="#efefef">ผู้รับเอกสาร:</td>
		                		<td colspan="3"><input id="Receiver" name="Receiver" maxlength="255" value="<?php echo $model->Receiver;?>" style="width:368px;"></td>
		                		
		                	</tr>
		                	<tr>
		                		<td bgcolor="#efefef">วันที่รับเอกสาร:</td>
		                		<td>
		                			<?php
													   
									   $selector_name = "ReceivedDate";
									   
									   $this_date_time = (is_null($model->ReceivedDate) ? null : $model->ReceivedDate);
									   error_log(var_export($this_date_time, true));
									   if($this_date_time != "0000-00-00"){
										   $this_selected_year = date("Y", strtotime($this_date_time));
										   $this_selected_month = date("m", strtotime($this_date_time));
										   $this_selected_day = date("d", strtotime($this_date_time));
									   }
									   
									   include ("date_selector.php");
									   
									?> 
		                		</td>
		                	</tr>
		                	<tr>
		                		<td bgcolor="#efefef">เลขที่ลงทะเบียน:</td>
		                		<td colspan="3"><input id="RequestNo" name="RequestNo" maxlength="25" value="<?php echo $model->RequestNo;?>" style="width:368px;"></td>
		                		
		                	</tr>
	                    	<tr>
	                    	  <td valign="top" bgcolor="#efefef">รายละเอียด: </td>
	                    	  <td colspan="3">
	                    	    <textarea name="NoticeDetail" cols="50" rows="5" id="NoticeDetail" ><?php writeHtml($model->NoticeDetail)?></textarea>
	                            
	                    	  </td>
	                   	  	</tr>                     
	                       	<tr>
		                       	<td bgcolor="#efefef">แนบเอกสาร: </td>
		                       	<td colspan="3">
		                       		<div>
		                       			<?php 
		                       			$this_parent_table = "noticeattachment";
		                       			$this_id = $id_get;
		                       			$file_type = $manage->NOTICE_FILE_TYPE;		
		                       			$this_cancreate = $canSave;                       			
		                       			include "doc_file_links_for_sequestration.php";
		                       			?>
		                       		</div>
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
	                       			<div  align="right">
	                       				<?php if($canSave){?>
	                       				<input type="submit" value="อัพเดทข้อมูล" onclick="return c2xNotice.saveNotice()"/>
	                       				<input type="button" value="ลบข้อมูล" onclick="return c2xNotice.deleteNotice()" />
	                       				<?php }?>
	                       				
	                       				<?php if($canPrint){?>	                       				
	                       				<input type="button" value="พิมพ์" onclick="return printNotice(<?php echo $id_get;?>);"/>
	                       				<?php }?>
	                       			</div>
	                       			
	                       		</td>
	                       </tr>
	                       	
		                </table>		                
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
<?php include_once 'global.js.php';?>
<script type="text/javascript">
	$(function(){
		c2xNotice.handleSelecDate();
		c2xNotice.config({
			InterestRate: <?php echo $interestRate;?>,
			TheCompanyWord: "<?php echo $the_company_word;?>",
		});
		c2xNotice.bindOrgDebtFrom({
			NoticeDetails:<?php echo json_encode($model->NoticeDetails) ?>
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

	function printNotice(id){
		var url = "generate_pdf_notice.php?id=" + id;
		window.open(url,'_blank');
		return false;
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