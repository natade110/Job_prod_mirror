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
	
	$typecode_filter = " and CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
}



///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter .= " and CompanyTypeCode >= 200  and CompanyTypeCode < 300";

	
}else{
	
	$typecode_filter .= " and CompanyTypeCode < 200";
	
}





//ratio thing
$ratio_to_use = default_value(getFirstItem("select var_value 
											from vars where var_name = 'ratio_$the_year'"),100);
						
$half_ratio_to_use = $ratio_to_use/2;

$sub_table_sql = "

			select 
				if(lawfulness.Employees is null, company.Employees,lawfulness.Employees) the_employees
			from 
				company				
				
			JOIN
				lawfulness on lawfulness.cid = company.cid
				and year = '$the_year'
				
				$province_table
				$typecode_filter
				
			where 
				CompanyTypeCode != '14'
				$province_filter
			"; //echo $sub_table_sql; exit();

$employee_1_sql = "select 
						sum(
							if(the_employees < $ratio_to_use
									, 0 
									, if(
											the_employees % $ratio_to_use <= $half_ratio_to_use
											, floor(the_employees/$ratio_to_use)
											, ceil(the_employees/$ratio_to_use)
										
										)
											)
							)
					from
						($sub_table_sql) sub_table
						
					"; //echo $employee_1_sql; exit();
					
$this_employee_1 = default_value(getFirstItem($employee_1_sql),0);

//--------------

$sub_table_sql = "

			select 
				if(lawfulness.Employees is null, company.Employees,lawfulness.Employees) the_employees
			from 
				company				
				
			JOIN
				lawfulness on lawfulness.cid = company.cid
				and year = '$the_year'
				
				$province_table
			where 
				CompanyTypeCode = '14'
				$province_filter
			"; //echo $sub_table_sql; exit();

$employee_2_sql = "select 
						sum(
							if(the_employees < $ratio_to_use
									, 0 
									, if(
											the_employees % $ratio_to_use <= $half_ratio_to_use
											, floor(the_employees/$ratio_to_use)
											, ceil(the_employees/$ratio_to_use)
										
										)
											)
							)
					from
						($sub_table_sql) sub_table
						
					"; //echo $employee_2_sql; exit();
					
$this_employee_2 = default_value(getFirstItem($employee_2_sql),0);		

?>

<div align="center">
            <strong>สรุปอัตราส่วนที่สถานประกอบการและหน่วยงานภาครัฐจะต้องรับคนพิการเข้าทำงาน <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="3" align="center" valign="top"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
      </tr>
      <tr >
        <td width="50" align="center" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="485" align="center" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ประเภท</strong> </div></td>
        <td width="120" align="center" valign="top" ><div align="center"><strong>ทำงานใน<br />
          สถานประกอบการ<br />
(ราย)</strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      
      <tr>
        <td width="50" valign="top"><div align="center">1</div></td>
        <td width="485" valign="top"><div align="left">สถานประกอบการ
</div>          </td>
        <td width="120" align="right" valign="top"><div align="right"><?php echo formatEmployee($this_employee_1);?> </div></td>
      </tr>
      <tr>
        <td width="50" valign="top"><div align="center">2</div></td>
        <td width="485" valign="top"><div align="left">หน่วยงานภาครัฐ
</div>          </td>
        <td width="120" align="right" valign="top"><div align="right"><?php echo formatEmployee($this_employee_2);?> </div></td>
      </tr>
	  </tbody>
        
        <tfoot>
      <tr>
        <td colspan="2" align="right" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="120" align="right" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployee($this_employee_1 + $this_employee_2);?></strong> </div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
