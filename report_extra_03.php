<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
		
	 
<?php

include "db_connect.php";

$the_year = "2021";



if($_GET[mode] == 2){
	
	$mode = 2;
	$lawful_status_in = "1,2";
	
}elseif($_GET[mode] == 3){
	
	$mode = 3;
	$lawful_status_in = "1";
	
}else{
	
	$mode = 1;
	$lawful_status_in = "1,2,3";
	
	
}
//$lawful_status_in = "1";
?>

ข้อมูลใบเสร็จจ้างงานปี 2564


<?php

	$sql = "
		
		
		select 
			* 
		from
			receipt re
				join
					payment pay
					on
					re.RID = pay.RID
					
				join
					lawfulness law
					on
					pay.LID = law.LID
				join
					company
					on
					law.cid = company.cid
		where
			(
				year(re.ReceiptDate) = 2021
				and
				NEPFundPaymentID is not null
			)
			
			/*or 
			re.RID in (
					 		74478
							, 74746
							, 63400
							, 65671
							, 65672
			)*/
			
		order by re.RID desc
			
		-- limit 0 ,100
				
			   
	
	";
	
	$the_result = mysql_query($sql);
	
	
	
	

?>
<table border=1>
	
  <tr>
		<td>
			RID
		</td>
		<td>
			NEPFundPaymentID
		</td>
	  <td>
			CID
		</td>
		<td>CompanyCode</td>
		<td>CompanyName</td>
		<td>ปีการปฏิบัติฯ&nbsp;</td>
		
		<td>
			BookReceiptNo
		</td>
		<td>
			ReceiptNo
		</td>
		<td>
			ReceiptDate
		</td>
		<td>
			จำนวนเงินที่จ่าย
		</td>
		<td>
	  		ต้น ม33
	  	</td>
	 	 <td>
	  		ดอก ม33
	  	</td>
	  
	   <td>
	  		ต้น ม34
	  	</td>
	 	 <td>
	  		ดอก ม34
	  	</td>
	  
	  <td>
	  		ต้น ม35
	  	</td>
	 	 <td>
	  		ดอก ม35
	  	</td>
	  
	    <td bgcolor="#efefef">
	  		รวมต้น
	  	</td>
	 	  <td bgcolor="#efefef">
	  		รวมดอก
	  	</td>
	   <td>
	  		diff
	  	</td>
	 
		
	<tr>
	
	<?php
		while($the_row = mysql_fetch_array($the_result)){				
			
	?>
	<tr>
		<td>
			<?php echo $the_row[RID];?>
		</td>
		<td>
			<?php echo $the_row[NEPFundPaymentID];?>
		</td>
		<td><?php echo $the_row[CID];?></td>
		<td><?php echo $the_row[CompanyCode];?></td>
		<td><?php echo $the_row[CompanyNameThai];?></td>
		<td><?php echo $the_row[Year]+543;?></td>
		
		
		
		<td>
			<?php echo $the_row[BookReceiptNo];?>
		</td>
		<td>
			<?php echo $the_row[ReceiptNo];?>
		</td>
		<td >
			<?php echo $the_row[ReceiptDate];?>
		</td>
		<td align="right">
			<?php echo number_format($the_row[Amount],2);?>
		</td>
		
		<td align="right">
			<?php
			
				$meta_sql = "
					
					SELECT 
						p_amount - p_pending_amount as paid_for_principal
						, p_interests - p_pending_interests as paid_for_interests
					FROM 
						`receipt_meta` a 
							join
							lawful_33_principals b
							on
							a.meta_for = concat(b.p_lid, b.p_from, b.p_to)
					WHERE 
						a.`meta_rid` = ".$the_row[RID]."
				
				";
			
			//echo $meta_sql;
			$meta_33_row = getFirstRow($meta_sql);
			
			echo number_format($meta_33_row[paid_for_principal],2);
			
			?>
		</td>
		<td align="right">
			
			<?php 
				echo number_format($meta_33_row[paid_for_interests],2);
			?>
			
		</td>
		
		<td align="right">
			<?php
			
				$meta_34_sql = "
					
					SELECT 
						GREATEST(p_amount,0) as paid_for_principal
						, GREATEST(p_interests,0) as paid_for_interests
					FROM 
						
						lawful_34_principals aa
						
					WHERE 
						aa.p_to =  ".$the_row[RID]."
						
				
				";
			
			//echo $meta_sql;
			$meta_34_row = getFirstRow($meta_34_sql);
			
			echo number_format($meta_34_row[paid_for_principal],2);
			
			?>
		</td>
		<td align="right">
			
			<?php 
				echo number_format($meta_34_row[paid_for_interests],2);
			?>
			
		</td>
		
		<td align="right">
			<?php
			
				$meta_35_sql = "
					
					SELECT 
						p_amount - p_pending_amount as paid_for_principal
						, p_interests - p_pending_interests as paid_for_interests
					FROM 
						`receipt_meta` a 
							join
							lawful_35_principals b
							on
							a.meta_for = concat('c', b.p_lid, b.p_from, b.p_to)
					WHERE 
						a.`meta_rid` = ".$the_row[RID]."
				
				";
			
			//echo $meta_sql;
			$meta_35_row = getFirstRow($meta_35_sql);
			
			echo number_format($meta_35_row[paid_for_principal],2);
			
			?>
		</td>
		<td align="right">
			
			<?php 
				echo number_format($meta_35_row[paid_for_interests],2);
			?>
			
		</td>
		
		 <td align="right" bgcolor="#efefef">
			
			<?php 
				echo number_format($meta_33_row[paid_for_principal]+$meta_34_row[paid_for_principal]+$meta_35_row[paid_for_principal],2);
			?>
			
		</td>
		
		 <td align="right" bgcolor="#efefef">
			
			<?php 
				echo number_format($meta_33_row[paid_for_interests]+$meta_34_row[paid_for_interests]+$meta_35_row[paid_for_interests],2);
			?>
			
		</td>
		<td bgcolor="#FFFFE0" align="right">
			
			<?php 
				$the_diff = $the_row[Amount] - 
					($meta_33_row[paid_for_principal]+$meta_34_row[paid_for_principal]+$meta_35_row[paid_for_principal])
					-
					($meta_33_row[paid_for_interests]+$meta_34_row[paid_for_interests]+$meta_35_row[paid_for_interests]);
			
				if($the_diff > 00.1){
					
					echo "<font color=red>".number_format($the_diff,2)."</font>";
				}
			?>
			
		</td>
		
		
		
	<tr>
	
	<?php
		}
	?>
	
</table>
	
	
</body>
</html>