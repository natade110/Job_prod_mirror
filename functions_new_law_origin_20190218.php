<?php

	function get33DeductionByLeidStartStopArray($the_leid, $as_of_date = ""){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		
		//echo "what"; exit();	
		$parent_leid = getParentOfLeid($the_leid);
		$child_leid = getChildOfLeid($the_leid);
		
		$the_row = getFirstRow("select * from lawful_employees$es_table_name_suffix where le_id = '$the_leid'") ;
		
		//
		$year_start_date = $the_row[le_year]."-01-01";	
		$year_end_date = $the_row[le_year]."-12-31";
		
		$this_lawful_year = $the_row[le_year];
		
		//
		$parent_end_date = $year_start_date;
		
		//
		$interest_start_date = $the_row[le_year]."-04-01";
		
		//yoes 20190208
		//need interest start/end date for BEFORE and AFTER value
		$interest_start_date_after = "0000-00-00";
		$interest_end_date_after = "0000-00-00";
			
		//yoes 20190208 - current date is always date now()
		$current_date = date('Y-m-d');
			
		
		//yoes 20190125
		///check if have any payment specifically for this leid
		//first check for self
		$sql = "
			
			select 
					rid
					, ReceiptDate
				from
					receipt
				where
					rid in (
			
						select
							meta_rid
						from
							receipt_meta
						where
							meta_for = '33_for'
							and
							meta_value in ( 
								'$the_leid'								
							)
						
					)
					
				order by 
					ReceiptDate asc, rid asc
				limit 0, 1
		
		"; //
		
		//echo $sql;
		
		$latest_payment_date_array = getFirstRow($sql);
		
		//echo "<br>".$the_leid;
		
		$latest_payment_date = $latest_payment_date_array[ReceiptDate];
		$latest_payment_rid = $latest_payment_date_array[rid];
		
		//echo "latest_payment_date: ".$latest_payment_date;
			
		//echo $latest_payment_date; exit();
		
		if($latest_payment_date && !$leid_filters){
			
			//have payment from self -> calculate interests to self date
			$interest_end_date = $latest_payment_date;
			//echo $interest_end_date;
			
		}else{
			
			$interest_end_date = date('Y-m-d');
			
		}
		
		//yoes 20190208 -> calculate total paid amount for this chain ....
		//
		
		//echo "total_paid_amount -> ".$total_paid_amount; 
		
		//echo $interest_end_date; exit();
		
		
		//yoes 20190104
		//$interest_end_date = "2018-03-27";
		//$interest_end_date = "2018-04-01";
		
		if($the_row[le_start_date] == "0000-00-00" || $the_row[le_start_date] <= $the_row[le_year]."-01-01"){		
			$the_row[le_start_date] = $year_start_date;	
		}
		
		if($the_row[le_end_date] == "0000-00-00"){		
			
			//yoes 20190207
			$le_end_date_still_employed = 1;
			
			$the_row[le_end_date] = $year_end_date;	
		}
		
		//
		
		//echo "child_leid: $child_leid";
		
		//echo "interest_start_date: ".$interest_start_date;
		
		//yoes 20181029 -- need to have "before" and "after" amount
		if($parent_leid){
			//
			$parent_end_date = get3345DaysParentEndDate($the_leid);		
			
			//echo "interest_start_date $interest_start_date < parent_end_date $parent_end_date";
					
			//yoes 20190109
			//if parent end date is <= this year then -> disregard that
			$parent_end_date_year = substr($parent_end_date, 0,4);
			if($parent_end_date_year < $this_lawful_year){
				
				//$parent_end_date = $the_row[le_start_date];
				$parent_end_date = $year_start_date;
			}
			
			
			//echo "parent_end_date $parent_end_date";
			
			//yoes 20190103 - something about interest date
			//if parent_end_date come later than 1 april then use parent end date as interest start date
			if($interest_start_date < $parent_end_date){
				$interest_start_date = $parent_end_date;
			}
			
			
			
			//
			
			//have parent -> use parent end date as start_date		
			//echo "$parent_end_date == $the_row[le_start_date]";
			
			if($parent_end_date == $the_row[le_start_date]){
				$this_m33_date_diff_before = 0;
			}else{		
				$this_m33_date_diff_before = dateDiffTs(strtotime($parent_end_date), strtotime($the_row[le_start_date]),-1);	
				
			}
			
			//echo "interest_start_date $interest_start_date < parent_end_date $parent_end_date";
			//echo "this_m33_date_diff_before ".$this_m33_date_diff_before;
			
			
			if($this_m33_date_diff_before <= 45){
				$this_m33_date_diff_before_deductable_days = $this_m33_date_diff_before;
			}
			
		}else{
			$this_m33_date_diff_before = dateDiffTs(strtotime($year_start_date), strtotime($the_row[le_start_date]),0);	
		}
		
		
		//
		
		$this_m33_date_diff_before_deducted = $this_m33_date_diff_before-$this_m33_date_diff_before_deductable_days;
		
		
		if($child_leid){
			//have child -> no need calculate "after"
			$this_m33_date_diff_after = 0;
		}else{
			$this_m33_date_diff_after = dateDiffTs(strtotime($the_row[le_end_date]), strtotime($year_end_date),0);	
		}
		
		//yoes 20181029 -- deductable able
		$this_m33_date_no_pay = dateDiffTs(strtotime($the_row[le_start_date]), strtotime($the_row[le_end_date]),1);	
			
		//$this_m33_date_diff = dateDiffTs(strtotime($the_row[le_start_date]), strtotime($the_row[le_end_date]),$the_off_set);	
		
		//echo $this_m33_date_diff;
		$this_year_wage =  getThisYearWage($the_row[le_year]);	
		
		//split this to before +after
		$m34_to_pay_before = $this_year_wage * ($this_m33_date_diff_before); 
		$m34_to_pay_before_deducted = $this_year_wage * ($this_m33_date_diff_before_deducted);
		
		//echo "<br>m34_to_pay_before - ".$m34_to_pay_before;
		//echo "<br>this_m33_date_diff_before_deducted - ".$this_m33_date_diff_before_deducted;
		
		//yoes 20190208
		//???
		$m34_to_pay_after= $this_year_wage * ($this_m33_date_diff_after);	
		
		$m34_no_need_pay = $this_year_wage * ($this_m33_date_no_pay);	
		
		$m34_deductable = $this_year_wage * ($this_m33_date_diff_before_deductable_days);	
		
		//total to pay (all years)
		//$m34_to_pay_deduction = 365*$this_year_wage-$m34_to_pay_before-$m34_to_pay_after;	

		
		//interst days -> before +after
		//$interest_days_before = getInterestDate($parent_end_date, $the_row[le_year], $the_row[le_start_date]);
		//$interest_days_after = getInterestDate($the_row[le_end_date], $the_row[le_year], date('Y-m-d'));
		
		//function getInterestDaysFromLeid($the_leid){
		
		//echo "--> ".$interest_start_date; 
		
		//echo "--> ".$interest_days_after; 
		
		//yoes 20190208
		//clarify:
		//	- $interest_end_date 	can be	-> latest_payment_date or current_date
		//	- $interest_start_date 	can be	-> parent_end_date or 1 april
		if($interest_end_date >= $interest_start_date){
			
			$interest_days_before = max(dateDiffTs(strtotime($interest_start_date), strtotime($interest_end_date), 1),0);
			
			if($child_leid){
				
				
				//have child = no "AFTER"
				//echo "--> have_child"; 
				
				$interest_days_after = 0;
				
				
			}elseif($the_row[le_end_date] < $interest_start_date){			
			
				//echo "--> case 02"; 
				$interest_days_after = max(dateDiffTs(strtotime($interest_start_date), strtotime($interest_end_date), 1),0);
				
			}else{
				
				//echo "--> case else"; 
				
				//yoes 201901011			
				//le_end_date more than 01 april
				//echo "le_end_date ".$the_row[le_end_date]." vs interest_end_date $interest_end_date";
				
				//$interest_start_date = $the_row[le_end_date];
				
				//yoes 20190201
				//small fix here
				//yoes 20190207 -- fix here because
				//echo "---> ".  $the_row[le_end_date];
				
				if(!$le_end_date_still_employed){			
					$interest_start_date = $the_row[le_end_date];
				}
				
				$interest_days_after = max(dateDiffTs(strtotime($the_row[le_end_date]), strtotime($interest_end_date), 0),0);
				
			}
			
			
		}else{
		
			/*echo "--> case BIG else le_end_date -> " . $the_row[le_end_date]; 		
			
			echo "<br>".$the_row[le_end_date] . " - ". $interest_end_date ;
			echo "<br> dateDiffTs ===> " . dateDiffTs(strtotime($the_row[le_end_date]), strtotime($interest_end_date));
			
			$interest_days_after = max(dateDiffTs(strtotime($the_row[le_end_date]), strtotime($interest_end_date), 0),0);
			*/

		}	
		
		
		//yoes 20190208
		//calculate "AFTER" value
		//do this for bottom-most child
		if(!$child_leid){
			
			
			//find real $interest_start_date_before here
			
			$interest_start_date_after = max($interest_start_date, $latest_payment_date);
			
			//??
			if(!$le_end_date_still_employed){			
				$interest_start_date_after = max($interest_start_date_after, $the_row[le_end_date]);
			}
			
			
			//echo "<br>  max($the_row[le_start_date], $the_row[le_end_date], $latest_payment_date)";
			
			
			///find real $interest_days_after here
			//it as to be one of followings:
			//	- $interest_end_date (BEFORE)
			//	- $latest_payment_date
			//	- $the_row[le_end_date]
			//	- current_date
			
			//*** also account for that there are some payment that paid for this CHILD		
			$interest_end_date_after = $current_date;
			
			
			
			$interest_days_after = max(dateDiffTs(strtotime($interest_start_date_after), strtotime($interest_end_date_after), 0),0);
			
			/*
			echo "<br>interest_start_date_after: $interest_start_date_after";
			echo "<br>interest_end_date_after: $interest_end_date_after";
			echo "<br>interest_days_after: $interest_days_after";*/
		}
		
		
		
		//echo "--> ".$interest_start_date; 
		//echo "<br>interest_days_before - " . $interest_days_before;
		//echo "<br>interest_days_after - " . $interest_days_after;
		
		//}
		
		//echo "interest_days_before $interest_days_before bb interest_days_after $interest_days_after" ;
		
		
		//yoes 20190208 --> if no child then also calculate interests from remianing amount
		if(!$chil_leid){
			//$total_paid_amount = get33PaidAmountbyLeidArray($the_leid);
			//echo "<br>total_paid_amount -> $total_paid_amount";
		}
		
		//$total_33_array = get33AmountToPayByLeid($the_leid);
		//print_r($total_33_array);
		//echo "<br>total_33_array -> $total_33_array";
		
		//amount to pay
		$interest_amount_before = $interest_days_before * (7.5/100/365) * $m34_to_pay_before_deducted;
		$interest_amount_after  = $interest_days_after  * (7.5/100/365) * $m34_to_pay_after ;
		
		//echo "interest_amount_after = $interest_days_after  * (7.5/100/365) * $m34_to_pay_after";
		
		//******
		
		//yoes 20190129
		//cleanup interests
		$interest_amount_before = round($interest_amount_before,2);
		$interest_amount_after = round($interest_amount_after,2);
		
		//echo "-> ".$interest_amount_after; 
		
		
		$deduction = array(
						
						
						//parent+child
						"my_leid" => $the_leid
						, "parent_leid" => $parent_leid
						, "child_leid" => $child_leid
						

						//date before +after +middle
						, "this_m33_date_diff_before" => $this_m33_date_diff_before
						, "this_m33_date_diff_before_deductable_days" => $this_m33_date_diff_before_deductable_days 
						, "this_m33_date_diff_before_deducted" => $this_m33_date_diff_before_deducted //for case <= 45 days
						
						
						, "this_m33_date_diff_after" => $this_m33_date_diff_after					
						, "this_m33_date_no_pay" => $this_m33_date_no_pay
											
						
						//payment for before +after +middle
						, "m34_to_pay_before" => $m34_to_pay_before
						, "m34_to_pay_before_deducted" => $m34_to_pay_before_deducted
						, "m34_to_pay_after" => $m34_to_pay_after
						, "m34_deductable" => $m34_deductable
						, "m34_no_need_pay" => $m34_no_need_pay
						
						//interests for before +after (no middle)
						, "interest_start_date" => $interest_start_date
						, "interest_start_date_after" => $interest_start_date_after	//yoes 201902018 -add this
						, "interest_days_before" => $interest_days_before
						
						, "interest_end_date" => $interest_end_date
						, "interest_end_date_after" => $interest_end_date_after	//yoes 201902018 -add this
						, "interest_days_after" => $interest_days_after
						
						, "interest_amount_before" => $interest_amount_before
						, "interest_amount_after" => $interest_amount_after
						
						//yoes 20190125
						, "latest_payment_date" => $latest_payment_date
						, "latest_payment_rid" => $latest_payment_rid
						
		
					);
		
		//print_r($deduction);
		
		return $deduction;
	}


?>