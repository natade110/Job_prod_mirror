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

$the_year = "2011";

if(isset($_POST["ddl_year"])){
	$the_year = $_POST["ddl_year"];
	
	
	
	
}


if($the_year >= 2013){

	$is_2013 = 1;
	//year > 2013 => only concern main branch
	$branch_codition =  " AND BranchCode < 1 ";

}

//yoes 20160614 -- start to use common includes here
include "report_school_filter.inc.php";


//print_r($_POST);


//yoes 20130813
//get ratio to use for this year...
$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_".$the_year."'"),100);

$the_year_to_use = formatYear($the_year);

$province_text = "ทั่วประเทศ";
$province_filter = "";		
if(isset($_POST["Province"]) && $_POST["Province"] != ""){
	$province_filter = " and company.Province = '".$_POST["Province"]."'";
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
	
	
}





if($_POST["CompanyTypeCode"] == "14"){
	
	//$typecode_filter = " and CompanyTypeCode = '14'";
	//$typecode_filter .= " and CompanyTypeCode >= 200  and CompanyTypeCode < 300";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	//$typecode_filter = " and CompanyTypeCode != '14'";
	//$typecode_filter = " and CompanyTypeCode < 200";
	$business_type = "สถานประกอบการ";
	$business_code = "เลขทะเบียนนายจ้าง";
}



///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter .= " and CompanyTypeCode >= 200  and CompanyTypeCode < 300";
	$business_type = "หน่วยงานภาครัฐ";
	$business_code = "เลขทะเบียนหน่วยงาน";
	
	if($_POST["CompanyTypeCode"]){
		
		$typecode_filter .= " and CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'";
		$the_company_word = getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'");
	}

	
}else{
	
	$typecode_filter .= " and CompanyTypeCode < 200";
	
}


//yoes 20130813 - add last modify date/time for lawfulness
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


if($_POST["chk_from"] && ($filter_from || $filter_to)){

	$last_modified_sql = "
	
			and
			company.CID in (
			
				select mod_cid from modify_history where mod_type = 1
				
				$filter_from
				$filter_to			
			)	
			";	
}

//yoes 20211102
if($_POST[lawful_flag]){	
	
	$lawful_flag = $_POST[lawful_flag]*1;	
	
	$lawful_filter = "and law.lawfulStatus = '$lawful_flag'";
	
	$lawful_text = getLawfulText($lawful_flag);
	
	
	
}


//yoes 20211107 -- date range and stuffs
if($_POST[rad_date_range] == "lawful"){
	
	//yoes 20231003
	//$date_range_filter = "and year(re.ReceiptDate) = '$the_year'";
	$date_range_filter = "and law.year = '$the_year'";
	$date_range_text = "ประจำปี<b><U>การปฏิบัติตามกฏหมาย</b></u> " . ($the_year+543) ;
	
}
if($_POST[rad_date_range] == "range"){
	
	$the_from_year = $_POST["date_from_year"];
	$the_from_month = $_POST["date_from_month"];
	$the_from_day = $_POST["date_from_day"];
	
	$the_to_year = $_POST["date_to_year"];
	$the_to_month = $_POST["date_to_month"];
	$the_to_day = $_POST["date_to_day"];
	
	
	$date_range_filter = "and re.ReceiptDate >= '$the_from_year-$the_from_month-$the_from_day'";
	$date_range_filter .= "and re.ReceiptDate <= '$the_to_year-$the_to_month-$the_to_day'";
	$date_range_text = " ตามวันชำระเงินช่วงวันที่ " . formatDateThai("$the_from_year-$the_from_month-$the_from_day") . " ถึง " . formatDateThai("$the_to_year-$the_to_month-$the_to_day");
	
}

if($_POST[rad_date_range] == "month"){
	
	
	$the_6411_month = $_POST["report_6411__month"];
	$the_6411_year = $_POST["report_6411__year"];
	
	
	$date_range_filter = "and month(re.ReceiptDate) = '$the_6411_month'";
	$date_range_filter .= "and year(re.ReceiptDate) = '$the_6411_year'";
	$date_range_text = " ตามวันชำระเงินเดือน " . formatMonthThai($the_6411_month) . "  พ.ศ. ". ($the_6411_year+543);
	
}


if($_POST[rad_date_range] == "quarter"){
	
	
	$the_6411_q_month = $_POST["report_6411_q_quarter"];
	$the_6411_q_year = $_POST["report_6411_q_year"];
	
	if($the_6411_q_month == 01){
		$date_range_filter = " and month(re.ReceiptDate) between 1 and 3";
	}
	if($the_6411_q_month == 02){
		$date_range_filter = " and month(re.ReceiptDate) between 4 and 6";
	}
	if($the_6411_q_month == 03){
		$date_range_filter = " and month(re.ReceiptDate) between 7 and 9";
	}
	if($the_6411_q_month == 04){
		$date_range_filter = " and month(re.ReceiptDate) between 10 and 12";
	}
	
	$date_range_filter .= " and year(re.ReceiptDate) = '$the_6411_q_year'";
	$date_range_text = " ไตรมาสที่ " . ($the_6411_q_month) . "  พ.ศ. ". ($the_6411_q_year+543);
	
}

?>

<div align="center">
            <strong>ข้อมูลเงินสมทบตามมาตรา 34 <?php echo $the_company_word;?> <?php echo $province_text;?> <?php echo $lawful_text;?> <?php echo $date_range_text;?></strong>
              <br>
</div>

<?php

	$sql = "
		
		
		select 
			*
			, re.Amount as receipt_amount
		from
			receipt re
				join
					payment pay
					on
					re.RID = pay.RID
					
				join
					lawfulness law
					on
					pay.LID = law.LID
				join
					company
					on
					law.cid = company.cid
					
				$province_table
				
				
		where
			(
				1=1
				
				$date_range_filter
				
				and
				NEPFundPaymentID is not null
				$lawful_filter
			)
			
			$province_filter
			
			$typecode_filter
			
			$last_modified_sql
			
			 AND is_payback = 0
			
		order by re.RID desc
		
				
			   
	
	";

	//echo $sql;
	
	$the_result = mysql_query($sql);
	
	
	
	

?>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
		 
		  <tr >
			  
			  <td valign="top" align="center">
				<strong>ลำดับที่</strong>
			</td>
			<td valign="top" align="center">
				<strong>รหัสการจ่ายเงิน</strong>
			</td>
		 
			<td valign="top" align="center"><strong>เลขทะเบียนนายจ้าง</strong></td>
			<td valign="top" align="center"><strong>ชื่อสถานประกอบการ</strong></td>
			
			<td valign="top" align="center"><strong>สถานะการปฏิบัติตามกฎหมาย</strong></td>
			
			<td valign="top" align="center">
				<strong>ใบเสร็จเล่มที่</strong>
			</td>
			<td valign="top" align="center">
				<strong>ใบเสร็จเลขที่</strong>
			</td>
			<td valign="top" align="center">
				<strong>วันที่ชำระเงิน</strong>
			</td>
			<td valign="top" align="center">
				<strong>จำนวนเงินที่จ่าย</strong>
			</td>
			<td valign="top" align="center">
				<strong>จ่ายเป็นเงินต้น ม33</strong>
			</td>
			 <td valign="top" align="center">
				<strong>จ่ายเป็นดอกเบี้ย ม33</strong>
			</td>

		   <td valign="top" align="center">
				<strong>จ่ายเป็นเงินต้น ม34</strong>
			</td>
			 <td valign="top" align="center">
				<strong>จ่ายเป็นดอกเบี้ย ม34</strong>
			</td>

		  <td valign="top" align="center">
				<strong>จ่ายเป็นเงินต้น ม35</strong>
			</td>
			 <td valign="top" align="center">
				<strong>จ่ายเป็นดอกเบี้ย ม35</strong>
			</td>

			<td bgcolor="#efefef" valign="top" align="center" >
				<strong>รวมจ่ายเป็นเงินต้น</strong>
			</td>
			  <td bgcolor="#efefef" valign="top" align="center">
				<strong>รวมจ่ายเป็นดอกเบี้ย</strong>
			</td>
			  
			   <td bgcolor="#FFFFE0" valign="top" align="center">
				<strong>ส่วนต่าง</strong>
			</td>
		 
			  
		  </tr>
		  
      </thead>
      
      <tbody>
      <?php
	
	while($the_row = mysql_fetch_array($the_result)){	
		
		//print_r($lawful_row);
		
		$row_count++;
		
		$the_province_text = formatProvince(getFirstItem("select province_name from provinces where province_id = '".$lawful_row["Province"]."'"));		
		//$address_to_use = $lawful_row["Address1"]." ".$lawful_row["Moo"]." ".$lawful_row["Soi"]." ".$lawful_row["Road"]." ".$lawful_row["Subdistrict"]." ".$lawful_row["District"]." ".$the_province_text." ".$lawful_row["Zip"];
		$address_to_use = getAddressText($lawful_row);
	
	
		$this_row_ratio = getEmployeeRatio( $lawful_row["ratioEmployees"],$ratio_to_use);
	
		$the_sum_ratio += $this_row_ratio;
		$the_sum += $lawful_row["Hire_NumofEmp"];
  ?>
      
		  
		  <tr>
			<td>
				<?php echo $row_count;?>
			</td>
			<td>
				<?php echo $the_row[NEPFundPaymentID];?>
			</td>			
			<td><?php echo $the_row[CompanyCode];?></td>
			<td><?php echo formatCompanyName($the_row["CompanyNameThai"],$the_row["CompanyTypeCode"]);?></td>
			
			<td>
				<div align=center>
					<?php echo getLawfulText($the_row[LawfulStatus]);?>
				</div>
			</td>
			
			<td>
				<?php echo $the_row[BookReceiptNo];?>
			</td>
			<td>
				<?php echo $the_row[ReceiptNo];?>
			</td>
			<td >
				<?php echo formatDateThai($the_row[ReceiptDate]);?>
			</td>
			<td align="right">
				<?php 
				
					echo number_format($the_row[receipt_amount],2);
					
					$sum_amount += $the_row[receipt_amount];
				?>
			</td>

			<td align="right">
				<?php

					$meta_sql = "

						SELECT 
							p_amount - p_pending_amount as paid_for_principal
							, p_interests - p_pending_interests as paid_for_interests
						FROM 
							`receipt_meta` a 
								join
								lawful_33_principals b
								on
								a.meta_for = concat(b.p_lid, b.p_from, b.p_to)
						WHERE 
							a.`meta_rid` = ".$the_row[RID]."

					";

				//echo $meta_sql;
				$meta_33_row = getFirstRow($meta_sql);

				echo number_format($meta_33_row[paid_for_principal],2);

				?>
			</td>
			<td align="right">

				<?php 
					echo number_format($meta_33_row[paid_for_interests],2);
				?>

			</td>

			<td align="right">
				<?php

					$meta_34_sql = "

						SELECT 
							GREATEST(p_amount,0) as paid_for_principal
							, GREATEST(p_interests,0) as paid_for_interests
						FROM 

							lawful_34_principals aa

						WHERE 
							aa.p_to =  ".$the_row[RID]."


					";

				//echo $meta_sql;
				$meta_34_row = getFirstRow($meta_34_sql);

				echo number_format($meta_34_row[paid_for_principal],2);

				?>
			</td>
			<td align="right">

				<?php 
					echo number_format($meta_34_row[paid_for_interests],2);
				?>

			</td>

			<td align="right">
				<?php

					$meta_35_sql = "

						SELECT 
							p_amount - p_pending_amount as paid_for_principal
							, p_interests - p_pending_interests as paid_for_interests
						FROM 
							`receipt_meta` a 
								join
								lawful_35_principals b
								on
								a.meta_for = concat('c', b.p_lid, b.p_from, b.p_to)
						WHERE 
							a.`meta_rid` = ".$the_row[RID]."

					";

				//echo $meta_sql;
				$meta_35_row = getFirstRow($meta_35_sql);

				echo number_format($meta_35_row[paid_for_principal],2);

				?>
			</td>
			<td align="right">

				<?php 
					echo number_format($meta_35_row[paid_for_interests],2);
				?>

			</td>

			 <td align="right" bgcolor="#efefef">

				<?php 
					echo number_format($meta_33_row[paid_for_principal]+$meta_34_row[paid_for_principal]+$meta_35_row[paid_for_principal],2);
				?>

			</td>

			 <td align="right" bgcolor="#efefef">

				<?php 
					echo number_format($meta_33_row[paid_for_interests]+$meta_34_row[paid_for_interests]+$meta_35_row[paid_for_interests],2);
				?>

			</td>
			  
			  
			  <td bgcolor="#FFFFE0" align="right">
			
				<?php 
					$the_diff = $the_row[Amount] - 
						($meta_33_row[paid_for_principal]+$meta_34_row[paid_for_principal]+$meta_35_row[paid_for_principal])
						-
						($meta_33_row[paid_for_interests]+$meta_34_row[paid_for_interests]+$meta_35_row[paid_for_interests]);

					if($the_diff > 00.1){

						echo "<font color=red>".number_format($the_diff,2)."</font>";
					}
				?>

			</td>
			



		<tr>
      <?php
	}
  ?>
	  </tbody>
        
        <tfoot>
		   <tr >
			  
			 
			<td bgcolor="#efefef" colspan=8 valign="top" align="center">
				<strong></strong>
			</td>
			<td bgcolor="#efefef" valign="top" align="right">
				<?php echo number_format($sum_amount,2);?>
			</td>
			<td bgcolor="#efefef" valign="top" align="center">
				<strong></strong>
			</td>
			<td bgcolor="#efefef" valign="top" align="center">
				<strong></strong>
			</td>
			 <td bgcolor="#efefef" valign="top" align="center">
				<strong></strong>
			</td>

		   <td bgcolor="#efefef" valign="top" align="center">
				<strong></strong>
			</td>
			 <td bgcolor="#efefef" valign="top" align="center">
				<strong></strong>
			</td>

		  <td bgcolor="#efefef" valign="top" align="center">
				<strong></strong>
			</td>
			 <td bgcolor="#efefef" valign="top" align="center">
				<strong></strong>
			</td>

			<td bgcolor="#efefef" valign="top" align="center" >
				<strong></strong>
			</td>
			  <td bgcolor="#efefef" valign="top" align="center">
				<strong></strong>
			</td>
			 
			  
		  </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
