<?php
require_once 'db_connect.php';
require_once 'c2x_include.php';

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_423.xls");
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
$selectedLawStatus = NULL;
$now = date("Y");
$now_test = date("Y-m-d");
$current = $now - 10;	


if($_POST["ddl_year"]){
	
	$year_selected = $_POST["ddl_year"];

}
	//$courted_table_sql2 = "left join lawfulness_meta t on b.lid = t.meta_lid";
	$courted_meta_sql2 = "and t.meta_for = 'courted_flag'";




if(isset($_POST["LawStatus"]) && is_numeric($_POST["LawStatus"])){
	$selectedLawStatus = intval($_POST["LawStatus"]);
}

if($the_year > 2012){
	$is_2013 = 1;
}

$lawStatusMapping = getLawStatusMapping();

if($all_year == "1"){
	$expire2Year = $expire2Year + 543;
	$expire1Year = $expire1Year + 543;
	$expire8Month = $expire8Month + 543;
	
	//$the_year_to_use = $expire2Year  . ',' . $expire1Year  . ',' . $expire8Month ;
	$the_year_to_use = 'ทุกปีงบประมาณ';
	
	
}else{
	$the_year_to_use = formatYear($the_year);
}

$province_text = "ทั่วประเทศ";
$province_filter = "";	










	
if(isset($_POST["Province"]) && $_POST["Province"] != "" && $_POST["rad_area"] == "province"){
	$province_filter = " and a.Province = '".$_POST["Province"]."'";
	$sub_province_filter = " and c.Province = '".$_POST["Province"]."'";
	
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	$province_filter = " and c.section_id = '".$_POST["Section"]."')";
	$sub_province_filter = " and c.section_id = '".$_POST["Section"]."'";
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}

if($_POST["CompanyTypeCode"] == "14"){
	
	$typecode_filter = " and a.CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	$typecode_filter = " and a.CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
}


///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter .= " and a.CompanyTypeCode >= 200  and a.CompanyTypeCode < 300";
	
}else{
	
	$typecode_filter .= " and a.CompanyTypeCode < 200";
	
}

//$year_2_filter = "AND l.Year <= DATE_FORMAT((DATE_ADD(NOW(),INTERVAL +2 YEAR)), '%Y')";
//$year_1_filter = "AND l.Year <= DATE_FORMAT((DATE_ADD(NOW(),INTERVAL +1 YEAR)), '%Y')";
//$month_8_filter = "AND l.Year <= DATE_FORMAT((DATE_ADD(NOW(),INTERVAL +8 MONTH)), '%Y')";

//$year_2_filter = "AND (c.branchcode < 1 or l.Year <= (DATE_FORMAT(now(), '%Y')-2))";
//$year_1_filter = "AND (c.branchcode < 1 or l.Year <= (DATE_FORMAT(now(), '%Y')-1))";
//$month_8_filter = "AND (c.branchcode < 1 or now() <= (DATE_FORMAT(now(), '%m')-8))";

////// starts LOGIC here


$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_$the_year'"),100);
$wage_rate = default_value(getFirstItem("select var_value from vars where var_name = 'wage_".$the_year."'"),159);
$year_date = 365;

$condition_sql = " and b.LawfulStatus in (0,2)";

if($is_2013){
	$condition_sql .= " and branchCode < 1 and b.Employees >= $ratio_to_use";
}

if (!is_null($selectedLawStatus)){
	$condition_sql .= " and a.LawStatus = $selectedLawStatus";
}

	//bank add call log from law_system 20230108
	if($year_selected){
		
				$url = "http://203.154.94.105/law_system/law_ws/getLawfulChangeRequest.php?mode=report&year=$year_selected";
				
	}else{
		
				$url = "http://203.154.94.105/law_system/law_ws/getLawfulChangeRequest.php?mode=report";
	}

																	$ch = curl_init();
																	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
																	curl_setopt($ch, CURLOPT_URL,$url);
																	$result=curl_exec($ch);
																	curl_close($ch);

																	
																	$array = json_decode($result, true);	
																	//$data = json_encode($array);
																	$data = $array["data"]["LawEditList"];
																	$total_law_edit = count($data);
	


include "law_expire.php";
			
// echo $main_sql;		 //exit();	

if($is_pdf || $is_excel){
	$w50 = 50;
	$w75 = 75;
	$w100 = 100;
	$w125 = 125;
	$w350 = 350;
}

?>

<div align="center">
	<strong>log request การขอแก้ไขข้อมูลจำนวนเงินและลูกจ้าง ก่อนส่งฟ้องคดีจากระบบการติดตามและดำเนินคดี </strong>
	<br />
	<br />
</div>
    
    
<table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      
      <tr>
        <td width="0" rowspan="3" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
	  </tr>
	  
	  <tr>
        <td width="0" align="center" valign="bottom"><div align="center"><strong>ข้อมูลสถานประกอบการ</strong></div></td>
		<td width="0" align="center" valign="bottom"><div align="center"><strong>ปีงบประมาณ</strong></div></td>
		<td width="0" align="center" valign="bottom"><div align="center"><strong>จำนวนลูกจ้างที่ขอเปลี่ยน</strong></div></td>
		<td width="0" align="center" valign="bottom"><div align="center"><strong>จำนวนลูกจ้างเดิม</strong></div></td>
        <td width="0" align="center" valign="bottom"><div align="center"><strong>ชื่อ user ที่ส่ง</strong></div></td>
		<td width="0" rowspan="2" align="center" valign="bottom"><div align="center"><strong>วันที่ส่ง</strong></div></td>
		<td width="0" rowspan="2" align="center" valign="bottom"><div align="center"><strong>วันที่รับเรื่อง</strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom"><div align="center"><strong>วันที่ยกเลิก</strong></div></td>
	  </tr>

	  
	  

	  
      </thead>
      <tbody>
      <?php
		  //$lawful_result = mysql_query($main_sql4);	
		  
		  
		  for ($i = 0; $i <= $total_law_edit; $i++) { $row_count++; ?>
              
			  <?php if($data[$i]["CompanyNameThai"]){ ?>
			  <tr>
                <td width="<?php echo $w50?>"  valign="top"><div align="center"><?php echo $row_count;?></div></td>
				<td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $data[$i]["CompanyNameThai"]?></div></td>
				<td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo formatYear($data[$i]["Year"])?></div></td>
				<td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $data[$i]["Employees"]?></div></td>
				<td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $data[$i]["EmployeesOld"]?></div></td>
				<td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $data[$i]["user_name"];?></div></td>
				<td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo formatDateThai($data[$i]["CreatedDateTime"],1,5);?></div></td>
				
				<?php if($data[$i]["Approve_accept"] != '0000-00-00 00:00:00'){ ?>
				<td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo formatDateThai($data[$i]["Approve_accept"],1,5);?></div></td>
				
				<?php }else{ ?>
				
				<td width="<?php echo $w75?>"  valign="top"><div align="center"><?php echo "-";?></div></td>
				
				<?php } ?>
				
				<?php if($data[$i]["Approve_reject"] != '0000-00-00 00:00:00'){ ?>
				<td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo formatDateThai($data[$i]["Approve_reject"],1,5);?></div></td>
				
				<?php }else{ ?>
				
				<td width="<?php echo $w75?>"  valign="top"><div align="center"><?php echo "-";?></div></td>
				
				<?php } ?>
			  </tr>
				
			  <?php } ?>
				
              
	<?php } ?>
    </tbody>
    <tfoot>
    </tfoot>
</table>

<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?></div>

