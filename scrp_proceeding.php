<?php
require_once 'db_connect.php';
require_once 'c2x_model.php';
require_once 'c2x_function.php';
require_once 'scrp_config.php';
require_once 'lawstatus_log.php';

class ManageProceeding {
	public $PROCEEDINGS_FILE_TYPE = "Proceedings_docfile";
    public function saveProceeding($post, $files, $pid){
		global $the_company_word;
		global $hire_docfile_relate_path;
		global $sess_userfullname;
		$dupedKey = false;	
		$dupedSID = 0;
		$saveResult = new ProceedingResult();
		$saveResult->IsComplete = false;
		
		
		try{
			$model = $this->getProceedingInput($pid, $_POST);
			$validateResult = $this->validateProceeds($model, $the_company_word);
			
			if($validateResult->IsValid){		
				$attachments = fixFilesArray($files["Proceedings_Docfile"]);
				
				if($pid == 0){
					$saveResult = $this->insertProceeding($model, $attachments, $sess_userfullname, $hire_docfile_relate_path);
				}else{
					$saveResult = $this->updateProceeding($model,  $attachments, $sess_userfullname, $hire_docfile_relate_path);				
				}
			}else{
				$saveResult->DupedKey = $validateResult->DupedKey;
				$saveResult->DupedPID = $validateResult->DupedPID;
				$saveResult->Data = $model;
				$saveResult->Message = $validateResult->Message;
			}	
			
			
		}catch(Exception $ex){
			$saveResult->Message = $ex->getMessage();
		}			
		
		return $saveResult;		
	}
	
    /**
	 * @param int $pid
	 * @param object $seqType
	 * @return ProceedingResult
	 */
	public function getProceeding($pid){
		$result = new ProceedingResult();
		
		$sql = "SELECT p.PID, c.CID, c.CompanyCode, c.BranchCode, c.CompanyNameThai AS CompanyName, c.CompanyTypeCode,
		p.RequestDate, p.GovDocumentNo, p.Detail, p.TotalAmount, p.ProDate, p.PType, p.Status, p.DueDay, p.OtherExpense, p.FeeCourt
		FROM company c
		INNER JOIN proceedings p ON c.CID = p.CID
		WHERE p.PID = $pid";
		//echo($sql);
		$sqlResult = mysql_query($sql);
		$proceeding = mysql_fetch_object($sqlResult, "Proceeding");
		
		try {
			error_log('----');
			if($proceeding){
				$result->IsComplete = true;
				$proceeding->CurrentPType = $proceeding->PType;				
				$proceeding->CalDate = (isset($proceeding->ProDate) ? new DateTime($proceeding->ProDate) : new DateTime());
				$proceeding->NoticeReceivedDate = $this->getNoticeReceivedDate($pid, $proceeding->CID);
				$proceeding->Start2011InterestDate = $this->getStart2011InterestDate($proceeding->NoticeReceivedDate);
				
				$proceeding->ProceedingPayments = $this->getProceedingPayments($pid);
				if(is_null($proceeding->DueDay)){
					$proceeding->DueDay = 30;
				}
				
				
				$result->Data = $proceeding;
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
	 * @return ProceedingListResult
	 */
	public function getProceedingList($cid){
		$result = new ProceedingListResult();
	
		$sql = "SELECT p.PID, p.CID, p.RequestDate,  p.GovDocumentNo, p.TotalAmount, p.OtherExpense, p.ProDate, p.PType,
					CASE  WHEN pa.ACount is null THEN 0 ELSE 1 END as HasAttachment
				FROM proceedings p
				LEFT JOIN (
				    SELECT PID, count(*) AS ACount FROM proceedingsattachment  GROUP BY PID
				)pa ON p.PID = pa.PID
				WHERE p.CID = $cid";		
		
		try{
			$sqlResult = mysql_query($sql);
			$error = mysql_error();	
				
			if($error == ""){
				$result->IsComplete = true;
				
				$dataItems = array();
				while ($row = mysql_fetch_object($sqlResult, "ProceedingList")){
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
	 * @param int $pid
	 * @param $_POST $post
	 * @return Sequestration
	 */
	public function getProceedingInput($pid, $post){
		global $PROCEEDING_TYPE;
		$proceedsChargeType = $PROCEEDING_TYPE->ศาลสั่งฟ้อง;
		$model = new Proceeding();
		
		$model->PID = $pid;
	
		$text = $post["HiddCID"];
		$model->CID = $text;
	
		$text = $post["HiddCompanyCode"];
		$model->CompanyCode = $text;
        
		$text = $post["HiddBranchCode"];
		$model->BranchCode = $text;
		
		$text = $post["CalDate"];
		$model->CalDate = ($text != "")? new DateTime($text) : null;
        
		$model->PType = $post["ProceedingType"];
		$model->CurrentPType = $post["CurrentPType"];
        	
		$day = $post["RequestDate_day"];
		$month = $post["RequestDate_month"];
		$year = $post["RequestDate_year"];
		
		if(($day != "00") && ($month != "00") && ($year != "0000")){
			$requestDate = $year."-".$month."-".$day;
			$model->RequestDate = new DateTime($requestDate);
		}else{
			$model->RequestDate = null;
		}	
        
		$text = $post["GovDocumentNo"];		
		$model->GovDocumentNo = trim($text);
			
		$text = $post["TotalAmount"];
		$totalAmount = (float)$text;
		$model->TotalAmount = round($totalAmount, 2);
        
		$text = $post["Detail"];
		$model->Detail = trim($text);
		
		$text = $post["CompanyName"];
		$model->CompanyName = $text;
		
		$text = $post["CompanyTypeCode"];
		$model->CompanyTypeCode = $text;
        
		if($model->PType == $proceedsChargeType){
			$pday = $post["ProDate_day"];
			$pmonth = $post["ProDate_month"];
			$pyear = $post["ProDate_year"];
			
			if(($pday != "00") && ($pmonth != "00") && ($pyear != "0000")){
				$proDate = $pyear."-".$pmonth."-".$pday;
				$model->ProDate = new DateTime($proDate);
			}else{
				$model->ProDate = null;
			}
			
			$text = $post["DueDay"];
			$text = trim($text);
			$model->DueDay = is_numeric($text)? floatval($text) : null;
			
			$text = $post["OtherExpense"];
			$text = trim($text);
			$model->OtherExpense = is_numeric($text)? floatval($text) : null;
			
			$text = $post["FeeCourt"];
			$model->FeeCourt = is_numeric($text)? floatval($text) : null;
		}	
		
		$text = $post["NoticeReceivedDate"];
		$model->NoticeReceivedDate = (empty($text))? null : $text;
		
		$text = $post["Start2011InterestDate"];
		$model->Start2011InterestDate = (empty($text))? null : $text;
		
		$proceedingPayments = (!empty($post["ProceedingPayments"]))? json_decode($post["ProceedingPayments"]) : null;
		$model->ProceedingPayments = $proceedingPayments;

		return $model;
	}
    
	/**
	 * @param int $pid
	 * @param $_POST $post
	 * @return SequestrationResult
	 */
	public function deleteProceeding($pid, $post){
		$result = new ProceedingResult();
		$result->IsComplete = true;
		$model = $this->getProceedingInput($pid, $post);
		$result->Data = $model;
		
		$delResult = $this->deleteProceedingWithID($pid, $model->CID);		
		$result->IsComplete = $delResult->IsComplete;
		$result->Message = $delResult->Message;					
		
		return  $result;
	}  
    
	public function deleteProceedingWithID($pid, $cid){
		$result = new ResultMessage();
		$result->IsComplete = true;
		
		dbBegin();
		$delProceedsAttach = "DELETE FROM proceedingsattachment WHERE PID = $pid";
		mysql_query($delProceedsAttach);
		$error = mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
		
		$delProceedsFile = "DELETE FROM files WHERE (file_for = $pid) AND (file_type = '$this->PROCEEDINGS_FILE_TYPE')";
		mysql_query($delProceedsFile);
		$error = mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
		
		$delProceedsPayment = "DELETE FROM proceedingpayment WHERE PID = $pid";
		mysql_query($delProceedsPayment);
		$error = mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}
		
		$delProceeds = "DELETE FROM proceedings WHERE PID = $pid";
		mysql_query($delProceeds);
		$error = mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			error_log($error);
			dbRollback();
			return  $result;
		}		
		
		global $sess_userid;
		lawstatus_delete($cid, $pid, LAWSTATUS_LOG_DOCUMENT_TYPE_PROCEEDING, $sess_userid);
		
		dbCommit();
		
		return $result;
	}
	
	public function canCreateCompanyProceeds($cid){
		global $LAWFUL_STATUS;
		
		$result = new ProceedingYearResult();
		$result->IsComplete = true;
		$result->Data = false;
		$hasRole = hasCreateRoleSequestrationByCID($cid);
		
		if($hasRole){
			$sql = "SELECT l.Year
			FROM lawfulness l
			INNER JOIN company c ON l.CID = c.CID
			WHERE (l.CID = $cid) AND ((l.LawfulStatus = '0' or l.LawfulStatus is null) or (l.LawfulStatus = '2'))			
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
	 * @param int $cid
	 * @return ProceedingsAttachmentResult
	 */
	public function getAttachmentListByCID($cid){
		$result = new ProceedingsAttachmentResult();
		
		$sql = "SELECT p.PID, p.CID, pa.file_id AS FileID, f.file_name AS FileName, f.file_type AS FileType 
				FROM proceedings p
				INNER JOIN proceedingsattachment pa on p.PID = pa.PID
				INNER JOIN files f on pa.file_id = f.file_id
				WHERE p.CID = $cid";
		error_log($sql);
		$sqlResult = mysql_query($sql);
		$error = mysql_error();
		if($error == ""){
			$result->IsComplete = true;
			$dataItems = array();
			while ($obj = mysql_fetch_object($sqlResult, "ProceedingsAttachment")){
				array_push($dataItems, $obj);
			}
			$result->Data = (count($dataItems) > 0? $dataItems : null);
		}else{
			$result->IsComplete = false;
			$result->Message = $error;
		}
		
		return $result;
	}
	
	/**
	 * 
	 * @param Proceeding $model
	 * @param array $files
	 * @param string $userfullname
	 * @throws Exception
	 * @return ProceedingResult
	 */
	private function insertProceeding($model, $files, $userfullname, $hireDocfileRelatePath){		
		global $sess_userid;	
		$result = new ProceedingResult();
		$result->IsComplete = false;		
		$username = mysql_real_escape_string($userfullname);
		$requestDateFormat = $model->RequestDate->format("Y-m-d");		
		$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);
		$detail = mysql_real_escape_string($model->Detail);
		$totalAmount = (is_null($model->TotalAmount))? "NULL" : $model->TotalAmount ;
		
		$the_sql = "INSERT INTO proceedings(CID, PType, Status, RequestDate, GovDocumentNo, TotalAmount, Detail, ProDate, DueDay, OtherExpense, CreatedDate, CreatedBy, CreatedByID)
					VALUES($model->CID, $model->PType, 1, '$requestDateFormat', '$govDocumentNo', $totalAmount, '$detail', NULL, NULL, NULL, NOW(), '$username', $sess_userid)";
		try {
			dbBegin();
			
			mysql_query($the_sql);
			$error = mysql_error();
			if($error != ""){
				throw new Exception($error);
			}
			$pid = mysql_insert_id();
			
			$this->insertProceedingPayments($pid, $model->CompanyCode, $model->BranchCode, $model->ProceedingPayments);
			$this->saveAttachment($files, $pid, $hireDocfileRelatePath);
			$this->updateCompanyInfo($model->CID, $model);
			
			global $sess_userid;			
			lawstatus_add($model->CID, $pid, LAWSTATUS_LOG_DOCUMENT_TYPE_PROCEEDING, $sess_userid);
			
			dbCommit();
			
			$result->IsComplete = true;
			$model->PID = $pid;
			
			
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
	 * 
	 * @param Proceeding $model
	 * @param array $files
	 * @param sting $userfullname
	 * @param string $hireDocfileRelatePath
	 * @return ProceedingResult
	 */
	private function updateProceeding($model, $files, $userfullname, $hireDocfileRelatePath){
		global $sess_userid;
		global $PROCEEDING_TYPE;
		$proceedsChargeType = $PROCEEDING_TYPE->ศาลสั่งฟ้อง;
		 
		$result = new ProceedingResult();
		$result->IsComplete = false;
		
		$pid = $model->PID;
		$userfullname = mysql_real_escape_string($userfullname);
		$requestDateFormat = $model->RequestDate->format("Y-m-d");
		$proDateFormat = (is_null($model->ProDate))? "NULL" : "'".$model->ProDate->format("Y-m-d")."'";
		$dueDay = (is_null($model->DueDay))? "NULL" : $model->DueDay;
		$otherExpense = (is_null($model->OtherExpense))? "NULL" : $model->OtherExpense;
		$feeCourt= (is_null($model->FeeCourt))? "NULL" : $model->FeeCourt;
		$totalAmount = (is_null($model->TotalAmount))? "NULL" : $model->TotalAmount;
		$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);
		$proceedingDetail = mysql_real_escape_string($model->Detail);
		
		$the_sql = "UPDATE proceedings SET 
						GovDocumentNo = '$govDocumentNo',
						RequestDate = '$requestDateFormat',
						TotalAmount = $totalAmount,
						Detail = '$proceedingDetail', 
                        ProDate = $proDateFormat,
                        PType = $model->PType,
                        DueDay = $dueDay,
                        OtherExpense = $otherExpense,
                        FeeCourt = $feeCourt,
						ModifiedDate = NOW(),
						ModifiedBy = '$userfullname',
						ModifiedByID = $sess_userid
					WHERE PID = $pid";		
		try{
			dbBegin();
			mysql_query($the_sql);			
			
				
			$this->insertProceedingPayments($pid, $model->CompanyCode, $model->BranchCode, $model->ProceedingPayments);
			$this->saveAttachment($files, $pid, $hireDocfileRelatePath);		
			$this->updateCompanyInfo($model->CID, $model);
			
			global $sess_userid;
			lawstatus_add($model->CID, $pid, LAWSTATUS_LOG_DOCUMENT_TYPE_PROCEEDING, $sess_userid);
		
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
	
	private function clearProceedingPayments($pid){
		$sqlDel = "DELETE FROM proceedingpayment WHERE PID = $pid";
		mysql_query($sqlDel);
	}
    
	private function insertProceedingPayments($pid, $companyCode, $branchCode, array $payments){
		global $DEBT_INTEREST_RATE;
		$this->clearProceedingPayments($pid);	
		
		$lidList = $this->getLID($companyCode, $branchCode); 
		
		for ($i = 0; $i < count($payments); $i++){
			$item = $payments[$i];
			$lid = $lidList[$item->Year];
			$interestAmount = $item->InterestAmount;	
			$interestRate = (($item->Year > 2011) || ($interestAmount > 0))? $DEBT_INTEREST_RATE : 0;
			
			$sqlInsert = "INSERT INTO proceedingpayment(PID, LID, TotalAmount, InterestAmount, PrincipleAmount, InterestRate, InterestPerDay)
								VALUES($pid, $lid, $item->TotalAmount, $interestAmount, $item->PrincipleAmount, $interestRate, $item->InterestPerDay)";
			
			mysql_query($sqlInsert);
			$error = mysql_error();
			
			if($error != ""){
				throw new Exception($error);
			}			
		}
	}
	
	
	
	/**
	 * @param Proceeding $model
	 */
	private function updateCompanyInfo($cid, $model){
		global $PROCEEDING_TYPE;
		$proceedsChargeType = $PROCEEDING_TYPE->ศาลสั่งฟ้อง;
		global $sess_userid;
		
		$proDateFormat = "NULL";
		$dueDay = "NULL";
		$otherExpense = "NULL";
		$feeCourt = "NULL";
		
		if(($model != null) && ($model->PType == $proceedsChargeType)){
			$proDateFormat = "'".$model->ProDate->format("Y-m-d")."'";
			$dueDay = $model->DueDay;
			$otherExpense = (isset($model->OtherExpense))? $model->OtherExpense : "NULL";
			$feeCourt = (isset($model->FeeCourt))? $model->FeeCourt : "NULL";
		}		
		
		$sql = "UPDATE company SET
					ProDate = $proDateFormat,
					DueDay = $dueDay,
					OtherExpense = $otherExpense,
					FeeCourt = $feeCourt,
					LastModifiedBy = '$sess_userid',
					LastModifiedDateTime = NOW()
				WHERE CID = $cid";
		
		
		mysql_query($sql);
		$error = mysql_error();
		if($error != ""){
			throw new Exception($error);
		}
	}
	
	
	private function saveAttachment($files, $pid, $hireDocfileRelatePath){
		$fileFor = $this->PROCEEDINGS_FILE_TYPE;
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
					VALUES('$newHireDocfileName', '$pid', '$fileFor')";
					
					
					mysql_query($insertFileSql);
						
					$mysql_error = mysql_error();
					if($mysql_error != ""){
						throw new Exception($mysql_error);
					}
					$fid = mysql_insert_id();
						
					$insertSeqFileSql = "INSERT INTO proceedingsattachment(file_id, PID)
					VALUES('$fid', '$pid')";
					
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
	 * @param int $pid
	 * @return SequestrationPayment[]
	 */
	private function getProceedingPayments($pid){
		$sql = "SELECT pPayment.PPID, pPayment.PID, pPayment.LID, 
				pPayment.PrincipleAmount,  pPayment.InterestAmount, pPayment.InterestRate, pPayment.InterestPerDay, pPayment.TotalAmount,
				law.Year
				FROM proceedingpayment pPayment
				INNER JOIN lawfulness law ON pPayment.LID = law.LID
				WHERE PID = $pid";
		$sqlResult = mysql_query($sql);
		$payments = array();
		while ($payment = mysql_fetch_object($sqlResult, "ProceedingPayment")) {
			array_push($payments, $payment);
		}
		mysql_free_result($sqlResult);
		return (count($payments) > 0)? $payments : null;
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
	 * @param int $pid
	 * @param int $cid
	 * @return <NULL, string(Y-m-d)>
	 */
	private function getNoticeReceivedDate($pid, $cid){
		$receivedDate = null;
		$seqSql = "SELECT ForYear FROM company_lawstatus_log WHERE (DocumentID = $pid)
		AND (DocumentType = '".LAWSTATUS_LOG_DOCUMENT_TYPE_PROCEEDING."')
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
	 * @param Proceeding $model
	 * @param string $theCompanyWord
	 * @return ProceedingValidationResult $result
	 */
	private function validateProceeds($model, $theCompanyWord){
		global $PROCEEDING_TYPE;
		$proceedChargeType = $PROCEEDING_TYPE->ศาลสั่งฟ้อง;
		
		$result = new ProceedingValidationResult();
		$result->IsValid = true;
		
		$errorMessage = "";
		$pid = $model->PID;
				
		
		if(!empty($model->GovDocumentNo)){
			$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);
			$sqlChkDup = "SELECT count(*), PID FROM proceedings WHERE GovDocumentNo ='$govDocumentNo' AND PID <> $pid";
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
				$result->DupedPID = $row[1];
			}
		}		
		
		if(is_null($model->RequestDate)){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: วันที่\n";
		}
		
		if(empty($model->GovDocumentNo)){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: หนังสือเลขที่\n";
		}

		if(empty($model->PType)){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: กระบวนการ\n";
		}else if($model->PType == $proceedChargeType){
			if(is_null($model->ProDate)){
				$result->IsValid = false;
				$errorMessage .= "กรุณาใส่ข้อมูล: วันที่ศาลสั่งฟ้อง\n";
			}
			
			if(is_null($model->DueDay) || (isset($model->DueDay) && $model->DueDay == 0)){
				$result->IsValid = false;
				$errorMessage .= "กรุณาใส่ข้อมูล: กำหนดชำระภายใน\n";
			}
			
			if((isset($model->OtherExpense)) && (!is_numeric($model->OtherExpense))){
				$result->IsValid = false;
				$errorMessage .= "กรุณาใส่ข้อมูล: ค่าใช้จ่ายอื่นๆ เป็นตัวเลข\n";
			}
		}     
		
		if(is_null($model->ProceedingPayments)){
			$result->IsValid = false;
			$errorMessage .="กรุณาเลือก: ".$theCompanyWord."ที่ต้องการส่งพนักงานอัยการ/ยื่นขอรับเงินกรณีล้มละลาย\n";
		}
		
		$result->Message = $errorMessage;
		return $result;
	}	
	
}

class ProceedingPayment{
	/***
	 * 
	 * @var int
	 */
	public $SPID;
	/***
	 *
	 * @var int
	 */
	public $PID;
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
	public $InterestPerDay;
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
	
}

class ProceedingList{
	/**
	 *
	 * @var int
	 */
	public $PID;
	/**
	 *
	 * @var int
	 */
	public $CID;
	/**
	 *
	 * @var DateTime
	 */
	public $RequestDate;
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
	/**
	 *
	 * @var double
	 */
	public $OtherExpense;
	/**
	 *
	 * @var DateTime
	 */
	public $ProDate;
	/**
	 * กระบวนการ
	 * @var int
	 */
	public $PType;
	/**
	 * 
	 * @var int : 0 = ไม่มีเอกสารแนบ, 1 = มีเอกสารแนบ
	 */
	public $HasAttachment;
}

class Proceeding{
	/**
	 * 
	 * @var int
	 */
	public $PID;
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
	 * กระบวนการ
	 * @var int
	 */
	public $PType;
	/**
	 * กระบวนการ (ใช้เปรียบเทียบ)
	 * @var int
	 */
	public $CurrentPType;
	/**
	 *
	 * @var int
	 */
	public $Status;
	/**
	 *
	 * @var DateTime
	 */
	public $RequestDate;
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
	/**
	 *
	 * @var string
	 */
	public $Detail;
	/**
	 * วันที่ศาลสั่งฟ้อง
	 * @var DateTime
	 */
	public $ProDate;
	/**
	 * กำหนดชำระภายใน
	 * @var int
	 */
	public $DueDay;
	/**
	 * ค่าใช้จ่ายอื่นๆ
	 * @var double 
	 */
	public $OtherExpense;
	/**
	 * สำหรับคำนวณยอดเงินคงค้าง คิดจากวันปัจจุบัน หรือวันที่ศาลสั่งฟ้อง(กรณี กระบวนการ=ศาลสั่งฟ้อง)
	 * @var DateTime
	 */	
	public $CalDate;
	/**
	 * ค่าฤชาธรรมเนียม
	 * @var double
	 */
	public $FeeCourt;
	/**
	 * วันที่รับเอกสารโนติส
	 * @var DateTime
	 */
	public $NoticeReceivedDate;
	/**
	 * วันที่เริ่มคิดดอกเบี้ยปี 2011 (นับ 30 วัน ตั้งแต่วันที่รับแจ้งโนติส)
	 * @var DateTime
	 */
	public $Start2011InterestDate;
	/**
	 *
	 * @var ProceedingPayment[]
	 */
	public $ProceedingPayments;
}

class ProceedingsAttachment{
	/**
	 * @var int
	 */
	public $PID;
	/**
	 * @var int
	 */
	public $CID;
	/**
	 * @var int
	 */
	public $FileID;
	/**
	 * @var string
	 */
	public $FileName;
	/**
	 * @var string
	 */
	public $FileType;
}

class ProceedingResult extends ResultMessage{
	/**
	 * @var Proceeding
	 */
	public $Data;
	
	/**
	 * @var bool
	 */
	public $DupedKey;
	
	/**
	 * @var int
	 */
	public $DupedPID;
}

class ProceedingListResult extends ResultMessage{
	/**
	 * @var ProceedingList
	 */
	public $Data;
}

class ProceedingsAttachmentResult extends ResultMessage{
	/**
	 * @var ProceedingsAttachment
	 */
	public $Data;
}

class ProceedingValidationResult extends ResultValidation{
	/**
	 * @var bool
	 */
	public $DupedKey;
	
	/**
	 * @var int
	 */
	public $DupedPID;	
	
}

class ProceedingYearResult extends ResultMessage{
	/**
	 * ปีที่สามารถสร้างโนตีส
	 * @var int
	 */
	public $Data;
}


?>