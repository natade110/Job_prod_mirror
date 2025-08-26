<?php
require_once 'db_connect.php';
require_once 'c2x_include.php';
require_once 'scrp_add_collection.php';
require_once 'scrp_sequestration.php';
require_once 'scrp_get_schedulecollectionhistory.php';
require_once 'scrp_notice.php';
require_once 'scrp_proceeding.php';
require_once 'ThaiFormat.php';

//สำหรับเช็คว่าใครสามารถแก้ไขข้อมูลได้



global $this_id;
global $this_year;
$canEditCollection = hasCreateRoleCollectionByCID($this_id);
$canViewCollection = hasViewRoleCollection();
$canEditSequestration = hasCreateRoleSequestrationByCID($this_id);
$canViewSequestration = (hasViewRoleSequestration() || ($sess_accesslevel == $USER_ACCESS_LEVEL->เจ้าหน้าที่_พก));

$this_companycode = $output_values["CompanyCode"];
$lawStatusIconMapping = getLawStatusIconMapping();
$lawStatusMapping = getLawStatusMapping();
$thaiFormat = new ThaiFormat();
$this_thaiyear = ($this_year + 543);

$PROCEEDING_TYPE_MAPPING = getProceedingEditTypeMapping();

$manageCollection = new ManageCollection();
$noticeManage = new ManageNotice();
$sequestration = new ManageSequestration();
$proceeds = new ManageProceeding();

?>
<script type="text/javascript" src="./scripts/manage.org.sequestration.js"></script>
<script type="text/javascript">
<!--

//-->
</script>
<div class="success-message success-message-star" message-for="sequestration-update" style="display: none"></div>
<div class="error-message error-message-star" message-for="sequestration-error"  style="display: none"></div>

<?php if($canViewCollection || $canViewSequestration){
?>
<h2 class="default_h1">ประวัติการทวงถามและดำเนินคดีตามกฎหมาย</h2>
<?php 
}?>


<?php if($canViewCollection){
?>
<div id="Sequestration_LetterHistory" class="content-block"><!-- Sequestration_LetterHistory (ประวัติการส่งจดหมายแจ้งทวงถาม) -->
	<?php 
	
	$compCollectionResult = $manageCollection->getCollectionCompanyListByCID($this_id);
	$compCollectionList = ($compCollectionResult->IsComplete)? $compCollectionResult->Data : array();
	
	$canCreateCollectionResult = $manageCollection->canCreateCompanyCollection($this_id);
	$canCreateCompCollection = (($canCreateCollectionResult->IsComplete) && ($canCreateCollectionResult->Data != null));
	$createCollectionCss = ($canCreateCompCollection)? "show-line" : "hide-block";
	?>
	<div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการส่งจดหมายแจ้งทวงถาม</div> 
	
	<?php if($compCollectionResult->IsComplete == false){?>
	<div class="error-message">* <?php echo nl2br($compCollectionResult->Message);?></div>
	<?php }?>
	<?php if($canCreateCollectionResult->IsComplete == false){?>
	<div class="error-message">* <?php echo nl2br($canCreateCollectionResult->Message);?></div>
	<?php }?>
	
	
    <a id="LinkCreateCollection" href="collection_create.php?search_id=<?php echo $this_id;?>&for_year=<?php echo $canCreateCollectionResult->Data?>" class="add-link <?php echo $createCollectionCss?>">+ ส่งจดหมายแจ้งทวงถาม</a>                         
   
    
    <table width="100%" class="nep-grid" style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5">
		<tr>
			<th width="100">วันที่</th>
			<th width="40">ครั้งที่</th>
			<th width="100">หนังสือเลขที่</th>
			<th width="100">เลขที่ลงทะเบียน</th>
			<th width="100">วันที่รับเอกสาร</th>
			<th width="120">ผู้รับ</th>
			<th width="120">ผู้ดำเนินการ</th>
			<?php if($canEditCollection){?>
			<th width="65"></th>	
			<?php }?>
		</tr>
		<?php for ($i = 0; $i < count($compCollectionList); $i++){
			$collectionItem = $compCollectionList[$i];
			$ccid = $collectionItem->CCID;
			$receiverDate = isset($collectionItem->ReceiverDate)? new DateTime($collectionItem->ReceiverDate) : null;
			$receiverDateFormat = is_null($receiverDate)? "" :  $receiverDate->format("Y-m-d");
		?>
		<tr row-id="<?php echo $ccid?>" row-data="collectioncompany">			
			<td><?php echo $thaiFormat->date_format(new DateTime($collectionItem->RequestDate), "j M Y", true);?></td>
			<td><?php echo $collectionItem->RequestNo;?></td>
			<td>
				<a href="collection_edit.php?id=<?php echo $collectionItem->CollectionID?>"><?php echo $collectionItem->GovDocumentNo;?></a>
			</td>
			<td><input id="collectionReceiverNo<?php echo $ccid?>" value="<?php writeHtml($collectionItem->ReceiverNo);?>" class="nep-input" maxlength="25"/></td>
			<td><input id="collectionDatepicker<?php echo $ccid?>" class="nep-datepicker" value="<?php echo $receiverDateFormat;?>"/></td>
			<td><input id="collectionReceiver<?php echo $ccid?>" value="<?php writeHtml($collectionItem->Receiver);?>" class="nep-input"  maxlength="255"/></td>			
			<td><?php echo $collectionItem->RequestBy;?></td>
			<?php if($canEditCollection){?>
			<td width="45">
				<a href="javascript:void()" onclick="return c2xOrgSeq.saveCollectionCompany(<?php echo $ccid?>);" class="icon icon-save" title="บันทึก" ></a>
				<a href="javascript:void()" onclick="return c2xOrgSeq.deleteCollectionCompany(<?php echo $ccid?>);" class="icon icon-del" title="ลบ" ></a>
			</td>	
			<?php }?>
		</tr>		
		<?php }?>
	</table>
</div><!-- Sequestration_LetterHistory (ประวัติการส่งจดหมายแจ้งทวงถาม) -->

<div id="Sequestration_EmailHistory" class="content-block"><!-- Sequestration_EmailHistory (ประวัติการเตือนทาง e-mail) -->
	<?php 
		$schduleHistoryDataAccess = new ScheduleCollectionHistoryDataAccess();
		$schduleHistoryResult = $schduleHistoryDataAccess->getScheduleCollectionHistoryByCid($this_id);	
		$schduleHistoryList = ($schduleHistoryResult->IsComplete)? $schduleHistoryResult->Data : array();	
	?>
	<div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการส่งอีเมลล์ทวงถาม</div> 
	<?php if($schduleHistoryResult->IsComplete == false){?>
	<div class="error-message">* <?php echo nl2br($schduleHistoryResult->Message);?></div>
	<?php }?>
	
	
	<table width="100%" class="nep-grid" style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5">
		<tr>
			<th width="80">ประจำปี</th>
			<th>วันที่ส่ง</th>
			<th>วันที่รับ</th>					
		</tr>
		<?php for ($i = 0; $i < count($schduleHistoryList); $i++){
			$schduleHistoryItem = $schduleHistoryList[$i];
			$receiverDateFormat = isset($schduleHistoryItem->ReceivedDate)? $thaiFormat->date_format(new DateTime($schduleHistoryItem->ReceivedDate), "j M Y", true) : "";
		?>
		<tr>
			<td class="text-center"><?php echo ($schduleHistoryItem->Year + 543);?></td>
			<td><?php echo $thaiFormat->date_format(new DateTime($schduleHistoryItem->SentDate), "j M Y", true);?></td>
			<td><?php echo $receiverDateFormat?></td>
		</tr>		
		<?php }?>
	</table>
		
</div><!-- Sequestration_EmailHistory (ประวัติการเตือนทาง e-mail) -->
<?php 
}//if($canViewCollection)?>


<?php if($canViewSequestration){
?>
<div id="Sequestration_NoticeHistory" class="content-block"><!-- Sequestration_NoticeHistory (ประวัติการแจ้ง Notice) -->
	<?php 
	
	$noticeResult = $noticeManage->getNoticeDocumentList($this_id);
	$noticeItems = ($noticeResult->IsComplete)? $noticeResult->Data : array();
	
	$noticeCanCreateResult = $noticeManage->canCreateCompanyNoticeDocument($this_id);
	$noticeCanCreate = ($noticeCanCreateResult->IsComplete && ($noticeCanCreateResult->Data != null));
	$createNoticeCss = ($noticeCanCreate)? "show-line" : "hide-block";
	?>
	<div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการแจ้งโนติส</div> 
	<?php if($noticeResult->IsComplete == false){?>
	<div class="error-message">* <?php echo nl2br($noticeResult->Message);?></div>
	<?php }?>
	
    <a id="LinkCreateNotice" href="notice_create.php?search_id=<?php echo $this_id;?>" class="add-link <?php echo $createNoticeCss?>">+ แจ้งโนติส</a>                         
	<table width="100%" class="nep-grid" style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5">
		<tr>
			<th width="100">วันที่</th>
			<th width="100">หนังสือเลขที่</th>
			<th width="120">ผู้ดำเนินการ</th>		
			<th width="120">ผู้รับเอกสาร</th>	
			<th width="100">วันที่รับเอกสาร</th>	
			<th width="100">เลขที่ลงทะเบียน</th>	
			<th width="120">จำนวนเงิน</th>			
			<?php if($canEditSequestration){?>
			<th width="65"></th>	
			<?php }?>
		</tr>
		<?php for ($i = 0; $i < count($noticeItems); $i++){
			$noticeItem = $noticeItems[$i];
			$noticeID = $noticeItem->NoticeID;
			$receivedDate = isset($noticeItem->ReceivedDate)? new DateTime($noticeItem->ReceivedDate) : null;
			$receivedDateFormat = is_null($receivedDate)? "" : $receivedDate->format("Y-m-d");
		?>
		<tr row-id="<?php echo $noticeID?>" row-data="noticedocument">			
			<td><?php echo $thaiFormat->date_format(new DateTime($noticeItem->DocumentDate), "j M Y", true);?></td>
			<td>
				<a href="notice_edit.php?id=<?php echo $noticeID?>"><?php writeHtml($noticeItem->GovDocumentNo);?></a>
			</td>
			<td><?php echo $noticeItem->CreatedBy;?></td>
			<td><input id="noticeReceived<?php echo $noticeID;?>" value="<?php writeHtml($noticeItem->Receiver);?>" class="nep-input" maxlength="255"/></td>
			<td><input id="noticeDatepicker<?php echo $noticeID;?>" class="nep-datepicker" value="<?php echo $receivedDateFormat;?>"/></td>
			<td><input id="noticeRequestNo<?php echo $noticeID;?>" value="<?php writeHtml($noticeItem->RequestNo);?>" class="nep-input" maxlength="25"/></td>
			<td class="text-right"><?php echo number_format($noticeItem->TotalAmount, 2, ".", ",");?></td>
			<?php if($canEditSequestration){?>
			<td width="45">
				<a href="javascript:void()" onclick="return c2xOrgSeq.saveNoticeDocument(<?php echo $noticeID;?>);" class="icon icon-save" title="บันทึก" ></a>
				<a href="javascript:void()" onclick="return c2xOrgSeq.deleteNoticeDocument(<?php echo $noticeID;?>, <?php echo $this_id?>);" class="icon icon-del" title="ลบ" ></a>
			</td>	
			<?php }?>
		</tr>		
		<?php }?>
	</table>
</div><!-- Sequestration_NoticeHistory (ประวัติการแจ้ง Notice) -->

<div id="Sequestration_SequestrationHistory" class="content-block"><!-- Sequestration_SequestrationHistory (ประวัติการแจ้งอายัด) -->
	<div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการแจ้งอายัด</div> 
	<?php 
	
	
	$sequestrationResult = $sequestration->getSequestrationList($this_id);
	$sequestrationList = array();
	$sequestrationItem = new SequestrationList();
	$squestrationStatusCss =  $lawStatusIconMapping[3];
	$squestrationStatusText = $lawStatusMapping[3];
	$cancelSequestStatusCss = "fa fa-unlock";
	$cancelSequestStatusText = "ถอนอายัดแล้ว";
	
	if($sequestrationResult->IsComplete){
		$sequestrationList = $sequestrationResult->Data;			
	}
	
	$canCreateSequestResult = $sequestration->canCreateCompanySequestration($this_id);
	$canCreateSequest = ($canCreateSequestResult->IsComplete && ($canCreateSequestResult->Data != null));
	$createSequestCss = ($canCreateSequest)? "show-line" : "hide-block";
	?>
	
	
    <a id="LinkCreateSequest" href="holding_create.php?search_id=<?php echo $this_id;?>" class="add-link <?php echo $createSequestCss?>">+ แจ้งอายัด</a>                         
    
	<table width="100%" class="nep-grid" style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5">
		<tr>
			<th width="80">วันที่</th>
			<th width="120">หนังสือเลขที่</th>
			<th width="120">จำนวนเงิน</th>
			<th>ผู้ดำเนินงาน</th>	
			<th width="45">
				<?php echo ($canEditSequestration)? "" : "สถานะ"?>
			</th>
			
		</tr>
		<?php 
		$statusCss = "";
		$statusText = "";
		for($i = 0; $i < count($sequestrationList); $i++){ 			
			$sequestrationItem = $sequestrationList[$i];
			$sequestID = $sequestrationItem->SID;
			$cancelSequestID = $sequestrationItem->CSID;
			
			$documentDate = new DateTime($sequestrationItem->DocumentDate);
			if((!$canEditSequestration) && (is_null($cancelSequestID))){
				$statusCss = $squestrationStatusCss;
				$statusText = $squestrationStatusText;
			}else{
				$statusCss = $cancelSequestStatusCss;
				$statusText = $cancelSequestStatusText;
			}		
			
		
		?>
		<tr row-id="<?php echo $sequestID;?>" row-data="sequestration">
			<td><?php echo $thaiFormat->date_format($documentDate, "j M Y", true);?></td>
			<td><a href="holding_edit.php?id=<?php echo $sequestrationItem->SID?>" link-id="SequestrationLink<?php echo $sequestID?>"><?php writeHtml($sequestrationItem->GovDocumentNo);?></a></td>
			<td class="text-right"><?php echo number_format($sequestrationItem->TotalAmount, 2, ".", ",");?></td>
			<td><?php echo $sequestrationItem->CreatedBy;?></td>
			<td class="text-nowrap text-center">
				<?php if($canEditSequestration && (is_null($sequestrationItem->CSID))){?>
				<a href="javascript:void()" onclick="openCancelSequestration(<?php echo $sequestID?>); return false;" title="ถอนการอายัด" class="icon icon-locksequestration"  ></a>
	            <a href="javascript:void()" onClick="return c2xOrgSeq.deleteSequestration(<?php echo $sequestID?>, <?php echo $this_id?>);" title="ลบข้อมูล" class="icon icon-del" ></a>
				<?php }else if($canEditSequestration){?>
				<a href="javascript:void()" onclick="openCancelSequestration(<?php echo $sequestID;?>, <?php echo $sequestrationItem->CSID;?>); return false;" title="แก้ไขการถอนการอายัด" class="icon icon-unlocksequestration"  ></a>
				<a href="javascript:void()" onClick="return c2xOrgSeq.deleteSequestration(<?php echo $sequestID?>, <?php echo $this_id?>);" title="ลบข้อมูล" class="icon icon-del" ></a>
				<?php }else if(!$canEditSequestration && (is_null($sequestrationItem->CSID))){?>
					<a href="javascript:void()" title="<?php echo $squestrationStatusText?>" class="icon icon-locksequestration"  disabled="disabled" ></a>
				<?php }else{?>					
					<a href="javascript:void()" onclick="openCancelSequestration(<?php echo $sequestID;?>, <?php echo $sequestrationItem->CSID;?>); return false;"  title="<?php echo $cancelSequestStatusText?>" class="icon icon-unlocksequestration"></a>
				<?php }?>				
			</td>
		</tr>
		<?php }?>
	</table>
	<div id="cancelSequestrationFrom" style="position: absolute; padding: 3px; display: none; top: 253.5px; left: 173px; opacity: 0; background-color: rgb(0, 102, 153);">
        <table align="center" cellpadding="3" style="background-color:#fff; width:600px;border: 1px solid; border-collapse:collapse;">
			<tr style="background-color: #efefef; border: 1px solid grey; padding:3px;">
				<td><strong>ถอนอายัด  หนังสือเลขที่: <span id="SequestrationGovDocumentNo"></span></strong></td>
			</tr>								
			<tr>
				<td align="center" >
					<iframe id="CancelsequestrationPopup" src="" style="width: 100%; height:350px; border: 0px; background-color: #fff;">		
					</iframe>				
				</td>
			</tr>	        
		</table>	
     </div><!-- cancelSequestrationFrom -->
</div><!-- Sequestration_SequestrationHistory (ประวัติการแจ้งอายัด) -->

<div id="Sequestration_ProceedingsHistory" class="content-block"><!-- Sequestration_ProceedingsHistory (ประวัติการส่งอัยการเพื่อฟ้องคดี) -->
	<div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการส่งอัยการเพื่อฟ้องคดี</div> 
	<?php 
	
	$proceedsResult = $proceeds->getProceedingList($this_id);
	$proceedsList = array();
	$proceedsItem = new ProceedingList();
	
	if($proceedsResult->IsComplete){
		$proceedsList = $proceedsResult->Data;
	}
	
	$canCreateProceedsResult = $proceeds->canCreateCompanyProceeds($this_id);
	$canCreateProceeds = ($canCreateProceedsResult->IsComplete && ($canCreateProceedsResult->Data != null));
	$createProceedCss = ($canCreateProceeds)? "show-line" : "hide-block";
	?>
	
	
    <a id="LinkCreateProceeds" href="proceedings_create.php?search_id=<?php echo $this_id;?>" class="add-link <?php echo $createProceedCss?>">+ การส่งพนักงานอัยการ/ยื่นขอรับเงินกรณีล้มละลาย</a>                         
    
    <table width="100%" class="nep-grid" style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5">
		<tr>
			<th width="80">วันที่</th>
			<th width="120">หนังสือเลขที่</th>			
			<th width="80">กระบวนการ</th>
			<th width="80">วันที่ศาลสั่งฟ้อง</th>
			<th width="120">จำนวนเงิน</th>	
			<th width="45"></th>			
		</tr>
		<?php 
		$proceedsID = 0;		
		for($i = 0; $i < count($proceedsList); $i++){
			$proceedsItem = $proceedsList[$i];
			$proceedsID = $proceedsItem->PID;
			$requestDate = new DateTime($proceedsItem->RequestDate);
			$proDate = (is_null($proceedsItem->ProDate)? null :  new DateTime($proceedsItem->ProDate));
			$proDateFormat = (is_null($proDate)? "" : $thaiFormat->date_format($proDate, "j M Y", true));
			$otherExpense = (is_null($proceedsItem->OtherExpense)) ? 0 : $proceedsItem->OtherExpense;			
			$totalAmount = (is_null($proceedsItem->TotalAmount))? 0 : $proceedsItem->TotalAmount;	
			$proceedsTotal = round(($otherExpense + $totalAmount), 2);
			$proceedsTotalFormat = ($proceedsTotal > 0)? number_format($proceedsTotal, 2, ".", ",") : "";
			
			$viewProceedsAttachCss = ($proceedsItem->HasAttachment == 0)? "hide-block" : "";
		?>
		<tr row-id="<?php echo $proceedsID;?>" row-data="proceeds">
			<td><?php echo $thaiFormat->date_format($requestDate, "j M Y", true);?></td>
			<td><a href="proceedings_edit.php?id=<?php echo $proceedsID?>" link-id="ProceedingsLink<?php echo $proceedsID?>"><?php writeHtml($proceedsItem->GovDocumentNo)?></a></td>
			<td><?php echo $PROCEEDING_TYPE_MAPPING[$proceedsItem->PType]?></td>	
			<td><?php echo $proDateFormat;?></td>
			<td class="text-right"><?php echo $proceedsTotalFormat;?></td>			
			<td class="text-nowrap text-center">			
				<a href="javascript:void()" onclick="openProceedingsAttachment(<?php echo $proceedsID?>); return false;" 
					title="เอกสารแนบ" class="icon icon-view <?php echo $viewProceedsAttachCss?>"></a>
				<?php if($canEditSequestration){?>
	            <a href="javascript:void()" onClick="return c2xOrgSeq.deleteProceeds(<?php echo $proceedsID?>, <?php echo $this_id?>);" title="ลบข้อมูล" class="icon icon-del" ></a>
				<?php }?>				
			</td>
		</tr>
		<?php
		}
		?>
	</table>
	
	<div id="ProceedingsFrom" style="position: absolute; padding: 3px; display: none; top: 253.5px; left: 173px; opacity: 0; background-color: rgb(0, 102, 153);">
        <table align="center" cellpadding="3" style="background-color:#fff; width:600px;border: 1px solid; border-collapse:collapse;">
			<tr style="background-color: #efefef; border: 1px solid grey; padding:3px;">
				<td><strong>หนังสือเลขที่: <span id="ProceedingsRequestNo"></span></strong></td>
			</tr>								
			<tr>
				<td align="center">
					<div  id="ProceedingsAttachment" style="margin: 10px 20px"></div>
					<div style="text-align: center; margin-top: 5px; margin-bottom: 5px;">
						<input type="button" onclick="return closeProceedingsAttachment()" value="ปิดหน้าต่างนี้">	
					</div>				   
				</td>
			</tr>	        
		</table>	
     </div><!-- cancelSequestrationFrom -->
</div><!-- Sequestration_ProceedingsHistory (ประวัติการส่งอัยการเพื่อฟ้องคดี) -->

<?php //bank add 20230106 MA 2022 ข้อ 40 ?>

<div id="Sequestration_SequestrationHistory" class="content-block"> <!-- Sequestration_SequestrationHistory (ประวัติการแจ้งอายัด) -->
	<div style="font-weight: bold; padding:0 0 5px 0;">ประวัติการรับ/ส่งข้อมูล จากระบบการติดตามและดำเนินคดี</div>  
	<?php 
	
	
	$sequestrationResult = $sequestration->getSequestrationList($this_id);
	$sequestrationList = array();
	$sequestrationItem = new SequestrationList();
	$squestrationStatusCss =  $lawStatusIconMapping[3];
	$squestrationStatusText = $lawStatusMapping[3];
	$cancelSequestStatusCss = "fa fa-unlock";
	$cancelSequestStatusText = "ถอนอายัดแล้ว";
	
	if($sequestrationResult->IsComplete){
		$sequestrationList = $sequestrationResult->Data;			
	}
	
	$canCreateSequestResult = $sequestration->canCreateCompanySequestration($this_id);
	$canCreateSequest = ($canCreateSequestResult->IsComplete && ($canCreateSequestResult->Data != null));
	$createSequestCss = ($canCreateSequest)? "show-line" : "hide-block";
	?>
	
	
    <a id="LinkCreateSequest" href="holding_create.php?search_id=<?php echo $this_id;?>" class="add-link <?php echo $createSequestCss?>">+ แจ้งอายัด</a>                         
    
	
	<?php //bank add 20230106 MA 2022 ข้อ 40
		
	$sql =	"select b.meta_value 
			from lawfulness a , lawfulness_meta b
			where a.cid = '$this_id'
			and a.year = '$this_year'
			and a.lid = b.meta_lid 
			and b.meta_for = 'courted_by'";
	
		$lawful_result = mysql_query($sql);	
		  
		  while ($lawful_row = mysql_fetch_array($lawful_result)) { ?>
	<table width="100%" class="nep-grid" style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5">
		<tr>
			<th width="80">ผู้ที่ส่งเรื่อง</th>
			<th width="120">วันที่ส่งเรื่อง</th>
			<th width="120">หนังสือเลขที่</th>
			<th width="120">หมายเหตุ</th>
			<th width="45">
				
			</th>
			
		</tr>

		<tr>
			<td align="center" style="text-align: center">
			
			 <?php 
			 
			 $court_by = getFirstItem("select u.user_name
									from users u
									where u.user_id = '".$lawful_row["meta_value"]."'																									
									");
			 echo $court_by;?>
			</td>
			
			<td align="center" style="text-align: center">
			
			 <?php 
			 
			 $court_date = getFirstItem("select b.meta_value 
											from lawfulness a , lawfulness_meta b
											where a.cid = '$this_id'
											and a.year = '$this_year'
											and a.lid = b.meta_lid 
											and b.meta_for = 'courted_date'																									
									");
			 echo formatDateThai($court_date,1,5);?>
			</td>
			
			
			<td align="center" style="text-align: center">
			
			 <?php 
			 
			 $lead_book_no = getFirstItem("select b.meta_value 
											from lawfulness a , lawfulness_meta b
											where a.cid = '$this_id'
											and a.year = '$this_year'
											and a.lid = b.meta_lid 
											and b.meta_for = 'lead_book_no'																									
									");
			 echo $lead_book_no;?>
			</td>
		</tr>
		
	</table>
	<?php } ?>
	<?php //end bank add 20230106 MA 2022 ข้อ 40 ?>
	

</div>






<?php 
} //if($canViewSequestration)?>




<script type="text/javascript">

$(function () {
	 createDatePicker();
	 c2xOrgSeq.config({
		 ManageUrl: 'scrp_manage_org_sequestration.php',
		 SequestrationTitle: '<?php echo $squestrationStatusText;?>',
		 CancelledSequestrationTitle: '<?php echo $cancelSequestStatusText?>',
		 ProceedingsAttachments: <?php echo json_encode($proceeds->getAttachmentListByCID($this_id))?>,
		 HireDocfileRelatePath:<?php echo json_encode($hire_docfile_relate_path)?>,
		 CID: <?php echo $this_id?>
	 });

	 
});

function createDatePicker(){
	var datepicker = $(".nep-datepicker");
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

function displayDeleteCancelSequestrationMessage(sid){
	c2xOrgSeq.displayDeleteCancelSequestrationMessage(sid);
	fadeOutMyPopup('cancelSequestrationFrom');
}

function openCancelSequestration(sid, csid){
	var cid = <?php echo json_encode($this_id)?>;
	var sID = c2x.clone(sid);
	var csID = c2x.clone(csid);
	var container = $("#CancelsequestrationPopup");
	var govDocumentNo = $("a[link-id='SequestrationLink"+ sID +"']").text();
	$("#SequestrationGovDocumentNo").text(govDocumentNo);
	var url = "cancelsequestration_popup.php?sid=" + sid + "&cid="+cid;
	url = (typeof(csid) != "undefined")? (url + "&csid=" + csID) : url;	
	
	$(container).attr("src", url);

	fireMyPopup('cancelSequestrationFrom',400,350);	
	
}

function updateSequestrationToUnlock(sid, csid){
	c2xOrgSeq.updateSequestrationToUnlock(sid, csid);
}

function openProceedingsAttachment(pid){
	c2xOrgSeq.openProceedAttachment(pid);
	fireMyPopup('ProceedingsFrom',400,350);	
}   

function closeProceedingsAttachment(){
	fadeOutMyPopup('ProceedingsFrom');	
	return false;
}



</script>