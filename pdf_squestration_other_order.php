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
	            	<span>คุณภาพชีวิตคนพิการ รวมเป็นเงินทั้งสิ้น '.$debtTotalAmount.' บาท ('.$debtTotalAmountWord.')</span>
	            	<span>แก่กองทุนส่งเสริมและพัฒนาคุณภาพชีวิติคนพิการ กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการตามมาตรา ๒๕ </span>
	            	<span>แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคน พิการ พ.ศ. ๒๕๕๐</span>
	            </td>
	       </tr>
	       <tr>
	            <td colspan="3">
	            	<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	            	<span>อาศัยอำนาจตามความในมาตรา ๓๖ แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</span>
	            	<span>ตามมาตรา พ.ศ. ๒๕๕๐ มาตรา ๔ แห่งพระราชบัญญัติส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ พ.ศ. ๒๕๕๐ และที่</span>
	             	<span>แก้ไขเพิ่มเติม (ฉบับบที่ ๒) พ.ศ. ๒๕๕๖ และมาตรา ๒๐(๑๐) แห่งพระราชบัญญัติปรับปรุง&nbsp;&nbsp;กระทรวง&nbsp;&nbsp;ทบวง&nbsp;&nbsp;กรม </span>
	            	<span>(ฉบับที่ ๑๔) พ.ศ. ๒๕๕๘ จึงสั่งให้อายัดทรัพ์สินอื่นๆ ดังมีรายการต่อไปนี้</span>	                
	                '.$otherList.'
	            </td>
	       </tr>
	       <tr>
	            <td colspan="3">
	                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	                <span>แต่ทั้งนี้ไม่เกินวงเงิน '.$debtTotalAmount.' บาท ('.$debtTotalAmountWord.') </span>
	                <span>ซึ่งเป็นวงเงินที่'.$fullCompanyName.' ค้างชำระแก่ กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ&nbsp;&nbsp;&nbsp;โดย</span>
	                <span>คำนวณเงินเพิ่ม (ดอกเบี้ย) ถึงวันที่ '.$documentDateFormat.' </span>
	                <span>หากมีการส่งมอบเงินตามตำสั่งอายัดฉบับนี้&nbsp;&nbsp;&nbsp;หลังจากวันที่ '.$documentDateFormat.' จะต้องคำนวณเงินเพิ่ม </span>
	                <span>(ดอกเบี้ย) &nbsp;&nbsp;ตามกฎหมายเพื่อส่งมอบอีกเดือนละ&nbsp;&nbsp;'.$interatePerMonthFormat.'&nbsp;&nbsp;บาท ('.$interatePerMonthFormatText.')</span>
	                <span>&nbsp;หรือวันละ &nbsp;&nbsp;'.$interatePerDayFormat.' บาท ('.$interatePerDayFormatText.')</span>	               
	            </td>
	       </tr>
	       <tr>
	            <td colspan="3">
	                <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
	                <span>ฉะนั้นห้ามมิให้ผู้ใดกระทำให้เสียหาย จำหน่าย จ่าย โอน หรือกระทำอย่างหนึ่งอย่างใด&nbsp;&nbsp;แก่ทรัพย์สิน</span><br />
	                <span>หรือสิทธิเรียกร้องที่อายัด&nbsp;&nbsp;&nbsp;&nbsp;เว้นแต่จะได้รับอนุญาตจาก&nbsp;&nbsp;&nbsp;อธิบดีกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการหรือผู้ซึ่ง</span>
	                <span>อธิบดีมอบหมาย</span>	                                    
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
					<span>&nbsp;&nbsp;&nbsp;&nbsp;อธิบดีกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</span> 
				</td>
			</tr>		
		</table>
';

return $text;
?>