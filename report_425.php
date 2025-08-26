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
	<strong>รายชื่อสถานประกอบการที่ส่งเรื่องไปยังระบบการติดตามและดำเนินคดี <?php echo $province_text;?> <?php if(!$_POST["ddl_year"]){ ?>  ทุกปีงบประมาณ  <?php }else{ ?> ประจำปี <?php echo formatYear($year_selected); } ?></strong>
	<br />
	<br />
</div>
    
    
<table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      
      <tr>
        <td width="0" rowspan="3" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="0"   colspan="4" align="center" valign="bottom"><div align="center"><strong>ข้อมูลสถานประกอบการ</strong></div></td>
        <td colspan="14" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>การปฏิบัติตามกฎหมาย</strong></div></td>
        </tr>
      <tr>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center"><strong>เลขทะเบียนนายจ้าง</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center"><strong>ชื่อสถานประกอบการ</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center"><strong>ที่ตั้ง</strong></div></td>
		<td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center"><strong>ปีที่ปฏิบัติ</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>จำนวนลูกจ้าง<br />(ราย)</strong> </div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>อัตราส่วน <?php echo $ratio_to_use;?>:1 (ราย)
</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" >
            <div align="center"><strong>รับคนพิการเข้าทำงาน<br />ตามมาตรา 33<br />(ราย)</strong></div>
        </td>
        <td width="0" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>จ่ายเงินแทนการรับคนพิการ		
        ตามมาตรา 34  (บาท)</strong></div></td>
        <td colspan="2" align="center" valign="bottom" ><div align="center"><strong>การให้สัมปทาน</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ยอดเงินค้างชำระ</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ปฎิบัติตามกฎหมาย<br />แต่ไม่ครบอัตราส่วน</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ไม่ปฏิบัติตามกฎหมาย </strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>สถานะ<br />การดำเนินการ<br />ตามกฎหมาย</strong></div></td>
	    <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ผู้ที่ส่งเรื่อง</strong></div></td>
		<td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>วันที่ส่ง</strong></div></td>
		<td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ส่งแล้ว</strong></div></td>
	  </tr>
      <tr >
        <td width="<?php echo $w75;?>" align="center" valign="bottom" ><div align="center"><strong>คนพิการ<br />
          
          (ราย)
</strong></div></td>
        <td width="<?php echo $w75;?>" align="center" valign="bottom" ><div align="center"><strong>ผู้ดูแลคนพิการ<br />
        (ราย) </strong></div></td>
        </tr>
      </thead>
      <tbody>
      <?php
		  $lawful_result = mysql_query($main_sql3);	
		  
		  while ($lawful_row = mysql_fetch_array($lawful_result)) {
		  
			$row_count++;
			$company_name_to_use = formatCompanyName($lawful_row["CompanyNameThai"],$lawful_row["CompanyTypeCode"],$lawful_row["CompanyTypeName"]);
			
			$address_to_use = getAddressText($lawful_row);
			//
			
			$final_employee = getEmployeeRatio( $lawful_row["company_employees"],$ratio_to_use);
			
							
			//echo $the_money_sql;
			
			$paid_money = $lawful_row["paid_amount"];
			
			
			$this_row = 0;
			
			$curator_user = $lawful_row["curator_user_count"];
			
			$curator_usee = $lawful_row["curator_usee_count"];
		
			
			
	  ?>
              <tr>
                <td width="<?php echo $w50?>"  valign="top"><div align="center"><?php echo $row_count;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $lawful_row["CompanyCode"]?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $company_name_to_use;?></div>          </td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo $lawful_row["Year"] + 543;?></div></td>
				<td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo $lawful_row["company_employees"]?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php 
																					echo $final_employee; 
																					$extra_employee = $final_employee;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php 
				
														echo $lawful_row["Hire_NumofEmp"];
														$extra_employee = $extra_employee - $lawful_row["Hire_NumofEmp"];
				
														?></div></td>
               <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo formatMoneyReport($paid_money);?></div></td>
               <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php 
				
										echo $curator_usee;
										$extra_employee = $extra_employee - $curator_usee;
										
										?></div></td>
                    <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php
											
											echo $curator_user;
											$extra_employee = $extra_employee - $curator_user;
											
											if($extra_employee < 0){
												$extra_employee = 0;
											}
					
					
											?></div></td>
                    <td width="<?php echo $w75?>" align="right"  valign="top">
<?php
							
							
							
							//start recript....
							$this_lawful_year = $the_year;
							$this_id = $lawful_row["the_company_cid"];
							
							//reset this
							$paid_money = 0;
							
							$interest_date = dateDiffTs(strtotime(date("$this_lawful_year-01-31")), strtotime(date("Y-m-d")));
							
							$start_money = $extra_employee*$wage_rate*$year_date;
							
							//echo "extra_employee: ".$extra_employee;
							//echo "<br>wage_rate: ".$wage_rate;
							//echo "<br>year_date: ".$year_date;
							
							//echo "<br>start_money: ".$start_money;
								
							$payback_money = $lawful_row["payback_amount"];
							
							
							//echo $start_money . " - " . $paid_money;
							
							$owned_money = $start_money - $paid_money ;//+$payback_money
							
							//echo $owned_money . "-" ;
							
							if($owned_money < 0){
								$owned_money = 0;
							}
												//generate reciept info
												$the_sql = "select 
																receipt.ReceiptDate, receipt.Amount
															from payment, receipt , lawfulness
															where 
															receipt.RID = payment.RID
															and
															lawfulness.LID = payment.LID
															and
															ReceiptYear = '$this_lawful_year'
															and
															lawfulness.CID = '".$this_id."' 
															
															and
															is_payback != 1
															order by ReceiptDate asc";
												
												//echo $the_sql;
												$the_result = mysql_query($the_sql);
												
												$have_receipt = 0;
												while($result_row = mysql_fetch_array($the_result)){
												
													$have_receipt = 1;
													
													//echo "select * from receipt where RID = '".$result_row["RID"]."'";										
													$receipt_row = $result_row;//getFirstRow("select * from receipt where RID = '".$result_row["RID"]."'");
												
															
															$owned_money = $owned_money - $paid_from_last_bill;//+ $receipt_row["Amount"]
															
															//echo $owned_money . " || ";
															
															
																$this_paid_amount = $receipt_row["Amount"];											
																								
																								
																
																if(!$last_payment_date){
																	$last_payment_date = "$this_lawful_year-01-31 00:00:00";
																}
																						
																if(strtotime(date($last_payment_date)) 
																	< 
																	strtotime(date("$this_lawful_year-01-31"))){
																
																	$last_payment_date = "$this_lawful_year-01-31 00:00:00";
																
																}
																
																//echo $last_payment_date . " --=-- ";
																	
																
																$interest_date = getInterestDate($last_payment_date, $this_lawful_year, $receipt_row["ReceiptDate"]);
																
	
																$last_payment_date_to_show = $last_payment_date;
																$last_payment_date = $receipt_row["ReceiptDate"];
																
																if($this_lawful_year >= 2012){ //only show interests when 2012+
																	
																	//echo $interest_date . " - " . $owned_money . " - " . $year_date;
																	
																	$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
																}else{
																	$interest_money = 0;
																}
																
																
																//echo $interest_money . " || ";
																
																
																
																if($total_pending_interest > 0){
																
																	$interest_money += $total_pending_interest;
																
																}
																
																
																
																if($this_paid_amount < $interest_money){
																	$have_pending_interest = 1;
																	$interest_money_to_show = $this_paid_amount;
																}else{
																	$interest_money_to_show = $interest_money;
																}
																
																//if($last_payment_date_to_show != "$this_lawful_year-01-31 00:00:00"){
																if($is_pay_detail_first_row > 0){
																
																}
																$is_pay_detail_first_row++;
															
															
																
																$this_paid_money = $this_paid_amount-$interest_money;
																
																if($this_paid_money < 0){
																	$this_paid_money = 0;
																}
																
																
																$paid_money += $this_paid_money;
																
																$paid_from_last_bill = $this_paid_money;
																
															
															if($this_paid_amount < $interest_money){
																$pending_interest = (($interest_money - $this_paid_amount ));
																
																$total_pending_interest = $pending_interest;
															
															 }else{
															
																$total_pending_interest = 0;
															
															}
													}		//end while for looping to display payment details	
													//if($start_money > 0){
													//only show this for 2012++ year
													if($this_lawful_year > 2011){
													//only show this if has starting money
															//update owned money here
														$owned_money = $start_money - $paid_money;// - $payback_money
															
														
														$the_sql = "select max(paymentDate) from payment, receipt , lawfulness
															where 
															receipt.RID = payment.RID
															and
															lawfulness.LID = payment.LID
															and
															ReceiptYear = '$this_lawful_year'
															and
															lawfulness.CID = '".$this_id."' 
															
															and
															is_payback != 1
															";
														
														
														$actual_interest_date = getFirstItem($the_sql);
														
														
													
													//cal culate interest money
													
													if($owned_money <= 0){
													
														//no longer calculate interests
														$interest_date = 0;
													}else{
														$interest_date = getInterestDate($actual_interest_date, $this_lawful_year, "Y-m-d");
													}
													
													//echo "<br>$actual_interest_date" . " / ". $this_lawful_year . " / ".  strtotime(date("Y-m-d"))."<br>";
													
													if($this_lawful_year >= 2012){ //only show interests when 2012+
														$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
													}else{
														$interest_money = 0;
													}
																					
															$the_final_money = $owned_money + $interest_money +$payback_money +$total_pending_interest;
															/*
															echo $the_final_money . " = ".$owned_money 
															." + ". $interest_money 
															." + ".$payback_money 
															. " + " . $total_pending_interest;	*/			
															
															//$the_final_money = number_format($the_final_money,2);
															$the_final_money = round($the_final_money,2);
														
															if($the_final_money < 0){
																echo "-";
															}else{
																echo "";
															}
															
															if(floor($the_final_money) > 0){
																echo "<font color='red'>";
															}else if($the_final_money < 0){
																echo "<font color='green'>";
																$the_final_money = $the_final_money * -1;
															}else{
																echo "<font>";
															}
														
															echo formatNumber($the_final_money);
															
															echo "</font>";
															}//starting_money > 0?></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php if($lawful_row["LawfulStatus"] == 2){echo 'X';}?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php if($lawful_row["LawfulStatus"] == 0){echo 'X';}?></div></td>
                <td width="<?php echo $w50?>"  valign="top"><?php writeHTML($lawStatusMapping[$lawful_row["LawStatus"]]);?></td>
				
				<?php 
				/*
				$now = date("Y");
				$now_test = date("Y-m-d");
				
				$current = $now - 10;
				$lawful_year = $the_year - 10;
				
				//$test_2_year = $current - $the_year;
				
				$expire2Year = $current + 2;
				$expire1Year = $current + 1;
				//$t = strtotime($current);
				//$month_8_test = date("Y",$t);
				
				//$arr = explode('-', $month_8_test);
				
				$raw_month = date('Y-m-d', strtotime($now_test. ' - 10 Year'));
				$expire8Month = date('Y', strtotime($raw_month. ' - 8 months'));

					$sql = "SELECT 
							   
							  COUNT(*)
							FROM 
							  lawfulness l 
							  INNER JOIN company c ON l.CID = c.CID
							  INNER JOIN provinces p ON p.province_id = c.Province 
							  AND l.year = '$the_year' 
							  AND l.cid = '".$this_id."'
							  AND l.year = $expire2Year 
							  $sub_province_filter
							  ";
				
				$deadline2Year = getFirstItem($sql);
				
				
					$sql2 = "SELECT 
							   
							  COUNT(*)
							FROM 
							  lawfulness l 
							  INNER JOIN company c ON l.CID = c.CID
							  INNER JOIN provinces p ON p.province_id = c.Province 
							  AND l.year = '$the_year' 
							  AND l.cid = '".$this_id."'
							  AND l.year = $expire1Year
							  $sub_province_filter
							  ";
				
				$deadline1Year = getFirstItem($sql2);
				
				
					$sql3 = "SELECT 
							   
							  COUNT(*)
							FROM 
							  lawfulness l 
							  INNER JOIN company c ON l.CID = c.CID
							  INNER JOIN provinces p ON p.province_id = c.Province 
							  AND l.year = '$the_year' 
							  AND l.cid = '".$this_id."'
							  AND l.year = $expire8Month
							  $sub_province_filter
							  ";
				
				$deadline8Month = getFirstItem($sql3);
				*/
					?>
					
				<?php  $sql_cour = "select 1 from lawfulness_meta where meta_lid = '".$lawful_row["LID"]."' and meta_for = 'courted_flag'"; 
					   $ex_cour_sent = getFirstItem($sql_cour);
				?>	
					
				<td width="<?php echo $w50?>"  valign="top"><div align="center"><?php $court_by = getFirstItem("select uu.user_name 
																									from lawfulness_meta bb , users uu
																									where bb.meta_lid = '".$lawful_row["LID"]."'
																									and bb.meta_for = 'courted_by'
																									and uu.user_id = meta_value
																									
																									");
																					echo $court_by;?></div></td>	
				<td width="<?php echo $w50?>"  valign="top"><div align="center"><?php $court_date = getFirstItem("select bb.meta_value 
																									from lawfulness_meta bb
																									where bb.meta_lid = '".$lawful_row["LID"]."'
																									and bb.meta_for = 'courted_date'
																									
																									");
																					echo formatDateThai($court_date,1,5);?></div></td>	
				<td width="<?php echo $w50?>"  valign="top"><div align="center"><?php if($ex_cour_sent == "1"){echo 'X';}?></div></td>		
					
				<!--<td width="<?php //echo $w50?>"  valign="top"><div align="center"><?php // if($deadline8Month){echo 'X';}?></div></td> -->
				<!--<td width="<?php //echo $w50?>"  valign="top"><div align="center"><?php// if($deadline1Year){echo 'X';}?></div></td>	-->
				<!--<td width="<?php //echo $w50?>"  valign="top"><div align="center"><?php //if($deadline2Year){echo 'X';}?></div></td>	-->
				
				<!--<td width="<?php //echo $w50?>"  valign="top"><div align="center"><?php //if($deadline1Year){echo 'X';}?></div></td>	 -->
				<!--<td width="<?php //echo $w50?>"  valign="top"><div align="center"><?php //if($deadline8Month){echo 'X';}?></div></td> -->
				
				<!--td width="<?php //echo $w50?>"  valign="top"><div align="center"><?php //echo $lawful_row["LID"];?></div></td> -->
				<!--td width="<?php //echo $w50?>"  valign="top"><div align="center"><?php //echo $this_id;?></div></td> -->

			  </tr>
     	<?php 
				//yoes 20140822  -- reset this VAR
				$paid_from_last_bill = 0;
				$last_payment_date = "";
				
				
				$owned_money = 0;
				$interest_money = 0;
				$payback_money = 0;
				$total_pending_interest = 0;
				
			}?>
    </tbody>
    <tfoot>
    </tfoot>
</table>

<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?></div>

