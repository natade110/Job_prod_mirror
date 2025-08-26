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

if($_POST["CompanyTypeCode"] == "14"){
	
	$typecode_filter = " and CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
}

?>

<div align="center">
            <strong>รายละเอียด<?php echo $business_type;?>ที่ปฏิบัติตามกฎหมายตามมาตรา 35  <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="5"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
      </tr>
      <tr >
        <td width="50" rowspan="2" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="177" rowspan="2" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ชื่อ<?php echo $business_type;?></strong> </div></td>
        <td width="228" rowspan="2" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ที่อยู่</strong> </div></td>
        <td colspan="2" width="200" ><div align="center"><strong>ให้สัมปทานตามมาตรา 35</strong></div></td>
        </tr>
      <tr >
        <td width="100" ><div align="center"><strong>คนพิการ (ราย)</strong></div></td>
        <td width="100" ><div align="center"><strong>ผู้ดูแลคนพิการ (ราย)</strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      <?php
	//generate info
	$lawful_sql = "select 
						
						Hire_NumofEmp
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
						, lawfulness.LID as the_lid
						
						from 
							lawfulness
							, company
							
							 $province_table
						where 
							company.CID = lawfulness.CID
							and LawfulStatus = '1'  
							and Hire_status = '0' 
							and pay_status = '0' 
							and Conc_status = '1' 
							$typecode_filter
							$province_filter
							and Year = '$the_year'
						order by 
							CompanyNameThai asc
									";
									
	//echo $lawful_sql; exit();									
									
	$lawful_result = mysql_query($lawful_sql);	
	while ($lawful_row = mysql_fetch_array($lawful_result)) {
	
	
		$row_count++;
		$the_province_text = formatProvince(getFirstItem("select province_name from provinces where province_id = '".$lawful_row["Province"]."'"));		
		$address_to_use = $lawful_row["Address1"]." ".$lawful_row["Moo"]." ".$lawful_row["Soi"]." ".$lawful_row["Road"]." ".$lawful_row["Subdistrict"]." ".$lawful_row["District"]." ".$the_province_text." ".$lawful_row["Zip"];
	
		$the_sum += $lawful_row["Hire_NumofEmp"];
		
		$the_sql = "
												
					select count(*) 
					from 
					curator 
					where 
					curator_lid = '".$lawful_row["the_lid"]."' 
					and curator_parent = 0
					and
					curator_is_disable = 0
				
				";
		
		$curator_user = getFirstItem($the_sql);	
		$total_curator_user += $curator_user;
		
		
		
		$the_sql = "
												
					select count(*) 
					from 
					curator 
					where 
					curator_lid = '".$lawful_row["the_lid"]."' 
					and curator_parent = 0
					and
					curator_is_disable = 1
				
				";
		
		$curator_usee = getFirstItem($the_sql);		
		$total_curator_usee += $curator_usee;
		//echo $the_sql;
		
				
  ?>
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $row_count;?></div></td>
        <td width="177" valign="top"><div align="left"><?php echo formatCompanyName($lawful_row["CompanyNameThai"],$lawful_row["CompanyTypeCode"]);?></div></td>
        <td width="228" valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
        <td width="100" valign="top"><div align="right"><?php echo $curator_usee;?></div></td>
        <td width="100" valign="top"><div align="right"><?php echo $curator_user;?></div></td>
      </tr>
      <?php
	}
  ?>
	  </tbody>
        
        <tfoot>
      <tr>
        <td colspan="3" width="455" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="100" style="border-bottom:double;" ><div align="right"><strong><?php echo $total_curator_usee;?></strong></div></td>
        <td width="100" style="border-bottom:double;" ><div align="right"><strong><?php echo $total_curator_user;?></strong></div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
