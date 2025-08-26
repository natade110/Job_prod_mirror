<?php
require_once 'db_connect.php';
require_once 'ws/nepfund_webservice.php';

$result = new stdClass();


if(isset($_POST["companycode"]) && isset($_POST["branchcode"])){
	$result->Status = 1;
	try {
		$companyCode = $_POST["companycode"];
		$branchCode = array($_POST["branchcode"]);	
		$calDate = new DateTime($_POST["caldate"]);
		$start2011InterestDate = (($_POST["start2011interestdate"] != null) && ($_POST["start2011interestdate"] != ""))? new DateTime($_POST["start2011interestdate"]) : null;
		
		$diffDate = new DateTime($_POST["caldate"]);
		$diffDate = $diffDate->modify("+1 day");
		
		$calc = new CalculatedPayment();
		$startInterestDate = new DateTime();
		$endInterestDate = new DateTime();
		$endInterestDate = $endInterestDate->modify("+1 day");
		
		$data = $calc->getOutstandingDebts($companyCode, $branchCode, null, true, $calDate);	
		$dataItems = array();
		
		$debt = new stdClass();
		$debtItem = new OutstandingDebt();	
		$interestPerDay = 0;
		$paymentPerDay = new OutstandingPayment();
		if(($data != null) && (count($data) > 0)){
			for($i = 0; $i < count($data); $i++){
				$debtItem = $data[$i];		
				
				$diffInterest = round($diffDebtItem->InterestAmount, 2);
				$interest = round($debtItem->InterestAmount, 2);
				
				$debt = new stdClass();
				$debt->Year = $debtItem->Year;
				$debt->CompanyCode = $debtItem->CompanyCode;
				$debt->CompanyName = $debtItem->CompanyName;
				$debt->BranchCode = $debtItem->BranchCode;
				$debt->PrincipleAmount = $debtItem->PrincipleAmount;
				$debt->InterestAmount = $debtItem->InterestAmount;
				$debt->TotalAmount = $debtItem->TotalAmount;
				
				
				$paymentPerDay = $calc->getOutstandingPayment($debtItem->Year, $startInterestDate, $endInterestDate, $debt->PrincipleAmount, 0, 0);
				$interestPerDay = $paymentPerDay->Interest;
				$debt->InterestPerDay = round($interestPerDay, 2);
				
				if(($debt->Year) == 2011 && ($start2011InterestDate != null) && ($start2011InterestDate <= $calDate)){
					
					
					
					$interestDate = date_diff($calDate, $start2011InterestDate);
					$interestDate = ($interestDate->days + 1); /* จน. วันที่คิดดอกเบี้ย */
					
					$interestPerDay =  (($debt->PrincipleAmount * 0.075)/365) * 1;
					$interestPerDay = round($interestPerDay, 2);
					
					$interestAmount = (($debt->PrincipleAmount * 0.075)/365) * $interestDate;
					$interestAmount = round($interestAmount, 2);
					$totalAmount = round(($debt->PrincipleAmount  + $interestAmount), 2);
					
					
					$debt->InterestAmount = $interestAmount;
					$debt->TotalAmount = $totalAmount;		
					$debt->InterestPerDay = $interestPerDay;
				}
				
				array_push($dataItems, $debt);	
			}
		}
		$result->Data = $dataItems;
	}catch (Exception $e){
		$result->Status = 0;
		$result->Message = $e->getMessage();
	}
}else{
	$result->Status = 0;
	$result->Message = "ไม่พบข้อมูล";
}

echo  json_encode($result);


