<?php


$text = '
		<table>
			<tr>
	            <td style="text-align: center;" colspan="3">
	                <img alt="" src="./images/kut.jpg" />
	            </td>
	        </tr>
		    <tr>
				<td colspan="3" width="610" style="text-align:center">
					<span>คำสั่งอธิบดีกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</span><br />
					<span>ที่&nbsp;&nbsp;'.$documentNo.'</span><br />
				    <span>เรื่อง&nbsp;การอายัดทรัพย์สินของ'.$fullCompanyName.'</span>	
				</td>			
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
	            	<span>ด้วย'.$fullCompanyName.' ไม่นำส่งเงินที่ต้องส่งเข้ากองทุนส่งเสริมและพัฒนา</span>
	            	<span>คุณภาพชีวิตคนพิการ รวมเป็นเงินทั้งสิ้น&nbsp;&nbsp;'.$debtTotalAmount.'&nbsp;&nbsp;บาท ('.$debtTotalAmountWord.')</span>
	            	<span>แก่กองทุนส่งเสริมและพัฒนาคุณภาพชีวิติคนพิการ กรมส่งเสริมและพัฒนาคุณภาพชีวิต</span>
	            	<span>คนพิการตามมาตรา ๒๕ แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคน พิการ พ.ศ. ๒๕๕๐</span>
	            </td>
	       </tr>
	       <tr>
	            <td colspan="3">
	            	<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	            	<span>อาศัยอำนาจตามความในมาตรา ๓๖ แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</span>
	            	<span>ตามมาตรา พ.ศ. ๒๕๕๐ มาตรา ๔ แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ พ.ศ. ๒๕๕๐ และที่</span>
	             	<span>แก้ไขเพิ่มเติม (ฉบับบที่ ๒) พ.ศ. ๒๕๕๖ และมาตรา ๒๐(๑๐) แห่งพระราชบัญญัติปรับปรุง&nbsp;&nbsp;กระทรวง&nbsp;&nbsp;ทบวง&nbsp;&nbsp;กรม</span>
	            	<span>(ฉบับที่ ๑๔) พ.ศ. ๒๕๕๘ จึงสั่งให้อายัดเงินในบัญชีธนาคาร ซึ่ง</span><span>'.$bankName.'</span>
	                <span>จะต้องส่งมอบให้แก่'.$fullCompanyName.' ผู้ไม่นำส่งเงินข้างต้น ดังมีรายการต่อไปนี้</span>
	                '.$bankDetail.'
	            </td>
	       </tr>
	       <tr>
	            <td colspan="3">
	                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	                <span>แต่ทั้งนี้ไม่เกินวงเงิน '.$debtTotalAmount.' ('.$debtTotalAmountWord.') </span>
	                <span>ซึ่งเป็นวงเงินที่'.$fullCompanyName.' ค้างชำระแก่ กองทุนส่งเสริมและพัฒนาคุณภาพชีวิต</span>
	                <span>คนพิการ กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ โดย คำนวณเงินเพิ่ม (ดอกเบี้ย) ถึงวันที่ '.$documentDateFormat.' </span>
	                <span>หากมีการส่งมอบเงินตามตำสั่งอายัดฉบับนี้ หลังจากวันที่ '.$documentDateFormat.'&nbsp;&nbsp;&nbsp;&nbsp;จะต้องคำนวณเงินเพิ่ม </span>
	                <span>(ดอกเบี้ย)ตามกฎหมายเพื่อส่งมอบอีกเดือนละ '.$interatePerMonthFormat.' บาท ('.$interatePerMonthFormatText.')</span>
	                <span>หรือวันละ '.$interatePerDayFormat.' บาท ('.$interatePerDayFormatText.')</span>	               
	            </td>
	       </tr>
	       <tr>
	            <td colspan="3">
	                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	                <span>ฉะนั้น เมื่อหนี้ตามสัญญาดังกล่าวข้างต้นถึงกำหนดการชำระจึงห้ามมิให้</span>
	                <span>'.$bankName.' ชำระ เงินตามจำนวนที่สั่ง</span>
	                <span>อายัดแก่'.$fullCompanyName.' แต่ให้ส่งมอบแก่กองทุนส่งเสริมและพัฒนา</span>
	                <span>คุณภาพชีวิตคนพิการกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ และห้ามมิให้ผู้ใด</span>	  
	                <span>กระทำให้เสียหาย จำหน่าย จ่าย โอน หรือกระทำอย่างหนึ่งอย่างใดแก่ทรัพย์สิน หรือสิทธิเรียกร้องที่อายัดเว้นแต่จะได้รับอนุญาตจากอธิบดีกรมส่งเสริม</span>
	                <span>และพัฒนาคุณภาพชีวิตคนพิการ หรือผู้ซึ่งอธิบดีมอบหมาย</span>	                                    
	            </td>
	       </tr>
	       <tr>
	            <td colspan="3">
	                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	                <span>ทั้งนี้เพื่อนำเงินมาชำระหนี้ที่ค้างอยู่ดังกล่าวข้างต้น ตลอดจนค่าธรรมเนียมและค่าใช้จ่ายในการอายัด</span>
	                <span>ทรัพย์สินรายนี้</span>	                                                
	            </td>
	       </tr>	       
			<tr>
				<td>&nbsp;</td>
				<td colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>ส่ง ณ ลงวันที่ '.$documentDateFormat.'</span></td>
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
					<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(นายสมชาย เจริญอำนวยสุข)</span><br />
					<span>&nbsp;&nbsp;&nbsp;อธิบดีกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</span> 
				</td>
			</tr>		
		</table>
';

return $text;
?>