<?php
include_once "db_connect.php";
require_once 'c2x_include.php';
require_once 'scrp_proceeding.php';

$canView = hasViewRoleSequestration($_GET["id"]);
$canSave = hasCreateRoleSequestration($_GET["id"]);

if(!$canView){
	header ("location: index.php");
}

$interestRate = $DEBT_INTEREST_RATE;

$taskName = (isset($_POST["HiddTaskName"]))? $_POST["HiddTaskName"] : "";
$id_get = (isset($_GET["id"]))? $_GET["id"] : 0;
$errorMessage = "";
$dupedKey = false;
$dupedPID = 0;
$isUpdated = false;

$manage = new ManageProceeding();
$model = new Proceeding();
if($taskName == "save"){	
	$saveResult = $manage->saveProceeding($_POST, $_FILES, $id_get);
	$model = $saveResult->Data;
	if($saveResult->IsComplete == true){
		$isUpdated = true;		
	}else if($saveResult->DupedKey == true){
		$dupedKey = $saveResult->DupedKey;
		$dupedPID = $saveResult->DupedPID;
	}else{
		$errorMessage = $saveResult->Message;
	}
}else if($taskName == "delete"){
	$delResult = $manage->deleteProceeding($id_get, $_POST);
	
	if($delResult->IsComplete){
		header("location: litigation_list.php?deleted=deleted");
	}else{
		$model = $delResult->Data;
		$errorMessage = $delResult->Message;
	}
}else if(is_numeric($id_get)){
	$result = $manage->getProceeding($id_get);
	if($result->IsComplete){
		$model = $result->Data;
	}else{
		$errorMessage = $result->Message;
	}
}else{
	$errorMessage = "รหัสไม่ถูกต้อง";
	
}

if(is_null($model->PID)){	
	$canSave = false;
}


// End Creat Sql
?>
<?php include "header_html.php";?>
				<td valign="top" style="padding-left:5px;"> <!-- td top --> 				
					<script type="text/javascript" src="./scripts/manage.proceedingscreate.js"></script>
					<style type="text/css" >
						#DdlProvince{
							width:173px;
						}					
						
					</style>
                    <h2 class="default_h1" style="margin:0; padding:0;"  >การส่งพนักงานอัยการ/ยื่นขอรับเงินกรณีล้มละลาย</h2>                  
                    <div style="padding:5px 0 10px 2px"><a href="litigation_list.php?">การดำเนินคดีตามกฎหมายทั้งหมด</a> > หนังสือเลขที่: <?php writeHtml($model->GovDocumentNo);?></div>
					
					<form method="post" action="" id="search_form" action="proceedings_edit.php?id=<?php echo $id_get ?>" enctype="multipart/form-data" >
						<input type="hidden" id="TotalAmount" name="TotalAmount" value="<?php echo $_POST["TotalAmount"]?>"/>
						<input type="hidden" id="HiddTaskName" name="HiddTaskName" value="<?php echo $taskName;?>">
						<input type="hidden" id="ProceedingPayments" name="ProceedingPayments" />
						<input type="hidden" id="HiddCID" name="HiddCID" value="<?php echo $model->CID?>"/>	
						<input type="hidden" id="HiddBranchCode" name="HiddBranchCode" value="<?php echo $model->BranchCode?>"/>
						<input type="hidden" id="HiddCompanyCode" name="HiddCompanyCode" value="<?php echo $model->CompanyCode?>"/>	
						<input type="hidden" id="CalDate" name="CalDate" value="<?php echo (isset($model->CalDate)? $model->CalDate->format("Y-m-d") :'' )?>">
						<input type="hidden" id="CompanyName" name="CompanyName" value="<?php echo $model->CompanyName?>"/>
						<input type="hidden" id="CompanyTypeCode" name="CompanyTypeCode" value="<?php echo $model->CompanyTypeCode?>"/>
						<input type="hidden" id="NoticeReceivedDate" name="NoticeReceivedDate" value="<?php echo $model->NoticeReceivedDate; ?>">
						<input type="hidden" id="Start2011InterestDate" name="Start2011InterestDate" value="<?php echo $model->Start2011InterestDate; ?>">
					<?php if($_GET["added"]=="added"){?>							
                        <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่่มข้อมูลการส่งพนักงานอัยการ/ยื่นขอรับเงินกรณีล้มละลายเสร็จสิ้น</div>
					<?php 
						}else if($dupedKey == true){
					?>	
                        <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <a href="proceedings_edit.php?id=<?php echo $dupedPID?>">หนังสือเลขที่ <?php writeHtml($model->GovDocumentNo)?></a> มีอยู่ในระบบแล้ว กรุณาใส่หนังสือเลขที่ใหม่</div>
                         
                    <?php
						}else if(!empty($errorMessage)){?>
						 <div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <?php echo nl2br($errorMessage);?></div>
				    <?php }else if($isUpdated == true){?>
                    	<div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลการส่งพนักงานอัยการ/ยื่นขอรับเงินกรณีล้มละลายเสร็จสิ้น</div>
                    <?php }?>
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
	                    	<td><input name="GovDocumentNo" type="text" id="GovDocumentNo" value="<?php writeHtml($model->GovDocumentNo) ?>" maxlength="25"/> *</td>
	                	</tr>
	                	
	                	<tr>
	                	
	                		<td bgcolor="#efefef">กระบวนการ: </td>
	                    	<td>	   
	                    		<input type="hidden" id="CurrentProceedingType" name="CurrentProceedingType" value="<?php echo $model->CurrentPType; ?>"> 
	                    		<input type="hidden" id="ProceedingType" name="ProceedingType" value="<?php echo $model->PType; ?>">                  		
                                <?php
												   
                                     echo createDropDownListFromMapping("DdlProceedingType", getProceedingEditTypeMapping(), $model->PType, "-- select --")
													   
								?>  *
	                    	</td>
	                    	<td bgcolor="#efefef" id="ProDateLabel">วันที่ศาลสั่งฟ้อง: </td>
	                    	<td>
	                    		<?php
												   
								   $selector_name = "ProDate";
								   
								   $this_date_time = (!is_null($model->ProDate))? ((gettype($model->ProDate) == "string")? $model->ProDate : $model->ProDate->format("Y-m-d")) : "0000-00-00";
								 
								   if($this_date_time != "0000-00-00"){
									   $this_selected_year = date("Y", strtotime($this_date_time));
									   $this_selected_month = date("m", strtotime($this_date_time));
									   $this_selected_day = date("d", strtotime($this_date_time));
								   }
								   
								   include ("date_selector.php");
								   
								   ?>     
								   <span> *</span>  
	                    	</td>	                		
	                	</tr>
	                	
	                	<tr id="ChargeDetailBlock" style="display: none;">
	                		<td bgcolor="#efefef" >กำหนดชำระภายใน: </td>
	                		<td><input id="DueDay" name="DueDay" value="<?php echo $model->DueDay?>" onblur="c2xProceeds.checkDueDayChange()"/> * วัน</td>
	                		
	                		<td bgcolor="#efefef" >ค่าใช้จ่ายอื่นๆ: </td>
	                		<td><input id="OtherExpense" name="OtherExpense" value="<?php echo $model->OtherExpense?>" onblur="c2xProceeds.checkOtherExpenseChange()"/></td>
	                	</tr>
	                	<tr id="FeeCourtBlock" style="display: none;">
	                		<td bgcolor="#efefef" >ค่าฤชาธรรมเนียม: </td>
	                		<td>
	                			<span id="FeeCourtLabel"><?php if($model->FeeCourt != null){echo number_format($model->FeeCourt, 2, ".", ",");} else{ echo "0.00";}?></span>
	                			<input id="FeeCourt" name="FeeCourt" type="hidden" value="<?php echo $model->FeeCourt?>" /> บาท
	                		</td>
	                	</tr>
	                	
	                	<tr>
		                		<td bgcolor="#efefef">ชื่อ<?php echo $the_company_word?>: </td>
		                		<td><?php $companyNameToUse = formatCompanyName($model->CompanyName,$model->CompanyTypeCode); echo $companyNameToUse; ?></td>
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
                    	    <textarea name="Detail" cols="50" rows="5" id="Detail" ><?php writeHtml($model->Detail)?></textarea>
                            
                    	  </td>
                   	  	</tr>                     
                       	<tr>
	                       	<td bgcolor="#efefef">แนบเอกสาร:</td>
	                       	<td colspan="3">
	                       		<div>
	                       			<?php 
	                       			$this_parent_table = "proceedingsattachment";
	                       			$this_id = $id_get;
	                       			$file_type = $manage->PROCEEDINGS_FILE_TYPE;		
	                       			$this_cancreate = $canSave;
	                       			include "doc_file_links_for_sequestration.php";
	                       			?>
	                       		</div>
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
                       			<div align="right">
                       				<?php if($canSave){?>
	                       			<input type="submit" value="อัพเดทข้อมูล" onclick="return c2xProceeds.saveProceeds()"/>
	                       			<input type="button" value="ลบข้อมูล" onclick="return c2xProceeds.deleteProceeds()" />
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
<?php include_once 'global.js.php';?>
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
		c2xProceeds.bindOrgDebtFrom({			
			ProceedingPayments:<?php echo json_encode($model->ProceedingPayments) ?>,
			IsEditPage:true		    
	    });
		
		
	});	

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