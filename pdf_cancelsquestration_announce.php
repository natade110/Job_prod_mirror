<?php


$text = '
		<table width="100%">
			<tr>
	            <td style="width:158.03mm; " colspan="3">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<img alt="" src="./images/kut.jpg" style="height: 32mm; width:27mm;"/>
	            </td>
	        </tr>
		    <tr>
				<td colspan="3" style="text-align:center;">ประกาศถอนอายัดทรัพย์สิน ของ'.$fullCompanyName.'</td>	
				
			</tr>	
			
			<tr>
				<td colspan="3" style="">	
					<div style="font-size:16pt;text-align:center">__________________________________</div>
					<div style="font-size:5pt;">&nbsp;</div>
				</td>			
			</tr>
			<tr>
            	<td colspan="3" style="text-indent: 25mm;">									
	            	<span>ด้วย '.$fullCompanyName.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	            			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ได้ชำระเงินให้แก่</span><br />	            			
					<span>กองทุนส่งเสริมและพัฒนาคุณภาพชีวิติคนพิการกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ตามคำสั่งอายัด ทรัพย์สินของเลขธิการสำนักงานส่งเสริมและพัฒนา</span>
	            	<span>คุณภาพชีวิตคนพิการแห่งชาติ ที่&nbsp;'.$seqDocumentNO.' ลงวันที่ '.$seqDateFormat.'&nbsp;ครบถ้วนแล้ว</span>
				</td>
			</tr>
	        <tr><td class="3" style="font-size: 12pt">&nbsp;</td></tr>	
			<tr>
            	<td colspan="3"  style="text-indent: 25mm;">
					<span>ดังนั้น&nbsp;&nbsp;&nbsp;&nbsp;เลขาธิการสำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติอาศัยอำนาจ</span><br />
	            	<span>ตามความในมาตรา ๓๖ แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการตามมาตรา พ.ศ. ๒๕๕๐ มาตรา แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ พ.ศ. ๒๕๕๐  จึงมี</span>
	            	<span>คำสั่ง '.$cancelDocumentNO.' ลงวันที่ '.$cancelDateFormat.' ให้ถอนการอายัดทรัพย์สินของ'.$fullCompanyName.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ดังต่อไปนี้</span>
	                '.$sequestionDetails.'
				</td>
			</tr>
	        <tr><td colspan="3" style="font-size: 12pt">&nbsp;</td></tr>	
	        <tr>
	             <td colspan="3" style="text-indent: 25mm;">
	               	<span>ทั้งนี้ ตั้งแต่บัดนี้เป็นต้นไป</span>
	             </td>
	        </tr>	
	        <tr><td colspan="3" style="height: 12pt"></td></tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>ประกาศ ณ ลงวันที่ '.$cancelDateFormat.'</span></td>
			</tr>
			<tr><td colspan="3" style="height: 16pt"></td></tr>
			<tr><td colspan="3" style="height: 16pt"></td></tr>
			<tr><td colspan="3" style="height: 16pt"></td></tr>
			<tr>
				<td colspan="3">
					<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					
						(.....................................................)</span><br />
					<span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						พนักงานเจ้าหน้าที่ผู้ได้รับมอบหมายจากเลขาธิการ</span><br />
					<span>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</span>
				</td>
			</tr>				
		</table>
';

return $text;