<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_24.xls");
	$is_excel = 1;

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
	$province_filter = " and a.Province = '".$_POST["Province"]."'";
	
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	$province_table = ", provinces";
	$province_filter = " and a.Province in (select province_id from provinces where section_id = '".$_POST["Section"]."')";
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




//yoes 20130813 - add last modify date/time for lawfulness
if($_POST["date_from_year"] > 0 && $_POST["date_from_month"] > 0 && $_POST["date_from_day"] > 0){

	$the_year = $_POST["date_from_year"];
	$the_month = $_POST["date_from_month"];
	$the_day = $_POST["date_from_day"];
	
	$filter_from = " and LastModifiedDateTime >= '$the_year-$the_month-$the_day 00:00:01'";
}

if($_POST["date_to_year"] > 0 && $_POST["date_to_month"] > 0 && $_POST["date_to_day"] > 0){

	$the_year = $_POST["date_to_year"];
	$the_month = $_POST["date_to_month"];
	$the_day = $_POST["date_to_day"];
	
	$filter_to = " and LastModifiedDateTime <= '$the_year-$the_month-$the_day 23:59:59'";
}

if($_POST["chk_from"] && ($filter_from || $filter_to)){

	$last_modified_sql = "
	
			and
			a.CID in (
			
				select mod_cid from modify_history where mod_type = 1
				
				$filter_from
				$filter_to			
			)	
			";	
}




////// starts LOGIC here


$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_$the_year'"),100);

if($is_2013){

	$condition_sql .= " and branchCode < 1";

}



$main_sql = "

			select
				*
				, b.Employees as company_employees
				, a.CID as the_company_cid
				
				, b.LID as the_lid
			from
				company a
				, lawfulness b
				, provinces c
			where
				a.CID = b.CID
				and
				b.Year = '$the_year'
				
				and 
				a.Province = c.province_id
				
				$province_filter
				
				$last_modified_sql
				
				$condition_sql

			
			order by
				province_name, CompanyNameThai asc
			
			
			";


////////			
if($is_2013){

$main_sql = "

			select
				*
				, b.Employees as company_employees
				, a.CID as the_company_cid
				
				, b.LID as the_lid
			from
				company a
				, lawfulness b
				, provinces c
				
				,(
					SELECT 
			
						CompanyCode as the_sub_companycode
						,sum(Employees) as the_sub_sum
			
					 FROM 
						company
						
					group by 
						
						CompanyCode	
						
					having 
						sum(Employees) > 99	
				)e
				
				
			where
				a.CID = b.CID
				and
				b.Year = '$the_year'
				
				and 
				a.Province = c.province_id
				
				$province_filter
				
				$condition_sql
				
				$last_modified_sql
				
				and
				a.companyCode = e.the_sub_companycode

			
			order by
				province_name, CompanyNameThai asc
			
			
			";



}			
			
			
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
            <strong>รายงานข้อมูลสถานประกอบการ <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />
</strong>
              <br>
</div>
    
    
<table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td width="0" rowspan="3" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="0"   colspan="3" valign="bottom"><div align="center"><strong>ข้อมูลสถานประกอบการ</strong></div></td>
        <td colspan="10" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>การปฏิบัติตามกฎหมาย</strong></div></td>
        </tr>
      <tr >
        <td width="<?php echo $w75;?>" rowspan="2" valign="bottom"><div align="center"><strong>ชื่อสถานประกอบการ</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" valign="bottom"><div align="center"><strong>ที่ตั้ง</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>จำนวนลูกจ้าง<br />(ราย)</strong> </div></td>
        <td width="<?php echo $w75;?>" rowspan="2" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>อัตราส่วน <?php echo $ratio_to_use;?>:1 (ราย)
</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" valign="bottom" ><div align="center"><strong>
          
          รับคนพิการเข้าทำงาน
  <br />ตามมาตรา 33
  <br />(ราย)
          
          
        </strong></div></td>
        <td width="0" rowspan="2" valign="bottom" ><div align="center"><strong>จ่ายเงินแทนการรับคนพิการ		
        ตามมาตรา 34  (บาท)</strong></div></td>
        <td colspan="2" valign="bottom" ><div align="center"><strong>การให้สัมปทาน</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" valign="bottom" ><div align="center"><strong>ปฎิบัติตามกฎหมาย </strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" valign="bottom" ><div align="center"><strong>ปฎิบัติตามกฎหมาย<br />
          
          แต่ไม่ครบอัตราส่วน
          
</strong></div></td>
        <td width="0" rowspan="2" valign="bottom" ><div align="center"><strong>ไม่ปฏิบัติตามกฎหมาย </strong></div></td>
        <td width="0" rowspan="2" valign="bottom" ><div align="center"><strong>ไม่เข้าข่ายจำนวน<br />
        ลูกจ้างตามกฎหมาย </strong></div></td>
        <td width="0" rowspan="2" valign="bottom" ><div align="center"><strong>หมายเหตุ </strong></div></td>
      </tr>
      <tr >
        <td width="<?php echo $w75;?>" valign="bottom" ><div align="center"><strong>คนพิการ<br />
          
          (ราย)
</strong></div></td>
        <td width="<?php echo $w75;?>" valign="bottom" ><div align="center"><strong>ผู้ดูแลคนพิการ<br />
        (ราย) </strong></div></td>
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
			
			$final_employee = getEmployeeRatio( $lawful_row["company_employees"],$ratio_to_use);
			
			$type_35_to_use = getFirstItem("select count(*) from curator where curator_lid = '".$lawful_row["LID"]."'");
			
			
			
			/////
			//try generate recipt data
			$the_money_sql = "select sum(receipt.Amount)  as the_amount
							from payment, receipt , lawfulness
							where 
							receipt.RID = payment.RID
							and
							lawfulness.LID = payment.LID
							and
							ReceiptYear = '".$the_year."'
							and
							lawfulness.CID = '".$lawful_row["the_company_cid"]."'
							
							and is_payback = '0'
							
							";
							
			//echo $the_money_sql;
			
			$paid_money = getFirstItem($the_money_sql);
			
			
			$this_row = 0;
			
			
			
			//curator
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
		
			
			
	  ?>
      
              <tr>
                <td width="<?php echo $w50?>"  valign="top"><div align="center"><?php echo $row_count;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $company_name_to_use;?></div>          </td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo $lawful_row["company_employees"]?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo $final_employee?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo $lawful_row["Hire_NumofEmp"]?></div></td>
                
                
                
               <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo formatMoneyReport($paid_money);?></div></td>
                
                
                
                
               
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php echo $curator_usee;?></div></td>
                    <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php echo $curator_user;?></div></td>
                    <td width="<?php echo $w75?>"  valign="top"><div align="center">
                      <?php if($lawful_row["LawfulStatus"] == 1){echo 'X';}?>
                    </div></td>
               
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php if($lawful_row["LawfulStatus"] == 2){echo 'X';}?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php if($lawful_row["LawfulStatus"] == 0){echo 'X';}?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php if($lawful_row["LawfulStatus"] == 3){echo 'X';}?></div></td>
                <td width="<?php echo $w50?>"  valign="top"><?php if($lawful_row["Status"] == 0){echo "ปิดกิจการ";}else{echo "&nbsp;";}?></td>
              </tr>
     
     
     			
     	
     	<?php }?>
	  </tbody>
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
