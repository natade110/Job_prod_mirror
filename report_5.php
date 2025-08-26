<?php

include "db_connect.php";

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
	$branch_codition_2 =  " AND company.BranchCode < 1 ";

}

//yoes 20160614 -- start to use common includes here
include "report_school_filter.inc.php";

//yoes 20130813
//get ratio to use for this year...
$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_".$the_year."'"),100);


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
	
//  $typecode_filter = " and CompanyTypeCode = '14'";
//	$business_type = "หน่วยงานภาครัฐ";
		
//}else{
	$typecode_filter = " and CompanyTypeCode != '14'";
	$typecode_filter_2 = " and company.CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
//}

$typecode_filter .= " and CompanyTypeCode < 200";
$typecode_filter_2 .= " and company.CompanyTypeCode < 200";

//bank 20221223
include "org_type_filter.php";

//yoes 20130813 - add last modify date/time for lawfulness
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


//variables
$year_date = 365;
$the_wage = getThisYearWage($the_year);


//yoes 20211107
if($_POST[lawful_flag]){		
	$lawful_flag = $_POST[lawful_flag]*1;		
}

?>

<div align="center">
            <strong>รายละเอียดการปฏิบัติตามกฎหมาย ม.33 และ ม.34 <?php echo $province_text;?> <?php echo getLawfulText($lawful_flag);?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="11" align="center" valign="top"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
      </tr>
      <tr >
        <td width="50" rowspan="2" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td rowspan="2" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>เลขทะเบียนนายจ้าง</strong></div></td>
        <td width="177" rowspan="2" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ชื่อ<?php echo $business_type;?></strong> </div></td>
        <td width="228" rowspan="2" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ที่อยู่</strong> </div></td>
        <td width="227" rowspan="2" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>อัตราส่วนที่ต้องรับ<br />
        คนพิการเข้าทํางาน(ราย)</strong></div></td>
        <td width="100" rowspan="2" align="center" valign="top" ><div align="center"><strong>รับคนพิการ<br />
          ตามมาตรา 33
<br />
(ราย)</strong></div></td>
        <td colspan="5" align="center" valign="top" >
        <div align="center">
        <strong>จ่ายเงินแทนรับคนพิการเข้าทำงาน<br />ตามมาตรา 34</strong>
        </div>
        
        </td>
        </tr>
      <tr >
        <td width="100" align="center" valign="top" ><strong>จ่ายเงินแทน<br />
          รับคนพิการ<br />
        (ราย)</strong></td>
        <td width="100" align="center" valign="top" ><strong>เงินต้น<br />
        (บาท)</strong></td>
        <td width="100" align="center" valign="top" ><strong>ดอกเบี้ย<br />
        (บาท)</strong></td>
        <td width="100" align="center" valign="top" ><strong>จ่ายเกิน<br />
(บาท)</strong></td>
        <td width="100" align="center" valign="top" ><div align="center"><strong> รวม <br />
        (บาท)</strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      <?php
	//generate info
	$sub_table = "						
			(
			select 
				sum(receipt.Amount) the_sum
				, receipt.RID the_rid 
				, main_flag
				, ReceiptYear
				, company.cid the_cid 
				, company.cid the_company_id
				, company.CID
				, CompanyNameThai
				, CompanyTypeCode
				
				, Address1
				, Moo
				, Soi
				, Road
				, Subdistrict
				, District
				, Province
				, Zip
				, ReceiptNo
				, BookReceiptNo
				
				, CompanyCode
				, IF(lawfulness.Employees > 0, lawfulness.Employees, company.Employees) as ratioEmployees
				
				
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
				and is_payback = 0
				
				$typecode_filter
				$province_filter
				
				$branch_condition
				$CompanyType_filter
				
			group by
				company.cid
				,receipt.rid
			)
			";
	  
	//generate info
	$lawful_sql = "
					
					
					select 
						*
						, a.the_sum amount
						, main_flag
						, lawfulness.lid as lawfulness_lid
					from 
						company, lawfulness
						left outer join 
							$sub_table a 
						on
							lawfulness.CID = a.the_cid 
							and 
							lawfulness.Year = a.ReceiptYear
					WHERE 
						company.CID = lawfulness.CID
						AND LawfulStatus = '$lawful_flag'
						AND Hire_status = '1'
						AND pay_status = '1'
						AND Conc_status = '0'
						
						
						$province_filter
						
						$branch_codition_2
						
						$school_filter
						
						$last_modified_sql
						
						
						$typecode_filter_2
						
						AND Year = '$the_year'
						
						
					order by 
						company.CompanyNameThai asc
						
				   ";
									
	//echo $lawful_sql; exit();									
									
	$lawful_result = mysql_query($lawful_sql);	
	
	
	
	$address_array = array();
	$company_name_array = array();
	$cid_array = array();
	$amount_array = array();
	$text_array = array();
	$employee_array = array();
	
	$company_code_array = array();
	$ratioEmployees_array = array();
	
	//yoes 20160119
	$lid_array = array();
	$province_array = array(); // yoes 20160126
	
	
	while ($lawful_row = mysql_fetch_array($lawful_result)) {
		
		
		$the_sql = "select * from company a, provinces b where a.province = b.province_id and a.cid = '".$lawful_row["the_company_id"]."'";		
		$company_row = getFirstRow($the_sql); //2 is cid
		
		$the_sql = "select 
						IF(a.Employees > 0, a.Employees, b.Employees) as ratioEmployees 
					from 
						lawfulness a
						, company b 
					where 
						a.CID = b.CID 
						and 
						a.LID = '".$lawful_row["LID"]."'
						
						";		
		$lawfulness_row = getFirstRow($the_sql); 
		
		
		
		//prepare rows as array...
		$the_province_text = formatProvince(getFirstItem("select province_name from provinces where province_id = '".$lawful_row["Province"]."'"));		
		//$address_to_use = $lawful_row["Address1"]." ".$lawful_row["Moo"]." ".$lawful_row["Soi"]." ".$lawful_row["Road"]." ".$lawful_row["Subdistrict"]." ".$lawful_row["District"]." ".$the_province_text." ".$lawful_row["Zip"];
		$address_to_use = getAddressText($lawful_row);
		
		$company_name_to_use = formatCompanyName($lawful_row["CompanyNameThai"],$lawful_row["CompanyTypeCode"]);
		
		if($lawful_row["main_flag"] == 1){
			$amount_to_use = $lawful_row["amount"];
			$text_to_use = "";
		}else{
			$amount_to_use = 0;
			$text_to_use = "จ่ายในใบเสร็จเล่มที่ ".$lawful_row["BookReceiptNo"]." เลขที่ ". $lawful_row["ReceiptNo"];
		}
		
		array_push($address_array,$address_to_use);
		array_push($company_name_array,$company_name_to_use);
		array_push($cid_array,$lawful_row["the_cid"]);
		array_push($amount_array,$amount_to_use);
		array_push($text_array,$text_to_use);
		array_push($employee_array, $lawful_row["Hire_NumofEmp"]);
		
		array_push($company_code_array,$company_row["CompanyCode"]);
		array_push($ratioEmployees_array,$lawfulness_row["ratioEmployees"]);
		
		//yoes 20160119
		array_push($lid_array,$lawful_row["lawfulness_lid"]);
		array_push($province_array,$lawful_row["Province"]); // yoes 20160126
		
	
	}
	
	$holder_amount = 0;
	$holder_text = "";
	
	for($i=0;$i<count($cid_array);$i++){
				
		//if next cid = this cid, then, remember current value just skip this loop
		if($cid_array[$i+1] == $cid_array[$i]){
			$holder_amount += $amount_array[$i];
			$holder_text .=  " ".$text_array[$i];
			
			//echo "<BR>HOLDER AMOUNT: $holder_amount";
			
			continue;
		}
		
		//else just show current info + holder info
		$row_count++;
		
		$amount_to_show = $holder_amount+$amount_array[$i];
		$the_sum_amount += $amount_to_show;
		$text_to_show = $holder_text . " ". $text_array[$i];
		
		if($amount_to_show > 0){
			$formatted_amount = formatMoneyReport($amount_to_show);
		}else{
			$formatted_amount = "";
		}
		
		$the_sum_employee += $employee_array[$i];
  ?>
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $row_count;?></div></td>
        <td width="50" valign="top"><div align="left"><?php echo $company_code_array[$i];?></div></td>
        <td width="177" valign="top"><div align="left"><?php echo $company_name_array[$i];?></div></td>
        <td width="228" valign="top"><div align="left"><?php echo $address_array[$i];?></div></td>
        <td align="right" valign="top"><div align="right"><?php 
					$ratio_employees = getEmployeeRatio( $ratioEmployees_array[$i],$ratio_to_use);
					
					echo $ratio_employees;
					
					$total_ratioEmployees += $ratio_employees;
					
					?></div></td>
        <td width="100" align="right" valign="top"><div align="right"><?php echo formatEmployee($employee_array[$i]);?> </div></td>
        <td width="100" align="right" valign="top">
        
        
        
         
        <?php 
			
			
			
			//yoes 20160119 -- common function to try get payments ratio per 1 lid here --> ineeficient because use 1 query per lid
			//variables here
			$lid_to_get_34 = $lid_array[$i];
			$employees_ratio = $ratio_employees-$employee_array[$i];
			
			//echo "employees_ratio -- $employees_ratio --";
			$year_date = $year_date;
			$the_wage = $the_wage;
			$this_lawful_year = $the_year;			
			$the_province = $province_array[$i]; // yoes 20160126
			
			////////
			//below are script for getting all details by LID
			////////			
			include "scrp_get_34_from_lid.php";
			////////
			//above are script for getting all details by LID
			////////
			
			
						
			//echo $cid_array[$i]."-";	
			
			$total_maimad_paid += $maimad_paid;
			$total_paid_money += $paid_money;
			$total_interest_money += $interest_money;
			$total_extra_money += $extra_money; //yoes 20160201 --> case จ่ายเกิน
			
			
			
			echo formatEmployee($maimad_paid);	
		
		
		
		?>
        
        
        </td>
        <td width="100" align="right" valign="top"><?php echo formatNumber($paid_money);?></td>
        <td width="100" align="right" valign="top"><?php echo formatNumber($interest_money);?></td>
        <td width="100" align="right" valign="top"><?php echo formatNumber($extra_money);?></td>
        <td width="100" align="right" valign="top"><div align="right"><?php echo $formatted_amount . $text_to_show;?></div></td>
      </tr>
      <?php
		$holder_amount = 0;
		$holder_text = "";
	}
  ?>
	  </tbody>
        
        <tfoot>
      <tr>
        <td colspan="4" align="right" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="227" align="right" ><div align="right"><strong><?php echo number_format($total_ratioEmployees,0);?></strong></div></td>
        <td width="100" align="right" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployee($the_sum_employee);?></strong> </div></td>
        <td align="right" style="border-bottom:double;" ><strong>
          <?php 	
			
			echo formatEmployee($total_maimad_paid);				
		
		?>
        </strong></td>
        <td align="right" style="border-bottom:double;" ><strong>
          <?php 
		
			echo formatNumber($total_paid_money);				
				
		
		?>
        </strong></td>
        <td align="right" style="border-bottom:double;" ><strong>
          <?php 
		
			echo formatNumber($total_interest_money);				
			
		?>
        </strong></td>
        <td align="right" style="border-bottom:double;" >
        <strong><?php 
		
			echo formatNumber($total_extra_money);				
			
		?></strong>
        </td>
        <td width="100" align="right" style="border-bottom:double;" ><div align="right"><strong><?php echo formatMoneyReport($the_sum_amount);?></strong> </div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
