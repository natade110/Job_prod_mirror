<?php
	
	$origin = $_SERVER['HTTP_ORIGIN'];
	$allowed_domains = array(
		"http://203.154.94.105"
		, "http://law.dep.go.th"
	);

	if (in_array($origin, $allowed_domains)) {
		header('Access-Control-Allow-Origin: ' . $origin);
	}
	
	
	
	include "db_connect.php";
	
	
	//yoes 20220304 - add ejob mode
	$the_mode = $_GET["the_mode"];
	if($the_mode == "ejob"){
		$the_mode = "ejob";
	}else{
		$the_mode = "";
	}
	
	//yoes 20220325
	$summary_only = $_GET["show_summary_only"];
	
	
	
	if(!$the_leid){
		$the_leid = $_GET["the_leid"]*1;
	}
		
	
	//$current_date = '2022-02-18';
	
	if(!$the_leid){		
		//no vars specify
		exit();		
	}
	
	
	//467034
	$p_from = $the_leid;
	
	if($the_mode == "ejob"){		
	
		
		//
		$es_table_name_suffix = "_company";
		$es_field_name_suffix = "-es";
		$es_principle_table_suffix = "_temp";
		//$p_from_meta = getFirstItem("select job_leid from lawful_employees$es_table_name_suffix where le_id = '$p_from'");			
		//echo "select job_leid from lawful_employees$es_table_name_suffix where le_id = '$p_from'";
		
		
	}else{
		//non-ejob
		//$p_from_meta = $p_from;		
	}
	
	//yoes 20220825 -- calculate this before showing?
	//$current_date = date("Y-m-d");
	//generate33PrincipalFromLID(($the_lid), "_temp", $the_mode);
	//generate33InterestsFromLID(($the_lid), "_temp", $current_date, $the_mode);
	
	$principal_sql = "
												
		select
			*
		from
			lawful_33_principals$es_principle_table_suffix
		where
			p_from = '".$p_from."'
			or
			(
				p_from = 0
				and
				p_to = '".$p_from."'
			)
	
	";
	
	//yoes 20230720
	//case temp ใช้รวมกันระหว่าง ejob +job แต่ไม่แยก lid
	if($the_leid == 72075){
		//echo $principal_sql; exit();
		$principal_sql = "
												
			select
				*
			from
				lawful_33_principals$es_principle_table_suffix
			where
				(
					p_from = '".$p_from."'
					or
					(
						p_from = 0
						and
						p_to = '".$p_from."'
					)
				)
				and
				p_lid = '2050595877'

		";
	}

    if($the_leid == 129459){

        //echo $principal_sql; exit();
        $principal_sql = "
												
			select
				*
			from
				lawful_33_principals$es_principle_table_suffix
			where
				(
					p_from = '".$p_from."'
					or
					(
						p_from = 0
						and
						p_to = '".$p_from."'
					)
				)
				and
				p_lid = '2050618021'

		";
    }
	
	//common vars
	$le_row = getFirstRow("select * from lawful_employees$es_table_name_suffix where le_id = '$the_leid'");
	$lawfulness_row = getFirstRow("select * from lawfulness$es_table_name_suffix where cid = '".$le_row["le_cid"]."' and year = '".$le_row["le_year"]."'");
	
	//echo "select * from lawful_employees$es_table_name_suffix where le_id = '$the_leid'";
	//print_r($lawfulness_row);
	$this_lid = $lawfulness_row["LID"];
	//echo "select * from lawful_employees_company where leid = '$the_leid'";
	//print_r($le_row);
	
	$this_lawful_year = $lawfulness_row["Year"];
	
	//echo $principal_sql;
	
	$principal_result = mysql_query($principal_sql);
	
	$interests_row = array();
	
	//$principal_row = getFirstRow($principal_sql);
	while($principal_row = mysql_fetch_array($principal_result)){
		
		//print_r($principal_row);
		
		
		
		if($principal_row && $this_lawful_year >= 2018){
			
			if($summary_only){
				echo "<div style='display: none;'>";
			}
			
			
			//yoes 20200724 for https://app.asana.com/0/794303922168293/1185797049999353
			$day_display_offset = 0;
			if($principal_row[p_from] && $principal_row[p_to]){
				$day_display_offset = -1;															
			}
			
			//echo "<br>---- beta ----";
			echo "<br><font color=orangered>ต้องจ่ายเงินแทน "
				. number_format(dateDiffTs(strtotime($principal_row[p_date_from]), strtotime($principal_row[p_date_to]), $day_display_offset),0) ." วัน "
				. number_format($principal_row[p_amount],2) ." บาท ";
			
			echo "</font>";	
			
			//yoes 20200624 -- total for each chain
			//$m33row_total_principal += $principal_row[p_amount];
			
			
			//yoes 20200618 try get interests function here...
			//echo $this_lid ." -- ". $principal_row[p_from] ." -- ".  $principal_row[p_to];
			//$interests_row = generateInterestsFromPrincipals($this_lid, $principal_row[p_from],  $principal_row[p_to]);
			$interests_row = generateInterestsFromPrincipals($this_lid, $principal_row[p_from],  $principal_row[p_to], "m33", "$es_principle_table_suffix", date("Y-m-d"), $the_mode);
			//echo "interests_row = generateInterestsFromPrincipals($this_lid, $principal_row[p_from],  $principal_row[p_to], m33, _temp, date(Y-m-d), ejob);";
			
			//print_r($interests_row);
			
			$interest_details = $interests_row[interest_details];
			
			//print_r($interest_details);
			
			$m33row_total_paid = 0;
			
			for($iii = 0; $iii < count($interest_details) ; $iii++){
				
				echo "<br>1. เงินต้นต้องชำระ ".number_format($interest_details[$iii][pre_pending_principal], 2);
				
				
				
				if($interest_details[$iii][last_loop_left_over_interest]){
					echo " ดอกเบี้ย " . number_format($interest_details[$iii][this_interest]-$interest_details[$iii][last_loop_left_over_interest], 2)
								."+<font color=purple>" . number_format($interest_details[$iii][last_loop_left_over_interest],2) . "</font>";
				}else{
					echo " ดอกเบี้ย " . number_format($interest_details[$iii][this_interest], 2);
				}
				
				if($interest_details[$iii][interest_days] > 0){
					echo "<br>ดอกเบี้ยคิดจากวันที่ " . formatDateThai($interest_details[$iii][interest_start_date], 0) . " ถึง " . formatDateThai($interest_details[$iii][interest_end_date], 0) . " (".$interest_details[$iii][interest_days]." วัน)";
					
					if($interest_details[$iii][pre_principal_to_calculate_interests] != $interest_details[$iii][pre_pending_principal]){
					
						echo "<font color=purple>";
						echo "<br>ดอกเบี้ยคิดจากเงินต้น " . number_format($interest_details[$iii][pre_principal_to_calculate_interests], 2) . " บาท";
						echo "<br>** เงินต้นคิดจนถึงวันที่ 31 ธค เนื่องจากเป็นการจ่ายเงินก่อนที่มีคนใหม่มาแทน";
						echo "</font>";
						
					}																
					
				}
				
				$the_this_receipt_sum_to_pay = 0;
				
				if($interest_details[$iii][pre_pending_principal]+$interest_details[$iii][this_interest] >= 0){
					echo "<br><b>รวมต้องชำระ " . number_format($interest_details[$iii][pre_pending_principal]+$interest_details[$iii][this_interest], 2) . " บาท</b>";
				}else{
					echo "<br><b>จ่ายเกิน " . (number_format($interest_details[$iii][pre_pending_principal]+$interest_details[$iii][this_interest], 2)) . " บาท</b>";
					
				}
				
				//print_r($interest_details[$iii]);
				if($interest_details[$iii][meta_value]){
					echo "<br><font color=blue>";
					echo "มีการจ่ายเงินวันที่ " . formatDateThai($interest_details[$iii][ReceiptDate], 0);
					echo " เล่มที่ ".$interest_details[$iii][BookReceiptNo]." เลขที่ ".$interest_details[$iii][ReceiptNo];
					
					echo " จำนวนเงิน " . number_format($interest_details[$iii][meta_value], 2) . " บาท";
																					
					if($interest_details[$iii][left_over_interest]){
						echo "<br><font color=purple>เหลือดอกเบี้ย ".number_format($interest_details[$iii][left_over_interest],2)." บาท</font>";
					}
					
					echo "</font>";
				}
				
				$m33row_total_paid += $interest_details[$iii][meta_value];
				
				
			}
			
			echo "
			<br>2. เงินต้นที่เหลือ ".number_format($interests_row[p_principal_after],2)." บาท ดอกเบี้ย ".number_format($interests_row[pending_interests],2)."";
			$the_this_receipt_sum_to_pay = 0;
			
			if($interests_row[p_principal_after]+$interests_row[pending_interests] >= 0){
				echo "<br><b>รวมต้องชำระ " . number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2) . " บาท</b>";
			}else{
				echo "<br><b>จ่ายเกิน " . (number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2)) . " บาท</b>";
				
			}
			
			
			if($summary_only){
			
				echo "</div>";
			
				if($interests_row[p_principal_after]+$interests_row[pending_interests] >= 0){
					echo "<br><b>รวมต้องชำระ " . number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2) . " บาท</b>";
				}else{
					echo "<br><b>จ่ายเกิน " . (number_format($interests_row[p_principal_after]+$interests_row[pending_interests], 2)) . " บาท</b>";
					
				}
				
				
			}
			
		}
		
		
		
	
	}