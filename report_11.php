<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_2.xls");

}elseif($_POST["report_format"] == "pdf"){
	
	$is_pdf = 1;
	//header("location: create_pdf_2.php");

}else{

	header ('Content-type: text/html; charset=utf-8');
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
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}

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
	$business_type = "สถานประกอบการ";
//}

?>

<div align="center">
            <strong>รายละเอียดการปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการแยกตามประเภทกิจการ <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr >
        <td width="50" rowspan="4"  valign="top"><br />          <div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="137" rowspan="4"  valign="top"><br />          <div align="center" style="vertical-align:middle;"><strong>ประเภทกิจการ</strong> </div></td>
        <td colspan="6"  valign="top" width="488"><div align="center"><strong>นายจ้างหรือสถานประกอบการ 					</strong></div></td>
        </tr>
      <tr >
        <td colspan="2" width="168" ><div align="center"><strong>จำนวนสถานประกอบการ</strong></div></td>
        <td colspan="4" width="320" ><div align="center"><strong>การปฏิบัติตามกฎหมาย</strong></div></td>
        </tr>
      <tr >
        <td width="84" rowspan="2" valign="top"><br /><div align="center"><strong>แห่ง</strong></div></td>
        <td width="84" rowspan="2" valign="top"><br /><div align="center"><strong>ร้อยละ</strong></div></td>
        <td width="80" ><div align="center"><strong>มาตรา 33</strong></div></td>
        <td width="80" ><div align="center"><strong>มาตรา 34</strong></div></td>
        <td colspan="2" width="160" ><div align="center"><strong>มาตรา 35</strong></div></td>
        </tr>
      <tr >
        <td width="80" ><div align="center"><strong>คนพิการ (ราย)
        </strong></div></td>
        <td width="80" ><div align="center"><strong>ส่งเงิน (บาท)</strong></div></td>
        <td width="80" ><div align="center"><strong>คนพิการ (ราย)</strong></div></td>
        <td width="80" ><div align="center"><strong>ผู้ดูแลคนพิการ (ราย)</strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      <?php
	//generate info
	$get_orgtype_sql = "select *
            from businesstype
            order by BusinessTypeName asc
            ";
    //echo get_orgtype_sql;
    $orgtype_result = mysql_query($get_orgtype_sql);
    
    $all_company_sql = "select 
						
							count(company.CID)
						
						from 
							lawfulness
							, company
							$province_table
						where 
							company.CID = lawfulness.CID
							and LawfulStatus = '1'
							$typecode_filter
							$province_filter
							$branch_codition
							and Year = '$the_year'";
	//echo $all_company_sql; exit();							
	$this_all_company = getFirstItem($all_company_sql);	
    
    while ($orgtype_row = mysql_fetch_array($orgtype_result)) {
		$row_count++;
		
	
		$the_sum_employee += $lawful_row["Hire_NumofEmp"];
		$the_sum_amount += $lawful_row["amount"];
		
		
		if($orgtype_row["BusinessTypeCode"] == "0000"){
		
			//not indicated business code?
			//also include NULL
			$business_type_filter = "
			
						and 
						
						(
						
							BusinessTypeCode = '".$orgtype_row["BusinessTypeCode"]."'
							or 
							BusinessTypeCode 
							NOT IN (

								SELECT BusinessTypeCode
								FROM businesstype
							)
							OR BusinessTypeCode =  ''
						)
						
						";
		
		}else{
		
			$business_type_filter = "and BusinessTypeCode = '".$orgtype_row["BusinessTypeCode"]."'";
		}
		
		$lawful_company_sql = "select 
						
							count(company.CID)
						
						from 
							lawfulness
							, company
							$province_table
						where 
							company.CID = lawfulness.CID
							and LawfulStatus = '1'
							$typecode_filter
							$province_filter
							$branch_codition
							and Year = '$the_year'
							
							$business_type_filter
							
									";
		$this_lawful_company = getFirstItem($lawful_company_sql);	
		
		$this_percent = 0;	
		
		if($this_lawful_company > 0){
			$this_percent = formatNumber($this_lawful_company/$this_all_company * 100);
		}							
		//echo $lawful_company_sql; exit();					
		
		
		$rule_34_sql = "select 
						
							sum(Hire_NumofEmp)
						
						from 
							lawfulness
							, company
							 $province_table
						where 
							company.CID = lawfulness.CID
							and LawfulStatus = '1' 
							$typecode_filter
							$province_filter
							$branch_codition
							and Year = '$the_year'
							
							$business_type_filter
						
									";				
			
		
		//echo $rule_34_sql;		
		
		$this_rule_34 = default_value(getFirstItem($rule_34_sql),0);
		$sum_rule_34 += $this_rule_34;
		
		
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
				and main_flag = 1
				$business_type_filter
				and is_payback = 0
				$typecode_filter
				$province_filter
				$branch_codition
			group by
				company.cid
				, receipt.RID
			)
			";
	  
	//generate info
	$rule_35_sql = "
					
					
					select 
						sum(a.the_sum)
					from 
						lawfulness
						, $sub_table a
					where
						lawfulness.CID = a.the_cid
						and lawfulness.Year = a.ReceiptYear
						and LawfulStatus = '1'
						
						
				   ";
		
		//echo $rule_35_sql;
											
		$this_rule_35 = default_value(getFirstItem($rule_35_sql),0);
		$sum_rule_35 += $this_rule_35;				
		
	
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
							and LawfulStatus = '1'
							$typecode_filter
							$province_filter
							$branch_codition
							and Year = '$the_year'
							$business_type_filter
					
					)
					
					and 
					curator_parent = 0
					and
					curator_is_disable = 0
				
				";
				
		
		$curator_user = getFirstItem($the_sql);	
		$total_curator_user += $curator_user;	
		
		//echo $the_sql;
		
		$the_sql = "select 
							count(*) 
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
										and LawfulStatus = '1'
										$typecode_filter
										$province_filter
										$branch_codition
										and Year = '$the_year'
										$business_type_filter
								
								)
						and 
						curator_parent = 0
						and
						curator_is_disable = 1";
		
		$curator_usee = getFirstItem($the_sql);
		
		$total_curator_usee += $curator_usee;
		
  ?>
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $row_count;?></div></td>
        <td width="137" valign="top"><div align="left"><?php echo $orgtype_row["BusinessTypeName"];?></div></td>
        <td width="84" valign="top"><div align="right"><?php echo formatEmployeeReport($this_lawful_company);?></div></td>
        <td width="84" valign="top"><div align="right"><?php echo formatNumberReport($this_percent);?> </div></td>
        <td width="80" valign="top"><div align="right"><?php echo formatEmployeeReport($this_rule_34);?> </div></td>
        <td width="80" valign="top"><div align="right"><?php echo formatMoneyReport($this_rule_35);?></div></td>
        <td width="80" valign="top"><div align="right"><?php echo formatEmployeeReport($curator_usee);?></div></td>
        <td width="80" valign="top"><div align="right"><?php echo formatEmployeeReport($curator_user);?></div></td>
      </tr>
      <?php
	}
  ?>
	  </tbody>
        
        <tfoot>
      <tr>
        <td colspan="2" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="84" style="border-bottom:double;"><div align="right"><strong><?php echo formatEmployeeReport($this_all_company);?></strong> </div></td>
        <td width="84" style="border-bottom:double;"><div align="right"><strong>100.00</strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployeeReport($sum_rule_34);?></strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong><?php echo formatMoneyReport($sum_rule_35);?></strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployeeReport($total_curator_usee);?></strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployeeReport($total_curator_user);?></strong></div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
