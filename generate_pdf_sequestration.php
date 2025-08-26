<?php

require_once('tcpdf/tcpdf.php');
require_once('tcpdf/tcpdf_include.php');
require_once "db_connect.php";
require_once 'c2x_include.php';
require_once 'ThaiFormat.php';


$sequestrationRow = null;
if(is_numeric($_GET["id"])){
	$sid = $_GET["id"];
	$sql = "SELECT s.DocumentDate, s.GovDocumentNo, s.TotalAmount, 
			c.CompanyNameThai, c.CompanyTypeCode
			FROM sequestration s
			INNER JOIN company c on s.CID = c.CID
			WHERE s.SID = $sid ";
	
	$sqlResult = mysql_query($sql);
	
	$sequestrationRow = mysql_fetch_array($sqlResult);
	
	$mysqlError = mysql_error();
	if($mysqlError != ""){
		error_log($mysqlError);
		echo $mysqlError;
		exit();
	}
	
	//sequestration details
	$sDetailSql = "SELECT sd.SequestrationType, sd.DocumentNo, sd.AccountType, sd.bank_id, b.bank_name, sd.bank_branchname,
				   sd.province_code, prov.province_name, sd.district_code, dist.district_name,
			       sd.subdistrict_code, sDist.subdistrict_name, Other				   
				   FROM sequestrationdetail sd			       
			       LEFT JOIN bank b ON sd.bank_id = b.bank_id
			       LEFT JOIN provinces prov ON sd.province_code = prov.province_code
			       LEFT JOIN districts dist ON (sd.district_code = dist.district_code) AND (sd.province_code = dist.province_code)
			       LEFT JOIN subdistrict sDist ON (sd.subdistrict_code = sDist.subdistrict_code) AND 
			       			 (sd.province_code = sDist.province_code) AND (sd.district_code = sDist.district_code)			       
				   WHERE sd.SID = $sid
				   ORDER BY sd.SequestrationType, sd.bank_id, sd.SDID
			";
	
	$sDetailSqlResult = mysql_query($sDetailSql);
	$mysqlError = mysql_error();
	if($mysqlError != ""){
		error_log($mysqlError);
		echo $mysqlError;
		exit();
	}
	
	$sequestionDetails = "";
	$orderNo = 0;
	$propertyOrder = 0;
	$carOrder = 0;
	$otherOrder = 0;
	$moneySequestrationType = $SEQUESTRATION_TYPE->Money;
	$accountTypes = getAcountTypeMapping();
	$banks = array();
	$properties = array();
	$propertyList = "";
	$carList = "";
	$otherList = "";
	$sequestrationType = "";
	
	$thaiFormat = new ThaiFormat();
	
	while($sDetail = mysql_fetch_array($sDetailSqlResult)){
		$orderNo ++;
		$sequestionDetails .= '<br /><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
		$sequestrationType = $sDetail["SequestrationType"];
		
		if($sequestrationType == $moneySequestrationType){
			array_push($banks, $sDetail);
			$sequestionDetails .= '<span>'.$thaiFormat->number($orderNo).'.&nbsp;'.$sDetail["bank_name"].'&nbsp;'.$accountTypes[$sDetail["AccountType"]].' &nbsp; เลขที่ '.$sDetail["DocumentNo"].'&nbsp;</span>';
			$sequestionDetails .= '<span>สาขา'.$sDetail["bank_branchname"].'</span>';
		}else if($sequestrationType == $SEQUESTRATION_TYPE->Property){
			$propertyOrder ++;
			$propertyList .= '<br /><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
			$propertyList .= '<span>'.$thaiFormat->number($propertyOrder).'.&nbsp; ที่ดิน โฉนดเลขที่ '.$sDetail["DocumentNo"].'&nbsp;</span>';
			$propertyList .= '<span>ตำบล/แขวง '.$sDetail["subdistrict_name"].'&nbsp;อำเภอ/เขต '.$sDetail["district_name"].'&nbsp;จังหวัด'.$sDetail["province_name"].'</span>';
			
			$sequestionDetails .= '<span>'.$thaiFormat->number($orderNo).'.&nbsp; ที่ดิน โฉนดเลขที่ '.$sDetail["DocumentNo"].'&nbsp;</span>';
			$sequestionDetails .= '<span>ตำบล/แขวง '.$sDetail["subdistrict_name"].'&nbsp;อำเภอ/เขต '.$sDetail["district_name"].'&nbsp;จังหวัด'.$sDetail["province_name"].'</span>';
		}else if($sequestrationType == $SEQUESTRATION_TYPE->Car){
			$carOrder ++;
			$carList .= '<br /><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
			$carList .= '<span>'.$thaiFormat->number($carOrder).'.&nbsp; เลขทะเบียน '.$sDetail["DocumentNo"].'&nbsp;</span>';
			$carList .= '<span>ปี '.$sDetail["CarYear"].'</span>';
				
			$sequestionDetails .= '<span>'.$thaiFormat->number($orderNo).'.&nbsp; เลขทะเบียน '.$sDetail["DocumentNo"].'&nbsp;</span>';
			$sequestionDetails .= '<span>ปี '.$sDetail["CarYear"].'</span>';
		}else if($sequestrationType == $SEQUESTRATION_TYPE->Other){
			$otherOrder ++;
			$otherList .= '<br /><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
			$otherList .= '<span>'.$thaiFormat->number($otherOrder).'.&nbsp; '.$sDetail["Other"].'&nbsp;</span>';			
				
			$sequestionDetails .= '<span>'.$thaiFormat->number($orderNo).'.&nbsp; '.$sDetail["Other"].'&nbsp;</span>';			
		}			
		
	}

	
	//payment
	$sqlPayment = "SELECT SUM(InterestPerDay) AS TotalInterestPerDay FROM sequestrationpayment WHERE SID = $sid";
	$sqlPaymentResult = mysql_query($sqlPayment);
	$mysqlError = mysql_error();
	if($mysqlError != ""){
		error_log($mysqlError);
		echo $mysqlError;
		exit();
	}
	$totalInterestPerDay = 0;
	while($sPayment = mysql_fetch_array($sqlPaymentResult)){
		$totalInterestPerDay = $sPayment["TotalInterestPerDay"];
	}
	$totalInterestPerMonth = round(($totalInterestPerDay * 30), 2);
}else{
	exit();
}
// create new PDF document
$pdf = new TCPDF("P", "cm", PDF_PAGE_FORMAT, true, 'UTF-8', false);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins

$pdf->SetMargins(2.54, 1.75,2.54);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------
// set font
$pdf->SetFont('THSarabun', '', 16);

if(!is_null($sequestrationRow) && (count($sequestrationRow) > 0)){	
	
	$fullCompanyName = formatCompanyName($sequestrationRow["CompanyNameThai"], $sequestrationRow["CompanyTypeCode"]);
	$debtTotalAmount = $thaiFormat->number_format($sequestrationRow["TotalAmount"], 2, ".", ",");
	$debtTotalAmountWord = $thaiFormat->number_tobaht($sequestrationRow["TotalAmount"]);
	$documentDateFormat =  $thaiFormat->date_format(new DateTime($sequestrationRow["DocumentDate"]), "j F Y");
	$interatePerDayFormat = $thaiFormat->number_format($totalInterestPerDay, 2, ".", ",");
	$interatePerDayFormatText = $thaiFormat->number_tobaht($totalInterestPerDay);
	$interatePerMonthFormat = $thaiFormat->number_format($totalInterestPerMonth, 2, ".", ",");
	$interatePerMonthFormatText = $thaiFormat->number_tobaht($totalInterestPerMonth);
	$documentNo = $thaiFormat->to_thainum($sequestrationRow["GovDocumentNo"]);
	
	
	$docDate = new DateTime($sequestrationRow["DocumentDate"]);
	$docYear = $docDate->format("Y");	
	$documentYear =  $thaiFormat->number(($docYear + 543));
	
	//คำสั่งการอายัดทรัพย์สิน (ธนาคาร)
	if(count($banks) > 0){
		$countBank = 1;
		$bankDetail = '';
		$detail = $banks[0];
		$bankID = $detail["bank_id"];
		$bankName = $detail["bank_name"];
		$bankDetail .= '<br /><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
		$bankDetail .= '<span>'.$thaiFormat->number(1).'.&nbsp;'.$detail["bank_name"].'&nbsp;'.$accountTypes[$detail["AccountType"]].' &nbsp; เลขที่ '.$detail["DocumentNo"].'&nbsp;</span>';
		$bankDetail .= '<span>สาขา'.$detail["bank_branchname"].'</span>';
		for($i = 1; $i < count($banks); $i++){
			$detail = $banks[$i];
			if($detail["bank_id"] != $bankID){
				$pdf->AddPage();
				$html = include "pdf_squestration_money_order.php";
				$pdf->writeHTML($html, true, false, true, false, '');
				$pdf->lastPage();
				
				$bankDetail = "";
				$bankName = "";
				$countBank = 0;
				$bankID = $detail["bank_id"];
			}		
			$countBank ++;			
			$bankName = $detail["bank_name"];
			$bankDetail .= '<br /><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
			$bankDetail .= '<span>'.$thaiFormat->number($countBank).'.&nbsp;'.$detail["bank_name"].'&nbsp;'.$accountTypes[$detail["AccountType"]].' &nbsp; เลขที่ '.$detail["DocumentNo"].'&nbsp;</span>';
			$bankDetail .= '<span>สาขา'.$detail["bank_branchname"].'</span>';
		}
		
		$pdf->AddPage();
		$html = include "pdf_squestration_money_order.php";
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->lastPage();
		
	}
	
	
	//คำสั่งการอายัดทรัพย์สิน (ที่ดิน)
	if($propertyList != ""){
		$pdf->AddPage();
		$html = include "pdf_squestration_property_order.php";
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->lastPage();
	}
	
	//คำสั่งการอายัดทรัพย์สิน (รถยนต์)
	if($carList != ""){
		$pdf->AddPage();
		$html = include "pdf_squestration_car_order.php";
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->lastPage();
	}
	
	//คำสั่งการอายัดทรัพย์สิน (อื่นๆ)
	if($carList != ""){
		$pdf->AddPage();
		$html = include "pdf_squestration_other_order.php";
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->lastPage();
	}
		
	
	//ประกาศการอายัดทรัพย์สิน
	$pdf->AddPage();
	$html = include "pdf_squestration_announce.php";
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->lastPage();
}

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('sequestration_doc.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
?>