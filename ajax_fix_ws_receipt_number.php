<?php

	include "db_connect.php";
	
	
	$sql = "
		
		select
			*
		from
			bill_payment
		where
			ReceiptID in (

				select
					RID
				from
					receipt
				where
					concat(BookReceiptNo, ReceiptNO)
					in (

						select
							concat(BookReceiptNo, ReceiptNO)
							-- , count(*)
						from
							receipt
						where
							paymentMethod = 'WS'
						group by
							concat(BookReceiptNo, ReceiptNO)
						having
							count(*) > 1
					
					
					)
			
			)
			
		order by
			concat(ServiceRef1, ServiceRef2) asc
	
	";
	
	$the_result = mysql_query($sql);
	
	while ($the_row = mysql_fetch_array($the_result)) {
		
		$fund_sql = "
		
			select
				*
			from
				nepfund_import_log_detail
			where
				RawText like '%".$the_row[ServiceRef1].$the_row[ServiceRef2]."%'
				
			limit
				0,1
		
		";
		
		
		$fund_row = getFirstRow($fund_sql);
		$fund_text = $fund_row[RawText];
		
		
		$receipt_sql = "select * from receipt where RID = '".$the_row[ReceiptID]."'";
		
		$receipt_row = getFirstRow($receipt_sql);
		//print_r($fund_row);
		
		if($fund_text){
		
		
			$fund_payment_id = trim(substr($fund_text, 35, 7));
			$fund_booknum = trim(substr($fund_text, 55, 25));
			$fund_bookno = trim(substr($fund_text, 80, 25));
			
			$current_bookno = $receipt_row[BookReceiptNo].$receipt_row[ReceiptNo];
			$correct_bookno = $fund_booknum.$fund_bookno;
			
			$current_tid = $receipt_row[NEPFundPaymentID];
			$correct_tid = $fund_payment_id;
			
			echo "<br>current book no/num is: ".$receipt_row[BookReceiptNo].$receipt_row[ReceiptNo] . " with tid = ". $receipt_row[NEPFundPaymentID];
			echo " -- correct book no/num is: ".$fund_booknum.$fund_bookno . " with tid = ". $fund_payment_id;
			
			if($current_bookno != $correct_bookno){
				
				echo "<font color=red> - do fix the thing...</font>";
				
				//if($fund_payment_id == 6754595 ){
					
					$fix_sql = "
								update 
									receipt 
								set 
									BookReceiptNo = '$fund_booknum'
									, ReceiptNo = '$fund_bookno'
									, NEPFundPaymentID = '$fund_payment_id'
								where
									RID = '". $receipt_row[RID]."'
								limit 
									1
									
									";
									
					mysql_query($fix_sql);
					
					echo " - fixed!";
				
				//}
				
			}
			
			
		}
		
	}

?>