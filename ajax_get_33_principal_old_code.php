<?php

	include "db_connect.php";
		
	$skip_html_head = 1;
	
	//include "header_html.php";

	//try to create principals for all 33 using old codes...
	
	//delete statment incase for debug
	$delete_statement = "
	
		delete from
			lawful_33_principals_old_code
		where
			p_lid = '204489'
	
	";
	
	//$moomin = mysql_query($delete_statement);/**/
	
	if($_GET[the_year]){
		$the_year  = $_GET[the_year]*1;
	}else{
		$the_year = "2019, 2020";
	}
	
	$m33_sql = "
	
		select
			*
		from
			lawful_employees a
				join
					lawfulness b
						on
						a.le_cid = b.cid
						and
						a.le_year = b.year
		where
			le_year in ( $the_year )
			and
			le_id not in (
			
				select
					p_from
				from
					lawful_33_principals_old_code
			
			)
			and
			le_id not in (
			
				select 
					meta_leid
				from 
					lawful_employees_meta 
				where 
					
					meta_for = 'is_extra_33'
					and
					meta_value != 0
			
			)
		limit 0,500
			
	
	";
	
	/*
	le_cid in (			
		84061			
	)
	*/
	//echo $m33_sql; exit();
	$m33_result = mysql_query($m33_sql);
	
	$replace_sql = "
		
			replace into lawful_33_principals_old_code(
			
				p_lid
				, p_from
				, p_amount
				, p_interests
				
			)values";
	
	$the_count = 0;
	
	while($m33_row = mysql_fetch_array($m33_result)){
	
		$the_count++;
		$arr = get3335DeductionByXIDArray($m33_row[le_id], "", 0, "m33");
		
		echo "<br>--- " . $arr[m34_to_pay_before_origin];
		print_r($arr);
		
		echo "($arr[interest_amount_before]+$arr[interest_amount_after])";
		
		if($the_count > 1){
			$replace_sql .= ",";
		}
			
		
		$replace_sql .= "
		
			(
			
				'".$m33_row[LID]."'
				, '".$m33_row[le_id]."'
				, '".($arr[m34_to_pay_before_origin]+$arr[m34_to_pay_after])."'
				, '".($arr[interest_amount_before]+$arr[interest_amount_after])."'
			
			)
		
		";
		//old-code interests is ... 
		//, '".($arr[interest_amount_before]+$arr[interest_amount_after])."'
		//, '".($arr[m34_total_interest])."'
		
		
		//print_r($arr);
		
		
	
	}
	
	$replace_sql .= ";";
	
	echo "<br>";
	echo $replace_sql;
	
	mysql_query($replace_sql);
	
	
	
	
?>
<?php if($the_count && !$moomin){ ?>
<script>
	location.reload();
</script>
<?php } ?>