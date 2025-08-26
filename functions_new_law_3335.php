<?php

	if($sess_accesslevel == 4){
		$es_table_name_suffix = "_company";
		$es_field_name_suffix = "-es";
	}


	//yoes 20181025
	//turn this functions to return array instead...
	function get3335DeductionByXIDArray($the_id, $as_of_date = "", $show_message = 0, $mode = "m33"){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		//message 1 = normal  - 99 = debug
		//$show_message = 99;
		//$show_message = 1;
		
		
		//yoes 20190222
		//set variables here (for use same function but differ table name/columns between 33vs35)
		if($mode == "m35"){
			
			$assoc_table_name = "curator".$es_table_name_suffix;
			$assoc_pk_name = "curator_id";
			$assoc_pk_shortname = "curator_id"; //leid vs curator_id -> use in output
			
			$assoc_year_column_name = "Year";
			
			$assoc_start_date_column_name = "curator_start_date";
			$assoc_end_date_column_name = "curator_end_date";
			
			$assoc_display_name_column_name = "curator_name";			
			
			$assoc_receipt_meta_name = "35_for";
			
			$the_mxx_word = "m35"; // -- for output
			
			$desc_01 = "ต้องจ่ายเงินแทนก่อนการใช้สิทธิ";
			$desc_02 = "รวมเงินต้นยกมาจากการใช้สิทธิก่อนหน้า";
			$desc_03 = "ต้องจ่ายเงินแทนส่วนที่เหลือหลังเลิกใช้สิทธิ";
			$desc_04 = "ใช้สิทธิแทน";
			$desc_05 = "ใช้สิทธิแทนโดย";
			
			
		}elseif($mode == "m33"){
			
			$assoc_table_name = "lawful_employees".$es_table_name_suffix;
			$assoc_pk_name = "le_id";
			$assoc_pk_shortname = "leid"; //leid vs curator_id -> use in output
			
			$assoc_year_column_name = "le_year";
			
			$assoc_start_date_column_name = "le_start_date";
			$assoc_end_date_column_name = "le_end_date";
			
			$assoc_display_name_column_name = "le_name";			
			
			$assoc_receipt_meta_name = "33_for";
			
			$the_mxx_word = "m33"; // -- for output
			
			$desc_01 = "ต้องจ่ายเงินแทนก่อนการรับเข้าทำงาน";
			$desc_02 = "รวมเงินต้นยกมาจากคนทำงานก่อนหน้า";
			$desc_03 = "ต้องจ่ายเงินแทนส่วนที่เหลือหลังจากออกงาน";
			$desc_04 = "ทำงานแทน";
			$desc_05 = "ทำงานแทนโดย";
			
			
		}else{
			
			echo "mode not specified"; exit();
			
		}
		
		
		if($show_message == 99){		
			echo "<br><b>the_id: $the_id </b>";
		}
		
		
		//---------------------
		//#00 init stuffs
		// 	- all records have to do this
		//---------------------
		
		
			if($mode == "m35"){
				$the_row = getFirstRow("select 
											* 
										from 
											$assoc_table_name 
												join lawfulness$es_table_name_suffix
													on 
													curator_lid = lid
										where 
											$assoc_pk_name = '$the_id'") ;
			}else{
				$the_row = getFirstRow("select * from $assoc_table_name where $assoc_pk_name = '$the_id'") ;
			}
			
			//
			$year_start_date = $the_row[$assoc_year_column_name]."-01-01";	
			$year_end_date = $the_row[$assoc_year_column_name]."-12-31";
			
			$this_lawful_year = $the_row[$assoc_year_column_name];		
			//
			$parent_end_date = $year_start_date;		
			//
			//yoes 2019083
			//print_r($the_row);
			if($the_row[$assoc_year_column_name] >= 2018){
				$year_interest_start_date = $the_row[$assoc_year_column_name]."-04-01";
			}else{
				$year_interest_start_date = $the_row[$assoc_year_column_name]."-02-01";
			}
			
			if($as_of_date){
				
				$current_date = $as_of_date;
				
			}else{
				$current_date = date('Y-m-d');
			}
			
			$this_year_wage =  getThisYearWage($the_row[$assoc_year_column_name]);
			
			
			
			
			//yoes 20190222
			//special for m35
			if($mode == "m35" && trim($the_row[curator_event] == "ฝึกงาน" && $this_lawful_year >= 2018 && $this_lawful_year <= 2500)){
				
				//see if it is 6 months ?
				//echo $the_row[curator_id] . $the_row[curator_event]; exit();
				
				$curator_start_date = $the_row[curator_start_date];		
				//$curator_start_date = "2018-07-31";
				$curator_start_day = substr($curator_start_date, 8, 2) ;
				$curator_start_month = substr($curator_start_date, 5, 2) ;
				$curator_start_year = substr($curator_start_date, 0, 4) ;
				
				
				$curator_6_month_month_year = date('Y-m-01', strtotime("+6 months", strtotime($curator_start_year."-".$curator_start_month."-01")));
				$curator_6_month_month_year_last_day = date('t', strtotime($curator_6_month_month_year));
				$curator_6_month_day = $curator_start_day;
				if($curator_6_month_day > $curator_6_month_month_year_last_day){
					$curator_6_month_day = $curator_6_month_month_year_last_day;
				}
				
				
				$curator_6_month_for_compare = substr($curator_6_month_month_year,0,7)."-$curator_6_month_day";
				$curator_6_month_for_compare = date('Y-m-d', strtotime("-1 day", strtotime($curator_6_month_for_compare)));
				$curator_6_month_for_compare .= " 00:00:00";
				
				$curator_end_date = $the_row[curator_end_date];
				$curator_end_day = substr($curator_end_date, 8, 2) ;
				$curator_end_month = substr($curator_end_date, 5, 2) ;
				$curator_end_year = substr($curator_end_date, 0, 4) ;
				
				
				
				if($the_row[curator_end_date] >= $curator_6_month_for_compare || $the_row[curator_end_date] == "0000-00-00"){
					$is_6_month_training = 1;
				}else{
					$is_6_month_training = 0;
				}
						
			}
			
			
			//"SELF" start date
			if($the_row[$assoc_start_date_column_name] == "0000-00-00" || $the_row[$assoc_start_date_column_name] <= $the_row[$assoc_year_column_name]."-01-01" || $is_6_month_training){		
				$the_row[$assoc_start_date_column_name] = $year_start_date;	
			}
			
			///SELF end date
			if($the_row[$assoc_end_date_column_name] == "0000-00-00" || $is_6_month_training){						
				$the_row[$assoc_end_date_column_name] = $year_end_date;	
			}
			
			if($the_id == 284291){
				//echo $the_row[$assoc_start_date_column_name];
				//echo " vs ".$the_row[$assoc_end_date_column_name];
			}
		
		
		
			//yoes 20190419
			//move this here because we want to know if BEFORE needs to do AFTER -> in scrp_get_interests_new_law3335 around LINE 49 "if(!$child_leid){"
			//
			
			if($mode == "m35"){
				$child_leid = getChildOfCurator($the_id);
			}else{
				$child_leid = getChildOfLeid($the_id);
			}
			
			if($child_leid){
				//have child -> no need calculate "after"
				$this_m33_date_diff_after = 0;
			}else{
				$this_m33_date_diff_after = dateDiffTs(strtotime($the_row[$assoc_end_date_column_name]), strtotime($year_end_date),0);	
			}
		
		
		
		//-------------------
		//#01 PARENT stuffs
		//-------------------
		
			
			//yoes 20190222
			// diff function 33vs35 here
			if($mode == "m35"){
				$parent_leid = getParentOfCurator($the_id);
				
			}else{
				$parent_leid = getParentOfLeid($the_id);				
			}
			
			
			
						
			$select_receipt_start_date = $parent_end_date;
			
			
			
			//-------------------
			//#01.01 ----> manage PARENT here
			//	-- dont need to do anthing with interests for now
			//-------------------
			if($parent_leid){
				
				
				//yoes 20190218
				//get pending amount of parent
				//
				//error_reporting(1);
				//$parent_row = get33DeductionByLeidArray($parent_leid,"",0);				
				$parent_row = get3335DeductionByXIDArray($parent_leid,"", 0, $mode);
				
				if($the_id == 381354){				
					//print_r($parent_row);				
				}
				
				
				$parent_principal = $parent_row[m34_total_principal];
				$parent_interest = $parent_row[m34_total_interest];
				
				
				//yoes 20190421 -- no longer need this m34_to_pay_pending
				//$parent_total_pending = $parent_row[m34_to_pay_pending];
				$parent_total_pending = 0;
				
				
				//yoes 20200527 --
				if($the_id == 365451){
					$parent_total_paid = $parent_row[m34_total_paid];
					//echo "m34_total_paid: ".$parent_total_paid;
				}else{
					$parent_total_paid = $parent_row[m34_total_paid];
				}
				
				
				//echo "<br>parent_total_pending: " . $parent_total_pending;
				
				
				
				
				//$parent_total_amount = $parent_row[total_amount];
				
				if($show_message == 99){		
					
					echo "<br><font color=blue>parent-parent_leid: $parent_leid </font>";
					echo "<br><font color=blue>parent-parent_principal: $parent_principal </font>";
					echo "<br><font color=blue>parent-parent_interest: $parent_interest </font>";
					echo "<br><font color=blue>parent-parent_total_pending: $parent_total_pending </font>";
				}
				
				//echo "<br>parent_total_amount: $parent_total_amount";
			
			
				//
			
				if($mode == "m35"){
					$parent_start_date = get3545DaysParentStartDate($the_id); // yoes 20190305
					$parent_end_date = get3545DaysParentEndDate($the_id);
					
					if($parent_start_date == "0000-00-00"){						
						$parent_start_date = "";
					}
					
				}else{
					$parent_start_date = get3345DaysParentStartDate($the_id);
					$parent_end_date = get3345DaysParentEndDate($the_id);
					
					if($parent_start_date == "0000-00-00"){						
						$parent_start_date = "";
					}
					
				}
				
				
				
				$parent_end_date_year = substr($parent_end_date, 0,4);
				if($parent_end_date_year < $this_lawful_year){
					
					//$parent_end_date = $the_row[$assoc_start_date_column_name];
					$parent_end_date = $year_start_date;
				}
								
				
				if($parent_end_date == $the_row[$assoc_start_date_column_name]){
					$this_m33_date_diff_before = 0;
				}else{		
					$this_m33_date_diff_before = dateDiffTs(strtotime($parent_end_date), strtotime($the_row[$assoc_start_date_column_name]),-1);			
					$select_receipt_start_date = $parent_end_date;					
				}
				
				
				if($this_m33_date_diff_before <= 45){
					$this_m33_date_diff_before_deductable_days = $this_m33_date_diff_before;
					
					//yoes 20190315
					$is_45_days = 1;
					
				}else{
					
					$is_45_days = 0;
					
				}
				
				//echo "<br>have parent";
				
			}else{
				
				//NO PARENT
				$this_m33_date_diff_before = dateDiffTs(strtotime($year_start_date), strtotime($the_row[$assoc_start_date_column_name]),0);	
				$select_receipt_start_date = $year_start_date;
				
				//echo "<br>No parent";
			}
			
			
			
			if($show_message == 99){
				echo "<br>this_m33_date_diff_before: $this_m33_date_diff_before";
			}
			
			
			
		
		//-------------------
		//#02 BEFORE stuffs
		//-------------------
		
			$this_m33_date_diff_before_deducted = $this_m33_date_diff_before-$this_m33_date_diff_before_deductable_days;
			
			$m34_to_pay_before = $this_year_wage * ($this_m33_date_diff_before_deducted); //yoes 20190218 - change this from this_m33_date_diff_before
			$m34_to_pay_before_deducted = $m34_to_pay_before;
			
			//yoes 20190218 -> also add parent principal to the BEFORE calculation
			$m34_to_pay_before_origin = $m34_to_pay_before;
			$m34_to_pay_before = $m34_to_pay_before + $parent_total_pending;
			
			
			//echo "<br>m34_to_pay_before: ".$m34_to_pay_before;
			
			$m34_deductable = $this_year_wage * ($this_m33_date_diff_before_deductable_days);
			
			
			if($show_message == 1){
				
				if($this_m33_date_diff_before_deducted){
				
					echo "<br><font color=orangered>($desc_01 "
						. number_format($this_m33_date_diff_before_deducted,0) ." วัน "
						. number_format($m34_to_pay_before_origin,2) ." บาท ";
					
					echo ")</font>";
				
				}elseif($this_m33_date_diff_before && $this_m33_date_diff_before_deducted == 0){
			
					echo "<br><font color=blue>(รับแทนใน ". $this_m33_date_diff_before_deductable_days ." วัน ไม่ต้องจ่าย "
						. number_format($m34_deductable,2) ." บาท ";			
					echo ")</font>";																	
					
				}
				
				
				if($parent_total_pending){
						
						echo "<br><font color=orangered>(+$desc_02 "
						. number_format($parent_total_pending,2) ." บาท "
					
						. " =  "
						. number_format($m34_to_pay_before,2) ." บาท ";
					
					echo ")</font>";
					
				}
				
				
				
			}
			
			
			//-------------------
			//#02-01 Interests for BEFORE stuffs
			//-------------------
			
			
			//yoes 20190315
			//if no parent then calculate interest for SELF (if any)
			
			if(!$parent_leid){
				
				$m34_to_pay_total_origin = $m34_to_pay_total;
				$select_receipt_start_date_origin = $select_receipt_start_date;
				$parent_end_date_origin = $parent_end_date;
				
				
				$select_receipt_start_date = $the_row[$assoc_year_column_name]."-01-01";
				$parent_end_date = ($the_row[$assoc_year_column_name]-1)."-12-31";
				
			}
				
			//yoes 20190419 --> have to calculate this anyway
			if(1 == 1){
				
				$m34_to_pay_total = $m34_to_pay_before;
				
				//yoes 20190318 --> turn this to include ...
				
				
				if($m34_to_pay_total){
					
					
					$is_before = 1;
					include "scrp_get_interests_new_law3335.php";
					$is_before = 0;
					
				}
				
				
				
				
				//yoes 20190318 --> total left from before is here
				$m34_to_pay_before = $m34_to_pay_pending;
			
			}
			
			
			if(!$parent_leid){
				
				$m34_to_pay_total = $m34_to_pay_total_origin;
				$select_receipt_start_date = $select_receipt_start_date_origin;
				$parent_end_date = $parent_end_date_origin;
				
			}
			
			
			//echo "<br>m34_to_pay_before >> ".$m34_to_pay_before;
			
			
			
			
		//-------------------
		//#03 SELF stuffs
		//-------------------	
			
			//yoes 20190220
			if($the_row[$assoc_end_date_column_name] > $year_end_date){
				
				$this_m33_date_no_pay = dateDiffTs(strtotime($the_row[$assoc_start_date_column_name]), strtotime($year_end_date),1);
				
			}else{
				$this_m33_date_no_pay = dateDiffTs(strtotime($the_row[$assoc_start_date_column_name]), strtotime($the_row[$assoc_end_date_column_name]),1);
			}

			
			$m34_no_need_pay = $this_year_wage * ($this_m33_date_no_pay);
			
			if($show_message == 1){
				echo "<br><font color=green>(แทน ม.34 ได้ "
				. $this_m33_date_no_pay ." วัน "
				. number_format($m34_no_need_pay,2) ." บาท)</font>";
			}
			
			
			
		
		//--------------------------------------
		//#03-01 SELF and interests STUFFS
		//------------------------------------
		
			
			
		//-------------------
		//#04 AFTER stuffs
		//-------------------
		
			
			
			
			
			
			//yoes 20190220
			if($this_m33_date_diff_after < 0){
				$this_m33_date_diff_after = 0;
			}
			
			$m34_to_pay_after = $this_year_wage * ($this_m33_date_diff_after);
			
			
			
			if($show_message == 1){
				
				
			
				if($this_m33_date_diff_after){
					echo "<br><font color=orangered>$desc_03 "
						. number_format($this_m33_date_diff_after,0) ." วัน "
						. number_format($m34_to_pay_after,2) ." บาท ";
				
					
					echo "</font>";	
				}
				
			}
			
		

		//-------------------
		//#05 TOTAL stuffs --> *** NEW
		//-------------------
		
		
			//yoes 20190315
			// no have parent -> only calculate "AFTER" (we calculate "Before" separately)
			
			//echo "$m34_to_pay_total = $m34_to_pay_before + $m34_to_pay_after";
				
			//yoes 20190418 -- calculate interests from "After" only
			//$m34_to_pay_total = $m34_to_pay_before + $m34_to_pay_after;
			$m34_to_pay_total = $m34_to_pay_after;
				
			
			
			//yoes 20190218 ??
			$m34_total_principal = $m34_to_pay_before_origin +$m34_to_pay_after+ $parent_principal;
			
			if($the_id == 284291){
				//echo "$m34_to_pay_before_origin +$m34_to_pay_after+ $parent_principal";
			}
			
			//yoes 20190220 -- total principal also include parent's principal
			//$m34_total_principal = $m34_to_pay_total + $parent_principal;
			
			//yoes 20190218 -> also include parent's interest
			
			//echo "<br>parent_interest - > $parent_interest";
			
			
			$m34_total_interest += $parent_interest;
			
			
			//echo "<br>m34_total_interest - ".$m34_total_interest;
			
			//-------------------
			//#04-01 interests for AFTER stuffs
			//	- BUT only do this if have to pay something at-all
			//-------------------
			
			
			//echo "m34_to_pay_total: ".$m34_to_pay_total;
			
			//yoes 20190318 --> turn this to include ...
			
			//echo "<br>m34_to_pay_pending 11 : " . $m34_to_pay_pending;
			
			//echo "<br>m34_total_interest 1 -> ".$m34_total_interest;
			
			
			if($m34_to_pay_total){
				
				//yoes 20190418 -- add flag to tell that this is "AFTER"
				
				if($the_id == 365451){
					
					//yoes 20200527 for https://app.asana.com/0/794303922168293/1177576993297769
					//do noting
					
				}elseif($the_id == 332209){ //yoes 20200615 - duped total paid here for https://app.asana.com/0/794303922168293/1180306855862685
					
					$m34_total_paid_beforexx = $m34_total_paid;
					//dont for get interest after!
					$is_after = 1;
					include "scrp_get_interests_new_law3335.php";
					$is_after = 0;
					
					$m34_total_paid = $m34_total_paid_beforexx;
					
				}else{
					$is_after = 1;
					include "scrp_get_interests_new_law3335.php";
					$is_after = 0;
				}
				
			}
			
			//echo "<br>m34_to_pay_pending 22 : " . $m34_to_pay_pending;
			
			// ------------
			// ------------
			// ------------ end *** IF***
			// ------------
			// ------------ if($m34_to_pay_total){
			
			
			//echo "<br>m34_total_interest 2 -> ".$m34_total_interest;
			//yoes 20190419 --> move this here
			
			
			//echo "parent_total_paid ?? -> $parent_total_paid";
			
			//yoes 20190421
			if(!$m34_to_pay_total && !$m34_to_pay_before && !$m34_to_pay_after){


				//yoes 20190421 if self have no calculation -> get total paid from parent here too anyway
				//yoes 20200327
				//fix for https://app.asana.com/0/794303922168293/1168577748578937 ?
				//yoes 20200422
				//skip this for 374083
				//as per https://app.asana.com/0/794303922168293/1171735520264034
				//yoes 20200526
				//also fixed for this case https://app.asana.com/0/794303922168293/1177365440639330
				if(
					$the_id == 364602 || $the_id == 374083 || $the_id == 343646
					|| 
					($the_id == 365449 || $the_id == 365450 || $the_id == 365451)
					|| //yoes 20200615 for https://app.asana.com/0/794303922168293/1180306855862685
					( $the_id == 332210 || $the_id == 332209 || $the_id == 332208 )
					
				){
					
					/*
					echo "the_id: $the_id ";
					echo "is_before: $is_before";
					echo "is_after: $is_after";
					echo "my total paid: $m34_total_paid";
					echo "parent_total_paid: $parent_total_paid";
					*/
					
					$m34_total_paid = $m34_total_paid ;//+ $parent_total_paid;
					
					//echo $m34_total_paid;
					
					
					/*if($the_id == 365451){
						echo "my total paid: $m34_total_paid";
						echo "parent_sstotal_paid: $parent_total_paid";
						//$m34_total_paid = $m34_total_paid+$parent_total_paid;
						$m34_total_paid = 0;
					}*/
					//echo "wws";
					
					//echo "b-- " . $m34_total_paid;
					
					
				}else{
					
					
					$m34_total_paid = $m34_total_paid + $parent_total_paid;
					
					if($the_id == 365451){
						//echo "b-- " . $m34_total_paid;
					}
				}
			}
			
			if($the_id == 365451){
				//echo "c-- " . $m34_total_paid;
			}
		
			
			//$m34_to_pay_pending = ($m34_total_principal+$m34_total_interest) - $m34_total_paid - $parent_total_paid ; 
			$m34_to_pay_pending = ($m34_total_principal+$m34_total_interest) - $m34_total_paid; 
			
			$m34_to_pay_pending = round($m34_to_pay_pending,2);
			
			//echo "<br>m34_to_pay_pending from law3335 1 -> ".$m34_to_pay_pending;
						
			
			//yoes 20190220 show total amount on child's row
			if($show_message == 1 && !$child_leid){
				
				
				
				
				if($m34_total_principal+$m34_total_interest != 0){
	
	
					if($m34_total_interest){
						
						echo "<br><font color=red>รวมต้องจ่ายเงินต้น + ดอกเบี้ย = 
						
						".number_format($m34_total_principal,2) . "+" . number_format($m34_total_interest,2). "
					
						=".number_format($m34_total_principal+$m34_total_interest,2). " บาท</font>";
						
					}else{
						
						echo "<br><font color=red>รวมต้องจ่ายเงินต้น = ".number_format($m34_total_principal+$m34_total_interest,2). " บาท</font>";
						
					}
					
					
				}				
				
				
				
				
				if($m34_total_paid){
					
					echo "<br><font color=green>มีการจ่ายเงินแล้ว " . number_format($m34_total_paid,2) . " บาท</font>";
					
				}
				
				
				if($m34_to_pay_pending < 0){
					
					//yoes 20190418 -- also need to add total interests here
					echo "<br><font color=purple><b>จ่ายเงินเกิน " . number_format($m34_to_pay_pending,2) . " บาท</b></font>";
					
				}elseif($m34_to_pay_pending > 0){
					
					echo "<br><font color=orangered><b>ขาดจ่าย " . number_format($m34_to_pay_pending,2) . " บาท</b></font>";	
					
				}
				
				
				if($m34_to_pay_pending == 0){
					
					echo "<br><font color=green><b>ปฏิบัติทดแทนครบแล้ว</b></font>";					
					
				}
				
				
			}
		
		
			
			
		
		//-------------------
		//#99 - output
		//-------------------
		
			
		
			if($show_message == 1){
			
				if($parent_leid){
				
					$parent_message_row = getFirstRow("select * from $assoc_table_name where $assoc_pk_name = '".$parent_leid."'");
										
					//echo "<br>parent_leid: $parent_leid";
					
					echo "<br><font color='#ff00ff'>($desc_04 ".$parent_message_row[$assoc_display_name_column_name]." )</font>";
					
				}
				
				
				if($child_leid){
				
					$child_message_row = getFirstRow("select * from $assoc_table_name where $assoc_pk_name = '".$child_leid."'");
					
					//echo "<br>child_leid: $child_leid";
					
					echo "<br><font color='blue'>($desc_05 ".$child_message_row[$assoc_display_name_column_name]." )</font>";
					
				}
						
		
			}
			
			/*
			echo "<br>m34_total_principal " . $m34_total_principal	;
			echo "<br>m34_total_interest " . $m34_total_interest	;
			echo "<br>m34_total_paid " . $m34_total_paid;
			echo "<br>m34_to_pay_pending " . $m34_to_pay_pending ;*/
			
				
			
			
			//
			//$m33_total_reduction_array[m34_to_pay_before_deducted] +$m33_total_reduction_array[m34_to_pay_after]
			
			//echo "<br>m34_to_pay_before_deducted: ".$m34_to_pay_before_deducted . " --m34_to_pay_after: " . $m34_to_pay_after;
			
			
			//echo "<br>m34_to_pay_pending from law3335 2 -> ".$m34_to_pay_pending;
			
			$deduction = array(
						
						
				//parent+child
				"my_" . $assoc_pk_shortname => $the_id
				, "parent_" . $assoc_pk_shortname => $parent_leid
				, "child_" . $assoc_pk_shortname => $child_leid
				
				//date before +after +middle
				, "this_".$the_mxx_word."_date_diff_before" => $this_m33_date_diff_before
				, "this_".$the_mxx_word."_date_diff_before_deductable_days" => $this_m33_date_diff_before_deductable_days 
				, "this_".$the_mxx_word."_date_diff_before_deducted" => $this_m33_date_diff_before_deducted //for case <= 45 days
				
				
				, "this_".$the_mxx_word."_date_diff_after" => $this_m33_date_diff_after					
				, "this_".$the_mxx_word."_date_no_pay" => $this_m33_date_no_pay
									
				
				//payment for before +after +middle
				, "m34_to_pay_before" => $m34_to_pay_before
				, "m34_to_pay_before_deducted" => $m34_to_pay_before_deducted
				, "m34_to_pay_after" => $m34_to_pay_after				
				, "m34_deductable" => $m34_deductable
				, "m34_no_need_pay" => $m34_no_need_pay
				
				
				//yoes 20190218 -> all items
				
				
				, "m34_total_principal" => $m34_total_principal				
				, "m34_total_interest" => $m34_total_interest				
				, "m34_total_paid" => $m34_total_paid
				, "m34_to_pay_pending" => $m34_to_pay_pending
				
				
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
				
				//yoes 20200620 -- vars for help debugging
				, "m34_to_pay_before_origin" => $m34_to_pay_before_origin
				, "interest_amount" => $interest_amount
				

			);
			
			//$deduction = get33DeductionByLeidStartStopArray($the_id, $as_of_date);
			
			return $deduction;
		
	}
	


?>