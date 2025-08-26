<?php
require_once('tcpdf/tcpdf.php');
require_once('tcpdf/tcpdf_include.php');
require_once 'db_connect.php';
require_once 'ThaiFormat.php';
require_once 'scrp_sequestration.php';
require_once 'c2x_constant.php';


$thaiFormat = new ThaiFormat();
$id = 0;
$seqModel = new Sequestration();
$cancelModel = new CancelledSequestration();
$fullCompanyName = "";
$cancelDocumentNO = "";
$seqDocumentNO = "";
$cancelDateFormat = "";
$seqDateFormat = "";

$sequestionDetails;
$isError = false;
if(isset($_GET["id"]) && (is_numeric($_GET["id"]))){

	$id = trim($_GET["id"]);
	$manage = new ManageSequestration();
	$cancelResult = $manage->getCancelSequestration($id);
	if($cancelResult->IsComplete){
		$cancelModel = $cancelResult->Data;
		$sid = $cancelModel->SID;
		$cancelDocumentNO = $thaiFormat->to_thainum($cancelModel->RequestNo);
		$cancelDateFormat =  $thaiFormat->date_format($cancelModel->RequestDate, "j F Y");
		
		$seqResult = $manage->getSequestration($sid, $SEQUESTRATION_TYPE);		
		if($seqResult->IsComplete){
			$seqModel = $seqResult->Data;			
			$fullCompanyName = formatCompanyName($seqModel->CompanyName, $seqModel->CompanyCode);
			$seqDocumentNO = $thaiFormat->to_thainum($seqModel->GovDocumentNo);
			$seqDateFormat =  $thaiFormat->date_format(new DateTime($seqModel->DocumentDate), "j F Y");
		}else{
			$isError = true;
			$errorMessage = $seqResult->Message;
		}		
		
	}else{
		$isError = true;
		$errorMessage = $cancelResult->Message;
	}
	
	
	//sequestration details
	$sDetailSql = "SELECT sd.SequestrationType, sd.DocumentNo, sd.AccountType, sd.bank_id, b.bank_name, sd.bank_branchname,
					sd.province_code, prov.province_name, sd.district_code, dist.district_name,
					sd.subdistrict_code, sDist.subdistrict_name
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
	
	while($sDetail = mysql_fetch_array($sDetailSqlResult)){
		$orderNo ++;
		$sequestionDetails .= '<br /><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
		if($sDetail["SequestrationType"] == $moneySequestrationType){
			array_push($banks, $sDetail);
			$sequestionDetails .= '<span>'.$thaiFormat->number($orderNo).'.&nbsp;'.$sDetail["bank_name"].'&nbsp;'.$accountTypes[$sDetail["AccountType"]].' &nbsp; เลขที่ '.$sDetail["DocumentNo"].'&nbsp;</span>';
			$sequestionDetails .= '<span>สาขา'.$sDetail["bank_branchname"].'</span>';
		}else{
			$propertyOrder ++;
			$propertyList .= '<br /><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
			$propertyList .= '<span>'.$thaiFormat->number($propertyOrder).'.&nbsp; ที่ดิน โฉนดเลขที่ '.$sDetail["DocumentNo"].'&nbsp;</span>';
			$propertyList .= '<span>ตำบล/แขวง '.$sDetail["subdistrict_name"].'&nbsp;อำเภอ/เขต '.$sDetail["district_name"].'&nbsp;จังหวัด'.$sDetail["province_name"].'</span>';
				
			$sequestionDetails .= '<span>'.$thaiFormat->number($orderNo).'.&nbsp; ที่ดิน โฉนดเลขที่ '.$sDetail["DocumentNo"].'&nbsp;</span>';
			$sequestionDetails .= '<span>ตำบล/แขวง '.$sDetail["subdistrict_name"].'&nbsp;อำเภอ/เขต '.$sDetail["district_name"].'&nbsp;จังหวัด'.$sDetail["province_name"].'</span>';
		}
	
	}
	
	
	
}

if(!$isError){
	ob_start();
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
	
	$pdf->SetMargins(3, 1.5, 2);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
	
	
	//set some language-dependent strings
	$pdf->setLanguageArray($l);
	
	// ---------------------------------------------------------
	// set font
	$pdf->SetFont('THSarabun', '', 16);
	
	$pdf->AddPage();
	$html = include "pdf_cancelsquestration_order.php";
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->lastPage();
	
	$pdf->AddPage();
	$html = include "pdf_cancelsquestration_announce.php";
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->lastPage();

	
	
	
	//Close and output PDF document
	$pdf->Output('cancelsequestration_doc.pdf', 'I');
}
?>



