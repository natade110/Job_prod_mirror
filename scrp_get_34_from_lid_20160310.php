<?php 

	/*
	---> input as below
	
	
	$lid_to_get_34 = $lid_array[$i]; //lid
	$employees_ratio = $employees_ratio; //ต้องรับคนกี่คน
	$year_date = $year_date; //days in years
	$the_wage = $the_wage; //ค่าจ้างประขำปี
	$this_lawful_year = $this_lawful_year; //ปีนี้ปีอะไร (ปีของ lid)
	$the_province = $province_array[$i] //province อะไร
	
	---> output as below
	$total_maimad_paid += $maimad_paid; //จ่ายให้กี่คน
	$total_paid_money += $paid_money; //เงินต้นกี่บาท
	$total_interest_money += $interest_money; //ดอกเบี้ยเท่าไหร่
	$total_extra_money += $extra_money; //จ่ายเกินเท่าไหร่
	
	*/
	
	if($this_lawful_year == 2011){
		//echo $the_year ."". $the_province;
		$the_wage = getThisYearWage($this_lawful_year, $the_province);			
		$the_wage = $the_wage/2;
		//echo "<br>". $the_wage . " -- ";
	}
	

	$the_sql = "select *
				, receipt.amount as receipt_amount
				, lawfulness.year as lawfulness_year
				 from payment, receipt , lawfulness
					where 
					receipt.RID = payment.RID
					and
					lawfulness.LID = payment.LID
					
					and
					lawfulness.lid = '".$lid_to_get_34."' 
					
					and
					is_payback != 1
					and 
					main_flag = 1
					order by ReceiptDate, BookReceiptNo, ReceiptNo asc";
	
	//echo $the_sql; exit();
			
	$the_result = mysql_query($the_sql) or die(mysql_error()); //this one is slow...
	
	//resets
	$paid_money = 0;
	$extra_money = 0;
	
	
	$start_money = $employees_ratio*$year_date*$the_wage;
	
	//echo "start_money -- $start_money --";
	
	//echo "<br>employees_ratiooo ".$employees_ratio." oo";
	
	$owned_money = $start_money;
	$paid_from_last_bill = 0;
	$this_lid_interests = 0;
	$last_payment_date = 0;
	
	while($result_row = mysql_fetch_array($the_result)){
		
			$have_some_34 = 1;
		
			$owned_money = $owned_money - $paid_from_last_bill;
			
			$this_paid_amount = $result_row["receipt_amount"];	
			
			//echo "<br>owned_money;;".$owned_money.";;";								
			
			//echo "<br>this_paid_amount**".$this_paid_amount."**";
			
			$this_lawful_year = $result_row[lawfulness_year];
														
			if(!$last_payment_date){
				
				$last_payment_date = "$this_lawful_year-01-31 00:00:00";
			}
									
			if(strtotime(date($last_payment_date)) 
				< 
				strtotime(date("$this_lawful_year-01-31"))){
			
				$last_payment_date = "$this_lawful_year-01-31 00:00:00";
			
			}
			
			//echo $last_payment_date;
			
			$interest_date = getInterestDate($last_payment_date, $this_lawful_year, $result_row["ReceiptDate"]);
			//echo "<br>interest_date,".$interest_date.",";										

			$last_payment_date_to_show = $last_payment_date;
			$last_payment_date = $result_row["ReceiptDate"];
			
			if($this_lawful_year >= 2012){ //only show interests when 2012+
				
				//echo "<br>doGetInterests($interest_date,$owned_money,$year_date)";
				$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
			}else{
				$interest_money = 0;
			}
			
			$this_lid_interests += $interest_money;
			
			
			//echo "<br>interest_money::".$interest_money."::";	
			
			if($total_pending_interest > 0){																
				$interest_money += $total_pending_interest;					
			}
			
			
			if($this_paid_amount < $interest_money){
				$have_pending_interest = 1;
				
			}					
			
			
			$this_paid_money = $this_paid_amount-$interest_money;
			
			//echo "<br> $this_paid_money = $this_paid_amount - $interest_money ;"; 
			
			if($this_paid_money < 0){
				$this_paid_money = 0;
			}
			
			
			$paid_money += $this_paid_money;
			
			$paid_from_last_bill = $this_paid_money;
			
		
			if($this_paid_amount < $interest_money){
				$pending_interest = (($interest_money - $this_paid_amount ));
				
				$total_pending_interest = $pending_interest;
			
			 }else{
			
				$total_pending_interest = 0;
			
			}
			
			
	}//end while for looping to display payment details	
	
	//exit();
	
	//exit();
	
	//echo "($paid_money/($year_date*$the_wage)"; exit();
	
	//yoes 20160201 --> if จ่ายเกิน then move it somewhere else
	//echo "if( $paid_money > $start_money ){"; exit();
	if( $paid_money > $start_money){
		
		$extra_money = $paid_money - $start_money;
		$paid_money = $start_money;
	}
	
	
	//echo " floor( $paid_money /( $year_date * $the_wage )); "; //exit();
	
	$maimad_paid = floor($paid_money/($year_date*$the_wage));
	
	//yoes 20160119 -- override stuffs
	$interest_money = $this_lid_interests;
	
	//out is
	//$maimad_paid --> number of maimad that covered by this payments
	//$paid_money --. amount เงินต้น paid
	//$interest_money --> interest money paid ($paid_money+$interest_money should equal all amount padi)
	
	//echo  formatNumber($incurred_paid_money) . "<br>";
	
	//echo "moomin ?";

?>