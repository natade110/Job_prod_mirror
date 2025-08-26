<?php 
require_once 'db_connect.php';
require_once 'ThaiFormat.php';
require_once 'scrp_notice.php';
require_once 'c2x_constant.php';

function displayError($errorMessage){	
	ob_end_clean();
	header('Content-Type: text/plain; charset=utf-8');
	echo $errorMessage;
	die();
}


ob_start();


$thaiFormat = new ThaiFormat();
$fullCompanyName = "";
$govDocumentNO = "";
$documentDate = "";
$debts = array();
$totalPrincipleAmount = 0;
$noticeYear = "";
$isError = false;
$errorMessage = "";

if(isset($_GET["id"]) && (is_numeric($_GET["id"]))){
	
	$id = trim($_GET["id"]);
	$manage = new ManageNotice();
	$result = $manage->getNoticeDocument($id);
	if($result->IsComplete){
		$model = $result->Data;
		
		$debts = $model->NoticeDetails;
		$fullCompanyName = formatCompanyName($model->CompanyName, $model->CompanyTypeCode);
		$govDocumentNO = $thaiFormat->to_thainum($model->GovDocumentNo);
		error_log($model->GovDocumentNo);
		error_log($govDocumentNO);
		$documentDate = $thaiFormat->date_format(new DateTime($model->DocumentDate), "j F Y");
	}else{
		$isError = true;
		$errorMessage = $result->Message;
	}
}




?>

<?php if(!$isError){?>
<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="utf-8" />
    <title></title>
    
    <style type="text/css">
    	@font-face {
            font-family: 'TH SarabunPSK';
            src: url(./bill_payment/fonts/THSarabun.ttf);
            font-style: normal;
        }
        @font-face {
            font-family: 'TH SarabunPSK';
            src: url(./bill_payment/fonts/THSarabun Bold.ttf);
            font-weight: bold;
        }
        @font-face {
            font-family: 'TH SarabunPSK';
            src: url(./bill_payment/fonts/THSarabun Italic.ttf);
            font-style: italic;
        }
        @font-face {
            font-family: 'TH SarabunPSK';
            src: url(./bill_payment/fonts/THSarabun BoldItalic.ttf);
            font-style: italic;
            font-weight: bold;
        }
        /* http://meyerweb.com/eric/tools/css/reset/ 
            v2.0 | 20110126
            License: none (public domain)
        */

        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td,
        article, aside, canvas, details, embed, 
        figure, figcaption, footer, header, hgroup, 
        menu, nav, output, ruby, section, summary,
        time, mark, audio, video {
	        margin: 0;
	        padding: 0;
	        border: 0;
	        font-size:100%;
            font-family: 'TH SarabunPSK';
	        vertical-align: baseline;	       
        }
        /* HTML5 display-role reset for older browsers */
        article, aside, details, figcaption, figure, 
        footer, header, hgroup, menu, nav, section {
	        display: block;
        }
        body {
	        line-height: 1;
        }
        
        table {
	        border-collapse: collapse;
	        border-spacing: 0;
        }
        a, a:HOVER, a:VISITED, a:ACTIVE{
        	color: black;
        	text-decoration: none;
        }
        /* Printer Setup */
        @page
        {
            width: 210mm;
            height: 297mm;
            margin:0;
            padding:0;
        }

        /* Custom Style */
        html {
            width: 210mm;
            height: 297mm;
        }

        body{
           /*  width: 199mm;
            height: 286mm;
            margin: 5.5mm; */
            margin-top:39mm;
            margin-left:30mm; 
            margin-right:20mm;
            margin-bottom: 20mm;
           /*  outline: 1px solid black; */
        }
        @media pdf
        {
        	* {
	            line-height: 16pt;
	            font-size: 16pt;
	            word-wrap: break-word;
	            
	            font-family: 'TH SarabunPSK';
            	
        	}
        	
        	@font-face {
	            font-family: 'TH SarabunPSK';
	            src: url(./bill_payment/fonts/THSarabun.ttf);
	            font-style: normal;
	        }
        } 
         
         div.content{
         	text-indent: 25mm;
            /* text-align:justify; */
         	width:158.03mm;           
         }
         
         div.content, div.content-block{
         	width:158.03mm; 
         }
         
         span.amount{
         	display: inline-block;
         	width: 104px;
         	        	
         }         
         
        
    </style>
</head>
<body>
	<div style="text-align: center;" >
		<img alt="" src="./images/kut.jpg" style="height: 32mm; width:27mm; position: fixed; top: 14mm; left: 92mm; z-index: 10000" />
	</div>
	<table width="100%">		
        <tr>
            <td style="width:290px;">ที่&nbsp;<?php echo $govDocumentNO?></td>
            <td style="width:65px;">&nbsp;</td>
            <td style="">
            	กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ<br />
            	๒๕๕  อาคาร ๖๐ ปี  กรมประชาสงเคราะห์<br />
            	ถนนราชวิถี เขตราชเทวี กรุงเทพฯ  ๑๐๔๐๐
            </td>
	    </tr>
	    <tr><td class="3" style="height: 6pt"></td></tr>
		<tr>
			<td>&nbsp;</td>			
			<td colspan="2"><?php echo $documentDate?></td>
		</tr>	
		<tr><td class="3" style="height: 6pt"></td></tr>	
		<tr>
			<td colspan="3">
				เรื่อง&nbsp;&nbsp;แจ้งให้ชำระเงินเข้ากองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ
			</td>
        </tr>
        <tr><td class="3" style="height: 6pt"></td></tr>		
        <tr>
        	<td colspan="3">
				เรียน&nbsp;&nbsp;กรรมการผู้จัดการ<?php echo $fullCompanyName ?>
			</td>
        </tr>
        <tr><td class="3" style="height: 6pt"></td></tr>
        <tr>
        	<td colspan="3">
        		<div style="text-indent: 25mm; text-align: justify;">ด้วยกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ&nbsp;&nbsp;&nbsp;&nbsp;ได้ตรวจสอบการปฏิบัติตามกฎหมายการ</div>
     			<div style="text-align: justify;">จ้างงานคนพิการตามพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ พ.ศ. ๒๕๕๐ และที่แก้ไขเพิ่มเติม</div>
     			<div style="text-align: justify;">(ฉบับที่ ๒) พ.ศ. ๒๕๕๖ เมื่อวันที่ <?php echo $documentDate?> พบว่า <?php echo $fullCompanyName?> มิได้
     	ปฎิบัติตามกฎหมาย โดยการจ้างคนพิการเข้าทำงานในสถานประกอบการ หรือจัดให้สัมปทาน หรือส่งเงินเข้า
     	กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการตามมาตรา ๓๓ มาตรา ๓๕ หรือมาตรา ๓๔ 
     	แห่งพระราชบัญญัติดังกล่าว ดังนี้</div>
        	</td>
        </tr>
     </table>	
   
     
    <?php 
          if(!is_null($debts) && (count($debts) > 0) ){
            		$year = 0; $item = null; $detailHtml = "";
            		$principleAmount = 0;
            		$debtInterestRateFormat = $thaiFormat->number($DEBT_INTEREST_RATE);
            		$has2011Year = false;
            		for($i = 0; $i < count($debts); $i++){
            			$item = $debts[$i];
            			$year = $item->Year;
            			$yearFormat = $thaiFormat->number($year + 543);
            			$principleAmount = $item->PrincipleAmount;
            			$totalPrincipleAmount = round(($totalPrincipleAmount + $principleAmount), 2);
            			$detailHtml = '<div class="content">ประจำปี '.$yearFormat.' จำนวน <span class="amount">'.$thaiFormat->number_format($principleAmount, 2, ".", ","). '</span> บาท ';
            			if($year > 2011){
            				$detailHtml .= 'พร้อมดอกเบี้ยในอัตราร้อยละ '.$debtInterestRateFormat.'&nbsp;ต่อปี นับแต่ วันที่ ๑ กุมภาพันธ์ '.$yearFormat.' จนถึงวันที่ชำระครบถ้วน';
            			}
            			$detailHtml .= '</div>';
            			$has2011Year = (!$has2011Year)? ($year == 2011) : $has2011Year;
            			echo $detailHtml;
            		}
            		
            		if($has2011Year){
            			$noticeYear = ' อนึ่ง&nbsp;&nbsp;การชำระเงินประจำปี ๒๕๕๔ หากพ้นกำหนดเวลาดังกล่าวให้ถือว่าผิดนัด&nbsp;&nbsp;&nbsp;และให้เสีย ดอกเบี้ย ใน อัตราร้อยละ ๗.๕ ต่อปี นับแต่วันที่ผิดนัด จนถึงวันที่ชำระครบถ้วน ';
            		}
            		
          }
       ?>
       	<div style="height: 6pt"></div>       
       	<div  class="content" style="">กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;จึงแจ้งมายังท่านให้นำเงิน&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;จำนวน </div>
		<div class="content-block">
			<div style="text-align:justify;"><span style="width: 115px;display: inline-block;"><?php echo $thaiFormat->number_format($totalPrincipleAmount, 2, ".", ",")?></span> บาท  พร้อมดอกเบี้ยนำส่ง ณ กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ เลขที่ ๒๕๕ </div>	      
	      	<div style="text-align:justify;">อาคาร&nbsp;&nbsp;๖๐ ปี กรมประชาสงเคราะห์ ถนนราชเทวี เขตราชเทวี กรุงเทพฯ&nbsp;&nbsp;ภายใน ๓๐ วัน&nbsp;&nbsp;นับแต่วันที่ได้รับ</div>
	        <div style="text-align:justify;">หนังสือฉบับนี้<?php echo $noticeYear?>หากท่านเพิกเฉย ไม่นำเงินมาชำระ&nbsp;&nbsp;กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ&nbsp;&nbsp;&nbsp;จะดำเนินการตามกฎหมายกับท่าน&nbsp;&nbsp;&nbsp;และ ท่านสามารถตรวจสอบรายละเอียดได้ที่ <a href="www.job.nep.go.th" target="_blank">www.job.nep.go.th</a>
	        </div>
		</div>      
       
      <div style="height: 6pt"></div>
      <div class="content">จึงเรียนมาเพื่อโปรดชำระเงินตามกำหนด</div>   
      <div style="height: 12pt"></div>         				
      <table style="width:100%">	
      	<tr>
      		<td style="width:290px;" ></td>
      		<td colspan="2" style="padding-left: 170pt">ขอแสดงความนับถือ</td>
      	</tr>
      	<tr>
      		<td colspan="3" style="height: 16pt"></td>
      	</tr>
      	<tr>
      		<td colspan="3" style="height: 16pt"></td>
      	</tr>
      	<tr>
      		<td colspan="3" style="height: 16pt"></td>
      	</tr>
      	<tr>
      		<td colspan="3" style="padding-left: 200pt">(นายสมชาย&nbsp;&nbsp;เจริญอำนวยสุข)</td>
      	</tr>
      	<tr>
      		<td colspan="3" style="padding-left: 156pt">อธิบดีกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</td>
      	</tr>       
		<tr>
      		<td colspan="3" style="height: 16pt"></td>
      	</tr>
      	<tr>
      		<td colspan="3" style="height: 16pt"></td>
      	</tr>
      	<tr>
      		<td colspan="3" style="height: 16pt"></td>
      	</tr>
		<tr>
			<td colspan="3">
				กองกองทุนและส่งเสริมความเสมอภาคคนพิการ<br />
				กลุ่มส่งเสริมความเสมอภาคและงานคดี<br />
				โทร. ๐ ๒๑๐๖ ๙๓๔๙ <br/>
				โทรสาร ๐ ๒๑๐๖ ๙๓๕๑
			</td>
		</tr>			
	</table>
</body>
</html>
<?php 
}// !$isError
else{
	displayError($errorMessage);
}

use Dompdf\Dompdf;

if (!$isHTMLMode){
	$html = ob_get_contents();
	
	ob_end_clean();
	mb_internal_encoding('UTF-8');
	define("DOMPDF_ENABLE_CSS_FLOAT", true);
	define("DOMPDF_DEFAULT_MEDIA_TYPE", "print");
	define("DOMPDF_DEFAULT_PAPER_SIZE", "a4");
	define("DOMPDF_UNICODE_ENABLED", true);
	define("DOMPDF_ENABLE_FONTSUBSETTING", true);
	require_once __DIR__.'/dompdf/autoload.inc.php';
	try {
		
		$dompdf = new Dompdf();
		$dompdf->setPaper("A4");		
		$options = $dompdf->getOptions();
		$options->setDefaultMediaType('pdf')
		        ->setDefaultFont('TH SarabunPSK')		        
				->setIsHtml5ParserEnabled(true)
		        ->setIsJavascriptEnabled(false);
		
		$dompdf->setOptions($options);
 		
 		$dompdf->loadHtml($html);
		$dompdf->render();		
		$dompdf->stream("notice_document", array('Attachment'=>false)); 
		exit(0);
		
	} catch(Exception $ex){
		error_log($ex);
	}
}
?>