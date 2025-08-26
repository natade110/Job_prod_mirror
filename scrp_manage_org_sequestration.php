<?php
require_once 'scrp_notice.php';
require_once 'scrp_add_collection.php';
require_once 'scrp_sequestration.php';
require_once 'scrp_proceeding.php';
require_once 'c2x_include.php';
require_once 'c2x_model.php';

$TASK_NAME = (object)array(
		"SaveCollectionCompany" => 0, 
		"DelCollectionCompany" => 1,
		
		"SaveNoticeDocument" => 2, 
		"DelNoticeDocument" => 3,				
		
		"DelSequestration" => 4, 
		"SaveCancelledSequestration" => 5, 
		"DelCancelledSequestration"=> 6,
		
		"DelProceedings" => 7,
		
		"CheckCanCreateCollection" => 8,
		"CheckCanCreateNotice" => 9,
		"CheckCanCreateSequest" => 10,
		"CheckCanCreateProceeds" => 11,
);


$result = new ResultMessage();
if(isset($_POST["task"]) && isset($_POST["id"])){
	$task = $_POST["task"];
	$id = $_POST['id'];
	$cid = isset($_POST)? $_POST["cid"] : 0;
	switch ($task){
		case $TASK_NAME->SaveCollectionCompany :	
				$receiverno = $_POST["receiverno"];
				$receiverdate = (($_POST["receiverdate"] == null)? null : new DateTime($_POST["receiverdate"])) ;
				$receiver = $_POST["receiver"];
				$model = new CollectionCompany();
				$model->CCID = $id;
				$model->ReceiveNo = $receiverno;
				$model->ReceiveDate = $receiverdate;
				$model->Receiver = $receiver;
				
				$manage = new ManageCollection();								
				$updateResult = $manage->updateCollectionCompanyReceived($model);
				$result->IsComplete = $updateResult->IsComplete;
				$result->Message = $updateResult->Message;				
			break;
			
		case $TASK_NAME->DelCollectionCompany :
			$manage = new ManageCollection();	
			$delResult = $manage->deleteCollectionCompanyWithID($id);
			$result->IsComplete = $delResult->IsComplete;
			$result->Message = $delResult->Message;
			break;
			
		case $TASK_NAME->SaveNoticeDocument :
			$received = $_POST["received"];
			$receivedate = ($_POST["receivedate"] == null)? null : new DateTime($_POST["receivedate"]);
			$requestno = $_POST["requestno"];
			
			$model = new NoticeDocument();
			$model->NoticeID = $id;
			$model->Receiver = $received;
			$model->ReceivedDate = $receivedate;	
			$model->RequestNo = $requestno;
			
			$manage = new ManageNotice();
			$updateResult = $manage->updateNoticeDocumentRecieved($model);
			$result->IsComplete = $updateResult->IsComplete;
			$result->Message = $updateResult->Message;
			break;
			
		case $TASK_NAME->DelNoticeDocument :
			$manage = new ManageNotice();
			$delResult = $manage->deleteNoticeDocumentWithID($id, $cid);
			$result->IsComplete = $delResult->IsComplete;
			$result->Message = $delResult->Message;
			break;
			
		case $TASK_NAME->DelSequestration :
			$manage = new ManageSequestration();
			$delResult = $manage->deleteSequestrationWithID($id, $cid);
			$result->IsComplete = $delResult->IsComplete;
			$result->Message = $delResult->Message;
			break;	
			
		case $TASK_NAME->SaveCancelledSequestration :
			break;
			
		case $TASK_NAME->DelCancelledSequestration :
			break;
			
		case $TASK_NAME->DelProceedings :
			$manage = new ManageProceeding();
			$delResult = $manage->deleteProceedingWithID($id,$cid);
			$result->IsComplete = $delResult->IsComplete;
			$result->Message = $delResult->Message;
			break;
			
		case $TASK_NAME->CheckCanCreateCollection :
			$manage = new ManageCollection();
			$checkResult = $manage->canCreateCompanyCollection($cid);
			$result = $checkResult;			
			break;
			
		case $TASK_NAME->CheckCanCreateNotice :
			$manage = new ManageNotice();
			$checkResult = $manage->canCreateCompanyNoticeDocument($cid);
			$result = $checkResult;
			break;
			
		case $TASK_NAME->CheckCanCreateSequest :
			$manage = new ManageSequestration();
			$checkResult = $manage->canCreateCompanySequestration($cid);
			$result = $checkResult;
			break;
			
		case $TASK_NAME->CheckCanCreateProceeds :
			$manage = new ManageProceeding();
			$checkResult = $manage->canCreateCompanyProceeds($cid);
			$result = $checkResult;
			break;
	}
	
}else{
	$result->Message = "ไม่พบข้อมูล";
}

echo  json_encode($result);
