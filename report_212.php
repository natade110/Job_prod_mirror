<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_112.xls");
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

if($the_year > 2012){
	$is_2013 = 1;
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



			
			
	//new condition as of 20140520
	$main_sql = "
			
			select
				*				
				, c.CID as the_company_cid
				
				, a.LawfulStatus as the_lawful_status
				, b.LawfulStatus as the_old_lawful_status
				
				, a.LID as now_lid
				, b.LID as old_lid
			from
				lawfulness a
					join 
						lawfulness_old_law b
							on
							a.lid = b.lid
							and
							a.lawfulStatus != b.lawfulStatus
							and
							b.lawfulStatus is not null
							and
							a.Year = '$the_year'
							
					join
						company c
							on a.cid = c.cid
							
							
					left join
						provinces d
							on 
								c.Province = d.province_id
			
			where
				
				1=1
				
				$province_filter
				
				$condition_sql

				$business_filter
				
				$last_modified_sql
				
				$typecode_filter
			
			order by
				province_name, CompanyNameThai asc
						
			
			
			";


//echo $main_sql; exit();



			
//echo $main_sql;		 //exit();	

if($is_pdf || $is_excel){

	$w50 = 50;
	$w75 = 75;
	$w100 = 100;
	$w125 = 125;
	$w350 = 350;
	
}



?>

<div align="center">
            <strong>รายการสถานประกอบการที่สถานะการทำตามกฎหมายเปลี่ยนไปหลังจากเริ่มใช้กฎหมายใหม่ปี 2561 <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />
</strong>
              <br>
</div>
    
    
<table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      <tr >
        <td align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>เลขทะเบียนนายจ้าง</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>ชื่อสถานประกอบการ</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>ที่อยู่</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>การปฏิบัติตามกฎหมาย<br>
        (ตามกฎหมายใหม่)</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>การปฏิบัติตามกฎหมาย<br>
        (ตามกฎหมายเก่า)</strong></div></td>
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
                <td width="<?php echo $w50?>"  valign="top"><div align="center"><?php echo $row_count;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $lawful_row["CompanyCode"];?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $company_name_to_use;?></div>          </td>
                <td width="<?php echo $w75?>" align="left"  valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
                <td width="<?php echo $w75?>"  valign="top">
				<div align="center">
				
					<?php 
					
						//print_r($lawful_row);
						
						echo getLawfulText($lawful_row["the_lawful_status"]);
					
					?>
					
				</div></td>
                <td width="<?php echo $w75?>" align="left"  valign="top">
				<div align="center">
				
				
				<?php 
					
						//print_r($lawful_row);
						
						echo getLawfulText($lawful_row["the_old_lawful_status"]);
					
					?>
				
				
				</div></td>
                <?php
				
					
					//echo $the_money_sql;
					
					if($money_num_rows){
					
						//echo "hey";
						while ($pmh_row = mysql_fetch_array($paid_money_history_result)) {
						
							
						?>
                        
                        <?php
						
						
						}
					
					}else{
					
					?>
                    
                    <?php
					
					}
				
				?>
              </tr>
     
     
     			 <?php
				
					
					//echo $the_money_sql;
					
					if($money_num_rows){
					
						//echo "hey";
						while ($pmh_row = mysql_fetch_array($paid_money_history_result_2)) {
						
							
						?>
                        <?php
						
						
						}
					
					}
					
					?>
                    
     			
     	
     	<?php }?>
	  </tbody>
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
