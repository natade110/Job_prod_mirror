<?php

include "db_connect.php";

//print_r($_POST);

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_28.xls");
	
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



if($_POST["mod_date_month"] && $_POST["mod_date_year"] && $_POST["mod_date_month"] > 0 && $_POST["mod_date_year"] > 0){

	$the_month = $_POST["mod_date_month"];
	$the_year = $_POST["mod_date_year"];
	
	$condition_sql .= " and mod_date between '$the_year-$the_month-01' and '$the_year-$the_month-31'";
	

}



if($_POST["register_org_name"]){

	$condition_sql .= " and register_org_name like '%".trim(doCleanInput($_POST["register_org_name"]))."%'";

}


if($_POST["register_name"]){

	$condition_sql .= " and register_name like '%".trim(doCleanInput($_POST["register_name"]))."%'";

}

//from view_register.pgp
if($_GET["mod_register_id"]){

	$condition_sql .= " and mod_register_id = '".trim(doCleanInput($_GET["mod_register_id"]))."'";

}

if($_POST["Province"]){

	$condition_sql .= " and register_province = '".trim(doCleanInput($_POST["Province"]))."'";

}


$main_sql = "

				select
				*
				 from
					 modify_history_register    a
					 ,register b
					 ,provinces c
					
				 where
					 a.mod_register_id = b.register_id
					 and
					 c.province_id = b.register_province
					 $condition_sql
				 
				 order by
				 
				 mod_date desc
				 ,province_name asc
				 ,register_org_name asc
				 

			";
			
			
//echo $main_sql;			

if($is_pdf || $is_excel){

	$w50 = 50;
	$w100 = 100;
	$w125 = 125;
	
}

?>

<div align="center">
            <strong>การบันทึกข้อมูลเจ้าหน้าที่ของสถานประกอบการที่ใช้ระบบรายงานผลการจ้างงานคนพิการ</strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      
      <tr >
        
        <td width="<?php echo $w125;?>" align="center" ><div align="center" ><strong>ชื่อสถานประกอบการ</strong> </div></td>
        <td width="<?php echo $w100;?>" align="center" ><div align="center" ><strong>จังหวัด</strong></div></td>
        <td width="<?php echo $w100;?>" align="center" ><div align="center" ><strong>ชื่อผู้ติดต่อ</strong></div></td>
        <td width="<?php echo $w100;?>" align="center" ><div align="center" ><strong>เบอร์โทรศัพท์</strong></div></td>
        <td width="<?php echo $w100;?>" align="center" ><div align="center" ><strong>อีเมล์</strong></div></td>
        <td width="<?php echo $w100;?>" align="center" ><div align="center" ><strong>ตำแหน่ง</strong></div></td>
        <td width="<?php echo $w100;?>" align="center" ><div align="center"><strong>วัน/เวลา</strong></div></td>
        <td width="<?php echo $w100;?>" align="center" ><div align="center" ><strong>User Name</strong> </div></td>
        <td width="<?php echo $w100;?>" align="center" ><div align="center"><strong>ข้อมูลที่ปรับปรุง</strong></div></td>
        <td width="<?php echo $w100;?>" align="center" ><div align="center"><strong>รายละเอียด</strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      
      
      <?php
		  $lawful_result = mysql_query($main_sql);	
		  
		  while ($lawful_row = mysql_fetch_array($lawful_result)) {
		  
		  	//print_r($lawful_row);
		  
			$row_count++;
			$company_name_to_use = formatCompanyName($lawful_row["CompanyNameThai"],$lawful_row["CompanyTypeCode"]);
			
			$address_to_use = getAddressText($lawful_row);
			//
			
	  ?>
      
      <tr>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo $lawful_row["register_org_name"]?></div></td>
        <td width="<?php echo $w50;?>" valign="top"><?php echo $lawful_row["province_name"];?></td>
        <td width="<?php echo $w125;?>" valign="top"><div align="left"><?php echo $lawful_row["register_contact_name"]?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo $lawful_row["register_contact_phone"]?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo $lawful_row["register_email"]?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo $lawful_row["register_position"]?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo $lawful_row["mod_date"]?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo ($lawful_row["register_name"]);?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo getOrgModType($lawful_row["mod_type"]);?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php 
		
		if($lawful_row["mod_type"] == 3 || $lawful_row["mod_type"] == 4){
			echo ($lawful_row["mod_desc"]);
		}
		if($lawful_row["mod_type"] == 6){
			
			echo "แก้ไขโดย user: " . getFirstItem("select user_name from users where user_id = '".$lawful_row["mod_desc"]."'");
			
		}
		
		?></div></td>
      </tr>
      
      
      <?php }?>
	  </tbody>
        
        
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
