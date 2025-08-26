<?php

include "db_connect.php";

header ('Content-type: text/html; charset=utf-8');

$the_year = "2011";

if(isset($_GET["year"])){
	$the_year = $_GET["year"];
}




if($the_year >= 2013){

	$is_2013 = 1;
	$branch_codition =  " AND BranchCode < 1 ";

}

$the_year_to_use = formatYear($the_year);

$typecode_filter = " and CompanyTypeCode != '14'";
$typecode_filter2 = " and CompanyTypeCode = '14'";
$business_type = "สถานประกอบการ";

$typecode_filter .= " and CompanyTypeCode < 200";


?>

<?php


		$i = $_GET["filter"]*1;
		$data_index = $_GET["data"]*1;
	  
		$row_name_array = array(
		
			"รับคนพิการเข้าทำงานครบตามอัตราส่วน (ม.33)"
			,"ส่งเงินเข้ากองทุนแทนการรับคนพิการเข้าทำงาน (ม.34)"
			,"ให้สัมปทานฯ (ม.35)"
			,"รับคนพิการเข้าทำงานและส่งเงินเข้ากองทุนฯ (ม.33,34)"
			,"รับคนพิการเข้าทำงานและให้สัมปทานฯ (ม.33,35)"
			
			,"ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.34,35)"
			,"รับคนพิการเข้าทำงาน ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.33,34,35)"
			,"ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน"			
			,"ไม่ปฏิบัติตามกฎหมาย"
			,"ไม่เข้าข่ายจำนวนลูกจ้าง"
			
		);

	  	$lawful_filter_array = array(
		
			" and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '0' $branch_codition"
			," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '0' $branch_codition"
			," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '0' and Conc_status = '1' $branch_codition"
			," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '0' $branch_codition"			
			," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '1' $branch_codition"
						
			," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '1' $branch_codition"
			," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '1' $branch_codition"
			," and LawfulStatus = '2' $branch_codition"								
			," and (LawfulStatus = '0' or LawfulStatus is null) $branch_codition"
			," and LawfulStatus = '3' $branch_codition"	
			
			
		);
	  	
		//echo $data_index ;
		
		if($data_index == "33"){
		
		
				$the_sql = "select 
								
									company.CID, companyCode, companyNameThai, BranchCode, Province, Hire_NumofEmp
									
									,company.Employees as company_employees, lawfulness.Employees as lawful_employees
								
								from 
									lawfulness
									, company
									 $province_table
								where 
									company.CID = lawfulness.CID
									
									$typecode_filter
									
									$province_filter
									and Year = '$the_year'
									".$lawful_filter_array[$i]."
									
									$branch_codition
									$last_modified_sql
									
									and Hire_NumofEmp > 0
									
									order by companyNameThai asc
									
											";		
									
		
		}elseif($data_index == "351"){
		
			$the_sql = "
												
					select
					
						 company.CID, companyCode, companyNameThai, BranchCode, Province, Hire_NumofEmp
						
						 ,company.Employees as company_employees, lawfulness.Employees as lawful_employees
						
					from 
					
						curator 
						, company
						, lawfulness
					
					where 
					
						company.CID = lawfulness.CID
						and
						lawfulness.LID = curator_lid
						and
					
					curator_lid in
					(
					
						select 
							lid
						from 
							lawfulness
							, company
							$province_table
						where 
							company.CID = lawfulness.CID
							".$lawful_filter_array[$i]."
							$province_filter
							
							$typecode_filter
							
							and Year = '$the_year'
							
							$branch_codition
							
							$last_modified_sql
					
					)
					
					and 
					curator_parent = 0
					and
					curator_is_disable = 1
					
					group by
					
						company.CID, companyCode, companyNameThai, BranchCode, Province, Hire_NumofEmp
						
					
					order by companyNameThai asc
				
				";
			
		}elseif($data_index == "352"){
		
			$the_sql = "
												
					select
					
						 company.CID, companyCode, companyNameThai, BranchCode, Province, Hire_NumofEmp
								
					 	,company.Employees as company_employees, lawfulness.Employees as lawful_employees
					 
					from 
					
						curator 
						, company
						, lawfulness
					
					where 
					
						company.CID = lawfulness.CID
						and
						lawfulness.LID = curator_lid
						and
					
					curator_lid in
					(
					
						select 
							lid
						from 
							lawfulness
							, company
							$province_table
						where 
							company.CID = lawfulness.CID
							".$lawful_filter_array[$i]."
							$province_filter
							
							$typecode_filter
							
							and Year = '$the_year'
							
							$branch_codition
							
							$last_modified_sql
					
					)
					
					and 
					curator_parent = 0
					and
					curator_is_disable = 0
					
					group by
					
						company.CID, companyCode, companyNameThai, BranchCode, Province, Hire_NumofEmp
						
					
					order by companyNameThai asc
				
				";
			
		}elseif($data_index == "34"){

				$sub_table = "						
					(
					select 
						sum(receipt.Amount) the_sum
						, receipt.RID the_rid 
						, ReceiptYear
						, company.cid the_cid 
						
					from 
						receipt
						, payment
						, company
						, lawfulness
						  $province_table
					where
						receipt.RID = payment.RID
						and payment.LID = lawfulness.LID
						and lawfulness.CID = company.CID
						and ReceiptYear = '$the_year'
						AND is_payback =0
						and main_flag = 1
						$typecode_filter
						$province_filter
						$branch_codition
						
						$last_modified_sql
						
					group by
						company.cid
						, receipt.RID
					)
					";
				

				$the_sql = "						
					
					select 
								 company.CID, companyCode, companyNameThai, BranchCode, Province, Hire_NumofEmp
						
								,company.Employees as company_employees, lawfulness.Employees as lawful_employees
						
							from 
								lawfulness
								, $sub_table a
								, company
							where
								lawfulness.CID = a.the_cid
								and
								lawfulness.CID = company.cid
								and lawfulness.Year = a.ReceiptYear
								
								$last_modified_sql
								
							
								
						".$lawful_filter_array[$i]."
						
						
						group by
						
							company.CID, companyCode, companyNameThai, BranchCode, Province, Hire_NumofEmp
							
						
						order by companyNameThai asc
					
					";


	
		}elseif($data_index == "99"){
			
			
			$the_sql = "select 
								company.CID, companyCode, companyNameThai, BranchCode, Province, Hire_NumofEmp
								
								,company.Employees as company_employees, lawfulness.Employees as lawful_employees
								
							from 
								lawfulness
								, company
								$province_table
							where 
								company.CID = lawfulness.CID
								".$lawful_filter_array[$i]."
								$province_filter
								
								$typecode_filter
								
								and Year = '$the_year'
								
								$branch_codition
								
								$last_modified_sql
								
										";
			
			
		}
		
		//echo $the_sql;
		
?>


<div align="center">
  <strong>ข้อมูลสถานประกอบการที่มีข้อมูลมาตรา <?php echo $data_index; ?> แต่รายงานมาว่ามีการ<?php echo $row_name_array[$i];?>เท่านั้น - ปี <?php echo $the_year_to_use;?></strong> 
              <br>
</div>



<table border="1" align="center" cellpadding="5" cellspacing="0" style="font-size:14px;">
    	<tr>
        	<td>
            	#
            </td>
        	<td>
            	เลขทะเบียนนายจ้าง
            </td>
            <td>
            	สาขา
            </td>
            <td>
            	ชื่อสถานประกอบการ
            </td>
            <td>
            	จังหวัด
            </td>
            <td>จำนวนลูกจ้าง</td>
            <td align="center" style="text-align: center">ปรับปรุงข้อมูล<br />การปฏิบัติตามกฏหมาย</td>
            <!--
            <td>
            	คนพิการมาตรา 33
            </td>-->
        </tr>
    

<?php		
		$result = mysql_query($the_sql);
		
		while ($result_row = mysql_fetch_array($result)) {
			
			$row_count++;
?>

	<tr>
            <td>
            
            	<?php echo $row_count; ?>
            </td>
			<td>
            	<a href="organization.php?id=<?php echo $result_row["CID"];?>&all_tabs=1&year=<?php echo $the_year;?>&focus=lawful" target="_blank">
            		<?php echo $result_row["companyCode"];?>
                </a>
            </td>
            <td>
            	<?php echo $result_row["BranchCode"];?>
            </td>
            <td>
            	<?php echo $result_row["companyNameThai"];?>
            </td>
            <td>
            	<?php echo getFirstItem("select province_name from provinces where province_id = '".$result_row["Province"]. "'");?>
            </td>
            
            <td style="text-align:right;">
            
            
            <?php 
			
				$employees_to_show = default_value($result_row["lawful_employees"],$result_row["company_employees"]);
			
				echo $employees_to_show;
				
			?>
            
            </td>
            
            <td align="center" style="text-align: center">
            
            <a href="organization.php?id=<?php echo $result_row["CID"];?>&year=<?php echo $the_year;?>&auto_post=1"
           
	        target="autopost"
            
            >ปรับปรุงข้อมูล</a>
            
            
            
            </td>
            <!--
            <td>
            	<?php echo $result_row["Hire_NumofEmp"];?>
            </td>-->
            
	 </tr>


<?php			
		}		

?>

</table> 



    
    
    