<?php

	//yoes 20200720 -- sync ws codes with actual receipt bookno booknum
	include "db_connect.php";
		
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
				NEPFundPaymentID like 'DUMMY_%'
				and
				re.paymentMethod = 'WS'
				and
				re.rid not in (
				
					select
						meta_rid
					from
						receipt_meta
				
				)
				
			order by
				re.RID desc

			limit
				0,20
				
		";
				
	//echo $sql; exit();
	
	
	$receipt_result = mysql_query($sql);
				
	while ($receipt_row = mysql_fetch_array($receipt_result)) {
		
		$the_rid = $receipt_row[the_rid];
		$the_amount = $receipt_row[the_amount];
		
		//yoes 20220526 - do reset lawfulness to get current interests
		doLawfulnessFullLog(1, $receipt_row[LID], "ajax_sync_ws_booknum.php - lawful33-35");
		resetLawfulnessByLID($receipt_row[LID]);
		
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
				
				mysql_query($sql);		
				
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
				
				mysql_query($sql);		
				
			}
			
		}
		
		
		//yoes 20220527 - do reset lawfulness อีกครั้งหลังจากแทนใบแล้ว
		doLawfulnessFullLog(1, $receipt_row[LID], "ajax_sync_ws_booknum.php - lawful33-35-2");
		resetLawfulnessByLID($receipt_row[LID]);
		
	}
	
	
	//yoes 20210713
	//add clean status ไม่เข้าข่าย
	//https://app.asana.com/0/794303922168293/1200352246176344
	$sql = "
	
		update
			lawfulness a	
				join
					company b
					on
					a.cid = b.cid
					and
					b.CompanyTypeCode < 200
					and
					b.CompanyTypeCode != 14
					
		set
			LawfulStatus = 3		
			
		where
			LawfulStatus in (0,1,2)
			and
			a.Employees < 100
			and
			year >= 2012
	
	";
	
	//echo $sql; exit();
	
	mysql_query($sql);
	
	
	//yoes 20210525
	//add ajax to clean WS payment after 12.00
	
	
	//yoes 20210422
	//---- add new cron for syncing payment ws status
	
	$sql = "
	
		SELECT 
			
			a.rid
			, a.paymentDate
			, b.lid
			
		FROM 	
			payment a 
				join 
					lawfulness b
						on
							a.LID = b.LID
			
				join
					receipt d
						on
						a.rid = d.rid
			
			join company c
				on b.cid = c.cid




			WHERE 
				a.paymentMethod = 'WS'
				and
				hour(d.ReceiptDate) >= 12
				and
				a.paymentDate > '2021-03-31'
	
	";
	
	$lid_result = mysql_query($sql);
	
	while ($lid_row = mysql_fetch_array($lid_result)) {
		
		
		//print_r($lid_row); 
		
		$sub_sql = "
		
			update
				receipt
			set
				ReceiptDate = '" . substr($lid_row["paymentDate"],0,10) . " 00:00:01'
			where
				rid = '".$lid_row["rid"]."'
		
		";
		
		mysql_query($sub_sql) or die(mysql_error());
		
		doLawfulnessFullLog(1, $lid_row["lid"], "ajax_sync_ws_booknum.php - syncing payment ws status");
		resetLawfulnessByLID($lid_row["lid"]);
		//echo "<br>$sub_sql";
		//echo "<br>- ".$lid_row["rid"] . " synced";
		
	}
	
	//exit();
	
	
	$sql = "
	
		select
			law.lid
		from
			receipt re
				join
					payment pa
					on
					re.rid = pa.rid
				join
					lawfulness law
					on
					law.lid = pa.lid
				join
					lawfulness_company lawc
					on
					lawc.cid = law.cid
					and
					lawc.year = law.year
		where
			re.paymentMethod = 'WS'		
			and
			NepfundPaymentId like 'DUMMY_%'			
			and
			lawc.lawful_submitted != 2
	
	";
	
	$lid_result = mysql_query($sql);
	
	while ($lid_row = mysql_fetch_array($lid_result)) {
		
		doLawfulnessFullLog(1, $lid_row["lid"], "ajax_sync_ws_booknum.php - syncing payment ws status - 2");
		resetLawfulnessByLID($lid_row["lid"]);
		//echo "<br>- ".$lid_row["lid"] . " synced";
		
	}
	
	//exit();
	
	
	//-----
	
		
	$sql = "
			select
				*
			from
				receipt
			where
				NEPFundPaymentID like 'DUMMY_%'
			order by
				RID desc

			limit
				0,20 ";
				
	$receipt_result = mysql_query($sql);
				
	while ($receipt_row = mysql_fetch_array($receipt_result)) {
		
		//print_r($receipt_row);
		
		
		$fund_sql = "
		
			select
				*
			from
				nepfund_import_log_detail
			where
				RawText like '%".$receipt_row[BookReceiptNo].$receipt_row[ReceiptNo]."%'
				
			limit
				0,1
		
		";
		
		
		$fund_row = getFirstRow($fund_sql);
		
		//print_r($fund_row);
		
		$fund_text = $fund_row[RawText];
		
		
		if($fund_text){
		
		
			$fund_payment_id = trim(substr($fund_text, 35, 7));
			$fund_booknum = trim(substr($fund_text, 55, 25));
			$fund_bookno = trim(substr($fund_text, 80, 25));
			
			/**/
			echo "<br>---rid: " . $receipt_row[RID];
			echo "<br>".$fund_text;
			echo "<br>".$fund_payment_id;
			echo "<br>".$fund_booknum;
			echo "<br>".$fund_bookno;
			/*exit();*/
			
			
			//preparing update
			$update_sql = "
			
				update
					bill_payment
				set
					NEPFundPaymentID = '".doCleanInput($fund_payment_id)."'
				where
					NEPFundPaymentID = '".$receipt_row[NEPFundPaymentID]."'
			
			";
			
			//echo "<br>".$update_sql; //exit();
			
			mysql_query($update_sql);
			
			//preparing update
			$update_sql = "
			
				update
					receipt
				set
					BookReceiptNo = '".doCleanInput($fund_booknum)."'
					, ReceiptNo = '".doCleanInput($fund_bookno)."'
					, NEPFundPaymentID = '".doCleanInput($fund_payment_id)."'
				where
					RID = '".$receipt_row[RID]."'
			
			";
			
			//echo "<br>".$update_sql;
			
			mysql_query($update_sql);
		
		}
		
		
	}						



	//yoes 20220719
	//add cron for delete การแทนใบเสร็จที่ ม33/35 หายไปแล้ว
	// https://mgsolution.monday.com/boards/1515748187/pulses/2920995416
	
	
	//first -> keep log
	$sql = "
		
		insert into receipt_meta_full_log(
			meta_rid
			, meta_for
			, meta_value
			, log_datetime
			, log_by
		
		)		
		select
			rem.meta_rid
			, rem.meta_for
			, rem.meta_value
			, now()
			, 1
			
		FROM 
			receipt_meta rem
				join
					receipt re
					on
					rem.meta_rid = re.rid
				join
					payment pay
					on
					pay.rid = re.rid
				join
					lawfulness law
					on
					law.lid = pay.lid
		where
			meta_rid < 100000
			and
			meta_rid > 67706
			and
			rem.meta_for not in (
			
				select
					concat(p_lid, p_from, p_to)
					
				from
					lawful_33_principals l33
			
			)
			and
			rem.meta_for not in (
			
				select
					concat('c',p_lid, p_from, p_to)
					
				from
					lawful_35_principals l35
			
			)
	
	";
	
	
	//mysql_query($sql);
	
	
	$sql = "
		
		Delete
			rem
		FROM 
			receipt_meta rem
				join
					receipt re
					on
					rem.meta_rid = re.rid
				join
					payment pay
					on
					pay.rid = re.rid
				join
					lawfulness law
					on
					law.lid = pay.lid
		where
			meta_rid < 100000
			and
			meta_rid > 67706
			and
			rem.meta_for not in (
			
				select
					concat(p_lid, p_from, p_to)
					
				from
					lawful_33_principals l33
			
			)
			and
			rem.meta_for not in (
			
				select
					concat('c',p_lid, p_from, p_to)
					
				from
					lawful_35_principals l35
			
			)
		
	";
	
	//mysql_query($sql);	

	//echo "s";

?>