<?php

include "db_connect.php";


//print_r($_POST);

///
$report_type = $_POST["report_type"];
///

if($report_type  == 33){

	$report_type_filter = " and the_type = 33";

}elseif($report_type  == 35){

	$report_type_filter = " and the_type = 35";
	
}


if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_24.xls");
	$is_excel = 1;

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
	$province_filter = " and Province = '".$_POST["Province"]."'";
	
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	$province_table = ", provinces";
	$province_filter = " and Province in (select province_id from provinces where section_id = '".$_POST["Section"]."')";
	
	
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}

if($_POST["CompanyTypeCode"] == "14"){
	
	//$typecode_filter = " and CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	//$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
}


///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter .= " and z.CompanyTypeCode >= 200  and z.CompanyTypeCode < 300";
	
	if($_POST["CompanyTypeCode"]){
		
		$typecode_filter .= " and z.CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'";
		$the_company_word = getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'");
	}
	
}else{
	
	$typecode_filter .= " and z.CompanyTypeCode < 200";
	
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
			z.CID in (
			
				select mod_cid from modify_history where mod_type = 1
				
				$filter_from
				$filter_to			
			)	
			";	
}





////// starts LOGIC here


$main_sql = "

				  select
					
					the_code
					
				  from
				
				  (
					select
					le_code as the_code
					, le_name as the_name
					, 'l' as the_type
				  from
					lawful_employees
				
					union
				
					select
					  curator_idcard as the_code
					  , curator_name as the_name
					  , 'c' as the_type
					from
					  curator
				
					  )                    a
				
				
				group by the_code
				having count(the_code) > 1
				
				order by the_code asc
				


			";
			
			
//echo $main_sql;			


if($is_pdf || $is_excel){

	$w50 = 50;
	$w75 = 75;
	$w100 = 100;
	$w125 = 125;
	$w350 = 350;
	
}


?>

<div align="center">
            <strong>รายงานคนพิการปฏิบัติตามกฎหมายซ้ำซ้อน <?php echo $the_company_word;?> <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td width="<?php echo $w50;?>" rowspan="2"  valign="bottom"><div align="center" style="vertical-align:bottom;"><strong>ลำดับที่ </strong></div></td>
        <td width="<?php echo $w350+25;?>" colspan="5"  valign="top"><div align="center" style="vertical-align:middle;"><strong>รายละเอียดผู้ใช้สิทธิ</strong> </div></td>
        <td width="<?php echo $w75;?>" rowspan="2" valign="bottom" ><div align="center" style="vertical-align:bottom;"><strong>การปฏิบัติ<?php if(!$is_pdf){?><br /><?php }?>ตามกฎหมาย</strong></div></td>
        <td width="<?php echo $w75 +$w100+$w75+$w75+$w75+$w50;?>" colspan="5" valign="top" ><div align="center"><strong>รายละเอียดหน่วยงานภาครัฐ</strong></div></td>
        </tr>
      <tr >
        <td width="<?php echo $w100;?>" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>เลขที่บัตรประชาชน</strong> </div></td>
        <td width="<?php echo $w75;?>" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ชื่อ - นามสกุล</strong> </div></td>
        <td width="<?php echo $w75;?>" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>อายุ (ปี)</strong> </div></td>
        <td width="<?php echo $w125;?>" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ประเภทความพิการ</strong></div></td>
        <td width="<?php echo $w50;?>" valign="bottom" ><div align="center"><strong>สถานะ</strong></div></td>
        <td width="<?php echo $w75;?>" valign="bottom" ><div align="center"><strong>ชื่อหน่วยงาน</strong></div></td>
        <td width="<?php echo $w75;?>" valign="bottom" ><div align="center"><strong>ที่ตั้ง</strong></div></td>
        <td width="<?php echo $w75;?>" valign="bottom" ><div align="center"><strong>วันเริ่มงาน/เริ่มสัญญา</strong></div></td>
        <td width="<?php echo $w100;?>" valign="bottom" ><div align="center"><strong>ตำแหน่งงาน/ลักษณะสัมปทาน</strong></div></td>
        <td width="<?php echo $w75;?>" valign="bottom" ><div align="center"><strong>เงินเดือน/มูลค่าสัมปทาน </strong></div></td>
        </tr>
      </thead>
      
      <tbody>
      
      
       <?php
		  $lawful_result = mysql_query($main_sql);	
		  
		  while ($lawful_row = mysql_fetch_array($lawful_result)) {
		  
			
			$this_the_code = $lawful_row["the_code"];
			
			//for each code, get everyone in that code...
			$the_code_sql = "
			
			
			
								
						select
						  *
						from
						
						(
						
						select
						
							le_code as the_code
							, le_name as the_name
							, le_age as the_age
							, le_disable_desc as the_desc
							, '33' as the_type
							, le_cid as the_cid
							
							, le_start_date as the_date
							, le_position as the_position
							, le_wage as the_wage
							, le_year as the_year
						
						 from 
						 	lawful_employees a
							, company z
							
							
						 where 
						 	le_code = '$this_the_code'
							and
							le_cid = CID
						
							$province_filter
							
							$last_modified_sql
							
							$typecode_filter
						
						 union
						
						
						 select
						
							curator_idcard as the_code
							, curator_name as the_name
							, curator_age as the_age
							, curator_disable_desc as the_dsc
							, '35' as the_type
							, b.cid as the_cid
							, '' as the_date
							, '' as the_position
							, '' as the_wage
							, Year as the_year
						
						 from
						
						
						  curator a
						  , lawfulness b
						  , company z
						
						  where
							curator_idcard = '$this_the_code'
						
							and
							a.curator_lid = b.LID
							
							and
							b.CID = z.CID
						
							$province_filter
							
							$last_modified_sql
						
							$typecode_filter
							
						
						
						)a
						
						
						where the_year = '$the_year'
						
						$report_type_filter
						
						order by the_code asc
						
						
			
			
			";
			$the_code_result = mysql_query($the_code_sql);
			//echo $the_code_sql;
			
			
			while ($the_code_row = mysql_fetch_array($the_code_result)) {
			
				$row_count++;
			
				if($the_code_row["the_type"] == 33){				
					$this_type = "มาตรา 33";				
				}else{
					$this_type = "มาตรา 35";
				}
				
				
				//get company info
				$company_sql = "select * from company where CID = '". $the_code_row["the_cid"] ."'";
				
				$company_row = getFirstRow($company_sql);
				
				
				$company_name_to_use = formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]);
			
				$address_to_use = getAddressText($company_row);
			
	  ?>
      <!--
      	<tr >
        <td rowspan="2" width="<?php echo $w50;?>" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="<?php echo $w350;?>"  colspan="5"  valign="top"><div align="center" style="vertical-align:middle;"><strong>รายละเอียดผู้ใช้สิทธิ</strong> </div></td>
        <td width="<?php echo $w100;?>" rowspan="2" valign="bottom" ><div align="center"><strong>การปฏิบัติตามกฎหมาย</strong></div></td>
        <td width="<?php echo $w75 +$w100+$w75+$w75+$w75+$w50;?>" colspan="5" valign="top" ><div align="center"><strong>รายละเอียดสถานประกอบการ</strong></div></td>
        </tr>
      <tr >
        <td width="<?php echo $w100;?>" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>เลขที่บัตรประชาชน</strong> </div></td>
        <td width="<?php echo $w75;?>" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ชื่อ - นามสกุล</strong> </div></td>
        <td width="<?php echo $w75;?>" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>อายุ (ปี)</strong> </div></td>
        <td width="<?php echo $w100;?>" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ประเภทความพิการ</strong></div></td>
        <td width="<?php echo $w50;?>" valign="bottom" ><div align="center"><strong>สถานะ</strong></div></td>
        <td width="<?php echo $w75;?>" valign="bottom" ><div align="center"><strong>ชื่อสถานประกอบการ</strong></div></td>
        <td width="<?php echo $w75;?>" valign="bottom" ><div align="center"><strong>ที่ตั้ง</strong></div></td>
        <td width="<?php echo $w75;?>" valign="bottom" ><div align="center"><strong>วันเริ่มงาน/เริ่มสัญญา</strong></div></td>
        <td width="<?php echo $w100;?>" valign="bottom" ><div align="center"><strong>ตำแหน่งงาน/ลักษณะสัมปทาน</strong></div></td>
        <td width="<?php echo $w75;?>" valign="bottom" ><div align="center"><strong>เงินเดือน/มูลค่าสัมปทาน </strong></div></td>
        </tr>-->
      
                  <tr>
                    <td width="<?php echo $w50;?>" valign="top"><div align="center"><?php  echo $row_count; ?></div></td>
                    <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo $the_code_row["the_code"];?>&nbsp;</div></td>
                    <td width="<?php echo $w75;?>" valign="top"><div align="left"><?php echo $the_code_row["the_name"];?></div></td>
                    <td width="<?php echo $w75;?>" valign="top"><div align="left"><?php echo $the_code_row["the_age"];?></div></td>
                    <td width="<?php echo $w125;?>"  valign="top"><div align="left"><?php echo $the_code_row["the_desc"];?></div></td>
                    <td width="<?php echo $w75;?>"  valign="top">&nbsp;</td>
                    <td width="<?php echo $w50;?>"  valign="top"><div align="center"><?php echo $this_type;?></div></td>
                    <td width="<?php echo $w75;?>"  valign="top"><div align="left"><?php echo $company_name_to_use;?></div></td>
                    <td width="<?php echo $w75;?>"  valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
                    <td width="<?php echo $w75;?>" valign="top"><div align="left"><?php if(strlen($the_code_row["the_date"])>1){echo formatDateThai($the_code_row["the_date"]);}else{echo "&nbsp;";}?></div></td>
                    <td width="<?php echo $w100;?>"  valign="top"><div align="left"><?php echo default_value($the_code_row["the_position"],"&nbsp;");?></div></td>
                    <td width="<?php echo $w75;?>"  valign="top"><div align="right"><?php echo default_value(formatNumberReport($the_code_row["the_wage"]),"&nbsp;");?></div></td>
                  </tr>
                    
                    
     		<?php }  ?>
            
     <?php } ?>
     
	  </tbody>
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
