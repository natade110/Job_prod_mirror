<?php 
require_once "nepfund_webservice_message.php";
require_once "nepfund_webservice_config.php";
include_once __DIR__."/../functions.php";

class OutstandingPayment{
	/**
	 * @var double
	 **/
	public  $OutstandingAmount;
	/**
	 * @var double
	 **/
	public  $Interest;
	/**
	 * @var double
	 **/
	public  $OutstandingInterest;
	/**
	 * @var DateTime
	 **/
	public  $StartInterestDate;

}

class Lawfulness{
	/**
	 * @var int
	 * */
	public $LID;
	/**
	 * @var int
	 * */
	public $Year;
	/**
	 * @var int
	 * */
	public $CID;
	/**
	 * @var string
	 * */
	public $CompanyCode;
	/**
	 * @var string
	 * */
	public $BranchCode;
	/**
	 * @var string
	 * */
	public $CompanyName;
	/**
	 * @var int
	 * */
	public $Employees;
	/**
	 * @var double
	 * */
	public $Province54Wage;
	/**
	 * @var int
	 * */
	public $CountLaw33;
	/**
	 * @var int
	 * */
	public $CountLaw35;

}

class Payment{
	/**
	 * @var int
	 */
	public $LID;
	/**
	 * @var double
	 */
	public $ReceiptAmount;
	/**
	 * @var DateTime
	 */
	public $ReceiptDate;
}

class CalculatedPaidAmount{
	/**
	 * 
	 * @var double
	 */
	public $PaidPrincipleAmount;
	/**
	 * 
	 * @var double
	 */
	public $PaidInterestAmount;
}

class CalculatedPayment{

	/**
	 * Get list of outstanding debt
	 * @param string $companyCode
	 * @param string[] $branchCodes
	 * @param int[] $years
	 * @param bool $isFilterLawfulStatus true for filter lawfulSatus is 0 or 1
	 * @param DateTime $dateToCalculateInterest
	 * @return OutstandingDebt[]
	 */
	public function getOutstandingDebts($companyCode, array $branchCodes, array $years = NULL, $isFilterLawfulStatus, $dateToCalculateInterest = NULL){
		$companySql = "";
		$whereCompany = ($isFilterLawfulStatus)? " WHERE lawfulness.LawfulStatus in(0,2) AND " : " WHERE ";
		$outstandingDebts = array();
		$outstandingDebt;

		$companyCode = trim($companyCode);
		$whereCompany .= " CompanyCode = '".mysql_real_escape_string($companyCode)."'";
		
		if (!is_null($branchCodes)){
			$branchCode = "";
			
				for($i = 0; $i < count($branchCodes); $i++){
					$branchCode .= ",'".mysql_real_escape_string($branchCodes[$i])."'";
				}

			if ($branchCode != ""){
				$whereCompany .= (" AND BranchCode in(".substr($branchCode, 1).")");
			}			
		}
		
		if(!is_null($years)){
			$whereYear = "";
			for($i = 0; $i < count($years); $i++){
				$whereYear .= ",$years[$i]";
			}
			
			if ($whereYear != ""){
				$whereCompany .= (" AND Year in(".substr($whereYear, 1).")");
			}
		}
		

		/* select company from lawfulness table */
		$companySql = "SELECT lawfulness.LID, lawfulness.Year, company.CID, company.CompanyCode, company.BranchCode, company.CompanyNameThai AS CompanyName";
		$companySql .= ", lawfulness.Employees, provinces.province_54_wage AS Province54Wage, lawfulness.Hire_NumofEmp AS CountLaw33, law35.CountLaw35";
		$companySql .= " FROM company INNER JOIN lawfulness ON company.CID = lawfulness.CID LEFT JOIN provinces ON company.Province = provinces.province_id";
		$companySql .= " LEFT JOIN ( SELECT  lawfulness.LID, count(*) AS CountLaw35 FROM lawfulness INNER JOIN curator ON lawfulness.LID = curator.curator_lid WHERE curator.curator_parent = 0 GROUP BY  lawfulness.LID)law35 ON lawfulness.LID = law35.LID";
		$companySql .=   $whereCompany;
		$companySql .= " ORDER BY lawfulness.Year, company.CompanyCode, company.BranchCode ASC";

		$companies = array();
		$company;
		$companyResult = mysql_query($companySql);
		while ($company = mysql_fetch_object($companyResult, "Lawfulness")){
			array_push($companies, $company);
		}
		mysql_free_result($companyResult);

		/* select ratio and wage form vars table */
		$vars = array();
		$varsSql = "SELECT var_name, var_value FROM vars WHERE var_name like 'ratio_%' OR var_name like 'wage_%' ";
		$varResult = mysql_query($varsSql);
		while ($varRow = mysql_fetch_row($varResult)){
			$vars[$varRow[0]] = $varRow[1];
		}
		mysql_free_result($varResult);


		/* calulated payment from payment table */
		$employees; $countLaw33; $countLaw34; $countLaw35;
		$ratio; $ratioKey; $wage; $wageKey; $year;
		$startAmount; $principleAmount; $totalAmount; $interest; $outstandingInterest;
		$startInterestDate; $interestDate; $receiptDate;
		$paidAmount; $paidInterestDate; $outstandingAmount;
		$outstandingPayment;

		$paymentSql = ""; $paymentResult;
		$paymentSqlColumn = "SELECT payment.LID,  receipt.Amount AS ReceiptAmount, receipt.ReceiptDate  FROM payment INNER JOIN receipt on payment.RID = receipt.RID";
		for ($i = 0; $i < count($companies); $i++){
			$company = $companies[$i];
			$employees = $company->Employees;
			$countLaw33 = $company->CountLaw33;
			$countLaw35 = (is_null($company->CountLaw35))? 0 : $company->CountLaw35;
			$year = $company->Year;

			if ($year > 2011){
				$ratio = 100;

				$wageKey = "wage_".$year;
				$wage = (is_null($vars[$wageKey]))? 300 : $vars[$wageKey];
			}else{
				$ratioKey = "ratio_".$year;
				$ratio = (is_null($vars[$ratioKey]))? 200 : $vars[$ratioKey];

				$wage = $company->Province54Wage/2;
			}
			
			$countLaw34 = $this->getNumberOfLaw34($ratio, $employees, $countLaw33, $countLaw35);
			
			$startAmount = $countLaw34 * $wage * 365; /* เงินต้น */
			$startInterestDate = new DateTime($year."-01-31 0:0:0"); /* เริ่มคิดดอกเบี้ยปีวันที่ 1 กุมภาพันธ์  และเริ่มมีค่าปรับปี 2555 */
			$receiptDate = new DateTime();

			$outstandingAmount = $startAmount;
			$outstandingInterest = 0;
			$interest = 0;

			$paymentSql = "$paymentSqlColumn WHERE receipt.is_payback = 0 AND payment.LID = $company->LID";
			
			if ($dateToCalculateInterest !== NULL){
				$paymentSql .= ' AND receipt.ReceiptDate<=\''.$dateToCalculateInterest->format('Y-m-d').'\'';
			}
			
			$paymentSql .= " ORDER BY receipt.ReceiptDate";
			$paymentResult = mysql_query($paymentSql);
			
			//print_r("<br />");
			while ($paymentRaw = mysql_fetch_object($paymentResult, "Payment")){
				$paidAmount = $paymentRaw->ReceiptAmount;
				$receiptDate = new DateTime($paymentRaw->ReceiptDate);

				$outstandingPayment = $this->getOutstandingPayment($year, $startInterestDate, $receiptDate, $outstandingAmount, $outstandingInterest, $paidAmount);
				$outstandingAmount = $outstandingPayment->OutstandingAmount;
				$outstandingInterest = $outstandingPayment->OutstandingInterest;
				$startInterestDate = $outstandingPayment->StartInterestDate;
			}
			mysql_free_result($paymentResult);

			$paidAmount = 0;
			if ($dateToCalculateInterest == NULL){
				$receiptDate = new DateTime();
			}else{
				$receiptDate = $dateToCalculateInterest;
			}
			$outstandingPayment = $this->getOutstandingPayment($year, $startInterestDate, $receiptDate, $outstandingAmount, $outstandingInterest, $paidAmount);
			
			$outstandingAmount = $outstandingPayment->OutstandingAmount;
			$interest = round($outstandingPayment->Interest + $outstandingPayment->OutstandingInterest, 2);				

			if($outstandingAmount > 0){
				$outstandingDebt = new OutstandingDebt();
				$outstandingDebt->Year = $year;
				$outstandingDebt->CompanyCode = $company->CompanyCode;
				$outstandingDebt->BranchCode = $company->BranchCode;
				$outstandingDebt->CompanyName = $company->CompanyName;
				$outstandingDebt->InterestAmount = $interest;
				$outstandingDebt->PrincipleAmount = $outstandingAmount;
				$outstandingDebt->TotalAmount = round($outstandingAmount + $interest, 2);	
				
				array_push($outstandingDebts, $outstandingDebt);
			}
		}
		

		return $outstandingDebts;
	}

	/** 
	 * Get list of outstanding debt
	 * @param CalculatedReceipt[] $receipts or SavedReceipt[] $receipts or Lawfulness[]  $receipts
	 * @param bool $isFilterLawfulStatus : true for filter lawfulSatus is 0 or 1
	 * @return OutstandingDebt[] 
	 */
	public function getOutstandingDebtsForRecheck(array $receipts, $isFilterLawfulStatus){
		$branchCodes = array();
		$years = array();
		$$receipt;
		for ($i = 0; $i < count($receipts); $i++){
			$receipt = $receipts[$i];
			array_push($branchCodes, $receipt->BranchCode);
			array_push($years, $receipt->Year);
		}
		$receipt = $receipts[0];
		$companyCode = $receipt->CompanyCode;
		$outstandingDebts = $this->getOutstandingDebts($companyCode, $branchCodes, $years, $isFilterLawfulStatus);

		$key; $outstandingDebt;
		$outstandingDebtsResult  = array();
		for($i = 0; $i < count($outstandingDebts); $i++){
			$outstandingDebt = $outstandingDebts[$i];
			$key = $outstandingDebt->Year.$outstandingDebt->BranchCode;
			$outstandingDebtsResult[$key] = $outstandingDebt;
		}

		return $outstandingDebtsResult;
	}
	
	
	/**
	 * Calculated paid amount and paid interest
	 * @param double $paidTotalAmount
	 * @param double $principleAmount
	 * @param double $interestAmount
	 * @return CalculatedPaidAmount
	 */
	public function calculatePaidAmount($paidTotalAmount, $principleAmount, $interestAmount){
		$result = new CalculatedPaidAmount();
		$paidAmount = 0; $paidInterest = 0;
		if ($paidTotalAmount >= $interestAmount){
			$paidInterest = $interestAmount;
			$paidAmount = ($paidTotalAmount > ( $principleAmount + $interestAmount))? $principleAmount : ($paidTotalAmount - $interestAmount);
		}else{
			$paidInterest = $paidTotalAmount;
		}

		$result->PaidInterestAmount = round($paidInterest, 2);
		$result->PaidPrincipleAmount = round($paidAmount, 2);
		
	
		return $result;
	}
	/**
	 * Get outstanding payment (interest, outstanding interest, outstanding amount and datetime of payment)
	 * @param int $lawfulnessYear
	 * @param DateTime $startInterestDate
	 * @param DateTime $receiptDate
	 * @param double $outstandingAmount
	 * @param double $outstandingInterest
	 * @param double $paidAmount
	 * @return OutstandingPayment
	 */
	public function getOutstandingPayment($lawfulnessYear, $startInterestDate, $receiptDate, $outstandingAmount, $outstandingInterest, $paidAmount){
		$interest = 0;
// 		print_r("<br />");

// 		print_r($outstandingAmount); print_r(" : "); 
		/* คำนวณดอกเบี้ย */
		if(($receiptDate <= $startInterestDate) || ($lawfulnessYear <= 2011)){
			/* จ่ายภายใน 31 มกราคม เริ่มมีดอกเบี้ยปี 2555 */
			$interest = 0;
		}else{
			$interestDate = date_diff($receiptDate, $startInterestDate);
			$interestDate = $interestDate->days; /* จน. วันที่ค้างจ่าย */

			$interest = (($outstandingAmount * 0.075)/365) * $interestDate; /* ดอกเบี้ย */
			$interest = round($interest, 2);

			$startInterestDate = new DateTime($receiptDate->format("Y-m-d 0:0:0"));
		}

		$interestOfPayment = $interest;

		/* คำนวณการจ่าย */
		if ($paidAmount > 0){
			if ($paidAmount > ($interest + $outstandingInterest)){
				$paidAmount = $paidAmount - $interest - $outstandingInterest;
				$interest = 0;
				$outstandingInterest = 0;
			}else{
				$interest = $interest - $paidAmount;
				$outstandingInterest += $interest;
				$paidAmount = 0;
				$interest = ($interest < 0)? 0 : $interest;
			}
		}


		$outstandingAmount = $outstandingAmount - $paidAmount;	
		$outstandingAmount = ($outstandingAmount < 0)? 0 : $outstandingAmount;

		$payment = new OutstandingPayment();
		$payment->Interest = $interestOfPayment;
		$payment->OutstandingAmount = round($outstandingAmount, 2);
		$payment->OutstandingInterest = round($outstandingInterest, 2);
		$payment->StartInterestDate = $startInterestDate;
				
		//error_log(" Interest : $payment->Interest");
		//error_log(" OutstandingInterest : $payment->OutstandingInterest");
		//error_log(" OutstandingAmount : $payment->OutstandingAmount");	
			
		return $payment;
	}

	/**
	 * For reseting lawfulstatus when user call cancel payment
	 * @param string $cancelPaymentID
	 * @return $isUpdate: return true if update status is success 
	 */
	public function  resetLawfulStatus($cancelPaymentID){		
		$lawfulStatus = 0; $payStatus = 0;
		$isUpdated = false;
		$errorMessage = "";
		
		/* select lawfulness */
		$lawfulnessSql = "
			SELECT law.LID, law.Year, com.CID, com.CompanyCode, com.BranchCode AS BranchCode, '' AS CompanyName, 0 AS Province54Wage, law.Employees, law.Hire_NumofEmp AS CountLaw33, law35.CountLaw35
			FROM cancelled_payment cp			
			INNER JOIN lawfulness law on cp.LID = law.LID
			INNER JOIN company com on law.CID = com.CID
			LEFT JOIN (
				SELECT  lawfulness.LID, count(*) AS CountLaw35 FROM lawfulness INNER JOIN curator ON lawfulness.LID = curator.curator_lid WHERE curator.curator_parent = 0 GROUP BY  lawfulness.LID
				)law35 ON law.LID = law35.LID
			where cp.NEPFundPaymentID = $cancelPaymentID";
	
		$lawfulnessResult = mysql_query($lawfulnessSql);
		$lawfulnessList = array();
		while ($lawfulness = mysql_fetch_object($lawfulnessResult, "Lawfulness")){
			array_push($lawfulnessList, $lawfulness);
		}
		mysql_free_result($lawfulnessResult);
		
		$receiptWebServiceUserId = $this->getReceiptWebServiceUser();
		
		if(count($lawfulnessList) > 0){
			/* select ratio form vars table */
			$vars = array();
			$varsSql = "SELECT var_name, var_value FROM vars WHERE var_name like 'ratio_%'";
			$varResult = mysql_query($varsSql);
			while ($varRow = mysql_fetch_row($varResult)){
				$vars[$varRow[0]] = $varRow[1];
			}
			mysql_free_result($varResult);
			
			$outstandingDebts = $this->getOutstandingDebtsForRecheck($lawfulnessList, false);
			$outstandingDebt; $lawfulness;
			$ratio; $year;
			$employees; $countLaw33; $countLaw34; $countLaw35;
			
			$isLaw33 = false;
			$key;
			
			$countPaymentSql; $countPaymentResult;
			$countPayment = 0;
			
			$lawfulnessUpdateSql;
			$modDate; $modifyHistorySql;
			
			for($i = 0; $i < count($lawfulnessList); $i++){
				$lawfulness = $lawfulnessList[$i];
				$year = $lawfulness->Year;
				$employees = $lawfulness->Employees;
				$countLaw33 = $lawfulness->CountLaw33;
				$countLaw35 = (is_null($lawfulness->CountLaw35))? 0 : $lawfulness->CountLaw35;
					
				if ($year > 2011){
					$ratio = 100;
			
				}else{
					$ratioKey = "ratio_".$year;
					$ratio = (is_null($vars[$ratioKey]))? 200 : $vars[$ratioKey];
				}
					
				/* เข้าข่ายต้องปฏิบัติตามกฎหมายหรือไม่ */
				$isLaw33 = ($employees >= $ratio);
					
				/* จำนวนคนที่ต้องรับเพิ่ม  */
				$countStartLaw33 = $this->getNumberOfLaw34($ratio, $employees, 0, 0);
			
				/* ข้อมูลการค้างชำระ */
				$key = $year.$lawfulness->BranchCode;
				$outstandingDebt = $outstandingDebts[$key];
					
				/* find payment history */
				$countPaymentSql = "SELECT count(receipt.RID)
				FROM receipt
				INNER JOIN payment ON receipt.RID = payment.RID
				WHERE payment.LID = $lawfulness->LID AND receipt.is_payback = 0";
				$countPaymentResult = mysql_query($countPaymentSql);
			
				while ($row = mysql_fetch_row($countPaymentResult)){
					$countPayment = $row[0];
				}
					
				/* find lawfulness status */				
				if($isLaw33){					
					if((($countLaw33 + $countLaw35) >= $countStartLaw33) || ($outstandingDebt->PrincipleAmount == 0)){
						$lawfulStatus = 1; /* ปฏิบัติตามกฎหมาย */
						
					}else if( ($countLaw33 > 0) || ($countLaw35 > 0) || ($countPayment > 0)){
						$lawfulStatus = 2; /* ปฏิบัติตามกฏหมายแต่ไม่ครบตามอัตราส่วน */
				
					}else{
					 	$lawfulStatus = 0; /* ไม่ปฏิบัติตามกฎหมาย */
					}
			
				}else{
					$lawfulStatus = 3; /* ไม่เข้าข่ายจำนวนลูกจ้าง */
				}
				
				/* สถานะทำตามมาตรา 34 */
				$payStatus = ($countPayment > 0)? 1 : 0;
				
				
				 /* update ข้อมูลตาราง lawfulness */				 	
				 $lawfulnessUpdateSql = "
				 UPDATE lawfulness SET
				  LawfulStatus = $lawfulStatus,
				 pay_status = $payStatus
				 WHERE LID = $lawfulness->LID";
				 
				 
				 	
				 $lawfulnessUpdateResult = mysql_query($lawfulnessUpdateSql);
				 	
				 $isUpdated = (!$lawfulnessUpdateResult)? false : true;
				 
				 if(!$lawfulnessUpdateResult){
				 	$isUpdated = false;
				 	$errorMessage = "การแก้ไขข้อมูลสถานะสถานประกอบการเกิดข้อผิดผลาด";
				 	break;
				 }else{
				 	
				 	/* update ข้อมูลตาราง modify_history */
				 	$this->addModifyHistoryByCompanyID($lawfulness->CID, $receiptWebServiceUserId);
				 	
				 	$isUpdated = true;
				 }
			}
			
		}else{
			$errorMessage = "ไม่พบข้อมูลรหัสการชำระเงินในรายการยกเลิกการชำระเงิน";
		}
	
		
		
		$return = new StdClass();
		$return->IsUpdated = $isUpdated;
		$return->ErrorMessage = $errorMessage;
	
		
		return $return;
	}
	
	private function getNumberOfLaw34($ratio, $employees, $countLaw33, $countLaw35){
		$countLaw34 = 0;
		$countLaw34 = floor($employees / $ratio);
		$countLaw34 = (($employees % $ratio) > ($ratio/2))? ($countLaw34 + 1) : $countLaw34;
		
		$countLaw34 = $countLaw34 - $countLaw33 - $countLaw35;
		return  ($countLaw34 < 0)? 0 : $countLaw34;
	}
	
	/**
	 * @param SavedReceipt[] $receipts
	 * @return $lids : [YYYY1BranchCode1:lid1, YYYY2BranchCode2: lid2]
	 */
	public function getLidList($receipts){
		$lids = array();
		$years = array();
		$companycode = "";
		$branches = array();
		$receipt = new SavedReceipt();
		
		if($receipts != null){
			$companycode = $receipts[0]->CompanyCode;
			for($i = 0; $i < count($receipts); $i++ ){
				$receipt = $receipts[$i];
				array_push($years, $receipt->Year);
				array_push($branches, $receipt->BranchCode);
			}
			
			$branchcode = "";
			if(count($branches) == 1){
				$branchcode = ("'".$branches[0]."'");
			}else{
				$branchcode = "'".implode("','", $branches)."'";
			}			
			
			$year = implode(",", $years);
			$sql = "SELECT l.Year, l.LID, c.BranchCode
					FROM lawfulness l
					INNER JOIN company c ON l.CID = c.CID
					WHERE (c.CompanyCode = '$companycode') 
						AND (c.BranchCode in($branchcode))
						AND (l.Year in($year))";			
			
			$lidResult = mysql_query($sql);
			$key = "";
			while ($row = mysql_fetch_array($lidResult)){
				$key = $row["Year"]. $row["BranchCode"];
				$lids[$key] = $row["LID"];
			}
		}
		
		return  $lids;
	}
	
	public function addModifyHistoryByCompanyID($cid, $receiptWebServiceUserId){
		
		$modDate = new DateTime();
		$modDate = $modDate->format("Y-m-d H:i:s");
		$modifyHistorySql = "INSERT INTO modify_history (mod_cid, mod_date, mod_type, mod_user_id)
		VALUES( $cid, '$modDate', 1, $receiptWebServiceUserId) ";
		
		mysql_query($modifyHistorySql);
	}
	
	public function addModifyHistoryByReceiptInfo($year, $companyCode, $branchCode, $receiptWebServiceUserId){
		
		$modDate = new DateTime();
		$modDate = $modDate->format("Y-m-d H:i:s");
		$modifyHistorySql = "INSERT INTO modify_history (mod_cid, mod_date, mod_type, mod_user_id)
			SELECT mod_cid, mod_date, mod_type, mod_user_id FROM (SELECT com.CID mod_cid,  '$modDate' mod_date, 1 mod_type, $receiptWebServiceUserId mod_user_id FROM lawfulness law INNER JOIN company com ON law.CID = com.CID
							WHERE law.Year = $year AND com.CompanyCode = '$companyCode' AND com.BranchCode = '$branchCode' 
							) AS tmp
			LIMIT 1;";
		
		mysql_query($modifyHistorySql);
		
	}
	
	public function getReceiptWebServiceUser(){
		$userId = 0;
		$sql = "SELECT user_id FROM users WHERE user_name = 'receipt_websevice'";
		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_row($result)){
			$userId = $row[0];
		}
		
		if($userId == 0){
			throw new Exception('กรุณาเพิ่มผู้ใช้งานสำหรับการบริการเว็บเซอร์วิซ');
		}
		
		return $userId;		
	}
}


 
class NepFundService
{
	/**
	 * @var int
	 */
	private $logid;

	function ListCompany(ListCompanyRequest $request)
	{
		$result = new ListCompanyResponse();
		$result->TransactionID = $request->TransactionID;

		if ($this->beginDB($result) && $this->validateTransaction($result, $request) && $this->beginLog($result, 'ListCompany', $request)){
			try {

				$provinceCode = $request->ProvinceCode;
				$companyCode =  $request->CompanyCode;
				$companyName = $request->CompanyName;

				$where = " WHERE CID IN (SELECT law.CID FROM lawfulness law WHERE law.LawfulStatus in (0,2)) 
						         AND CompanyTypeCode < 200 ";
				$whereCompanyCode = "";
				$whereCompanyName = "";

				if (!empty($provinceCode)){
					$provinceCode = mysql_real_escape_string(trim($provinceCode));
					$where .= " AND (province_code = '".$provinceCode."')";
				}

				if (!empty($companyCode)){
					$companyCode = mysql_real_escape_string(trim($companyCode));
					$where .= " AND (CompanyCode like '%".$companyCode."%')";
				}

				if(!empty($companyName)){
					$companyNameContains = preg_split("/[\s]+/", $companyName);
					$whereCompanyName = "";

					for ($i = 0; $i < count($companyNameContains); $i++){
						$companyName = $companyNameContains[$i];
						$companyName = mysql_real_escape_string($companyName);
						$whereCompanyName .= (" AND (CompanyNameThai like '%".$companyName."%')");
					}

					$where .= $whereCompanyName;
				}

				$query = "SELECT CompanyNameThai AS CompanyName, CompanyCode, BranchCode, Address1 AS Address, Road, Subdistrict, District, province_name AS Province";
				$query .= " FROM company INNER JOIN provinces ON province = province_id";
				$query .= $where;
				$query .= " ORDER BY  CompanyCode, BranchCode asc";

				$queryResult = mysql_query($query);

				if (!$queryResult){
					$result->IsSuccess = false;
					$result->ErrorMessage = "เกิดข้อผิดพลาดในการดึงข้อมูลจากฐานข้อมูล";
				}else{

					$companies = array();
					while ($company = mysql_fetch_object($queryResult, "CompanyInfo")) {
						array_push($companies, $company);
					}
					mysql_free_result($queryResult);

					$result->IsSuccess = true;
					$result->Companies = $companies;
				}
			} catch (Exception $e) {
				$this->handleUnexpectedException($result, $e);
			}

			$this->commitLog($result);
		}


		return $result;
	}

	function ListOutstandingDebt(ListOutstandingDebtRequest $request){
		$result = new ListOutstandingDebtResponse();
		$result->TransactionID = $request->Transaction;

		if ($this->beginDB($result) && $this->validateTransaction($result, $request) && $this->beginLog($result, 'ListOutstandingDebt', $request)){
			try {

				$outstandingDebts = array();
				if ((!is_null($request)) && (!empty($request->CompanyCode))){
					$calculatePayment = new CalculatedPayment();
					$outstandingDebts = $calculatePayment->getOutstandingDebts($request->CompanyCode, $request->BranchCodes, null, true);
				}

				$result->IsSuccess = true;
				$result->OutstandingDebts = $outstandingDebts;

			}catch(Exception $e){
				$this->handleUnexpectedException($result, $e);
			}

			$this->commitLog($result);
		}

		return $result;
	}

	function CalculateReceiptInfo(CalculateReceiptInfoRequest $request){
		$result = new CalculateReceiptInfoResponse();
		$result->TransactionID = $request->TransactionID;

		if ($this->beginDB($result) && $this->validateTransaction($result, $request) && $this->beginLog($result, 'CalculateReceiptInfo', $request)){
			try {
				
				$baseReceipts = array();
				$newReceipts = array();
				$newReceipt;
				$isValid = true;
				$errorMsage = ""; 

				$baseReceipts = $request->Receipts;

				if (!is_null($baseReceipts)){
					$calculatePayment = new CalculatedPayment();
					$receipt; $outstandingDebt;
					$branchCode; $year; $key;
					$principleAmount; $interestAmount; 
					$paidPrincipleAmount;
					$paidTotalAmount; 
					$outstandingDebts = $calculatePayment->getOutstandingDebtsForRecheck($baseReceipts, true);
					
					for($i = 0; $i < count($baseReceipts); $i++){
						$receipt = $baseReceipts[$i];

						$key = $receipt->Year.$receipt->BranchCode;
						$outstandingDebt = $outstandingDebts[$key];

						$principleAmount = $receipt->PrincipleAmount;
						$interestAmount = $receipt->InterestAmount;						
						
						if (($outstandingDebt != null) && 
								($outstandingDebt->PrincipleAmount > 0) &&
								($principleAmount == $outstandingDebt->PrincipleAmount) &&
								($interestAmount == $outstandingDebt->InterestAmount)){
							$paidTotalAmount = $receipt->PaidTotalAmount;

// 							print_r("<br />");print_r($paidTotalAmount); 
// 							print_r(":"); print_r($principleAmount); 
// 							print_r(":"); print_r($interestAmount);

							$paidAmountObj = $calculatePayment->calculatePaidAmount($paidTotalAmount, $principleAmount, $interestAmount);
							
							$newReceipt = new CalculatedReceipt();
							$newReceipt->Year = $receipt->Year;
							$newReceipt->CompanyCode = $receipt->CompanyCode;
							$newReceipt->BranchCode = $receipt->BranchCode;
							$newReceipt->CompanyName = $receipt->CompanyName;
							$newReceipt->InterestAmount = $interestAmount;
							$newReceipt->PrincipleAmount = $receipt->PrincipleAmount;
							$newReceipt->PaidInterestAmount =  $paidAmountObj->PaidInterestAmount;
							$newReceipt->PaidPrincipleAmount = $paidAmountObj->PaidPrincipleAmount;
							$newReceipt->PaidTotalAmount = $receipt->PaidTotalAmount;
							$newReceipt->TotalAmount = $receipt->TotalAmount;

							array_push($newReceipts, $newReceipt);
						}else{
							$isValid = false;
							$newReceipts = $baseReceipts;
							$errorMsage = "ข้อมูลบางรายการมียอดเงินต้นหรือดอกเบี้ยไม่ถูกต้อง";
							break;
						}
					}
				}

				$result->IsSuccess = $isValid;
				$result->Receipts = $newReceipts;
				$result->ErrorMessage = $errorMsage;

			} catch (Exception $e) {
				$this->handleUnexpectedException($result, $e);
			}

			$this->commitLog($result);
		}

		return $result;
	}

	function SaveReceipt(SaveReceiptRequest $request){
		$result = new SaveReceiptResponse();
		$result->TransactionID = $request->TransactionID;
		$result->PaymentID = $request->PaymentID;
		$result->Receipts = $request->Receipts;

		$errorMsg = "";
		$isValid = false;

		if ($this->beginDB($result) && $this->validateTransaction($result, $request) && $this->beginLog($result, 'SaveReceipt', $request)){
			if (!empty($request->PaymentID) && (!is_null($request->Receipts) && ($this->checkDuplicatePayment($result, $request)))){
				try {

					$receipts = array();
					$receipt; $outstandingDebt;
					$branchCode; $year; $key;
					$principleAmount; $interestAmount;
					$paidPrincipleAmount; $paidInterestAmount;
					$paidTotalAmount;
					$calculatePayment = new CalculatedPayment();
					
					$lawfulStatus;
					$lawfulnessUpdate;
					
					$cid; $cidSql; $cidResult;
					$companyCode;
					$branchCode;
					
					$receipts = $request->Receipts;

					$checkPaymentMethod = $this->checkPaymentMethod($receipts);
					
					
					if ($checkPaymentMethod->IsValid){

						$outstandingDebts = $calculatePayment->getOutstandingDebtsForRecheck($receipts, true);
						$outstandingDebt; $paymentInsert; $receiptInsert;
						$nepFundPaymentID; $paymentDate; $receiptNote;
						$receiptID; $receiptYear;
						$bankNames = $checkPaymentMethod->BankNames;
						$bankId;

						$sessUserID = $calculatePayment->getReceiptWebServiceUser();
						$lids = $calculatePayment->getLidList($receipts);
						
						if($lids != null){
							foreach ($lids as $key => $lid){
								doLawfulnessFullLog($sessUserID, $lid, "Webservices Before Save Receipt");
							}
							
						}else{
							throw new Exception("ไม่พบข้อมูล LID");
						}
						
						
						$this->begin();
						$isValid = true;						
						$lid = 0;
						$receiptWebServiceUserId = $calculatePayment->getReceiptWebServiceUser();
						for($i = 0; $i < count($receipts); $i++){
							$receipt = $receipts[$i];

							$principleAmount = $receipt->PrincipleAmount;
							$interestAmount = $receipt->InterestAmount;
							$key = $receipt->Year.$receipt->BranchCode;
							$outstandingDebt = $outstandingDebts[$key];

							if (($principleAmount == $outstandingDebt->PrincipleAmount) &&
									($interestAmount == $outstandingDebt->InterestAmount)){

								$bankId = (empty($receipt->BankName))? 0 : $bankNames[$receipt->BankName];
								
								if(($receipt->PaymentMethod == "Cheque") && ($bankId == null)){
									$isValid = false;
									$errorMsg = "ไม่พบชื่อธนาคารที่ระบุ";
									break;
								}

								if(gettype($receipt->PaymentDate) == 'object'){
									$paymentDate = $receipt->PaymentDate->format("Y-m-d");
								}else{
									$paymentDate = $receipt->PaymentDate;
								}

								$receiptYear =  $receipt->Year;
								$companyCode = mysql_real_escape_string($receipt->CompanyCode);
								$branchCode = mysql_real_escape_string($receipt->BranchCode);
								
								/*--Save Receipt --*/
								$receiptInsert = "INSERT INTO receipt(Amount, BookReceiptNo, is_payback, NepFundPaymentID,
								PaymentMethod, ReceiptDate, ReceiptNo, ReceiptYear)
								VALUES( $receipt->PaidTotalAmount, '".mysql_real_escape_string($receipt->ReceiptBookNo)."', 0, '".mysql_real_escape_string($request->PaymentID)."',
								'$receipt->PaymentMethod', '$paymentDate', '".mysql_real_escape_string($receipt->ReceiptNo)."', $receiptYear)";

								mysql_query($receiptInsert);
								$receiptID = mysql_insert_id();
								/*-----------------*/
								 
								/*--Save Payment --*/
								$paymentInsert = "INSERT INTO payment(Amount, bank_id, LID, main_flag, PaymentMethod, PaymentDate, RefNo, RID)
								SELECT $receipt->PaidTotalAmount, $bankId, law.LID,  1,
								'$receipt->PaymentMethod','$paymentDate', '".mysql_real_escape_string($receipt->PaymentDocNo)."', '$receiptID'
								FROM lawfulness law INNER JOIN company comp on law.CID = comp.CID
								WHERE law.Year = $receipt->Year AND comp.CompanyCode = '".mysql_real_escape_string($receipt->CompanyCode)."' AND comp.BranchCode = '".mysql_real_escape_string($receipt->BranchCode)."'";

								mysql_query($paymentInsert);

								/*-----------------*/

								/*-- Update Lawfulness -- */
								$lawfulStatus = ($receipt->PaidTotalAmount >= $receipt->TotalAmount)? 1 : 2; /* 1 = ปฏิบัติตามกฎหมาย, 2 = ปฏิบัติตามกฏหมายแต่ไม่ครบตามอัตราส่วน */
		 						
		 						$lid = $lids[$receiptYear.$branchCode];		 						
		 						
		 						$lawfulnessUpdate = "
		 							UPDATE lawfulness SET				 						
			 							LawfulStatus = $lawfulStatus,
			 							pay_status = 1
		 						    WHERE lid = $lid";
		 						
		 						mysql_query($lawfulnessUpdate);
		 						
		 						//error_log("-------------------------------");
		 						//error_log($lawfulnessUpdate);
		 						/*------------------------*/
		 						
		 						/*-- Insert modify_history -- */
		 						$calculatePayment->addModifyHistoryByReceiptInfo($receiptYear, $companyCode, $branchCode, $receiptWebServiceUserId);
								
							}else{
								$isValid = false;
								$errorMsg = "ข้อมูลบางรายการมียอดเงินต้นหรือดอกเบี้ยไม่ถูกต้อง";
								break;
							}
						}					
						
						if ($isValid){
							$this->commit();
						}else {
							$this->rollback();
						}
						
						foreach ($lids as $key => $lid){							
							doLawfulnessFullLog($sessUserID, $lid, "Webservices After Save Receipt");
							
						}
						
						
					}else{
						$isValid = false;
						$errorMsg = $checkPaymentMethod->ErrorMessage;
					}

				} catch (Exception $e) {
					$this->handleUnexpectedException($result, $e);
				}
			}else{
				
				if($result->ErrorMessage ==  null){
					$errorMsg = "ข้อมูลการชำระเงินไม่ถูกต้อง";
				}else{
					$errorMsg = $result->ErrorMessage;
				}
				
			}

			$result->IsSuccess = $isValid;
			$result->ErrorMessage = $errorMsg;

			$this->commitLog($result);
		}

		return $result;
	}

	function CancelPayment(CancelPaymentRequest $request){
		$result = new CancelPaymentResponse();
		$result->TransactionID = $request->TransactionID;
		$result->PaymentID = $request->PaymentID;

		$errorMsg = "";
		$isValid = false;

		if ($this->beginDB($result) && $this->validateTransaction($result, $request) && $this->beginLog($result, 'CancelPayment', $request)){
			$paymentId = mysql_real_escape_string($request->PaymentID);
			if (!empty($paymentId)){
				
				try {
					$calculatedPayment = new CalculatedPayment();
					
					$sessUserID = $calculatedPayment->getReceiptWebServiceUser();					
					$lids = array();
					$lidSql = "SELECT p.LID 
							FROM receipt r 
							INNER JOIN payment p ON r.RID = p.RID
							WHERE NEPFundPaymentID = '$paymentId'";					
					$lidResult = mysql_query($lidSql);
					while ($lidRow = mysql_fetch_array($lidResult)){
						array_push($lids, $lidRow["LID"]);
					}
					for($i = 0; $i < count($lids); $i++){
						
						doLawfulnessFullLog($sessUserID, $lids[$i], "Webservices Before Cancel Payment");
					}
					
					
					$this->begin();
					$sqlInsertCancelPayment = "INSERT INTO cancelled_payment
						(PID, RefNo, bank_id, LID, main_flag, RID, BookReceiptNo, ReceiptNo, Amount, PaymentMethod,
						ReceiptNote, ReceiptYear, ReceiptDate, is_payback, NEPFundPaymentID)
						SELECT p.PID, p.RefNo, p.bank_id, p.LID, p.main_flag,
						r.RID, r.BookReceiptNo, r.ReceiptNo, r.Amount, r.PaymentMethod, r.ReceiptNote, r.ReceiptYear, r.ReceiptDate,
						r.is_payback, r.NEPFundPaymentID
						FROM payment p INNER JOIN receipt r ON p.rid = r.rid
						WHERE r.NEPFundPaymentID = '$paymentId'";
					mysql_query($sqlInsertCancelPayment);

					$sqlDeletePayment = "DELETE p, r FROM payment p INNER JOIN receipt r ON p.rid = r.rid
						WHERE r.NEPFundPaymentID = '$paymentId'";
					mysql_query($sqlDeletePayment);
					
					/* update lawfulness.LawfulStatus status and lawfulness.pay_status */
					
					
					$updatedResult = $calculatedPayment->resetLawfulStatus($paymentId);
					
					$isValid = $updatedResult->IsUpdated;
					if($updatedResult->IsUpdated){
						$this->commit();
					}else{						
						$errorMsg = $updatedResult->ErrorMessage;
						$this->rollback();
					}		

					
					for($i = 0; $i < count($lids); $i++){
						doLawfulnessFullLog($sessUserID, $lids[$i], "Webservices After Cancel Payment");
					}
					
					
				} catch (Exception $e) {
					$this->rollback();
					$isValid = false;
					$errorMsg = $e->getMessage();
				}

			}else{

				$errorMsg = "ข้อมูลการยกเลิกการชำระเงินไม่ถูกต้อง";
			}
			$result->IsSuccess = $isValid;
			$result->ErrorMessage = $errorMsg;
			$this->commitLog($result);
		}

		return $result;
	}

	function Login(LoginRequest $requestObj){
		$response = new LoginResponse();

		if ($this->beginDB($response)){
			$loginSuccess = false;

			try{
				if (WebServiceConfig::validateUser($requestObj->Username, $requestObj->Password)) {
					//สร้าง Transasction ID จาก Database
					$result = mysql_query("select uuid()");
					$row = mysql_fetch_row($result);
					if ($row){
						$transactionId = $row[0];
						mysql_free_result($result);
						$username = mysql_real_escape_string($requestObj->Username);
	
						$query = "INSERT INTO webservice_transaction (TransactionID, Username, CreateTime, LastAccessTime)";
						$query .= " VALUES (unhex(replace('$transactionId', '-', '')), '$username', NOW(), NOW())";
	
						if (mysql_query($query))
						{
							$loginSuccess = true;
							$response->TransactionID = $transactionId;
						}else{
							$response->IsSuccess = false;
							$response->ErrorMessage = "ไม่สามารถเริ่มต้น Transaction ได้";
						}
					}else{
						$response->IsSuccess = false;
						$response->ErrorMessage = "ไม่สามารถเริ่มต้น Transaction ได้";
					}
				}

				$response->IsSuccess = $loginSuccess;
				if (!$loginSuccess && $response->ErrorMessage == null){
					$response->ErrorMessage = "Username/Password ผิด";
				}
			}catch(Exception $ex){
				$this->handleUnexpectedException($response, $ex);
			}
		}

		return $response;
	}

	/**
	 * For validation payment method and bank name
	 * @param SavedReceipt[] $savedReceipts
	 * @return {IsValid  = True/False, BankNames = array(Name => "", ID => ""), ErrorMessage = ""}
	 */
	private function checkPaymentMethod(array $savedReceipts){
		$isValid = true;
		$bankNames = array();

		$whereBankName = "";
		$errorMsg = "";
		foreach ($savedReceipts as $obj){
			
			if (($obj->PaymentMethod == "Cheque") && (!empty($obj->BankName)) && (!empty($obj->PaymentDocNo))){
				$whereBankName .= ",'".mysql_escape_string($obj->BankName)."'";
			}else if($obj->PaymentMethod == "Note" && !empty($obj->PaymentDocNo) && empty($obj->BankName)) {
			}else if($obj->PaymentMethod == "Cash" && empty($obj->BankName) && empty($obj->PaymentDocNo)) {
			}else{
				$isValid = false;
				$bankNames = array();
				break;
			}

			if(!empty($obj->ReceiptNo) && !empty($obj->ReceiptBookNo)){
				$sqlReceipt = "SELECT 1 FROM receipt WHERE BookReceiptNo = '".mysql_escape_string($obj->ReceiptBookNo)."' AND ReceiptNo = '".mysql_escape_string($obj->ReceiptNo)."'";
				$sqlReceiptResult = mysql_query($sqlReceipt);
				$sqlReceiptRowNum = mysql_num_rows($sqlReceiptResult);
				if ($sqlReceiptRowNum >= 1){
					$isValid = false;
					$errorMsg = "ใบเสร็จบางรายการมีอยู่ในระบบแล้ว";
					break;
				}
				if ($sqlReceiptRowNum === false){
					$isValid = false;
					$errorMsg = 'เกิดปัญหาในการเชื่อมต่อกับฐานข้อมูล';
					break;
				}
			}else{
				$isValid = false;
				$bankNames = array();
				break;
			}
		}

		if($isValid){
			if (!empty($whereBankName)){
				$whereBankName = substr($whereBankName, 1);
				$sql = "SELECT bank_name, bank_id FROM bank WHERE bank_name in($whereBankName)";
				$sqlResult = mysql_query($sql);
				while ($row = mysql_fetch_row($sqlResult)){
					$bankNames[$row[0]] = $row[1];
				}
			}
		}else{
			$errorMsg = "ข้อมูลวิธีการจ่ายเงินไม่ถูกต้อง";
		}


		$return = new StdClass();
		$return->IsValid = $isValid;
		$return->BankNames = $bankNames;
		$return->ErrorMessage = $errorMsg;

		return $return;
	}

	/**
	 * Validate the transaction id of $request that it has a correct format and doesn't expired (create time is not exceed 1 hour ago),
	 * return True when transaction id has a correct format and create time less or equal 1 hour  otherwise return False
	 * @param BaseResponse $response a object to store a result and an error message.
	 * @param BaseRequest $request a request that need to validate transaction id. 
	 * @return boolean
	 */
	private function validateTransaction(BaseResponse $response, BaseRequest $request){
		$isValid = false;
		$transactionID = $request->TransactionID;
		if (preg_match("/^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i", $transactionID)){
			$validateSQL = "SELECT 1 FROM webservice_transaction WHERE TransactionID = unhex(replace('$transactionID', '-', ''))";
			$validateSQL .= " AND CreateTime >= ADDTIME(NOW(), '-01:00')";
			$sqlResult = mysql_query($validateSQL);

			if ($sqlResult)
			{
				if (mysql_num_rows($sqlResult) > 0){
					// Update LastAccessTime
					$sqlUpdate = "UPDATE webservice_transaction SET LastAccessTime = NOW() WHERE TransactionID = unhex(replace('$transactionID', '-', ''))";
					mysql_query($sqlUpdate);
					$isValid = true;
				}else{
					$response->IsSuccess = false;
					$response->ErrorMessage = "TransactionID หมดอายุหรือไม่มีในระบบ";
				}
				mysql_free_result($sqlResult);
			}else{
				$response->IsSuccess = false;
				$response->ErrorMessage = "เกิดปัญหากับฐานข้อมูล";
			}
		}else{
			$response->IsSuccess = false;
			$response->ErrorMessage = "TransactionID ไม่ถูกต้อง";
		}
		return $isValid;
	}

	private function checkDuplicatePayment(SaveReceiptResponse $response, SaveReceiptRequest $sqlPayment){
		$isValid = true;
		$paymentID = $sqlPayment->PaymentID;
		
		
		$sqlPayment = "SELECT NepFundPaymentID FROM receipt WHERE NepFundPaymentID = '$paymentID'";
		$sqlPaymentResult = mysql_query($sqlPayment);		
					
		while ($payment = mysql_fetch_row($sqlPaymentResult)){
			$isValid = false;
			$response->IsSuccess = false;
			$response->ErrorMessage = "รหัสของการชำระเงินนี้มีอยู่ในระบบแล้ว";
		}


		return $isValid;
	}

	private function begin(){
		mysql_query("BEGIN");
		//mysql_query("START TRANSACTION");
	}

	private function commit(){
		mysql_query("COMMIT");
	}

	private function rollback(){
		mysql_query("ROLLBACK");
	}

	/**
	 * Begin database connection
	 * @param BaseResponse $response the response that will be used to set an error message when a connection fail. 
	 * @return boolean
	 */
	private function beginDB(BaseResponse $response){
		$hasDbProblem = true;

		$dbLink = mysql_connect(WebServiceConfig::DB_SERVER, WebServiceConfig::DB_USER, WebServiceConfig::DB_PASS);
		if ($dbLink){
			if (mysql_select_db(WebServiceConfig::DB_SCHEMA)){
				mysql_query("SET character_set_client=utf8");   //ตั้งค่าการดึงข้อมูลออกมาให้เป็น utf8
				mysql_query("SET character_set_results=utf8");	//ตั้งค่าการส่งข้อมุลลงฐานข้อมูลออกมาให้เป็น utf8
				mysql_query("SET character_set_connection=utf8"); //ตั้งค่าการติดต่อฐานข้อมูลให้เป็น utf8
				$hasDbProblem = false;
			}else{
				mysql_close($dbLink);
			}
		}

		if ($hasDbProblem && $response != null){
			$response->IsSuccess = false;
			$response->ErrorMessage = "เกิดปัญหาในการเชื่อมต่อกับฐานข้อมูล";
		}

		return !$hasDbProblem;
	}

	private function beginLog(BaseResponse $responseObj, $method, BaseRequest $requestObj){
		$logSuccess = false;

		$transactionID = $requestObj->TransactionID;
		$request = mysql_real_escape_string(json_encode($requestObj));

		$sqlInsert = "INSERT INTO webservice_log(TransactionID, Method, Request, Response, LogTime)";
		$sqlInsert .= " VALUES(unhex(replace('$transactionID', '-', '')), '".$method."', '".$request."', NULL, NOW())";

		if (mysql_query($sqlInsert)){
			$this->logid = mysql_insert_id();
			$responseObj->LogID = $this->logid;
			$logSuccess = true;
		}else{
			$responseObj->IsSuccess = false;
			$responseObj->ErrorMessage = "การบันทึกล็อกมีปัญหา";
		}

		return $logSuccess;
	}

	private function commitLog(BaseResponse $responseObj){
		if ($responseObj != null){
			$response = mysql_real_escape_string(json_encode($responseObj));
			$query = "UPDATE webservice_log SET Response = '$response' WHERE LogID = $this->logid";
			mysql_query($query);
		}
	}

	private function handleUnexpectedException(BaseResponse $responseObj, Exception $ex){
		if ($responseObj != null){
			$responseObj->IsSuccess = false;
			$responseObj->ErrorMessage = "เกิดปัญหาในการทำงาน";
			error_log($ex->getMessage());
			error_log($ex->getTraceAsString());
		}
	}
	
	public function TestConnection(){
		$response = new BaseResponse();
		if ($this->beginDB($response)){
			$response->IsSuccess = true;
		}
		return $response;
	}
}
