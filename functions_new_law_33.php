<?php

	require_once("functions_new_law_origin_20190218.php");

	if($sess_accesslevel == 4){
		$es_table_name_suffix = "_company";
		$es_field_name_suffix = "-es";
	}

	
	function get33AmountToPayByLeid($the_leid){
				
				$result_array = array();
				$current_leid = $the_leid;
				
				//if have child just return nothing -> we only interested in lowest child
				if(getChildOfLeid($current_leid)){
					return false;
				}
				
				//echo $the_leid;
				$ii = 0;			
				while($current_leid && $ii < 100){
					
					//echo $current_leid;
					
					$result_array = get33DeductionByLeidArray($current_leid);
					
					//print_r($result_array);
					
					//$total_amount += $result_array[m34_to_pay_before_deducted]+$result_array[m34_to_pay_after];
					
					//yoes 20190218
					$total_principal += $result_array[m34_total_principal];
					
					
					$total_amount += $result_array[m34_to_pay_pending];
					$total_interest += $result_array[interest_amount_before]+$result_array[interest_amount_after];
					
					//yoes 20190218
					//get33DeductionByLeidArray already include parents
					//$current_leid = getParentOfLeid($current_leid);
					$current_leid = "";
					
					$ii++;
					
				}
				
				$return_array = array(
					
					"total_principal" => $total_principal
					, "total_interest" => $total_interest
					, "total_amount" => $total_amount
					
				
					);
					
					
				//print_r($return_array);
				
				return $return_array;
				
				//print_r($parent_array);
				
	}

	//
	function get33DeductionByCIDYearArray($this_cid, $this_lawful_year, $as_of_date = ""){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		
		//get lawfulness m33 of this lawfulness
		$the_sql = "
						select
							*
						from
							lawful_employees$es_table_name_suffix 
						where
							le_cid = '$this_cid'
							and 
							le_year = '$this_lawful_year'
							and
							le_id not in (
							
								select
									meta_leid
								from
									lawful_employees_meta
								where
									meta_for = 'is_extra_33$es_field_name_suffix'
									and
									meta_value = 1
							
							)
						order by
							le_start_date asc
							, le_end_date desc
						";
		
		//echo $the_sql; exit();
						
		$m33_result = mysql_query($the_sql);				
		
		//$mm33_counter;
		//$mm33_full_deduction_counter;
		//$m33_partial_array = array();
		
		while($m33_row = mysql_fetch_array($m33_result)){
			
			//echo "<br>". $m33_row[le_id];
				
			$m33_total_reduction_array = get33DeductionByLeidArray($m33_row[le_id], $as_of_date);
			
			//print_r($m33_total_reduction_array);
			
			$m33_total_reduction += $m33_total_reduction_array[m34_no_need_pay];
			
			//echo "<br>--<br>";
			//print_r($m33_total_reduction_array); exit();
			//echo "$m33_total_reduction_array[m34_to_pay_before] +$m33_total_reduction_array[m34_to_pay_after]";
			
			$m33_total_missing += $m33_total_reduction_array[m34_to_pay_before_deducted] +$m33_total_reduction_array[m34_to_pay_after];
			
			//yyoes 20190325
			$m33_total_pending += $m33_total_reduction_array[m34_to_pay_pending];
			
			
			//echo "<br> total m34_to_pay_pending->".$m33_total_reduction_array[m34_to_pay_pending];
			//echo "<br> total m33_total_pending->".$m33_total_pending;
			
			$m33_total_interests += $m33_total_reduction_array[interest_amount_before] +$m33_total_reduction_array[interest_amount_after];
			
		}
		
		//echo "<br>"; exit();
		//
		//echo $m33_total_reduction;
		//echo $m33_total_missing; exit();
		//echo $m33_total_interests;
		
		//echo "<br>total m33_total_pending: -> ".$m33_total_pending; exit();
		
		return array(
		
				"m33_total_reduction" => $m33_total_reduction
				,"m33_total_missing" => $m33_total_missing
				,"m33_total_interests" => $m33_total_interests
				,"m33_total_pending" => $m33_total_pending
		
			);
		
	}

	
	//yoes 20190222
	//yoes add this here so we can use the MAIN get3335DeductionByXIDArray as common function
	function get33DeductionByLeidArray($the_leid, $as_of_date = "", $show_message = 0){
		
		$output = get3335DeductionByXIDArray($the_leid, $as_of_date, $show_message, "m33");
		return $output;
		
	}

	
	


	function get33PaidAmountbyLeidArray($the_leid){
		
		
		$this_parents_array = getAll33ParentsByLeidArray($the_leid);
			
		for($i=0;$i<count($this_parents_array);$i++){
			
			$leid_filters .= ",'".$this_parents_array[$i]."'";
		}
		
		$sql = "
			
			select 
					sum(Amount)
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
								$leid_filters									
							)
						
					)
					
		
		";
		
		//echo $sql;
		
		$the_amount = getFirstItem($sql);
		
		return $the_amount;
		
	}
	
	
	
	//yoes 20180208
	//yoes 20181025
	//add param to be able to get "intersts" of this leid also
	// $get_interest = 1 -> show money
	// $get_interest = 2 -> show day
	function get33DeductionByLeid($the_leid, $get_interest = 0){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		$the_row = getFirstRow("select * from lawful_employees$es_table_name_suffix where le_id = '$the_leid'") ;
		
		if($the_row[le_start_date] == "0000-00-00" || $the_row[le_start_date] <= $the_row[le_year]."-01-01"){		
			$the_row[le_start_date] = $the_row[le_year]."-01-01";	
		}
		
		//
		$parent_end_date = get3345DaysParentEndDate($the_leid);
		
		
		//off set apply to parent only (first record)
		$the_off_set = 1;
		if($parent_end_date){
			
			$employment_gap = dateDiffTs(strtotime($parent_end_date), strtotime($the_row[le_start_date]));
			
			if($employment_gap <= 45){		
				$the_row[le_start_date] = $parent_end_date;					
			}
			
			$the_off_set = 0;
		}
		
		if($the_row[le_end_date] == "0000-00-00"){		
			$the_row[le_end_date] = $the_row[le_year]."-12-31";	
			
		}
		
		
		
		$this_m33_date_diff = dateDiffTs(strtotime($the_row[le_start_date]), strtotime($the_row[le_end_date]),$the_off_set);	

		//yoes 20240709 ?
		if($the_row[le_end_date] == '2024-12-31' && $this_m33_date_diff >= 366 && $the_row[le_year] == 2024){			
			//$this_m33_date_diff = 365;
		}
	
		
		//echo $this_m33_date_diff;
		
		
		//yoes 20181025 - also account for ดอกเบี้ย
		if($get_interest == 1){
			return getInterestDate($parent_end_date, $the_row[le_year], $the_row[le_start_date]) * getThisYearWage($the_row[le_year]);
		}elseif($get_interest == 2){
			return getInterestDate($parent_end_date, $the_row[le_year], $the_row[le_start_date]);
		}	
		
		$m33_total_reduction = getThisYearWage($the_row[le_year]) * $this_m33_date_diff;
		return $m33_total_reduction;
		
	}


	function getAll33ParentsByLeidArray($the_leid){
		
		$result_array = array();
		$current_leid = $the_leid;
		
		//no parent then -> nothing	
		if(!getParentOfLeid($current_leid)){
			//echo "wwwa";
			return $result_array;
		}
		
		
		//echo $the_leid;
		$ii = 0;			
		while($current_leid && $ii < 100){
			
			//echo $current_leid;
			$current_leid = getParentOfLeid($current_leid);
			
			if($current_leid){
				array_push($result_array, $current_leid);
			}
			
			
			$ii++;
			
		}
		
		return $result_array;


	}






	function getParentOfLeid($the_leid, $the_mode = ""){
		
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
						lawful_employees$es_table_name_suffix a				
							left join lawful_employees_meta b
								on a.le_id = b.meta_leid
								and
								meta_for = 'child_of$es_field_name_suffix' 
					where 
						le_id = '$the_leid'
					");
					
		if($child_of_row["meta_value"]){		
			//#this leid is child of something
			//get end-date of that
			$parent_leid = $child_of_row["meta_value"]*1;
			
		}	
		return $parent_leid;
		
	}




	function countLawfulEmployeesByLid($the_lid){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		
		$lid_row = getFirstRow("select cid, year from lawfulness$es_table_name_suffix where lid = '$the_lid'");
		
		
		$the_count = getFirstItem("
		
			select
				count(*)
			from
				lawful_employees$es_table_name_suffix
			where
				le_cid = '".$lid_row["cid"]."'
				and
				le_year = '".$lid_row["year"]."'
		
		");
		
		
		
		return $the_count;
		
		
	}



	function getChildOfLeid($the_leid, $the_mode = ""){	
	
	
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
						lawful_employees$es_table_name_suffix a				
							left join lawful_employees_meta b
								on a.le_id = b.meta_leid
								and
								meta_for = 'child_of$es_field_name_suffix' 
					where 
						meta_value = '$the_leid'
					");
					
		if($child_of_row["meta_value"]){		
			//#this leid is child of something
			//get end-date of that
			$child_leid = $child_of_row["le_id"]*1;
			
		}	
		return $child_leid;
		
	}

	function get3345DaysParentEndDate($the_leid){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		$parent_leid =  getParentOfLeid($the_leid);
		
		if($parent_leid){
			$parent_end_date = getFirstItem("select le_end_date from lawful_employees$es_table_name_suffix where le_id = '$parent_leid'");
		}
		//echo " - ". $parent_end_date;
		
		return $parent_end_date;
		
	}
	
	
	function get3345DaysParentStartDate($the_leid){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		$parent_leid =  getParentOfLeid($the_leid);
		
		if($parent_leid){
			$parent_end_date = getFirstItem("select le_start_date from lawful_employees$es_table_name_suffix where le_id = '$parent_leid'");
		}
		//echo " - ". $parent_end_date;
		
		return $parent_end_date;
		
	}

	
	function getHireNumOfEmpFromLid($the_lid, $force_old_law = 0, $the_mode = ""){
		
		//global $es_table_name_suffix ;
		//global $es_field_name_suffix ;
		if($the_mode == "ejob"){
			//
			$es_table_name_suffix = "_company";
			$es_field_name_suffix = "-es";
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
						
						le_id not in (
						
							select
								meta_value
							from
								lawful_employees_meta
							where
								meta_for = 'child_of$es_field_name_suffix'
								
						
						)
						
					and
						le_id not in (
						
							select
								meta_leid
							from
								lawful_employees_meta
							where
								meta_for = 'is_extra_33$es_field_name_suffix'
								and
								meta_value = 1
						
						)
			
			";
		}
		
		$the_sql = "
					SELECT 
						count(le_id)
					FROM 
						lawful_employees$es_table_name_suffix
					where
						le_cid = '".$company_row["the_cid"]."'
						and le_year = '".$company_row["the_year"]."'
						
						$extra_sql
						
						";
						
		return getFirstItem($the_sql);
		
	}



	function get33DeductionByCIDYear($this_cid, $this_lawful_year){
		
		global $es_table_name_suffix ;
		global $es_field_name_suffix ;
		
		//get lawfulness m33 of this lawfulness
		$the_sql = "
						select
							*
						from
							lawful_employees$es_table_name_suffix 
						where
							le_cid = '$this_cid'
							and 
							le_year = '$this_lawful_year'
							and
							le_id not in (
							
								select
									meta_leid
								from
									lawful_employees_meta
								where
									meta_for = 'is_extra_33$es_field_name_suffix'
									and
									meta_value = 1
							
							)
							
						order by
							le_start_date asc
							, le_end_date desc
						";
		
		//echo $the_sql; //exit();
						
		$m33_result = mysql_query($the_sql);				
		
		$mm33_counter;
		$mm33_full_deduction_counter;
		$m33_partial_array = array();
		
		while($m33_row = mysql_fetch_array($m33_result)){
			
			//echo "<br>". $m33_row[le_id];
				
			$m33_total_reduction += get33DeductionByLeid($m33_row[le_id]);
			
		}
		
		return $m33_total_reduction;
		
	}




?>