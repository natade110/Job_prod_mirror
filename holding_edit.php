<?php
include_once "db_connect.php";
include_once "session_handler.php";
require_once 'c2x_include.php';
require_once 'scrp_sequestration.php';

$canView = hasViewRoleSequestration($_GET["id"], $SEQUESTRATION_DOCUMENT_TYPE->อายัดทรัพย์สิน);
$canSave = hasCreateRoleSequestration($_GET["id"], $SEQUESTRATION_DOCUMENT_TYPE->อายัดทรัพย์สิน);
$canPrint = true;
if(!$canView){
	header ("location: index.php");
}

$interestRate = $DEBT_INTEREST_RATE;

$taskName = (isset($_POST["HiddTaskName"]))? $_POST["HiddTaskName"] : "";
$sid_get = (isset($_GET["id"]))? $_GET["id"] : 0;
$type_get = (isset($_GET["type"]))? $_GET["type"] : 0;
$errorMessage = "";
$dupedKey = false;
$dupedSID = 0;
$manage = new ManageSequestration();
$model = new Sequestration();
$isUpdated = false;

if(is_numeric($sid_get) && ($taskName == "save")){
	$lawStatus = $COMPANY_LAW_STATUS->อายัดทรัพย์สิน;
	$saveResult = $manage->saveSequestration($_POST, $_FILES, $sid_get, $sess_userfullname, $lawStatus, $SEQUESTRATION_TYPE, $the_company_word, $hire_docfile_relate_path);
	
	if($saveResult->IsComplete == true){
		$isUpdated = true;		
	}else if($saveResult->DupedKey == true){
		$dupedKey = $saveResult->DupedKey;
	}else{
		$errorMessage = $saveResult->Message;
	}
	
	$model = $saveResult->Data;
}else if($taskName == "delete"){
	$delResult = $manage->deleteSequestration($sid_get, $_POST);
	
	
	if($delResult->IsComplete){
		header("location: litigation_list.php?deleted=deleted");
	}else{
		$model = $delResult->Data;
		$errorMessage = $delResult->Message;
	}
	
}else if(is_numeric($sid_get)){
	$result = $manage->getSequestration($sid_get, $SEQUESTRATION_TYPE);
	if($result->IsComplete){
		$model = $result->Data;
	}else{
		$errorMessage = $result->Message;
	}
}else{
	$errorMessage = "รหัสไม่ถูกต้อง";
	
}

if(is_null($model->SID)){
	$canPrint = false;
	$canSave = false;	
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
                    <h2 class="default_h1" style="margin:0; padding:0;"  >การแจ้งอายัด หนังสือเลขที่: <?php echo $model->GovDocumentNo?></h2> 
                                        
                    <div style="padding:5px 0 10px 2px"><a href="litigation_list.php">การดำเนินคดีตามกฎหมายทั้งหมด</a> > หนังสือเลขที่: <?php echo $model->GovDocumentNo;?></div>
                    
                    <form method="post" action="holding_edit.php?id=<?php echo $sid_get ?>"  enctype="multipart/form-data" >
                     
                    <?php if($_GET["added"]=="added"){?>							
                        <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่่มข้อมูลอายัดเสร็จสิ้น</div>
                    <?php }else if($dupedKey == true){?>
                    	<div style="color:#990000; padding:5px 0 0 0; font-weight: bold;">* <a href="holding_edit.php?id=<?php echo $dupedSID;?>">หนังสือเลขที่ <?php echo $model->GovDocumentNo?></a> มีอยู่ในระบบแล้ว</div>
                    <?php }else if(!empty($errorMessage)){?>
                    	<div style="color:#FF3300; padding:5px 0 0 0; font-weight: bold;">* <?php echo nl2br($errorMessage);?></div>
                    <?php }else if($isUpdated == true){?>
                    	<div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลอายัดเสร็จสิ้น</div>
                    <?php }?>
                    	<div class="success-message success-message-star" message-for="sequestration-update" style="display: none"></div>		
					
						<input type="hidden" id="TotalAmount" name="TotalAmount" value="<?php echo $model->TotalAmount?>"/>
						<input type="hidden" id="HiddTaskName" name="HiddTaskName" value="<?php echo $taskName;?>" /> 						
						<input type="hidden" id="HiddCID" name="HiddCID" value="<?php echo $model->CID?>"/>
						<input type="hidden" id="HiddCSID" name="HiddCSID" value="<?php echo $model->CSID?>"/>
						<input type="hidden" id="HiddCompanyCode" name="HiddCompanyCode" value="<?php echo $model->CompanyCode?>"/>
						<input type="hidden" id="HiddBranchCode" name="HiddBranchCode" value="<?php echo $model->BranchCode?>"/>
						<input type="hidden" id="NoticeDate" name="NoticeDate" value="<?php echo $model->NoticeDate; ?>">
						<input type="hidden" id="Start2011InterestDate" name="Start2011InterestDate" value="<?php echo $model->Start2011InterestDate; ?>">  
						<input type="hidden" id="CreatedBy" name="CreatedBy" value="<?php echo $model->CreatedBy?>"/>
						<input type="hidden" id="CompanyName" name="CompanyName" value="<?php echo $model->CompanyName?>"/>
						<input type="hidden" id="CompanyTypeCode" name="CompanyTypeCode" value="<?php echo $model->CompanyTypeCode?>"/>
						<input type="hidden" id="SequestrationPayments" name="SequestrationPayments" />
						
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
			                    	 <div id="Org2011DebtBlock" style="display: none; margin-top: 7px;">
		                    	 	
		                    	 	 </div>
			                    </td>
			              	</tr>
			              	<tr>
			                    <td valign="top" bgcolor="#efefef">ประเภทการอายัด: </td>
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
			                    	 	<div id="SequesterTypeMoneyContainer" style="display: none; margin-left: 20px; margin-top:20px; margin-right: 10px;"><!-- ธนาคาร -->
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
		                    	 					<input id="BankBranch" name="BankBranch"  style="width:355px;" maxlength="255"/> *
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
				                    	 				<th>ธนาคาร</th>
				                    	 				<th>สาขา</th>
				                    	 				<th>		                    	 					
				                    	 				</th>		                    	 				
				                    	 			</tr>
		                    	 				</thead>
		                    	 				<tbody>		                    	 					
		                    	 				</tbody>
			                    	 		</table>	
			                    	 	</div><!-- //ธนาคาร -->
			                    	 	
			                    	 	<div id="SequesterTypePropertyContainer" style="display: none;  margin-left: 20px; margin-top: 20px; margin-right: 10px;"><!-- ที่ดิน -->
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
				                    	 				<th>		                    	 					
				                    	 				</th>		                    	 				
				                    	 			</tr>
			                    	 			</thead>
			                    	 		    <tbody></tbody>                   	 			
			                    	 			
			                    	 		</table>
			                    	 	</div><!-- //ที่ดิน -->
			                    	 	
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
	                    	  <td valign="top" bgcolor="#efefef">รายละเอียด: </td>
	                    	  <td colspan="3">
	                    	    <textarea name="SequestrationDetail" cols="50" rows="5" id="SequestrationDetail" ><?php echo writeHtml($model->SequestrationDetail)?></textarea>
	                            
	                    	  </td>
	                   	  	</tr>                     
	                       	<tr>
		                       	<td bgcolor="#efefef">แนบเอกสาร: </td>
		                       	<td colspan="3">
		                       		<div>
		                       			<?php 
		                       			$this_parent_table = "sequestrationattachment";
		                       			$this_id = $sid_get;
		                       			$file_type = $manage->SEQUESTRATION_FILE_TYPE;	
		                       			$this_cancreate = $canSave;
		                       			include "doc_file_links_for_sequestration.php";
		                       			?>
		                       		</div>
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
	                       			<div  align="right">
	                       				<?php if($canSave){?>
	                       				<input type="submit" value="อัพเดทข้อมูล" onclick="return c2xHolding.saveHolding()"/>
	                       				<?php }?>
	                       				<input type="button" id="ButtonCancelSequestration" value="ถอดอายัด" style="display: none" />
	                       				<?php if($canSave){?>
	                       				<input type="button" value="ลบข้อมูล" onclick="return c2xHolding.deleteHolding()" />
	                       				<?php }?>
	                       				
	                       				<?php if($canPrint){?>	                       				
	                       				<input type="button" value="พิมพ์" onclick="return printSequestration(<?php echo $sid_get;?>);"/>
	                       				<?php }?>
	                       			</div>
	                       			
	                       		</td>
	                       </tr>	                       	
		                </table>		                
					</form> 
					<div id="cancelSequestrationFrom" style="position: absolute; padding: 3px; display: none; top: 253.5px; left: 173px; opacity: 0; background-color: rgb(0, 102, 153);">
				        <table align="center" cellpadding="3" style="background-color:#fff; width:600px;border: 1px solid; border-collapse:collapse;">
							<tr style="background-color: #efefef; border: 1px solid grey; padding:3px;">
								<td><strong>ถอนอายัด  หนังสือเลขที่: <?php echo writeHtml($model->GovDocumentNo)?></strong></td>
							</tr>								
							<tr>
								<td align="center" >
									<iframe id="CancelsequestrationPopup" src="" style="width: 100%; height:350px; border: 0px; background-color: #fff;">		
									</iframe>				
								</td>
							</tr>	        
						</table>	
				   </div><!-- cancelSequestrationFrom -->
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
		c2xHolding.handleSelecDate();
		c2xHolding.config({
			InterestRate: <?php echo $interestRate;?>,
			TheCompanyWord: "<?php echo $the_company_word;?>",
			SequestrationType: <?php echo json_encode($SEQUESTRATION_TYPE)?>
		});
		c2xHolding.bindOrgDebtFrom({
			SequesterTypeMoney:<?php echo json_encode($model->SequestrationMoneyDetails) ?>,
			SequesterTypeProperty: <?php echo json_encode($model->SequestrationPropertyDetails) ?>,
			SequesterTypeCar: <?php echo json_encode($model->SequestrationCarDetails) ?>,
			SequesterTypeOther: <?php echo json_encode($model->SequestrationOtherDetails) ?>,
			SequesterPayments:<?php echo json_encode($model->SequestrationPayments) ?>
		});

		c2x.handleDdlCascadeProvince({
			ProvinceID: "DdlProvince",
			DistrictID: "DdlDistrict",
			SubDistrictID: "DdlSubDistrict"
		});

		var sid = <?php echo json_encode($model->SID)?>;
		var csid = <?php echo json_encode($model->CSID)?>;
		var canSave = <?php echo json_encode($canSave)?>;
		if(csid != null){
			$("#ButtonCancelSequestration").show();
			$("#ButtonCancelSequestration").val("ข้อมูลการถอนอายัด");
			$("#ButtonCancelSequestration").click(function(){
				openCancelSequestration(sid, csid);
			});		
		}else if(canSave){		
			$("#ButtonCancelSequestration").show();
			$("#ButtonCancelSequestration").val("ถอนอายัด");
			$("#ButtonCancelSequestration").click(function(){
				openCancelSequestration(sid);
			});	
		}

		var type = <?php echo json_encode($type_get)?>;
		if(type == 2){
			var btn = $("#ButtonCancelSequestration").get(0);
			if(typeof(btn) != 'undefined'){
				$("#ButtonCancelSequestration").click();
			}
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

	});								   

	function printSequestration(id){
		var url = "generate_pdf_sequestration.php?id=" + id;
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

	function displayDeleteCancelSequestrationMessage(sid){
		var sID = c2x.clone(sid);
		var messageBlock = $('div[class~="success-message"][message-for="sequestration-update"]');
    	$(messageBlock).text("การลบข้อมูลการถอนอายัดเสร็จสิ้น");
    	$(messageBlock).show();

    	var btnText = "ถอนอายัด";
    	$("#HiddCSID").val("");
    	$("#ButtonCancelSequestration").val(btnText);
		$("#ButtonCancelSequestration").click(function(){
			openCancelSequestration(sID);
		});		
		
		fadeOutMyPopup('cancelSequestrationFrom');
	}
	
	function openCancelSequestration(sid, csid){
		clearMessage();
		var sID = c2x.clone(sid);
		var csID = c2x.clone(csid);
		var cid = <?php echo json_encode($model->CID)?>;
		var container = $("#CancelsequestrationPopup");		
		var url = "cancelsequestration_popup.php?sid=" + sID + "&cid="+cid;
		url = (typeof(csid) != "undefined")? (url + "&csid=" + csID) : url;	
		
		$(container).attr("src", url);

		fireMyPopup('cancelSequestrationFrom',400,350);	
		
	}
	function updateSequestrationToUnlock(sid, csid){
		var sID = c2x.clone(sid);
		var csID = c2x.clone(csid);
		
		var btnText = "ข้อมูลการถอนอายัด";
		$("#HiddCSID").val(csID);
		$("#ButtonCancelSequestration").val(btnText);
		$("#ButtonCancelSequestration").click(function(){
			openCancelSequestration(sID, csID);
		});		
	}
	 function clearMessage(){
    	var updateBlock = $('div[message-for="sequestration-update"]').get(0);
    	
    	$(updateBlock).hide();

    }
</script>		
</body>
</html>