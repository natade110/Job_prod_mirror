<?php

include "db_connect.php";

include("Charts/Includes/FusionCharts.php");

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_2.xls");
	
}elseif($_POST["report_format"] == "words"){
	
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=nep_hire_report.doc");	

}elseif($_POST["report_format"] == "pdf"){
	
	$is_pdf = 1;
	//header("location: create_pdf_2.php");

}else{

	$is_html = "1";
	header ('Content-type: text/html; charset=utf-8');
	
	$chk_details = $_POST["chk_details"];
	
}

$the_year = "2011";

if(isset($_POST["ddl_year"])){
	$the_year = $_POST["ddl_year"];
}

if($the_year >= 2013){

	$is_2013 = 1;
	//year > 2013 => only concern main branch
	$branch_codition =  " AND BranchCode < 1 ";

}

$the_year_to_use = formatYear($the_year);

$province_text = "ทั่วประเทศ";
$province_filter = "";		
if(isset($_POST["Province"]) && $_POST["Province"] != "" && $_POST["rad_area"] == "province"){
	$province_filter = " and company.Province = '".$_POST["Province"]."'";
	$province_detailed_34_filter = " and a.Province = '".$_POST["Province"]."'"; //yoes 20160125
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}

//yoes 20150604
//extra conditions for ดินแดง
//$province_filter .= " and District LIKE '%ดินแดง%'";


if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	$province_table = ", provinces";
	$province_filter = " and company.Province = provinces.province_id and provinces.section_id = '".$_POST["Section"]."'";
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}

//if($_POST["CompanyTypeCode"] == "14"){
	
//	$typecode_filter = " and CompanyTypeCode = '14'";
//	$business_type = "หน่วยงานภาครัฐ";
		
//}else{
	$typecode_filter = " and CompanyTypeCode != '14'";
	$typecode_filter2 = " and CompanyTypeCode = '14'";
	$business_type = "สถานประกอบการ";
//}


$typecode_filter .= " and CompanyTypeCode < 200";




////
//yoes 20130820 - add last modify date/time for lawfulness
if($_POST["date_from_year"] > 0 && $_POST["date_from_month"] > 0 && $_POST["date_from_day"] > 0){

	$the_mod_year = $_POST["date_from_year"];
	$the_month = $_POST["date_from_month"];
	$the_day = $_POST["date_from_day"];
	
	$filter_from = " and LastModifiedDateTime >= '$the_mod_year-$the_month-$the_day 00:00:01'";
}

if($_POST["date_to_year"] > 0 && $_POST["date_to_month"] > 0 && $_POST["date_to_day"] > 0){

	$the_mod_year = $_POST["date_to_year"];
	$the_month = $_POST["date_to_month"];
	$the_day = $_POST["date_to_day"];
	
	$filter_to = " and LastModifiedDateTime <= '$the_mod_year-$the_month-$the_day 23:59:59'";
}


if($_POST["chk_from"] && ($filter_from || $filter_to)){

	$last_modified_sql = "
	
			and
			company.CID in (
			
				select mod_cid from modify_history where mod_type = 1
				
				$filter_from
				$filter_to			
			)	
			";	
}

//yoes 20160119 -- variables
$the_limit = getThisYearRatio($the_year);

$half_limit = $the_limit/2;

//catch-all wage
$the_wage = getThisYearWage($the_year);
//echo "the wage: ".$the_wage . " : ";
$the_cost_per_person = $the_wage*365;

$year_date = 365;

?>


<div align="center">
  <strong>สถิติการปฏิบัติตามกฎหมายของสถานประกอบการเอกชน <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <?php if($is_html && 1==0){?>
    <table align="center">
    	<tr>
        	<td>
            <div id="chart1div">
               ...
            </div>
            </td>
            <td>
            <div id="chart2div">
               ...
            </div>
            </td>
        </tr>
    </table>
    <?php }?>
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td  rowspan="4" align="center"  >          <div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td  rowspan="4" align="center"  >          <div align="center" style="vertical-align:middle;"><strong>รายการ</strong> </div></td>
        <td  colspan="14" align="center" ><div align="center"><strong>นายจ้างหรือสถานประกอบการ</strong></div></td>
        </tr>
      <tr >
        <td  colspan="2" rowspan="2" align="center" valign="top"><div align="center"><strong>จำนวน<br>
          สถานประกอบการ	
 </strong></div></td>
        <td colspan="12" align="center" valign="top" ><div align="center"><strong>การปฏิบัติตามกฎหมาย	
        </strong></div></td>
        </tr>
      <tr >
        <td rowspan="2" align="center" valign="top">
        
        <div align="center"><strong>
        อัตราส่วนที่ต้อง<br />
        รับคนพิการเข้า<br />
        ทํางาน(ราย) </strong></div>
        
        </td>
        <td colspan="2" align="center" valign="top"><div align="center"><strong>          รับคนพิการ<br />
          จริง ตาม <br />
          ม33,ม34,ม35
        </strong>
        </div></td>
        <td align="center" valign="top"><div align="center"><strong>มาตรา 33</strong></div></td>
        <td colspan="3" align="center" valign="top"><div align="center"><strong>มาตรา 35</strong></div></td>
        <td colspan="5" align="center" valign="middle"><div align="center"><strong>มาตรา 34</strong></div></td>
        </tr>
      <tr >
        <td align="center" valign="top"><div align="center"><strong>แห่ง </strong></div></td>
        <td align="center"  valign="top"><div align="center"><strong>ร้อยละ </strong></div></td>
        <td align="center" valign="top"><strong>(ราย) </strong></td>
        <td align="center" valign="top"><strong>(ร้อยละ)</strong></td>
        <td align="center"  valign="top"><div align="center"><strong>คนพิการ<br />
        (ราย) </strong></div></td>
        <td align="center"  valign="top"><div align="center"><strong>คนพิการ<br>
        (ราย) </strong></div></td>
        <td align="center"  valign="top"><div align="center"><strong>ผู้ดูแล<br>
          คนพิการ<br>
        (ราย) </strong></div></td>
        <td align="center"  valign="top"><div align="center"><strong>รวม<br />
        (ราย) </strong></div></td>
        <td align="center" valign="middle"><strong>จ่ายเงินแทน<br />
          รับคนพิการ<br />
          (ราย)</strong></td>
        <td align="center" valign="middle"><strong>เงินต้น<br />
          (บาท)</strong></td>
        <td align="center" valign="middle"><strong>ดอกเบี้ย<br />
          (บาท)</strong></td>
        <td align="center" valign="middle"><strong>จ่ายเกิน<br />
(บาท)</strong></td>
        <td align="center" valign="middle"><div align="center"><strong> รวม <br />
          (บาท)</strong></div></td>
        </tr>
      <tr >
        <td  valign="top"><div align="center"><strong>1</strong></div></td>
        <td colspan="15"  valign="top"><div align="left"><strong>ปฏิบัติตามกฎหมายครบตามอัตราส่วน</strong></div></td>
        </tr>
      </thead>
      
      <tbody>
      
      <?php
	  
	  	if($_POST["chk_non_ratio"]){
	  
			$row_name_array = array(
				"รับคนพิการเข้าทำงานครบตามอัตราส่วน (ม.33)"
				,"ส่งเงินเข้ากองทุนแทนการรับคนพิการเข้าทำงาน (ม.34)"
				,"ให้สัมปทานฯ (ม.35)"
				,"รับคนพิการเข้าทำงานและส่งเงินเข้ากองทุนฯ (ม.33,34)"
				,"รับคนพิการเข้าทำงานและให้สัมปทานฯ (ม.33,35)"
				
				,"ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.34,35)"
				,"รับคนพิการเข้าทำงาน ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.33,34,35)"
				,"ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน"
				
				,"ไม่เข้าข่ายจำนวนลูกจ้าง"
				
				,"ไม่ปฏิบัติตามกฎหมาย"
				
				//,"มีการทำตามกฏหมาย แต่ไม่มีการ check flag ม.33,ม.34 หรือ ม.35"
				
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
				
				," and LawfulStatus = '3' $branch_codition"
				
				," and (LawfulStatus = '0' or LawfulStatus is null) $branch_codition"
				
				//," and LawfulStatus != '3' and Hire_status = '0' and pay_status = '0' and Conc_status = '0' $branch_codition"
				
	
			);
			
			
			$all_company_sql = "select 
								count(company.CID)
								
							from 
							
								company
								JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								 $province_table
							where 
								1=1
								$typecode_filter
								$province_filter
								
								$branch_codition
								
								$last_modified_sql
								
								
										";		
			
		}else{
			
			$row_name_array = array(
				"รับคนพิการเข้าทำงานครบตามอัตราส่วน (ม.33)"
				,"ส่งเงินเข้ากองทุนแทนการรับคนพิการเข้าทำงาน (ม.34)"
				,"ให้สัมปทานฯ (ม.35)"
				,"รับคนพิการเข้าทำงานและส่งเงินเข้ากองทุนฯ (ม.33,34)"
				,"รับคนพิการเข้าทำงานและให้สัมปทานฯ (ม.33,35)"
				
				,"ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.34,35)"
				,"รับคนพิการเข้าทำงาน ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.33,34,35)"
				,"ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน"
				
				//,"ไม่เข้าข่ายจำนวนลูกจ้าง"
				
				,"ไม่ปฏิบัติตามกฎหมาย"
				
				
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
				
				//," and LawfulStatus = '3' $branch_codition"
				
				," and (LawfulStatus = '0' or LawfulStatus is null) $branch_codition"
					
	
			);
			
			
			$all_company_sql = "select 
								count(company.CID)
								
							from 
							
								company
								JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								 $province_table
							where 
								(lawfulstatus != 3 or lawfulstatus is null)
								$typecode_filter
								$province_filter
								
								$branch_codition
								
								$last_modified_sql
								
								
								
								
										";		
										
									//	echo $all_company_sql;
			
			
		}
	  	
	  ?>
      
      <?php 
	  
										
			

		
		$all_company = getFirstItem($all_company_sql);
		
		//echo "<br><br>$all_company_sql";	
		
		//echo "<br>count all company = ".$all_company;
		
		$all_company2_sql = "select 
								count(company.CID)
								
							from 
							
								company
								JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								 $province_table
							where 
								(lawfulstatus != 3 or lawfulstatus is null)
								
								$branch_codition
								
								$typecode_filter2
								$province_filter
								
								$last_modified_sql
								
										"; //echo $all_company_sql; exit();
		$all_company2 = getFirstItem($all_company2_sql);								
		
		
		//echo "<br>count all company2 = ".$all_company2;
	  
	  $the_count = 0;
	  $choice_prefix = "1.";
	  
	  for($i=0; $i<count($row_name_array);$i++){
	  
	  	$the_count++;
		
		?>
        
			 <?php if($i == 7){ //end if row 7 -> add total...
			 
			 	//reset the count
				$the_count = 2;
				$choice_prefix = ""; //remove prefix
			 
			 	?>
                  <tr>
                    <td  valign="top" colspan="2"><div align="right"><b>รวมลำดับที่ 1.1-1.7</b></div></td>
                    <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?php $total_lawful_company = formatEmployeeReport($total_company); echo $total_lawful_company;?></strong> </div></td>
                    <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?php 
					
						//echo floor(($total_percent*100))/100;
						
						echo number_format($total_percent,2);
						
						?></strong> </div></td>
                    <td align="right"  <?php echo $footer_row?>><div align="right"> <strong><?php echo formatEmployeeReport($total_all_ratio);?> </strong></div></td>
                    <td align="right"  <?php echo $footer_row?>>
                    <div align="right"><strong><?php echo formatEmployeeReport($sum_emp + $total_curator_usee + $total_curator_user + $total_all_34)?></strong></div></td>
                    <td align="right"  <?php echo $footer_row?>>
                    
                     <div align="right"><strong>
					<?php 
                    
                        if($total_all_ratio || $sum_emp || $total_curator_usee || $total_curator_user || $total_all_34){
                    
                            echo number_format(($sum_emp + $total_curator_usee + $total_curator_user + $total_all_34)/$total_all_ratio*100,2) . "%";
                        
                        }else{
                            echo "-";	
                        }
                        
                    ?>
                    </strong> </div>
                    
                    
                    </td>
                    <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($sum_emp);?></strong> </div></td>
                    <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_usee);?></strong></div></td>
                    <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_user);?></strong></div></td>
                    <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_usee+$total_curator_user);?></strong></div></td>
                    <td align="right"  <?php echo $footer_row?>><strong><?php echo formatEmployee($total_all_34)?></strong></td>
                    <td align="right"  <?php echo $footer_row?>><strong><?php echo formatNumber($total_incurred_paid_money)?></strong></td>
                    <td align="right"  <?php echo $footer_row?>><strong><?php echo formatNumber($total_incurred_interest_money)?></strong></td>
                    <td align="right"  <?php echo $footer_row?>><strong><?php echo formatNumber($total_incurred_extra_money)?></strong></td>
                    <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?php 
					
						echo formatMoneyReport($sum_rule_35);
						//echo formatMoneyReport($total_incurred_paid_money+$total_incurred_interest_money+$total_incurred_extra_money);
						
						?></strong> </div></td>
                  </tr>
              <?php }?>
        
        
        <?php
	  
		
		$company_sql = "select 
							count(company.CID)
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

		//echo "<br><br>".$company_sql; //exit();
									
									
		//$all_company += $this_company;
		 
		 $company2_sql = "select 
							count(company.CID)
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
							
									"; //echo "<br>".$company_sql; //exit();
									
		
		
		//yoes 20160328 --> add อัตราส่วนที่ต้องรับคนพิการเข้าทํางาน(ราย)
		$sql_all_ratio = "
				select 
					sum(
						if(
							lawfulness.employees % $the_limit <= $half_limit
							, floor(lawfulness.employees/$the_limit)
							, ceil(lawfulness.employees/$the_limit)
	
						) 
					)                    as company_ratio
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
				
			$all_ratio = getFirstItem($sql_all_ratio);
			$total_all_ratio += $all_ratio;
		


		//for "unlawful", do it a hardway
		if($i==count($row_name_array)-1){
			$company_sql = "select 
						
							count(company.CID)
						from 
							company
							JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							 $province_table
						where 
							(lawfulstatus = 0 or lawfulstatus is null)
							
							
							
							$typecode_filter
							
							
							$branch_codition
							$province_filter
							
							$last_modified_sql
							
							";
							
							
							
			 $company2_sql = "select 
						
							count(company.CID)
						from 
							company
							JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							 $province_table
						where 
							(lawfulstatus = 0 or lawfulstatus is null)
							
							$typecode_filter
							
							$branch_codition
							$province_filter
							
							
							$last_modified_sql
							
							";										
		}
											
											
		//echo "<br><br>$i :: ".$company_sql;
		
		
	  	 $this_company = default_value(getFirstItem("$company_sql"),0);
		 
		// echo "<br>this company: $this_company";
		
		//echo "<br><br>$i :: ".$company2_sql;
		 
		 
		 $total_company += $this_company;
									
	  	 $this_company2 = default_value(getFirstItem("$company2_sql"),0);
		 $total_company2 += $this_company2;
		 
		 $this_percent = 0;	
		 $this_percent2 = 0;	
		
		if($this_company > 0){
			$this_percent = round($this_company/$all_company * 100,2);
			//echo $this_company . " -- "  . $all_company . " = " . $this_percent;
			$total_percent += number_format($this_percent,2);
		}	
		if($this_company2 > 0){
			$this_percent2 = round($this_company2/$all_company2 * 100,2);
			$total_percent2 += number_format($this_percent2,2);
		}	
		 
		$this_emp_sql = "select 
						
							sum(Hire_NumofEmp)
						
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
							
									";			
			
			
		//echo $this_emp_sql;						

		//for "unlawful", do it a hardway
		if($i==count($row_name_array)-1){
		
		
			$company_sql = "select 
						
							sum(Hire_NumofEmp)
						from 
							company
							JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							 $province_table
						where 
							(lawfulstatus = 0 or lawfulstatus is null)
							
							$typecode_filter
							
							$province_filter							
							
							$branch_codition
							$last_modified_sql
							";
		}
		
		$this_emp = default_value(getFirstItem($this_emp_sql),0);
		$sum_emp += $this_emp;
		
		//echo "<br><br> $this_emp_sql";
		
		//----
		$this_emp2_sql = "select 
						
							sum(Hire_NumofEmp)
						
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
							
									";				
			
		$this_emp2 = default_value(getFirstItem($this_emp2_sql),0);
		$sum_emp2 += $this_emp2;
		
		//echo "<br><br> $this_emp2_sql";
		
		//--------------
		
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
				and lawfulness.year = '$the_year'
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
		
		$rule_35_sql = "						
			
			select 
						sum(a.the_sum)
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
			
			";
		//echo "<br><br>".$rule_35_sql;
		
									
		$this_rule_35 = default_value(getFirstItem($rule_35_sql),0);
		$sum_rule_35 += $this_rule_35;
		
		
		
		
		//---------- curator stuffs
		$the_sql = "
												
					select count(*) 
					from 
					curator 
					where 
					
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
					
				";
				
		
		$curator_user = getFirstItem($the_sql);	
		$total_curator_user += $curator_user;	
		
		
		
		//---------- curator stuffs
		$the_sql = "
												
					select count(*) 
					from 
					curator 
					where 
					
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
					
				
				";
				
				
		//echo "<br><br>$the_sql";
		
		$curator_usee = getFirstItem($the_sql);	
		$total_curator_usee += $curator_usee;	
		
		
		
		
	  ?>
      
      <?php
	  
	  	//get number of company to render graph
		
		//lawfulness
		if($i==0){
	  		$company_1 = $this_company;
		}
		if($i==1){
	  		$company_2 = $this_company;
		}
		if($i==2){
	  		$company_3 = $this_company;
		}
		if($i==3){
	  		$company_4 = $this_company;
		}
		if($i==4){
	  		$company_5 = $this_company;
		}
		if($i==5){
	  		$company_6 = $this_company;
		}
		if($i==6){
	  		$company_7 = $this_company;
		}
		
		
		//non-lawfulness
	  	if($i==count($row_name_array)-2){
	  		$this_not_finish_company = $this_company;
		}
	  	if($i==count($row_name_array)-1){
	  		$this_unlawful_company = $this_company;
		}
		
		//filter for detailed page
		$filter_i = $i;
		
		//extra for non-ratio report
		if($_POST["chk_non_ratio"] && $i == 8){
			$filter_i = 9;
		}
		
		if($_POST["chk_non_ratio"] && $i == 9){
			$filter_i = 8;
		}
	  
	  	
	  
	  ?>
      
      
      <tr>
        <td  valign="top"><div align="center">
		
        	<?php if(!$choice_prefix){echo "<b>";}?>
				<?php echo $choice_prefix;?><?php echo $the_count;?>
            <?php if(!$choice_prefix){echo "</b>";}?>
            
          </div></td>
        <td  valign="top"><div align="left">
			<?php if(!$choice_prefix){echo "<b>";}?>
				<?php echo $row_name_array[$i];?>
            <?php if(!$choice_prefix){echo "</b>";}?>
            </div></td>
        <td align="right"  valign="top"><div align="right">
		
			<?php echo formatEmployeeReport($this_company);?> 
        
        
       		 <?php if($chk_details){ ?>
            <a href="report_1_details.php?year=<?php echo $the_year;?>&filter=<?php echo $filter_i; ?>&data=99" target="_blank" title="แสดงรายละเอียด">...</a>
            <?php }?>  
        
        
        </div></td>
        <td align="right"  valign="top"><div align="right"><?php 
		
			//echo floor(($this_percent*100))/100;
			
			echo number_format($this_percent,2);
			
			?> </div></td>
        <td align="right"  valign="top">
        
        
        	<div align="right">
            
            	<?php 
				
					
					if(!$all_ratio){
						$all_ratio = 0;	
					}
					
					echo formatEmployeeReport($all_ratio);
					
					?>
            </div>
        
        </td>
        <td align="right"  valign="top">
        
        
        <?php 
		
		//yoes 20160125
			//...whatever here
			
			
			$sql_01 = "
			
				select 
					a.cid
					, b.lid
					, a.province
					,sum(
						if(b.employees >= $the_limit
							,
							if(
								b.employees % $the_limit <= $half_limit
								, floor(b.employees/$the_limit)
								, ceil(b.employees/$the_limit)
		
							)
							,0
						) 
					)                    as company_ratio
					, b.Hire_NumofEmp as num_hired
					, COALESCE(max(sum_curator),0) as num_curated
					, 
					
					sum(
						if(b.employees >= $the_limit
							,
							if(
								b.employees % $the_limit <= $half_limit
								, floor(b.employees/$the_limit)
								, ceil(b.employees/$the_limit)
		
							)
							,0
						) 
					)  - b.Hire_NumofEmp - COALESCE(max(sum_curator),0) as num_needed
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.year = '$the_year'
							
							".$lawful_filter_array[$i]."
							
						left outer join
							(
							
								select 
									a.cid
									, count(*) as sum_curator
								from
									company a
										join lawfulness b
											on a.cid = b.cid
											and b.year = '$the_year'
										join curator c
											on b.lid = c.curator_lid
											and c.curator_parent = 0
											
								where
									
									1=1
									".$lawful_filter_array[$i]."
									
								group by
									a.cid
							
							) cccc
								on a.cid = cccc.cid
								
				join
				 (
			
						select 
							distinct(c.lid)
						from
							company a
								join lawfulness b
									on a.cid = b.cid
									and b.year = '$the_year'
								join payment c
									on b.lid = c.lid
								join receipt d
									on c.rid = d.rid
						where
							
							1=1
							".$lawful_filter_array[$i]."
						
					) mnm
					
					 on 
					 
					b.lid = mnm.lid
					
				where
					1=1
					$province_detailed_34_filter
					
				group by
					a.cid
					, b.lid
					, b.Hire_NumofEmp
			
			";
			
			//echo $sql_01; // exit();
			
			$the_sql = "
						select 
							*
							, receipt.amount as receipt_amount 
							, lawfulness.lid as lawfulness_lid
							, zzz.cid as company_cid
						from 
							payment
							, receipt
							, lawfulness
							join (
							
								$sql_01
							
							) zzz on lawfulness.lid = zzz.lid
							
						where 
						
							receipt.RID = payment.RID
							and
							lawfulness.LID = payment.LID
							
							and
							lawfulness.year = '".$the_year."' 
							
							and
							is_payback = 0
							and
							main_flag = 1
							order by lawfulness.lid,ReceiptDate, BookReceiptNo, ReceiptNo asc";
							
			//echo $the_sql; 
		
		
			$the_result = mysql_query($the_sql) or die(mysql_error()); //this one is slow...
			
			$the_row = 0;
			$last_lid = "";
			$last_cid = "";
			$last_num_needed  = "";
			$paid_from_last_bill = 0;
			$last_payment_date = 0;
			$paid_money = 0;
			$start_money = 0;
			$interest_money = 0;
			$maimad_paid = 0;
			
			//total
			$incurred_paid_money = 0;
			$incurred_interest_money = 0;
			$incurred_extra_money = 0;
			
			$this_lawful_year = $the_year;
			
			//reset for all rows
			$all_34 = 0;
			
			
			include "scrp_get_34_details_from_lid_result_set.php.php";
			
			
			//if blank then 0
			
			$total_13 += $all_34;
			
			$total_all_34 += $all_34;
		
		
		?>
        
        <div align="right"><?php echo formatEmployeeReport($this_emp + $curator_usee + $curator_user + $all_34)?> </div>
        
        </td>
        <td align="right"  valign="top">
		<div align="right">
		
		<?php 
		
			if($all_ratio || $this_emp || $curator_usee || $curator_user || $all_34){
		
				echo number_format(($this_emp + $curator_usee + $curator_user + $all_34)/$all_ratio*100,2) . "%";
			
			}else{
				echo "-";	
			}
			
		?>
        
        
        </div></td>
        <td align="right"  valign="top"><div align="right">
		
		
			<?php echo formatEmployeeReport($this_emp);?>
            
            
			<?php if($chk_details){ ?>
            <a href="report_1_details.php?year=<?php echo $the_year;?>&filter=<?php echo $filter_i; ?>&data=33" target="_blank" title="แสดงรายละเอียด">...</a>
            <?php }?>            
        
        
        </div></td>
        <td align="right" valign="top"><div align="right">
		
			<?Php echo formatEmployeeReport($curator_usee);?>
        
       		<?php if($chk_details){ ?>
            	<a href="report_1_details.php?year=<?php echo $the_year;?>&filter=<?php echo $filter_i; ?>&data=351" target="_blank" title="แสดงรายละเอียด">...</a>
            <?php }?>  
        
        </div></td>
        <td align="right"  valign="top"><div align="right">
		
			<?Php echo formatEmployeeReport($curator_user);?>
            <?php if($chk_details){ ?>
            <a href="report_1_details.php?year=<?php echo $the_year;?>&filter=<?php echo $filter_i; ?>&data=352" target="_blank" title="แสดงรายละเอียด">...</a>
            <?php }?>  
            
            </div></td>
        <td align="right"  valign="top">
        <?Php echo formatEmployeeReport($curator_usee+$curator_user);?>
        </td>
        <td align="right"  valign="top">
        
        <?php 
		
			
		
			echo formatEmployeeReport($all_34);
		?>
        
        </td>
        <td align="right"  valign="top"><?php echo formatNumber($incurred_paid_money); $total_incurred_paid_money += $incurred_paid_money;?></td>
        <td align="right"  valign="top"><?php echo formatNumber($incurred_interest_money); $total_incurred_interest_money += $incurred_interest_money?></td>
        <td align="right"  valign="top"><?php echo formatNumber($incurred_extra_money); $total_incurred_extra_money += $incurred_extra_money;?></td>
        <td align="right"  valign="top"><div align="right">
        
			<?php echo formatMoneyReport($this_rule_35); //echo $rule_35_sql;?>
            
            <?php if($chk_details){ ?>
                <a href="report_1_details.php?year=<?php echo $the_year;?>&filter=<?php echo $filter_i; ?>&data=34" target="_blank" title="แสดงรายละเอียด">...</a>
                <?php }?>  
        </div></td>
        </tr>
      
      
				 
      
      <?php
	  }
	  ?>
	  </tbody>
        
        <?php
			if($_POST["report_format"] == "pdf"){
				//
			}else{
				$footer_row = 'style="border-bottom:double;"';
			}
		?>
        
        <tfoot>
      <tr>
        <td colspan="2" ><div align="right"><strong>รวมลำดับที่ 1-3 ทั้งสิ้น</strong></div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($all_company);?></strong> </div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong>
        
        <?php 
			//echo number_format($total_percent,2); 
		
			echo "100.00"; ?>
       
        
        </strong> </div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"> <strong><?php echo formatEmployeeReport($total_all_ratio);?> </strong></div></td>
        <td align="right"  <?php echo $footer_row?>>
        
        <div align="right"><strong><?php echo formatEmployeeReport($sum_emp+$total_curator_usee+$total_curator_user+$total_all_34);?></strong> </div>
        
        </td>
        <td align="right"  <?php echo $footer_row?>>
        
        
        <div align="right"><strong>
        <?php 
		
			if($total_all_ratio || $sum_emp || $total_curator_usee || $total_curator_user || $total_all_34){
		
				echo number_format(($sum_emp + $total_curator_usee + $total_curator_user + $total_all_34)/$total_all_ratio*100,2) . "%";
			
			}else{
				echo "-";	
			}
			
		?>
        </strong> </div>
        
        </td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($sum_emp);?></strong> </div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_usee);?></strong></div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_user);?></strong></div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_usee+$total_curator_user);?></strong></div></td>
        <td align="right"  <?php echo $footer_row?>><strong><?php echo formatEmployee($total_all_34)?></strong></td>
        <td align="right"  <?php echo $footer_row?>><strong><?php echo formatNumber($total_incurred_paid_money)?></strong></td>
        <td align="right"  <?php echo $footer_row?>><strong><?php echo formatNumber($total_incurred_interest_money)?></strong></td>
        <td align="right"  <?php echo $footer_row?>><strong><?php echo formatNumber($total_incurred_extra_money)?></strong></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?php 
		
		echo formatMoneyReport($sum_rule_35);
		
		//echo formatMoneyReport($total_incurred_paid_money+$total_incurred_interest_money+$total_incurred_extra_money);
		
		?></strong> </div></td>
        </tr>
      </tfoot>
</table>
    
    
<?php if($is_html){?>
             
        <script type="text/javascript" src="Charts/FusionCharts.js"></script>
            
        <?php 
        
        
        $strXML = "<chart palette='2' piefillAlpha='34' pieBorderThickness='3' hoverFillColor='FDCEDA' pieBorderColor='FFFFFF' baseFontSize='9' useHoverColor='1' caption='' >     <category label='การปฏิบัติตามกฎหมายของสถานประกอบการ'  fillColor='DBFF6C' >        		<category label='ปฏิบัติตามกฎหมายครบตามอัตราส่วน' fillColor='DBFF6C' value='".deleteCommas($total_lawful_company)."'>					   <category label='(ม.33)' value='$company_1' >				  			   </category>			   			   <category label='(ม.34)' value='$company_2'>							   </category>			   			   <category label='(ม.35)' value='$company_3'>							   </category>			   <category label='(ม.33,34)' value='$company_4'>							   </category>			   <category label='(ม.33,35)' value='$company_5'>							   </category>			   <category label='(ม.34,35)' value='$company_6'>							   </category>			   <category label='(ม.33,34,35)' value='$company_7'>							   </category>			   			           </category>						     <category label='ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน' fillColor='FFFF00'  value='".deleteCommas($this_not_finish_company)."' >        		  				     </category>	      <category label='ไม่ปฏิบัติตามกฎหมาย' fillColor='DAEDFC' link='Details.asp?CIO' value='".deleteCommas($this_unlawful_company)."'>        				     </category>	 		 	   </category>        </chart>";
        
        //echo renderChart("Charts/MultiLevelPie.swf","",$strXML, "donut_1", 800, 800, false, true);
        
        
        $strXML = "<chart caption='ร้อยละการปฏิบัติตามกฎหมายของสถานประกอบการ' showPercentValues='1' formatNumberScale='0' >    <set label='ครบตามอัตราส่วน' value='".deleteCommas($total_lawful_company)."' color='40FF00'/>   <set label='ไม่ครบตามอัตราส่วน' color='FFFF00'  value='".deleteCommas($this_not_finish_company)."' />   <set label='ไม่ปฏิบัติตามกฎหมาย' value='".deleteCommas($this_unlawful_company)."' color='FF0000'/></chart>";
        
        $strXML2 = "<chart caption='ร้อยละการปฏิบัติตามกฎหมายครบตามอัตราส่วน แบ่งตามมาตรา' showPercentValues='1' palette='3' >    <set label='ม.33' value='$company_1' color='40FF00'/><set label='ม.34' value='$company_2' color='00FFFF' /><set label='ม.35' value='$company_3' color='FFFF00' /><set label='ม.33,34' value='$company_4' color='FF0000'/><set label='ม.33,35' value='$company_5' color='0000FF' /><set label='ม.34, 35' value='$company_6' color='#FACC2E' /><set label='ม.33,34,35' value='$company_7' color='FF00FF' /> </chart>";
        ?>    
        
        
        <script type="text/javascript">
           var chart1 = new FusionCharts("Charts/Pie3D.swf", "ChId1", "550", "400", "0", "1");
           chart1.setXMLData("<?php echo $strXML;?>");
           chart1.render("chart1div");
        </script>
        
        <script type="text/javascript">
           var chart2 = new FusionCharts("Charts/Pie3D.swf", "ChId2", "550", "400", "0", "1");
           chart2.setXMLData("<?php echo $strXML2;?>");
           chart2.render("chart2div");
        </script>
    
    
<?php }?>    


<?php if($_POST[chk_sum_company]){?>
<div align="center">
<br />
   <strong> จำนวนสถานประกอบการที่ปฏิบัติตามกฎหมายครบตามอัตราส่วน...</strong>
<br />
มีการรับคนพิการเข้าทำงานครบตามอัตราส่วน (ม.33): <?php echo formatEmployeeReport($company_1+$company_4+$company_5+$company_7);?> แห่ง
    <br />
    มีการส่งเงินเข้ากองทุนแทนการรับคนพิการเข้าทำงาน (ม.34): <?php echo formatEmployeeReport($company_2+$company_4+$company_6+$company_7);?> แห่ง
    <br />
    มีการให้สัมปทานฯ (ม.35): <?php echo formatEmployeeReport($company_3+$company_5+$company_6+$company_7);?> แห่ง
</div>
<?php }?>


    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
