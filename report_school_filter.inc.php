<?php 

//yoes 20160614 -- more conditions
if($_POST[company_type] == 1){	
	
	$school_filter = " and
						company.cid in (
							
							select
								meta_cid
							from
								company_meta
							where
								meta_for = 'is_school' and meta_value = 1
						
						)
					";	
	
	
	//for report 10
	$school_filter_alias = " and
						zzz.my_cid in (
							
							select
								meta_cid
							from
								company_meta
							where
								meta_for = 'is_school'	and meta_value = 1
						
						)
					";
					
	$school_filter_no_alias = " and
						cid in (
							
							select
								meta_cid
							from
								company_meta
							where
								meta_for = 'is_school'	 and meta_value = 1
						
						)
					";
	
	
	//for report 01
	$school_filter_34 = " and
						a.cid in (
							
							select
								meta_cid
							from
								company_meta
							where
								meta_for = 'is_school'	 and meta_value = 1
						
						)
					";
}

if($_POST[company_type] == 2){	
	
	$school_filter = " and
						company.cid not in (
							
							select
								meta_cid
							from
								company_meta
							where
								meta_for = 'is_school'	 and meta_value = 1
						
						)
					";	
	
	//for report 10
	$school_filter_alias = " and
						zzz.my_cid not in (
							
							select
								meta_cid
							from
								company_meta
							where
								meta_for = 'is_school'	 and meta_value = 1
						
						)
					";	
					
	$school_filter_no_alias = " and
						cid not in (
							
							select
								meta_cid
							from
								company_meta
							where
								meta_for = 'is_school'	and meta_value = 1
						
						)
					";	
					
	//for report 01
	$school_filter_34 = " and
						a.cid not in (
							
							select
								meta_cid
							from
								company_meta
							where
								meta_for = 'is_school'	and meta_value = 1
						
						)
					";	
}

?>