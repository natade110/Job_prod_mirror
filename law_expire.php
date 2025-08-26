<?php

$condition_sql = " and b.LawfulStatus in (0,2)";

$main_sql = "
			select
			    distinct
				b.Hire_NumofEmp
				, b.LID
				, b.Year
				, a.CompanyCode
				, a.CompanyTypeCode
				, ct.CompanyTypeName
				, a.CompanyNameThai
				, a.Province
				, c.province_id
				, c.province_name
				, a.LawStatus
				, b.Employees as company_employees
				, a.CID as the_company_cid
				, b.LID as the_lid
				, a.Address1
				, a.Moo
				, a.Soi
				, a.Road
				, a.Subdistrict
				, a.District
				, a.Zip
                , b.LawfulStatus
                , a.Status
				, COALESCE(curator_user.curator_user_count, 0) as curator_user_count
				, COALESCE(curator_usee.curator_usee_count, 0) as curator_usee_count
				, paids.paid_amount as paid_amount
				, COALESCE(paybacks.payback_amount, 0) as payback_amount
				$ex1 
				$ex2 
				$ex3
				
			from company a
			inner join lawfulness b on a.CID = b.CID
						
			left join provinces c on a.Province = c.province_id
			left join companytype ct on a.CompanyTypeCode = ct.CompanyTypeCode
			left join (
				select curator_lid, count(*) curator_user_count
				from curator
				where curator_parent = 0 and curator_is_disable = 0
				group by curator_lid
			) curator_user on b.LID = curator_user.curator_lid
			left join (
				select curator_lid, count(*) curator_usee_count
				from curator
				where curator_parent = 0 and curator_is_disable = 1
				group by curator_lid
			) curator_usee on b.LID = curator_usee.curator_lid
			left join (
				select payment.LID, sum(receipt.Amount) paid_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 0
				group by payment.LID
			) paids ON b.LID = paids.LID
			left join (
				select payment.LID, sum(receipt.Amount) payback_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 1
				group by payment.LID
			) paybacks ON b.LID = paybacks.LID 
			$courted_table_sql
				
			where 1=1
				
				$courted_meta_sql
				
				$year_selected
				
				$showAll
				
				$expire2Year_condition
			
				$expire1Year_condition
			
				$expire8Month_condition
			
				$province_filter
				
				$condition_sql
				
				$typecode_filter
				
				
			order by
				Year asc
				
			";


//dashboard sent back
$main_sql_sent_back = "
			select
			    distinct
				b.LID
				, a.CID
				, b.Year
				, a.CompanyNameThai
				

				
			from company a
			inner join lawfulness b on a.CID = b.CID
						
			left join provinces c on a.Province = c.province_id
			left join companytype ct on a.CompanyTypeCode = ct.CompanyTypeCode
			left join (
				select curator_lid, count(*) curator_user_count
				from curator
				where curator_parent = 0 and curator_is_disable = 0
				group by curator_lid
			) curator_user on b.LID = curator_user.curator_lid
			left join (
				select curator_lid, count(*) curator_usee_count
				from curator
				where curator_parent = 0 and curator_is_disable = 1
				group by curator_lid
			) curator_usee on b.LID = curator_usee.curator_lid
			left join (
				select payment.LID, sum(receipt.Amount) paid_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 0
				group by payment.LID
			) paids ON b.LID = paids.LID
			left join (
				select payment.LID, sum(receipt.Amount) payback_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 1
				group by payment.LID
			) paybacks ON b.LID = paybacks.LID 
			$courted_table_sql
				
			where 1=1
				
				$courted_meta_sql
				
				$year_selected
				
				$showAll
				
				$expire2Year_condition
			
				$expire1Year_condition
			
				$expire8Month_condition
			
				$province_filter
				
				$condition_sql
				
				$typecode_filter
				
				
			order by
				Year asc
			";
			
			
//report 427
$main_sql1_1 = "
			select
			    distinct
				b.Hire_NumofEmp
				, b.LID
				, b.Year
				, a.CompanyCode
				, a.CompanyTypeCode
				, ct.CompanyTypeName
				, a.CompanyNameThai
				, a.Province
				, c.province_id
				, c.province_name
				, a.LawStatus
				, b.Employees as company_employees
				, a.CID as the_company_cid
				, b.LID as the_lid
				, a.Address1
				, a.Moo
				, a.Soi
				, a.Road
				, a.Subdistrict
				, a.District
				, a.Zip
                , b.LawfulStatus
                , a.Status
				, COALESCE(curator_user.curator_user_count, 0) as curator_user_count
				, COALESCE(curator_usee.curator_usee_count, 0) as curator_usee_count
				, paids.paid_amount as paid_amount
				, COALESCE(paybacks.payback_amount, 0) as payback_amount
				$ex1 
				$ex2 
				$ex3
				
			from company a
			inner join lawfulness b on a.CID = b.CID
						
			left join provinces c on a.Province = c.province_id
			left join companytype ct on a.CompanyTypeCode = ct.CompanyTypeCode
			left join (
				select curator_lid, count(*) curator_user_count
				from curator
				where curator_parent = 0 and curator_is_disable = 0
				group by curator_lid
			) curator_user on b.LID = curator_user.curator_lid
			left join (
				select curator_lid, count(*) curator_usee_count
				from curator
				where curator_parent = 0 and curator_is_disable = 1
				group by curator_lid
			) curator_usee on b.LID = curator_usee.curator_lid
			left join (
				select payment.LID, sum(receipt.Amount) paid_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 0
				group by payment.LID
			) paids ON b.LID = paids.LID
			left join (
				select payment.LID, sum(receipt.Amount) payback_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 1
				group by payment.LID
			) paybacks ON b.LID = paybacks.LID 
			$courted_table_sql
				
			where 1=1
				
				$courted_meta_sql
				
				$year_selected
				
				$showAll
				
				$expire2Year_condition
			
				$expire1Year_condition
			
				$expire8Month_condition
			
				$province_filter
				
				$condition_sql
				
				$typecode_filter
				
				
			order by
				Year desc
			";


if($expire2Year && $expire1Year && $expire8Month){
	
	$showAll2 = "AND b.Year in ($expire2Year,$expire1Year,$expire8Month)";
	
}elseif($showDash == 1){
	
	$showAll2 = "AND b.Year in ($expire2Year,$expire1Year,$expire8Month,$year_selected)";

}elseif($year_selected){
	//report_425.php	
	$showAll2 = "AND b.Year = $year_selected";
}


$main_sql2 = "
			select
			    distinct
				b.Hire_NumofEmp
				, b.LID
				, b.Year
				, a.CompanyCode
				, a.CompanyTypeCode
				, ct.CompanyTypeName
				, a.CompanyNameThai
				, a.Province
				, c.province_id
				, c.province_name
				, a.LawStatus
				, b.Employees as company_employees
				, a.CID as the_company_cid
				, b.LID as the_lid
				, a.Address1
				, a.Moo
				, a.Soi
				, a.Road
				, a.Subdistrict
				, a.District
				, a.Zip
                , b.LawfulStatus
                , a.Status
				, COALESCE(curator_user.curator_user_count, 0) as curator_user_count
				, COALESCE(curator_usee.curator_usee_count, 0) as curator_usee_count
				, paids.paid_amount as paid_amount
				, COALESCE(paybacks.payback_amount, 0) as payback_amount
				,(case when b.year = '$expire2Year' then '2 ปี'
				       when b.year = '$expire1Year' then '1 ปี'
					   when b.year = '$expire8Month' then '8 เดือน'
						  else 0 end) expireYear

				
			from company a
			inner join lawfulness b on a.CID = b.CID
						
			left join provinces c on a.Province = c.province_id
			left join companytype ct on a.CompanyTypeCode = ct.CompanyTypeCode
			left join (
				select curator_lid, count(*) curator_user_count
				from curator
				where curator_parent = 0 and curator_is_disable = 0
				group by curator_lid
			) curator_user on b.LID = curator_user.curator_lid
			left join (
				select curator_lid, count(*) curator_usee_count
				from curator
				where curator_parent = 0 and curator_is_disable = 1
				group by curator_lid
			) curator_usee on b.LID = curator_usee.curator_lid
			left join (
				select payment.LID, sum(receipt.Amount) paid_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 0
				group by payment.LID
			) paids ON b.LID = paids.LID
			left join (
				select payment.LID, sum(receipt.Amount) payback_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 1
				group by payment.LID
			) paybacks ON b.LID = paybacks.LID 
			left join lawfulness_meta t on b.lid = t.meta_lid
				
			where 1=1
				and t.meta_lid not in (select tt.meta_lid from lawfulness_meta tt where tt.meta_lid = b.lid and tt.meta_for = 'courted_flag')
				
				$courted_meta_sql2
				
				$showAll2
				
				$expire2Year_condition
			
				$expire1Year_condition
			
				$expire8Month_condition
			
				$province_filter
				
				$condition_sql
				
				$typecode_filter
				
				and
				b.LawfulStatus in (0,2)
				
			order by
				Year asc
			
			";
			
		//bank 20230207 remove select top 50
		//report_425.php	
			$main_sql3 = "
			select
			    distinct
				b.Hire_NumofEmp
				, b.LID
				, b.Year
				, a.CompanyCode
				, a.CompanyTypeCode
				, ct.CompanyTypeName
				, a.CompanyNameThai
				, a.Province
				, c.province_id
				, c.province_name
				, a.LawStatus
				, b.Employees as company_employees
				, a.CID as the_company_cid
				, b.LID as the_lid
				, a.Address1
				, a.Moo
				, a.Soi
				, a.Road
				, a.Subdistrict
				, a.District
				, a.Zip
                , b.LawfulStatus
                , a.Status
				, COALESCE(curator_user.curator_user_count, 0) as curator_user_count
				, COALESCE(curator_usee.curator_usee_count, 0) as curator_usee_count
				, paids.paid_amount as paid_amount
				, COALESCE(paybacks.payback_amount, 0) as payback_amount
				,(case when b.year = '$expire2Year' then '2 ปี'
				       when b.year = '$expire1Year' then '1 ปี'
					   when b.year = '$expire8Month' then '8 เดือน'
						  else 0 end) expireYear
	
						  

				
			from company a
			inner join lawfulness b on a.CID = b.CID
						
			left join provinces c on a.Province = c.province_id
			left join companytype ct on a.CompanyTypeCode = ct.CompanyTypeCode
			left join (
				select curator_lid, count(*) curator_user_count
				from curator
				where curator_parent = 0 and curator_is_disable = 0
				group by curator_lid
			) curator_user on b.LID = curator_user.curator_lid
			left join (
				select curator_lid, count(*) curator_usee_count
				from curator
				where curator_parent = 0 and curator_is_disable = 1
				group by curator_lid
			) curator_usee on b.LID = curator_usee.curator_lid
			left join (
				select payment.LID, sum(receipt.Amount) paid_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 0
				group by payment.LID
			) paids ON b.LID = paids.LID
			left join (
				select payment.LID, sum(receipt.Amount) payback_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 1
				group by payment.LID
			) paybacks ON b.LID = paybacks.LID 
			left join lawfulness_meta t on b.lid = t.meta_lid
				
			where 1=1
				and t.meta_lid in (select tt.meta_lid from lawfulness_meta tt where tt.meta_lid = b.lid and tt.meta_for = 'courted_flag')
				
				$courted_meta_sql2
				
				$showAll2
				
				$expire2Year_condition
			
				$expire1Year_condition
			
				$expire8Month_condition
			
				$province_filter
				
				$condition_sql
				
				$typecode_filter
				
				
			order by
				Year asc
			";
			
			
		//report_426.php	
			$main_sql4 = "
			select
			    distinct
				b.Hire_NumofEmp
				, b.LID
				, b.Year
				, a.CompanyCode
				, a.CompanyTypeCode
				, ct.CompanyTypeName
				, a.CompanyNameThai
				, a.Province
				, c.province_id
				, c.province_name
				, a.LawStatus
				, b.Employees as company_employees
				, a.CID as the_company_cid
				, b.LID as the_lid
				, a.Address1
				, a.Moo
				, a.Soi
				, a.Road
				, a.Subdistrict
				, a.District
				, a.Zip
                , b.LawfulStatus
                , a.Status
				, COALESCE(curator_user.curator_user_count, 0) as curator_user_count
				, COALESCE(curator_usee.curator_usee_count, 0) as curator_usee_count
				, paids.paid_amount as paid_amount
				, COALESCE(paybacks.payback_amount, 0) as payback_amount
				,(case when b.year = '$expire2Year' then '2 ปี'
				       when b.year = '$expire1Year' then '1 ปี'
					   when b.year = '$expire8Month' then '8 เดือน'
						  else 0 end) expireYear
	
						  

				
			from company a
			inner join lawfulness b on a.CID = b.CID
						
			left join provinces c on a.Province = c.province_id
			left join companytype ct on a.CompanyTypeCode = ct.CompanyTypeCode
			left join (
				select curator_lid, count(*) curator_user_count
				from curator
				where curator_parent = 0 and curator_is_disable = 0
				group by curator_lid
			) curator_user on b.LID = curator_user.curator_lid
			left join (
				select curator_lid, count(*) curator_usee_count
				from curator
				where curator_parent = 0 and curator_is_disable = 1
				group by curator_lid
			) curator_usee on b.LID = curator_usee.curator_lid
			left join (
				select payment.LID, sum(receipt.Amount) paid_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 0
				group by payment.LID
			) paids ON b.LID = paids.LID
			left join (
				select payment.LID, sum(receipt.Amount) payback_amount
				from payment
				inner join receipt on receipt.RID = payment.RID
				where 
					receipt.is_payback = 1
				group by payment.LID
			) paybacks ON b.LID = paybacks.LID 
			left join lawfulness_meta t on b.lid = t.meta_lid
				
			where 1=1
				and t.meta_lid in (select tt.meta_lid from lawfulness_meta tt where tt.meta_lid = b.lid and tt.meta_for = 'courted_flag')
				
				$courted_meta_sql2
				
				$showAll2
				
				$expire2Year_condition
			
				$expire1Year_condition
			
				$expire8Month_condition
			
				$province_filter
				
				$condition_sql
				
				$typecode_filter
				
				
			order by
				Year asc
			
			";			