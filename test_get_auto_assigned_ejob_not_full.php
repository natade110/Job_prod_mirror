<?php


	include "db_connect.php";
	
	
	//get all bill payments
	$sql = "
	
		select
			*
		from
			bill_payment bill
				join
					receipt rep
					on
					bill.receiptId = rep.rid
				join
					lawfulness law
					on
					law.lid = bill.lid
				join
					company	com
					on
					com.cid = law.cid
				
		where
			bill.LID in (
				
				select
					lid
				from					
					lawfulness
				where
					year in (2021, 2022)
					-- year in (2020)
			
			)
			and
			bill.receiptId in (
			
				select
					meta_rid
				from
					receipt_meta
					
			
			)
		
	
	";
	
?>

	<table border=1>
		<tr>
			<td>
				cid
			</td>
			<td>
				year
			</td>
			<td>
				company name
			</td>
			<td>
				company code
			</td>
			<td>
				rid
			</td>
			<td>
				booknum
			</td>
			<td>
				bookno
			</td>
			<td>
				status
			</td>
			<td>
				bill amount
			</td>
			<td>
				used amount
			</td>
			<td>
				matched/unmatched
			</td>
		</tr>
	</table>

<?php
	
	
	$receipt_result = mysql_query($sql);
				
	while ($receipt_row = mysql_fetch_array($receipt_result)) {
		
		$bill_amount = $receipt_row["TotalAmount"];
		
		//for each bills
		echo "<br>cid" . ";" . $receipt_row["CID"] . ";year;" . $receipt_row["Year"] . ";" . $receipt_row["CompanyNameThai"] . ";" . $receipt_row["CompanyCode"] . ";" . "rid;".$receipt_row["RID"]. ";" . $receipt_row["BookReceiptNo"] . ";" . $receipt_row["ReceiptNo"] . ";" . $bill_amount . ";" . $receipt_row["LawfulStatus"];
		
		//see if used all
		$sql = "
			
			select
				sum(meta_value)
			from
				receipt_meta
			where
				meta_rid = '".$receipt_row["RID"]."'
		
		";
		
		$used_amount = getFirstItem($sql);
		
		echo ";bill_amount is;" . $bill_amount;
		echo ";used_amount is; " . $used_amount;
		
		if($bill_amount != $used_amount){
			echo "<font color=red>;unmatched</font>";			
		}else{
			echo "<font color=green>;matched</font>";	
		}
		
	}