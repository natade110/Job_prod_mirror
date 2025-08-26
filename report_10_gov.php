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
	
	$wanted_province = $_POST["Province"];
}

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	
	$wanted_section = $_POST["Section"];

	$province_table = ", provinces";
	$province_filter = " and company.Province = provinces.province_id and provinces.section_id = '".$_POST["Section"]."'";
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}

if($_POST["CompanyTypeCode"] == "14"){
	
	//$wanted_typeCode = $_POST["CompanyTypeCode"];
	//$typecode_filter = " and CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{

	//$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "หน่วยงานภาครัฐ";
}

///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter .= " and CompanyTypeCode >= 200  and CompanyTypeCode < 300";
	$business_type = "หน่วยงานภาครัฐ";
	$business_code = "เลขทะเบียนหน่วยงาน";
	
	if($_POST["CompanyTypeCode"]){
		
		$typecode_filter .= " and CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'";
		$the_company_word = getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'");
			
	}
	
}else{
	
	$typecode_filter .= " and CompanyTypeCode < 200";
	
}






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
			CID in (
			
				select mod_cid from modify_history where mod_type = 1
				
				$filter_from
				$filter_to			
			)	
			";	
}

?>

<div align="center">
            <strong>รายละเอียด<?php echo $the_company_word;?> ปฏิบัติไม่ครบตามอัตราส่วน <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="8"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
        </tr>
      <tr >
        <td width="50" rowspan="2" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td rowspan="2" valign="middle"><div align="center" style="vertical-align:middle;"><strong><?php echo $business_code;?></strong></div></td>
        <td width="117" rowspan="2" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ชื่อ<?php echo $business_type;?></strong> </div></td>
        <td width="168" rowspan="2" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ที่อยู่</strong> </div></td>
        <td rowspan="2" valign="middle"><div align="center" style="vertical-align:middle;"><strong>อัตราส่วนที่ต้องรับ<br />
          คนพิการเข้าทํางาน(ราย)</strong></div></td>
        <td width="80" rowspan="2" ><div align="center"><strong>รับคนพิการ<br />
          ตามมาตรา 33
<br />
(ราย)</strong></div></td>
        <td colspan="2" width="160" ><div align="center"><strong>ให้สัมปทานฯ ตามมาตรา 35</strong></div></td>
        </tr>
      <tr >
        <td width="160" ><div align="center"><strong>คนพิการ (ราย)</strong></div></td>
        <td width="80" ><div align="center"><strong>ผู้ดูแลคนพิการ (ราย)</strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      <?php
	//generate info
	$sub_table = "						
			(
			select 
				company.cid the_cid 
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
				
				, CompanyCode
				, IF(lawfulness.Employees > 0, lawfulness.Employees, company.Employees) as ratioEmployees
				
				
			from 
				 company
				, lawfulness
				  $province_table
			where
				lawfulness.CID = company.CID
				
				$province_filter
				
				$typecode_filter
				
			group by
				company.cid
			)
			";
	  
	//generate info
	$lawful_sql = "
					
					
					select 
						*
						, lawfulness.CID as the_company_id
						, lawfulness.LID as the_lid
					from 
						lawfulness						
					
						join
						$sub_table a
						
						on 
						
						lawfulness.CID = a.the_cid
						
					
					where
						
						lawfulness.Year = '$the_year'
						and LawfulStatus = '2'  
						
						$last_modified_sql
						
						
						
					order by 
						CompanyNameThai asc
						
				   ";	
									
	//echo $lawful_sql; //exit();									
									
	$lawful_result = mysql_query($lawful_sql);	
	$address_array = array();
	$company_name_array = array();
	$cid_array = array();
	$lid_array = array();
	$amount_array = array();
	$text_array = array();
	$employee_array = array();
	
	$company_code_array = array();
	$ratioEmployees_array = array();
	
	
	while ($lawful_row = mysql_fetch_array($lawful_result)) {
		
		
		//just skip this if province is not in desired province
		
		//print_r($lawful_row);echo $lawful_row[2];exit();
		
		//prepare rows as array...
		//echo "select * from company where cid = '".$lawful_row[2]."'"; exit();
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
		
		//just skip this if province is not in desired province
		if($wanted_province && $company_row["Province"] != $wanted_province){
			continue;
		}
		
		if($wanted_section && $company_row["section_id"] != $wanted_section){
			continue;
		}
		
		if($wanted_typeCode == 14 && $company_row["CompanyTypeCode"] != 14){
			continue;
		}elseif($wanted_typeCode != 14 && $company_row["CompanyTypeCode"] == 14){
			continue;
		}
		
		
		$the_province_text = formatProvince(getFirstItem("select province_name from provinces where province_id = '".$company_row["Province"]."'"));		
		$address_to_use = $company_row["Address1"]." ".$company_row["Moo"]." ".$company_row["Soi"]." ".$company_row["Road"]." ".$company_row["Subdistrict"]." ".$company_row["District"]." ".$the_province_text." ".$company_row["Zip"];
		
		
		
		$company_name_to_use = formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]);
		
		if($lawful_row["main_flag"] == 1){
			$amount_to_use = $lawful_row["amount"];
			$text_to_use = "";
		}elseif($lawful_row["main_flag"]){
			$amount_to_use = 0;
			$text_to_use = "จ่ายในใบเสร็จเล่มที่ ".$lawful_row["BookReceiptNo"]." เลขที่ ". $lawful_row["ReceiptNo"];
		}else{
			$amount_to_use = "0";
			$text_to_use = "-";
		}
		
		array_push($address_array,$address_to_use);
		array_push($company_name_array,$company_name_to_use);
		array_push($cid_array,$lawful_row["the_cid"]);
		array_push($lid_array,$lawful_row["the_lid"]);
		array_push($amount_array,$amount_to_use);
		array_push($text_array,$text_to_use);
		array_push($employee_array, $lawful_row["Hire_NumofEmp"]);
		
		array_push($company_code_array,$company_row["CompanyCode"]);
		array_push($ratioEmployees_array,$lawfulness_row["ratioEmployees"]);
	
	}
	
	$holder_amount = 0;
	$holder_text = "";
	
	for($i=0;$i<count($cid_array);$i++){
				
		//if next cid = this cid, then, remember current value just skip this loop
		if($cid_array[$i+1] == $cid_array[$i] && $cid_array[$i] && $cid_array[$i+1]){
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
		
		//echo $the_sql;
		
		$curator_usee = getFirstItem($the_sql);
		
		$total_curator_usee += $curator_usee;
		
  ?>
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $row_count;?></div></td>
        <td valign="top"><div align="left"><?php echo $company_code_array[$i];?></div></td>
        <td width="117" valign="top"><div align="left"><?php echo $company_name_array[$i];?></div></td>
        <td width="168" valign="top"><div align="left"><?php echo $address_array[$i];?></div></td>
        <td valign="top"><div align="right"><?php 
		
		
		$ratio_employees = getEmployeeRatio( $ratioEmployees_array[$i],$ratio_to_use);
		
		echo $ratio_employees;
		
		$total_ratioEmployees += $ratio_employees;
				
		
		?></div></td>
        <td width="80" valign="top"><div align="right"><?php echo formatEmployee($employee_array[$i]);?> </div></td>
        <td width="160" valign="top"><div align="right"><?php echo $curator_usee;?></div></td>
        <td width="80" valign="top"><div align="right"><?php echo $curator_user;?></div></td>
      </tr>
      <?php
	  $holder_amount = 0;
		$holder_text = "";
	}
	
	
	//print_r($lid_array);
	
  ?>
	  </tbody>
        
        <tfoot>
      <tr>
        <td colspan="4"><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="167"><div align="right"><strong><div align="right"><?php echo number_format($total_ratioEmployees,0);?></div></strong></div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployeeReport($the_sum_employee);?></strong> </div></td>
        <td width="160" <?php echo $footer_row;?> ><div align="right"><strong><?php echo $total_curator_usee;?></strong> </div></td>
        <td width="80" <?php echo $footer_row;?> ><div align="right"><strong><?php echo $total_curator_user;?></strong></div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
