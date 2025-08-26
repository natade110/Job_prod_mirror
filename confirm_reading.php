<?php
require_once 'db_connect.php';

$shid = NULL;
$email = NULL;
$isSuccess = false;

if(is_numeric($_GET["shid"])){
	$shid = intval($_GET["shid"]);
}

if(filter_var($_GET["email"], FILTER_VALIDATE_EMAIL)){
	$email = $_GET["email"];
}

if (!is_null($email) && !is_null($shid)){
	
	$result = mysql_query("
			update schedulecollectionhistory
			set ReceivedDate = COALESCE(ReceivedDate, NOW()) 
			where shid = $shid and Email = '".mysql_real_escape_string($email)."'");
	
	if ($result == true && mysql_affected_rows() == 1){
		$isSuccess = true;
	}
}
?>

<?php include "header_html.php";?>
<td valign="top" style="padding-left: 5px;">
	<h2 class="default_h1" style="margin:0; padding:0;">ยืนยันการอ่านจดหมายแจ้งเตือน</h2>
	<div style="padding-top:10px; font-weight: bold;">
		ขอขอบคุณที่ท่านเสียสละเวลาอันมีค่าช่วยยืนยันการรับทราบจดหมาย ระบบได้บันทึกว่าท่านรับทราบจดหมายแจ้งเตือนแล้ว ท่านสามารถปิดหน้านี้ได้ทันที
	</div>
</td>
</tr>

<!-- footer section -->
<tr>
	<td align="right" colspan="2">
    	<?php include "bottom_menu.php";?>
    </td>
</tr>
</table>
</td>
</tr>
</table>
</div>
<!--end page cell-->
</td>
</tr>
</table>
</body>
</html>
<!-- end footer section -->