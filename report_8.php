<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_8.xls");

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


//yoes 20160127 -- add this here
if($the_year >= 2013){

	$is_2013 = 1;
	//year > 2013 => only concern main branch
	$branch_codition =  " AND company.BranchCode < 1 ";	

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
	
//	$typecode_filter = " and CompanyTypeCode = '14'";
//	$business_type = "หน่วยงานภาครัฐ";
		
//}else{
	$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
//}
$typecode_filter .= " and CompanyTypeCode < 200";

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



//yoes 20160119 -- more variables
$the_wage = getThisYearWage($the_year);
$year_date = 365;

//yoes 20211107
if($_POST[lawful_flag]){		
	$lawful_flag = $_POST[lawful_flag]*1;		
}

?>


<div align="center">
            <strong>รายละเอียดการปฏิบัติตามกฎหมาย ม.33, ม.34 และ ม.35 <?php echo $province_text;?> <?php echo getLawfulText($lawful_flag);?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
  
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="14" align="center" valign="top"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
      </tr>
      <tr >
        <td  rowspan="2" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td rowspan="2" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>เลขทะเบียนนายจ้าง</strong></div></td>
        <td width="117" rowspan="2" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ชื่อ<?php echo $business_type;?></strong> </div></td>
        <td width="168" rowspan="2" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ที่อยู่</strong> </div></td>
        <td width="100" rowspan="2" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>อัตราส่วนที่ต้องรับ<br />
        คนพิการเข้าทํางาน(ราย)</strong></div></td>
        <td width="80" rowspan="2" align="center" valign="middle" ><div align="center"><strong>รับคนพิการ<br />
          ตามมาตรา 33
<br />
(ราย)</strong></div></td>
        <td colspan="5" align="center" valign="top" ><div align="center"><strong>จ่ายเงินแทนรับคนพิการเข้าทำงาน<br />ตามมาตรา 34</strong></div></td>
        <td  colspan="3" align="center" valign="top" ><div align="center"><strong>ให้สัมปทานฯ ตามมาตรา 35</strong></div></td>
        </tr>
      <tr >
        <td align="center" valign="top" ><strong>จ่ายเงินแทน<br />
          รับคนพิการ<br />
          (ราย)</strong></td>
        <td align="center" valign="top" ><strong>เงินต้น<br />
          (บาท)</strong></td>
        <td align="center" valign="top" ><strong>ดอกเบี้ย<br />
          (บาท)</strong></td>
        <td width="100" align="center" valign="top" ><strong>จ่ายเกิน<br />
          (บาท)</strong></td>
        <td align="center" valign="top" ><div align="center"><strong> รวม <br />
          (บาท)</strong></div></td>
        <td width="100" align="center" valign="top" ><div align="center"><strong>คนพิการ (ราย)</strong></div></td>
        <td width="80" align="center" valign="top" ><div align="center"><strong>ผู้ดูแลคนพิการ (ราย)</strong></div></td>
         <td  align="center" valign="top" ><strong>รวม (ราย)</strong></td>
      </tr>
      </thead>
      
      <tbody>
      <?php
	$sub_table = "						
			(
			select 
				sum(receipt.Amount) the_sum
				, receipt.RID the_rid 
				, main_flag
				, ReceiptYear
				, company.cid the_cid 
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
				,lawfulness.LID as the_lid
				
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
				
				$last_modified_sql
				
				$branch_codition
				
				$school_filter
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
					from 
						lawfulness
						, $sub_table a
					where
						lawfulness.CID = a.the_cid
						and lawfulness.Year = a.ReceiptYear
						and LawfulStatus = '$lawful_flag'  
						and Hire_status = '1' 
						and pay_status = '1' 
						and Conc_status = '1' 
					order by 
						CompanyNameThai asc
						
				   ";	
									
	//echo $lawful_sql; exit();									
									
	$lawful_result = mysql_query($lawful_sql);	$address_array = array();
	$company_name_array = array();
	$cid_array = array();
	$lid_array = array();
	$amount_array = array();
	$text_array = array();
	$employee_array = array();
	
	$company_code_array = array();
	$ratioEmployees_array = array();
	
	$province_array = array(); // yoes 20160126
	
	
	while ($lawful_row = mysql_fetch_array($lawful_result)) {
		
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
		array_push($lid_array,$lawful_row["the_lid"]);
		array_push($amount_array,$amount_to_use);
		array_push($text_array,$text_to_use);
		array_push($employee_array, $lawful_row["Hire_NumofEmp"]);
		
		array_push($company_code_array,$lawful_row["CompanyCode"]);
		array_push($ratioEmployees_array,$lawful_row["ratioEmployees"]);
		
		array_push($province_array,$lawful_row["Province"]); // yoes 20160126
	
	}
	
	$holder_amount = 0;
	$holder_text = "";
	
	for($i=0;$i<count($cid_array);$i++){
				
		//if next cid = this cid, then, remember current value just skip this loop
		if($cid_array[$i+1] == $cid_array[$i]){
			$holder_amount += $amount_array[$i];
			$holder_text .=  " ".$text_array[$i];
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
		
		
		//---------
		
		$the_sql = "
												
					select count(*) 
					from 
					curator 
					where 
					curator_lid = '".$lid_array[$i]."' 
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
							curator_lid = '".$lid_array[$i]."' 
						and 
						curator_parent = 0
						and
						curator_is_disable = 1";
		
		$curator_usee = getFirstItem($the_sql);
		
		$total_curator_usee += $curator_usee;
  ?>
      <tr>
        <td  valign="top"><div align="center"><?php echo $row_count;?></div></td>
        <td valign="top"><div align="left"><?php echo $company_code_array[$i];?></div></td>
        <td width="117" valign="top"><div align="left"><?php echo $company_name_array[$i];?></div></td>
        <td width="168" valign="top"><div align="left"><?php echo $address_array[$i];?></div></td>
        <td align="right" valign="top"><div align="right"><?php
		
			 echo $employees_ratio = getEmployeeRatio( $ratioEmployees_array[$i],$ratio_to_use);
			 
			 ?></div></td>
        <td width="80" align="right" valign="top"><div align="right"><?php echo formatEmployee($employee_array[$i]);?> </div></td>
        <td width="80" align="right" valign="top">
        
        <?php 
		
			
			//yoes 20160119 -- common function to try get payments ratio per 1 lid here --> ineeficient because use 1 query per lid
			//variables here
			$sum_user_usee = $curator_usee + $curator_user;
			 
			$lid_to_get_34 = $lid_array[$i];
			$employees_ratio = $employees_ratio - $sum_user_usee -$employee_array[$i];
			$year_date = $year_date;
			$the_wage = $the_wage;
			$this_lawful_year = $this_lawful_year;
			
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
        <td width="80" align="right" valign="top"><?php echo formatNumber($paid_money);?></td>
        <td width="80" align="right" valign="top"><?php echo formatNumber($interest_money);?></td>
        <td width="100" align="right" valign="top"><?php echo formatNumber($extra_money);?></td>
        <td width="100" align="right" valign="top"><div align="right"><?php echo $formatted_amount . $text_to_show;?></div></td>
        <td width="100" align="right" valign="top"><div align="right"><?php echo $curator_usee;?></div></td>
        <td width="80" align="right" valign="top"><div align="right"><?php echo $curator_user;?></div></td>
        <td width="80" align="right" valign="top"><div align="right"><?php echo $sum_user_usee; $total_sum_user_usee += $sum_user_usee;?></div></td>
      </tr>
      <?php
	  	$holder_amount = 0;
		$holder_text = "";
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
        <td  colspan="5" align="right" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="80" align="right" <?php echo $footer_row;?> ><div align="right"><strong><?php echo formatEmployee($the_sum_employee);?></strong> </div></td>
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
        <td align="right" style="border-bottom:double;" ><strong><?php 
		
			echo formatNumber($total_extra_money);				
			
		?></strong></td>
        <td width="100" align="right" <?php echo $footer_row;?> ><div align="right"><strong><?php echo formatMoney($the_sum_amount);?></strong></div></td>
        <td width="100" align="right" <?php echo $footer_row;?> ><div align="right"><strong><?php echo $total_curator_usee;?></strong> </div></td>
        <td width="80" align="right" <?php echo $footer_row;?> ><div align="right"><strong><?php echo $total_curator_user;?></strong></div></td>
        <td width="80" align="right" <?php echo $footer_row;?> ><div align="right"><strong><?php echo formatEmployee($total_sum_user_usee);?></strong></div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
