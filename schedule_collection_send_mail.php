<?php
require_once "db_connect.php";
require_once 'c2x_include.php';
require_once 'ThaiFormat.php';

$get_schedule = mysql_query("select * from schedulecollection");

if($get_schedule != null){
	$today = date('md');
	$beginYear = 0;
	$endYear = NULL;
	
	$send_mail = false;
	
	// verify date for send email
	if($post_row = mysql_fetch_array($get_schedule)){
		if($post_row['SentNo1'] == $today){
			$send_mail = true;
		}else if ($post_row['SentNo2'] == $today){
			$send_mail = true;
		}else if ($post_row['SentNo3'] == $today){
			$send_mail = true;
		}else if ($post_row['SentNo4'] == $today){
			$send_mail = true;
		}else {
			exit();
		}
		$beginYear = $post_row['BeginYear'];
		if (!is_null($post_row['EndYear'])){
			$endYear = $post_row['EndYear'];
		}
	}

	$filterYear = '';
	if (is_null($endYear) && $beginYear > 0){
		$filterYear = "AND l.Year=$beginYear";
	}else{
		$filterYear = "AND l.Year>=$beginYear AND l.Year<=$endYear";
	}
	
	// select collection for send e-mail
	$get_collection = mysql_query('select
									l.LID
									,l.LawfulStatus
									,l.Year
									,com.CompanyCode
									,com.CompanyNameThai
									,com.CompanyTypeCode
									,com.email email
									,com.ContactEmail1 email2
									,com.ContactEmail2 email3
									from lawfulness l
									left join company com on l.CID = com.CID
									where ((com.email is not null and length(com.email) > 0)
									  or (com.ContactEmail1 is not null and length(com.ContactEmail1) > 0)
									  or (com.ContactEmail2 is not null and length(com.ContactEmail2) > 0))
									  and l.LawfulStatus IN (0,2) '.$filterYear);
	
	if($get_collection !== false && $send_mail === true){
		while ($row_collection = mysql_fetch_array($get_collection)){
			$emailAddresses = array($row_collection['email'], $row_collection['email2'], $row_collection['email3']);
			$fullCompanyName = formatCompanyName($row_collection['CompanyNameThai'],$row_collection['CompanyTypeCode']);
			$lawful_status = $row_collection['LawfulStatus'];
			$year = $row_collection['Year'];
			
			if($lawful_status == 2){
				$subject = 'แจ้งข่าวการปฏิบัติตามกฎหมายการจ้างงานคนพิการ ประจำปี '.formatYear($year);
				$content = set_content_mail_status2($fullCompanyName, $year);
			}else {
				$subject = 'แจ้งข่าวการปฏิบัติตามกฎหมายการจ้างงานคนพิการ ประจำปี '.formatYear($year);
				$content = set_content_mail_status0($fullCompanyName, $year);
			}
			
			foreach ($emailAddresses as $to_email){
				$to_email = trim($to_email);
				if($to_email != null && filter_var($to_email, FILTER_VALIDATE_EMAIL)){
					//insert email history
					$lid = $row_collection['LID'];
					$receiver = $fullCompanyName;
					
					$fields = array(
							'LID' => $lid,
							'Receiver' => $receiver,
							'Email' => $to_email,
							'LawfulStatus' => $lawful_status
					);
					$special_fields = array('SentDate' => 'NOW()');
					if (executeInsert('schedulecollectionhistory', $fields, $special_fields)){
						$shid = mysql_insert_id();
	
						$url = htmlspecialchars(WEB_URL."/confirm_reading.php?shid=$shid&email=".($to_email));
						$linkHtml = "
		<p>เพื่อให้การตรวจสอบเป็นไปได้อย่างสะดวกรวดเร็ว ทางกองกองทุนฯ ต้องขอความกรุณาให้ท่านช่วยกดลิงค์ <a href='$url'>ยืนยันการอ่านจดหมาย</a> เพื่อยืนยันว่าท่านได้อ่านจดหมายฉบับนี้แล้ว</p>
		<p>ในกรณีที่ท่านไม่สามารถกดลิงค์ได้ กรุณาคัดลอกลิงค์ด้านล่างนี้ไปเปิดในโปรแกรมเว็บบราวเซอร์</p>
		<p style='background-color:#e0e0e0;padding:5px;margin:0 10px;'>$url</p>";
	
						//sent mail
						doSendMail($to_email, $subject, $content.$linkHtml);
					}
				}
			}
		}
	}
}

function set_content_mail_status2($companyName, $year){
	$htmlCompanyName = htmlspecialchars($companyName);
	$htmlYear = htmlspecialchars(ThaiFormat::number(formatYear($year)));
	$txt_content = "
	<p>เรียน &nbsp;&nbsp;&nbsp;$htmlCompanyName</p>
	<p style='text-indent:1in'>กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ได้แจ้งให้สถานประกอบการของท่านปฏิบัติตามพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ พ.ศ.๒๕๕๐ และที่แก้ไขเพิ่มเติม (ฉบับที่ ๒) พ.ศ. ๒๕๕๖ เรื่อง การปฏิบัติตามกฎหมายการจ้างงานคนพิการประจำปี $htmlYear โดยให้ปฏิบัติตามกฎหมายและรายงานผลการปฏิบัติ มายังกองกองทุนและส่งเสริมความเสมอภาคคนพิการ ภายในวันที่       ๓๑ มกราคม $htmlYear นั้น</p>
	<p>กองกองทุนและส่งเสริมความเสมอภาคคนพิการ ขอเรียนให้ท่านทราบว่า จากการตรวจสอบรายงานผลการปฏิบัติตามกฎหมายการจ้างงานคนพิการประจำปี $htmlYear พบว่า สถานประกอบการของท่านได้ปฏิบัติตามกฎหมายการจ้างงานคนพิการแล้วแต่ยังไม่ครบถ้วนตามอัตราส่วน จึงขอความร่วมมือท่านให้ปฏิบัติตามกฎหมายเพิ่มเติม โดยการส่งเงินเข้ากองทุนฯ พร้อมดอกเบี้ย ตามมาตรา ๓๔  พระราชบัญญัติดังกล่าว ณ กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ตามที่อยู่ข้างต้น หรือ ณ ที่ทำการของกองกองทุนและส่งเสริมความเสมอภาคคนพิการ ชั้น ๓ เลขที่ ๑๐๒/๔๑ ถนนกำแพงเพชร ๕ แขวงสามเสนใน เขตพญาไท กรุงเทพมหานคร ๑๐๔๐๐ โทรศัพท์  ๐ ๒๑๐๖ ๙๓๒๖-๓๑ หากท่านไม่ดำเนินการตามที่กฎหมายกำหนด กรมฯ มีความจำเป็นที่จะต้องดำเนินการประกาศโฆษณาการปฏิบัติตามกฎหมายจ้างงานคนพิการของท่านต่อสาธารณะ ตามมาตรา ๓๙ แห่งพระราชบัญญัติเดียวกัน  ต่อไป</p>
	";
	return $txt_content;
}

function set_content_mail_status0($companyName, $year){
	$htmlCompanyName = htmlspecialchars($companyName);
	$htmlYear = htmlspecialchars(ThaiFormat::number(formatYear($year)));
	$txt_content = "
	<p>เรียน &nbsp;&nbsp;&nbsp;$htmlCompanyName</p>
	<p style='text-indent:1in'>กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ได้แจ้งให้สถานประกอบการของท่านปฏิบัติตามพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ พ.ศ.๒๕๕๐ และที่แก้ไขเพิ่มเติม (ฉบับที่ ๒) พ.ศ. ๒๕๕๖ เรื่อง การปฏิบัติตามกฎหมายการจ้างงานคนพิการประจำปี $htmlYear โดยให้ปฏิบัติตามกฎหมายและรายงานผลการปฏิบัติ มายังกองกองทุนและส่งเสริมความเสมอภาคคนพิการ ภายในวันที่ ๓๑ มกราคม $htmlYear นั้น</p>
	<p>บัดนี้ ได้ล่วงเลยระยะเวลาที่ให้ปฏิบัติและให้รายงานผลการปฏิบัติตามกฎหมายดังกล่าวแล้ว ปรากฏว่ากองกองทุนและส่งเสริมความเสมอภาคคนพิการ ยังไม่ได้รับรายงานว่าสถานประกอบการของท่านได้ดำเนินการจ้างงานคนพิการ จัดให้สัมปทานหรือส่งเงินเข้ากองทุนฯ ตามกฎหมายประจำปี $htmlYear แล้วหรือไม่ ดังนั้น จึงขอความร่วมมือท่านให้ปฏิบัติตามกฎหมาย โดยการส่งเงินเข้ากองทุนฯ พร้อมดอกเบี้ย ตามมาตรา ๓๔  พระราชบัญญัติดังกล่าว ณ กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ตามที่อยู่ข้างต้น หรือ ณ ที่ทำการของกองกองทุนและส่งเสริมความเสมอภาคคนพิการ ชั้น ๓ เลขที่ ๑๐๒/๔๑ ถนนกำแพงเพชร ๕ แขวงสามเสนใน เขตพญาไท กรุงเทพมหานคร ๑๐๔๐๐ โทรศัพท์  ๐ ๒๑๐๖ ๙๓๒๖-๓๑ หากท่านไม่ดำเนินการตามที่กฎหมายกำหนด กรมฯ มีความจำเป็นที่จะต้องดำเนินการประกาศโฆษณาการปฏิบัติตามกฎหมายจ้างงานคนพิการของท่านต่อสาธารณะ ตามมาตรา ๓๙ แห่งพระราชบัญญัติเดียวกัน  ต่อไป</p>
	";
	
	return $txt_content;
}
?>