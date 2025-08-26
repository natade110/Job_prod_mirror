<?php
require_once 'db_connect.php';
require_once 'c2x_model.php';
require_once 'c2x_include.php';
require_once 'scrp_config.php';
require_once 'lawstatus_log.php';


class ManageNotice {
	public $NOTICE_FILE_TYPE = "NoticeDocument_docfile";
	
	public function saveNotice($post,$files, $id, $userfullname, $lawStatus, $theCompanyWord, $hireDocfileRelatePath){
		
		$dupedKey = false;	
		$dupedID = 0;
		$saveResult = new ManageNotice();
		$saveResult->IsComplete = false;
		
		
		try{
			$model = $this->getNoticeDocumentInput($id, $post);
			
			$attachments = fixFilesArray($files["NoticeDocument_Docfile"]);			
			
			$validateResult = $this->validateNoticeDocument($model, $attachments, $theCompanyWord);
			
			if($validateResult->IsValid){		
				
				if($id == 0){
					$saveResult = $this->insertNoticeDocument($model,$attachments, $userfullname, $lawStatus, $hireDocfileRelatePath);
				}else{
					$saveResult = $this->updateNoticeDocument($model, $attachments, $userfullname, $lawStatus, $hireDocfileRelatePath);
				}
							
			}else{
				$saveResult->DupedKey = $validateResult->DupedKey;
				$saveResult->DupedID = $validateResult->DupedID;
				$saveResult->Data = $model;
				$saveResult->Message = $validateResult->Message;
			}	
			
			
		}catch(Exception $ex){
			$saveResult->Message = $ex->getMessage();
		}			
		
		return $saveResult;		
	}
	
		
	/**
	 * @param int $id
	 * @return NoticeDocumentResult
	 */
	public function getNoticeDocument($id){
		$result = new NoticeDocumentResult();
		
		$sql = "SELECT n.NoticeID, c.CID, c.CompanyCode, c.BranchCode, c.CompanyNameThai AS CompanyName, c.CompanyTypeCode,
		n.DocumentDate, n.GovDocumentNo, n.NoticeDetail, n.TotalAmount, n.CreatedBy, n.Receiver, n.ReceivedDate, n.RequestNo
		FROM company c
		INNER JOIN noticedocument n ON c.CID = n.CID
		WHERE n.NoticeID = $id";
		$sqlResult = mysql_query($sql);
		$noticeDocument = mysql_fetch_object($sqlResult, "NoticeDocument");
		
		try {
			if($noticeDocument){
				$result->IsComplete = true;
		
				$noticeDocument->NoticeDetails = $this->getNoticeDetails($id);
		
				$result->Data = $noticeDocument;
			}else{
				$result->IsComplete = false;
				$result->Message = "ไม่พบข้อมูล";
			}
		} catch (Exception $ex) {
			$result->IsComplete = false;
			$result->Message = $ex->getMessage();
			handleUnexpectedException($ex);
		}
		
		
		
		return $result;		
	}
	
	/**
	 * @param $cid
	 * @return NoticeDocumentListResult
	 */
	public function getNoticeDocumentList($cid){
		$result = new NoticeDocumentListResult();
	
		$sql = "SELECT n.NoticeID, n.DocumentDate,  n.GovDocumentNo, n.TotalAmount, n.CreatedBy, n.Receiver , n.ReceivedDate, n.TotalAmount, n.RequestNo
					FROM noticedocument n
					WHERE n.CID = $cid
					ORDER BY n.DocumentDate, n.GovDocumentNo
				";
		
	
		try{
			$sqlResult = mysql_query($sql);
			$error = mysql_error();	
				
			if($error == ""){
				$result->IsComplete = true;
				
				$dataItems = array();
				while ($row = mysql_fetch_object($sqlResult, "NoticeDocumentList")){
					array_push($dataItems, $row);
				}
				
				$result->Data = (count($dataItems) > 0)? $dataItems : null;
				
				
			}else{
				$result->IsComplete = false;
				$result->Message = $error;
				error_log($error);
			}
			
		} catch (Exception $ex) {
			handleUnexpectedException($ex);
			$result->IsComplete = false;
			$result->Message = $ex->getMessage();
		}
	
		return $result;
	}
	
	
	
	
	/**
	 * @param int $sid
	 * @param $_POST $post
	 * @return NoticeDocument
	 */
	public function getNoticeDocumentInput($id, $post){
		$model = new NoticeDocument();
		
		$model->NoticeID = $id;
	
		$text = $post["HiddCID"];
		$model->CID = $text;
	
		$text = $post["HiddCompanyCode"];
		$model->CompanyCode = $text;
	
		$text = $post["HiddBranchCode"];
		$model->BranchCode = $text;
		
		$text = $post["CompanyName"];
		$model->CompanyName = $text;
		
		$text = $post["CompanyTypeCode"];
		$model->CompanyTypeCode = $text;
		
		$text = $post["CreatedBy"];
		$model->CreatedBy = $text;
	
		$day = $post["DocumentDate_day"];
		$month = $post["DocumentDate_month"];
		$year = $post["DocumentDate_year"];
		
		if(($day != "00") && ($month != "00") && ($year != "0000")){
			$model->DocumentDate = $year."-".$month."-".$day;
		}else{
			$model->DocumentDate = null;
		}	
					
		
		$text = $post["GovDocumentNo"];		
		$model->GovDocumentNo = trim($text);

		$text = $post["NoticeDetail"];
		$model->NoticeDetail = trim($text);
		
		$text = $post["TotalAmount"];
		$totalAmount = (float)$text;
		$model->TotalAmount = round($totalAmount, 2);
		
		if(isset($post["Receiver"])){
			$text = $post["Receiver"];
			$model->Receiver = trim($text);
		}
		
		if(isset($post["ReceivedDate_day"])){
			$day = $post["ReceivedDate_day"];
			$month = $post["ReceivedDate_month"];
			$year = $post["ReceivedDate_year"];
			
			if(($day != "00") && ($month != "00") && ($year != "0000")){
				$model->ReceivedDate = $year."-".$month."-".$day;
			}else{
				$model->ReceivedDate = null;
			}	
		}
		
		$text = $post["RequestNo"];
		$model->RequestNo = trim($text);
	
		$noticeDetails = (!empty($post["NoticeDetails"]))? json_decode($post["NoticeDetails"]) : null;
		$model->NoticeDetails = $noticeDetails;
	
		return $model;
	}
	
	/**
	 * @param int $id
	 * @param $_POST $post
	 * @return NoticeDocumentResult
	 */
	public function deleteNoticeDocument($id, $post){
		$result = new NoticeDocumentResult();
		$result->IsComplete = true;
		$model = $this->getNoticeDocumentInput($id, $post);
		$result->Data = $model;
		
		$delResult = $this->deleteNoticeDocumentWithID($id, $model->CID);		
		$result->IsComplete = $delResult->IsComplete;
		$result->Message = $delResult->Message;					
		
		return  $result;
	}  
	
	
	public function deleteNoticeDocumentWithID($id, $cid){
		$result = new ResultMessage();
		$result->IsComplete = true;
			
		dbBegin();
		// delete noticeattachment
		$delNoticeAttach = "DELETE FROM noticeattachment WHERE NoticeID = $id";			
		mysql_query($delNoticeAttach);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
		
		// delete file
		$delFile = "DELETE FROM files WHERE (file_for = $id) AND (file_type = '$this->NOTICE_FILE_TYPE')";
		mysql_query($delFile);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
		
		// delete noticedetail
		$delDetails = "DELETE FROM noticedetail WHERE NoticeID = $id";
		mysql_query($delDetails);
		$error =  mysql_error();
		if($error!= ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
		
		// delete noticedocument
		$delNoticedocument = "DELETE FROM noticedocument WHERE NoticeID = $id";
		mysql_query($delNoticedocument);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}		
		
		global $sess_userid;
		lawstatus_delete($cid, $id, LAWSTATUS_LOG_DOCUMENT_TYPE_NOTICE, $sess_userid);
		
		dbCommit();
		
		return $result;
	}
		
	/**
	 * @param NoticeDocument $model
	 * @return ResultMessage
	 */
	public function updateNoticeDocumentRecieved($model){
		$result = new ResultMessage();		
		global $sess_userfullname;
		$updateBy = "'".mysql_real_escape_string($sess_userfullname)."'";
		
		$receivedDate = (is_null($model->ReceivedDate))? "NULL" : "'".$model->ReceivedDate->format("Y-m-d")."'";
		$receiver = ($model->Receiver == "")? "NULL" : "'".mysql_real_escape_string($model->Receiver)."'";
		$requestNo = (is_null($model->RequestNo))? "NULL" : "'".mysql_real_escape_string($model->RequestNo)."'";
		
		$sql = "UPDATE noticedocument SET
					Receiver = $receiver,
					ReceivedDate = $receivedDate,
					RequestNo = $requestNo,
					ModifiedBy = $updateBy,
					ModifiedDate = NOW()
				WHERE NoticeID = $model->NoticeID";
		
		mysql_query($sql);
		$errorMsg = mysql_error();
		if($errorMsg == ""){
			$result->IsComplete = true;
		}else{
			$result->IsComplete = false;
			$result->Message = $errorMsg;
			error_log($errorMsg);
		}
		return $result;
	} 	
	
	/**
	 * 
	 * @param int $cid
	 * @return NoticeYearResult
	 */
	public function canCreateCompanyNoticeDocument($cid){
		global $LAWFUL_STATUS;
		global $COMPANY_LAW_STATUS;
		$hasRole = hasCreateRoleSequestrationByCID($cid);
	
		$result = new NoticeYearResult();
		$result->IsComplete = true;
		$result->Data = null;
		
		if($hasRole){
			$sql = "SELECT l.Year
				FROM lawfulness l
				INNER JOIN company c ON l.CID = c.CID
				WHERE (l.CID = $cid) AND ((l.LawfulStatus = '0' or l.LawfulStatus is null) or (l.LawfulStatus = '2'))
				AND (c.LawStatus = $COMPANY_LAW_STATUS->ยังไม่ดำเนินการ)
				ORDER BY l.Year limit 0, 1";
			
			
			
			$sqlResult = mysql_query($sql);
			$errorMsg = mysql_error();
			if($errorMsg == ""){
				$row = mysql_fetch_array($sqlResult);
				if($row){
					$result->Data = $row["Year"];
				}
			}else{
				$result->IsComplete = false;
				$result->Message = $errorMsg;
				error_log($errorMsg);
			}
		}
		
		return $result;
	}
	/**
	 * @param $_FILES $files
	 * @return ResultMessage $result
	 */
	private function saveAttachment($files, $id, $hireDocfileRelatePath){
		$fileFor = $this->NOTICE_FILE_TYPE;
		$result = new ResultMessage();
		$result->IsComplete = true;		
	
		
		if(!is_null($files)){
			
			$files = (is_array($files))? $files : array($files);
							
			for($i = 0; $i < count($files); $i++){
				$file = $files[$i];
				
				$hireDocfileFileName = $file->name;
				$newHireDocfileName = date("dmyhis").rand(00,99)."_".$hireDocfileFileName; 
				$hireDocfilePath = $hireDocfileRelatePath.$newHireDocfileName;
				$hireDocfilePath2 = $filename2 = iconv("UTF-8", "windows-874", $hireDocfilePath);
				$hireDocfilePath2 = $hireDocfilePath;
				
				
				if(move_uploaded_file($file->tmp_name, $hireDocfilePath2)){
					
					$newHireDocfileName = mysql_real_escape_string($newHireDocfileName);
					$insertFileSql = "INSERT INTO files(file_name, file_for, file_type)
									  VALUES('$newHireDocfileName', '$id', '$fileFor')";
					mysql_query($insertFileSql);
					
					$mysql_error = mysql_error();
					if($mysql_error != ""){
						throw new Exception($mysql_error);
					}
					$fid = mysql_insert_id();
					
					
					
					$insertSeqFileSql = "INSERT INTO noticeattachment(file_id, NoticeID)
										 VALUES('$fid', '$id')";
					mysql_query($insertSeqFileSql);
					
					$mysql_error = mysql_error();
					if($mysql_error != ""){
						throw new Exception($mysql_error);
					}	
					
					
				}
			}					
			
			
		}		
		return $result;
	}
	
	
	
	
	/***
	 * @param NoticeDocument $model
	 * @param $files : [{name = "", type = "", tmp_name = "", error = "", size = ""}]
	 * @return NoticeDocumentResult
	 */
	private function insertNoticeDocument($model, $files, $userfullname, $lawStatus, $hireDocfileRelatePath){	
		global $sess_userid;		
		$result = new NoticeDocumentResult();
		$result->IsComplete = false;		
		
		$documentDateFormat = $model->DocumentDate;
		$userfullname = mysql_real_escape_string($userfullname);
		$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);
		$detail = mysql_real_escape_string($model->NoticeDetail);
		
		$the_sql = "INSERT INTO noticedocument(CID, DocumentDate, GovDocumentNo, NoticeDetail, TotalAmount, CreatedDate, CreatedBy, CreatedByID)
					VALUES($model->CID, '$documentDateFormat', '$govDocumentNo', '$detail', $model->TotalAmount, NOW(), '$userfullname', $sess_userid)";
		try {
			dbBegin();
			
			mysql_query($the_sql);
			$error = mysql_error();
			if($error != ""){
				throw new Exception($error);
			}
			$id = mysql_insert_id();
			
			$this->insertNoticeDetails($id, $model->CompanyCode, $model->BranchCode, $model->NoticeDetails);
			
			$saveFilesResult = $this->saveAttachment($files, $id, $hireDocfileRelatePath);
			if($saveFilesResult->IsComplete == false){
				throw new Exception($saveFilesResult->Message);
			}
			
			global $sess_userid;
			error_log('--> cid ='.$model->CID.', id = '.$id.', type = '.LAWSTATUS_LOG_DOCUMENT_TYPE_NOTICE.', userid ='. $sess_userid);
			lawstatus_add($model->CID, $id, LAWSTATUS_LOG_DOCUMENT_TYPE_NOTICE, $sess_userid);
			
			dbCommit();
			
			$result->IsComplete = true;
			$model->NoticeID = $id;
			
			
		}catch(Exception $ex){
			dbRollback();
			handleUnexpectedException($ex);
			$result->IsComplete = false;
			$result->Message = $ex->getMessage();
		}
		
		$result->Data = $model;
		return  $result;
	}
	
	/**
	 * @param NoticeDocument $model
	 * @param $files : [{name = "", type = "", tmp_name = "", error = "", size = ""}]
	 * @param string $username
	 * @param int $lawStatus
	 * @return NoticeDocumentResult
	 */
	private function updateNoticeDocument($model, $files, $userfullname, $lawStatus, $hireDocfileRelatePath){
		global $sess_userid;
		$result = new NoticeDocumentResult();
		$result->IsComplete = false;
		
		$id = $model->NoticeID;
		$documentDateFormat = $model->DocumentDate;
		$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);
		$detail = mysql_real_escape_string($model->NoticeDetail);
		$userfullname = mysql_real_escape_string($userfullname);
		$receiver = isset($model->Receiver)? "'".mysql_real_escape_string($model->Receiver)."'" : "NULL";
		$requestNo = isset($model->RequestNo)? "'".mysql_real_escape_string($model->RequestNo)."'" : "NULL";
		
		$receivedDate = (isset($model->ReceivedDate))? "'".$model->ReceivedDate."'" : "NULL";
		
		$the_sql = "UPDATE noticedocument SET 
						GovDocumentNo = '$govDocumentNo',
						DocumentDate = '$documentDateFormat',
						TotalAmount = $model->TotalAmount,
						NoticeDetail = '$detail',
						Receiver = $receiver,
						RequestNo = $requestNo,
						ReceivedDate = $receivedDate,
						ModifiedDate = NOW(),
						ModifiedBy = '$userfullname',
						ModifiedByID = $sess_userid
					WHERE NoticeID = $id
					";
		
		try{
			dbBegin();
			mysql_query($the_sql);			
				
			$this->insertNoticeDetails($id, $model->CompanyCode, $model->BranchCode, $model->NoticeDetails);
			$saveFilesResult = $this->saveAttachment($files, $id, $hireDocfileRelatePath);
			if($saveFilesResult->IsComplete == false){
				throw new Exception($saveFilesResult->Message);
			}
			
			dbCommit();
			
			$mysqlError = mysql_error();
			if(empty($mysqlError)){
				$result->IsComplete = true;				
			}else{
				$result->IsComplete = false;
				$result->Message = $mysqlError;
			}				
			
		}catch(Exception $ex){
			dbRollback();
			handleUnexpectedException($ex);			
			$result->IsComplete = false;
			$result->Message = $ex->getMessage();
		}
		$result->Data = $model;
		
		return  $result;
	}

	
	
	private function insertNoticeDetails($id, $companyCode, $branchCode, array $details){	
		$sqlDel = "DELETE FROM noticedetail WHERE NoticeID = $id";
		mysql_query($sqlDel);		
		global $DEBT_INTEREST_RATE;
		$lidList = $this->getLID($companyCode, $branchCode);
		
		for ($i = 0; $i < count($details); $i++){
			$item = $details[$i];
			$lid = $lidList[$item->Year];
			$interestAmount = $item->InterestAmount;			
			$interestRate = ($item->Year > 2011)? $DEBT_INTEREST_RATE : 0;
			$sqlInsert = "INSERT INTO noticedetail(LID, NoticeID, PrincipleAmount, InterestAmount, InterestRate, TotalAmount, InterestPerDay)
								VALUES($lid, $id, $item->PrincipleAmount, $interestAmount, $interestRate, $item->TotalAmount, $item->InterestPerDay)";
			
			mysql_query($sqlInsert);
			$error = mysql_error();
			if($error != ""){
				throw new Exception($error);
			}
			
		}
	}
	
	
	private function getLID($companyCode, $branchCode){
		$list = array();
		$sql = "SELECT l.Year, l.LID FROM company c INNER JOIN lawfulness l ON c.CID = l.CID WHERE c.CompanyCode = '$companyCode' and c.BranchCode = '$branchCode'";
		$sqlResult = mysql_query($sql);
		while ($row = mysql_fetch_row($sqlResult)){
			$list[$row[0]] = $row[1];
		}	
		
		return  $list;
	}
	
	/**
	 * @param int $sid
	 * @return NoticeDetail[]
	 */
	private function getNoticeDetails($id){
		$sql = "SELECT noticeD.NoticeID, noticeD.LID, 
				noticeD.PrincipleAmount,  noticeD.InterestAmount, noticeD.InterestRate, noticeD.TotalAmount, noticeD.InterestPerDay,
				law.Year
				FROM noticedetail noticeD
				INNER JOIN lawfulness law ON noticeD.LID = law.LID
				WHERE noticeD.NoticeID = $id ORDER BY law.Year" ;
		$sqlResult = mysql_query($sql);
		$details = array();
		while ($detail = mysql_fetch_object($sqlResult, "NoticeDetail")) {
			array_push($details, $detail);
		}
		mysql_free_result($sqlResult);
		return (count($details) > 0)? $details : null;
	}
	
	/**
	 * @param NoticeDocument $model
	 * @param array of file : [{name = "", type = "", tmp_name = "", error = "", size = ""}] 
	 * @param string $theCompanyWord
	 * @return NoticeDocumentResult $result
	 */
	private function validateNoticeDocument($model, $files, $theCompanyWord){
		$result = new NoticeDocumentValidationResult();
		$result->IsValid = true;
	
		$errorMessage = "";
		$id = $model->NoticeID;
	
		if(!empty($model->GovDocumentNo)){
			$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);
			$sqlChkDup = "SELECT count(*), NoticeID FROM noticedocument WHERE GovDocumentNo ='$govDocumentNo' AND NoticeID <> $id";
			$sqlChkDupResult = mysql_query($sqlChkDup);
			
			$error = mysql_error();
			if($error != ""){
				throw new Exception($error);
			}
			$row = mysql_fetch_row($sqlChkDupResult);
			$count = $row[0];
	
			if($count > 0){
				$result->IsValid = false;
				$result->DupedKey = true;
				$result->DupedID = $row[1];
			}
		}
	
		if(is_null($model->DocumentDate)){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: วันที่\n";
		}
	
		if(empty($model->GovDocumentNo)){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: หนังสือเลขที่\n";
		}		
	
		if(is_null($model->NoticeDetails)){
			$result->IsValid = false;
			$errorMessage .="กรุณาเลือก: ".$theCompanyWord."ที่ต้องการแจ้งโนติส\n";
		}
		
		if(!is_null($model->ReceivedDate)){
			$tmp = explode("-", $model->ReceivedDate);
			$year = $tmp[0];
			$month = $tmp[1];
			$day = $tmp[2];
			
			if((($day == "00") && ($month == "00") && ($year == "0000")) ||(($day != "00") && ($month != "00") && ($year != "0000")) ){
				
			}else{
				$result->IsValid = false;
				$errorMessage .="กรุณาใส่ข้อมูล: วันที่รับเอกสารให้ถูกต้อง\n";
			}
		}
		
		$fileTypeIsValid = checkAllowFileUpload($files);
		if(!$fileTypeIsValid){
			$result->IsValid = false;
			$errorMessage .="ชนิดไฟล์แนบไม่ถูกต้อง\n";
		}
	
		$result->Message = $errorMessage;
		return $result;
	}
	
}	


class NoticeDetail{	
	/***
	 *
	 * @var int
	 */
	public $LID;
	/***
	 *
	 * @var int
	 */
	public $NoticeID;
	/***
	 *
	 * @var int
	 */
	public $Year;
	/***
	 *
	 * @var double
	 */
	public $PrincipleAmount;
	/***
	 *
	 * @var double
	 */
	public $InterestAmount;
	/***
	 *
	 * @var double
	 */
	public $InterestRate;
	/***
	 *
	 * @var double
	 */
	public $TotalAmount;
	/***
	 *
	 * @var double
	 */
	public $InterestPerDay;
}



class NoticeDocumentList{
	/**
	 *
	 * @var int
	 */
	public $NoticeID;
	
	/**
	 *
	 * @var DateTime
	 */
	public $DocumentDate;
	/**
	 *
	 * @var string
	 */
	public $GovDocumentNo;
	/**
	 *
	 * @var DateTime
	 */
	public $ReceivedDate;
	/**
	 *
	 * @var string
	 */
	public $Receiver;
	/**
	 *
	 * @var string
	 */
	public 	$RequestNo;
	/**
	 *
	 * @var string
	 */
	public $CreatedBy;
	/**
	 *
	 * @var double
	 */
	public $TotalAmount;
}

class NoticeDocument{
	/**
	 * 
	 * @var int
	 */
	public $NoticeID;
	/**
	 *
	 * @var int
	 */
	public $CID;	
	/**
	 *
	 * @var string
	 */
	public $CompanyCode;
	/**
	 *
	 * @var string
	 */
	public $BranchCode;
	/**
	 *
	 * @var string
	 */
	public $CompanyName;
	/**
	 *
	 * @var string
	 */
	public $CompanyTypeCode;
	/**
	 *
	 * @var DateTime
	 */
	public $DocumentDate;
	
	
	/**
	 *
	 * @var string
	 */
	public $GovDocumentNo;
	/**
	 *
	 * @var string
	 */
	public $NoticeDetail;	
	/**
	 *
	 * @var string
	 */
	public $CreatedBy;
	
	/**
	 *
	 * @var string
	 */
	public 	$Receiver;
	/**
	 *
	 * @var string
	 */
	public 	$RequestNo;
	/**
	 *
	 * @var DateTime
	 */
	public $ReceivedDate;
	/**
	 *
	 * @var Noticedetail[]
	 */
	public $NoticeDetails;
	
}

class NoticeDocumentResult extends ResultMessage{
	/**
	 * @var NoticeDocument
	 */
	public $Data;
	
	/**
	 * @var bool
	 */
	public $DupedKey;
	
	/**
	 * @var int
	 */
	public $DupedID;
}

class NoticeDocumentListResult extends ResultMessage{
	/**
	 * @var NoticeDocumentList
	 */
	public $Data;
}

class NoticeDocumentValidationResult extends ResultValidation{
	/**
	 * @var bool
	 */
	public $DupedKey;
	
	/**
	 * @var int
	 */
	public $DupedID;	
	
}

class NoticeYearResult extends ResultMessage{
	/**
	 * ปีที่สามารถสร้างโนตีส
	 * @var int
	 */
	public $Data;
}

