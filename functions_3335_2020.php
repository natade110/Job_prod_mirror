<?php

//include_once "db_connect.php";
	
//print_r(get33Flows(248090));	


//run this after have principals....
function syncPaymentMeta($this_lid, $force_resync = 0, $the_mode = "m33"){
	
	if($the_mode == "m35"){
		
		$principal_table = "lawful_35_principals";
		$meta_prefix = "c";
		$xx_for = "35_for";
		
	}else{
		
		//33
		$principal_table = "lawful_33_principals";
		$meta_prefix = "";
		$xx_for = "33_for";
		
	}
	
	//if force resync off then do the sync only when there are no "New" meta format
	if($force_resync == 0){
		
		$new_meta_count = getFirstItem("
		
			select
				count(*)
			from
				receipt_meta
			where
				meta_for like '".$meta_prefix.$this_lid."%'
		
		");
		
		if($new_meta_count){
			return 0;
		}
		
	}
	
	//see if there are payments ... ?
	$the_receipt_sql = "
		
		SELECT 
			* 
		FROM 
			payment a
				join 
					receipt b
					on
					a.rid = b.rid
		WHERE
			a.lid = '".($this_lid)."'																				
	
	";
	
	$the_receipt_result = mysql_query($the_receipt_sql);
	
	while($the_receipt_row = mysql_fetch_array($the_receipt_result)){
		
		$old_meta_sql = "
		
			select
				*
			from
				receipt_meta
			where
				meta_rid = '".$the_receipt_row[RID]."'
				and
				meta_for like '$xx_for-%-amount'
		
		";
		
		$old_meta_result = mysql_query($old_meta_sql);
		
		while($old_meta_row = mysql_fetch_array($old_meta_result)){
			
			$new_principal_sql = "
				
				select
					*
				from
					$principal_table
				where
					p_from  = '".(str_replace("-amount","",str_replace("$xx_for-","",$old_meta_row[meta_for])))."'
					or
					p_to  = '".(str_replace("-amount","",str_replace("$xx_for-","",$old_meta_row[meta_for])))."'
			
			";
			
			$new_principal_result = mysql_query($new_principal_sql);
			
			if(mysql_num_rows($new_principal_result) == 1){

				$new_principal_row = getFirstRow($new_principal_sql);
			
			
				//yoes 20200623
				//delete old meta before insert new metas...
				mysql_query("delete from receipt_meta where meta_rid = '".$the_receipt_row[RID]."' and meta_for = '".$meta_prefix.$this_lid.$new_principal_row[p_from].$new_principal_row[p_to]."'");
			
				//only have one meta -> this is easy -> just assign old meta to new meta
				$new_meta_sql = "
				
					replace into receipt_meta(
						meta_rid
						, meta_for
						, meta_value
					)values(
						'".$the_receipt_row[RID]."'
						, '".$meta_prefix.$this_lid.$new_principal_row[p_from].$new_principal_row[p_to]."'
						, '".$old_meta_row[meta_value]."'
					
					)
				";
				
				//echo "<br>new_meta_sql: $new_meta_sql";
				mysql_query($new_meta_sql);
				
			}else{
			
				//sypnosis 1 -> try assign to closest amounts
				
				$old_meta_amount = $old_meta_row[meta_value];
				$lowest_diff_id_from = 0;
				$lowest_diff_id_to = 0;
				$lowest_diff_amount = 9999999;
				
				$this_diff_amount = 999999;
				
				while($new_principal_row = mysql_fetch_array($new_principal_result)){
					
					//echo "<br> - new principal row: ";
					//print_r($new_principal_row);
					
					$this_diff_amount = abs($new_principal_row[p_amount]-$old_meta_row[meta_value]);
					
					if($this_diff_amount < $lowest_diff_amount){
						$lowest_diff_amount = $this_diff_amount;
						$lowest_diff_id_from = $new_principal_row[p_from];
						$lowest_diff_id_to = $new_principal_row[p_to];
					}
					
				}
				
				mysql_query("delete from receipt_meta where meta_rid = '".$the_receipt_row[RID]."' and meta_for = '".$meta_prefix.$this_lid.$lowest_diff_id_from.$lowest_diff_id_to."'");
				
				//only have one meta -> this is easy -> just assign old meta to new meta
				$new_meta_sql = "
				
					replace into receipt_meta(
						meta_rid
						, meta_for
						, meta_value
					)values(
						'".$the_receipt_row[RID]."'
						, '".$meta_prefix.$this_lid.$lowest_diff_id_from.$lowest_diff_id_to."'
						, '".$old_meta_row[meta_value]."'
					
					)
				";
				
				//echo "<br><b>$new_meta_sql</b>";
				mysql_query($new_meta_sql);
				
				
			}
			
		}
		
		
		
	}
	
	
}


function generate33InterestsFromLID($this_lid, $table_suffix = "", $current_date = "", $the_mode = ""){
	
	if(!$current_date){
		$current_date = date("Y-m-d");
	}
	
	$principal_33_sql = "
			
		select
			*
		from
			lawful_33_principals$table_suffix
		where
			p_lid = '$this_lid'
	
	";
	
	$principal_33_result = mysql_query($principal_33_sql);
	
	while($principal_33_row = mysql_fetch_array($principal_33_result)){

        if($this_lid == 2050649058){
            //print_r($principal_33_row);
            //exit();
            //echo "<pre>"; print_r($total_money_to_pay_array); echo "</pre>"; exit();
            //continue;
        }
		
		//yoes 20200618 try get interests function here...
		generateInterestsFromPrincipals($this_lid, $principal_33_row[p_from],  $principal_33_row[p_to], "m33", $table_suffix, $current_date, $the_mode);
		
	}

}

function generate35InterestsFromLID($this_lid, $table_suffix = "", $current_date = "", $the_mode = ""){
	
	if(!$current_date){
		$current_date = date("Y-m-d");
	}
	
	$principal_35_sql = "
			
		select
			*
		from
			lawful_35_principals$table_suffix
		where
			p_lid = '$this_lid'
	
	";
	
	$principal_35_result = mysql_query($principal_35_sql);
	
	while($principal_35_row = mysql_fetch_array($principal_35_result)){
		
		//yoes 20200618 try get interests function here...
		generateInterestsFromPrincipals($this_lid, $principal_35_row[p_from],  $principal_35_row[p_to], "m35", $table_suffix, $current_date, $the_mode);
		
	}

}


function calculateInterestDayOffset($last_payment_date, $year_interest_start_date, $interest_start_date, $interest_end_date, $the33_next_payment_date){
	
	//echo " <br>--> ".$the33_next_payment_date . " <-- ";
			
	//yoes 20200620
	//date off-set stuffs
	//may be move this to some common funcction or something
	if($last_payment_date >= $year_interest_start_date){
		//(1) if paid after or on 1 april.... then add ONE day to interest date
		$interest_day_offset = 1;
	}elseif($last_payment_date != '0000-00-00' && $last_payment_date < $year_interest_start_date){
		//(2) if paid before 1 april.... then not add a day
		$interest_day_offset = 0;
				
		if($the33_next_payment_date && $the33_next_payment_date >= $year_interest_start_date){
			$interest_day_offset = 1;
			
		}
		
	}elseif($interest_start_date == $year_interest_start_date){
		$interest_day_offset = 1;
	}else{
		$interest_day_offset = 1;
	}
	
	if($interest_start_date == $interest_end_date){
		$interest_day_offset = 0;
		//echo "what this";
		
	}	
	//
	
	return $interest_day_offset;
	
}



function generateInterestsFromPrincipals($this_lid, $p_from, $p_to, $mode = "m33", $table_suffix = "", $current_date = "", $the_mode = ""){
	
	if(!$this_lid){
		return;
	}
	
	
	if(!$current_date){
		$current_date = date("Y-m-d");
	}
	
	//yoes 20220304
	//global $es_table_name_suffix ;
	//global $es_field_name_suffix ;
	if($the_mode == "ejob"){
		//
		$es_table_name_suffix = "_company";
		$es_field_name_suffix = "-es";
	}
	
	//echo "<br> generateInterestsFromPrincipals($this_lid, $p_from, $p_to)";
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
		
		$principal_table = "lawful_35_principals$table_suffix";
		$assoc_display_card_id = "curator_code";		
		
		$meta_prefix = "c";
		
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
		
		$principal_table = "lawful_33_principals$table_suffix";
		$assoc_display_card_id = "le_code";
		
		$meta_prefix = "";
		
		
	}else{
		
		echo "mode not specified"; exit();
		
	}
	
	
	//prepare base data
	$principal_33_sql = "select * from $principal_table where p_from = '$p_from' and p_to = '$p_to' and p_lid = '$this_lid'";
    if($this_lid == 2050649058) {

        //print_r($interests_row);
        //echo $principal_33_sql; exit();
        //exit();
    }
	//echo $principal_33_sql;
	$principal_33_row = getFirstRow($principal_33_sql);
	
	$le_row_from = getFirstRow("select * from $assoc_table_name where $assoc_pk_name = '".$p_from."'");
	$le_row_to = getFirstRow("select * from $assoc_table_name where $assoc_pk_name = '".$p_to."'");
	
	if($mode == "m35"){
		$le_row_from[$assoc_start_date_column_name] = substr($le_row_from[$assoc_start_date_column_name],0,10);
		$le_row_from[$assoc_end_date_column_name] =  substr($le_row_from[$assoc_end_date_column_name],0,10);
		
		$le_row_to[$assoc_start_date_column_name] = substr($le_row_to[$assoc_start_date_column_name],0,10);
		$le_row_to[$assoc_end_date_column_name] =  substr($le_row_to[$assoc_end_date_column_name],0,10);
	}
	
	//yoes 20200623 fix bug incase row from is 0
	//
	if($mode == "m35"){
		$the_year = getFirstItem("select year from lawfulness where lid = '".$this_lid."'");	
	}else{
		$the_year = $le_row_from[le_year]?$le_row_from[le_year]:$le_row_to[le_year];
	}
	
	$this_year_wage = getThisYearWage($the_year);
	
	
	
	//see if there are payments ... ? for this principal
	//yoes 20220304 -- have to change p_from / p_to here
	if($the_mode == "ejob"){
		
		if($mode == "m33"){
			$p_from_meta = default_value(getFirstItem("select job_leid from lawful_employees_company where le_id = '$p_from'"),0);
			$p_to_meta = default_value(getFirstItem("select job_leid from lawful_employees_company where le_id = '$p_to'"),0);
		}elseif($mode == "m35"){
			$p_from_meta = default_value(getFirstItem("select job_curator_id from curator_company where curator_id = '$p_from'"),0);
			$p_to_meta = default_value(getFirstItem("select job_curator_id from curator_company where curator_id = '$p_to'"),0);
		}
		
		//echo "<BR>generateInterestsFromPrincipals(this_lid = $this_lid, p_from= $p_from, p_to = $p_to, mode = $mode = , table_suffix = $table_suffix = , current_date = $current_date = , the_mode = $the_mode = )<br>";	
		//echo $p_from_meta."<br>";
		//echo $p_to_meta."<br>";
		
		
	}else{
		//non-ejob
		$p_from_meta = $p_from;
		$p_to_meta = $p_to;
	}
	
	
	$the33_payment_sql = "
		
		SELECT 
			* 
		FROM 
			`receipt_meta` a
				join 
					receipt b
					on
					a.meta_rid = b.rid
		WHERE
			meta_for = '".($meta_prefix.$this_lid.$p_from_meta.$p_to_meta)."'
		order by
			ReceiptDate Asc
			, Amount Desc
			
	
	";
	
	if($the_mode == "ejob"){
		//echo "".$the33_payment_sql ."<br>";
	}
	
	$the33_payment_result = mysql_query($the33_payment_sql);
			
	
	//interests stuffs
	if($the_year >= 2018){
		$year_interest_start_date = $the_year."-04-01";
	}else{
		$year_interest_start_date = $the_year."-02-01";
	}
	
	//yoes 20200623 - more misc init 
	$year_first_date = $the_year."-01-01";
	$year_last_date = $the_year."-12-31";
	
	
	
	//pre-loop init
	$pending_principal = $principal_33_row[p_amount];

    if($this_lid == 2050649058) {

        //echo $pending_principal; exit();
    }
	
	$principal_to_calculate_interests = $pending_principal;
	
	
	$have_payments = 0;	
	$this_row_interests = 0;
	$last_payment_date = "0000-00-00";
	
	$interests_row = array();
	
	$interests_row[p_lid] = $this_lid;
	$interests_row[p_from] = $p_from;
	$interests_row[p_to] = $p_to;
	$interests_row[p_principal_before] = $pending_principal;
	
	//separete principal_to_calculate_interests...
	//$interests_row[principal_to_calculate_interests] = $principal_to_calculate_interests;
	
	$interests_row[interest_details] = array();
	
	//yoes 20200813 -- add result to some array first (so we can know that there are "next" payments....
	
	$the33_payment_array = array();
	
	while($the33_payment_row = mysql_fetch_array($the33_payment_result)){
		
		array_push($the33_payment_array, $the33_payment_row);
	
	}
	
	//while($the33_payment_row = mysql_fetch_array($the33_payment_result)){
	
	$last_loop_left_over_interest = 0;
		
	for($mim = 0; $mim < count($the33_payment_array); $mim++){
		
				
		$the33_payment_row = $the33_payment_array[$mim];
		$the33_next_payment_row = $the33_payment_array[$mim+1];
		
		//echo "<br>".$the33_payment_row[ReceiptDate] . " vs " . $le_row_to[$assoc_start_date_column_name];
		//yoes 20200817 - fix receiptDate format to yyyy-mm-dd
		$the33_payment_row[ReceiptDate] = substr($the33_payment_row[ReceiptDate],0,10);
		
		//add result to array
		$interest_detail = array();
		$interest_detail[pre_pending_principal] = $pending_principal;
		
		//yoes 20200623
		//test for 284291 -> 381354
		//pay before someone comein...
		//if($p_from == 284291 && $p_to == 381354){
		
		//for case 1.2
		
		if(
			!$p_from && $p_to && $the33_payment_row[ReceiptDate] < $le_row_to[$assoc_start_date_column_name]
			&&
			!getFirstItem("select count(*) from receipt_meta where meta_for = 'allow_prepaid' and meta_value = 1 and meta_rid = '".$the33_payment_row[RID]."'")
			){
			
			//echo "1";
			
			//echo "dateDiffTs(strtotime($year_first_date), strtotime($year_last_date) " . dateDiffTs(strtotime($year_first_date), strtotime($year_last_date));			
			$whole_year_principal = 365*$this_year_wage;		
			
			$principal_to_calculate_interests = $whole_year_principal;
		
		}elseif(
			$p_from && $p_to && $the33_payment_row[ReceiptDate] < $le_row_to[$assoc_start_date_column_name]
			&&
			!getFirstItem("select count(*) from receipt_meta where meta_for = 'allow_prepaid' and meta_value = 1 and meta_rid = '".$the33_payment_row[RID]."'")
			
			){		
		
			//echo "2";
			//for case 3.2
			
			//echo $the33_payment_row[ReceiptDate] . " < " . $le_row_to[$assoc_start_date_column_name] . "?";
			
			$whole_year_principal = max(dateDiffTs(strtotime($le_row_from[$assoc_end_date_column_name]), strtotime($year_last_date), 0),0)*$this_year_wage;						
			
			$principal_to_calculate_interests = $whole_year_principal;
			
			//echo "max(dateDiffTs(strtotime($le_row_from[le_end_date]), strtotime($year_last_date), 0),0)*$this_year_wage = $principal_to_calculate_interests";
			
		}else{
			
			//echo "3";
			$principal_to_calculate_interests = $pending_principal;
			
		}
		
		//echo "principal_to_calculate_interests -> $principal_to_calculate_interests";
		
		$interest_detail[pre_principal_to_calculate_interests] = $principal_to_calculate_interests;
		
		//interests start date can either be
		//1) year interests start date
		//2) last payment date
		//3) date วันที่ออก ของคนกอ่นหน้า +1
		$interest_start_date = max($year_interest_start_date, date('Y-m-d',strtotime($last_payment_date . "+1 days")), $principal_33_row[p_start_date]);
		
		
		//yoes 20200813 -> converted this to function
		//echo $the33_next_payment_row[ReceiptDate];
		$interest_day_offset = calculateInterestDayOffset($last_payment_date, $year_interest_start_date, $interest_start_date, $interest_end_date, $the33_payment_row[ReceiptDate]);
		
		
		//end date for this row...
		$interest_end_date = $the33_payment_row[ReceiptDate] ;
		
		//days to calculate interests...
		$interest_days = max(dateDiffTs(strtotime($interest_start_date), strtotime($interest_end_date), $interest_day_offset),0);
		
		//yoes 20200623 -- interests is calculate from $principal_to_calculate_interests
		
		//yoes 20200623 -- if no pending principal then just 0 interests
		if($principal_to_calculate_interests <= 0){
			$this_interest = 0;
		}else{
			$this_interest = round(($interest_days*(7.5/100/365)*$principal_to_calculate_interests),2);
		}
		
		//yoes 20200818 -- add left over interests from last loop
		$this_interest += $last_loop_left_over_interest;
		
		$pending_interests = $this_interest;
		//yoes 20201027 total interest will not include interests from last loop
		$total_33_interests += $this_interest - $last_loop_left_over_interest;	
		
		
		
		$paid_for_principal = max(round($the33_payment_row[meta_value]-$this_interest,2),0);
		
		$paid_for_interest = round(min($this_interest, $the33_payment_row[meta_value]),2);
		
		//echo "paid_for_interest ".$paid_for_interest;
		
		//echo "paid_for_principal = max( $the33_payment_row[meta_value]-$this_interest , 0 )= $paid_for_principal";
		
		$total_paid += $the33_payment_row[meta_value];
		$total_paid_for_principal += $paid_for_principal;
		
		//yoes 20200623 - "actual pending principal is decreased."
		$pending_principal = round($pending_principal-($paid_for_principal),2);
		
		//yoes 20200818 -- add pending interests
		$left_over_interest = round($this_interest-$paid_for_interest,2);
		
		
		//yoes 20200623 - also update principals that used for calculate interests
		//special case is stilll the same
		//yoes 20200813 -- or is it?
		//not really because already use special case "before"
		/*if(!$p_from && $p_to && $the33_payment_row[ReceiptDate] < $le_row_to[$assoc_start_date_column_name]){
		
			$principal_to_calculate_interests = $whole_year_principal;			
	
		}elseif($p_from && $p_to && $the33_payment_row[ReceiptDate] < $le_row_to[$assoc_start_date_column_name]){	
		
			$principal_to_calculate_interests = $whole_year_principal;			
			
		}else{			
			//normal case -> using new pending value instead
			$principal_to_calculate_interests = $pending_principal;			
		}*/
		
		$principal_to_calculate_interests = $pending_principal;
		
		$last_payment_date = $the33_payment_row[ReceiptDate];
		
		//echo $pending_principal . " vs " . $principal_to_calculate_interests;
		//echo $principal_to_calculate_interests;
		
		//add result to array...
		
		
		$interest_detail[interest_start_date] = $interest_start_date;
		$interest_detail[interest_end_date] = $interest_end_date;
		$interest_detail[interest_days] = $interest_days;		
		$interest_detail[this_interest] = $this_interest;
		
		$interest_detail[ReceiptDate] = $the33_payment_row[ReceiptDate];
		$interest_detail[BookReceiptNo] = $the33_payment_row[BookReceiptNo];
		$interest_detail[ReceiptNo] = $the33_payment_row[ReceiptNo];
		$interest_detail[meta_value] = $the33_payment_row[meta_value];
		
		$interest_detail[paid_for_interests] = $this_interest;
		$interest_detail[paid_for_principal] = $paid_for_principal;		
		$interest_detail[pending_principal] = $pending_principal;
		$interest_detail[principal_to_calculate_interests] = $principal_to_calculate_interests;
		
		$interest_detail[special_remarks] = $special_remarks;
		
		$interest_detail[left_over_interest] = $left_over_interest;
		$interest_detail[last_loop_left_over_interest] = $last_loop_left_over_interest;
				
		
		array_push($interests_row[interest_details], $interest_detail);
		
		//ending the loop
		$last_loop_left_over_interest = $left_over_interest;
				
	} //ends while($the33_payment_row = mysql_fetch_array($the33_payment_result)){
	
	//ending the loop
	//(in case not all principals are paid)
	
	//echo "pending_principal: ".$pending_principal;
	if($pending_principal > 0){
		
		
		
		$interest_detail = array();
		$interest_detail[pre_pending_principal] = $pending_principal;
		$interest_detail[pre_principal_to_calculate_interests] = $principal_to_calculate_interests;
		
		//echo "max($year_interest_start_date, date('Y-m-d',strtotime($last_payment_date . '+1 days')), $principal_33_row[p_start_date])";
		
		$interest_start_date = max($year_interest_start_date, date('Y-m-d',strtotime($last_payment_date . "+1 days")), $principal_33_row[p_start_date]);
		
		//yoes 20200620
		//date off-set stuffs
		/*if($last_payment_date >= $year_interest_start_date){
			//(1) if paid after or on 1 april.... then add ONE day to interest date
			$interest_day_offset = 1;
		}elseif($last_payment_date != '0000-00-00' && $last_payment_date < $year_interest_start_date){
			//(2) if paid before 1 april.... then not add a day
			$interest_day_offset = 0;
		}elseif($interest_start_date == $year_interest_start_date){
			$interest_day_offset = 1;
		}else{
			$interest_day_offset = 1;
		}
		
		if($interest_start_date == $interest_end_date){
			$interest_day_offset = 0;
		}*/
		
		//yoes 20200813 --> chnage this to common functions
		//yoes 20200813 --> next payment date is current date
		$interest_day_offset = calculateInterestDayOffset($last_payment_date, $year_interest_start_date, $interest_start_date, $interest_end_date, $current_date);
		
		//$current_date = date('Y-m-d');
		//yoes 20220218 -- move current_date that is TODAY into function's optional argument instead
		// -- to support function to allow to do คำนวณดอกเบี้ยจากวันที่ xxx ที่ไม่ใช่ today ได้
		$interest_end_date = $current_date ;
		
		//echo "max(dateDiffTs(strtotime($interest_start_date), strtotime($interest_end_date), $interest_day_offset),0);";
		
		$interest_days = max(dateDiffTs(strtotime($interest_start_date), strtotime($interest_end_date), $interest_day_offset),0);
		
		if($principal_to_calculate_interests <= 0){
			$this_interest = 0;
		}else{
			
			//echo "this_interest = round(($interest_days*(7.5/100/365)*$principal_to_calculate_interests),2);";
			$this_interest = round(($interest_days*(7.5/100/365)*$principal_to_calculate_interests),2);
		}
		
		//yoes 20200818 -- add left over interests from last loop
		$this_interest += $last_loop_left_over_interest;
		
		
		$pending_interests = $this_interest;
		
		//yoes 20201027 total interest will not include interests from last loop
		$total_33_interests += $this_interest - $last_loop_left_over_interest;	
		
		
		//add result to array...
		
		
		$interest_detail[interest_start_date] = $interest_start_date;
		$interest_detail[interest_end_date] = $interest_end_date;
		$interest_detail[interest_days] = $interest_days;		
		$interest_detail[this_interest] = $this_interest;
		
		$interest_detail[ReceiptDate] = "";
		$interest_detail[BookReceiptNo] = "";
		$interest_detail[ReceiptNo] = "";
		$interest_detail[meta_value] = "";
		
		$interest_detail[paid_for_interests] = "";
		$interest_detail[paid_for_principal] = "";		
		$interest_detail[pending_principal] = $pending_principal;
		
		$interest_detail[left_over_interest] = $left_over_interest;
		$interest_detail[last_loop_left_over_interest] = $last_loop_left_over_interest;
		
		array_push($interests_row[interest_details], $interest_detail);
		
	
	}else{
		
		//yoes 20200620
		//no principal = no interests
		$pending_interests = 0;
	}
	
	
	//add result to array...
	$interests_row[total_paid] = $total_paid;
	$interests_row[p_total_interests] = $total_33_interests;
	$interests_row[total_paid_for_principal] = $total_paid_for_principal;
	
	$interests_row[p_principal_after] = $pending_principal;
	
	//yoes 20200620 --> pending interests is wrong....
	$interests_row[pending_interests] = $pending_interests;
	
		
	//debug stuffs -> may need this or may be not
	//yoes 20200624 -- add p_pending
	$update_interests_sql = "
			
		update 
			$principal_table
		set
			p_interests = '".($total_33_interests*1)."'
			, p_paid = '".$total_paid."'
			, p_pending_amount = '".$pending_principal."'
			, p_pending_interests = '".$pending_interests."'
		where
			p_lid = '".$this_lid."'
			and
			p_from = '".$principal_33_row[p_from]."'
			and
			p_to = '".$principal_33_row[p_to]."'
			
		
	"; 
	
	//echo "<br>$update_interests_sql<br>";

    if($this_lid == 2050649058) {

        //print_r($interests_row);
        //exit();
    }
	
	mysql_query($update_interests_sql);
	
	return $interests_row; 
	
}

function generate33PrincipalFromLID($the_lid, $table_suffix = "", $the_mode = ""){
	
	$lawful_row = getFirstRow("select * from lawfulness where LID = '$the_lid'");
	
	//print_r($lawful_row);
	//yoes 20200611
	//also remove unexisted lawful33_principals (if any) - incase of รับแทน ไปแล้ว
	$p_delete_sql = "
			delete from 
				lawful_33_principals$table_suffix							
			where
				p_lid = '".($lawful_row[LID]*1)."'
	
	";
	
	mysql_query($p_delete_sql);

	//yoes 20220304
	//-ejob mode
	if($the_mode == "ejob"){
		$get_33_list_sql = getCompany33ListSqlEjob($lawful_row[CID], $lawful_row[Year]);
	}else{
		$get_33_list_sql = getCompany33ListSql($lawful_row[CID], $lawful_row[Year]);
	}


	
	
	$list_33_result = mysql_query($get_33_list_sql);



	
	
	
	while ($post_33_row = mysql_fetch_array($list_33_result)) {

        if($the_lid == 2050619067){
           //echo "<br><br>";
            //print_r($post_33_row); //exit();
        }
		
		if($lawful_row[LID] == 2050649058){
				//print_r($post_33_row);
		}
		//echo "<br>".$post_33_row[le_id];
		//yoes 20200611 - remove this from now
		//$flow_array = get33Flows($post_33_row[le_id]);		
		$flow_array = get3335Flows($post_33_row[le_id], "m33", $table_suffix, $the_mode);
		
	}

    if($the_lid == 2050649058){
        //print_r($flow_array); exit();
        //exit();
    }

}


function generate35PrincipalFromLID($the_lid, $table_suffix = "", $the_mode = ""){
	
	$lawful_row = getFirstRow("select * from lawfulness where LID = '$the_lid'");
	
	//print_r($lawful_row);
	//yoes 20200611
	//also remove unexisted lawful33_principals (if any) - incase of รับแทน ไปแล้ว
	$p_delete_sql = "
			delete from 
				lawful_35_principals$table_suffix							
			where
				p_lid = '".($lawful_row[LID]*1)."'
	
	";
	
	mysql_query($p_delete_sql);

	
	//yoes 20220304
	//-ejob mode
	if($the_mode == "ejob"){
		$get_35_list_sql = getCompany35ListSqlEjob($the_lid);
	}else{
		$get_35_list_sql = getCompany35ListSql($the_lid);
	}
	
	$list_35_result = mysql_query($get_35_list_sql);
	
	if($the_lid == 2050596915){
		//echo $get_35_list_sql;
	}
	
	while ($post_35_row = mysql_fetch_array($list_35_result)) {
		
						
		
		
		//echo "<br>".$post_33_row[le_id];
		//yoes 20200611 - remove this from now
		$flow_array = get3335Flows($post_35_row[curator_id], "m35", $table_suffix, $the_mode);
		
		if($the_lid == 2050596915){
			//echo "get3335Flows($post_35_row[curator_id], 'm35', $table_suffix, $the_mode)";
		}
		
		/*echo "<br> -- ";
		echo "<br> -- ";
		print_r($flow_array);*/
		
	}

}


//yoes 20200625 -- make this a common function
function get33Flows($the_leid){	
	return get3335Flows($the_leid, "m33");
}


function get35Flows($curator_id){	
	return get3335Flows($curator_id, "m35");
}



	
function get3335Flows($the_leid, $mode = "m33", $table_suffix = "", $the_mode = ""){
	
	
	//echo $the_mode;

	//yoes 20200625 -- generalize this
	//yoes 20220304 - no longer need Global Thing
	//global $es_table_name_suffix ;
	//global $es_field_name_suffix ;
	
	if($the_mode == "ejob"){
		//
		$es_table_name_suffix = "_company";
		$es_field_name_suffix = "-es";
	}
	
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
		
		$principal_table = "lawful_35_principals$table_suffix";
		$assoc_display_card_id = "curator_code";
		
		//echo $principal_table; exit();
		
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
		
		$principal_table = "lawful_33_principals$table_suffix";
		$assoc_display_card_id = "le_code";
		
		
	}else{
		
		echo "mode not specified"; exit();
		
	}
	
	
	
	$the_leid = $the_leid*1;	
	
	//echo $the_leid; exit();
	
	if(!$the_leid){
		//yoes 20231124 - changed from return 0 to just return
		//return 0;
		return;
		
	}
	
	//yoes 20200608
	//only do this for top-most parents
	//yoes 20200608 -- add for curator
	if($mode == "m35"){
		if(getParentOfCurator($the_leid, $the_mode)){
			return 0;
		}
		
		//yoes 20200615
		//only do this if this is non an "Extra le_id"
		$is_extra = getFirstItem("
			select 
				1 
			from 
				curator_meta 
			where 
				meta_curator_id = '".$the_leid."' 
				and 
				meta_for = 'is_extra_35$es_field_name_suffix'
				and
				meta_value != 0
				");
				
		if($is_extra){
			return 0;
		}
		
		
	}elseif($mode == "m33"){
		
		if(getParentOfLeid($the_leid, $the_mode)){
			return 0;
		}
		
		//yoes 20200615
		//only do this if this is non an "Extra le_id"
		$is_extra = getFirstItem("
			select 
				1 
			from 
				lawful_employees_meta 
			where 
				meta_leid = '".$the_leid."' 
				and 
				meta_for = 'is_extra_33$es_field_name_suffix'
				and
				meta_value != 0
				");
				
		if($is_extra){
			return 0;
		}
		
	}
	
	
		
	
	//init le_rows
	$le_row = getFirstRow("select * from $assoc_table_name where $assoc_pk_name = '$the_leid'");
	//echo "select * from $assoc_table_name where $assoc_pk_name = '$the_leid'";
	
	
	//yoes 20200624
	//m35 start-end date must be "date"
	if($mode == "m35"){
		$le_row[$assoc_start_date_column_name] = substr($le_row[$assoc_start_date_column_name],0,10);
		$le_row[$assoc_end_date_column_name] =  substr($le_row[$assoc_end_date_column_name],0,10);
	}

	//echo "<br>"."select * from $assoc_table_name where $assoc_pk_name = '$the_leid'";
	
	if($mode == "m35"){
		$the_year = getFirstItem("select year from lawfulness where lid = '".$le_row[curator_lid]."'");	
	}else{
		$the_year = $le_row[$assoc_year_column_name];	
	}
	
	
	$year_first_date = $the_year."-01-01";
	$year_last_date = $the_year."-12-31";
	
	//special for m35
	if(
		$mode == "m35" 
		&& trim($le_row[curator_event] == "ฝึกงาน" && $the_year >= 2018 && $the_year <= 2500)
		
		){
		
		
		
		$curator_start_date = $le_row[curator_start_date];		
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
		//$curator_6_month_for_compare .= " 00:00:00";
		
		$curator_end_date = $le_row[curator_end_date];
		$curator_end_day = substr($curator_end_date, 8, 2) ;
		$curator_end_month = substr($curator_end_date, 5, 2) ;
		$curator_end_year = substr($curator_end_date, 0, 4) ;
		
		
		
		if($le_row[curator_end_date] >= $curator_6_month_for_compare || $le_row[curator_end_date] == "0000-00-00"){
			$is_6_month_training = 1;
		}else{
			$is_6_month_training = 0;
		}
		
		
		
		
		
		
		if($is_6_month_training){		
			$le_row[$assoc_start_date_column_name] = $year_first_date;	
			$le_row[$assoc_end_date_column_name] = $year_last_date;	
		}
		
		
		
		
				
	}



    //yoes 20241127
    //yoes special for การจัดจ้างเหมาช่วงงานหรือการจ้างเหมาบริการ การช่วยเหลืออื่นใด
    $fixed_principal = 0;
    if(
        $mode == "m35"
        &&

        (
            //trim($le_row[curator_event]) == "จัดสถานที่จำหน่ายสินค้าหรือบริการ" //yoes 20250305 -- poom แจ้งว่า จัดสถานที่จำหน่ายสินค้าหรือบริการ ต้อง 1ปี และมูลค่าถึง ->> ตอนนี้แก้กลับเป็น check ตามเวลาก่อนตามปกติ
            trim($le_row[curator_event]) == "การให้ความช่วยเหลืออื่นใด"
            || trim($le_row[curator_event]) == "จัดจ้างเหมาช่วงงาน"
            )

            && $the_year >= 2025 && $the_year <= 2500

    ){

            $fixed_principal = $le_row["curator_value"];
            //echo $fixed_principal; exit();

    }


    //trim($le_row[curator_event]) == "จัดจ้างเหมาช่วงงาน"
    //yoes - 20250326 - quickfix for curator id = 214867
    //คือเป็น case จ้างเหมา แทนจ้างเหมา
    //บริษัท โมอินดี้ ดิจิตอล จำกัด  ปี 2568

    if($the_leid == 214867 && $le_row["curator_value"] == 20080){

        $fixed_principal = 20080+100370;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes - 20250404 - quickfix for curator id = 219295+219313 / บริษัท อีซูซุมอเตอร์(ประเทศไทย) จำกัด  2568
    if(($the_leid == 219295) && $le_row["curator_value"] == 10450 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 10450+110000;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    if(($the_leid == 219313) && $le_row["curator_value"] == 10450 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 10450+110000;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes - 20250418 - quickfix for curator id =
    if(($the_leid == 222458) && $le_row["curator_value"] == 19470 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 19470+100980;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes 20250502 - quick fix for CID == 601
    if(($the_leid == 222291) && $le_row["curator_value"] == 20450 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 20450+100000;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes 20250502 - quick fix for CID == 82352
    if(($the_leid == 214830) && $le_row["curator_value"] == 10037.5 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 10037.5+110412.5;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes 20250502 - quick fix for CID == 8282
    if(($the_leid == 216943) && $le_row["curator_value"] == 30117 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 30117+90333;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes 20250611 - quick fix for CID == 79726 2025
    if(($the_leid == 220305) && $le_row["curator_value"] == 20075 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 20075+100375;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    if(($the_leid == 220309) && $le_row["curator_value"] == 20075 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 20075+100375;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes 20250611 - quick fix for CID == 709 2025
    if(($the_leid == 222129) && $le_row["curator_value"] == 20080 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 20080+100370;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes 20250627 - quick fix for 96572 2025 - curator id = 215242
    if(($the_leid == 215242) && $le_row["curator_value"] == 60450 && trim($le_row["curator_event"]) == "การให้ความช่วยเหลืออื่นใด"){

        $fixed_principal = 60450+60000;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes 20250627 - quick fix for 694 2025 - 221713 and 221714
    if(($the_leid == 221713 || $the_leid == 221714) && $le_row["curator_value"] == 43920 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 43920+87840;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes 20250627 - qucik fix for 7435 2025 - 214714
    if(($the_leid == 214714) && $le_row["curator_value"] == 20080 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 20080+100370;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //for 73032 2025 -- 229167
    if(($the_leid == 229167) && $le_row["curator_value"] == 10043 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 10043+110407;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //for 103355 2025
    if(($the_leid == 223778) && $le_row["curator_value"] == 60450 && trim($le_row["curator_event"]) == "การให้ความช่วยเหลืออื่นใด"){

        $fixed_principal = 60450+60000;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes 20250716 for 3629 2025

    if(($the_leid == 209159 || $the_leid == 209167) && $le_row["curator_value"] == 50190 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 50190+70260;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //yoes 20250723 for 7627 2025 ...
    //220124
    if(($the_leid == 220124) && $le_row["curator_value"] == 60450 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 60450+60000;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }


    //yoes 20250804 fir 686 / 2025
    //213507
    if(($the_leid == 213507) && $le_row["curator_value"] == 50500 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 50500+70700;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //cid 559+2025
    //214920
    if(($the_leid == 214920) && $le_row["curator_value"] == 60450 && trim($le_row["curator_event"]) == "การให้ความช่วยเหลืออื่นใด"){

        $fixed_principal = 60450+60000;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //cid 46832 2025
    //curator_id 213973
    //60228.00  +60222.00
    if(($the_leid == 213973) && $le_row["curator_value"] == 60228 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 60228+60222;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //id=486&year=2025
    //220265
    //50191.00+70259.00
    if(($the_leid == 220265) && $le_row["curator_value"] == 50191 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 50191+70259;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }

    //?id=207&year=2025
    //220137
    //40154.00  +80296.00
    if(($the_leid == 220137) && $le_row["curator_value"] == 40154 && trim($le_row["curator_event"]) == "จัดจ้างเหมาช่วงงาน"){

        $fixed_principal = 40154+80296;
        $is_hardcode_fixed = 1;
        //echo "xxx";
        //exit();
    }


    //yoes 20250818

    if(
        ($le_row["curator_event"] == "จัดจ้างเหมาช่วงงาน" || $le_row["curator_event"] == "การให้ความช่วยเหลืออื่นใด")
        && $the_year >= 2025 && $the_year <= 2500
        && !$is_hardcode_fixed  // ใช้ flag แทน
        //&& !$fixed_principal  // ยังไม่ได้ set จาก hardcode ข้างบน
        //&& $the_leid == 229363
    ){

        //$fixed_principal = 40450 + 8000;
        //exit();

        ////หา all ลูก และเอา value ของลูกมาบวกใส่ value ตัวเอง
        // เริ่มต้นด้วย value ของตัวเอง

        $fixed_principal = $le_row["curator_value"];

        // หาลูก (curator ที่เข้าแทน) และรวม value ทั้งหมด
        $current_curator_id = $the_leid;
        $safety_counter = 0;

        while($current_curator_id && $safety_counter < 100) {
            // หาลูกของ curator ปัจจุบัน
            $child_curator_id = getChildOfCurator($current_curator_id, $the_mode);

            if($child_curator_id) {
                // ดึงข้อมูลของลูก รวมทั้ง event และ value
                $child_curator_sql = "SELECT curator_value, curator_event FROM curator WHERE curator_id = '{$child_curator_id}'";
                $child_curator_row = getFirstRow($child_curator_sql);

                // ตรวจสอบว่าลูกเป็น event ประเภทเดียวกันหรือไม่
                $child_event = trim($child_curator_row['curator_event']);
                if(($child_event == "จัดจ้างเหมาช่วงงาน" || $child_event == "การให้ความช่วยเหลืออื่นใด")
                    && $child_curator_row['curator_value']) {
                    $fixed_principal += $child_curator_row['curator_value'];
                }

                // เตรียมหาลูกของลูกในรอบต่อไป
                $current_curator_id = $child_curator_id;
            } else {
                // ไม่มีลูกแล้ว ออกจาก loop
                break;
            }

            $safety_counter++;
        }

    }



	
	//special for m35
	if(
		$mode == "m35" 
		
		//yoes 20230322 --> เพิ่มกรณีจัดจ้างเหมาช่วงงาน
		//ถ้ามูลค่าครบ ให้ใช้เป็นเต็มปี
		&& trim($le_row[curator_event] == "จัดจ้างเหมาช่วงงาน" && $the_year >= 2023 && $the_year <= 2500) 
		
		&& $le_row[curator_value] >= 119720
		
		&& ($le_row[curator_lid] == 2050598419 || $le_row[curator_lid] == 2050591418)
		
		){
		
		
		
		$le_row[$assoc_start_date_column_name] = $year_first_date;	
		$le_row[$assoc_end_date_column_name] = $year_last_date;	
		
		
				
	}
	
	//echo "<br><br>".$is_6_month_training;
	//echo "<br><br>".$le_row[$assoc_start_date_column_name];
	
	
	
	
	
	//yoes 20200615
	//if start date is null then - start date is sometime before this year
	if($le_row[$assoc_start_date_column_name] == "0000-00-00"){
		$le_row[$assoc_start_date_column_name] = ($the_year-1)."-12-31";
	}
	
	//get receipts of this LID
	//first get lawful row
	
	if($mode == "m35"){
		
		$lawful_row = getFirstRow("
		
			select
				*
			from
				lawfulness l
			where
				
				l.lid = '".$le_row[curator_lid]."'
		
		
		");
		
		
		
	}else{
		$lawful_row = getFirstRow("
		
			select
				*
			from
				lawfulness l
			where
				l.year = '$the_year'
				and
				l.cid = '".$le_row[le_cid]."'
		
		
		");
	}
	
	
	
	
	$this_year_wage = getThisYearWage($the_year);
	$current_date = date('Y-m-d');
	
	//yoes 20200608 add this	
	$flow_detail_array = array();
	$flow_result_array = array();


	
	if($the_year >= 2018){
		$year_interest_start_date = $the_year."-04-01";
	}else{
		$year_interest_start_date = $the_year."-02-01";
	}

		//init dates
		$date_array = array(
		
			/*"$the_year-04-01"
			, "$the_year-01-01"				
			, "$the_year-12-31"*/
		
		);
		
		$leid_array = array(
		
			/*"0"
			, "0"
			, "0"*/
		
		);
		
		$date_style_array = array(
		
			/*"list-group-item-warning"
			, "list-group-item-success"				
			, "list-group-item-success"*/
		
		);
		
		//init date array
		$the_date_array = array(
						
						//must-have dates
						/*array("date" => "$the_year-01-01", "le_row" => "", "style" => "list-group-item-success", "remarks" => "วันแรกของปี", "is_first_day_of_year" => "1")
						, array("date" => "$the_year-04-01", "le_row" => "", "style" => "list-group-item-warning", "remarks" => "วันที่เริ่มคิดดอกเบี้ย")
						, 
						*/
						
						array("date" => "$the_year-01-01", "le_row" => "", "style" => "list-group-item-success", "remarks" => "วันแรกของปี", "is_first_day_of_year" => "1")
						, array("date" => "$the_year-12-31", "le_row" => "", "style" => "list-group-item-success", "remarks" => "วันสุดท้ายของปี", "is_last_day_of_year" => "1")
						//init date from leid
						, array("date" => $le_row[$assoc_start_date_column_name], "le_row" => $le_row, "style" => "", "is_start_date" => 1)
						,  array("date" => $le_row[$assoc_end_date_column_name], "le_row" => $le_row, "style" => "", "is_end_date" => 1)
		
					);
		
		
		//echo "<br>"; print_r($the_date_array);
		
		
		//get childs of this le_id
		$leid_to_loop = $the_leid;
		
		if($mode == "m35"){
			$child_leid = getChildOfCurator($leid_to_loop, $the_mode);
		}else{
			$child_leid = getChildOfLeid($leid_to_loop, $the_mode);
		}
		
		
		while($child_leid && $i < 100){				
			
			$child_le_row = getFirstRow("select * from $assoc_table_name where $assoc_pk_name = '$child_leid'");	
			
			array_push($the_date_array, array("date" => $child_le_row[$assoc_start_date_column_name], "le_row" => $child_le_row, "style" => "", "is_child" => "1", "is_start_date" => 1));
			array_push($the_date_array, array("date" => $child_le_row[$assoc_end_date_column_name], "le_row" => $child_le_row, "style" => "", "is_child" => "1", "is_end_date" => 1));
			
			if($mode == "m35"){
				$child_leid = getChildOfCurator($child_le_row[$assoc_pk_name], $the_mode);
			}else{
				$child_leid = getChildOfLeid($child_le_row[$assoc_pk_name], $the_mode);
			}
			$i++;
			
		}
	
		
		//echo $receipt_sql;
	
		//echo "<br><br>";
		//print_r($the_date_array);
	
		//sort array by date
		//array_multisort($date_array,$date_style_array, $leid_array);								
		array_sort_by_column($the_date_array, "date");
		
		$principals_array = array();
		$interest_days_array = array();
		
		$last_end_date = "$the_year-01-01";
		
		$last_le_id = 0;		
		
		
		//echo "<br><br>--";
		//print_r($the_date_array);
		
		//yoes 20230918?
					if($the_leid == 176762){
						
						//print_r($le_row);
						//print_r($the_date_array);
						//exit();
					}
		
		
		
		for($i=0;$i<count($the_date_array);$i++){
			
			//init for each loop
			$is_entry_date = 0;
			$interest_day_offset = 0;
			$this_principal = 0;
			
			//yoes 20200615
			
			
			if($the_date_array[$i]["date"] != "0000-00-00"){


                // Add the fixed principal check and handling here
                if($fixed_principal){

                    // Calculate required amount for this year based on lawfulness record
                    $required_amount = 1 * getThisYearWage($the_year) * 365;


                    // Calculate pending amount as required amount minus fixed principal
                    $pending_amount = max($required_amount - $fixed_principal, 0);

                    //echo "$required_amount - $fixed_principal";
                    //echo $pending_amount; exit();

                    // For fixed principal cases, just create one row for full year
                    $p_sql = "replace into $principal_table(
                        p_lid,
                        p_from,
                        p_to, 
                        p_date_from,
                        p_date_to,
                        p_start_date,
                        p_amount
                    ) values (
                        '".($lawful_row[LID]*1)."',
                        '".($the_leid*1)."',
                        '".($the_leid*1)."',
                        '".$year_first_date."',
                        '".$year_last_date."',
                        '".$year_interest_start_date."',
                        '".($pending_amount*1)."'
                    )";

                    mysql_query($p_sql);

                    //exit();
                    // Skip rest of the processing since we've handled this case
                    break;
                }

				//echo "<br>";
				//print_r($the_date_array[$i]);
				
				if($the_date_array[$i]["date"] == $the_date_array[$i][le_row][$assoc_start_date_column_name]){
					//echo "วันที่เริ่มงาน"; 
					$is_entry_date = 1;		
					$last_end_date = 0; //no longer have last_end_date
					$date_type = "is_entry_date";
				}
				if($the_date_array[$i]["date"] == $the_date_array[$i][le_row][$assoc_end_date_column_name]){
					//echo "วันที่ออกจากงาน"; 					
					if($the_date_array[$i]["is_end_date"]){
						$last_end_date = $the_date_array[$i]["date"];
					}
					$date_type = "is_end_date";
					
					
					
				}
				
				if($the_date_array[$i]["remarks"]){
					$date_type = $the_date_array[$i]["remarks"];
					//echo "<br>$date_type";
				}
				
				if($the_date_array[$i][is_receipt]){								
					//echo "วันที่มีการชำระเงิน";
					$date_type = "is_pay_date";
				}
				
				
				//yoes 20200609 -- this is a receipt row...
				if($the_date_array[$i][is_receipt]){
					
			
				}elseif($is_entry_date == 1 && $the_date_array[$i]["date"] <= "$the_year-01-01"){
									
					$this_m33_date_diff_before = 0;					
					$this_principal = round($this_m33_date_diff_before*$this_year_wage,2);														
					$total_principal += $this_principal;									
					array_push($principals_array, $this_principal);
							
				}elseif($is_entry_date == 1 && !$the_date_array[$i]["is_child"]){
					
					$date_from = $the_date_array[$i-1]["date"];
					$this_m33_date_diff_before = dateDiffTs(strtotime($the_date_array[$i-1]["date"]), strtotime($the_date_array[$i]["date"]),0);										
					$this_principal = round($this_m33_date_diff_before*$this_year_wage,2);													
					$total_principal += $this_principal;									
					array_push($principals_array, $this_principal);
					
				}elseif($is_entry_date == 1 && $the_date_array[$i]["is_child"]){
					
					$date_from = $the_date_array[$i-1]["date"];
					$this_m33_date_diff_before = dateDiffTs(strtotime($the_date_array[$i-1]["date"]), strtotime($the_date_array[$i]["date"]),0) - 1;
					
					if($this_m33_date_diff_before <= 45
						
						//yoes 20210609 -- condition for 90 วัน
						|| ($this_m33_date_diff_before <= 90 && $the_date_array[$i-1]["date"] < "2021-04-01" && $the_year == 2021)
						
					
						){						
						//echo "ไม่คิดเงินต้นเพราะเป็นการรับแทนใน 45 วัน";										
					}else{						
						$this_principal = round($this_m33_date_diff_before*$this_year_wage,2);						
						$total_principal += $this_principal;										
						array_push($principals_array, $this_principal);
					}
					
				}elseif($the_date_array[$i]["is_last_day_of_year"] && $last_end_date){
							
					
					//yoes 20200813 -- special for last_day_of_year -> if there are เข้าแทนปีต่อไป => check if 45 days...
					//echo $the_date_array[$i+1]["date"];
					if($the_date_array[$i+1]["date"] && $the_date_array[$i+1]["date"] != "0000-00-00"){
						
						$the_date_diff = dateDiffTs(strtotime($last_end_date), strtotime($the_date_array[$i+1]["date"]),0);
						//echo $the_date_diff;
						
					}else{
						
						$the_date_diff = 9999;
						
					}
					
					if($the_date_diff <= 45){
						
						$date_from = $last_end_date;
						$this_m33_date_diff_before = $the_date_diff;
						$this_principal = 0;											
						$total_principal += $this_principal;									
						array_push($principals_array, $this_principal);

						
					}else{
							
						$date_from = $last_end_date;
						$this_m33_date_diff_before = dateDiffTs(strtotime($last_end_date), strtotime($the_date_array[$i]["date"]),0);
						$this_principal = round($this_m33_date_diff_before*$this_year_wage,2);											
						$total_principal += $this_principal;									
						array_push($principals_array, $this_principal);
					
					}
						
					
					
					//echo "oh hey - " . $the_date_array[$i]["date"] . " - " . $this_principal;
					
					
				}else{
					
					$this_principal = 0;
				}
				

                //yoes 20241127
                /*
                 *
                 * ปรับปรุงเงื่อนไขการคิดคำนวณเงิน มาตรา 35
                - การจัดจ้างเหมาช่วงงานหรือการจ้างเหมาบริการ ให้คำนวณตามมูลค่าสัญญา
                - การช่วยเหลืออื่นใด ให้คำนวณตามมูลค่าสัญญา

                 */
                if($fixed_principal){
                    $this_principal = $fixed_principal;
                }

				//calculate interests
				if($this_principal){
					
					//interest start-stop date
					//default to 1 april
					$interest_start_date = $year_interest_start_date;
					
					//if have someone that leave before this ...
					if($the_date_array[$i-1]["is_end_date"]){
						
						//yoes 20200612 --> interest start date is a Last-guy exit date+1 or 1 April 2020
						$interest_start_date = max($year_interest_start_date, date('Y-m-d', strtotime($the_date_array[$i-1]["le_row"][$assoc_end_date_column_name]. ' + 1 days') ));
						$interest_day_offset = -1;
					}
					
					$current_date = date('Y-m-d');
					$interest_end_date = $current_date;
					
					//echo "<br>ดอกเบี้ยคิดจากวันที่ " . formatDateThai($interest_start_date) . " ถึง ". formatDateThai($current_date);
					//$interest_days = max(dateDiffTs(strtotime($interest_start_date), strtotime($interest_end_date), 1)+$interest_day_offset,0);
					//$this_interest = round(($interest_days*(7.5/100/365)*$this_principal),2);
					//echo "<br>= ".$interest_days." วัน x 7.5/100/365 x " . $this_principal . " = ".$this_interest." บาท";
					
					//$total_interest += $this_interest;
					
					
					//yoes 20200611
					//if there principal then -> record this to DB
					
					
					
					$p_sql = " replace into $principal_table(
					
								p_lid
								, p_from
								, p_to
								, p_date_from
								, p_date_to
								, p_start_date								
								, p_amount
					
							)values(
							
								'".($lawful_row[LID]*1)."'
								, '".($last_le_id*1)."'
								, '".($the_date_array[$i][le_row][$assoc_pk_name]*1)."'
								, '".$date_from."'
								, '".$the_date_array[$i]["date"]."'
								
								, '".$interest_start_date."'
								
								, '".($this_principal*1)."'
							
							)
					
					";
					
					
					
					//echo "<br>".$p_sql;					
					mysql_query($p_sql);
					
				}
			
				
			
				
				if($total_principal != $previous_total){
					//echo $this_principal . " บาท";
				}					
				$previous_total = $total_principal;					
				if($total_interest != $previous_interest){
					//echo $this_interest . " บาท";
				}
				$previous_interest = $total_interest;
				
				//yoes 20200608 add "last leid"
				if($the_date_array[$i][le_row][$assoc_pk_name]){
					$last_le_id = $the_date_array[$i][le_row][$assoc_pk_name];
				}
				
				array_push($flow_detail_array,
				
					array(
						"date" => $the_date_array[$i]["date"]							
						, "le_id" => $the_date_array[$i][le_row][$assoc_pk_name]
						, "le_name" => $the_date_array[$i][le_row][$assoc_display_name_column_name]
						, "le_code" => $the_date_array[$i][le_row][$assoc_display_card_id]
						, "date_type" => $date_type
						, "date_diff" => $this_m33_date_diff_before
						, "interest_start_date" =>  $interest_start_date
						, "interest_end_date" => $interest_end_date
						, "interest_days" => $interest_days
						, "this_principal" => $this_principal
						, "this_interest" => $this_interest
						, "meta_amount" => $meta_amount
						, "last_le_id" => $last_le_id //just for reference
						
					)
				
				);
		
			} //if($the_date_array[$i]["date"] != "0000-00-00"){
		
		} //end loop  for($i=0;$i<count($the_date_array);$i++){
	
		
		
		$flow_result_array[details] = $flow_detail_array;
		$flow_result_array[total_principal] = $total_principal;
		$flow_result_array[total_interest] = $total_interest;
		
		return($flow_result_array);
		
		
	


} 	//end function

?>