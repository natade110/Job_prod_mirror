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

if($_POST["CompanyTypeCode"] == "14"){
	
	//$typecode_filter = " and CompanyTypeCode = '14'";
	//$typecode_filter .= " and CompanyTypeCode >= 200  and CompanyTypeCode < 300";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	//$typecode_filter = " and CompanyTypeCode != '14'";
	//$typecode_filter = " and CompanyTypeCode < 200";
	$business_type = "สถานประกอบการ";
	$business_code = "เลขทะเบียนนายจ้าง";
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

//yoes 20211102
if($_POST[lawful_flag]){	
	
	$lawful_flag = $_POST[lawful_flag]*1;	
	
}


?>

<div align="center">
            <strong>รายละเอียดการปฏิบัติตามกฎหมาย ม.33 <?php echo $the_company_word;?> <?php echo $province_text;?> <?php echo getLawfulText($lawful_flag);?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="6" align="center"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
      </tr>
      <tr >
        <td valign="top" width="50" align="center"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td valign="top" width="50" align="center"><div align="center" style="vertical-align:middle;"><strong><?php echo $business_code;?></strong></div></td>
        <td width="227" valign="top" align="center"><div align="center" style="vertical-align:middle;"><strong>ชื่อ<?php echo $business_type;?></strong> </div></td>
        <td valign="top" width="278" align="center"><div align="center" style="vertical-align:middle;"><strong>ที่อยู่</strong> </div></td>
        <td valign="top" align="center">
        <div align="center" style="vertical-align:middle;"><strong>อัตราส่วนที่ต้องรับ<br />คนพิการเข้าทํางาน(ราย)</strong> </div>
        
        
        </td>
        <td width="100" valign="top" align="center"><div align="center"><strong>รับคนพิการ<br />
        ตามมาตรา 33<br />(ราย)</strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      <?php
	
	$lawful_sql = "select 
							*
							, IF(lawfulness.Employees > 0, lawfulness.Employees, company.Employees) as ratioEmployees
							
						from 
								lawfulness 
								,company
								$province_table
						where 
							company.CID = lawfulness.CID  
							and LawfulStatus = '$lawful_flag'  
							$province_filter
							and Hire_status = '1' 
							and pay_status = '0' 
							and Conc_status = '0' 
							$typecode_filter
							and Year = '$the_year'
							
							$last_modified_sql
							
							$branch_codition
							
							$school_filter
							
							$CompanyType_filter
							
							
							
						order by CompanyNameThai asc
									";
									
	//echo $lawful_sql; // exit();									
									
	$lawful_result = mysql_query($lawful_sql);	
	while ($lawful_row = mysql_fetch_array($lawful_result)) {
		
		//print_r($lawful_row);
		
		$row_count++;
		
		$the_province_text = formatProvince(getFirstItem("select province_name from provinces where province_id = '".$lawful_row["Province"]."'"));		
		//$address_to_use = $lawful_row["Address1"]." ".$lawful_row["Moo"]." ".$lawful_row["Soi"]." ".$lawful_row["Road"]." ".$lawful_row["Subdistrict"]." ".$lawful_row["District"]." ".$the_province_text." ".$lawful_row["Zip"];
		$address_to_use = getAddressText($lawful_row);
	
	
		$this_row_ratio = getEmployeeRatio( $lawful_row["ratioEmployees"],$ratio_to_use);
	
		$the_sum_ratio += $this_row_ratio;
		$the_sum += $lawful_row["Hire_NumofEmp"];
  ?>
      <tr>
        <td width="50" valign="top" align="center"><div align="center"><?php echo $row_count;?></div></td>
        <td width="50" valign="top"><div align="left"><?php echo $lawful_row["CompanyCode"];?></div></td>
        <td width="227" valign="top"><div align="left"><?php echo formatCompanyName($lawful_row["CompanyNameThai"],$lawful_row["CompanyTypeCode"]);?></div></td>
        <td width="278" valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
        <td align="right" valign="top"><div align="right"><?php echo $this_row_ratio;?></div></td>
        <td width="100" align="right" valign="top"><div align="right"><?php echo formatEmployee($lawful_row["Hire_NumofEmp"]);?> </div></td>
      </tr>
      <?php
	}
  ?>
	  </tbody>
        
        <tfoot>
      <tr>
        <td width="555" colspan="4" align="right" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
		<td width="100" align="right" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployee($the_sum_ratio);?></strong> </div></td>
        <td width="100" align="right" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployee($the_sum);?></strong> </div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
