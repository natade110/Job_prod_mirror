<?php

//$todayDate = date('y-m-d');
//$thaiDate = formatDateThai($todayDate);


$text = '
		<table>
			<tr>
	            <td style="text-align: center;" colspan="3">
	                <img alt="" src="./images/kut.jpg" />
	            </td>
	        </tr>
	        <tr>
	            <td style="width:245px;"><span>ที่ '.$govDocumentNo.'</span></td>
	            <td style="width:91px;">&nbsp;</td>
	            <td style="width:265px;"><span>กองกองทุนและส่งเสริมความเสมอภาคคนพิการ</span></td>
	        </tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><span>๒๕๕  อาคาร ๖๐ ปี  ถนนราชวิถี แขวงทุ่งพญาไท</span></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><span>เขตราชเทวีกรุงเทพฯ  ๑๐๔๐๐</span></td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>วันที่  '.$requestDate.' </span></td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr>
				<td colspan="3">
					<span>เรื่อง</span>
					<span>&nbsp;&nbsp;&nbsp;แจ้งข่าวการปฏิบัติตามกฎหมายการจ้างงานคนพิการ ประจำปี  '.$year.' </span>
				</td>
            </tr>
			<tr><td colspan="3">&nbsp;</td></tr>
	        <tr>
	        	<td colspan="3">
					<span>เรียน</span>
					<span>&nbsp;&nbsp;&nbsp;'.$fullCompanyName.'</span>
				</td>
	        </tr>
			<tr><td colspan="3">&nbsp;</td></tr>
	        <tr>
	            <td colspan="3">
					<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	            	<span>กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ได้แจ้งให้สถานประกอบการของท่านปฏิบัติตาม</span>
					<span><br />พระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ พ.ศ.๒๕๕๐&nbsp;&nbsp;&nbsp;และที่แก้ไขเพิ่มเติม (ฉบับที่ ๒) พ.ศ. '.$year.' </span>
					<span><br />เรื่อง&nbsp;&nbsp;&nbsp;การปฏิบัติตามกฎหมายการจ้างงานคนพิการประจำปี '.$year.'&nbsp;&nbsp;โดยให้ปฏิบัติตามกฎหมายและรายงานผลการ</span>
					<span><br />ปฏิบัติมายังกองกองทุนและส่งเสริมความเสมอภาคคนพิการ ภายในวันที่ ๓๑ มกราคม '.$year.' นั้น</span><br /><br />
					<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	            	<span>บัดนี้ได้ล่วงเลยระยะเวลาที่ให้ปฏิบัติและให้รายงานผลการปฏิบัติตามกฎหมายดังกล่าวแล้ว</span>
	            	<span>ปรากฏว่ากองกองทุนและส่งเสริมความเสมอภาคคนพิการยังไม่ได้รับรายงานว่าสถานประกอบการของท่านได้ดำเนิน</span>
					<span>การจ้างงานคนพิการจัดให้สัมปทานหรือส่งเงินเข้ากองทุนฯ ตามกฎหมายประจำปี&nbsp;'.$year.'&nbsp;&nbsp;แล้วหรือไม่&nbsp;&nbsp;ดังนั้นจึงขอ</span>
					<span>ความร่วมมือท่านให้ปฏิบัติตามกฎหมาย โดยการส่งเงินเข้ากองทุนฯพร้อมดอกเบี้ยตามมาตรา ๓๔ &nbsp;&nbsp;พระราชบัญญัติ</span>
					<span>ดังกล่า&nbsp;&nbsp;ณ กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ&nbsp;&nbsp;ตามที่อยู่ข้างต้น หรือ&nbsp;&nbsp;&nbsp;ณ ที่ทำการของกองกองทุนและส่ง</span>
					<span>เสริมความเสมอภาคคนพิการชั้น&nbsp;&nbsp;&nbsp;๓&nbsp;&nbsp;เลขที่&nbsp;&nbsp;๑๐๒/๔๑ &nbsp;&nbsp;&nbsp;ถนนกำแพงเพชร&nbsp;&nbsp; ๕&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;แขวงสามเสนใน&nbsp;&nbsp;&nbsp;&nbsp;เขตพญาไท</span>
					<span>กรุงเทพมหานคร&nbsp;&nbsp;๑๐๔๐๐&nbsp;&nbsp;&nbsp;โทรศัพท์&nbsp;๐ ๒๑๐๖ ๙๓๒๖-๓๑ หากท่านไม่ดำเนินการตามที่กฎหมายกำหนด&nbsp;&nbsp;กรมฯ</span>
					<span>มีความจำเป็นที่จะต้องดำเนินการประกาศ&nbsp;&nbsp;&nbsp;&nbsp;โฆษณาการปฏิบัติตามกฎหมายจ้างงานคนพิการของท่านต่อสาธารณะ</span>
					<span>ตามมาตรา ๓๙&nbsp;แห่งพระราชบัญญัติเดียวกันต่อไป </span>
	            </td>
	        </tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr>
				<td colspan="3">
					<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	            	<span>จึงเรียนมาเพื่อโปรดพิจารณาดำเนินการ</span>
				</td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr>
				<td colspan="3">Email : borihansubsin@gmail.com</td>
			</tr>
		</table>
		';

return $text;
?>