<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_12.xls");

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
	$branch_codition =  " AND a.BranchCode < 1 ";

}

$the_year_to_use = formatYear($the_year);

$province_text = "ทั่วประเทศ";
$province_filter = "";		
if(isset($_POST["Province"]) && $_POST["Province"] != "" && $_POST["rad_area"] == "province"){
	$province_filter = " and province_id = '".$_POST["Province"]."'";
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	
	$province_filter = " and a.section_id = '".$_POST["Section"]."'";
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}


///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter = " and (a.CompanyTypeCode = '14'";
	$typecode_filter .= " or a.CompanyTypeCode >= 200  or a.CompanyTypeCode < 300)";
	
}else{
	$typecode_filter = " and a.CompanyTypeCode != '14'";
	$typecode_filter .= " and a.CompanyTypeCode < 200";
	
}




//yoes 20160119 -- variables
$the_limit = getThisYearRatio($the_year);
$half_limit = $the_limit/2;

$the_wage = getThisYearWage($the_year);

$the_cost_per_person = $the_wage*365;

$year_date = 365;


// echo $province_sql; exit();
?>

<div align="center">
  <strong>สถิติปฏิบัติตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการ
  <br /> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	 <tr >
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>สถานที่ตั้ง</strong> </div></td>
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>จำนวนสถานประกอบการที่มีลูกจ้างตั้งแต่ <?php echo $the_limit;?> คน ขึ้นไป (แห่ง)</strong></div></td>
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>อัตราส่วนที่ต้องรับคนพิการเข้าทํางาน(ราย)</strong></div></td>
   	   <td colspan="9"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>การปฏิบัติตามกฎหมายของนายจ้าง หรือเจ้าของสถานประกกอบการ (แห่ง)</strong></div></td>
   	   <td colspan="4"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>รับคนพิการเข้าทำงาน (ราย)</strong></div></td>
      </tr>
   	 <tr >
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>รับคนพิการเข้าทำงานตาม ม.33</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ส่งเงินเข้ากองทุนฯ ตาม ม.34</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ให้สัมปทานฯ ตาม ม.35</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>รับคนพิการตาม ม.33 และส่งเงินตาม ม.34</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>รับคนพิการตาม ม.33 และให้สัมปทานฯ ตาม ม.35</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ส่งเงินตาม ม.34และให้สัมปทาน ตาม ม.35</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>รับคนพิการ ตาม ม.33 ส่งเงิน ตาม ม.34 และให้สัมปทานฯ ตาม ม.35</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ปฏิบัติตามกฎหมายแต่ไม่ครบอัตราส่วน</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ไม่ปฏิบัติตามกฎหมาย</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>รับคนพิการเข้าทำงาน ตาม ม.33</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ส่งเงินเข้ากองทุนฯแทนการจ้างงาน</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ให้สัมปทานฯ แทนการจ้างงาน</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>คงเหลือที่ไม่รับคนพิการเข้าทำงาน</strong></div></td>
       
        </tr>
   	 
   
     
      
    
      
     
     <?php 
	 
	 
	 //yoes 20160119 -- loop for all provinces
	 $province_sql = "
	 
	 	select
			*
		from
			provinces a
				join province_section b
					on a.section_id = b.section_id
		where
			1=1
			
			$province_filter
			
		order by
			a.section_id asc, province_name asc
	 
	 
	 ";
	 
	 //
	
	 $province_result = mysql_query($province_sql) or die($mysql_error);
	 
	 
	 $last_section = "";
	 
	 while($province_row = mysql_fetch_array($province_result)){
		
		 //$the_province = 63; //กระบี่
		 //$the_province = 15; //นครราชสีมา
		// $the_province = 1; //กทม

		
		 $the_province = $province_row[province_id];
		 $the_province_name = $province_row[province_name];
		 
		 //draw section name here
		 if($province_row[section_id] != $last_section){
			?>
            
           	<tr >
               <td  align="left" valign="middle" colspan="17">
               
               	<strong><?php echo $province_row[section_name] ?></strong>
               
               </td>
              </tr>
            
            <?php 
		 }
		 
		 include "report_32_rows.php";		 
		 
		 
		 $last_section = $province_row[section_id];
	 }
	 
	 
	 ?>
     
     
     
     <tr >
   	   <td  align="center" valign="middle" colspan="2"><strong>ทั้งหมด</strong></td>
   	   
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_1)?></strong></td>
   	   <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_2)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_3)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_4)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_5)?></strong></td>
       
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_6)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_7)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_8)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_9)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_10)?></strong></td>
       
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_11)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_12)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_13)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_14)?></strong></td>
       <td  align="right" valign="middle"><strong><?php echo formatEmployee($total_15)?></strong></td>
      </tr>
      
    
	
        
        <?php
			if($_POST["report_format"] == "pdf"){
				//
			}else{
				$footer_row = 'style="border-bottom:double;"';
			}
		?>
        
      
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
