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

//yoes 20160126 ---> allow all years
if($the_year != 9999){
	$the_year_filter_le = " and c.le_year = '$the_year'";
	$the_year_filter_lawful = "and b.year = '$the_year'";
}


if($the_year >= 2013 && $the_year != 9999){

	$is_2013 = 1;
	//year > 2013 => only concern main branch
	$branch_codition =  " AND a.BranchCode < 1 ";

}

$the_year_to_use = formatYear($the_year);

$province_text = "ทั่วประเทศ";
$province_filter = "";		
if(isset($_POST["Province"]) && $_POST["Province"] != "" && $_POST["rad_area"] == "province"){
	$province_filter = " and a.province = '".$_POST["Province"]."'";
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	
	$province_filter = " and a.section_id = '".$_POST["Section"]."'";
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}





//yoes 20160126 - add last modify date/time for lawfulness
if($_POST["date_from_year"] > 0 && $_POST["date_from_month"] > 0 && $_POST["date_from_day"] > 0){

	$the_mod_year = $_POST["date_from_year"];
	$the_month = $_POST["date_from_month"];
	$the_day = $_POST["date_from_day"];
	
	$filter_from = " and verified_date >= '$the_mod_year-$the_month-$the_day 00:00:01'";
}

if($_POST["date_to_year"] > 0 && $_POST["date_to_month"] > 0 && $_POST["date_to_day"] > 0){

	$the_mod_year = $_POST["date_to_year"];
	$the_month = $_POST["date_to_month"];
	$the_day = $_POST["date_to_day"];
	
	$filter_to = " and verified_date <= '$the_mod_year-$the_month-$the_day 23:59:59'";
}


if($_POST["chk_from"] && ($filter_from || $filter_to)){

	$last_modified_sql = "
				
				$filter_from
				$filter_to			
			
			";	
}



///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter = " and (a.CompanyTypeCode = '14'";
	$typecode_filter .= " or a.CompanyTypeCode >= 200  or a.CompanyTypeCode < 300)";
	
}else{
	$typecode_filter = " and a.CompanyTypeCode != '14'";
	$typecode_filter .= " and a.CompanyTypeCode < 200";
	
}




?>

<div align="center">
  <strong>รายงานแสดงรายการสถานประกอบการที่ชำระเงินและเจ้าหน้าที่ยังไม่ได้กรอกรายละเอียดข้อมูล มาตรา 33 35 
  <br /> 
  
  <?php if($the_year == 9999){?>
  ข้อมูลทุกปี
  <?php }else{?>
  ประจำปี <?php echo $the_year_to_use;?>
  <?php }?>
  
  </strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	
   
   	 <tr >
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ลำดับ</strong></div></td>
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>เลขทะเบียนนายจ้าง</strong></div></td>
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ชื่อสถานประกอบการ</strong></div></td>
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ที่อยู่</strong></div></td>
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>จังหวัด</strong></div></td>
   	   
   	   <td colspan="3"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ข้อมูลที่ยังไม่ได้บันทึก</strong></div></td>
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>วันที่ตรวจสอบ</strong></div></td>
   	   <td colspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ผู้ตรวจสอบ</strong></div></td>
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>รายละเอียดการจ่ายเงิน</strong></div></td>
      </tr>
   	 <tr >
      
       
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ปีที่ไม่ได้บันทึก</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>มาตรา 33 (คน)</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>มาตรา 35 (คน)</strong></div></td>    
          
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>User name</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ชื่อ-นามสกุล</strong></div></td>
      </tr>
   	 
   	 
      
    
      
     
     <?php 
	 
	 
	 //yoes 20160119 -- loop for all provinces
	 $dummy_sql = "
	 
	 	select
			*
			, a.cid as company_cid
			, b.year as lawfulness_year
			, b.lid as lawfulness_lid
		from
	 		company a
			join
				lawfulness b
					on 
					a.cid = b.cid
					
					
					$the_year_filter_lawful
					
					$last_modified_sql
					
			join
				provinces aa
					on a.province = aa.province_id
			
			join (
			
			
				select
					c.le_cid as the_cid
					, b.lid as the_lid
				from
					lawful_employees c
						join lawfulness b
							on c.le_cid = b.cid and c.le_year = b.year
				where
									
					le_is_dummy_row = 1
					
					$the_year_filter_le
					
				union 
				select
					b.cid as the_cid
					, b.lid as the_lid
				from
					lawfulness b
					join
						curator d
						on d.curator_lid = b.lid
						
					and
					curator_is_dummy_row = 1
					and
					curator_parent = 0
					$the_year_filter_lawful
			
			
			) moomin
				on moomin.the_cid = a.cid
					and moomin.the_lid = b.lid
			
	 
	 	where
		
				1 = 1
				
				$province_filter
				
				$typecode_filter
				
				$branch_codition
	 
	 
	 
	 
	 
	 
	 ";
	 
	 //
	 //echo $dummy_sql; //exit();
	 $dummy_result = mysql_query($dummy_sql) or die($mysql_error);
	 
	 
	 
	 while($dummy_row = mysql_fetch_array($dummy_result)){
		
		$row_count++;
		
		?>
        
        
        <tr >
   	   <td  align="center" valign="middle"><?php echo $row_count;?></td>
   	    <td   valign="middle"><?php echo doCleanOutput($dummy_row[CompanyCode]);?></td>
   	   
       
	    <td   valign="middle"><?php 
		   
				echo formatCompanyName($dummy_row["CompanyNameThai"],$dummy_row["CompanyTypeCode"]);
				
		?></td>
		
		
		 <td   valign="middle"><?php 
	   
			echo getAddressText($dummy_row);
			
		?></td>
            
            
        <td   valign="middle"><?php echo doCleanOutput($dummy_row[province_name]);?></td>
        
        <td  align="center" valign="middle"><?php echo doCleanOutput($dummy_row[lawfulness_year]+543);?></td>
        
        <td align="right" valign="middle">
        <?php 
		
			$the_sql = "
				select 
					count(*) 
				from 
					lawful_employees c
				where 
					le_is_dummy_row = 1 
					
					and
					le_year = '".$dummy_row[lawfulness_year]."'
					
					and
					le_cid = '".$dummy_row[company_cid]."'
				";
				
			echo formatEmployee(getFirstItem($the_sql));
		
		?>
        
        </td>
        <td align="right" valign="middle">
        
        <?php 
		
			$the_sql = "
				select 
					count(*) 
				from 
					curator 
				where 
					curator_is_dummy_row = 1 
					and
					curator_parent = 0					
					and
					curator_lid = '".$dummy_row[lawfulness_lid]."'
				";
				
			echo formatEmployee(getFirstItem($the_sql));
		
		?>
        
        </td>
        
        <td  align="center" valign="middle"><?php echo doCleanOutput($dummy_row[verified_date]);?></td>
                
        <td  align="center" valign="middle"><?php 
		
		$verified_row = getFirstRow("select * from users where user_id = '".$dummy_row[verified_by]."'");
		
		echo $verified_row[user_name];
		
		?></td>
        
         <td  align="center" valign="middle"><?php echo trim($verified_row[FirstName]." " .$verified_row[LastName]);?></td>
         <td  align="left" valign="middle">
         <?php 
		 
		 	//try to get receipt details here
			$sql = "
			
			
					select 
							*
							, receipt.amount as receipt_amount 
							, lawfulness.lid as lawfulness_lid
							
						from 
							payment
							, receipt
							, lawfulness
							
						where 
						
							receipt.RID = payment.RID
							and
							lawfulness.LID = payment.LID
							
							and
							lawfulness.lid = '".$dummy_row[lawfulness_lid]."' 							
							
							order by ReceiptDate, BookReceiptNo, ReceiptNo asc
			
			";
		 
		 
		 	//echo $sql;
			
			$receipt_result = mysql_query($sql);
			$receipt_count = 0;
			
			while($receipt_row = mysql_fetch_array($receipt_result)){
				
				$receipt_count++;
				
				if($receipt_count > 1){
					echo "<br>";
				}
				
				echo "(".$receipt_count . ")";
				
				
				if($receipt_row[is_payback]){
					echo "(จ่ายเงินคืน)";	
				}
				
				
				echo "ใบเสร็จเล่มที่ ".$receipt_row[BookReceiptNo]." เลขที่ ".$receipt_row[ReceiptNo]." วันที่จ่าย ".formatDateThai($receipt_row["ReceiptDate"])." จำนวนเงิน ".formatNumber($receipt_row["Amount"])." บาท จ่ายโดย ".formatPaymentName($receipt_row["PaymentMethod"])." ";
				
			}
		 
		 ?>
         
         </td>
        
       
      </tr>
        
        
        
     <?php

		
		
	 }
	 
	 
	 ?>
     
     
     
    
      
    
	
        
        <?php
			if($_POST["report_format"] == "pdf"){
				//
			}else{
				$footer_row = 'style="border-bottom:double;"';
			}
		?>
        
      
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
