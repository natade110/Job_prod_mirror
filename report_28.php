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
	
	//yoes 20221107 -- get custom log
	//$condition_sql .= " and mod_date between '2022-08-01' and '2022-10-31'";
}



if($_POST["CompanyCode"]){

	$condition_sql .= " and CompanyCode like '%".trim(doCleanInput($_POST["CompanyCode"]))."%'";

}


if($_POST["CompanyNameThai"]){

	$condition_sql .= " and CompanyNameThai like '%".trim(doCleanInput($_POST["CompanyNameThai"]))."%'";

}

if($_POST["user_name"]){

	$condition_sql .= " and user_name like '%".trim(doCleanInput($_POST["user_name"]))."%'";

}

//yoes 20160614 --> add mod_type
//echo $_POST[mod_type];
if(strlen($_POST[mod_type]) > 0){
	
	$mod_type_filter = " and mod_type = '" . $_POST[mod_type] ."'";
	
}

$main_sql = "

				select
				*
				, e.year as lawfulness_year
				
				 from
				 modify_history    a
				 	
					left outer join
						lawfulness e
							on a.mod_lid = e.lid
				 
				 ,users b
				 ,company c
				 ,provinces d
				 
				 	
				 
				 where
					 a.mod_user_id = b.user_id
					 and
					 a.mod_cid = c.CID
					 and
					 c.Province = d.province_id
				 
					 $condition_sql
					 
					 $mod_type_filter
				 
				 order by
				 mod_date desc
				 ,province_name asc
				 ,CompanyNameThai asc
				 

			";
			
			
//echo $main_sql;			




if($is_pdf || $is_excel){

	$w50 = 50;
	$w100 = 100;
	$w125 = 125;
	
}

?>

<div align="center">
            <strong>รายงานการบันทึกข้อมูลสถานประกอบการ</strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td width="<?php echo $w100;?>" rowspan="2" align="center" valign="bottom" ><div align="center" ><strong>เลขที่บัญชีนายจ้าง</strong> </div></td>
        <td width="<?php echo $w50;?>" rowspan="2" align="center" valign="bottom" ><div align="center" ><strong>สาขา</strong> </div></td>
        <td width="<?php echo $w125;?>" rowspan="2" align="center" valign="bottom" ><div align="center" ><strong>ชื่อสถานประกอบการ</strong> </div></td>
        <td width="<?php echo $w100;?>" rowspan="2" align="center" valign="bottom" ><div align="center" ><strong>จังหวัด</strong></div></td>
        <td width="<?php echo $w100;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>วัน/เวลา</strong></div></td>
        <td colspan="3" align="center" valign="bottom" >ข้อมูล User</td>
        <td width="<?php echo $w100;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ข้อมูลที่ปรับปรุง</strong></div></td>
        <td width="<?php echo $w100;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>แก้ไขในปี</strong></div></td>
      </tr>
      <tr >
        <td width="<?php echo $w100;?>" align="center" valign="bottom" ><div align="center"><strong>ชื่อผู้ใช้งาน</strong></div></td>
        <td width="<?php echo $w100;?>" align="center" valign="bottom" ><div align="center"><strong>ชนิดของ user</strong></div></td>
        <td width="<?php echo $w100;?>" align="center" valign="bottom" ><div align="center"><strong>ชื่อ-นามสกุล</strong></div></td>
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
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo $lawful_row["CompanyCode"]?></div></td>
        <td width="<?php echo $w50;?>" valign="top"><div align="left"><?php echo $lawful_row["BranchCode"]?></div></td>
        <td width="<?php echo $w125;?>" valign="top"><div align="left"><?php echo $company_name_to_use;?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo $lawful_row["province_name"];?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><?php echo $lawful_row["mod_date"]?></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo $lawful_row["user_name"]?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo formatAccessLevel($lawful_row["AccessLevel"]);?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo $lawful_row["FirstName"]?> <?php echo $lawful_row["LastName"]?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="left"><?php echo getModType($lawful_row["mod_type"]);?></div></td>
        <td width="<?php echo $w100;?>" valign="top"><div align="center"><?php 
		
			if($lawful_row["lawfulness_year"]){
				echo $lawful_row["lawfulness_year"]+543;
			}
			
			?></div></td>
      </tr>
      
      
      <?php }?>
	  </tbody>
        
        
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
