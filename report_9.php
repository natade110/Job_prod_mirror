<?php

include "db_connect.php";
set_time_limit(300);

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_9.xls");

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



if($the_year >= 2013){

	$is_2013 = 1;	
	//year > 2013 => only concern main branch
	$branch_codition =  " AND BranchCode < 1 ";

}

//yoes 20160614 -- start to use common includes here
include "report_school_filter.inc.php";


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
	//$business_type = "หน่วยงานภาครัฐ";
		
}else{
	//$typecode_filter = " and CompanyTypeCode != '14'";
	//$business_type = "สถานประกอบการ";
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


?>

<div align="center">
            <strong>รายละเอียด<?php echo $the_company_word;?>ที่ไม่ปฏิบัติตามกฎหมาย <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="5" align="center" valign="top"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
      </tr>
      <tr >
        <td width="50" align="center" valign="top"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="300" align="center" valign="top"><div align="center" style="vertical-align:middle;"><strong><?php echo $business_code;?></strong></div></td>
		<td width="300" align="center" valign="top"><div align="center" style="vertical-align:middle;"><strong>เลขที่สาขา</strong></div></td>
        <td width="277" align="center" valign="top"><div align="center" style="vertical-align:middle;"><strong>ชื่อ<?php echo $business_type;?></strong> </div></td>
        <td width="328" align="center" valign="top"><div align="center" style="vertical-align:middle;"><strong>ที่อยู่</strong> </div></td>
        <td width="100" align="center" valign="top"><div align="center" style="vertical-align:middle;"><strong>อัตราส่วนที่ต้องรับ<br />
        คนพิการเข้าทํางาน(ราย)</strong></div></td>
        
        </tr>
      </thead>
      
      <tbody>
      <?php
	//generate info
	$lawful_sql = "select 
						
						 company.CID
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
						, BranchCode
						
						, CompanyCode
						, IF(lawfulness.Employees > 0, lawfulness.Employees, company.Employees) as ratioEmployees
				
						
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
							
							$school_filter
							$CompanyType_filter
						order by 
							CompanyNameThai asc
									";
									
	//echo $lawful_sql; //exit();									
									
	$lawful_result = mysql_query($lawful_sql);	
	while ($lawful_row = mysql_fetch_array($lawful_result)) {
		$row_count++;
		$the_province_text = formatProvince(getFirstItem("select province_name from provinces where province_id = '".$lawful_row["Province"]."'"));		
		//$address_to_use = $lawful_row["Address1"]." ".$lawful_row["Moo"]." ".$lawful_row["Soi"]." ".$lawful_row["Road"]." ".$lawful_row["Subdistrict"]." ".$lawful_row["District"]." ".$the_province_text." ".$lawful_row["Zip"];
		$address_to_use = getAddressText($lawful_row);
	
		
  ?>
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $row_count;?></div></td>
        <td valign="top">
        <div align="left"><?php echo $lawful_row["CompanyCode"];?></div>
        </td>
		<td valign="top">
        <div align="left"><?php echo $lawful_row["BranchCode"];?></div>
        </td>
        <td width="277" valign="top"><div align="left"><?php echo formatCompanyName($lawful_row["CompanyNameThai"],$lawful_row["CompanyTypeCode"]);?></div></td>
        <td width="328" valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
        <td align="right" valign="top"><div align="right"><?php 
					$ratio_employees = getEmployeeRatio( $lawful_row["ratioEmployees"],$ratio_to_use);
					
					echo $ratio_employees;
					
					$total_ratioEmployees += $ratio_employees;
					
					?></div></td>
        
      </tr>
     
      <?php
	}
  ?>
   <tr>
        <td colspan="4" align="right" valign="top"><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
      <td align="right" valign="top"><div align="right"><?php echo number_format($total_ratioEmployees,0);?></div></td>
      </tr>
  
	  </tbody>
        
        
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
