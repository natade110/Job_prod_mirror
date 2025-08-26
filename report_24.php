<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_24.xls");

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

if($the_year > 2012){

	$is_2013 = 1;
	
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

if($_POST["CompanyTypeCode"] == "14"){
	
	//$typecode_filter = " and CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
}







////// starts LOGIC here

if($is_2013){
	
	$new_condition .= " and branchCode < 1";
}

$the_employees_limit = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_$this_year'"),100);

$main_sql = "

				SELECT
				  *
				  , lawfulness.Employees as lawful_employees
				FROM
				  company
				  	join lawfulness on company.CID = lawfulness.CID
					
					
				  , provinces
				  
				  
				
				WHERE
				
				 
				  
				  (
				  	lawfulness.Year = '$the_year'
					
				  )
					
				  
				  and
				  company.Province = provinces.province_id
				  and
				  (
					
					lawfulness.employees < $the_employees_limit
				  )
				  
				  $typecode_filter				  
				  $province_filter
				  
				  $new_condition
				  
				  order by province_name, companyNameThai asc

			";
			


if($is_2013){


		$main_sql = "

				SELECT
				  *
				  , lawfulness.Employees as lawful_employees
				FROM
				  company
				  	join lawfulness on company.CID = lawfulness.CID
					
					
				  left outer join provinces on  company.Province = provinces.province_id
				  
				 
				  
				
				WHERE
				
				   BranchCode < 1 
				   
				   and lawfulness.LawfulStatus = '3' 
				 
				 	and
				  
				  (
				  	lawfulness.Year = '$the_year'
					
				  )
					
				  
				  
				  and
				  (
					
					lawfulness.employees < '$the_employees_limit'
				  )
				  
				  $typecode_filter				  
				  $province_filter
				  
				  $new_condition
				  
				  
				  
				  order by province_name, companyNameThai asc

			";

}
//echo $main_sql;			//exit();





?>

<div align="center">
            <strong>สถานประกอบการที่ไม่เข้าข่ายจำนวนลูกจ้างตามกฎหมาย <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td <?php if($is_pdf){?>width="50"<?php }?> rowspan="2" valign="bottom"><br />          <div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td <?php if($is_pdf){?>width="100"<?php }?> rowspan="2" valign="bottom"><br />          <div align="center" style="vertical-align:middle;"><strong>เลขที่บัญชีนายจ้าง</strong> </div></td>
        <td <?php if($is_pdf){?>width="150"<?php }?> rowspan="2" valign="bottom"><br />
          <div align="center" style="vertical-align:middle;"><strong>ชื่อสถานประกอบการ</strong> </div></td>
        <td <?php if($is_pdf){?>width="150"<?php }?> rowspan="2" valign="bottom"><br />
            <div align="center" style="vertical-align:middle;"><strong>ที่ตั้ง</strong></div></td>
        <td width="240" colspan="2" valign="top"><div align="center" style="vertical-align:middle;"><strong>ไม่เข้าข่ายตามกฎหมาย</strong> </div></td>
        </tr>
      <tr >
        <td width="120" ><div align="center"><strong>จำนวนลูกจ้าง<br />
(ราย)</strong></div></td>
        <td width="120" ><div align="center"><strong>อื่นๆ</strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      
      
      <?php
		  $lawful_result = mysql_query($main_sql);	
		  
		  while ($lawful_row = mysql_fetch_array($lawful_result)) {
		  
			$row_count++;
			$company_name_to_use = formatCompanyName($lawful_row["CompanyNameThai"],$lawful_row["CompanyTypeCode"]);
			
			$address_to_use = getAddressText($lawful_row);
			//
			
	  ?>
      
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $row_count;?></div></td>
        <td width="100" valign="top"><div align="left"><?php echo $lawful_row["CompanyCode"]?></div></td>
        <td width="150" valign="top"><div align="left"><?php echo $company_name_to_use;?></div></td>
        <td width="150" valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
        <td width="120" valign="top"><div align="right"><?php echo $lawful_row["lawful_employees"]?></div></td>
        <td width="120" valign="top"><div align="left"><?php if($lawful_row["Status"] == 0){echo "ปิดกิจการ";}else{echo "&nbsp;";}?></div></td>
      </tr>
      
      
      <?php }?>
      
      
      
	  </tbody>
        
        
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
