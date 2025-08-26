<?php
require_once 'db_connect.php';
require_once 'c2x_model.php';
require_once 'scrp_config.php';
require_once 'c2x_include.php';


class ManageCollection {	
	
	public function saveCollection($post, $files, $collectionid, $username, $userfullname, $theCompanyWord, $hireDocfileRelatePath){
		
		//FIRST, see how many checkboxes are checked
		$post_records = $_POST["total_records"]*1;
		$dupedKey = false;	
		$dupedCollectionID = 0;
		$saveResult = new CollectionResult();
		$saveResult->IsComplete = false;
		
		
		try{
			$model = $this->getCollectionInput($collectionid, $_POST);
			$attachments = fixFilesArray($files["Collection_docfile"]);
			$validateResult = $this->validateCollection($model, $attachments, $theCompanyWord);	
			if($validateResult->IsValid){		
				
				if($collectionid == 0){
					$saveResult = $this->insertCollection($model, $attachments, $username, $userfullname, $hireDocfileRelatePath);
				}else{
					$saveResult = $this->updateCollection($model, $attachments, $userfullname, $hireDocfileRelatePath);				
				}
			}else{
				$saveResult->DupedKey = $validateResult->DupedKey;
				$saveResult->DupedCollectionID = $validateResult->DupedCollectionID;
				$saveResult->Data = $model;
				$saveResult->Message = $validateResult->Message;
			}	
			
			
		}catch(Exception $ex){
			$saveResult->Message = $ex->getMessage();
		}			
		
		return $saveResult;		
	}
	
		
	/**
	 * @param int $collectionid
	 * @param object $seqType
	 * @return CollectionResult
	 */
	public function getCollection($collectionid){
		$result = new CollectionResult();
		$sql = "SELECT c.CollectionID, c.Year, c.RequestNo, c.RequestDate, c.GovDocumentNo, c.DocumentDetail, c.CreatedBy
		FROM collectiondocument c
		WHERE c.CollectionID = $collectionid";
		$sqlResult = mysql_query($sql);
		$collection = mysql_fetch_object($sqlResult, "Collection");
		
		try {
			if($collection){
				$result->IsComplete = true;
				$result->Data = $collection;
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
	 * @return CollectionListResult
	 */
	public function getCollectionList($cid){
		$result = new CollectionListResult();
	
		$sql = "SELECT s.CollectionID, cs.CSID, s.DocumentDate,  s.GovDocumentNo, s.TotalAmount, s.ProceedingsBy
					FROM collectiondocument s
					WHERE s.CID = $cid
				";
		
		//error_log($sql);
		try{
			$sqlResult = mysql_query($sql);
			$error = mysql_error();	
				
			if($error == ""){
				$result->IsComplete = true;
				
				$dataItems = array();
				while ($row = mysql_fetch_object($sqlResult, "CollectionList")){
					array_push($dataItems, $row);
				}
				
				$result->Data = (count($dataItems) > 0)? $dataItems : null;
				
				
			}else{
				$result->IsComplete = false;
				$result->Message = $error;
			}
			
		} catch (Exception $ex) {
			handleUnexpectedException($ex);
			$result->IsComplete = false;
			$result->Message = $ex->getMessage();
		}
	
		return $result;
	}
	
	
	/**
	 * @param int $collectionid
	 * @param $_POST $post
	 * @return Collection
	 */
	public function getCollectionInput($collectionid, $post){
		$model = new Collection();
		
		$model->CollectionID = $collectionid;
		
		$text = $post["Year"];

		$model->Year = $text;		
	

		$text = $post["HiddCompanyCode"];
		$model->CompanyCode = $text;
	
	
		$day = $post["RequestDate_day"];
		$month = $post["RequestDate_month"];
		$year = $post["RequestDate_year"];
		
		if(($day != "00") && ($month != "00") && ($year != "0000")){
			$model->RequestDate = $year."-".$month."-".$day;
		}else{
			$model->RequestDate = null;
		}	
			
		$text = $post["RequestNo"];
		$model->RequestNo = $text;
				
		$text = $post["GovDocumentNo"];		
		$model->GovDocumentNo = trim($text);

		$text = $post["DocumentDetail"];
		$model->DocumentDetail = trim($text);
		
		$text = $post["CreatedBy"];
		$model->CreatedBy = trim($text);
		
		return $model;
	}
	
	/**
	 * @param int $collectionid
	 * @param $_POST $post
	 * @return CollectionResult
	 */
	public function deleteCollection($collectionid, $post){
		$result = new CollectionResult();
		$result->IsComplete = true;
		$result->Data = $this->getCollectionInput($collectionid, $post);
		$delResult = $this->deleteCollectionWithID($collectionid);		
		$result->IsComplete = $delResult->IsComplete;
		$result->Message = $delResult->Message;					
		
		return  $result;
	}  
	
	public function deleteCollectionCompany($collectionid, $post){
		$result = new CollectionResult();
		$result->IsComplete = true;
		$result->Data = $this->getCollectionInput($collectionid, $post);
		$delResult = $this->deleteCollectionCompanyWithID($post["CCID"]);
		$result->IsComplete = $delResult->IsComplete;
		$result->Message = $delResult->Message;
	
		return  $result;
	}
	
	
	
	public function deleteCollectionCompanyWithID($ccid){
		$result = new ResultMessage();
		$result->IsComplete = true;
	
		$delCollectionCompany = "DELETE FROM collectioncompany WHERE CCID = $ccid";
		
		dbBegin();
			
		// delete collection
		mysql_query($delCollectionCompany);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			dbRollback();
			return  $result;
		}
		
		dbCommit();
		
		return $result;
	}
	
	public function deleteCollectionWithID($collectionid){
		$result = new ResultMessage();
		$result->IsComplete = true;
	
		$delAttach = "DELETE FROM collectionattachment
		WHERE CollectionID  = $collectionid";
	
		$delCollectionCompany = "DELETE FROM collectioncompany WHERE CollectionID  = $collectionid";
		
		$delCollection = "DELETE FROM collectiondocument WHERE CollectionID  = $collectionid";
		error_log($delCollection);
		dbBegin();
		mysql_query($delAttach);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			dbRollback();
			return  $result;
		}
	
		// delete collectioncompany
		mysql_query($delCollectionCompany);
		$error =  mysql_error();
		if($error!= ""){
			$result->IsComplete = false;
			$result->Message = $error;
			dbRollback();
			return  $result;
		}
	
		// delete collectiondocument
		mysql_query($delCollection);
		$error =  mysql_error();
		if($error != ""){
			$result->IsComplete = false;
			$result->Message = $error;
			dbRollback();
			return  $result;
		}
	
		dbCommit();
	
		return $result;
	}	
	
	/**
	 * @param int $cid
	 * @return CollectionCompanyListResult
	 */
	public function getCollectionCompanyListByCID($cid){
		$resutl = new CollectionCompanyListResult();
		$sql = "SELECT cc.CollectionID, cc.CCID, cc.CID, cd.RequestDate, cd.RequestNo, cd.GovDocumentNo,
				cc.ReceiverNo, cc.ReceiverDate, cc.Receiver, cd.CreatedBy as RequestBy
				FROM collectioncompany cc 
				INNER JOIN collectiondocument cd on cc.CollectionID = cd.CollectionID
				WHERE cc.CID = $cid
				ORDER BY cd.RequestDate, cd.RequestNo, cd.GovDocumentNo";
		error_log($sql);
		$sqlResult = mysql_query($sql);
		$errorMsg = mysql_error();
		if($errorMsg == ""){
			$resutl->IsComplete = true;
			$dataItems = array();
			while ($item = mysql_fetch_object($sqlResult, "CollectionCompany")) {
				array_push($dataItems, $item);
			}
			$resutl->Data = $dataItems;
		}else{
			$resutl->IsComplete = false;
			$resutl->Message = $errorMsg;
			error_log($errorMsg);
		}
		return $resutl;
	}
	/**
	 * ได้ปีที่ต้องทวงถามน้อยที่สุดที่ยังไม่ปฏิบัติตามกฎหมาย
	 * @param int $cid
	 * @return CollectionYearResult
	 */
	public function canCreateCompanyCollection($cid){		
		global $LAWFUL_STATUS;
		$hasRole = hasCreateRoleCollectionByCID($cid);
	
		$result = new CollectionYearResult();
		$result->IsComplete = true;
		$result->Data = null;
		
		if($hasRole){
			$sql = "SELECT Year FROM lawfulness WHERE (CID = $cid) AND ((LawfulStatus = '0' or LawfulStatus is null) or (LawfulStatus = '2'))
			ORDER BY Year limit 0, 1";
					
			error_log($sql);
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
	 * @param CollectionCompany $model
	 * @return ResultMessage
	 */
	public function updateCollectionCompanyReceived($model){
		$result = new ResultMessage();
		global $sess_userfullname;
		global $sess_userid;
		$updateBy = "'".mysql_real_escape_string($sess_userfullname)."'";
		
		$receiverNo = ($model->ReceiveNo == "")? "NULL" : "'".mysql_real_escape_string($model->ReceiveNo)."'";
		$receiverDate = (is_null($model->ReceiveDate))? "NULL" : "'".$model->ReceiveDate->format("Y-m-d")."'";
		$receiver = ($model->Receiver == "")? "NULL" : "'".mysql_real_escape_string($model->Receiver)."'";
		
		$sql = "UPDATE collectioncompany SET
			     	ReceiverNo = $receiverNo,
			     	ReceiverDate = $receiverDate,
			     	Receiver = $receiver,
			     	ModifiedBy = $updateBy,
			     	ModifiedDate = NOW(),
			     	ModifiedByID = $sess_userid
			    WHERE CCID = $model->CCID";
		
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
	
	/***
	 * @param $_POST $post
	 * @return CollectionResult
	 */
	private function insertCollection($model, $files, $username, $userfullname, $hireDocfileRelatePath){			
		$result = new CollectionResult();
		$result->IsComplete = false;
		global $sess_userid;

		$year = $model->Year;
		$requestDateFormat = $model->RequestDate;
		$requestNo = mysql_real_escape_string($model->RequestNo);
		$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);	
		$userfullname = mysql_real_escape_string($userfullname);
		if ($model->DocumentDetail != null) {
			$documentDetail = "'".mysql_real_escape_string($model->DocumentDetail)."'";
		}else{
			$documentDetail = 'Null';
		}
		
		$the_sql = "INSERT INTO collectiondocument(Year, RequestDate, RequestNo, GovDocumentNo, DocumentDetail, CreatedDate, CreatedBy,CreatedByID)
					VALUES($year, '$requestDateFormat', '$requestNo', '$govDocumentNo', $documentDetail, NOW(), '$userfullname',$sess_userid)";
		error_log("$the_sql  ".$the_sql);
		try {
			dbBegin();
			mysql_query($the_sql);
			if(mysql_error() != ""){
				throw new Exception(mysql_error());
			}
			$collectionid = mysql_insert_id();
			
			$this->insertCollectionCompanys($collectionid,$username);
			$saveFilesResult = $this->saveAttachment($files, $collectionid, $hireDocfileRelatePath);
			if($saveFilesResult->IsComplete == false){
				throw new Exception($saveFilesResult->Message);
			}
			
			dbCommit();
			
			$result->IsComplete = true;
			$model->CollectionID = $collectionid;
			
			
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
	 * @param Collection $model
	 * @param string $username
	 * @param int $lawStatus
	 * @return CollectionResult
	 */
	private function updateCollection($model, $files, $userfullname, $hireDocfileRelatePath){
		$result = new CollectionResult();
		$result->IsComplete = false;
		$year= $model->Year;		
		$collectionid = $model->CollectionID;
		$requestDateFormat = $model->RequestDate;
		$requestNo = mysql_real_escape_string($model->RequestNo);
		$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);	
		$userfullname = mysql_real_escape_string($userfullname);
		global $sess_userid;		
		if ($model->DocumentDetail != null) {
			$documentDetail = "'".mysql_real_escape_string($model->DocumentDetail)."'";
		}else{
			$documentDetail = 'NULL';
		}
		
		$the_sql = "UPDATE collectiondocument SET 
						year = '$year',		
						RequestDate = '$requestDateFormat',
						RequestNo = '$requestNo',								
						GovDocumentNo = '$govDocumentNo',
						DocumentDetail = $documentDetail, 
						ModifiedDate = NOW(),
						ModifiedBy = '$userfullname',
						ModifiedByID = $sess_userid
					WHERE CollectionID = $collectionid
					";
		try{
			dbBegin();
			mysql_query($the_sql);			
			
			$saveFilesResult = $this->saveAttachment($files, $collectionid, $hireDocfileRelatePath);
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

	private function insertCollectionCompanys($collectionid,$username){
		$table_name = "collectioncompany";
		$post_records = $_POST["total_records"]*1;
		$input_fields = array();
		global $sess_userid;		
		if ($model->Receiver != null) {
		 $receiver = "'".mysql_real_escape_string($model->Receiver)."'";
		 }else{
		 $receiver = 'NULL';
		 }
		 if ($model->ReceivedDate != null) {
		 $receivedDateFormat = "'".mysql_real_escape_string($model->ReceivedDate)."'";
		 }else{
		 $receivedDateFormat = 'NULL';
		 }		
		$special_fields = array('CollectionID','CID','ModifiedDate','ModifiedBy','ModifiedByID');
		$sqlDel = "DELETE FROM collectioncompany WHERE CollectionID = $collectionid";	
		mysql_query($sqlDel);
		
		if($_POST["send_to_all"]){
			$condition = ($_POST["send_to_all"]);
			$cur_year = $_POST["Year"];
			$get_org_sql = "SELECT z.CID as the_cid
							FROM company z
							LEFT JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
							LEFT JOIN provinces c ON z.province = c.province_id
							JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year'
							where 
							$condition
							";
			
			if($cur_year > 2012){
					$get_org_sql = "SELECT z.CID as the_cid
									FROM company z
									LEFT JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode
									LEFT JOIN provinces c ON z.province = c.province_id
									JOIN lawfulness y ON z.CID = y.CID and y.Year = '$cur_year'
									where
									$condition
									";
			}
			$org_result = mysql_query(stripslashes($get_org_sql));
			while ($post_row = mysql_fetch_array($org_result)) {
				$org_id = $post_row["the_cid"];
				$special_values = array("'".$collectionid."'", "'".$org_id."'", "NOW()", "'$username'",$sess_userid);
				$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace") . ";";
				mysql_query($the_sql) or die (mysql_error());
				if(mysql_error() != ""){
					throw new Exception(mysql_error());
				}
			}
		}else{
			for($i=1 ; $i<=$post_records ; $i++){
				if($_POST["chk_$i"]){
					$org_id = $_POST["chk_$i"];
					$special_values = array("'".$collectionid."'", "'".$org_id."'", "NOW()", "'$username'",$sess_userid);
					$the_sql = generateInsertSQL($_POST,$table_name,$input_fields,$special_fields,$special_values,"replace");
					
					mysql_query($the_sql);					
				}
				if(mysql_error() != ""){
					throw new Exception(mysql_error());
				}
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
	 * @param Collection $model
	 * @param array $attachments
	 * @param string $theCompanyWord
	 * @return CollectionValidationResult $result
	 */
	private function validateCollection($model, $attachments, $theCompanyWord){
		$result = new CollectionValidationResult();
		$result->IsValid = true;
		$errorMessage = "";
		$collectionid = $model->CollectionID;
				
		
		if(!empty($model->GovDocumentNo)){
			$govDocumentNo = mysql_real_escape_string($model->GovDocumentNo);
			$sqlChkDup = "SELECT count(*), CollectionID FROM collectiondocument WHERE GovDocumentNo ='$govDocumentNo' AND CollectionID <> $collectionid";
			$sqlChkDupResult = mysql_query($sqlChkDup);
			if(mysql_error() != ""){
				error_log(mysql_error());
			}
			$row = mysql_fetch_row($sqlChkDupResult);
			$count = $row[0];
			if($count > 0){
				$result->IsValid = false;
				$result->DupedKey = true;
				$result->DupedCollectionID = $row[1];
			}
		}		

		
		if(is_null($model->RequestDate)){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: วันที่\n";
		}

		if(empty($model->RequestNo)){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: ครั้งที่\n";
		}
				
		if(empty($model->GovDocumentNo)){
			$result->IsValid = false;
			$errorMessage .= "กรุณาใส่ข้อมูล: หนังสือเลขที่\n";
		}
		
		$fileTypeIsValid = checkAllowFileUpload($attachments);
		if(!$fileTypeIsValid){
			$result->IsValid = false;
			$errorMessage .="ชนิดไฟล์แนบไม่ถูกต้อง\n";
		}
		
	
		$result->Message = $errorMessage;
		return $result;
	}	
	
	/**
	 * @param $_FILES $files
	 * @return ResultMessage $result
	 */
	private function saveAttachment($files, $collectionid, $hireDocfileRelatePath){
		$fileFor = "Collection_docfile";
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
					VALUES('$newHireDocfileName', '$collectionid', '$fileFor')";
					//error_log($insertFileSql);
					mysql_query($insertFileSql);
						
					$mysql_error = mysql_error();
					if($mysql_error != ""){
						throw new Exception($mysql_error);
					}
					$fid = mysql_insert_id();
						
					$insertSeqFileSql = "INSERT INTO collectionattachment(file_id, CollectionID)
					VALUES('$fid', '$collectionid')";
					//error_log($insertFileSql);
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
	
}


class CollectionList{
	/**
	 *
	 * @var int
	 */
	public $CollectionID;
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
	/**
	 *
	 * @var string
	 */
	public $ProceedingsBy;
}

class Collection{
	/**
	 *
	 * @var int
	 */
	public $Year;
	/**
	 * 
	 * @var int
	 */
	public $CollectionID;
	/**
	 *
	 * @var string
	 */
	public $CompanyCode;
	/**
	 *
	 * @var DateTime
	 */
	public $RequestDate;
	/**
	 *
	 * @var string
	 */
	
	public $RequestNo;	
	/**
	 *
	 * @var string
	 */
	public $GovDocumentNo;
	/**
	 *
	 * @var string
	 */
	public $DocumentDetail;
	/**
	 *
	 * @var string
	 */
	public $CreatedBy;
	/**
	 *
	 * @var CollectionCompany[]
	 */
	public $SequestrationPropertyDetails;	
}

class CollectionCompany{
	/**
	 *
	 * @var int
	 */
	public $CCID;
	/**
	 *
	 * @var int
	 */
	public $CollectionID;
	/**
	 *
	 * @var int
	 */
	public $CID;
	/**
	 * @var string
	 */
	public $Receiver;
	/**
	 * @var DateTime
	 */
	public $ReceiveDate;
	/**
	 * @var string
	 */
	public $ReceiveNo;	
	/**
	 * @var string
	 */
	public $RequestNo;
	/**
	 * @var DateTime
	 */
	public $RequestDate;
	/**
	 * @var string
	 */
	public $GovDocumentNo;
	/**
	 * @var string
	 */
	public $RequestBy;
	
}

class CollectionResult extends ResultMessage{
	/**
	 * @var Collection
	 */
	public $Data;
	
	/**
	 * @var bool
	 */
	public $DupedKey;
	
	/**
	 * @var int
	 */
	public $DupedCollectionID;
}

class CollectionListResult extends ResultMessage{
	/**
	 * @var CollectionList
	 */
	public $Data;
}

class CollectionValidationResult extends ResultValidation{
	/**
	 * @var bool
	 */
	public $DupedKey;
	
	/**
	 * @var int
	 */
	public $DupedCollectionID;	
	
}

class CollectionCompanyListResult extends ResultMessage{
	/**
	 * @var CollectionCompany[]
	 */
	public $Data;
}

class CollectionYearResult extends ResultMessage{
	/**
	 * ปีที่สามารถสร้างจดหมายถวงถาม
	 * @var int
	 */
	public $Data;
}
?>