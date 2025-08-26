<?php
//============================================================+
// File name   : example_008.php
// Begin       : 2008-03-04
// Last Update : 2010-08-08
//
// Description : Example 008 for TCPDF class
//               Include external UTF-8 text file
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com s.r.l.
//               Via Della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Include external UTF-8 text file
 * @author Nicola Asuni
 * @since 2008-03-04
 */
 
//echo $_POST["the_report"]; exit();

require_once('tcpdf/config/lang/eng.php');
require_once('tcpdf/tcpdf.php');


class MYPDF extends TCPDF {

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
}

// create new PDF document
//$the_orientation = "L";

if($_POST["the_report"] == "report_25" || $_POST["the_report"] == "report_26" || $_POST["the_report"] == "report_27"){
	$the_orientation = "L";
	
}else{
	$the_orientation = "H";
	
}


$pdf = new TCPDF($the_orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// remove default header/footer
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));



// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins

$pdf->SetMargins(10, 10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('freeserif', '', 8);

// add a page
$pdf->AddPage();

// get esternal file content

$file_to_use = $_POST["the_report"].'.php';
//echo $file_to_use; exit();
$tbl = get_include_contents($file_to_use);

$pdf->writeHTML($tbl, true, false, false, false, '');



// ---------------------------------------------------------


//Close and output PDF document
$pdf->Output('example_009.pdf', 'I');

/*
 header("Content-type: application/force-download");
     header("Content-Transfer-Encoding: Binary");
     header("Content-length: ".filesize($file));
    header("Content-disposition: attachment; filename=\"example_009.pdf\"");
	*/


//============================================================+
// END OF FILE                                                
//============================================================+
?>