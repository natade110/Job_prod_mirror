<?php 

	//echo "asdasdasdasdasd";
	//exit();

	/*
	---> input as below
	
	
	
	$lid_to_get_34 = $lid_array[$i]; //lid
	$employees_ratio = $employees_ratio; //ต้องรับคนกี่คน
	$year_date = $year_date; //days in years
	$the_wage = $the_wage; //ค่าจ้างประขำปี
	$this_lawful_year = $this_lawful_year; //ปีนี้ปีอะไร (ปีของ lid)
	$the_province = $province_array[$i] //province อะไร
	$need_for_lawful -> อัตราส่วนที่ต้องรับ (ก่อนหัก 33 35) คือเท่าไหร่
	
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
		
		
		
		//yoes 20170123
		//also check if do interest for year 2554
		$do_54_budget = getFirstItem("
								
			select
				meta_value
			from
				lawfulness_meta
			where
				meta_for = 'do_54_budget'
				and
				meta_lid = '". $lid_to_get_34."'
		
		
		");
		
		
		$the_54_budget_date = getFirstItem("

				select
					meta_value
				from
					lawfulness_meta
				where
					meta_for = 'do_54_budget_start_date'
					and
					meta_lid = '". $lid_to_get_34."'
			
			
			");
		
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

    if($lid_to_get_34 == 2050540885){
        //echo $the_sql; exit();
    }
			
	$the_result = mysql_query($the_sql) or die(mysql_error()); //this one is slow...
	
	//resets
	$paid_money = 0;
	$extra_money = 0;
	
	
	$start_money = $employees_ratio*$year_date*$the_wage;
	
	//echo "$start_money = $employees_ratio*$year_date*$the_wage;"; exit();
	
	//echo $start_money; exit();
	
	
	$deducted_33 = 0;
	if($this_lawful_year >= 2018 && $this_lawful_year < 2050 && !$force_old_law){
		
		//$lid_employees = getFirstItem("select employees from lawfulness where lid = '$lid_to_get_34'");
		//$lid_ratio = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_".$this_lawful_year."'"),100);
		
		//yoes 20190123
		//no need this
		//$employees_ratio = getEmployeeRatio($lid_employees,$lid_ratio);
		$start_money = $employees_ratio*$year_date*$the_wage;
		
		//echo $start_money; exit();
		
		
				
		$lidcid = getFirstItem("select cid from lawfulness where lid = '$lid_to_get_34'");
		
		//$deducted_33 = get33DeductionByCIDYear($lidcid, $this_lawful_year);
		$deducted_33_array = get33DeductionByCIDYearArray($lidcid, $this_lawful_year);
		$deducted_33 = $deducted_33_array[m33_total_reduction];
				
		$deducted_35_array = get35DeductionByCIDYearArray($lidcid, $this_lawful_year);
		$deducted_35 = $deducted_35_array[m35_total_reduction];
		
		
		
		
		
		//echo $deducted_33; exit();
		
		//print_r($deducted_33); exit(); //300
		
		
		$deducted_3335 = $deducted_33 + $deducted_35;
		
		//echo " $start_money - $deducted_3335 "; exit();
					
		if($deducted_3335 > $start_money){
			$deducted_3335 = $start_money;
		}
		
		//echo "$start_money = $start_money - $deducted_3335"; exit();
		
		
		//yoes 20190123
		//no need this ... ?
		//$start_money = $start_money - $deducted_3335;
		
		
		//echo "dd33 $lidcid, $the_year ".$deducted_33 . " sss $start_money"; exit();
		//echo $start_money;	
		
	}

    if($lid_to_get_34 == 205533){
        //echo $the_sql; exit();
        //print_r($deducted_33_array); exit();
    }

	
	

	
	//echo "<br>employees_ratiooo ".$employees_ratio." oo";
	
	$owned_money = $start_money;
	$paid_from_last_bill = 0;
	$this_lid_interests = 0;
	$last_payment_date = 0;
	
	//yoes 20210209 -- delete old principals records
	///
	
	//yoes 20220211 -> use temp or real table?
	if(!$lawful_34_principals_table_name){
		$lawful_34_principals_table_name = "lawful_34_principals";
	}
	//yoes 20220218 -> what date to use for calculate interests?
	if(!$current_date){
		$current_date = date("Y-m-d");
	}
	
	$current_date = $current_date;
	
	$p_delete_sql = "
		delete from 
			$lawful_34_principals_table_name							
		where
			p_lid = '".($lid_to_get_34*1)."'	
	";
	
	mysql_query($p_delete_sql);
	
	$get34_last_rid = 0;
	
	while($result_row = mysql_fetch_array($the_result)){
		
			$have_some_34 = 1;
		
			$owned_money = $owned_money - $paid_from_last_bill;
			
			$this_paid_amount = $result_row["receipt_amount"];	
			
						
			
			//echo "<br>this_paid_amount**".$this_paid_amount."**";
			
			$this_lawful_year = $result_row[lawfulness_year];
														
			if(!$last_payment_date){
				
				
				//yoes 20170123
				if($the_54_budget_date){
				
					$last_payment_date = "$the_54_budget_date 00:00:00";
				
				}else{
					
					$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);	
				}
			}
									
			if(strtotime(date($last_payment_date)) 
				< 
				strtotime(date(getDefaultLastPaymentDateByYear($this_lawful_year)))){
			
				$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);
			
			}
			
			//echo $last_payment_date;
			
			$interest_date = getInterestDate($last_payment_date, $this_lawful_year, $result_row["ReceiptDate"]);
			//echo "<br>interest_date,".$interest_date.",";										

			$last_payment_date_to_show = $last_payment_date;
			
			
			if($this_lawful_year >= 2012){ //only show interests when 2012+
				
				//echo "<br>doGetInterests($interest_date,$owned_money,$year_date)";
				$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
				
				
				$table_34_interests = $interest_money;
				
				//echo "<br>ดอกเบี้ยที่เกี่ยวข้องกับใบนี้: .. " . $interest_money;
				
				
			}else{
				$interest_money = 0;
			}
			
			$this_lid_interests += $interest_money;
			
			
			//echo "<br>interest_money::".$interest_money."::";	exit();
			
			if($total_pending_interest > 0){																
				$interest_money += $total_pending_interest;					
			}
			
			//yoes 20221215
			//ซาฟารี 64
			if($last_pending_interest && $lid_to_get_34 == 2050553291){
				$interest_money += $last_pending_interest;
			}
			
			if($this_paid_amount < $interest_money){
				$have_pending_interest = 1;
				
			}					
			
			
			
			
			//echo "<br> $this_paid_money = $this_paid_amount - $interest_money ;"; 
			
			if($this_paid_money < 0){
				$this_paid_money = 0;
			}
			
			//echo $_SESSION['sess_accesslevel']; exit();
			
			//yoe test 20210209
			if(	
				(
					$lid_to_get_34 == 164036 
					|| $lid_to_get_34 == 2050531917
					|| $lid_to_get_34 == 2050532523
					|| $lid_to_get_34 == 2050542121
					|| $lid_to_get_34 == 191330
					|| $lid_to_get_34 == 1000185931
					|| 1==1 // yoes 20211103
				)
				
				//yoes 20220116
				//yoes 20220225 -> จ้างงาน ขอให้ใช้สูตรเดิมในปีที่ผ่านมาแล้ว
				&& 
				
				/*($_SESSION['sess_accesslevel'] == 1 || $_SESSION['sess_accesslevel'] == 2 
					|| ($_SESSION['sess_accesslevel'] == 3 && $this_lawful_year >= 2022 && $this_lawful_year < 2100)) //yoes 20211115 -- only admin will*/
				$this_lawful_year >= 2022 && $this_lawful_year < 2100
				
				||
				(
				
					//yoes 20220322 -> special case for ปี 64 บาง บ ที่จะใช้สูตรใหม่
					$lid_to_get_34 == 2050554622
					||
					$lid_to_get_34 == 2050569011
					||
					$lid_to_get_34 == 2050563339
					||
					$lid_to_get_34 == 2050553291 //บริษัท ซาฟารีเวิลด์ จำกัด (มหาชน)  64
					||
					$lid_to_get_34 == 192501
					||
					$lid_to_get_34 == 166900 //บริษัท ภูเก็ต เจมส์ พาวิลเลี่ยน จำกัด  61
					
					||
					$lid_to_get_34 == 2050549546 //ทรอพพิคอล ไอแลนด์ 63 
					
					
					||
					$lid_to_get_34 == 2050540885 //บริษัท  	บริษัท เวย หง จำกัด  63

                    ||
                    $lid_to_get_34 == 205533 //บริษัท  	พี พี กรุ๊ป สาคร(2008)

                    ||
                    $lid_to_get_34 == 2050540885 //บริษัท  	เวย หง
					
				)
				
				){
					
					
					
				//$this_paid_money -= 34572.70 ;
				//echo "**ลองลดเงินที่จ่าย**";
				
				//see if there are เอาเงินใบนี้ไปจ่าย 33/35 ซักที่แล้วหรือไม่				
				//don't forget to do this for 35 also
				
				$meta_used_sql = "
					
					select
						sum(meta_value) as the_sum
					from
						receipt_meta
					where
						meta_rid = '".$result_row[RID]."'
						and
						(
							-- meta_for like '33_for-%-amount'
							-- or
							meta_for like '". $lid_to_get_34 ."%'
							or
							meta_for like 'c". $lid_to_get_34 ."%'
						)
				
				";
				
				//if($lid_to_get_34 == 2050548321){echo $meta_used_sql; exit();}
				
				if($result_row[RID] == 50331){
					//echo "<br>meta_used_sql: ".$meta_used_sql; exit();
				}
				
				$receipt_amount_used = getFirstItem($meta_used_sql);
				//echo $receipt_amount_used;
				//$this_paid_money -= $receipt_amount_used;
				$this_paid_amount -= $receipt_amount_used;
				
			}
			
			
			if($this_paid_amount < $interest_money){
				$have_pending_interest = 1;
				
			}					
			
			
			$this_paid_money = $this_paid_amount-$interest_money;
			
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
			
		
		$last_receipt_date = $result_row["ReceiptDate"];
		
		//yoes 20190123
		$total_paid_amount += $this_paid_amount;
		
		
		
		/*echo "<br>". $result_row[RID] . "-".$result_row[ReceiptNo];
		echo "<br>จ่ายเป็นต้น .. :" . $this_paid_money;
		echo "<br>จ่ายเป็นดอก .. :" . $interest_money;*/
		
		
		//insert records
		$p_sql = " replace into $lawful_34_principals_table_name(
					
					p_lid
					, p_from
					, p_to
					, p_date_from
					, p_date_to
					
					, p_start_date								
					, p_amount
					, p_interests
					, p_paid
					
					, p_pending_amount
					, p_pending_interests
					, p_remarks
		
				)values(
				
					'".($lid_to_get_34*1)."'
					, '".$get34_last_rid."'
					, '".($result_row[RID]*1)."'					
					, '".$last_payment_date."'
					, '".$result_row["ReceiptDate"]."'
										
					, '".(0)."'
					, '".$owned_money."'					
					, '".((($table_34_interests*1)))."'
					, '".$this_paid_money."'
					
					, '".($owned_money-($this_paid_money))."'
					, '".$pending_interest."'
					, 'ดอกเบี้ย $interest_date วัน'
				
				)
		
		";
		
		//echo "<br>".$p_sql; exit();
		
		mysql_query($p_sql);
		
		
		//ending loop
		$last_payment_date = $result_row["ReceiptDate"];
		$get34_last_rid = $result_row[RID];
		
		///yoes 20221215
		//pending interests กรณีไม่จ่ายดอก
		$last_pending_interest = $pending_interest;
		
			
	}//end while for looping to display payment details	
	
	//echo $owned_money; exit();
	
	//yoes 20211109 -- more minor bugfix
	if($last_receipt_date){
		
		$owned_money = $owned_money - $paid_from_last_bill;
		
		if($last_receipt_date < getThisYearInterestDate($this_lawful_year)){
			
			$final_row_interest_date = getThisYearInterestDate($this_lawful_year);
			$final_row_date_from = $last_receipt_date;
			
			//echo $final_row_date_from ; exit();
			
		}else{
			
			$final_row_interest_date = $last_receipt_date;
			$final_row_date_from = $last_receipt_date;
			
		}
		
		//echo "..". getInterestDate($final_row_interest_date, $this_lawful_year, date("Y-m-d")); exit();
		
	}else{
		
		$final_row_interest_date = getThisYearInterestDate($this_lawful_year);
		$final_row_date_from = "$this_lawful_year-01-01";
	}
	
	//yoes 20211103 --> see if have lawful_34_principals row?
	$p34_row = getFirstItem("select count(*) from $lawful_34_principals_table_name where p_lid = '$lid_to_get_34'");
	
	//yoes 20211109 -- have to this anyway
	if(!$p34_row || $owned_money){
		
		
		//$table_34_interest_date = getInterestDate($final_row_interest_date, $this_lawful_year, date("Y-m-d"));
		$table_34_interest_date = getInterestDate($final_row_interest_date, $this_lawful_year, $current_date);
		
		//insert something here
		//yoes 20221215
		//ซาฟารี 64
		
		$final_row_34_interests = doGetInterests($table_34_interest_date,$owned_money,$year_date);
		
		if($last_pending_interest && $lid_to_get_34 == 2050553291){
			$final_row_34_interests += $last_pending_interest;
		}
		
		//yoes 20220214 --> add $total_pending_interest to the final rows..
		$p_sql = " replace into $lawful_34_principals_table_name(
					
					p_lid
					, p_from
					, p_to
					, p_date_from
					, p_date_to
					
					, p_start_date								
					, p_amount
					, p_interests
					, p_paid
					
					, p_pending_amount
					, p_pending_interests
					, p_remarks
		
				)values(
				
					'".($lid_to_get_34*1)."'
					, '0'
					, '0'					
					, '$final_row_date_from'
					, now()
										
					, '".$final_row_interest_date."'
					, '".$owned_money."'					
					, '".doGetInterests($table_34_interest_date,$owned_money,$year_date)."'
					, '0'
					
					, '".$owned_money."'
					, '".$final_row_34_interests."'
					, 'ดอกเบี้ย $table_34_interest_date วัน'
					
				)
		
		";
		
		//echo "<br>".$p_sql; exit();
		
		mysql_query($p_sql);
		
	}
	
	
	//yoes 20210902
	//จ่ายแล้วทั้งหมด...
	/*echo "<br>จ่ายแล้วทั้งหมด...: ".$total_paid_amount;*/
	
	//exit();
	
	
	if($lidcid == 8724){
		
		//yoes test 20190123
		//$this_lid_interests = 0;
		//echo $lidcid; exit();
		//echo $interest_date . " vs " . $last_payment_date . " --- ". $this_lawful_year . " -- ". $result_row["ReceiptDate"] ; exit();
		//$interest_money = 0;
	}
	
	
	if($this_lawful_year >= 2018 && $this_lawful_year < 2050 && !$force_old_law){
		
		
		//yoes 20190107 - start money include interests		
		//echo "$start_money += $deducted_33_array[m33_total_interests] + $deducted_35_array[m35_total_interests]"; exit();	
		//echo $last_receipt_date; exit();		
		
		//yoes 20190129
		//changed function so it already calcualte receipt date by itself
		//$deducted_33_array = get33DeductionByCIDYearArray($lidcid, $this_lawful_year, $last_receipt_date);
		//$deducted_35_array = get35DeductionByCIDYearArray($lidcid, $this_lawful_year, $last_receipt_date);
		$deducted_33_array = get33DeductionByCIDYearArray($lidcid, $this_lawful_year);
		$deducted_35_array = get35DeductionByCIDYearArray($lidcid, $this_lawful_year);
		
		
		//echo $this_lid_interests ; exit();
		
		//echo "$this_lid_interests += $deducted_33_array[m33_total_interests]"; exit();		
		$this_lid_interests += $deducted_33_array[m33_total_interests];
		$this_lid_interests += $deducted_35_array[m35_total_interests];
		
		//echo $this_lid_interests ; exit();
		
		
		
	}
	//exit();
	
	//exit();
	
	//echo "($paid_money/($year_date*$the_wage)"; exit();
	
	//yoes 20160201 --> if จ่ายเกิน then move it somewhere else
	//echo "if( $paid_money > $start_money ){"; exit();

	if($lid_to_get_34 == 2050540885){
		//echo $paid_money;
	}

	if( $paid_money > $start_money){
		
		$extra_money = $paid_money - $start_money;
		$paid_money = $start_money;
	}
	
	if($lid_to_get_34 == 2050557665){
		//echo $paid_money; exit();
	}
	
	//echo "$paid_money - $start_money"; exit();
	
	
	//echo " floor( $paid_money /( $year_date * $the_wage )); "; exit();
	
	//echo " huh: " . $paid_money/($year_date*159); //exit();
	//$moomin = $paid_money/($year_date*159);
	//cast correct type?
	//$moomin = number_format((float) $moomin,2);
	
	//echo " --- raw moomin data : " . $moomin . " --- ";
	
	/*
	if($moomin == 1){
		$moomin = 1;	
		echo " -- moomin is 01 -- ";
	}*/
	
	
	//echo " moomin: " . $moomin;
	//echo " huh2: " . floor($moomin);
	
	
	
	$paid_maimad_float = $paid_money/($year_date*$the_wage);
	$paid_maimad_float = number_format((float) $paid_maimad_float,10);




	//echo $paid_maimad_float; exit();
	
	
	$maimad_paid = floor($paid_maimad_float);

    if($lid_to_get_34 == 2050540885){
       //echo $maimad_paid;
    }
	
	
	
	//$maimad_paid = floor(1);
	
	//echo $maimad_paid; exit();
	
	//yoes 20160119 -- override stuffs
	$interest_money = $this_lid_interests;

    if($lid_to_get_34 == 2050540885){
        //echo "maimad paid: " . $maimad_paid;
        //echo "<br>paid_money: " . $paid_money;
        //echo "<br>interest_money: " . $interest_money;
    }
	//echo $interest_money; exit();


	
	//out is
	//$maimad_paid --> number of maimad that covered by this payments
	//$paid_money --. amount เงินต้น paid
	//$interest_money --> interest money paid ($paid_money+$interest_money should equal all amount padi)
	
	//echo "maimad_paid = $maimad_paid - paid_money = $paid_money - interest_money = $interest_money"; exit();
	
	//echo  formatNumber($incurred_paid_money) . "<br>";
	
	//echo "moomin ?"; exit();

?>