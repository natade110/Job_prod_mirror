<?php
require_once 'db_connect.php';
require_once 'c2x_model.php';
require_once 'c2x_include.php';
require_once 'scrp_config.php';
require_once 'lawstatus_log.php';


class ManageSequestration {
	public   $SEQUESTRATION_FILE_TYPE = "Sequestration_docfile";
	public   $CANCELLED_FILE_TYPE = "Cancelledsequest_docfile";
	public function saveSequestration($post,$files, $sid, $userfullname, $lawStatus, $seqType, $theCompanyWord, $hireDocfileRelatePath){
		
		$dupedKey = false;	
		$dupedSID = 0;
		$saveResult = new SequestrationResult();
		$saveResult->IsComplete = false;
		
		
		try{
			$model = $this->getSequestrationInput($sid, $post);
			
			$attachments = fixFilesArray($files["Sequestration_Docfile"]);			
			$validateResult = $this->validateHolding($model, $attachments, $theCompanyWord);
			
			if($validateResult->IsValid){		
				
				if($sid == 0){
					$saveResult = $this->insertSequestration($model,$attachments, $userfullname, $lawStatus, $hireDocfileRelatePath);
				}else{
					$saveResult = $this->updateSequestration($model, $attachments, $userfullname, $lawStatus, $hireDocfileRelatePath);
				}
							
			}else{
				$saveResult->DupedKey = $validateResult->DupedKey;
				$saveResult->DupedSID = $validateResult->DupedSID;
				$saveResult->Data = $model;
				$saveResult->Message = $validateResult->Message;
			}	
			
			
		}catch(Exception $ex){
			$saveResult->Message = $ex->getMessage();
		}			
		
		return $saveResult;		
	}
	
		
	/**
	 * @param int $sid
	 * @param object $seqType
	 * @return SequestrationResult
	 */
	public function getSequestration($sid, $seqType){
		$result = new SequestrationResult();
		
		$sql = "SELECT s.SID, c.CID, c.CompanyCode, c.BranchCode, c.CompanyNameThai AS CompanyName, c.CompanyTypeCode,
		s.DocumentDate, s.GovDocumentNo, s.SequestrationDetail, s.TotalAmount, s.CreatedBy, cs.CSID
		FROM company c
		INNER JOIN sequestration s ON c.CID = s.CID
		LEFT JOIN cancelledsequestration cs ON s.SID = cs.SID
		WHERE s.SID = $sid";
		$sqlResult = mysql_query($sql);
		$sequestration = mysql_fetch_object($sqlResult, "Sequestration");
		
		try {
			if($sequestration){
				$result->IsComplete = true;				
				$sequestration->NoticeDate = $this->getNoticeReceivedDate($sequestration->SID, $sequestration->CID);
				$sequestration->Start2011InterestDate = $this->getStart2011InterestDate($sequestration->NoticeDate);
		
				$sequestration->SequestrationMoneyDetails = $this->getSequestrationDetails($sid, $seqType->Money);
				$sequestration->SequestrationPropertyDetails = $this->getSequestrationDetails($sid, $seqType->Property);
				$sequestration->SequestrationCarDetails = $this->getSequestrationDetails($sid, $seqType->Car);
				$sequestration->SequestrationOtherDetails = $this->getSequestrationDetails($sid, $seqType->Other);
		
				$sequestration->SequestrationPayments = $this->getSequestrationPayments($sid);
				$result->Data = $sequestration;
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
	 * @return SequestrationListResult
	 */
	public function getSequestrationList($cid){
		$result = new SequestrationListResult();
	
		$sql = "SELECT s.SID, cs.CSID, s.DocumentDate,  s.GovDocumentNo, s.TotalAmount, s.CreatedBy
					FROM sequestration s
			        LEFT JOIN cancelledsequestration cs ON s.SID = cs.SID
					WHERE s.CID = $cid
				";
		
	
		try{
			$sqlResult = mysql_query($sql);
			$error = mysql_error();	
				
			if($error == ""){
				$result->IsComplete = true;
				
				$dataItems = array();
				while ($row = mysql_fetch_object($sqlResult, "SequestrationList")){
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
	 * @return Sequestration
	 */
	public function getSequestrationInput($sid, $post){
		$model = new Sequestration();
		
		$model->SID = $sid;
		
		$text = $post["HiddCSID"];
		$model->CSID = $text;
		
	
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
		
		$text = $post["NoticeDate"];
		$model->NoticeDate = (empty($text))? null : $text;
		
		$text = $post["Start2011InterestDate"];
		$model->Start2011InterestDate = (empty($text))? null : $text;
		
	
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
		
		$text = isset($post["CreatedBy"])? $post["CreatedBy"] : "";
		$model->CreatedBy = $text;

		$text = $post["SequestrationDetail"];
		$model->SequestrationDetail = trim($text);
		
		$text = $post["TotalAmount"];
		$totalAmount = (float)$text;
		$model->TotalAmount = round($totalAmount, 2);
	
		$sequesterDetails = (!empty($post["HiddSequesterTypeMoneyData"]))? json_decode($post["HiddSequesterTypeMoneyData"]) : null;
		$model->SequestrationMoneyDetails = $sequesterDetails;	
	
		$sequesterDetails = (!empty($post["HiddSequesterTypePropertyData"]))? json_decode($post["HiddSequesterTypePropertyData"]) : null;
		$model->SequestrationPropertyDetails = $sequesterDetails;
		
		$sequesterDetails = (!empty($post["HiddSequesterTypeCarData"]))? json_decode($post["HiddSequesterTypeCarData"]) : null;
		$model->SequestrationCarDetails = $sequesterDetails;
		
		$sequesterDetails = (!empty($post["HiddSequesterTypeOtherData"]))? json_decode($post["HiddSequesterTypeOtherData"]) : null;
		$model->SequestrationOtherDetails = $sequesterDetails;
	
		$sequesterPayments = (!empty($post["SequestrationPayments"]))? json_decode($post["SequestrationPayments"]) : null;
		$model->SequestrationPayments = $sequesterPayments;
	
		return $model;
	}
	
	public function getCancelSequestrationInput($sid, $csid, $cid, $post){
		$model = new CancelledSequestration();
		
		$model->SID = $sid;
	
		$model->CSID = is_numeric($csid)? $csid : 0;	
		
		$model->CID = $cid;
	
		$text = $post["RequestNo"];
		$model->RequestNo = trim($text);
		
		$text = $post["HiddRequestDate"];
		$model->RequestDate = ($text != "")? new DateTime($text) : null;
		
		$text = $post["CancelledDetail"];
		$model->CancelledDetail = trim($text);	
	
		return $model;
	}
	
	/**
	 * @param int $csid
	 * @return CancelSequestrationResult
	 */
	public function getCancelSequestration($csid){
		$result = new CancelSequestrationResult();
		if($csid > 0){
			$sql = "SELECT s.SID, cs.CSID, s.CID, cs.RequestNo, cs.RequestDate, cs.CancelledDetail, cs.CreatedBy, cs.CreatedDate
				    FROM cancelledsequestration cs
				    INNER JOIN sequestration s ON cs.SID = s.SID
					WHERE cs.CSID = $csid";
			
			$sqlResult = mysql_query($sql);
			$error = mysql_error();
			if($error == ""){
				$model = mysql_fetch_object($sqlResult, "CancelledSequestration");
				
				if($model){
					
					$dateFormat = $model->RequestDate;
					$model->RequestDate = new DateTime($dateFormat);
					
					$dateFormat = $model->CreatedDate;
					$model->CreatedDate = new DateTime($dateFormat);
				
					$result->IsComplete = true;
					$result->Data = $model;
				}
				
				
			}else{
				$result->IsComplete = false;
				$result->Message = $error;
			}
		}else{
			$result->IsComplete = false;
			$result->Message = "ไม่พบข้อมูล";
		}
		
		return $result;	
	}
	
	/**
	 * @param int $sid
	 * @param $_POST $post
	 * @return SequestrationResult
	 */
	public function deleteSequestration($sid, $post){
		$result = new SequestrationResult();
		$result->IsComplete = true;
		$model = $this->getSequestrationInput($sid, $post);
	
		$result->Data = $model;
		
		$delResult = $this->deleteSequestrationWithID($sid, $model->CID);		
		$result->IsComplete = $delResult->IsComplete;
		$result->Message = $delResult->Message;					
		
		return  $result;
	}  
	
	
	public function deleteSequestrationWithID($sid, $cid){
		$result = new ResultMessage();
		$result->IsComplete = true;
		
		$csidSql = "SELECT CSID FROM cancelledsequestration WHERE SID = $sid";		
		$csidSqlResult = mysql_query($csidSql);
		$csidArray = mysql_fetch_array($csidSqlResult);
		$csid = 0;
		if(!is_null($csidArray) && (count($csidArray) > 0)){
			$csid = $csidArray["CSID"];
		}
		
		dbBegin();
		
		if($csid > 0){
			// delete cancelledsequestttach
			$delCancelledAttach = "DELETE FROM cancelledsequestttach WHERE CSID = $csid";
			mysql_query($delCancelledAttach);
			$error =  mysql_error();
			if($error != ""){
				$result->IsComplete = false;
				$result->Message = $error;
				error_log($error);
				dbRollback();
				return  $result;
			}
			
			// delete files
			$delCancelledFiles = "DELETE FROM files WHERE file_for = $csid AND file_type = '$this->CANCELLED_FILE_TYPE'";
			mysql_query($delCancelledFiles);
			$error =  mysql_error();
			if($error != ""){
				$result->IsComplete = false;
				$result->Message = $error;
				error_log($error);
				dbRollback();
				return  $result;
			}
			
			// delete cancelledsequestration
			$delCancelled = "DELETE FROM cancelledsequestration WHERE CSID = $csid";
			mysql_query($delCancelled);
			$error =  mysql_error();
			if($error != ""){
				$result->IsComplete = false;
				$result->Message = $error;
				error_log($error);
				dbRollback();
				return  $result;
			}
			
			
			global $sess_userid;			
			lawstatus_delete($cid, $csid, LAWSTATUS_LOG_DOCUMENT_TYPE_CANCELLED_SEQUESTRATION, $sess_userid);
			
		}
		
		
		// delete sequestrationattachment
		$delSeqAttach = "DELETE FROM sequestrationattachment WHERE SID = $sid";	
		mysql_query($delSeqAttach);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
		
		// delete files
		$delSeqFiles = "DELETE FROM files WHERE file_for = $sid AND file_type = '$this->SEQUESTRATION_FILE_TYPE'";
		mysql_query($delSeqFiles);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
		
		// delete sequestrationpayment
		$delPayments = "DELETE FROM sequestrationpayment WHERE SID = $sid";
		mysql_query($delPayments);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
		
		// delete sequestrationdetail
		$delDetails = "DELETE FROM sequestrationdetail WHERE SID = $sid";
		mysql_query($delDetails);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
		
		// delete sequestration
		$delSequestration = "DELETE FROM sequestration WHERE SID = $sid";
		mysql_query($delSequestration);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
			
	
		global $sess_userid;
		
		lawstatus_delete($cid, $sid, LAWSTATUS_LOG_DOCUMENT_TYPE_SEQUESTRATION, $sess_userid);
		
		dbCommit();
		
		return $result;
	}
	
	/**
	 * @param $_POST $post
	 * @param $_FILES $files
	 * @param int $sid
	 * @param int $csid
	 * @return CancelSequestrationResult
	 */
	public function saveCancelledSequestration($post, $files, $sid, $csid, $cid){	
		
		$result = new CancelSequestrationResult();
		$result->IsComplete = true;
		
		$model = $this->getCancelSequestrationInput($sid, $csid, $cid, $post);
		try {			
			
			$attachments = fixFilesArray($files["Cancelledsequest_Docfile"]);
			$validateResult = $this->validateCancelSequestration($model, $attachments);
			
			if($validateResult->IsValid){
				global $hire_docfile_relate_path;
				$hireDocfileRelatePath = $hire_docfile_relate_path;
				if($csid == 0){
					$result = $this->insertCancelSequestration($model, $attachments, $hireDocfileRelatePath);					
				}else{					
					$result = $this->updateCancelSequestration($model, $attachments, $hireDocfileRelatePath);
				}	

				$model = $result->Data;
				
			}else{
				$result->IsComplete = false;
				$result->DupedKey = $validateResult->DupedKey;
				$result->DupedCSID = $validateResult->DupedCSID;
				$result->DupedSID = $validateResult->DupedSID;
			}
			
		} catch (Exception $ex) {
			$result->IsComplete = false;
			$result->Message = $ex;
			handleUnexpectedException($ex);
		}	
		$result->Data = $model;
		return $result;	
	}
	
	public function deleteCancelledSequestration($sid, $csid,  $cid, $post){
		$result = new CancelSequestrationResult();
		$result->IsComplete = true;
		$model = $this->getCancelSequestrationInput($sid, $csid,  $cid, $post);
		$result->Data = $model;
		try {
			
			if($model->CSID > 0){
				dbBegin();
				
				$delCancelledAttach = "DELETE FROM cancelledsequestttach WHERE CSID = $csid";
				mysql_query($delCancelledAttach);
				$error =  mysql_error();
				if($error != ""){
					$result->IsComplete = false;
					$result->Message = $error;
					error_log($error);
					dbRollback();
					return  $result;
				}
				
				$delAttach = "DELETE FROM files WHERE file_for = $csid  AND file_type = '$this->CANCELLED_FILE_TYPE'";
				mysql_query($delAttach);
				$error =  mysql_error();
				if($error != ""){
					$result->IsComplete = false;
					$result->Message = $error;
					error_log($error);
					dbRollback();
					return  $result;
				}
				
				$delCancelled = "DELETE FROM cancelledsequestration WHERE CSID = $csid";
				mysql_query($delCancelled);
				$error =  mysql_error();
				if($error != ""){
					$result->IsComplete = false;
					$result->Message = $error;
					error_log($error);
					dbRollback();
					return  $result;
				}
				
				global $sess_userid;				
				lawstatus_delete($model->CID, $csid, LAWSTATUS_LOG_DOCUMENT_TYPE_CANCELLED_SEQUESTRATION, $sess_userid);
				dbCommit();
				
				
			}else{
				$result->IsComplete = false;
				$result->Message = "ไม่พบข้อมูลการถอนอายัดที่ต้องการลบ";
			}
		}catch (Exception $ex) {
			$result->IsComplete = false;
			$result->Message = $ex;
			handleUnexpectedException($ex);
		}	
		return $result;
	}
	
	/**
	 * @param int $cid
	 * @param int $year
	 * @return BooleanResultMessage
	 */
	public function canCreateCompanySequestration($cid){
		global $LAWFUL_STATUS;
		global $COMPANY_LAW_STATUS;
	
		$hasRole = hasCreateRoleSequestrationByCID($cid);
		
		$result = new BooleanResultMessage();
		$result->IsComplete = true;
		$result->Data = null;
		
		if($hasRole){
			$sql = "SELECT l.Year
			FROM lawfulness l
			INNER JOIN company c ON l.CID = c.CID
			WHERE (l.CID = $cid) AND ((l.LawfulStatus = '0' or l.LawfulStatus is null) or (l.LawfulStatus = '2'))
			AND (c.LawStatus = $COMPANY_LAW_STATUS->แจ้งโนติส)
			ORDER BY l.Year limit 0, 1";
							
				
			$sqlResult = mysql_query($sql);
			$errorMsg = mysql_error();
			if($errorMsg == ""){
				$row = mysql_fetch_array($sqlResult);
				if($row){
					$year = $row["Year"];
					$compareReceivedDate = date('Y-m-d',strtotime(date("Y-m-d") . "-30 days"));
					
					$noticeSql = "SELECT n.NoticeID 
							FROM noticedocument n
							INNER JOIN company_lawstatus_log clog on n.NoticeID = clog.DocumentID
							WHERE (clog.ForYear = $year) AND (clog.DocumentType = '".LAWSTATUS_LOG_DOCUMENT_TYPE_NOTICE."')
									AND (clog.ChangeType = '".LAWSTATUS_LOG_CHANGE_TYPE_ADD."')
									AND (n.ReceivedDate <= '$compareReceivedDate')";
					
					$noticeResult = mysql_query($noticeSql);
 					if($noticeResult){
 						$result->Data = $row["Year"];
					}					
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
	private function saveSequestrationAttachment($files, $sid, $hireDocfileRelatePath){
		$fileFor = $this->SEQUESTRATION_FILE_TYPE;
		$result = new ResultMessage();
		$result->IsComplete = true;		
	
		
		if(!is_null($files)){
			
			$files = (is_array($files))? $files : array($files);
							
			for($i = 0; $i < count($files); $i++){
				$file = $files[$i];				
				
				$hireDocfileFileName = $file->name;
				$newHireDocfileName = date("dmyhis").rand(00,99)."_".$hireDocfileFileName; 
				$hireDocfilePath = $hireDocfileRelatePath.$newHireDocfileName;
				//$hireDocfilePath2 = $filename2 = iconv("UTF-8", "windows-874", $hireDocfilePath);
				$hireDocfilePath2 = $hireDocfilePath;
				
				
				if(move_uploaded_file($file->tmp_name, $hireDocfilePath2)){
					
					$newHireDocfileName = mysql_real_escape_string($newHireDocfileName);
					$insertFileSql = "INSERT INTO files(file_name, file_for, file_type)
									  VALUES('$newHireDocfileName', '$sid', '$fileFor')";
					mysql_query($insertFileSql);
					
					$mysql_error = mysql_error();
					if($mysql_error != ""){
						throw new Exception($mysql_error);
					}
					$fid = mysql_insert_id();	
					
					$insertSeqFileSql = "INSERT INTO sequestrationattachment(file_id, SID)
										 VALUES('$fid', '$sid')";
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
	
	private function saveCancelSequestrationAttachment($files, $csid, $hireDocfileRelatePath){
		$fileFor = $this->CANCELLED_FILE_TYPE;
		$result = new ResultMessage();
		$result->IsComplete = true;
		
		if(!is_null($files)){
				
			$files = (is_array($files))? $files : array($files);
				
			for($i = 0; $i < count($files); $i++){
				$file = $files[$i];
	
				$hireDocfileFileName = $file->name;
				$newHireDocfileName = date("dmyhis").rand(00,99)."_".$hireDocfileFileName;
				$hireDocfilePath = $hireDocfileRelatePath.$newHireDocfileName;
				//$hireDocfilePath2 = $filename2 = iconv("UTF-8", "windows-874", $hireDocfilePath);
				$hireDocfilePath2 = $hireDocfilePath;
	
	
				if(move_uploaded_file($file->tmp_name, $hireDocfilePath2)){
						
					$newHireDocfileName = mysql_real_escape_string($newHireDocfileName);
					$insertFileSql = "INSERT INTO files(file_name, file_for, file_type)
					VALUES('$newHireDocfileName', '$csid', '$fileFor')";				
					
					mysql_query($insertFileSql);
						
					$mysql_error = mysql_error();
					if($mysql_error != ""){
						throw new Exception($mysql_error);
					}
					$fid = mysql_insert_id();
						
					$insertSeqFileSql = "INSERT INTO cancelledsequestttach(file_id, CSID)
					VALUES('$fid', '$csid')";	
					
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
	 * @param $_POST $post
	 * @param $files : [{name = "", type = "", tmp_name = "", error = "", size = ""}]
	 * @return SequestrationResult
	 */
	private function insertSequestration($model, $files, $userfullname, $lawStatus, $hireDocfileRelatePath){
		global $sess_userid;
		$result = new SequestrationResult();
		$result->IsComplete = false;		
		
		$documentDateFormat = $model->DocumentDate;
		$userfullname = mysql_real_escape_string($userfullname);
		$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);
		$sequestrationDetail = mysql_real_escape_string($model->SequestrationDetail);
		
		$the_sql = "INSERT INTO sequestration(CID, DocumentDate, GovDocumentNo, SequestrationDetail, TotalAmount, CreatedDate, CreatedBy,CreatedByID )
					VALUES($model->CID, '$documentDateFormat', '$govDocumentNo', '$sequestrationDetail', $model->TotalAmount, NOW(), '$userfullname', $sess_userid)";
		try {
			dbBegin();
			
			mysql_query($the_sql);
			$error = mysql_error();
			if($error!= ""){
				throw new Exception($error);
			}
			$sid = mysql_insert_id();
			
			$this->insertSequestrationDetails($sid, $model->SequestrationMoneyDetails, $model->SequestrationPropertyDetails, $model->SequestrationCarDetails, $model->SequestrationOtherDetails);
			$this->insertSequestrationPayments($sid, $model->CompanyCode, $model->BranchCode, $model->SequestrationPayments);
			
			
			$saveFilesResult = $this->saveSequestrationAttachment($files, $sid, $hireDocfileRelatePath);
			if($saveFilesResult->IsComplete == false){
				throw new Exception($saveFilesResult->Message);
			}		
			
			global $sess_userid;
			lawstatus_add($model->CID, $sid, LAWSTATUS_LOG_DOCUMENT_TYPE_SEQUESTRATION, $sess_userid);
			
			dbCommit();
			
			$result->IsComplete = true;
			$model->SID = $sid;
			
			
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
	 * @param Sequestration $model
	 * @param $files : [{name = "", type = "", tmp_name = "", error = "", size = ""}]
	 * @param string $username
	 * @param int $lawStatus
	 * @return SequestrationResult
	 */
	private function updateSequestration($model, $files, $userfullname, $lawStatus, $hireDocfileRelatePath){
		global $sess_userid;
		$result = new SequestrationResult();
		$result->IsComplete = false;
		
		$sid = $model->SID;
		$documentDateFormat = $model->DocumentDate;
		$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);
		$sequestrationDetail = mysql_real_escape_string($model->SequestrationDetail);
		
		$the_sql = "UPDATE sequestration SET 
						GovDocumentNo = '$govDocumentNo',
						DocumentDate = '$documentDateFormat',
						TotalAmount = $model->TotalAmount,
						SequestrationDetail = '$sequestrationDetail', 
						ModifiedDate = NOW(),
						ModifiedBy = '$userfullname',
						ModifiedByID = $sess_userid
					WHERE SID = $sid
					";
		
		try{
			dbBegin();
			mysql_query($the_sql);			
				
			$this->insertSequestrationDetails($sid, $model->SequestrationMoneyDetails, $model->SequestrationPropertyDetails, $model->SequestrationCarDetails, $model->SequestrationOtherDetails);
			$this->insertSequestrationPayments($sid, $model->CompanyCode, $model->BranchCode, $model->SequestrationPayments);
			
			$saveFilesResult = $this->saveSequestrationAttachment($files, $sid, $hireDocfileRelatePath);
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

	

	private function insertSequestrationDetails($sid, array $moneyDetails = null, array $propertyDetails = null, array $carDetails = null, array $otherDetails = null){		
		$sqlDel = "DELETE FROM sequestrationdetail WHERE SID = $sid";
		mysql_query($sqlDel);
		
		$details = array();
		if($moneyDetails != null){
			$details = array_merge($details, $moneyDetails);
		}
		
		if($propertyDetails != null){
			$details = array_merge($details, $propertyDetails);
		}
		
		if($carDetails != null){
			$details = array_merge($details, $carDetails);
		}
		
		if($otherDetails != null){
			$details = array_merge($details, $otherDetails);
		}
		
// 		if(($moneyDetails != null) && ($propertyDetails != null) && ($carDetails != null) && ($otherDetails != null)){
// 			$details = array_merge($moneyDetails, $propertyDetails);
// 		}else if($moneyDetails != null){
// 			$details = $moneyDetails;
// 		}else if($propertyDetails != null){
// 			$details = $propertyDetails;
// 		}
		
		for ($i = 0; $i < count($details); $i++){
			$item = $details[$i];
			$sequestrationType = $item->SequestrationType;
			$documentNo = !empty($item->DocumentNo) ? "'".mysql_real_escape_string($item->DocumentNo)."'" : "NULL";
			$accountType = !empty($item->AccountType) ? "'".mysql_real_escape_string($item->AccountType)."'" : "NULL";
			$bankID = !empty($item->BankID) ? "'".mysql_real_escape_string($item->BankID)."'" : "NULL";
			$bankBranchName = !empty($item->BankBranchName) ? "'".mysql_real_escape_string($item->BankBranchName)."'" : "NULL";
			$provinceCode = !empty($item->ProvinceCode) ? "'".mysql_real_escape_string($item->ProvinceCode)."'" : "NULL";
			$districtCode = !empty($item->DistrictCode) ? "'".mysql_real_escape_string($item->DistrictCode)."'" : "NULL";
			$subDistrictCode = !empty($item->SubDistrictCode) ? "'".mysql_real_escape_string($item->SubDistrictCode)."'" : "NULL";
			$carYear = !(empty($item->CarYear))? $item->CarYear : "NULL";
			$other = !empty($item->Other) ? "'".mysql_real_escape_string($item->Other)."'" : "NULL";
			
			$sqlInsert = "INSERT INTO sequestrationdetail(SID, SequestrationType, DocumentNo, AccountType, bank_id, bank_branchname, province_code, district_code, subdistrict_code, CarYear, Other) 
					      VALUES($sid,$sequestrationType, $documentNo, $accountType, $bankID, $bankBranchName, $provinceCode, $districtCode, $subDistrictCode, $carYear, $other)";
			
			mysql_query($sqlInsert);
			if(mysql_error() != ""){
				throw new Exception(mysql_error());
			}
		}
	}
	
	private function insertSequestrationPayments($sid, $companyCode, $branchCode, array $payments){	
		$sqlDel = "DELETE FROM sequestrationpayment WHERE SID = $sid";
		global $DEBT_INTEREST_RATE;
		mysql_query($sqlDel);		
		
		$lidList = $this->getLID($companyCode, $branchCode);
		
		for ($i = 0; $i < count($payments); $i++){
			$item = $payments[$i];
			$lid = $lidList[$item->Year];
			$interestAmount = $item->InterestAmount;			
			$interestRate = (($item->Year > 2011) || ($interestAmount > 0))? $DEBT_INTEREST_RATE : 0;
			$sqlInsert = "INSERT INTO sequestrationpayment(LID, SID, PrincipleAmount, InterestAmount, InterestRate, TotalAmount, InterestPerDay)
								VALUES($lid, $sid, $item->PrincipleAmount, $interestAmount, $interestRate, $item->TotalAmount, $item->InterestPerDay)";
			
			mysql_query($sqlInsert);
			if(mysql_error() != ""){
				throw new Exception(mysql_error());
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
	 * @param int $cid
	 * @return <NULL, string(Y-m-d)>
	 */
	private function getNoticeReceivedDate($sid, $cid){
		$receivedDate = null;
		$seqSql = "SELECT ForYear FROM company_lawstatus_log WHERE (DocumentID = $sid) 
			AND (DocumentType = '".LAWSTATUS_LOG_DOCUMENT_TYPE_SEQUESTRATION."')
			AND (ChangeType = '".LAWSTATUS_LOG_CHANGE_TYPE_ADD."')
			AND (CID = $cid)";
		
		$seqResult = mysql_query($seqSql);
		if($seqResult){
			$seqRow = mysql_fetch_array($seqResult);
			$seqForYear = $seqRow["ForYear"];
			
			$noticeSql = "SELECT n.ReceivedDate
				FROM noticedocument n
				INNER JOIN company_lawstatus_log clog on n.NoticeID = clog.DocumentID 
						AND (clog.DocumentType = '".LAWSTATUS_LOG_DOCUMENT_TYPE_NOTICE."')
						AND (clog.ChangeType = '".LAWSTATUS_LOG_CHANGE_TYPE_ADD."')
				WHERE (clog.ForYear = $seqForYear) 
					AND (clog.CID = $cid) AND (n.CID = $cid)";
			
			$noticeResult = mysql_query($noticeSql);
			if($noticeResult){
				$noticeRow = mysql_fetch_array($noticeResult);
				$receivedDate = ($noticeRow["ReceivedDate"] != null)? new DateTime($noticeRow["ReceivedDate"]) : null;
			}
		}
		$receivedDateFormat = ($receivedDate != null)? $receivedDate->format("Y-m-d") : null;
		
		return $receivedDateFormat;		
	}
	
	/**
	 * วันที่เริ่มคิดดอกเบี้ยปี 2554 (30 วันตั้งแต่วันที่รับแจ้งโนติส)
	 * @param string $noticeReceivedDate
	 * @return <NULL, string("Y-m-d")>
	 */
	private function getStart2011InterestDate($noticeReceivedDate){
		$dateFormate = null;
		if($noticeReceivedDate != null){
			$recivedDate = new DateTime($noticeReceivedDate);
			$recivedDate->add(new DateInterval('P30D'));
			$dateFormate = $recivedDate->format("Y-m-d");
			
		}
		
		return $dateFormate;
	}
	
	/**
	 * 
	 * @param CancelledSequestration $model
	 * @return CancelSequestrationResult
	 */
	private function insertCancelSequestration($model, $files, $hireDocfileRelatePath){
		global $sess_userid;
		$result = new CancelSequestrationResult();
		$result->IsComplete = true;
		dbBegin();
		try {
			global $sess_userfullname;
			$createdBy = mysql_real_escape_string($sess_userfullname);
			$requestDate = $model->RequestDate->format("Y-m-d");
			$requestNo = mysql_real_escape_string($model->RequestNo);
			$detail = mysql_real_escape_string($model->CancelledDetail);
			
			$sql = "INSERT INTO cancelledsequestration(SID, RequestNo, RequestDate, CancelledDetail, CreatedDate, CreatedBy, CreatedByID)
					VALUES($model->SID, '$requestNo', '$requestDate', '$detail', NOW(), '$createdBy', $sess_userid)";
					
			mysql_query($sql);
			$error = mysql_error();
			if($error != ""){
				throw  new Exception($error);
			}
			$csid = mysql_insert_id();
			$model->CSID = $csid;
			$this->saveCancelSequestrationAttachment($files, $csid, $hireDocfileRelatePath);
			
			global $sess_userid;
			
			lawstatus_add($model->CID, $csid, LAWSTATUS_LOG_DOCUMENT_TYPE_CANCELLED_SEQUESTRATION, $sess_userid);
			dbCommit();
			
		} catch (Exception $ex) {
			dbRollback();
			$result->IsComplete = false;
			$result->Message = $ex->getMessage();
			handleUnexpectedException($ex);
		}
		$result->Data = $model;
		return $result;
	}
	
	/**
	 * 
	 * @param CancelledSequestration $model
	 * @param $_FILES $files
	 * @param string $hireDocfileRelatePath
	 * @throws Exception
	 * @return CancelSequestrationResult
	 */
	private function updateCancelSequestration($model, $files, $hireDocfileRelatePath){
		global $sess_userid;
		$result = new CancelSequestrationResult();
		$result->IsComplete = true;
		dbBegin();
		try {
			global $sess_userfullname;
			$csid = $model->CSID;
			$modifiedBy = mysql_real_escape_string($sess_userfullname);
			$requestDate = $model->RequestDate->format("Y-m-d");
			$requestNo = mysql_real_escape_string($model->RequestNo);
			$detail = mysql_real_escape_string($model->CancelledDetail);
				
			$sql = "UPDATE cancelledsequestration SET
						RequestNo = '$requestNo',
						RequestDate = '$requestDate',
						CancelledDetail = '$detail',
						ModifiedDate = NOW(), 
						ModifiedBy = '$modifiedBy',
						ModifiedByID = $sess_userid
				    WHERE CSID = $csid";		
			
			mysql_query($sql);
			$error = mysql_error();
			if($error != ""){
				throw  new Exception($error);
			}
			
			$this->saveCancelSequestrationAttachment($files, $csid, $hireDocfileRelatePath);
			dbCommit();
			
			$result->IsComplete = true;
		} catch (Exception $ex) {
			dbRollback();
			$result->IsComplete = false;
			$result->Message = $ex->getMessage();
			handleUnexpectedException($ex);
		}
		$result->Data = $model;
		return $result;
	}
	
	/**
	 * @param int $sid
	 * @return SequestrationPayment[]
	 */
	private function getSequestrationPayments($sid){
		$sql = "SELECT sPayment.SPID, sPayment.SID, sPayment.LID, 
				sPayment.PrincipleAmount,  sPayment.InterestAmount, sPayment.InterestRate, sPayment.TotalAmount, sPayment.InterestPerDay,
				law.Year
				FROM sequestrationpayment sPayment
				INNER JOIN lawfulness law ON sPayment.LID = law.LID
				WHERE SID = $sid  ORDER BY law.Year";
		$sqlResult = mysql_query($sql);
		$payments = array();
		while ($payment = mysql_fetch_object($sqlResult, "SequestrationPayment")) {
			array_push($payments, $payment);
		}
		mysql_free_result($sqlResult);
		return (count($payments) > 0)? $payments : null;
	}
	
	private function getSequestrationDetails($sid, $sequestrationType){		
		$sql = "SELECT '' AS UID ,seqD.SDID, seqD.SID, seqD.AccountType , '' AS AccountTypeName, seqD.DocumentNo, seqD.SequestrationType,
				seqD.bank_id AS BankID, b.bank_name AS BankName, seqD.bank_branchname AS BankBranchName,
				seqD.province_code AS ProvinceCode ,prov.province_name AS ProvinceName,
				seqD.district_code AS DistritctCode ,dist.district_name AS DistrictName,
				seqD.subdistrict_code AS SubDistritctCode ,sDist.subdistrict_name AS SubDistrictName,
				seqD.CarYear, seqD.Other
				FROM sequestrationdetail seqD
				LEFT JOIN bank b ON seqD.bank_id = b.bank_id
				LEFT JOIN provinces prov ON seqD.province_code = prov.province_code
				LEFT JOIN districts dist ON (seqD.district_code = dist.district_code) AND (seqD.province_code = dist.province_code)
				LEFT JOIN subdistrict sDist ON (seqD.subdistrict_code = sDist.subdistrict_code) 
								AND  (seqD.district_code = sDist.district_code) AND (seqD.province_code = sDist.province_code) 
				WHERE (seqD.SID = $sid) AND (seqD.SequestrationType = $sequestrationType)";
		$sqlResult = mysql_query($sql);
		
		$details = array();
		$accountTypes = getAcountTypeMapping();
		
		while ($detail = mysql_fetch_object($sqlResult, "SequestrationDetail")) {
			$accountTypeName = $accountTypes[$detail->AccountType];
			$detail->AccountTypeName = $accountTypeName;
			array_push($details, $detail);
		}
		mysql_free_result($sqlResult);
		
		return (count($details) > 0 ? $details : null);
	}
	
	/**
	 * @param Sequestration $model
	 * @param array of file : [{name = "", type = "", tmp_name = "", error = "", size = ""}] 
	 * @param string $theCompanyWord
	 * @return SequestrationValidationResult $result
	 */
	private function validateHolding($model, $files, $theCompanyWord){
		$result = new SequestrationValidationResult();
		$result->IsValid = true;
	
		$errorMessage = "";
		$sid = $model->SID;
	
	
		if(!empty($model->GovDocumentNo)){
			$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);
			$sqlChkDup = "SELECT count(*), SID FROM sequestration WHERE GovDocumentNo ='$govDocumentNo' AND SID <> $sid";
			$sqlChkDupResult = mysql_query($sqlChkDup);
			if(mysql_error() != ""){
				error_log(mysql_error());
			}
			$row = mysql_fetch_row($sqlChkDupResult);
			$count = $row[0];
	
			if($count > 0){
				$result->IsValid = false;
				$result->DupedKey = true;
				$result->DupedSID = $row[1];
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
	
		if(is_null($model->SequestrationMoneyDetails) && is_null($model->SequestrationPropertyDetails) &&  is_null($model->SequestrationCarDetails) && is_null($model->SequestrationOtherDetails) ){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: รายละเอียดประเภทการอายัด\n";
		}
	
		if(is_null($model->SequestrationPayments)){
			$result->IsValid = false;
			$errorMessage .="กรุณาเลือก: ".$theCompanyWord."ที่ต้องการแจ้งอายัด\n";
		}
		
		$fileTypeIsValid = checkAllowFileUpload($files);
		if(!$fileTypeIsValid){
			$result->IsValid = false;
			$errorMessage .="ชนิดไฟล์แนบไม่ถูกต้อง\n";
		}
	
		$result->Message = $errorMessage;
		return $result;
	}
	/**
	 * @param CancelledSequestration $model
	 * @param $_FILES $files
	 * @return CancelSequestrationValidationResult
	 */
	private function validateCancelSequestration($model, $files){
		$result = new CancelSequestrationValidationResult();
		$result->IsValid = true;
		$id = (!is_null($model->CSID))? $model->CSID : 0;
		
		if(!empty($model->RequestNo)){
			$RequestNo = mysql_real_escape_string($model->RequestNo);
			$sqlChkDup = "SELECT count(*), CSID, SID FROM cancelledsequestration WHERE RequestNo ='$RequestNo' AND CSID <> $id";
			$sqlChkDupResult = mysql_query($sqlChkDup);		
			
			$error = mysql_error();
			if($error != ""){
				error_log($error);
			}
			$row = mysql_fetch_row($sqlChkDupResult);
			$count = $row[0];
		
			if($count > 0){
				$result->IsValid = false;
				$result->DupedKey = true;
				$result->DupedCSID = $row[1];
				$result->DupedSID = $row[2];
			}
		}
		
		if(empty($model->RequestNo)){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: เลขที่หนังสือ\n";
		}
		
		if(is_null($model->RequestDate)){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: วันที่ถอนอายัด\n";
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


class SequestrationPayment{
	/***
	 * 
	 * @var int
	 */
	public $SPID;
	/***
	 *
	 * @var int
	 */
	public $SID;
	/***
	 *
	 * @var int
	 */
	public $LID;
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


class SequestrationDetail{
	/**
	 *
	 * @var string
	 */
	public $UID;
	/**
	 * 
	 * @var int
	 */
	public $SDID;
	/**
	 *
	 * @var int
	 */
	public $SID;
	/**
	 *
	 * @var int
	 */
	public $AccountType;
	/**
	 *
	 * @var string
	 */
	public $AccountTypeName;
	/**
	 *
	 * @var int
	 */
	public $BankID;
	/**
	 *
	 * @var string
	 */
	public $BankName;
	
	/**
	 *
	 * @var string
	 */
	public $BankBranchName;
	
	/**
	 * สมุดบัญชี/เลขที่โฉนด/ทะเบียนรถยนต์
	 * @var string
	 */
	public $DocumentNo;
	/**
	 *
	 * @var string
	 */
	public $SequestrationType;
	/**
	 *
	 * @var string
	 */
	public $ProvinceCode;	
	/**
	 *
	 * @var string
	 */
	public $ProvinceName;
	/**
	 *
	 * @var string
	 */
	public $DistrictCode;
	/**
	 *
	 * @var string
	 */
	public $DistrictName;
	
	/**
	 *
	 * @var string
	 */
	public $SubDistrictCode;
	/**
	 *
	 * @var string
	 */
	public $SubDistrictName;
	/**
	 * ปีรถยนต์
	 * @var int
	 */
	public $CarYear;
	/**
	 * ทรัพย์สินประเภทอื่นๆ
	 * @var string
	 */
	public $Other;
}


class SequestrationList{
	/**
	 *
	 * @var int
	 */
	public $SID;
	/**
	 *
	 * @var int
	 */
	public $CSID;
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
	 * @var double
	 */
	public $TotalAmount;
}

class Sequestration{
	/**
	 * 
	 * @var int
	 */
	public $SID;
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
	 * วันที่รับแจ้งโนติส
	 * @var DateTime
	 */
	public $NoticeDate;
	/**
	 * วันที่เริ่มคิดดอกเบี้ยปี 2011 (นับ 30 วัน ตั้งแต่วันที่รับแจ้งโนติส)
	 * @var DateTime
	 */
	public $Start2011InterestDate;
	/**
	 *
	 * @var string
	 */
	public $GovDocumentNo;
	/**
	 *
	 * @var string
	 */
	public $SequestrationDetail;
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
	/**
	 * รหัสการถอนอายัด
	 * @var int
	 */
	public $CSID;
	/**
	 *
	 * @var SequestrationDetail[]
	 */
	public $SequestrationMoneyDetails;
	
	/**
	 *
	 * @var SequestrationDetail[]
	 */
	public $SequestrationPropertyDetails;
	/**
	 *
	 * @var SequestrationDetail[]
	 */
	public $SequestrationCarDetails;
	/**
	 *
	 * @var SequestrationDetail[]
	 */
	public $SequestrationOtherDetails;
	/**
	 *
	 * @var SequestrationPayment[]
	 */
	public $SequestrationPayments;
	
}

class CancelledSequestration{
	/**
	 *
	 * @var int
	 */
	public $CSID;
	/**
	 *
	 * @var int
	 */
	public $SID;
	/**
	 *
	 * @var int
	 */
	public $CID;
	/**
	 * 
	 * @var string
	 */
	public $CancelledDetail;
	/**
	 *
	 * @var string
	 */
	public $CreatedBy;
	/**
	 *
	 * @var DateTime
	 */
	public $CreatedDate;
	
	/**
	 * วันที่
	 * @var Datetime
	 */
	public $RequestDate;
	/**
	 * เลขที่หนังสือ
	 * @var string
	 */
	public $RequestNo;
	
}

class SequestrationResult extends ResultMessage{
	/**
	 * @var Sequestration
	 */
	public $Data;
	
	/**
	 * @var bool
	 */
	public $DupedKey;
	
	/**
	 * @var int
	 */
	public $DupedSID;
}

class SequestrationListResult extends ResultMessage{
	/**
	 * @var SequestrationList
	 */
	public $Data;
}
class SequestrationValidationResult extends ResultValidation{
	/**
	 * @var bool
	 */
	public $DupedKey;
	
	/**
	 * @var int
	 */
	public $DupedSID;	
	
}


class CancelSequestrationResult extends ResultMessage{
	/**
	 * 
	 * @var CancelledSequestration
	 */
	public $Data;
	/**
	 * @var bool
	 */
	public $DupedKey;

	/**
	 * @var int
	 */
	public $DupedCSID;
	/**
	 * @var int
	 */
	public $DupedSID;
}

class CancelSequestrationValidationResult extends ResultValidation{
	/**
	 * @var bool
	 */
	public $DupedKey;

	/**
	 * @var int
	 */
	public $DupedCSID;
	
	/**
	 * @var int
	 */
	public $DupedSID;

}



?>