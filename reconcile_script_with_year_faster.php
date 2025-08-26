<?php 

	include "db_connect.php";
	
	$sql = "
		select 
			cid
			, year 
			, lid
		from 
			company_to_reconcile_with_year 
		where 
			reconciled = 0 
		limit 
			0, 100
		";
	
	//$reconcile_row = array();
	
	//$reconcile_row = getFirstRow($sql);
	
	//print_r($reconcile_row);
	
	$reconcile_result = mysql_query($sql);
	
	while($reconcile_row = mysql_fetch_array($reconcile_result)){
	
		$cid_to_reconcile = $reconcile_row["cid"];
		$lid_to_reconcile = $reconcile_row["lid"];
		$year_to_reconcile = $reconcile_row["year"];
		
		$this_lid = $lid_to_reconcile;
		
		
		//yoes 20210415
		if(!getLidBetaStatus($this_lid)){
		
			generate33PrincipalFromLID($this_lid);
			//yoes 20200817 -- only sync payment on FIRST TIME
			syncPaymentMeta($this_lid);
			generate33InterestsFromLID($this_lid);
			generate35PrincipalFromLID($this_lid);
			syncPaymentMeta($this_lid, 0, "m35");
			generate35InterestsFromLID($this_lid);
			
			$_GET[beta_on] = 1;
			
			//update the flag
			$beta_sql = "
				replace into
					lawfulness_meta(
						meta_lid
						, meta_for
						, meta_value
					)values(
						'$this_lid'
						, 'is_beta_2020'
						, '1'
					
					)
			
			";
			
			mysql_query($beta_sql);
			
			$is_beta_mode = 1;
			
		}elseif(getLidBetaStatus($this_lid)){
			
			$is_beta_mode = 1;
			
			//init new lawfulness data according to new code...
			//generate new principal...
			generate33PrincipalFromLID($this_lid);
			//sync payment meta from old to new (if applicable)
			//yoes 20200817 -- only sync payment on FIRST TIME
			//syncPaymentMeta($this_lid);
			//run interests
			generate33InterestsFromLID($this_lid);
			//exit();
			
			//yoes 20200626 -- also do for m35
			generate35PrincipalFromLID($this_lid);
			//syncPaymentMeta($this_lid, 0, "m35");
			generate35InterestsFromLID($this_lid);
		}
		
		//echo "<br>cid to reconcile: " . $cid_to_reconcile . " - ";
		//echo "year to reconcile: " . $year_to_reconcile . " - ";
		//echo "<br>lid to reconcile: " . $lid_to_reconcile . " - ";
		//echo "cid: " . $cid_to_reconcile . " - ";
		
		resetLawfulnessByLID($lid_to_reconcile);
		resetLawfulnessByLID_old_law($lid_to_reconcile);
		//doLawfulnessFullLog(1, $lid_to_reconcile, "reconcile_script");
		
		mysql_query("update company_to_reconcile_with_year set reconciled = 1 where lid = '".$lid_to_reconcile."'");
		
	}


	if(!$cid_to_reconcile){echo "all company reconciled successfully"; exit();}


	

?><script>

	location.reload();

</script>