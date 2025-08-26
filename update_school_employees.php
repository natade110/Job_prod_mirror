<?php

	include "db_connect.php";
	
	//echo getHireNumOfEmpFromLid(2050535448);
	
	
	$sql = "
	
		select
			*
		from
			z_school_temp
		where
			school_teacher + school_employees >= 100
			
			
	";
	
	
	$org_result = mysql_query($sql);
					
	//total records 
	$total_records = 0;

	while ($post_row = mysql_fetch_array($org_result)) {
		
		/*echo "<br>analysing cid " . $post_row[cid] ;
		
		if($post_row[hire_numofemp] != getHireNumOfEmpFromLid($post_row[lid])){
			
			
			echo "<br>cid " . $post_row[cid] . " not matched";
			
		}*/
		
		/*$the_row = getFirstRow("select
				*
			from
				company a
					join
						lawfulness b
						on
						a.cid = b.cid
						and
						b.year = 2024
				where
					 
					a.BranchCode <= 0
					and
					a.companyNameThai = '".$post_row[school_name]."'; ");*/
					
		$the_cid = getFirstItem("select cid from company where companyNameThai = '".$post_row[school_name]."' and branchCode < 1; ");
		
		echo "<br><font color=green>$the_cid</font> . ";
		if(!$the_cid){
				echo "<font color=red>not found in db</font>";
		}
		
		if($the_cid){
		
			$the_lid = getFirstItem("select lid from lawfulness where cid = '$the_cid' and year = 2024; ");
		}
		
		echo " . <font color=green>$the_lid</font> . ";
		
		$sql = " replace into lawfulness_meta values('$the_lid','school_teachers', '".$post_row[school_teacher]."' )";
		mysql_query($sql);
		//echo " <br>$sql ";
		$sql = " replace into lawfulness_meta values('$the_lid','school_employees', '".$post_row[school_employees]."' )";
		mysql_query($sql);
		//echo " <br>$sql ";
		$sql = " update lawfulness set Employees = '".($post_row[school_teacher]+$post_row[school_employees])."' where lid = '$the_lid';";
		mysql_query($sql);		
		//echo " <br>$sql ";
		
		
		resetLawfulnessByLID($the_lid);
		
		print_r($post_row);
		
	}
	
	
?><br>--end [process--