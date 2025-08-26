<?php

	if($sess_accesslevel == 4){
		$es_table_name_suffix = "_company";
		$es_field_name_suffix = "-es";
	}
	
	
	
	function get35DeductionByCuratorIdArray($the_leid, $as_of_date = "", $show_message = 0){
		
		$output = get3335DeductionByXIDArray($the_leid, $as_of_date, $show_message, "m35");
		return $output;
		
	}
	
	//yoes 20190222 -> no longer use below function
	function get35DeductionByCuratorIdArrayOld20190222($the_curator_id, $as_of_date = "0"){ // Old20190222
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		$parent_curator_id = getParentOfCurator($the_curator_id);
		$child_curator_id = getChildOfCurator($the_curator_id);
		
		//get target curator	
		$the_row = getFirstRow("select 
									* 
								from 
									curator$es_table_name_suffix a
										join 
											lawfulness$es_table_name_suffix b
												on
												a.curator_lid = b.lid
								where 
									curator_id = '$the_curator_id'") ;
									
									
		
		//
		$year_start_date = $the_row[Year]."-01-01";	
		$year_end_date = $the_row[Year]."-12-31";
		
		//
		$parent_end_date = $year_start_date;
		
		$interest_start_date = $the_row[Year]."-04-01";
		
		
		if($as_of_date && $as_of_date != "0000-00-00 00:00:00"){
			$current_date = $as_of_date;
		}else{
			
			//yoes 20190107
			//check lawful status to get latest payment date
			$lawful_row = getFirstRow("
			
						select  
							a.lid
							, LawfulStatus 
							, Year
						from 
							lawfulness$es_table_name_suffix a
						where
							lid = '".$the_row[curator_lid]."'
								
						");

			$lid_to_get_34 = $lawful_row[lid];			
			$lawful_status = $lawful_row[LawfulStatus];
			$lawful_year = $lawful_row[Year];
			
			
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
								meta_for = '35_for'
								and
								meta_value in ( 
									'$the_curator_id'								
								)
							
						)
						
					order by 
						ReceiptDate asc, rid asc
					limit 0, 1
			
			";
			
			
			$latest_payment_date_array = getFirstRow($sql);
			
			$curator_id_filters = "";
			
			
			$latest_payment_date = $latest_payment_date_array[ReceiptDate];
			$latest_payment_rid = $latest_payment_date_array[rid];
			
			
			//yoes 20190125
			//yoes 20190130
			//no longer need this
			/*
			if($lawful_status == 1 || !$child_curator_id){
				
				$the_sql = "select 
						ReceiptDate
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
						order by ReceiptDate, BookReceiptNo, ReceiptNo desc
						limit 0,1
						";
						
						
					$latest_payment_date = getFirstItem($the_sql);
					//echo " latest_payment_date ".$latest_payment_date;
				
			}*/
			
			
			if($latest_payment_date && $latest_payment_date != "0000-00-00 00:00:00"  && 1==0){
				$current_date = $latest_payment_date;
			}elseif($latest_payment_date && !$curator_id_filters){
				
				//have payment from self -> calculate interests to self date
				$current_date = $latest_payment_date;
				//echo $current_date;
				
			}else{
				$current_date = date('Y-m-d');
			}
			
		}
		
		
		//yoes 20190109 - if ฝึกงงาน 6 เดือน then = whole year
		//
		
		
		if(trim($the_row[curator_event] == "ฝึกงาน" && $lawful_year >= 2018 && $lawful_year <= 2500)){
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
		
		if($the_row[curator_start_date] == "0000-00-00" || $the_row[curator_start_date] <= $the_row[Year]."-01-01" || $is_6_month_training){		
			$the_row[curator_start_date] = $year_start_date;	
		}
		
		if($the_row[curator_end_date] == "0000-00-00" || $the_row[curator_end_date] > $year_end_date || $is_6_month_training){		
			$the_row[curator_end_date] = $year_end_date;	
		}
		
		
		//echo "$year_start_date - $year_end_date"; exit();
		//
		
		
		
		
		/*echo "<br>";
		echo "parent_curator_id: $parent_curator_id";
		echo "child_curator_id: $child_curator_id";
		*/
		
		//return;
		
		
		//yoes 20181029 -- need to have "before" and "after" amount
		if($parent_curator_id){
			//
			$parent_end_date = get3545DaysParentEndDate($the_curator_id);	
			
			if($interest_start_date < $parent_end_date){
				
				$interest_start_date = $parent_end_date;
				
			}

			
			//have parent -> use parent end date as start_date
			$this_m35_date_diff_before = dateDiffTs(strtotime($parent_end_date), strtotime($the_row[curator_start_date]),-1);	
			
			if($this_m35_date_diff_before <= 45){
				$this_m35_date_diff_before_deductable_days = $this_m35_date_diff_before;
			}
			
		}else{
			$this_m35_date_diff_before = dateDiffTs(strtotime($year_start_date), strtotime($the_row[curator_start_date]),0);	
		}
		
		
		$this_m35_date_diff_before_deducted = $this_m35_date_diff_before-$this_m35_date_diff_before_deductable_days;
		
		
		if($child_curator_id){
			//have child -> no need calculate "after"
			$this_m35_date_diff_after = 0;
		}else{
			$this_m35_date_diff_after = dateDiffTs(strtotime($the_row[curator_end_date]), strtotime($year_end_date),0);	
		}
		
		//yoes 20181029 -- deductable able
		$this_m35_date_no_pay = dateDiffTs(strtotime($the_row[curator_start_date]), strtotime($the_row[curator_end_date]),1);	
			
		
		
		//echo $this_m35_date_diff;
		$this_year_wage =  getThisYearWage($the_row[Year]);	
		
		//split this to before +after
		$m34_to_pay_before = $this_year_wage * ($this_m35_date_diff_before);
		$m34_to_pay_before_deducted = $this_year_wage * ($this_m35_date_diff_before_deducted);
		$m34_to_pay_after= $this_year_wage * ($this_m35_date_diff_after);	
		$m34_no_need_pay = $this_year_wage * ($this_m35_date_no_pay);	
		
		$m34_deductable = $this_year_wage * ($this_m35_date_diff_before_deductable_days);	
		
		
			
		if($current_date >= $interest_start_date){
			
			$interest_days_before = max(dateDiffTs(strtotime($interest_start_date), strtotime($current_date), 1),0);
			
			
			if($child_curator_id){
				//have child = no "AFTER"
				$interest_days_after = 0;
			}elseif($the_row[curator_end_date] < $interest_start_date){
				$interest_days_after = max(dateDiffTs(strtotime($interest_start_date), strtotime($current_date), 1),0);
			}else{
				
				//echo $the_row[curator_end_date];
				//yoes 20190201
				$interest_start_date = $the_row[curator_end_date];
				$interest_days_after = max(dateDiffTs(strtotime($the_row[curator_end_date]), strtotime($current_date), 0),0);
			}
			
			
		}	
		
		//}
		
		//amount to pay
		$interest_amount_before = $interest_days_before * (7.5/100/365) * $m34_to_pay_before_deducted;
		$interest_amount_after  = $interest_days_after  * (7.5/100/365) * $m34_to_pay_after ;
		
		//******
		
		
		
		
		$deduction = array(
						
						
						//parent+child
						"my_curator_id" => $the_curator_id
						, "parent_curator_id" => $parent_curator_id
						, "child_curator_id" => $child_curator_id
						

						//date before +after +middle
						, "this_m35_date_diff_before" => $this_m35_date_diff_before
						, "this_m35_date_diff_before_deductable_days" => $this_m35_date_diff_before_deductable_days 
						, "this_m35_date_diff_before_deducted" => $this_m35_date_diff_before_deducted //for case <= 45 days
						
						
						, "this_m35_date_diff_after" => $this_m35_date_diff_after					
						, "this_m35_date_no_pay" => $this_m35_date_no_pay
											
						
						//payment for before +after +middle
						, "m34_to_pay_before" => $m34_to_pay_before
						, "m34_to_pay_before_deducted" => $m34_to_pay_before_deducted
						, "m34_to_pay_after" => $m34_to_pay_after
						, "m34_deductable" => $m34_deductable
						, "m34_no_need_pay" => $m34_no_need_pay
						
						//interests for before +after (no middle)
						, "interest_start_date" => $interest_start_date
						, "interest_days_before" => $interest_days_before
						
						, "interest_end_date" => $current_date
						, "interest_days_after" => $interest_days_after
						
						, "interest_amount_before" => $interest_amount_before
						, "interest_amount_after" => $interest_amount_after
						
						
						//yoes 20190130 --> add this for curator also
						, "latest_payment_date" => $latest_payment_date
						, "latest_payment_rid" => $latest_payment_rid
						
		
					);
		
		
		//echo "<br>-----";print_r($deduction);
		
		return $deduction;
	}
	
	
	
	
	function get35AmountToPayByCuratorId($the_curator_id){
			
			$result_array = array();
			$current_curator_id = $the_curator_id;
			
			//if have child just return nothing -> we only interested in lowest child
			if(getChildOfCurator($current_curator_id)){
				return false;
			}
			
			$ii = 0;			
			while($current_curator_id && $ii < 100){
				
				
				$result_array = get35DeductionByCuratorIdArray($current_curator_id);
				
				//print_r($result_array);
				
				$total_amount += $result_array[m34_to_pay_before_deducted]+$result_array[m34_to_pay_after];
				$total_interest += $result_array[interest_amount_before]+$result_array[interest_amount_after];
				
				$current_curator_id = getParentOfCurator($current_curator_id);
				
				
				$ii++;
				
			}
			
			$return_array = array(
				
				"total_amount" => $total_amount
				, "total_interest" => $total_interest
			
				);
				
				
			
			return $return_array;
			
			
	}

	


	function getParentOfCurator($curator_id, $the_mode = ""){

		//yoes 20220304
		//global $es_table_name_suffix ;
		//global $es_field_name_suffix ;
		if($the_mode == "ejob"){
			//
			$es_table_name_suffix = "_company";
			$es_field_name_suffix = "-es";
		}
		
		$parent_of_row = getFirstRow("
					select 
						* 
					from 
						curator$es_table_name_suffix a				
							left join curator_meta b
								on a.curator_id = b.meta_value
								and
								meta_for = 'child_of$es_field_name_suffix' 
					where 
						meta_curator_id = '$curator_id'
					");
					
		if($parent_of_row["meta_value"]){		
			//#this leid is child of something
			//get end-date of that
			$parent_curator_id = $parent_of_row["meta_value"]*1;
			
		}	
		return $parent_curator_id;
		
	}



	function getChildOfCurator($curator_id, $the_mode = ""){	
	
		//yoes 20220304
		//global $es_table_name_suffix ;
		//global $es_field_name_suffix ;
		if($the_mode == "ejob"){
			//
			$es_table_name_suffix = "_company";
			$es_field_name_suffix = "-es";
		}
		
		$child_of_row = getFirstRow("
					select 
						* 
					from 
						curator$es_table_name_suffix a				
							left join curator_meta b
								on a.curator_id = b.meta_curator_id
								and
								meta_for = 'child_of$es_field_name_suffix' 
					where 
						meta_value = '$curator_id'
					");
					
		if($child_of_row["meta_value"]){		
			//#this leid is child of something
			//get end-date of that
			$child_curator_id = $child_of_row["meta_curator_id"]*1;
			
		}	
		return $child_curator_id;
		
	}


	function getChildrenOfCurator($curator_id){	
		
		$all_child_id_array = array();
		
		$child_id = getChildOfCurator($curator_id);
		
		while($child_id){
		
			array_push($all_child_id_array, $child_id);
			
			$child_id = getChildOfCurator($child_id);
			
		}
		
		//print_r($all_child_id_array);
		
		return $all_child_id_array;
		
		
	}


	function countCuratorByLid($the_lid){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		$lid_row = getFirstRow("select cid, year from lawfulness$es_table_name_suffix where lid = '$the_lid'");
		
		
		$the_count = getFirstItem("
		
			select
				count(*)
			from
				curator$es_table_name_suffix
			where
				curator_lid = '".$the_lid."'
				and
				curator_parent = 0
		
		");
		
		
		
		return $the_count;
		
		
	}


	function get3545DaysParentEndDate($the_curator_id){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		$parent_curator_id =  getParentOfCurator($the_curator_id);
		
		if($parent_curator_id){
			$parent_end_date = getFirstItem("select curator_end_date 
												from curator$es_table_name_suffix 
												where curator_id = '$parent_curator_id'");
		}
		//echo " - ". $parent_end_date;
		
		return $parent_end_date;
		
	}
	
	
	function get3545DaysParentStartDate($the_curator_id){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		$parent_curator_id =  getParentOfCurator($the_curator_id);
		
		if($parent_curator_id){
			$parent_end_date = getFirstItem("select curator_start_date 
												from curator$es_table_name_suffix 
												where curator_id = '$parent_curator_id'");
		}
		//echo " - ". $parent_end_date;
		
		return $parent_end_date;
		
	}
	
	

	function getNumCuratorFromLid($the_lid, $force_old_law = 0, $the_mode = ""){
		
		/*global $es_table_name_suffix ;
		global $es_field_name_suffix ;*/
		
		if($the_mode == "ejob"){
			$es_table_name_suffix = "_company" ;
			$es_field_name_suffix = "-es" ;
		}
		
		$company_row = getFirstRow("
					select 
						a.CID as the_cid
						, b.Year as the_year
					from
						company a
							join
								lawfulness$es_table_name_suffix b
									on
									a.cid = b.cid
					where
						b.lid = '$the_lid'

							");
		
		//
		if($company_row["the_year"] >= 2018 && $company_row["the_year"] <= 2050 && !$force_old_law){
			$extra_sql = "
			
					and
						
						curator_id not in (
						
							select
								meta_value
							from
								curator_meta
							where
								meta_for = 'child_of$es_field_name_suffix'
						
						)
						
					and
						curator_id not in (
						
							select
								meta_curator_id
							from
								curator_meta
							where
								meta_for = 'is_extra_35$es_field_name_suffix'
								and
								meta_value = 1
						
						)
			
			";
		}
		
		$the_sql = "
					SELECT 
						count(*)
					FROM 
						curator$es_table_name_suffix
					where
						curator_lid = '$the_lid'
						and
						curator_parent = 0
						
						$extra_sql
						
						";
						
		return getFirstItem($the_sql);
		
	}
	
	
	//yoes 20230109
	function getNumCuratorUseeFromLid($the_lid, $force_old_law = 0, $the_mode = ""){
		
		//global $es_table_name_suffix ;
		//global $es_field_name_suffix ;
		if($the_mode == "ejob"){
			$es_table_name_suffix = "_company" ;
			$es_field_name_suffix = "-es" ;
		}
		
		$company_row = getFirstRow("
					select 
						a.CID as the_cid
						, b.Year as the_year
					from
						company a
							join
								lawfulness$es_table_name_suffix b
									on
									a.cid = b.cid
					where
						b.lid = '$the_lid'

							");
		
		//
		if($company_row["the_year"] >= 2018 && $company_row["the_year"] <= 2050 && !$force_old_law){
			$extra_sql = "
			
					and
						
						curator_id not in (
						
							select
								meta_value
							from
								curator_meta
							where
								meta_for = 'child_of$es_field_name_suffix'
						
						)
						
					and
						curator_id not in (
						
							select
								meta_curator_id
							from
								curator_meta
							where
								meta_for = 'is_extra_35$es_field_name_suffix'
								and
								meta_value = 1
						
						)
			
			";
		}
		
		$the_sql = "
					SELECT 
						count(*)
					FROM 
						curator$es_table_name_suffix
					where
						curator_lid = '$the_lid'
						and
						curator_parent in (
						
							SELECT 
								curator_id
							FROM 
								curator$es_table_name_suffix
							where
								curator_lid = '$the_lid'
								and
								curator_parent = 0
						
								$extra_sql
						
						)
						
						
						
						";
						
		return getFirstItem($the_sql);
		
	}


	///--
	function get35DeductionByCIDYearArray($this_cid, $this_lawful_year, $as_of_date = "0"){
		
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		$this_lid = getFirstItem("
			
						select
							lid
						from
							lawfulness$es_table_name_suffix
						where
							cid = '$this_cid'
							and
							year = '$this_lawful_year'
					
					");
		
		//get lawfulness m35 of this lawfulness
		$the_sql = "
						select
							*
						from
							curator$es_table_name_suffix
						where
							curator_lid = '$this_lid'
							and 
							curator_parent = 0
							and
							curator_id not in (
							
								select
									meta_curator_id
								from
									curator_meta
								where
									meta_for = 'is_extra_35$es_field_name_suffix'
									and
									meta_value = 1
							
							)	
						";
		
		//echo $the_sql;
						
		$m35_result = mysql_query($the_sql);				
		
			
		while($m35_row = mysql_fetch_array($m35_result)){
			
			//echo "<br>". $m35_row[le_id];
				
			$m35_total_reduction_array = get35DeductionByCuratorIdArray($m35_row[curator_id], $as_of_date);
			
			$m35_total_reduction += $m35_total_reduction_array[m34_no_need_pay];
			
			$m35_total_missing += $m35_total_reduction_array[m34_to_pay_before_deducted] +$m35_total_reduction_array[m34_to_pay_after];
			
			$m35_total_interests += $m35_total_reduction_array[interest_amount_before] +$m35_total_reduction_array[interest_amount_after];
			
			$m35_total_pending += $m35_total_reduction_array[m34_to_pay_pending];
			
		}
		
		
		return array(
		
				"m35_total_reduction" => $m35_total_reduction
				,"m35_total_missing" => $m35_total_missing
				,"m35_total_interests" => $m35_total_interests
				,"m35_total_pending" => $m35_total_pending
		
			);
		
	}



?>