<?php 
while($result_row = mysql_fetch_array($the_result)){
				
	$the_row++;
	
	//echo "<br>row -> ".$the_row;
	
	//yoes 20160126 ---> ! if this is 2011 then wage is "by province"
	if($the_year == 2011){
		$the_wage = getThisYearWage($the_year, $result_row[province]);	
		//echo "<br>the wage: ".$the_wage . " : ";
		$the_wage = $the_wage/2;
		$the_cost_per_person = $the_wage*365;
	}
	
	//echo $the_wage;
	
	//start of loop
	//this is a new "LID
	if($result_row[lawfulness_lid] != $last_lid ){
		
		//starting new loop
		
		
	
		//yoes 20160119 --- wont need this
		/*
		if($maimad_paid > $last_num_needed){
			$maimad_paid = $last_num_needed;
		}
		*/
		
		if($_POST["Province"] == 36 && 1==0){
			echo "<br>- ".$result_row[lawfulness_lid] . " - $result_row[CID] - $result_row[Year]";
		}
		
		//yoes 20160201 --> handle the "จ่ายเกิน" case
		if($paid_money > $start_money){
			
			
			if($_POST["Province"] == 36 && 1==0){
				echo "start_money -- ". $start_money . "<br>";
				echo "paid_money -- ". $paid_money . "<br>";			
				echo "<br>$result_row[lawfulness_lid] - $result_row[CID] - $result_row[Year] - $paid_money > $start_money " . ($paid_money > $start_money);
				echo "<br>" . ($paid_money - $start_money);
			}
						
			$extra_money = $paid_money - $start_money;		
			
			$paid_money = $start_money;
			$incurred_extra_money += $extra_money;
			
			
			//
			//echo "incurred_extra_money -- ". $incurred_extra_money . "<br>";
		}
		
		
		
		//yoes 20160510 -- fix float problem
		//$maimad_paid = floor($paid_money/($year_date*$the_wage)); < -- this is an old code
		$paid_maimad_float = $paid_money/($year_date*$the_wage);
		$paid_maimad_float = number_format((float) $paid_maimad_float,10);
		$maimad_paid = floor($paid_maimad_float);
		
		
		
		$incurred_paid_money += $paid_money;
		
		$all_34 += $maimad_paid;
		
		//show stat before going to new cid
		/*
		echo "<br>LAST CID: ".$last_cid."<br>LAST start_money: ".$start_money;
		echo "<br>LAST paid_money: ".$paid_money;
		echo "<br>LAST interest_money: ".$interest_money;
		echo "<br>LAST maimad_paid: <font color='red'>".$maimad_paid."</font>";
		echo "<br>-----";
		*/
		
		//echo $_POST["Province"];
		
		
		
		
		//how much this guy have to pay?
		$start_money = $result_row[num_needed]*$year_date*$the_wage;
		
		if($_POST["Province"] == 36 && 1==0){
			echo "<br>$result_row[num_needed] * $year_date * $the_wage;";
			echo " -> ".$start_money;
		}
		
		//echo $result_row[num_needed]; 
		
		$deducted_33 = 0;
		if($the_year >= 2018 && $the_year < 2050){
			
			$start_money = $result_row[company_ratio]*$year_date*$the_wage;
			
			//yoes 20240907 - use new function get33DeductionByCIDYearArray instead?
			$deducted_33 = get33DeductionByCIDYear($result_row[cid], $the_year);			
			//$arrr = array();
			//$arrr = get33DeductionByCIDYearArray($result_row[cid], $the_year);
			//$deducted_33 = $arrr["m33_total_reduction"];
			
						
			if($deducted_33 > $start_money){
				$deducted_33 = $start_money;
			}
			
			//echo "<br>".$result_row[lawfulness_lid] . " - " . $deducted_33;
			
			$start_money -= $deducted_33;
			
		}
		
		if($_POST["Province"] == 36 && 1==0){
			echo "<br>deducted_33: $deducted_33";
			echo " -> ".$start_money;
		}
		
		//yoes 20180209
		
		//echo $start_money . "<br>";
		
		$owned_money = $start_money;
		$paid_money = 0;
		$paid_from_last_bill = 0;
		$last_payment_date = 0;
		$total_pending_interest = 0;
		
		
	}
	
	
	
	$owned_money = $owned_money - $paid_from_last_bill;
	//echo "<br>owned_money TOP: ".$owned_money;
	//echo "<br>Current CID: ".$result_row[company_cid];
	
	$this_paid_amount = $result_row["receipt_amount"];									
	
	//echo "<br>".$this_paid_amount;
												
	if(!$last_payment_date){
		$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);
	}
							
	if(strtotime(date($last_payment_date)) 
		< 
		strtotime(date(getDefaultLastPaymentDateByYear($this_lawful_year)))){
	
		$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);
	
	}
	
	
	$interest_date = getInterestDate($last_payment_date, $this_lawful_year, $result_row["ReceiptDate"]);
				
	//echo "<br>interest_date: $interest_date";								

	$last_payment_date_to_show = $last_payment_date;
	$last_payment_date = $result_row["ReceiptDate"];
	
	//echo "<br>owned_money: $owned_money";	 
	
	if($this_lawful_year >= 2012){ //only show interests when 2012+
		
		$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
	}else{
		$interest_money = 0;
	}
	
	/*if($interest_money == 6885){
		echo $result_row[company_cid]."<br>";	
	}*/
		
	
	//echo "<br>interest_money: $interest_money";	
	
	if($total_pending_interest > 0){																
		//yoes 20160201 --> not need this for result set?
		$interest_money += $total_pending_interest;	
		/*echo "-_- $total_pending_interest <br>";	
		echo "$result_row[company_cid] <br>";			*/
	}
	
	
	if($this_paid_amount < $interest_money){
		$have_pending_interest = 1;
		
		//yoes 20160201
		//paid less than interest
		//only count interest that is paid
		/*echo "this_paid_amount: $this_paid_amount <br>";	
		echo "interest_money: $interest_money <br>";	
		echo "$result_row[company_cid] <br>";*/
		//$interest_money = $this_paid_amount;
		
		
	}else{
		//
	}
	
	$is_pay_detail_first_row++;
	
	$this_paid_money = $this_paid_amount-$interest_money;
	
	if($this_paid_money < 0){
		$this_paid_money = 0;
	}
	
	
	$paid_money += $this_paid_money;
	
	
	
	$paid_from_last_bill = $this_paid_money;
	

	if($this_paid_amount < $interest_money){
		$pending_interest = (($interest_money - $this_paid_amount ));
		//echo "pending_interest: $pending_interest <br>";	
		$total_pending_interest = $pending_interest;
	
		//yoes 20160201
		//paid less than interest
		//only count interest that is paid
		$interest_money = $this_paid_amount;
	 }else{
	
		$total_pending_interest = 0;
	
	}
	

	
	$last_lid = $result_row[lawfulness_lid];
	$last_cid = $result_row[company_cid];
	$last_num_needed = $result_row[num_needed];
	//end of loope
	
	
	$incurred_interest_money += $interest_money;
	
}//end while for looping to display payment details	
				


//finalize loop
if($the_row){
	
	
	
	
	//yoes 20160201 --> handle the "จ่ายเกิน" case
	if($paid_money > $start_money){
		$extra_money = $paid_money - $start_money;
		
		$paid_money = $start_money;
		$incurred_extra_money += $extra_money;
	}
	//how many "paid money" are used to pay for maimaid?
	
	$incurred_paid_money += $paid_money;
	
	
	//yoes 20160510 -- fix float problem
	//$maimad_paid = floor($paid_money/($year_date*$the_wage)); < -- this is an old code
	$paid_maimad_float = $paid_money/($year_date*$the_wage);
	$paid_maimad_float = number_format((float) $paid_maimad_float,10);
	$maimad_paid = floor($paid_maimad_float);
	
}else{
	$maimad_paid = 0;	
}

//yoes 20160119 --- wont need this
//if($maimad_paid > $last_num_needed){
//	$maimad_paid = $last_num_needed;
//}



$all_34 += $maimad_paid;
		
/*	
echo "<br>FINAL ----".$result_row[cid]."<br>FINAL start_money: ".$start_money;
echo "<br>FINAL paid_money: ".$paid_money;
echo "<br>FINAL interest_money: ".$interest_money;
echo "<br>FINAL maimad_paid: ".$maimad_paid;*/




//echo "<br>---> ". $all_maimad_paid;
//echo $the_row;
//echo "<br>---> ".$all_34;


?>