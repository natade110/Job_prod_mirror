<?php
//============================================================+
set_time_limit(300);
//echo $_POST["the_report"]; exit();


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

if($_POST["the_report"] == "report_25" || $_POST["the_report"] == "report_262" || $_POST["the_report"] == "report_27"){
	$the_orientation = "A4-L";
	
}else{
	$the_orientation = "A4";
	
}


$file_to_use = $_POST["the_report"].'.php';
//echo $file_to_use; exit();
$tbl = get_include_contents($file_to_use);
//echo $tbl; exit();

include("./mpdf/mpdf.php");

//$mpdf=new mPDF(); 
$mpdf=new mPDF('utf-8', $the_orientation);

$mpdf->useAdobeCJK = true;		// Default setting in config.php
						// You can set this to false if you have defined other CJK fonts

$mpdf->SetAutoFont(AUTOFONT_ALL);	//	AUTOFONT_CJK | AUTOFONT_THAIVIET | AUTOFONT_RTL | AUTOFONT_INDIC	// AUTOFONT_ALL
						// () = default ALL, 0 turns OFF (default initially)

$mpdf->WriteHTML($tbl);

$mpdf->Output(); 

exit;


//============================================================+
// END OF FILE                                                
//============================================================+
?>