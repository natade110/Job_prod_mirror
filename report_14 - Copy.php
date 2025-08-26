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

$the_year = date("Y");
$the_month = date("m");

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


if($_POST["AccessLevel"] > 0){

	$filter_access_level = " and AccessLevel = '".$_POST["AccessLevel"]."'";
}


if(strlen($_POST["user_name"]) > 0){

	$filter_user_name = " and user_name like '%".doCleanInput($_POST["user_name"])."%'";
}


$the_year_to_use = formatYear($the_year);


$header_text = "ของแต่ละผู้ใช้งานระบบ";
if($_POST["AccessLevel"] == "1"){
	$header_text = "ของผู้ดูแลระบบ";
}
if($_POST["AccessLevel"] == "2"){
	$header_text = "เจ้าหน้าที่ พก.";
}
if($_POST["AccessLevel"] == "3"){
	$header_text = "เจ้าหน้าที่ พมจ.";
}
if($_POST["AccessLevel"] == "5"){
	$header_text = "ผู้บริหาร";
}

?>

<div align="center">
            <strong>รายงานตรวจสอบบันทึกข้อมูลสถานประกอบการ<?php echo $header_text;?></strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>

      <tr >
      
      	<td><div align="center" style="vertical-align:middle;"><strong>ลำดับที่</strong></div></td>
       <td><div align="center" style="vertical-align:middle;"><strong>ชื่อผู้ใช้งานระบบ</strong></td>
        <td><div align="center" style="vertical-align:middle;"><strong>ประเภทธุรกิจ</strong></td>
        <td><div align="center" style="vertical-align:middle;"><strong>ชื่อบริษัท</strong></td>
        <td><div align="center" style="vertical-align:middle;"><strong>เลขที่บัญชีนายจ้าง</strong></td>        
        <td><div align="center" style="vertical-align:middle;"><strong>ที่อยู่</strong></td>
        
        <td><div align="center" style="vertical-align:middle;"><strong>หมู่</strong></td>
        <td><div align="center" style="vertical-align:middle;"><strong>ซอย</strong></td>
        <td><div align="center" style="vertical-align:middle;"><strong>ถนน</strong></td>
        <td><div align="center" style="vertical-align:middle;"><strong>อำเภอ/เขต</strong></td>
        <td><div align="center" style="vertical-align:middle;"><strong>ตำบล/แขวง</strong></td>
        
        <td><div align="center" style="vertical-align:middle;"><strong>จังหวัด</strong></td>
        <td><div align="center" style="vertical-align:middle;"><strong>แก้ไขข้อมูลวันที่</strong></td>
       </td>
      
        
      </tr>
      </thead>
      
      <tbody>
      <?php
	//generate info
	$lawful_sql = "
					select
					 user_name
					 , CompanyTypeName
					 , 	CompanyNameThai
					 , CompanyCode 	
					 
					 ,Address1
					 ,Moo
					 ,Soi
					 ,Road
					 ,Subdistrict
					 ,District
					 ,Province_name
					,LastModifiedDateTime
					from
						company a, users b
						,provinces c
						, companytype d
					where
						a.lastmodifiedby = b.user_id
						and
						a.province = c.province_id
						and
						a.CompanyTypeCode = d.CompanyTypeCode
						
						$filter_from
						
						$filter_to
						
						$filter_access_level
						
						$filter_user_name
						
					order by LastModifiedDateTime desc
									";
									
	//echo $lawful_sql;//exit();									
									
	$lawful_result = mysql_query($lawful_sql);	
	while ($lawful_row = mysql_fetch_array($lawful_result)) {
		$row_count++;
		
		
  ?>
      <tr>
      
      	<td  valign="top"><div align="center"><?php echo $row_count;?></div></td>
        <td  valign="top"><div align="center"><?php echo $lawful_row["user_name"];?></div></td>
        <td  valign="top"><div align="left"><?php echo $lawful_row["CompanyTypeName"];?></div></td>
        <td  valign="top"><div align="left"><?php echo $lawful_row["CompanyNameThai"];?></div></td>
        <td  valign="top"><div align="right"><?php echo $lawful_row["CompanyCode"];?></div></td>
        <td  valign="top"><div align="right"><?php echo $lawful_row["Address1"];?></div></td>
        
        <td  valign="top"><div align="right"><?php echo $lawful_row["Moo"];?></div></td>
        <td  valign="top"><div align="right"><?php echo $lawful_row["Soi"];?></div></td>
        <td  valign="top"><div align="right"><?php echo $lawful_row["Road"];?></div></td>
        <td  valign="top"><div align="right"><?php echo $lawful_row["District"];?></div></td>
        <td  valign="top"><div align="right"><?php echo $lawful_row["Subdistrict"];?></div></td>
        
        <td  valign="top"><div align="right"><?php echo $lawful_row["Province_name"];?></div></td>
        <td  valign="top"><div align="right"><?php echo $lawful_row["LastModifiedDateTime"];?></div></td>
      
        
      </tr>
      <?php
	}
  ?>
	  </tbody>
        
        
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
