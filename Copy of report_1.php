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
	$typecode_filter2 = " and CompanyTypeCode = '14'";
	$business_type = "สถานประกอบการ";
//}

?>

<div align="center">
            <strong>สรุปการปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการ และหน่วยงานภาครัฐ <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td width="45" rowspan="3"  valign="top"><br />          <div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="150" rowspan="3"  valign="top"><br />          <div align="center" style="vertical-align:middle;"><strong>รายการ</strong> </div></td>
        <td width="275" colspan="5" ><div align="center"><strong>นายจ้างหรือสถานประกอบการ 				
</strong></div></td>
        <td colspan="4" width="220" ><div align="center"><strong>หน่วยงานภาครัฐ			
</strong></div></td>
        </tr>
      <tr >
        <td colspan="2" valign="top" width="100"><div align="center"><strong>จำนวน<br>
          สถานประกอบการ	
 </strong></div></td>
        <td colspan="2" valign="top" width="100"><div align="center"><strong>การปฏิบัติ<br>
          ตามกฎหมาย	
 </strong></div></td>
        <td width="75" rowspan="2" valign="top"><div align="center"><strong>ส่งเงิน<br>
          เข้า<br>
          กองทุน<br>
        (บาท)</strong></div></td>
        <td colspan="2" width="110" ><div align="center"><strong>จำนวนหน่วยงานภาครัฐ	
</strong></div>          </td>
        <td colspan="2" width="110" ><div align="center"><strong>การปฏิบัติตามกฎหมาย	
</strong></div></td>
        </tr>
      <tr >
        <td width="50" valign="top"><div align="center"><strong>แห่ง </strong></div></td>
        <td width="50" valign="top"><div align="center"><strong>ร้อยละ </strong></div></td>
        <td width="50" valign="top"><div align="center"><strong>คนพิการ<br>
        (ราย) </strong></div></td>
        <td width="50" valign="top"><div align="center"><strong>ผู้ดูแล<br>
          คนพิการ<br>
        (ราย) </strong></div></td>
        <td width="55" valign="top"><div align="center"><strong>แห่ง </strong></div></td>
        <td width="55" valign="top"><div align="center"><strong>ร้อยละ </strong></div></td>
        <td width="55" valign="top"><div align="center"><strong>คนพิการ<br>
        (ราย) </strong></div></td>
        <td width="55" valign="top"><div align="center"><strong>ผู้ดูแล<br>
          คนพิการ<br>
        (ราย) </strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      
      <?php
	  
		$row_name_array = array(
			"รับคนพิการเข้าทำงานครบ<br>ตามอัตราส่วนตามมาตรา 33"
			,"ส่งเงินเข้ากองทุนฯ ตามมาตรา 34"
			,"ให้สัมปทาน ฯ ตามมาตรา 35"
			,"รับคนพิการตามมาตรา 33 และ ส่งเงินเข้ากองทุนฯ ตามมาตรา 34"
			,"รับคนพิการตามมาตรา 33 และให้สัมปทาน ฯ ตามมาตรา 35"
			
			,"ส่งเงินเข้ากองทุนฯ ตามมาตรา 34 และให้สัมปทาน ฯ ตามมาตรา 35"
			,"รับคนพิการตามมาตรา 33  ส่งเงินเข้ากองทุนฯ ตามมาตรา 34 และให้สัมปทาน ฯ ตามมาตรา 35"
			,"ปฏิบัติตามกฎหมาย<br>แต่ไม่ครบตามอัตราส่วน"
			,"ไม่ปฏิบัติตามกฎหมาย"
		);

	  	$lawful_filter_array = array(
			" and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '0' "
			," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '0' "
			," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '0' and Conc_status = '1' "
			," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '0' "
			," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '1' "
			
			," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '1' "
			," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '1' "
			," and LawfulStatus = '2' "
			," and (LawfulStatus = '0' or LawfulStatus is null) "
		);
	  	
	  ?>
      
      <?php 
	  
		$all_company_sql = "select 
								count(company.CID)
								
							from 
								lawfulness
								, company
								$province_table
							where 
								company.CID = lawfulness.CID
								and CompanyTypeCode != '14'
								and LawfulStatus != '3'
								$province_filter
								and Year = '$the_year'
										";
										
		$all_company_sql = "select 
								count(company.CID)
								
							from 
							
								company
								LEFT JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								 $province_table
							where 
								(lawfulstatus != 3 or lawfulstatus is null)
								$typecode_filter
								$province_filter
										";										
										
										 //echo $all_company_sql; exit();
		$all_company = getFirstItem($all_company_sql);	
		
		$all_company2_sql = "select 
								count(company.CID)
								
							from 
							
								company
								LEFT JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								 $province_table
							where 
								(lawfulstatus != 3 or lawfulstatus is null)
								$typecode_filter2
								$province_filter
										"; //echo $all_company_sql; exit();
		$all_company2 = getFirstItem($all_company2_sql);								
		
	  
	  for($i=0; $i<count($row_name_array);$i++){
		
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
							and CompanyTypeCode != '14'
							and Year = '$the_year'
									"; //echo $company_sql; exit();
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
							and CompanyTypeCode = '14'
							and Year = '$the_year'
									"; //echo $company_sql; exit();


		//for "unlawful", do it a hardway
		if($i==count($row_name_array)-1){
			$company_sql = "select 
						
							count(company.CID)
						from 
							company
							LEFT JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							 $province_table
						where 
							(lawfulstatus = 0 or lawfulstatus is null)
							and CompanyTypeCode != '14'
							$province_filter";
							
			 $company2_sql = "select 
						
							count(company.CID)
						from 
							company
							LEFT JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							 $province_table
						where 
							(lawfulstatus = 0 or lawfulstatus is null)
							and CompanyTypeCode = '14'
							$province_filter";										
		}
											
	  	 $this_company = default_value(getFirstItem("$company_sql"),0);
								
									
	  	 $this_company2 = default_value(getFirstItem("$company2_sql"),0);
		 
		 $this_percent = 0;	
		 $this_percent2 = 0;	
		
		if($this_company > 0){
			$this_percent = formatNumberReport($this_company/$all_company * 100);
		}	
		if($this_company2 > 0){
			$this_percent2 = formatNumberReport($this_company2/$all_company2 * 100);
		}	
		 
		$this_emp_sql = "select 
						
							sum(Hire_NumofEmp)
						
						from 
							lawfulness
							, company
							 $province_table
						where 
							company.CID = lawfulness.CID
							and CompanyTypeCode != '14'
							$province_filter
							and Year = '$the_year'
							".$lawful_filter_array[$i]."
									";				
		
		///if($i == 7){
			//echo $this_emp_sql; exit();
		//}
		

		//for "unlawful", do it a hardway
		if($i==count($row_name_array)-1){
			$company_sql = "select 
						
							sum(Hire_NumofEmp)
						from 
							company
							LEFT JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							 $province_table
						where 
							(lawfulstatus = 0 or lawfulstatus is null)
							and CompanyTypeCode != '14'
							$province_filter";
		}
		
		$this_emp = default_value(getFirstItem($this_emp_sql),0);
		$sum_emp += $this_emp;
		
		//----
		$this_emp2_sql = "select 
						
							sum(Hire_NumofEmp)
						
						from 
							lawfulness
							, company
							 $province_table
						where 
							company.CID = lawfulness.CID
							and CompanyTypeCode = '14'
							$province_filter
							and Year = '$the_year'
							".$lawful_filter_array[$i]."
									";				
			
		$this_emp2 = default_value(getFirstItem($this_emp2_sql),0);
		$sum_emp2 += $this_emp2;
		
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
				and ReceiptYear = '$the_year'
				and main_flag = 1
				$typecode_filter
				$province_filter
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
					where
						lawfulness.CID = a.the_cid
						and lawfulness.Year = a.ReceiptYear
						
				".$lawful_filter_array[$i]."
			
			";
		//echo $rule_35_sql;
		
									
		$this_rule_35 = default_value(getFirstItem($rule_35_sql),0);
		$sum_rule_35 += $this_rule_35;
		
	  ?>
      <tr>
        <td width="45" valign="top"><div align="center"><?php echo $i+1;?></div></td>
        <td width="150" valign="top"><div align="left"><?php echo $row_name_array[$i];?></div></td>
        <td width="50" valign="top"><div align="right"><?php echo formatEmployeeReport($this_company);?> </div></td>
        <td width="50" valign="top"><div align="right"><?php echo formatNumberReport($this_percent);?> </div></td>
        <td width="50" valign="top"><div align="right"><?php echo formatEmployeeReport($this_emp);?></div></td>
        <td width="50" valign="top"><div align="right">-</div></td>
        <td width="75" valign="top"><div align="right"><?php echo formatMoneyReport($this_rule_35); //echo $rule_35_sql;?></div></td>
        <td width="55" valign="top"><div align="right"><?php echo formatEmployeeReport($this_company2);?> </div></td>
        <td width="55" valign="top"><div align="right"><?php echo formatNumberReport($this_percent2);?> </div></td>
        <td width="55" valign="top"><div align="right"><?php echo formatEmployeeReport($this_emp2);?></div></td>
        <td width="55" valign="top"><div align="right">- </div></td>
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
        <td colspan="2" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="50" <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($all_company);?></strong> </div></td>
        <td width="50" <?php echo $footer_row?>><div align="right"><strong>100.00</strong> </div></td>
        <td width="50" <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($sum_emp);?></strong> </div></td>
        <td width="50" <?php echo $footer_row?>><div align="right">-</div></td>
        <td width="75" <?php echo $footer_row?>><div align="right"><strong><?php echo formatMoneyReport($sum_rule_35);?></strong> </div></td>
        <td width="55" <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($all_company2);?></strong> </div></td>
        <td width="55" <?php echo $footer_row?>><div align="right"><strong>100.00</strong> </div></td>
        <td width="55" <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($sum_emp2);?></strong> </div></td>
        <td width="55" <?php echo $footer_row?>><div align="right"><strong></strong> -</div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
