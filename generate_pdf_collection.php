<?php

require_once('tcpdf/tcpdf.php');
require_once('tcpdf/tcpdf_include.php');
require_once "db_connect.php";
require_once 'ThaiFormat.php';


if(is_numeric($_GET["id"]) && (!is_null($_GET["cid"]))){
	$collection_id = $_GET["id"];
	$cids = preg_split("/[_]/", $_GET["cid"]);
	$cidFilter = implode(",", $cids);
	$get_row_sql = "select c.*,cd.Year	from collectioncompany c
								inner join company com on c.CID = com.CID
								inner join collectiondocument cd on cd.CollectionID = c.CollectionID
								inner join lawfulness l on c.CID = l.CID  and cd.Year =  l.Year
								where c.CollectionID  = '$collection_id' limit 0,1";
	error_log($get_row_sql);
	
	$post_row = getFirstRow($get_row_sql);

	$output_fields = array('CollectionID','Year','RequestDate','GovDocumentNo','DocumentDetail','Reciever','RecievedDate');

	for($i = 0; $i < count($output_fields); $i++){
		$output_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
	}
	
	
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

$pdf->SetMargins(2.54, 1.75, 2.54);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------
// set font
$pdf->SetFont('THSarabun', '', 16);

$total_records = 0;

$get_collecion_sql = "select c.*,cd.Year,cd.RequestDate,cd.GovDocumentNo,c.CCID, c.CID,l.LawfulStatus as lawfulness_status ,com.CompanyCode,com.CompanyNameThai,com.CompanyTypeCode
from collectioncompany c
inner join company com on c.CID = com.CID
inner join collectiondocument cd on cd.CollectionID = c.CollectionID
inner join lawfulness l on c.CID = l.CID  and cd.Year =  l.Year
where c.CollectionID  = '$collection_id' and c.CID in ($cidFilter)
order by com.CompanyNameThai asc";

error_log($get_collecion_sql);

$collection_result = mysql_query($get_collecion_sql);
while ($post_row_q = mysql_fetch_array($collection_result)) {
	$total_records++;
	// add a page
	$pdf->AddPage();
	
	$thaiFormat = new ThaiFormat();
	$year =  $thaiFormat->number($post_row_q['Year'] + 543);
	$requestDate = $thaiFormat->date_format(new DateTime($post_row_q['RequestDate']), "j F Y");
	$govDocumentNo = $thaiFormat->to_thainum($post_row_q['GovDocumentNo']);
	$status = $post_row_q['lawfulness_status'];
	$fullCompanyName = formatCompanyName($post_row_q["CompanyNameThai"],$post_row_q["CompanyTypeCode"]);
	setcookie('meeoh',$fullCompanyName);
	if($status == 0){
		$html = include "pdf_collection_status2.php";
	}else {
		$html = include "pdf_collection_status0.php";
	}
	
	$pdf->writeHTML($html, true, false, true, false, '');

	// reset pointer to the last page
	$pdf->lastPage();
}
// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('collection_doc.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
?>