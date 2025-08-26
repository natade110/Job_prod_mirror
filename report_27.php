<?php

include "db_connect.php";

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

if($the_year >= 2013){

	$is_2013 = 1;
	//year > 2013 => only concern main branch
	$branch_codition =  " AND BranchCode < 1 ";

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

//yoes 20150604
//extra conditions for ดินแดง
//$province_filter .= " and District LIKE '%ดินแดง%'";

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	$province_table = ", provinces";
	$province_filter = " and Province in (select province_id from provinces where section_id = '".$_POST["Section"]."')";
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}


//other filter
if($_POST["CompanyCode"]) {
    $other_filters .= " and b.CompanyCode like '%".doCleanInput($_POST["CompanyCode"])."%'";
}
if($_POST["CompanyNameThai"]) {
    $other_filters .= " and b.CompanyNameThai like '%".doCleanInput($_POST["CompanyNameThai"])."%'";
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


//yoes 20250102
//other criterias as per ma ข้อ 5.2.6.16
if($_POST["CompanyCode"]){

	$other_filter .= " and CompanyCode like '%". doCleanInput($_POST["CompanyCode"]) ."%'";

}
if($_POST["CompanyNameThai"]){

	$other_filter .= " and CompanyNameThai like '%". doCleanInput($_POST["CompanyNameThai"]) ."%'";

}

//yoes 20161003 --> filter gender
if($_POST["le_gender"] == "m"){

	$other_filter .= " and le_gender = 'm'";

}elseif($_POST["le_gender"] == "f"){

	$other_filter .= " and le_gender = 'f'";

}elseif($_POST["le_gender"] == "n"){

	$other_filter .= " and coalesce(le_gender,'') = ''";

}


////// starts LOGIC here

if($the_year >= 2018 && $the_year <= 2050 && !$other_filters){
	$extra_sql = "
	
			and
				
				le_id not in (
				
					select
						meta_value
					from
						lawful_employees_meta
					where
						meta_for = 'child_of$es_field_name_suffix'
						and
						meta_value != 0
						
				
				)
				
			and
				le_id not in (
				
					select
						meta_leid
					from
						lawful_employees_meta
					where
						meta_for = 'is_extra_33$es_field_name_suffix'
						and
						meta_value = 1
				
				)
	
	";
}

$main_sql = "

			select
			  *
			from
			  lawful_employees a
			  join company b on  a.le_cid = b.CID
			  left outer join provinces c on b.Province = c.province_id
			where
			 
				
				le_year = '$the_year'
				
				$typecode_filter
				
				$province_filter
				
				$other_filter
				
				$branch_codition
				
				$extra_sql
			
			    $other_filters
				
			order by
				province_name, CompanyNameThai asc
				

			";
			
			
//echo $main_sql;			exit();

if($is_pdf || $is_excel){

	$w50 = 50;
	$w75 = 75;
	$w100 = 100;
	$w125 = 125;
	$w350 = 350;
	
}



?>

<div align="center">
            <strong>รายงานการจ้างงานคนพิการในสถานประกอบการตามรายชื่อคนพิการ <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
<table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td width="<?php echo $w50;?>" rowspan="2" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="<?php echo $w100+$w75+$w75+$w75+$w75;?>"  colspan="5" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>รายละเอียดคนพิการ</strong> </div></td>

          <?php if($other_filters){?>
              <td width="<?php echo $w75+$w75+$w75+$w75+$w75+$w75+$w75;?>"  colspan="9" align="center" valign="bottom" ><div align="center"><strong>รายละเอียดการจ้างงานในสถานประกอบการ</strong></div></td>
          <?php }else{ ?>
            <td width="<?php echo $w75+$w75+$w75+$w75+$w75+$w75+$w75;?>"  colspan="8" align="center" valign="bottom" ><div align="center"><strong>รายละเอียดการจ้างงานในสถานประกอบการ</strong></div></td>
          <?php }?>
        </tr>



      <tr >
        <td width="<?php echo $w100;?>" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>เลขบัตรประจำตัวคนพิการ</strong> </div></td>
        <td width="<?php echo $w75;?>" align="center"  valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ชื่อคนพิการ</strong> </div></td>
        <td width="<?php echo $w75;?>" align="center"  valign="bottom"><div align="center" style="vertical-align:middle;"><strong>อายุ (ปี)</strong> </div></td>
        <td width="<?php echo $w75;?>" align="center"  valign="bottom"><div align="center" style="vertical-align:middle;"><strong>เพศ</strong></div></td>
        <td width="<?php echo $w75;?>" align="center"  valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลักษณะความพิการ</strong></div></td>
        <td width="<?php echo $w75;?>" align="center"  valign="bottom" ><div align="center"><strong>เลขทะเบียนนายจ้าง</strong></div></td>
        <td width="<?php echo $w75;?>" align="center"  valign="bottom" ><div align="center"><strong>เลขสาขา</strong></div></td>
        <td width="<?php echo $w75;?>" align="center"  valign="bottom" ><div align="center"><strong>ชื่อสถานประกอบการ</strong></div></td>
        <td width="<?php echo $w75;?>" align="center"  valign="bottom" ><div align="center"><strong>ที่อยู่</strong></div></td>
        <td width="<?php echo $w75;?>" align="center"  valign="bottom" ><div align="center"><strong>วันเริ่มงาน</strong></div></td>

        <?php if($other_filters){?>
        <td width="<?php echo $w75;?>" align="center"  valign="bottom" ><div align="center"><strong>วันที่ออกจากงาน</strong></div></td>
        <?php }?>


        <td width="<?php echo $w75;?>" align="center"  valign="bottom" ><div align="center"><strong>ตำแหน่ง</strong></div></td>
        <td colspan="2" align="center"  valign="bottom" ><div align="center"><strong>เงินเดือน </strong></div></td>
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
          
            <td width="<?php echo $w50;?>"  valign="top"><div align="center"><?php echo $row_count;?></div></td>
            <td width="<?php echo $w100;?>"  valign="top"><div align="left"><?php echo $lawful_row["le_code"]?>&nbsp;</div>          </td>
            <td width="<?php echo $w75;?>"  valign="top"><div align="left"><?php echo $lawful_row["le_name"]?></div></td>
            <td width="<?php echo $w75;?>"  valign="top"><div align="center"><?php echo $lawful_row["le_age"]?></div></td>
            <td  width="<?php echo $w75;?>" valign="top"><div align="left"><?php echo formatGender($lawful_row["le_gender"])?></div></td>
            <td width="<?php echo $w75;?>"  valign="top"><div align="left"><?php echo $lawful_row["le_disable_desc"]?></div></td>
            <td width="<?php echo $w75;?>"  valign="top"><div align="left"><?php echo $lawful_row["CompanyCode"]?></div></td>
            <td width="<?php echo $w75;?>"  valign="top"><div align="left">
			<?php echo default_value($lawful_row["BranchCode"],"")?><?php if($is_excel){echo "&nbsp;";}?></div></td>
            <td width="<?php echo $w75;?>"  valign="top"><div align="left"><?php echo ($company_name_to_use);?></div></td>
            <td width="<?php echo $w75;?>"  valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
            <td width="<?php echo $w75;?>"  valign="top"><div align="left"><?php echo formatDateThai($lawful_row["le_start_date"])?></div></td>
              <?php if($other_filters){?>
                  <td width="<?php echo $w75;?>"  valign="top"><div align="left"><?php echo $lawful_row["le_end_date"]=="0000-00-00"?"":formatDateThai($lawful_row["le_end_date"])?></div></td>
              <?php }?>

            <td width="<?php echo $w75;?>"  valign="top"><div align="left"><?php echo formatPositionGroup($lawful_row["le_position"])?></div></td>
            <td  width="<?php echo $w75;?>" valign="top" style="border-right: none;"><div align="right"><?php echo formatMoneyReport($lawful_row["le_wage"])?></div></td>
            <td  width="<?php echo $w75;?>" valign="top" style="border-left: none;"><div align="center"><?php echo getWageUnit($lawful_row["le_wage_unit"])?></div></td>
            </tr>
     
     <?php }?>
	  </tbody>
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
