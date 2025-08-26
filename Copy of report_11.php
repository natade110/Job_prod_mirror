<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_2.xls");

}elseif($_POST["report_format"] == "pdf"){
	
	$is_pdf = 1;
	//header("location: create_pdf_2.php");

}

$the_year = "2011";

if(isset($_POST["ddl_year"])){
	$the_year = $_POST["ddl_year"];
}

$the_year_to_use = formatYear($the_year);

$province_text = "ทั่วประเทศ";
$province_filter = "";		
if(isset($_POST["Province"]) && $_POST["Province"] != "" && $_POST["rad_area"] == "province"){
	$province_filter = " and company.Province = '".$_POST["Province"]."'";
	$province_text = "จังหวัด".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	$province_table = ", provinces";
	$province_filter = " and company.Province = provinces.province_id and provinces.section_id = '".$_POST["Section"]."'";
	$province_text = "ภาค".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
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
            <strong>สรุปการปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการครบตามอัตราส่วน โดยแยกตามประเภทกิจการ <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr >
        <td width="50" rowspan="4"  valign="top"><br />          <div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="117" rowspan="4"  valign="top"><br />          <div align="center" style="vertical-align:middle;"><strong>ประเภทกิจการ</strong> </div></td>
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
							and Year = '$the_year'";
	//echo $all_company_sql; exit();							
	$this_all_company = getFirstItem($all_company_sql);	
    
    while ($orgtype_row = mysql_fetch_array($orgtype_result)) {
		$row_count++;
		
	
		$the_sum_employee += $lawful_row["Hire_NumofEmp"];
		$the_sum_amount += $lawful_row["amount"];
		
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
							and Year = '$the_year'
							and BusinessTypeCode = '".$orgtype_row["BusinessTypeCode"]."'
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
							and Year = '$the_year'
							and BusinessTypeCode = '".$orgtype_row["BusinessTypeCode"]."'
						
						
									";				
			
		$this_rule_34 = default_value(getFirstItem($rule_34_sql),0);
		$sum_rule_34 += $this_rule_34;
		
		
		$rule_35_sql = "select 
						
							sum(Amount) as amount
						
						from 
							lawfulness
							, company
							, payment
							 $province_table
						where 
							lawfulness.LID = payment.LID 
							and	company.CID = lawfulness.CID
							and LawfulStatus = '1' 
							$typecode_filter
							$province_filter
							and Year = '$the_year'
							and BusinessTypeCode = '".$orgtype_row["BusinessTypeCode"]."'
						
									";	//echo $rule_35_sql; exit();
									
		$this_rule_35 = default_value(getFirstItem($rule_35_sql),0);
		$sum_rule_35 += $this_rule_35;									
		
  ?>
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $row_count;?></div></td>
        <td width="117" valign="top"><div align="left"><?php echo $orgtype_row["BusinessTypeName"];?></div></td>
        <td width="84" valign="top"><div align="right"><?php echo formatEmployee($this_lawful_company);?></div></td>
        <td width="84" valign="top"><div align="right"><?php echo $this_percent;?> </div></td>
        <td width="80" valign="top"><div align="right"><?php echo $this_rule_34;?> </div></td>
        <td width="80" valign="top"><div align="right"><?php echo formatMoney($this_rule_35);?> </div></td>
        <td width="80" valign="top"><div align="right"><?php echo $this_rule_34;?> </div></td>
        <td width="80" valign="top"><div align="right"> </div></td>
      </tr>
      <?php
	}
  ?>
	  </tbody>
        
        <tfoot>
      <tr>
        <td colspan="2" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="84" style="border-bottom:double;"><div align="right"><strong><?php echo formatEmployee($this_all_company);?></strong> </div></td>
        <td width="84" style="border-bottom:double;"><div align="right"><strong>100.00</strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployee($sum_rule_34);?></strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong><?php echo formatMoney($sum_rule_35);?></strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployee($sum_rule_34);?></strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong></strong> </div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
