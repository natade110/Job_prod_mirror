<?php

	//yoes 20190318
	//---- will only do this is no parent_leid and have some "AFTER" stuffs
	
	
	
	
	$do_calculate_interests = 0;
	
	
	//echo  "assoc_end_date_column_name: ".$the_row[$assoc_end_date_column_name];
	
	if(
	
		!$parent_leid 
		&& $the_row[$assoc_end_date_column_name] >= $year_end_date
		&& !$is_before
		
		){
		
		$do_calculate_interests = 0;
		
	}else{
		
		$do_calculate_interests = 1;
		
	}
	
	//echo "do_calculate_interests: ".$do_calculate_interests;
	//echo "<br>m34_to_pay_total: ".$m34_to_pay_total;
	
	
	
	
	if($m34_to_pay_total && $do_calculate_interests){
		
		

		
		//yoes 20190422
		//no parent or child -> before no after -> get everything
		
		//yoes 20190425
		
		/*echo "<br>parent_leid: " . $parent_leid;
		echo "<br>child_leid: " . $child_leid;
		echo "<br>this_m33_date_diff_before: " . $this_m33_date_diff_before;
		echo "<br>this_m33_date_diff_after: " . $this_m33_date_diff_after;*/
		
		if(
		
			$parent_leid
			
			&&
		
			$child_leid
			
			&&
			
			$is_before
			
			
		){
		
			$select_receipt_start_date = $parent_start_date;
			//$select_receipt_end_date = $current_date;		
			$select_receipt_end_date = $the_row[$assoc_start_date_column_name];
			//echo $the_row[$assoc_start_date_column_name];

			if($this_m33_date_diff_after > 0){						
								
				$select_receipt_start_date = $parent_start_date;
				$select_receipt_end_date = $the_row[$assoc_end_date_column_name];
			}
			
			if($this_m33_date_diff_after <= 0){
			
				$select_receipt_start_date = $parent_start_date;
				
				$select_receipt_end_date = $current_date;
				
			}
			
			
			//echo "<br>asd1123123 ";
		
		}elseif(
		
			$parent_leid
			
			&&
		
			$child_leid
			
			&&
			
			$is_after
			
			
		){
		
		
			//catch-all
			//$select_receipt_start_date =  $parent_start_date;
			//$select_receipt_end_date =  $current_date;
		
			if($this_m33_date_diff_before_deducted > 0){
				//$select_receipt_start_date =  $the_row[$assoc_end_date_column_name];
				//$select_receipt_end_date =  $current_date;
			}
			
			
			if($this_m33_date_diff_before_deducted <= 0){
				$select_receipt_start_date =  $parent_start_date;
				$select_receipt_end_date =  $current_date;
			}
			
		
		}elseif(
		
			$parent_leid
			
			&&
		
			!$child_leid
			
			&&
			
			$is_before
			
			
			
		){
			
			//yoes 20190506
			//catch-all here
			$select_receipt_start_date = $parent_start_date;
			$select_receipt_end_date = $the_row[$assoc_end_date_column_name];	
			
			if($this_m33_date_diff_after > 0){						
				
				//
				$select_receipt_start_date = $parent_start_date;
				$select_receipt_end_date = $the_row[$assoc_end_date_column_name];	
				
			}
			
			if($this_m33_date_diff_after <= 0){
			
				$select_receipt_start_date = $parent_start_date;
				$select_receipt_end_date = $current_date;	
				
			}

			//echo "this_m33_date_diff_after" .  $this_m33_date_diff_after;
			
			//
			//echo "xxxx";
			//echo "<br>5351233232a ";
		
		}elseif(
		
			$parent_leid
			
			&&
		
			!$child_leid
			
			&&
			
			$is_after
		
		){
			
			//catch-all
			
			
			
			if($this_m33_date_diff_before_deducted > 0){
				//$select_receipt_start_date =  $the_row[$assoc_end_date_column_name];
				$select_receipt_start_date = date("Y-m-d", strtotime($the_row[$assoc_end_date_column_name] . ' +1 day'));
				$select_receipt_end_date =  $current_date;
				
				//echo "- this_m33_date_diff_before_deducted - $this_m33_date_diff_before_deducted - ";
				//echo "xxa222?";
			}
			
			
			if($this_m33_date_diff_before_deducted <= 0){
				$select_receipt_start_date =  $parent_start_date;
				$select_receipt_end_date =  $current_date;
				
				//echo "asdasdxxx?";
				
				
			}
			
			//echo "uhh?";
			
			
			
		}elseif(
		
			!$parent_leid
			&&
			!$child_leid
			&&
			$is_before			
						
			
			)
		{
			
			if($this_m33_date_diff_after > 0){
				$select_receipt_start_date = $year_start_date;
				$select_receipt_end_date = $the_row[$assoc_end_date_column_name];
			}
			
			if($this_m33_date_diff_after <= 0){
				$select_receipt_start_date = $year_start_date;
				$select_receipt_end_date = $current_date;
			}
			
			//echo "<br>XXXXXX ";
			
					
		
		}elseif(
			!$parent_leid
			&&
			!$child_leid
			&&
			$is_after
			
			
			)
		{
			
			//yoes 20190422
			//no parent or child -> AFTER no BEFORE -> get everything
			if($this_m33_date_diff_before_deducted > 0){
				$select_receipt_start_date = date("Y-m-d", strtotime($the_row[$assoc_end_date_column_name] . ' +1 day')); // $the_row[$assoc_end_date_column_name];
				$select_receipt_end_date = $current_date;
			}
			
			
			if($this_m33_date_diff_before_deducted <= 0){
				$select_receipt_start_date = $year_start_date;
				$select_receipt_end_date = $current_date;
			}
			
			
			//echo "<br>ssss ";
			
		
		}elseif(
			!$parent_leid
			&&
			$child_leid
			&&
			$is_before
			)
		{
			
			
			
			if($this_m33_date_diff_after > 0){
				
				$select_receipt_start_date = $year_start_date;
				$select_receipt_end_date = $the_row[$assoc_end_date_column_name];
				
			}
			
			
			if($this_m33_date_diff_after <= 0){
				
				$select_receipt_start_date = $year_start_date;
				$select_receipt_end_date = $current_date;
				
				
				//echo "<br>asdasdccxx";
				
			}
			
			
			
		}elseif(
			!$parent_leid
			&&
			$child_leid
			&&
			$is_after						
			&&
			$this_m33_date_diff_before_deducted <= 0
			
			)
		{
			
			
			$select_receipt_start_date = $year_start_date;
			$get_child_start_date_sql = "
				
									
								select
									$assoc_start_date_column_name
								from
									$assoc_table_name
								where
									$assoc_pk_name = '$child_leid'
								
			
								";
								
			//echo $get_child_start_date_sql;
			
			$select_receipt_end_date = getFirstItem($get_child_start_date_sql);
			
			
			
		}elseif(
			!$parent_leid
			&&
			$is_before
			)
		{
		
			//echo "what";
			//echo "<br>xxxccccc";
			
			$select_receipt_start_date = $year_start_date;
			$select_receipt_end_date = $the_row[$assoc_end_date_column_name];
			
			
			//yoes 20190506
			if(!$child_leid && $this_m33_date_diff_after > 0){
				
				
			}
			
			//yoes 20190417
			//if is an "after" calculation ...
			
			//yoes 20190411
			//yoes 20190419 -- is no need date after -> just get all related receipts
			if(!$child_leid && $this_m33_date_diff_after <= 0){
				
				$select_receipt_end_date = $current_date;
				//echo "what";
				//echo "<br>1222da";
				
			}
			
			
			//yoes 20190419
			//if no need date diff after but also have child .....
			//get receipts upto start of new child
			if($child_leid && $this_m33_date_diff_after <= 0){
				
				$get_child_start_date_sql = "
				
									
									select
										$assoc_start_date_column_name
									from
										$assoc_table_name
									where
										$assoc_pk_name = '$child_leid'
									
				
									";
									
				//echo $get_child_start_date_sql;
				
				$select_receipt_end_date = getFirstItem($get_child_start_date_sql);
				
				
				//echo "<br>asdasdccxx";
				
			}
			
			
			
			
			
		}elseif(
			!$parent_leid
			&&
			!$is_before
			&&
			$this_m33_date_diff_before_deducted > 0
			)
		{
			
			//echo "<br>asdasda ";
			
			$select_receipt_start_date =  $the_row[$assoc_end_date_column_name];
			$select_receipt_end_date = $current_date;
			
			//echo "case  !parent_leid !is_before";
			//echo " - select_receipt_start_date - " . $select_receipt_start_date;
			
			
		//yoes 20190419 ??
		}elseif(
		
			$parent_leid	
			
			)
		{
			
			//echo "<br>5321321";
			
			
			if($is_before && $this_m33_date_diff_after > 0){
				
				$select_receipt_start_date =  $the_row[$assoc_start_date_column_name];
				$select_receipt_end_date =  $the_row[$assoc_end_date_column_name];
				
			}elseif($is_before && $this_m33_date_diff_after <= 0){
				
				$select_receipt_start_date =  $the_row[$assoc_start_date_column_name];
				$select_receipt_end_date =  $current_date; //yoes 20190421
				
			}
			
			if(	$is_after){
				$select_receipt_start_date =  $the_row[$assoc_end_date_column_name];
				$select_receipt_end_date =  $current_date;
			}
			
			
			
		
		}else{
			
			
			//echo " case - else";
			
			//echo "<br>asdasdw222 ";
			
			$select_receipt_end_date = $current_date;
			
			
			
			
			
		}
		
		
		//echo "<br>" . $select_receipt_start_date;
		//echo "<br>" . $select_receipt_end_date;
		
			
		//find associated payments that happen in AFTER period
		$sql = "
		
			select 
					rid
					, ReceiptDate
					, Amount
					, BookReceiptNo
					, ReceiptNo
				from
					receipt
				where
					rid in (
			
						select
							meta_rid
						from
							receipt_meta
						where
							meta_for = '$assoc_receipt_meta_name'
							and
							meta_value in ( 
								'$the_id'								
							)
						
					)
					
				and
				ReceiptDate
				between '".$select_receipt_start_date."' and '".$select_receipt_end_date."'
				
				order by 
					ReceiptDate asc
					, BookReceiptNo asc
					, ReceiptNo asc	
					
				
		";

		//echo "<br>select_receipt_start_date " . $select_receipt_start_date;
		//echo "<br>select_receipt_end_date " . $select_receipt_end_date;
		//echo "<br>AFTER sql: ---> ".$sql;
		
		$receipt_result = mysql_query($sql);				
		
		//yoes 20190418 - remove this -> m34_to_pay_pending INCLUDES interests / m34_to_pay_total didn't have intersts
		$m34_to_pay_pending_pricipal = $m34_to_pay_total;
		
		$last_receipt_date = $parent_end_date;
		
		//yoes 20190419
		
		
		$have_34_payment = 0;
		
		while($receipt_row = mysql_fetch_array($receipt_result)){
			
			$have_34_payment = 1;
			
			//init row
			//yoes 20190318
			//see if have meta amount for this receipt
			
			$meta_amount = getFirstItem("
				
				
				select
					meta_value
				from
					receipt_meta
				where
					meta_for = '".$assoc_receipt_meta_name."-".$the_id."-amount'
					
					and
					
					meta_rid = '".$receipt_row[rid]."'
			
				");
				
			
				
			//echo "meta_amount: ". $meta_amount;
			
	
			if($meta_amount){
				
				$receipt_paid_amount = $meta_amount;
				
			}else{
			
				//if not have then just use receipt amount
				$receipt_paid_amount = $receipt_row[Amount];
				
			}
			
			
			
			
			//echo "<br><font color=orangered>Found receipt: " . $receipt_row[ReceiptDate] . "</font>";
			
			//-----------------
			//---  INTEREST START DATE CAN BE:
			//	-> parent_end_date OR 1 april
			//---------------
			
			//echo "<br>this_lawful_year: $this_lawful_year";
			if(
			
				$the_row[$assoc_start_date_column_name] > $receipt_row[ReceiptDate]
				&&
				$parent_leid
				&&
				//$this_id = 315112
				$receipt_row[ReceiptDate] < $year_interest_start_date				
				
				){
			
				
				//yoes 20190503 -> ไม่จำเป็นเสมอไป เช่น กรณีคืนเงิน ..... ?
				//$m34_to_pay_pending_pricipal = 365 * getThisYearWage($this_lawful_year);
				$m34_to_calculate_interests = $m34_to_pay_pending_pricipal;
			
			
			}elseif(
				$the_row[$assoc_start_date_column_name] > $receipt_row[ReceiptDate]
				&&
				//$this_id = 315230
				$receipt_row[ReceiptDate] < $year_interest_start_date
			){
			
				//yoes 20190321
				//if pay จ้างคน หลังจากมีการจ่ายเงิน then ????
				//เงินต้น คืดจากเต็มปี ณ วันนี้
				//yoes 20190503 -> ไม่จำเป็นเสมอไป เช่น กรณีคืนเงิน ..... ?
				$m34_to_calculate_interests = $m34_to_pay_pending_pricipal;
			
			
			}elseif(
				$the_row[$assoc_start_date_column_name] > $receipt_row[ReceiptDate]
				
				||				
				$the_id == 276446
				
				||				
				$the_id == 314578
				
				||				
				$the_id == 319928
				
				||				
				$the_id == 311275
				
				||				
				$the_id == 311276
				
			){
			
				//yoes 20190321
				//if pay จ้างคน หลังจากมีการจ่ายเงิน then ????
				//เงินต้น คืดจากเต็มปี ณ วันนี้
				//yoes 20190503 -> ไม่จำเป็นเสมอไป เช่น กรณีคืนเงิน ..... ?
				$m34_to_pay_pending_pricipal = 365 * getThisYearWage($this_lawful_year);
				$m34_to_calculate_interests = $m34_to_pay_pending_pricipal;
			
			
			}elseif($receipt_row[ReceiptDate] < $the_row[$assoc_end_date_column_name]){
			
				//yoes 20190311
				//if this payment is before SELF_END_DATE then -> only calculate interest for BEFORE
				$m34_to_calculate_interests = $m34_to_pay_pending_pricipal - $m34_to_pay_after;
										
			}else{
				$m34_to_calculate_interests = $m34_to_pay_pending_pricipal;
			}
			
			
			//yoes 20190418
			//echo "<br>m34_to_calculate_interests: ".$m34_to_calculate_interests;
			//$m34_to_calculate_interests = $m34_to_calculate_interests - $principal_amount_from_before;
			
			
			//yoes 20190315
			//if this payment is AFTER SELF_END_DATE AND SELF is within 45 days from last then -> use SELF end date as interest_start_date date
			
			if($the_row[$assoc_start_date_column_name] > $receipt_row[ReceiptDate] && 1 == 0){
								
				//yoes 20190321
				//if pay จ้างคน หลังจากมีการจ่ายเงิน then
				//เงินต้น คืดจากเต็มปี ณ วันนี้				
				$interest_start_date = $year_start_date;
				
				//echo "111111";
			
			}elseif(
			
				$last_receipt_date == $parent_end_date 					
				
					&& $receipt_row[ReceiptDate] > $the_row[$assoc_end_date_column_name]
					
				){
							
							
				//default interest_start_date			
				$interest_start_date = max(date("Y-m-d", strtotime($the_row[$assoc_end_date_column_name] . ' +1 day')), $year_interest_start_date);		
				
				
				//yoes 20190417
				//if no parent
				//and is "before" payment					
				//and start date is sometime after 1 jan this year
				
				if(
				
					!$parent_leid
					
					&&
								
					$is_before	
				
					&&
					
					$the_row[$assoc_start_date_column_name] > $year_start_date	
				
				){
					
					$interest_start_date = $year_interest_start_date;
					
				}
				
				//echo "$last_receipt_date == $parent_end_date ";
				
				//echo "22222";
				
				//yoes 20190325
				//if end date is more than this year then use ... interest start date or parent end date as interest start date (as oppose to use 1 jan of next year)
				if(
					$interest_start_date > $year_end_date 
				
					&& $the_row[$assoc_end_date_column_name] == $year_end_date
					
					){
					
					
					//yoes 20190409 -- parent end day +1
					$interest_start_date = max($year_interest_start_date, date("Y-m-d", strtotime($parent_end_date . ' +1 day')));
					
					
					//echo "<br>22222--> $parent_end_date vs  $interest_start_date";
				}
				
				
				
				//echo $the_row[$assoc_end_date_column_name];
				
				//echo " $interest_start_date > $year_end_date ";
				
				//echo $the_row[$assoc_end_date_column_name];
				
				//yoes 20200422 for https://app.asana.com/0/794303922168293/1171735520264034
				if($the_id == 344036 || $the_id == 344037){
					$interest_start_date = date("Y-m-d", strtotime($parent_end_date . ' +1 day'));
					//echo $interest_start_date;
				}
				
			}else{
				
				
				//never have a payment before
				//yoes 20190311 --> interest start date = start from day+1 from last hire
				$interest_start_date = max(date("Y-m-d", strtotime($last_receipt_date . ' +1 day')), $year_interest_start_date);
				
				
				//echo "<br> $last_receipt_date vs $year_interest_start_date";
				
				
				//echo "33333";
				
			}
			
			
			
			
			
			
			
			
			//-----------------
			//---  INTEREST END DATE CAN BE:
			//	can be	-> latest_payment_date or current_date
			//---------------
			
			$latest_payment_date = $current_date;
			//$latest_payment_date = "2018-08-15"; //testing purpose
			$interest_end_date = min($receipt_row[ReceiptDate], $current_date);
			
			
			
			
			
			//echo "<br>interest_start_date: ".$interest_start_date . " vs interest_end_date: " . $interest_end_date; 
			
			//----------------
			//INTEREST DAYS		
			//yoes 20190429
			if(
				$interest_start_date  > $interest_end_date				
								
				||	$receipt_row[ReceiptDate] < $year_interest_start_date //||yoes 20190502
			
				){				
				
				$interest_start_date = $interest_end_date;
				$interest_days = 0;
				
				//echo "11111";
				
			}else{
				
				$interest_days = max(dateDiffTs(strtotime($interest_start_date), strtotime($interest_end_date), 1),0);
				
				//echo "22222 $interest_start_date vs $interest_end_date";
				
			}
			
			
			
			
			
			
			//yoes 20190220 -> m34_to_pay_pending_origin is PENDING for use in echoing the message
			$m34_to_pay_pending_origin = $m34_to_pay_pending_pricipal;
			$interest_amount = $interest_days  * (7.5/100/365) * $m34_to_calculate_interests ;
			
			//echo "interest_amount = $interest_days  * (7.5/100/365) * $m34_to_calculate_interests ";
			
			$interest_amount = max($interest_amount, 0);
			
			//$interest_amount = $interest_amount + $interest_amount_from_before;
			
			$interest_amount = round($interest_amount,2);					
			
			$interest_amount_after += $interest_amount;
			
			//echo "<br>interest_amount: ".$interest_amount;
			//echo "<br>interest_amount_after: ".$interest_amount_after;
								
			//yoes 20190218
			$m34_total_interest += $interest_amount;
			
			//echo "<br>m34_total_interest: $m34_total_interest";
			
			
			//reset for next loop
			//$m34_to_pay_pending_pricipal = $m34_to_pay_pending_pricipal - ($receipt_paid_amount-$interest_amount) ;
			$m34_to_pay_pending_pricipal = $m34_to_pay_pending_pricipal - ($receipt_paid_amount-$interest_amount) +$principal_amount_from_before  +$interest_amount_from_before ; //yoes 20190418 add
			$m34_to_pay_pending_pricipal = round($m34_to_pay_pending_pricipal,2);	
			
			//echo "<br>m34_to_pay_pending_pricipal" . $m34_to_pay_pending_pricipal;

			
			//echo "principal_amount_from_before" . $principal_amount_from_before;
			
			if($show_message == 1){
			
			
				
				
				echo "<span id='m33_details_".$receipt_row[rid]."'>";
			
				echo "<br><font color=blue>มีจ่ายเงินวันที่ ".formatDateThai($receipt_row[ReceiptDate])			
					." เล่มที่ ".$receipt_row[BookReceiptNo]." ใบเสร็จเลขที่ ".$receipt_row[ReceiptNo]." จำนวนเงิน ".number_format($receipt_paid_amount,2)." บาท </font>";						
				
				
				if($m34_to_pay_pending_origin){
					echo " <font size=smaller>เงินต้นคงเหลือ ณ วันนี้ " . number_format($m34_to_calculate_interests,2). " บาท</font>";
				}
				
				
				if($interest_amount){
					echo " <font size=smaller>ดอกเบี้ย ณ วันนี้ " . number_format($interest_amount,2). " บาท (ดอกเบี้ย $interest_days วัน 							
					
						คิดจากวันที่ " . formatDateThai($interest_start_date) . " ถึงวันที่ " . formatDateThai($interest_end_date) . " 
						
						คิดจากเงินต้น " . number_format($m34_to_calculate_interests,2) . " บาท
						
						)</font>"; 
						
						
				}
				
				
				//yoes 20190418
				if($principal_amount_from_before != 0 ){
					echo " <font size=smaller color=magenta>+เงินต้นคงเหลือยกมา " . number_format($principal_amount_from_before,2). " บาท</font>";
					
				}
				//yoes 20190418
				if($interest_amount_from_before){
					echo " <font size=smaller color=magenta>+ดอกเบี้ยยกมา " . number_format($interest_amount_from_before,2). " บาท</font>";
					
				}
				
				
				
				if($receipt_paid_amount){
					
					
					if($interest_amount){
						echo " จ่ายเป็นดอกเบี้ย " . number_format($interest_amount,2). " บาท ";
					}
					
					//yoes 20190418
					if($interest_amount_from_before){
						echo " <font size=smaller color=magenta>+จ่ายเป็นดอกเบี้ยยกมา " . number_format($interest_amount_from_before,2). " บาท</font>";
						
					}
					
					echo "จ่ายเป็นเงินต้น " . number_format($receipt_paid_amount-$interest_amount,2). " บาท	";

					
					if($principal_amount_from_before != 0){
						echo " <font size=smaller color=magenta>+จ่ายเป็นเงินต้นคงเหลือยกมา " . number_format($principal_amount_from_before,2). " บาท</font>";
						
					}
					
					
					echo "เงินต้นคงเหลือหลังจากชำระเงิน " . number_format($m34_to_pay_pending_pricipal,2) . " บาท
					
					</font>";
					
				}
				
				
				echo "</span>";
				
				
			}
			
			
			
			$last_receipt_date = $receipt_row[ReceiptDate];
			
			$m34_total_paid += $receipt_paid_amount;					
			
			
			//clean after used
			$principal_amount_from_before = 0;
			$interest_amount_from_before = 0;
			
			
			//yoes 20190803
			$last_receipt_row_ReceiptDate = str_replace(" 00:00:00", "",$receipt_row[ReceiptDate]);
			
			
		} 
		
		
		
		
//-------------------------------------------------------
//
//			CLOSED THE LOOP
//
//
//
//
//-------------------------------------------------------			
		
		
		//echo "m34_to_pay_pending: " . $m34_to_pay_pending;
		
		//	-------------
		//	-------------
		//	-------***************** 	end loop for associated receipts
		//	-------------
		//	-------------
		
		
		
		// ----------------- still in IF ($m34_to_pay_total){
		
		
		
		
		//yoes 20190220
		//also add total payment from parents
		
		
		
		
		$m34_total_paid = $m34_total_paid + $parent_total_paid;
		
		//echo "<br>m34_total_paid 1 $the_id: $m34_total_paid";
		
		//yoes 20190503
		//BEFORE จ่ายเกิน
		if($principal_amount_from_before < 0){
			
			$m34_to_pay_pending_pricipal = $m34_to_pay_pending_pricipal + $principal_amount_from_before; //use "+" because principal_amount_from_before is NEGATIVE
			
			if($show_message == 1){
				
				echo " <font size=smaller color=magenta>+หักเงินต้นที่จ่ายเกิน " . number_format($principal_amount_from_before,2). " บาท</font>";
				
			}
			
			
		}else{
		
			$m34_to_pay_pending_pricipal = $m34_to_pay_pending_pricipal;//- $parent_total_paid;
		}
		
		//echo "m34_to_pay_pending: " . $m34_to_pay_pending;
		
		
		
		


		//echo "m34_to_pay_pending_pricipal" . $m34_to_pay_pending_pricipal;

		
		//yoes 20190220
		//interests stuffs for "AFTER"
		//..... practically closing the loop
		if($m34_to_pay_pending_pricipal > 0){
		
			//-----------------
			//---  INTEREST START DATE CAN BE:
			//	-> parent_end_date OR 1 april
			//---------------
			
			
			
			//yoes 20190315 -- is top most member -> calculate interests from AFTER only
			//yoes 20190318 -- is top most and not end in self 
			if(
			
					!$parent_leid 
						
						&& 
						$the_row[$assoc_end_date_column_name] < $year_end_date	
					
						&&
						
						!$is_before 
					
					){
				
				$m34_to_calculate_interests	 = $m34_to_pay_pending_pricipal;
			}else{
				$m34_to_calculate_interests	 = $m34_to_pay_pending_pricipal;
			}
			
			//echo "<br>m34_to_calculate_interests: ".$m34_to_calculate_interests;
			
			
			
			//echo "<br>last_receipt_date: $last_receipt_date";
			
			//yoes 20190315
			//if this record is before 01 JAN and BEFORE 31 DEC -> and end contract after $year_interest_start_date then
			//	-> calculate interests from SELF END DATE
								
			if(
				
				$the_row[$assoc_start_date_column_name] <= $year_start_date	//record is before 01 JAN
				
				&&
			
				$the_row[$assoc_end_date_column_name] <= $year_end_date	//BEFORE 31 DEC
				
				&& 
				
				$the_row[$assoc_end_date_column_name] > $year_interest_start_date	//END SELF AFTER 1 APRIL						
										
			){
				
				//$interest_start_date = date("Y-m-d", strtotime($the_row[$assoc_end_date_column_name] . ' +1 day'));
				
				//yoes 20200326 -> interim fix for this
				//if have last receipt date then should use last receipt date+1 as interest start date?
				//as per sana ID https://app.asana.com/0/794303922168293/1168382914837953
				//if($the_id == 299622){
				$interest_start_date = max(date("Y-m-d", strtotime($last_receipt_date . ' +1 day')), date("Y-m-d", strtotime($the_row[$assoc_end_date_column_name] . ' +1 day')) , $year_interest_start_date);
				//}
				
				//echo "1";
				
			}elseif(
				
				!$parent_leid
				
				&&
			
				$the_row[$assoc_end_date_column_name] < $year_end_date	//BEFORE 31 DEC
				
				&& 
				
				$the_row[$assoc_end_date_column_name] > $year_interest_start_date	//END SELF AFTER 1 APRIL
								
				
				&&
				
				!$is_before
			
			){
				
				//echo "year_end_date: $year_end_date";
			
				//yoes 20190315 - if no have parent then -> this START interest date is SELF end date
				$interest_start_date = date("Y-m-d", strtotime($the_row[$assoc_end_date_column_name] . ' +1 day'));
				
				//yoes 20190417 ??? --> if have receipt then use receipt
				$interest_start_date = max(date("Y-m-d", strtotime($last_receipt_date . ' +1 day')), $interest_start_date);
				
				//echo "2";
				//echo "interest_start_date: ".$interest_start_date;
				
			
			}elseif(
				
				
				//yoes 20190421
				$is_after
				
				&&
				
				$parent_leid
				
				&&
			
				$the_row[$assoc_end_date_column_name] < $year_end_date	//BEFORE 31 DEC
				
				&& 
				
				$the_row[$assoc_end_date_column_name] > $year_interest_start_date	//END SELF AFTER 1 APRIL
			
			){
			
				$interest_start_date = max(date("Y-m-d", strtotime($last_receipt_date . ' +1 day')), date("Y-m-d",strtotime($the_row[$assoc_end_date_column_name] . ' +1 day')) , $year_interest_start_date);
			
			}else{
				
				
				
				//echo "$last_receipt_date vs $year_interest_start_date"; 
				
				$interest_start_date = max(date("Y-m-d", strtotime($last_receipt_date . ' +1 day')), $year_interest_start_date);
				
				//echo "3";
				
			}
			
			
			//echo "<br>interest_start_date ===> ".$interest_start_date;
			
			//-----------------
			//---  INTEREST END DATE CAN BE:
			//	can be	-> latest_payment_date or current_date
			//---------------
			
			$interest_end_date = $current_date;
			
			
			//yoes 20190417
			//--- if this BEFORE and have money left -> try to find applicable end date
			if($is_before){

				$sql = "
				
						select 
							ReceiptDate
						from
							receipt
						where
							rid in (
					
								select
									meta_rid
								from
									receipt_meta
								where
									meta_for = '$assoc_receipt_meta_name'
									and
									meta_value in ( 
										'$the_id'								
									)
								
							)
							
						and
						ReceiptDate
						> '".$select_receipt_end_date."'
						
						order by 
							ReceiptDate asc							
							
						limit 0,1 
						
				";
				
				//echo "<br>ssss -> ".$sql;
				
				$interest_end_date = getFirstItem($sql);
				
				if(!$interest_end_date){
					$interest_end_date = $current_date;
				}
				
				
			}
			
			
			//$latest_payment_date = "2018-08-15"; //testing purpose
			
			
			//----------------
			//INTEREST DAYS
			
			//echo "-- " . $receipt_row[ReceiptDate];
			
			
			if(
			
				//yoes 20190502
				//if have some payment ... and that payment is less than $year_interest_start_date
				//then don't need to calculate interests
				!($last_receipt_date == $parent_end_date)
				&&
				$last_receipt_date < $year_interest_start_date				
				&& 
				$have_34_payment
				
			){
				//echo "last_receipt_date: $last_receipt_date";
				$interest_days = 0;
				
			}elseif(
				
				//yoes 20190803
				//if pay before doing 33
				//no interest for this ...
				$last_receipt_row_ReceiptDate
				&&
				$last_receipt_row_ReceiptDate != "0000-00-00"
				&&
				$last_receipt_row_ReceiptDate != "0000-00-00 00:00:00"
				&&
				$last_receipt_row_ReceiptDate <= $the_row[$assoc_start_date_column_name]
				
			
			){
				
				//echo "--last_receipt_date-- ". $receipt_row[ReceiptDate];		
				//echo "--assoc_start_date_column_name-- ". $the_row[$assoc_start_date_column_name];
				
				//$interest_start_date = $the_row[$assoc_start_date_column_name];
				$interest_days = 0;
				
			}else{
				
				$interest_days = max(dateDiffTs(strtotime($interest_start_date), strtotime($interest_end_date), 1),0);
				
			}
			
			
			$interest_amount = $interest_days  * (7.5/100/365) * $m34_to_calculate_interests ;
			
			//echo "interest_amount = $interest_days  * (7.5/100/365) * $m34_to_calculate_interests ;";
			
			$interest_amount = round($interest_amount,2);
			
			//echo $interest_amount;
			
			$interest_amount_after += $interest_amount;
			
			//echo "<br>m34_total_interest 1.4 -> ".$m34_total_interest;
			
			$m34_total_interest += $interest_amount;
			
			//echo "<br>m34_total_interest 1.5 -> ".$m34_total_interest;
			
			
			//reset for PRINT RESULT --> You need this					
			
			$m34_to_pay_pending_origin = $m34_to_pay_pending_pricipal;
			
			//echo "<br>m34_to_pay_pending = $m34_to_pay_pending - ($receipt_paid_amount-$interest_amount) ";
			
			$m34_to_pay_pending_pricipal = $m34_to_pay_pending_pricipal - $interest_amount;
			//$m34_to_pay_pending = $m34_to_pay_pending + $interest_amount  ;
			
			
			$m34_to_pay_pending_pricipal = round($m34_to_pay_pending_pricipal,2);				


			
								
			$last_receipt_date = $receipt_row[ReceiptDate];
			
			
			
			//echo "show_message ".$show_message;
			
			if($show_message == 1){
				
				if($interest_amount != 0){
					
					echo "<font color=orangered>+ดอกเบี้ย "
					. $interest_days . " วัน"
					. number_format($interest_amount,2) ." บาท";
					echo "</font>";
					
					echo " ดอกเบี้ยคิดจากวันที่ " . formatDateThai($interest_start_date) . "";
					echo " ถึงวันที่ " . formatDateThai($interest_end_date) . "";
					
					echo " คิดจากเงินต้น " . number_format($m34_to_calculate_interests,2) . " บาท";
					
					
				}
				
				
			}
			
			
			
		}
		
		
		
		//
		//yoes 20190418 - also add "LAST" interestes here
		//$m34_to_pay_pending
		
		
		
		// end if($m34_to_pay_pending){
		// ***** END IF ******
			
		
		
		//echo "<br>principal_amount_from_before".$principal_amount_from_before;
		
		//echo "m34_to_pay_pending_pricipal" . $m34_to_pay_pending_pricipal;
		
		//yoes 20190305 - จ่ายเกิน กรณีคืนเงิน
		if($is_before && $m34_to_pay_pending_pricipal < 0){
			
			$interest_amount_from_before = 0;
			$principal_amount_from_before = $m34_to_pay_pending_pricipal;
			
		}elseif($is_before && $m34_to_pay_pending_pricipal > 0){
			
			//yoes 20190418
			$interest_amount_from_before = $interest_amount;
			$principal_amount_from_before = $m34_to_calculate_interests;
			
		}else{
			
			$interest_amount_from_before = 0;
			$principal_amount_from_before = 0;
			
		}
		
		//echo "principal_amount_from_before" . $principal_amount_from_before;
		
		
		
		//yoes 20190418 ?
		$m34_to_pay_pending = $m34_to_pay_pending_pricipal;
		
	
	} //end IF if($m34_to_pay_total && $do_calculate_interests){
	
	
	//echo "$the_id | $m34_to_pay_total | $do_calculate_interests | $parent_leid |";

?>