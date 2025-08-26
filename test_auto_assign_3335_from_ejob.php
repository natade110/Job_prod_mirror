<?php

	//yoes 20200720 -- sync ws codes with actual receipt bookno booknum
	include "db_connect.php";
	
	doLawfulnessFullLog(1, 2050580059, "test_auto_assign_3335_from_ejob.php");
	resetLawfulnessByLID(2050580059);
	
	
	/*$sql = "
		
		select
			*
		from
			lawful_33_principals
		where
			p_lid = '2050580059'
	
	";
	
	
	$law33_result = mysql_query($sql);
	
	while ($law33_row = mysql_fetch_array($law33_result)) {
		
		$use_amount = min($law33_row[p_amount]+$law33_row[p_interests], $the_amount);
		
		echo $law33_row[p_amount];
		echo "---".$law33_row[p_interests];
		
		echo "---".$the_amount;
		
		//$the_amount -= $use_amount;
		
		
	}
	
	exit();*/
	
	
	//yoes 20210611
	//add cron for the metas.
	//ขา รับ แล้ว จ่าย
	$sql = "
			select
				*
				, re.amount as the_amount
				, re.rid as the_rid
			from
				receipt re
					join
						payment pay
						on
						re.rid = pay.rid
			where
				-- NEPFundPaymentID like 'DUMMY_%'
				-- and
				re.paymentMethod = 'WS'
				and
				re.rid not in (
				
					select
						meta_rid
					from
						receipt_meta
				
				)
				and
				pay.lid in (
				
					select
						lid
					from					
						lawfulness
					where
						year in (2021, 2022)
						-- year in (2020)
				)
				
			order by
				re.RID desc

			-- limit 0,20
				
		";
				
	echo $sql; exit();
	
	
	$receipt_result = mysql_query($sql);
				
	while ($receipt_row = mysql_fetch_array($receipt_result)) {
		
		$the_rid = $receipt_row[the_rid];
		$the_amount = $receipt_row[the_amount];
		
		//see if have 33 rows ที่ต้องแทน
		$sql = "
		
			select
				*
			from
				lawful_33_principals
			where
				p_lid = '".$receipt_row[LID]."'
		
		";
		
		
		$law33_result = mysql_query($sql);
		
		while ($law33_row = mysql_fetch_array($law33_result)) {
			
			$use_amount = min($law33_row[p_amount]+$law33_row[p_interests], $the_amount);
			$the_amount -= $use_amount;
			
			
			if($use_amount > 0){
				
				$sql = "
				
					replace into receipt_meta(
						
						meta_rid
						, meta_for
						, meta_value				
					
					)values(
					
						'$the_rid'
						, '".($law33_row[p_lid].$law33_row[p_from].$law33_row[p_to])."'
						, '".($use_amount)."'
					
					)
				
				";			
				
				
				//mysql_query($sql);
				
				
				//echo "<br>".$sql; 
				
				$company_name_year_row = getFirstRow("
				
					select
						*
					from
						lawfulness law
							join
								company c
								on
								law.cid = c.cid
					where
						law.lid = '".$law33_row[p_lid]."'
					
						
				
				");
				
				echo "<br>".$company_name_year_row["CID"]."/".$company_name_year_row["CompanyCode"]."/".$company_name_year_row["CompanyNameThai"]."/".$company_name_year_row["Year"];
				
				/**/
				
				//		
				
			}
			
		}
		
		
		
		//see if have 35 rows ที่ต้องแทน
		$sql = "
		
			select
				*
			from
				lawful_35_principals
			where
				p_lid = '".$receipt_row[LID]."'
		
		";
		
		
		$law35_result = mysql_query($sql);
		
		while ($law35_row = mysql_fetch_array($law35_result)) {
			
			$use_amount = min($law35_row[p_amount]+$law35_row[p_interests], $the_amount);
			$the_amount -= $use_amount;
			
			
			if($use_amount > 0){
				
				$sql = "
				
					replace into receipt_meta(
						
						meta_rid
						, meta_for
						, meta_value				
					
					)values(
					
						'$the_rid'
						, 'c".($law35_row[p_lid].$law35_row[p_from].$law35_row[p_to])."'
						, '".($use_amount)."'
					
					)
				
				";			
				
				//mysql_query($sql);	
				
				//echo "<br>".$sql; 
				
				
				$company_name_year_row = getFirstRow("
				
					select
						*
					from
						lawfulness law
							join
								company c
								on
								law.cid = c.cid
					where
						law.lid = '".$law35_row[p_lid]."'
					
						
				
				");
				
				echo "<br>".$company_name_year_row["CID"]."/".$company_name_year_row["CompanyCode"]."/".$company_name_year_row["CompanyNameThai"]."/".$company_name_year_row["Year"];
				/**/
				//	
				
			}
			
		}
		
	}
	
	exit();
	