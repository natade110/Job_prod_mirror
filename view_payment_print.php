<?php

	include "db_connect.php";

	include "functions.php";
	
	include "session_handler.php";

	
	if(is_numeric($_GET["id"])){
	
		$this_id = doCleanInput($_GET["id"]);
		
		$sql = "select * 
								from 
									fnd_receipt
								where 
									ID  = '$this_id'
								limit 0,1";
		
		//echo $sql;
	
		$post_row = getFirstRow($sql);
		
		//echo count($post_row); exit();								
								
		//GET RECEIPT etc
		$this_receipt_no = $post_row["ReceiptNo"];
		$this_receipt_book = $post_row["ReceiptBookNo"];
		
		$this_province_id = $post_row["ReceiptProvince"];
		$this_acct_no = $post_row["AcctNo"];
		
		//print_r($post_row);	
		$mode = "edit";
		
		$sql = "select * from fnd_current_loans where loan_number = '".$this_acct_no."' and loan_province = '$this_province_id'";	
		$loan_row = getFirstRow($sql);
		
	}else{
	
		exit();
	
	}
	
	
	
	
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style>
@media print {
.header, .hide { visibility: hidden }
}
</style>
</head>

<body style="font-size:13px; font-family:Arial, Helvetica, sans-serif; width:750px;  ">
<table width="100%" border="0" align="center" >
<tr>
  <td align="left" valign="top">เล่มที่ <?php echo $this_receipt_book;?>
  
  <?php if($_GET["copy"] == 1){?>
  <br />(สำเนา)
  <?php }?>
  
  </td>
  <td align="center">
<img src="decors/garuda3.jpg" width="70" />
</td>
  <td align="right" valign="top">เลขที่ <?php echo addLeadingZeros($this_receipt_no,3);?></td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
  <td align="center">ใบเสร็จรับเงิน</td>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
  <td align="center">กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ</td>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
  <td align="center">สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</td>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
  <td align="center">กระทรวงพัฒนาสังคม และความมั่นคงของมนุษย์</td>
  <td align="center">&nbsp;</td>
</tr>
</table>
<table width="100%" border="0">
  <tr>
    <td><table  border="0" align="right">
      <tr>
        <td align="left">ที่ทำการ</td>
        <td align="left">
        
        <?php if($this_province_id == 1){?>
    	    สนง.ส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ
        <?php }else{?>
	        สนง.พัฒนาสังคมและความมั่นคงของมนุษย์ จังหวัด<?php echo getProvinceName($this_province_id);?>
        <?php }?>
        
        </td>
      </tr>
      <tr>
        <td align="left">วันที่</td>
        <td align="left"> <?php echo formatDateThai($post_row["ReceiptDate"]);?> </td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="100%" border="0" align="center">
  <tr>
    <td width="15%">ได้รับเงินจาก</td>
    <td><?php echo $post_row["PayerName"];?></td>
  </tr>
  <tr>
    <td>เป็นค่า</td>
    <td width="80%"><?php echo formatPayFor($post_row["PaymentType"]);?></td>
  </tr>
  <tr>
    <td>จำนวน</td>
    <td><?php echo number_format($post_row["Amount"],0);?> บาท</td>
  </tr>
  <tr>
    <td colspan="2">ไว้เป็นการถูกต้องแล้ว</td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="100%"><table width="95%"  border="1" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
      <tr>
        <td width="30%">เลขที่สัญญา</td>
        <td><?php echo $post_row["AcctNo"];?></td>
      </tr>
      <tr>
        <td>เลขที่ธนาณัติ</td>
        <td><?php echo $post_row["Ref1"];?></td>
      </tr>
      <tr>
        <td>เลขที่เช็ค/ธนาคาร</td>
        <td width="80%"><?php echo $post_row["Ref2"];?></td>
      </tr>
      <tr>
        <td>ลงวันที่</td>
        <td><?php 
				
				if($post_row["Ref1"]){
					echo formatDateThai($post_row["RefDate1"]);
				}
				if($post_row["Ref2"]){
					echo formatDateThai($post_row["RefDate2"]);
				}
				
			?></td>
      </tr>
      <tr>
        <td>เงินทีส่งตาม ม.34</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>ดอกเบีย</td>
        <td>&nbsp;</td>
      </tr>
    </table></td>
    <td width="0" rowspan="2"><table  border="0" align="right">
      <tr>
        <td align="center">(ลงชื่อ)......................................................................</td>
      </tr>
      <tr>
        <td align="center">(......................................................................) </td>
      </tr>
      <tr>
        <td align="center">ตำแหน่ง......................................................................</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="95%" border="1" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; clear:both">
      <tr>
        <td align="center">ยอดกู้ยกมา</td>
        <td align="center">ชำระวันนี้</td>
        <td align="center">ยอดคงเหลือ</td>
        </tr>
      <tr>
        <td align="center">
         <?php if($post_row["PaymentType"] == 1 && $loan_row["loan_amount"]){ 
			   
			   		//try to get paid amount
					
					$sql = "select sum(Amount)
					 from fnd_receipt 
					 where AcctNo = '".$this_acct_no."' 
					 and ReceiptProvince = '$this_province_id'
					 and ReceiptDate < '".$post_row["ReceiptDate"]."'
					 and Status = '0'
					 ";
					 
					// echo $sql;
					
					$paid_amount = getFirstItem($sql);
			   
			   		$current_balance = $loan_row["loan_amount"] - $paid_amount;
			   
			   ?> 
            		 <?php echo formatNumber($current_balance);?> บาท
             
              
              <?php }?>
        
        </td>
        <td align="center">
        
        <?php if($post_row["PaymentType"] == 1){?>
            
            	<?php echo number_format($post_row["Amount"],0);?> บาท
        <?php }?>
        
        </td>
        <td align="center"><?php if($post_row["PaymentType"] == 1 && $loan_row["loan_amount"]){ 
			   
			   		//try to get paid amount
					
					$sql = "select sum(Amount)
					 from fnd_receipt 
					 where AcctNo = '".$this_acct_no."' 
					 and ReceiptProvince = '$this_province_id'
					 and ReceiptDate <= '".$post_row["ReceiptDate"]."'
					 and Status = '0'
					 ";
					 
					// echo $sql;
					
					$paid_amount = getFirstItem($sql);
			   
			   		$current_balance = $loan_row["loan_amount"] - $paid_amount;
			   
			   ?> 
            		 <?php echo formatNumber($current_balance);?> บาท
             
              
              <?php }?></td>
        </tr>
    </table></td>
  </tr>
</table>    


</body>
</html>
<script type="text/javascript">
window.print();
window.onfocus=function(){ window.close();}
</script>