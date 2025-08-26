<?php

	include "db_connect.php";

	$the_year = $_GET[the_year]?$_GET[the_year]*1:date("Y");
	
	
	//$sql to get list of 33 that ไม่ครบปี
	
	$sub_33_sql = "
	
	
			select
				distinct(le_cid)
			from 
				(
					select
						le_cid
					from
						lawful_employees
					where
						le_year = $the_year
						and
						(
							le_start_date > '$the_year-01-01'
							or
							(
								le_end_date < '$the_year-12-31' 
								and 
								le_end_date != '0000-00-00'
							)
						)
						
						and
						le_id not in (
						
							select
								meta_leid
							from
								lawful_employees_meta
							where
								meta_for = 'child_of'
								and
								meta_value != 0
							
						)
						and
						le_id not in (
						
							select
								meta_value
							from
								lawful_employees_meta
							where
								meta_for = 'child_of'
								and
								meta_leid != 0
							
						)
					
					union
		
		
					select
						cc.le_cid
					from
						lawful_employees_meta aa
							join
								lawful_employees bb
								on
								aa.meta_leid = bb.le_id
							join
								lawful_employees cc
								on
								aa.meta_value = cc.le_id
					where
						
						bb.le_year = $the_year
						and					
						meta_for = 'child_of'
						and
						meta_value != 0
						and
						DATEDIFF(bb.le_start_date, cc.le_end_date)-1 > 45
						and
						DATEDIFF(bb.le_start_date, cc.le_end_date)-1 >= 0
				
				) a
	";
	
	$sub_35_sql = "
		
		select
			curator_lid
		from
			curator a
				join
					lawfulness b
					on
					a.curator_lid = b.lid
					and
					b.year = $the_year
		where
			(
				curator_start_date > '$the_year-01-01'
				or
				(
					curator_end_date < '$the_year-12-31' 
					and 
					curator_end_date != '0000-00-00'
				)
			)
			
			and
			curator_id not in (
			
				select
					meta_curator_id
				from
					curator_meta
				where
					meta_for = 'child_of'
				
			)
			
		
	
	";
	
	
	//get receipt that before april 1
	$valid_receipt_sql = "
		
		select
			lid
		from
			payment
		where
			PaymentDate > '$the_year-03-31 23:59:59'
		
		
	";
	
	
	$sql = "
	
		select
			a.cid
			, a.year
			, round(a.employees/100) as the_ratio
			, a.*
			, b.*
		from
			lawfulness a
				join
					company b
					on
					a.cid = b.cid
				
		where
		
			a.Hire_NumofEmp < round(a.employees/100)
			and
			a.year = $the_year
			and
			a.employees >=	100			
			and
			a.lawfulStatus = 1
			
			/* and	a.cid = 994654 */
			
			and ( 
				
				pay_status = 1
				and
				(
					hire_status = 1
					and
					conc_status = 0
				)
			
			)
			
			
			
			and
			a.lid in (
				
				$valid_receipt_sql
			
			)
			
			
			and
			
			(
				a.cid in (
				
					$sub_33_sql
					
				)
				/* or
				a.lid in (
				
					$sub_35_sql
				
				)  */
			
			)
	
	
	";
	
	echo $sql ;
	
	$result = mysql_query($sql);
	
?>

	<table border=1>
		<tr>
			<td>
				CID
			</td>
			<td>
				Year
			</td>
			<td>
				lawfulStatus
			</td>
			<td>
				companyNameThai
			</td>
			<td>
				receipt วันเดียวกันหมด?
			</td>
			
		</tr>
<?php
	
	while ($post_row = mysql_fetch_array($result)) {
?>

	<tr>
			<td>
				<?php echo $post_row["CID"];?>
			</td>
			<td>
				<?php echo $post_row["Year"];?>
			</td>
			<td>
				<?php echo $post_row["LawfulStatus"];?>
			</td>
			<td>
				<?php echo $post_row["CompanyNameThai"];?>
			</td>
			<td>
				
				<?php
					
					$receipt_sql = "
						
						
						select 
							count(*)
						from
							(
								select
									count(*)
								from
									payment
								where
									LID = '".$post_row[LID]."'
								group by
									PaymentDate
							) a
							
					
					";
					
					//echo $receipt_sql;
					$sub_receipt_count = getFirstItem($receipt_sql);
					
					if($sub_receipt_count != 1){ 
						echo "<font color=orangered>!!! ";
					}
					
						echo "<br>: " . $sub_receipt_count;
						
					if($sub_receipt_count != 1){ 
						echo "</font>";
					}
					
				
				?>
			
			</td>
			
		</tr>

<?php	
	}
?>
	</table>