<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_2.xls");

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
	$province_filter = " and company.Province = '".$_POST["Province"]."'";
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	$province_table = ", provinces";
	$province_filter = " and company.Province = provinces.province_id and provinces.section_id = '".$_POST["Section"]."'";
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}

if($_POST["CompanyTypeCode"] == "14"){
	
	$typecode_filter = " and CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
}

?>

<div align="center">
            <strong>รายละเอียด<?php echo $business_type;?>ที่ปฏิบัติตามกฎหมายไม่ครบตามอัตราส่วน <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="7"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
      </tr>
      <tr >
        <td width="50" rowspan="2" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="117" rowspan="2" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ชื่อ<?php echo $business_type;?></strong> </div></td>
        <td width="168" rowspan="2" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ที่อยู่</strong> </div></td>
        <td width="80" rowspan="2" ><div align="center"><strong>รับคนพิการ<br />
          ตามมาตรา 33
<br />
(ราย)</strong></div></td>
        <td width="80" rowspan="2" ><div align="center"><strong>ส่งเงิน<br />
          ตามมาตรา 34
<br />
(บาท)</strong></div></td>
        <td colspan="2" width="160" ><div align="center"><strong>ให้สัมปทานฯ ตามมาตรา 35</strong></div></td>
        </tr>
      <tr >
        <td width="80" ><div align="center"><strong>คนพิการ (ราย)</strong></div></td>
        <td width="80" ><div align="center"><strong>ผู้ดูแลคนพิการ (ราย)</strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      <?php
	//generate info
	$sub_table = "						
			(
			select 
				sum(receipt.Amount) the_sum
				, receipt.RID the_rid 
				, main_flag
				, ReceiptYear
				, company.cid the_cid 
				, company.CID
				, CompanyNameThai
				, CompanyTypeCode
				, Address1
				, Moo
				, Soi
				, Road
				, Subdistrict
				, District
				, Province
				, Zip
				, ReceiptNo
				, BookReceiptNo
			from 
				receipt
				, payment
				, company
				, lawfulness
				  $province_table
			where
				receipt.RID = payment.RID
				and payment.LID = lawfulness.LID
				and lawfulness.CID = company.CID
				and ReceiptYear = '$the_year'
				$typecode_filter
				$province_filter
			group by
				company.cid
				,receipt.rid
			)
			";
	  
	//generate info
	$lawful_sql = "
					
					
					select 
						*
						, a.the_sum amount
						, main_flag
					from 
						lawfulness
					
					left outer join
						$sub_table a
						
						on 
						
						lawfulness.CID = a.the_cid
						
					
					where
						
						lawfulness.Year = '$the_year'
						and LawfulStatus = '2'  
						
						and province is not null
						
					order by 
						CompanyNameThai asc
						
				   ";	
									
	//echo $lawful_sql; //exit();									
									
	$lawful_result = mysql_query($lawful_sql);	
	$address_array = array();
	$company_name_array = array();
	$cid_array = array();
	$amount_array = array();
	$text_array = array();
	$employee_array = array();
	
	while ($lawful_row = mysql_fetch_array($lawful_result)) {
		
		//print_r($lawful_row);echo $lawful_row[2];exit();
		
		//prepare rows as array...
		//echo "select * from company where cid = '".$lawful_row[2]."'"; exit();
		$the_sql = "select * from company where cid = '".$lawful_row[2]."'";
		//echo $the_sql; exit();
		$company_row = getFirstRow($the_sql); //2 is cid
		
		
		$the_province_text = formatProvince(getFirstItem("select province_name from provinces where province_id = '".$company_row["Province"]."'"));		
		$address_to_use = $lawful_row["Address1"]." ".$lawful_row["Moo"]." ".$lawful_row["Soi"]." ".$lawful_row["Road"]." ".$lawful_row["Subdistrict"]." ".$lawful_row["District"]." ".$the_province_text." ".$lawful_row["Zip"];
		
		
		
		$company_name_to_use = formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]);
		
		if($lawful_row["main_flag"] == 1){
			$amount_to_use = $lawful_row["amount"];
			$text_to_use = "";
		}elseif($lawful_row["main_flag"]){
			$amount_to_use = 0;
			$text_to_use = "จ่ายในใบเสร็จเล่มที่ ".$lawful_row["BookReceiptNo"]." เลขที่ ". $lawful_row["ReceiptNo"];
		}else{
			$amount_to_use = "0";
			$text_to_use = "-";
		}
		
		array_push($address_array,$address_to_use);
		array_push($company_name_array,$company_name_to_use);
		array_push($cid_array,$lawful_row["the_cid"]);
		array_push($amount_array,$amount_to_use);
		array_push($text_array,$text_to_use);
		array_push($employee_array, $lawful_row["Hire_NumofEmp"]);
	
	}
	
	$holder_amount = 0;
	$holder_text = "";
	
	for($i=0;$i<count($cid_array);$i++){
				
		//if next cid = this cid, then, remember current value just skip this loop
		if($cid_array[$i+1] == $cid_array[$i] && $cid_array[$i] && $cid_array[$i+1]){
			$holder_amount += $amount_array[$i];
			$holder_text .=  " ".$text_array[$i];
			continue;
		}
		
		//else just show current info + holder info
		$row_count++;
		
		$amount_to_show = $holder_amount+$amount_array[$i];
		$the_sum_amount += $amount_to_show;
		$text_to_show = $holder_text . " ". $text_array[$i];
		
		if($amount_to_show > 0){
			$formatted_amount = formatMoneyReport($amount_to_show);
		}else{
			$formatted_amount = "";
		}
		
		$the_sum_employee += $employee_array[$i];
  ?>
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $row_count;?></div></td>
        <td width="117" valign="top"><div align="left"><?php echo $company_name_array[$i];?></div></td>
        <td width="168" valign="top"><div align="left"><?php echo $address_array[$i];?></div></td>
        <td width="80" valign="top"><div align="right"><?php echo formatEmployee($employee_array[$i]);?> </div></td>
        <td width="80" valign="top"><div align="right"><?php echo $formatted_amount . $text_to_show;?></div></td>
        <td width="80" valign="top"><div align="right">- </div></td>
        <td width="80" valign="top"><div align="right">- </div></td>
      </tr>
      <?php
	  $holder_amount = 0;
		$holder_text = "";
	}
  ?>
	  </tbody>
        
        <tfoot>
      <tr>
        <td colspan="3" width="335"><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong><?php echo formatEmployeeReport($the_sum_employee);?></strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong><?php echo formatMoneyReport($the_sum_amount);?></strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong>-</strong> </div></td>
        <td width="80" style="border-bottom:double;" ><div align="right"><strong></strong> -</div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
