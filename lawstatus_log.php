<?php 

require_once 'c2x_include.php';


class CompanyInfo{
	public $companyId;
	public $lawStatus;
	public $beginLitigationYear;
}

class LawStatusUpdater{
	//Variables
	/**
	 * @var CompanyInfo
	 */
	private $companyInfo;
	private $isValid = false;
	
	//Parameters
	private $companyId;
	private $documentId;
	private $documentType;
	private $userId;
	
	function __construct($companyId, $documentId, $documentType, $userId){
		$this->companyId = $companyId;
		$this->documentId = $documentId;
		$this->documentType = $documentType;
		$this->userId = $userId;
		$this->isValid = true;		
		
		if (is_numeric($companyId)){
			$this->getCompanyInfo();
			
			if ($this->companyInfo == null){
				$this->isValid = false;
			}
		}else{
			$this->isValid = false;
		}
		
		if (!$this->checkDocumentType()){
			$this->isValid = false;
		}
		
// 		if (!is_int($documentId)){
// 			error_log('documentId');
// 			$this->isValid = false;
// 		}
		
		if (is_null($userId)){
			$this->isValid = false;
		}
		
	}
	
	private function checkDocumentType(){
		$documentType = $this->documentType;
		return !is_null($documentType)
				&& ($documentType == LAWSTATUS_LOG_DOCUMENT_TYPE_NOTICE
				|| $documentType == LAWSTATUS_LOG_DOCUMENT_TYPE_PROCEEDING
				|| $documentType == LAWSTATUS_LOG_DOCUMENT_TYPE_SEQUESTRATION
				|| $documentType == LAWSTATUS_LOG_DOCUMENT_TYPE_CANCELLED_SEQUESTRATION
				|| $documentType == LAWSTATUS_LOG_DOCUMENT_TYPE_RECEIPT);
	}
	
	private function getCompanyInfo(){
		$this->companyInfo = NULL;
		
		$companyQueryResult = mysql_query("SELECT CID, LawStatus, BeginLitigationYear FROM company WHERE CID=$this->companyId");
		if ($companyQueryResult !== false){
			$data = mysql_fetch_array($companyQueryResult);
			if ($data !== false){
				$info = new CompanyInfo();
				$info->lawStatus = $data['LawStatus'];
				$info->beginLitigationYear = $data['BeginLitigationYear'];
				$info->companyId = $this->companyId;
				
				$this->companyInfo = $info;
			}
		}
	}
	
	/**
	 * @param number $newLawStatus
	 * @return boolean
	 */
	private function updateLawStatus($newLawStatus, $newYear){
		$companyUpdateResult = false;
		$fields = array('LawStatus'=>$newLawStatus, 'BeginLitigationYear' => $newYear);
		
		if (executeUpdate('company', "CID=$this->companyId", $fields)){//("UPDATE company SET LawStatus=$newLawStatus WHERE CID=$this->companyId") === true){
			$companyUpdateResult = true;
		}
		return $companyUpdateResult;
	}
	
	private function getAddedLawStatusLog($documentType, $documentId)
	{
		$row = false;
		$selectQuery = "SELECT * FROM company_lawstatus_log 
				WHERE ChangeType='".LAWSTATUS_LOG_CHANGE_TYPE_ADD."'
				 AND DocumentType='".mysql_real_escape_string($documentType)."'
				 AND DocumentID='".mysql_real_escape_string($documentId)."'
		 		ORDER BY UpdatedDate DESC";
		$queryResult = mysql_query($selectQuery);
		
		if ($queryResult !== false)
		{
			$row = mysql_fetch_array($queryResult);
			mysql_free_result($queryResult);
		}
		
		return $row;
	}
	
	private function getHighestLawStatusLog()
	{
		$row = false;
		$selectQuery = "SELECT * FROM company_lawstatus_log 
				WHERE ChangeType='".LAWSTATUS_LOG_CHANGE_TYPE_ADD."'
				 AND ForYear='".$this->companyInfo->beginLitigationYear."'
				 AND CID='".$this->companyInfo->companyId."'
		 		ORDER BY CASE WHEN CalculatedLawStatus >= 10 THEN CalculatedLawStatus / 10 ELSE CalculatedLawStatus END DESC";
		$queryResult = mysql_query($selectQuery);
		
		if ($queryResult !== false)
		{
			$row = mysql_fetch_array($queryResult);
			mysql_free_result($queryResult);
		}
		
		return $row;
	}
	
	private function insertLawStatusLogForAdd($calculatedLawStatus, $actualLawStatus, $forYear){
		$fields = array(
				'CID' => $this->companyId,
				'ForYear' => $forYear,
				'DocumentID' => $this->documentId,
				'DocumentType' => $this->documentType,
				'PreviousLawStatus' => $this->companyInfo->lawStatus,
				'ActualLawStatus' => $actualLawStatus,
				'CalculatedLawStatus' => $calculatedLawStatus,
				'ChangeType' => LAWSTATUS_LOG_CHANGE_TYPE_ADD,
				'UpdatedDate' => date('Y-m-d H:i:s'),
				'UpdatedBy' => $this->userId
		);
		return executeInsert('company_lawstatus_log', $fields); //mysql_query($query);
	}
	
	private function insertLawStatusLogForDelete($calculatedLawStatus, $actualLawStatus, $forYear){
		$fields = array(
				'CID' => $this->companyId,
				'ForYear' => $forYear,
				'DocumentID' => $this->documentId,
				'DocumentType' => $this->documentType,
				'PreviousLawStatus' => $this->companyInfo->lawStatus,
				'ActualLawStatus' => $actualLawStatus,
				'CalculatedLawStatus' => $calculatedLawStatus,
				'ChangeType' => LAWSTATUS_LOG_CHANGE_TYPE_DELETE,
				'UpdatedDate' => date('Y-m-d H:i:s'),
				'UpdatedBy' => $this->userId
		);
		return executeInsert('company_lawstatus_log', $fields); //mysql_query($query);
	}
	
	public function add(){
		if (!$this->isValid){
			return;
		}
		
		if ($this->companyInfo->lawStatus == 9 || ($this->companyInfo->lawStatus != 0 && is_null($this->companyInfo->beginLitigationYear))){
			return;
		}

// 		สำหรับเอกสารอื่นๆ นอกจากใบเสร็จ
// 		ถ้า BeginLitigationYear เป็น NULL จะไม่มีการบันทึก Log และเปลี่ยนสถานะ
// 		ถ้า LawStatus เป็น 0/9 ให้ใช้สถานะเดิม
// 		ถ้าไม่เป็น 0/9 ให้ดูสถานะที่จะเปลี่ยนแปลงว่าสูงกว่าสถานะเดิมหรือไม่ ถ้าใช่ ให้ใช้สถานะใหม่ ถ้าไม่ใช่ให้ใช้สถานะเดิม
		
		
// 			สำหรับใบเสร็จ
// 			เวลาเพิ่ม (add) ส่งค่า CID, DocumentID, DocumentType, UserID
// 			ถ้า BeginLitigationYear เป็น NULL จะไม่มีการบันทึก Log และเปลี่ยนสถานะ
// 			ถ้า LawStatus เป็น 0/9 ให้ใช้สถานะเดิม
// 			ถ้าไม่เป็น 0/9 ให้ดูสถานะของ Lawfulness ทุกปีจะต้องเป็น 1 หรือ 3 แล้วจะเปลี่ยน LawStatus เป็น 9

		$calculatedLawStatus = 0;
		$shouldAddLog = true;
		
		if ($this->documentType == LAWSTATUS_LOG_DOCUMENT_TYPE_NOTICE){
			$calculatedLawStatus = 2;
		} else if ($this->documentType == LAWSTATUS_LOG_DOCUMENT_TYPE_PROCEEDING){
			$calculatedLawStatus = 4;
			
			$proceedingQueryResult = mysql_query("SELECT ProDate, PType FROM proceedings WHERE PID=$this->documentId");
			$row = mysql_fetch_array($proceedingQueryResult);
			mysql_free_result($proceedingQueryResult);
			
			if ($row['PType'] == 1){
				$calculatedLawStatus = 4;
			}else if ($row['PType'] == 2){
				$calculatedLawStatus = 6;
			}else if ($row['PType'] == 3){
				$calculatedLawStatus = 5;
			}
		} else if ($this->documentType == LAWSTATUS_LOG_DOCUMENT_TYPE_SEQUESTRATION){
			$calculatedLawStatus = 3;
		} else if ($this->documentType == LAWSTATUS_LOG_DOCUMENT_TYPE_CANCELLED_SEQUESTRATION){
			$calculatedLawStatus = 31;
		} else if ($this->documentType == LAWSTATUS_LOG_DOCUMENT_TYPE_RECEIPT){
			$checkResult = mysql_query("SELECT COUNT(*) Total FROM lawfulness WHERE LawfulStatus IN (0,2) AND CID=$this->companyId");
			if ($checkResult !== false){
				$checkRow = mysql_fetch_array($checkResult);
				if ($checkRow !== false){
					if ($checkRow['Total'] == 0){
						$calculatedLawStatus = 9;
					}else{
						$calculatedLawStatus = $this->companyInfo->lawStatus;
					}
				}
			}
// 		 						// ในกรณีที่ปฏิบัติตามกฎหมายแล้ว จะต้องตรวจสอบดูว่า lawstatus ของบริษัทจะต้องเปลี่ยนเป็นสถานะ 9 ชำระหนี้ครบแล้วหรือไม่
// 		 						// โดยบริษัทจะต้องมี lawstatus ไม่เป็น 0 ไม่เข้าข่ายหรือ 9 ชำระหนี้ครบ
// 		 						// และ lawfulness ทุกปีจะต้องเป็น 1 ปฏิบัติตามกฎหมาย หรือ 3 ไม่เข้าข่าย (หรือไม่มีสถานะ 0,2)
// 		 						if ($lawfulStatus == 1){
// 		 							$cidResult = mysql_query("SELECT CID, LawStatus FROM company WHERE companycode = '$companyCode' and branchcode = '$branchCode'");
// 		 							if ($cidResult !== false){
// 		 								$cidRow = mysql_fetch_array($cidResult);
// 		 								if ($cidRow !== false){
// 		 									$cid = $cidRow['CID'];
// 		 									$lawStatus = $cidRow['LawStatus'];
		 									
// 		 									if (!($lawStatus == 0 || $lawStatus == 9)){
// 		 										$checkResult = mysql_query("SELECT COUNT(*) Total FROM lawfulness WHERE LawfulStatus IN (0,2) AND CID=$cid");
// 		 										if ($checkResult !== false){
// 		 											$checkRow = mysql_fetch_array($checkResult);
// 		 											if ($checkRow !== false){
// 			 											if ($checkRow['Total'] == 0){
// 			 												if (mysql_query("UPDATE company SET LawStatus=9 WHERE CID=$cid") === false){
// 				 												$isValid = false;
// 				 												$errorMsg = "เกิดปัญหาในการปรับปรุงข้อมูลการชำระเงิน";
// 				 												break;
// 			 												}
// 			 											}
// 		 											}else{
// 		 												$isValid = false;
// 		 												$errorMsg = "เกิดปัญหาในการปรับปรุงข้อมูลการชำระเงิน";
// 		 												break;
// 		 											}
// 		 										}else{
// 				 									$isValid = false;
// 				 									$errorMsg = "เกิดปัญหาในการปรับปรุงข้อมูลการชำระเงิน";
// 				 									break;
// 		 										}
// 		 									}
// 		 								} else {
// 		 									$isValid = false;
// 		 									$errorMsg = "เกิดปัญหาในการปรับปรุงข้อมูลการชำระเงิน";
// 		 									break;
// 		 								}
// 		 							} else {
// 		 								$isValid = false;
// 		 								$errorMsg = "เกิดปัญหาในการปรับปรุงข้อมูลการชำระเงิน";
// 		 								break;
// 		 							}
// 		 						}
		}
		

		$query = "SELECT 1 FROM company_lawstatus_log WHERE DocumentID='$this->documentId'  AND DocumentType='$this->documentType' AND CalculatedLawStatus='$calculatedLawStatus'";
		$hasExistRecordResult = mysql_query($query);
		if (mysql_num_rows($hasExistRecordResult) > 0){
			$shouldAddLog = false;
		}
		
		if ($shouldAddLog){
			$forYear = $this->companyInfo->beginLitigationYear;
			if ($this->companyInfo->lawStatus == 0 && is_null($this->companyInfo->beginLitigationYear)){
				$forYear = date('Y');	
			}
			
			if ($this->adjustLawStatusWeight($calculatedLawStatus) > $this->adjustLawStatusWeight($this->companyInfo->lawStatus)){
				$this->updateLawStatus($calculatedLawStatus, $forYear);
				$actualLawStatus = $calculatedLawStatus;
			}else{
				$actualLawStatus = $this->companyInfo->lawStatus;
			}
			$this->insertLawStatusLogForAdd($calculatedLawStatus, $actualLawStatus, $forYear);
		}
	}
	
	private function adjustLawStatusWeight($lawstatus){
		return $lawstatus >= 10 ? $lawstatus / 10 : $lawstatus;
	}
	
	public function delete(){
		if (!$this->isValid){
			return;	
		}
		
		$addedLog = $this->getAddedLawStatusLog($this->documentType, $this->documentId);
		if ($addedLog !== false && $addedLog['ForYear'] == $this->companyInfo->beginLitigationYear){
			//เปลี่ยนสถานะของ log ที่บันทึกตอน add ให้กลายเป้น cancelled add
			executeUpdate(
			'company_lawstatus_log',
			"ChangeType='".LAWSTATUS_LOG_CHANGE_TYPE_ADD."'
					AND DocumentType='".mysql_real_escape_string($this->documentType)."'
					AND DocumentID='".mysql_real_escape_string($this->documentId)."'",
									array('ChangeType' => LAWSTATUS_LOG_CHANGE_TYPE_CANCELLED_ADD));
			
			$highestLawStatusLog = $this->getHighestLawStatusLog();
			
			$forYear = NULL;
			$calculatedLawStatus = 0;
			if ($highestLawStatusLog !== false){
				$calculatedLawStatus = $highestLawStatusLog['CalculatedLawStatus'];
				$forYear = $highestLawStatusLog['ForYear'];
			}
			$actualLawStatus = $calculatedLawStatus;
			$this->updateLawStatus($calculatedLawStatus, $forYear);

			$this->insertLawStatusLogForDelete($calculatedLawStatus, $actualLawStatus, $this->companyInfo->beginLitigationYear);
		}
// 		สำหรับเอกสารอื่นๆ นอกจากใบเสร็จ
// 		เวลายกเลิก/ลบ (delete) ส่งค่า CID, DocumentID, DocumentType
// 		จะบันทึกรายการใหม่ขึ้นมาและเปลี่ยนรายการเดิมให้ ChangeType = 'cancelled add'
// 	ถ้า ForYear เดิม ไม่ตรงกับ ForYear ใน Company ไม่ต้องเปลี่ยนสถานะ
// 			ถ้า LawStatus เป็น 0/9 ให้ใช้สถานะเดิม
// 			ถ้าไม่เป็น 0/9 ให้ดูจากรายการที่เป็น add เลือกเอาสถานะที่สูงที่สุด ดูจาก CalculatedLawStatus โดยไม่รวม 0/9 ในกรณีที่ไม่มีให้ใช้ 1

// 		สำหรับใบเสร็จ
// 		เวลายกเลิก/ลบ (delete) ส่งค่า CID, DocumentID, DocumentType
// 		จะบันทึกรายการใหม่ขึ้นมาและเปลี่ยนรายการเดิมให้ ChangeType = 'cancelled add'
// 	ถ้า ForYear เดิม ไม่ตรงกับ ForYear ใน Compan-y ไม่ต้องเปลี่ยนสถานะ
// 			ถ้า LawStatus ไม่เป็น 0/9 ให้ใช้สถานะเดิม
// 			ถ้าเป็น 9 ให้ดูจากรายการที่เป็น add เลือกเอาสถานะที่สูงที่สุด ดูจาก CalculatedLawStatus โดยไม่รวม 0/9 ในกรณีที่ไม่มีให้ใช้ 1
		
	}
}

function lawstatus_add($company_id, $document_id, $document_type, $user_id){
	$updater = new LawStatusUpdater($company_id, $document_id, $document_type, $user_id);
	$updater->add();
}

function lawstatus_delete($company_id, $document_id, $document_type, $user_id){
	$updater = new LawStatusUpdater($company_id, $document_id, $document_type, $user_id);
	$updater->delete();
}