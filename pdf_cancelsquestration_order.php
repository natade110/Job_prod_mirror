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
				<td colspan="3" style="text-align:center">
					<span>คำสั่งเลขาธิการสำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</span><br />
					<span>ที่&nbsp;&nbsp;'.$cancelDocumentNO.'</span><br />
				    <span>เรื่อง&nbsp;ถอนการอายัดทรัพย์สินของ'.$fullCompanyName.'</span>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:center;">
					<div style="font-size:16pt;">__________________________________</div>
					<div style="font-size:5pt;">&nbsp;</div>
				</td>
			</tr>
			<tr>
				<td colspan="3" style="text-indent: 25mm;">
				   <span>ตามที่สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ&nbsp;&nbsp;&nbsp;&nbsp;ได้อายัดทรัพย์สินของ</span><br />
				   <span>'.$fullCompanyName.'&nbsp;&nbsp;&nbsp;ตามคำสั่งเลขาธิการสำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่ง</span>
				   <span>ชาติ ที่ '.$cancelDocumentNO.' ลงวันที่ืืื '.$cancelDateFormat.' นั้น </span>
				</td>
			</tr>
			 <tr><td colspan="3" style="font-size: 12pt">&nbsp;</td></tr>		   		
			<tr>
				<td colspan="3">
				   <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				   <span>บัดนี้ '.$fullCompanyName.' นายจ้างหรือเจ้าของสถานประกอบการ &nbsp;&nbsp;ได้ชำระเงิน ให้แก่ กองทุนส่งเสริมและพัฒนาคุณภาพชีวิติคนพิการกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการครบถ้วน แล้ว</span>
				 
				</td>
			</tr>
			<tr><td colspan="3" style="font-size: 12pt">&nbsp;</td></tr>
			<tr>
	            <td colspan="3">
				   	<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				    <span>อาศัยอำนาจตามความในมาตรา&nbsp;&nbsp;&nbsp;๓๖&nbsp;แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิต คนพิการ ตามมาตรา พ.ศ. ๒๕๕๐&nbsp;&nbsp;มาตรา&nbsp;&nbsp;&nbsp;๔&nbsp;&nbsp;แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ พ.ศ. ๒๕๕๐ </span>
				   	<span>มีคำสั่งให้ถอนอายัดของ'.$fullCompanyName.' ดังต่อไปนี้</span><br />
				   
				    '.$sequestionDetails.'
				</td>
			</tr>
			<tr><td colspan="3" style="text-indent: 40mm;">ทั้งนี้ตั้งแต่บัดนี้เป็นต้นไป</td></tr>
			 <tr><td colspan="3" style="font-size: 12pt">&nbsp;</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>สั่ง ณ วันที่ '.$cancelDateFormat.'</span></td>
			</tr>			
		</table>
';

return $text;