<?php


$text = '
		<table>
			<tr>
	            <td style="text-align: center;" colspan="3">
	                <img alt="" src="./images/kut.jpg" />
	            </td>
	        </tr>
		    <tr>
				<td colspan="3" width="610" style="text-align:center">ประกาศอายัดทรัพย์สิน ของ'.$fullCompanyName.'</td>			
			</tr>	
			
			<tr>
				<td colspan="3" width="610" style="text-align:center;">	
					<div style="font-size:16pt;">__________________________________</div>
					<div style="font-size:5pt;">&nbsp;</div>
				</td>			
			</tr>
							
	        <tr>
	            <td colspan="3">
					<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	            	<span>ด้วย'.$fullCompanyName.' นายจ้างหรือเจ้าของสถานประกอบการไม่นำเงินส่ง</span>
					<span>เงินที่ต้องส่งเข้ากองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ รวมเป็นเงินทั้งสิ้น</span>
					<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$debtTotalAmount.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;บาท ('.$debtTotalAmountWord.') </span>
					<span>อธิบดีกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ โดยอาศัยอำนาจตามความในมาตรา ๓๖</span>
					<span>แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ พ.ศ. ๒๕๕๐ มาตรา ๔ แห่งพระราชบัญญัติ</span>
				    <span>ส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ พ.ศ. ๒๕๕๐ และที่แก้ไขเพิ่มเติม (ฉบับที่ ๒) พ.ศ. ๒๕๕๖</span>
				    <span>และมาตรา ๒๐(๑๐) แห่งพระราชบัญญัติปรับปรุงกระทรวง ทบวง กรม (ฉบับที่ ๑๔) พ.ศ. ๒๕๕๘ จึงมี</span>
					<span>คำสั่งที่ '.$documentNo.' ลงวันที่ '.$documentDateFormat.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ให้อยัดทรัพย์สินของ'.$fullCompanyName.' </span>
					<span>นำไปชำระหนี้ข้างต้น ดังมีรายการต่อไปนี้</span>
				    '.$sequestionDetails.'					
	            </td>
	        </tr>
			<tr>
				<td colspan="3">
				    <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				    <span>ฉะนั้น จึงห้ามมิให้ผู้ใดกระทำให้เสียหาย&nbsp;&nbsp;&nbsp;จำหน่าย&nbsp;&nbsp;จ่าย โอน&nbsp;&nbsp;&nbsp;&nbsp;หรือกระทำอย่างหนึ่งอย่างใดแก่</span><br />
				    <span>ทรัพย์สินหรือสิทธิเรียกร้องที่อายัด เว้นแต่จะได้รับอนุญาตจากอธิบดีกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการหรือ</span>
				    <span>ผู้ซึ่งอธิบดีมอบหมายแล้วเท่านั้น ผู้ใดจะคัดค้านการอายัดทรัพย์สินรายนี้ให้ยื่นคำคัดค้านได้ </span>
				    <span>ณ กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการใน ๑๕ วัน นับแต่วันที่ได้รับคำสั่งอายัดทรัพย์สินหรือ</span>
				    <span>ก่อนกำหนดการส่งมอบทรัพย์สิน แล้วแต่กรณี</span>				   
				</td>
		    </tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>ประกาศ ณ ลงวันที่ '.$documentDateFormat.'</span></td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">
					<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(นางณฐอร อินทร์ดีศรี)</span><br />
					<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;พนักงานเจ้าหน้าที่ผู้ได้รับมอบหมายจาก</span><br />
					<span>อธิบดีกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</span> 
				</td>
			</tr>		
		</table>
';

return $text;
?>