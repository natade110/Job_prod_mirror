<?php
include_once "db_connect.php";
include_once "session_handler.php";
require_once 'c2x_include.php';
require_once 'scrp_sequestration.php';


$csid_get = (isset($_GET["csid"]))? $_GET["csid"] : 0;
$sid_get = (isset($_GET["sid"]))? $_GET["sid"] : 0;
$cid_get = (isset($_GET["cid"]))? $_GET["cid"] : 0;
$task = (isset($_POST["HiddTask"]))? $_POST["HiddTask"] : "";
$csid = (!is_null($_POST["HiddCSID"]))? $_POST["HiddCSID"] : $csid_get;	
$sid = (!is_null($_POST["HiddSID"]))? $_POST["HiddSID"] : $sid_get;
$cid = (!is_null($_POST["HiddCID"]))? $_POST["HiddCID"] : $cid_get;	


	
$manage = new ManageSequestration();
$model = new CancelledSequestration();
$isDeleteSuccess = false;
$isCreateSuccess = false;
$isUpdateSuccess = false;
$dupedKey = false;
$dupedSID = 0; $dupedCSID = 0;
$errorMessage = "";
$requestDateForm = "";
if($task == "save"){	
	$saveResult = $manage->saveCancelledSequestration($_POST, $_FILES, $sid, $csid, $cid);
	$model = $saveResult->Data;
	$isCreateSuccess = ($csid == 0 && ($saveResult->IsComplete));
	$isUpdateSuccess = ($csid > 0 && ($saveResult->IsComplete));
	$dupedKey = $saveResult->DupedKey;
	$dupedCSID = $saveResult->DupedCSID;
	$dupedSID = $saveResult->DupedSID;
	$errorMessage = $saveResult->Message;
}else if($task == "delete"){
	$delResulst = $manage->deleteCancelledSequestration($sid, $csid,  $cid, $_POST);
	$model = $delResulst->Data;
	if($delResulst->IsComplete){
		$isDeleteSuccess = true;
	}else{
		$errorMessage = $delResulst->Message;
	}
	
}else{		
	if($csid > 0){
		$result = $manage->getCancelSequestration($csid);
		if($result->IsComplete){
			$model = $result->Data;
		}else{
			$errorMessage = $result->Message;
		}
	}
	$model->SID = $sid;
	$model->CSID = $csid;
	$model->CID = $cid;
}

$canView = hasViewRoleSequestration($model->CSID, $SEQUESTRATION_DOCUMENT_TYPE->ถอนอายัด);
$canEditable = hasCreateRoleSequestration($model->CSID, $SEQUESTRATION_DOCUMENT_TYPE->ถอนอายัด);
$canPrint = true;

$requestDateForm = ($model->RequestDate != null)? $model->RequestDate->format("Y-m-d") : "";



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ระบบรายงานผลการจ้างงานคนพิการ</title>
<LINK REL='StyleSheet' type='text/css' href='styles.css'>
<link rel="stylesheet" href="emx_nav_left.css" type="text/css">

<script class="jsbin" src="jquery-1.11.1.min.js"></script>
<script src="./jquery_validate/jquery.validate.js"></script>
<script type='text/javascript' src="jquery_ui/jquery-ui.js"></script>
<script type="text/javascript" src="./kendo/kendo.all.min.js"></script>
<script type="text/javascript" src="./kendo/kendo.culture.th-TH.min.js"></script>
<script type="text/javascript" src="./kendo/kendo.calendar.custom.js"></script>
<script type="text/javascript" src="./scripts/site.js"></script>
<script type="text/javascript">
	kendo.culture("th-TH");
</script>

<link rel='stylesheet' id='all-css'  href='jquery_ui/jquery-ui.css' type='text/css' media='all' />

<link rel="stylesheet" type="text/css" href="./jquery.datetimepicker.css"/ >
<script src="./build/jquery.datetimepicker.full.min.js"></script>
<link rel='stylesheet' href='css/kendo.custom.css' type='text/css' media='all' />
<link rel='stylesheet' href='css/font-awesome.min.css' type='text/css' media='all' />
<link rel='stylesheet' href='css/site.css' type='text/css' media='all' />
</head>
<style>
  
  #overlay { 
    display:none; 
    position:fixed; 
    background:#333333; 
  }
  #img-load { 
    position:fixed; 
  }
  
  html, body{
  	background: #fff;
  }
  
  .nep-datepicker{
  	width:100%;
  }
  
</style>
<body>
<div id="loader" style="display:none">
   <img id="ImageLoader" src="./decors/loading.gif" alt="" /> 
</div>
<?php if($isCreateSuccess){?>							
	<div class="alert-message" style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่่มข้อมูลถอนอายัดเสร็จสิ้น</div>
 <?php }else if($dupedKey == true){?>
    <div class="alert-message" style="color:#990000; padding:5px 0 0 0; font-weight: bold;">* <a href="javascript:void(0)" onclick="openCancelSequestrationDuped(<?php echo $dupedSID;?>, <?php echo $dupedCSID;?>)">หนังสือเลขที่ <?php echo $model->RequestNo?></a> มีอยู่ในระบบแล้ว</div>
<?php }else if(!empty($errorMessage)){?>
    <div class="alert-message" style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <?php echo nl2br($errorMessage);?></div>
<?php }else if($isUpdateSuccess == true){?>
    <div class="alert-message" style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลถอนอายัดเสร็จสิ้น</div>
<?php }?>
<form method="post" enctype="multipart/form-data"  style="background-color: #fff;" id="FormCancelSequestration" >
	<input type="hidden" name="HiddTask" id="HiddTask"/> 
	<input type="hidden" name="HiddSID" id="HiddSID" value="<?php echo $model->SID?>"/>
	<input type="hidden" name="HiddCSID" id="HiddCSID" value="<?php echo $model->CSID?>"/>
	<input type="hidden" name="HiddCID" id="HiddCID" value="<?php echo $model->CID?>"/>
	<table style="margin-left:auto; margin-right:auto; width:400px; padding:10px 0 0px 0;" class="table-form">
		<tr>
			<td colspan="2"><div id="cancelLetterFormResult" style="color:#006600; padding:5px 0 0 0; font-weight: bold; display: none">* บันทึกข้อมูลสำเร็จแล้ว</div></td>
		</tr>
	    <tr>
	    	<td>เลขที่หนังสือ: </td>
	  		<td>
				<input id="RequestNo" name="RequestNo" style="width: 98%" value="<?php echo writeHtml($model->RequestNo)?>"/>
	  		</td>
	  		<td>*</td>
		</tr>
	 	<tr>
		   <td>วันที่ถอนอายัด: </td>
		   <td>		   		
				<input id="RequestDate" name="RequestDate" class="nep-datepicker" value="<?php echo $requestDateForm;?>"/>
				<input id="HiddRequestDate" name="HiddRequestDate" type="hidden" value="<?php echo $requestDateForm;?>"/>
		   </td>
		   <td>*</td>
	 	</tr>
	 	<tr>
		   <td>รายละเอียดการถอนอายัด: </td>
		   <td>
				<textarea class="control" id="CancelledDetail" name="CancelledDetail" ><?php echo writeHtml($model->CancelledDetail)?></textarea>                      
		   </td>
		 </tr>
		 <tr>
            <td>แนบเอกสาร: </td>
            <td colspan="3">
                 <div>
                 <?php 
                 $this_parent_table = "cancelledsequestttach";
                 $this_id = $model->CSID;
                 $file_type = $manage->CANCELLED_FILE_TYPE;		 
                 $this_cancreate = $canEditable;
                 include "doc_file_links_for_sequestration.php";
                  ?>
                  </div>
                  <div style="padding:5px 0px;">
                       	<font style="font-size: 11px;">(ไฟล์ jpg, gif หรือ pdf เท่านั้น)</font>
                  </div>
	              <div class="input-file-container single-file">	                       			
                       	<input  name="Cancelledsequest_Docfile[]" multiple="multiple" type="file" accept=".pdf,.jpg,.jpeg,.gif" />	                       				                       			
                  </div> 
	         </td>
         </tr>
		 <tr>						                       
		   <td colspan="2">
				<div style="text-align: center">
					<?php if($canEditable){?>
					<input type="submit"  name="button" id="button" value="บันทึก" onclick="return saveCancelSequestration();" />
					<input id="BtnDeleteCancel" type="button" value="ลบข้อมูล" onclick="return deleteCancelSequestration();" style="display: none" /> 
					<?php }?>
					
					<input id="BtnPrintCancel" type="button" value="พิมพ์" onclick="return printCancel(<?php echo $model->CSID;?>);" style="display: none"/>		
								
					<input type="button" value="ปิดหน้าต่างนี้" onclick="closeCancelSequestration();  return false;">  
					  
				</div>						                       		               
		   </td>
		 </tr>						                      
	</table>
</form>
<?php include_once 'global.js.php';?>
<script type="text/javascript">

	$(function () {
		 var isDelete = <?php echo json_encode($isDeleteSuccess)?>;
		 var isCreate = <?php echo json_encode($isCreateSuccess)?>;
		 
		 var sid = <?php echo json_encode($model->SID)?>;
		 var csid = <?php echo json_encode($model->CSID)?>;
		 
		 if(isDelete){			 
			 window.parent.displayDeleteCancelSequestrationMessage(sid);
		 }else{
			 createDatePicker();
			 if(isCreate){				 
				 window.parent.updateSequestrationToUnlock(sid, csid);
			 }
		 }

		

		 if(csid > 0){
			 $("#BtnPrintCancel").show();
			 $("#BtnDeleteCancel").show();
		 }else{
			 $("#BtnPrintCancel").hide();
			 $("#BtnDeleteCancel").hide();
		 }
	});
	
	function closeCancelSequestration(){
		clearForm();
		window.parent.fadeOutMyPopup('cancelSequestrationFrom');
	}

	function deleteCancelSequestration(){
		var isConfirm = confirm("คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือว่าเป็นการสิ้นสุดจะไม่สามารถเรียกข้อมูลกลับมาได้");
		if(isConfirm){
			$("#HiddTask").val("delete");
			$("#FormCancelSequestration").submit();
		}
		return isConfirm;
	}	

	function createDatePicker(){
		var datepicker = $("#RequestDate");
		$.each(datepicker, function(){
			$(this).kendoDatePicker({               
	           format: "d MMM yyyy",
	           parseFormats: ["yyyy-MM-dd"],
	           culture: "th-TH",
	           footer: cale.footerTemplate, 
	           open: cale.onDatePickerOpen
	       });
		});
	}

	function validateCancelSequestration(){
		var isValid = true;
		var requestNo = $("#RequestNo").val();
		requestNo = $.trim(requestNo);
		
		var requestDatePicker = $("#RequestDate").data("kendoDatePicker");
    	var requestDate = requestDatePicker.value();       	
    	
		if(requestNo == ""){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: หนังสือเลขที่");
			$("#RequestNo").focus();
		}else if(requestDate == null){
			isValid = false;
			alert("กรุณาใส่ข้อมูล: วันที่ถอนอายัด");
			$("#RequestDate").focus();
		}

		return isValid;
	}

	function saveCancelSequestration(){
		var isValid = validateCancelSequestration();
		if(isValid){
			var requestDatePicker = $("#RequestDate").data("kendoDatePicker");
	    	var requestDate = requestDatePicker.value();   
	    	var requestDateFormat = (requestDate == null)? null : kendo.toString(requestDate, "yyyy-MM-dd");
			$("#HiddRequestDate").val(requestDateFormat);
			
			$("#HiddTask").val("save");
			$("#FormCancelSequestration").submit();
		}
		return false;
	}

	function openCancelSequestrationDuped(sid, csid){			
		window.parent.openCancelSequestration(sid, csid);
	}

	function clearForm(){
		$(".alert-message").remove();
		var requestDatePicker = $("#RequestDate").data("kendoDatePicker");
    	var requestDate = requestDatePicker.value(); 
    	requestDate = null;
		$("#RequestNo").val("");
		$("#CancelledDetail").val("");
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

	function printCancel(id){
		var url = "generate_pdf_cancelseqestration.php?id=" + id;
		window.open(url,'_blank');
		return false;
	}
</script>	
</body>
</html>