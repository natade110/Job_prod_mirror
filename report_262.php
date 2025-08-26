<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="th" xml:lang="th">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>รายงานข้อมูลสถานประกอบการ จังหวัดกระบี่ ประจำปี 2559</title>
</head>

<body><?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_26.xls");
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

//yoes 20160205
if($_GET["ddl_year"]){
	$_POST["ddl_year"] = $_GET["ddl_year"];
}

if(isset($_POST["ddl_year"])){
	$the_year = $_POST["ddl_year"];
}

if($the_year > 2012){
	$is_2013 = 1;
}

$the_year_to_use = formatYear($the_year);

$province_text = "ทั่วประเทศ";
$province_filter = "";		

//yoes 20160205
if($_GET["Province"]){
	$_POST["Province"] = $_GET["Province"];
}

if(isset($_POST["Province"]) && $_POST["Province"] != "" && $_POST["rad_area"] == "province"){
	$province_filter = " and a.Province = '".$_POST["Province"]."'";
	
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}


//yoes 20150604
//extra conditions for ดินแดง
//$province_filter .= " and District LIKE '%ดินแดง%'";


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

//yoes 20210422 - temporary for bee
//$condition_sql .= " and lawfulStatus in (0,2)";



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
				
				$typecode_filter

				
			
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
						,sum(bb.Employees) as the_sub_sum
			
					 FROM 
						company aa
							join lawfulness bb
								on aa.cid = bb.cid
								and
								bb.Year = '$the_year'
					group by 
						
						CompanyCode	
						
					having 
						sum(bb.Employees) > 99	
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
				
				$typecode_filter
				
				and
				a.companyCode = e.the_sub_companycode

			
			order by
				province_name, CompanyNameThai asc
			
			
			";



}			
			
			
if($_GET[limit]){
	
	
	$main_sql .= "limit 0,100";
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
            <h1 style="font-size:16px;">รายงานข้อมูลสถานประกอบการ  <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?></h1><br />

              <br>
</div>
    
    
<table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td width="0" rowspan="3" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="0"   <?php if(!$is_2013){ // yoes 20150330?>colspan="5"<?php }else{?>colspan="4"<?php }?>align="center" valign="bottom"><div align="center"><strong>ข้อมูลสถานประกอบการ</strong></div></td>
        <td colspan="11" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>การปฏิบัติตามกฎหมาย</strong></div></td>
        </tr>
      <tr >
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center"><strong>เลขทะเบียนนายจ้าง</strong></div></td>
        
		<?php if(!$is_2013){ // yoes 20150330?>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center"><strong>เลขที่สาขา</strong></div></td>
        <?php }?>
        
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center"><strong>ชื่อสถานประกอบการ</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center"><strong>ที่ตั้ง</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>จำนวนลูกจ้าง<br />(ราย)</strong> </div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>อัตราส่วน <?php echo $ratio_to_use;?>:1 (ราย)
</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>
          
          รับคนพิการเข้าทำงาน
  <br />ตามมาตรา 33
  <br />(ราย)
          
          
        </strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>จ่ายเงินแทนการรับคนพิการ		
        ตามมาตรา 34  (บาท)</strong></div></td>
        <td colspan="2" align="center" valign="bottom" ><div align="center"><strong>การให้สัมปทาน</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ยอดเงินค้างชำระ</strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ปฎิบัติตามกฎหมาย </strong></div></td>
        <td width="<?php echo $w75;?>" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ปฎิบัติตามกฎหมาย<br />
          
          แต่ไม่ครบอัตราส่วน
          
</strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ไม่ปฏิบัติตามกฎหมาย </strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>ไม่เข้าข่ายจำนวน<br />
        ลูกจ้างตามกฎหมาย </strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom" ><div align="center"><strong>หมายเหตุ </strong></div></td>
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
	  	  //echo $main_sql;
	  
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
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $lawful_row["CompanyCode"]?></div></td>
                
                <?php if(!$is_2013){ // yoes 20150330?>
                 <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $lawful_row["BranchCode"]?></div></td>
                <?php }?>
                
                
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $company_name_to_use;?></div>          </td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo $lawful_row["company_employees"]?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php 
																					echo $final_employee; 
																					$extra_employee = $final_employee;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php 
				
														echo $lawful_row["Hire_NumofEmp"];
														$extra_employee = $extra_employee - $lawful_row["Hire_NumofEmp"];
				
														?></div>
                                                        
                                                        </td>
                
                
                
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
							
							//yoes 20150601
							if($this_lawful_year == 2011){
								
								//use wage-rate by province instead
								$wage_rate = default_value(getFirstItem("select province_54_wage from provinces where province_id = '".$lawful_row["Province"]."'"),0);
								$wage_rate = $wage_rate/2;
								
								//echo $wage_rate;
								
							}else{
							
								$wage_rate = default_value(getFirstItem("select var_value from vars where var_name = 'wage_".$this_lawful_year."'"),159);
							
							}
							
							
							$year_date = 365;
							
							$interest_date = dateDiffTs(strtotime(date(getDefaultLastPaymentDateByYear($this_lawful_year))), strtotime(date("Y-m-d")));
							
							$start_money = $extra_employee*$wage_rate*$year_date;
							
							//echo "extra_employee: ".$extra_employee;
							//echo "<br>wage_rate: ".$wage_rate;
							//echo "<br>year_date: ".$year_date;
							
							//echo "<br>start_money: ".$start_money;
							
							$the_sql = "select sum(receipt.Amount) 
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
								is_payback = 1
								";
								
							$payback_money = getFirstItem("$the_sql");
							
							
							//echo $start_money . " - " . $paid_money . "....";
							
							$owned_money = $start_money - $paid_money ;//+$payback_money
							
							//echo $owned_money . "-" ;
							
							if($owned_money < 0){
								$owned_money = 0;
							}
							
							
							?>
                            
                            
                            <?php 
												//generate reciept info
												$the_sql = "select * from payment, receipt , lawfulness
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
													$receipt_row = getFirstRow("select * from receipt where RID = '".$result_row["RID"]."'");
												
												?>   
                                                           
                                                           
                                                            <?php
															
															$owned_money = $owned_money - $paid_from_last_bill;//+ $receipt_row["Amount"]
															
															//echo "paid last bill: ".$paid_from_last_bill . "";
															//echo $owned_money . " || ";
															
															?>
                                                           
                                                           
                                                            
                                                            <?php
															
																$this_paid_amount = $receipt_row["Amount"];											
																								
																								
																
																if(!$last_payment_date){
																	$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);
																}
																						
																if(strtotime(date($last_payment_date)) 
																	< 
																	strtotime(date(getDefaultLastPaymentDateByYear($this_lawful_year)))){
																
																	$last_payment_date = getDefaultLastPaymentDateByYear($this_lawful_year);
																
																}
																
																//echo $last_payment_date . " --=-- ";
																	
																
																$interest_date = getInterestDate($last_payment_date, $this_lawful_year, $receipt_row["ReceiptDate"]);
																
	
																$last_payment_date_to_show = $last_payment_date;
																$last_payment_date = $receipt_row["ReceiptDate"];
																
																if($this_lawful_year >= 2012){ //only show interests when 2012+
																// yoes 20150330 -- > always show interest
																//if(1==1){
																	
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
																
																
																
															?>
                                                            
                                                            
                                                            <?php 
																
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
															
														?>
                                                         
                                                         
                                                         
                                                <?php
													
													}		//end while for looping to display payment details	
																						
												?>
                                                
                                                
                                                
                                                
                                                <?php //if($start_money > 0){
													//only show this for 2012++ year
													//if($this_lawful_year > 2011){
													if(1==1){ //yoes 20150330 ==>always show this anyway	
														
													//only show this if has starting money
												?>
                                            	
														<?Php 
															
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
													//yoes 20150330 ==>always show this anyway	
													//if(1==1){
														$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
													}else{
														$interest_money = 0;
													}
													
													?>
                                                    
                                                    
                                                   
                                                    
                                                    
                                                    
                                                  
                                                        
                                                        <?php 
																
															
															//yoes 20210408
															if($the_year >= 2018 && $the_year < 2050){
																
																$m33_principal_row = getFirstRow("
																			
																				select
																					sum(p_amount) as the_principals
																					, sum(p_interests) as the_interests
																				from
																					lawful_33_principals
																				where
																					p_lid = '".$lawful_row["LID"]."'
																			
																			");
																			
																$m35_principal_row = getFirstRow("
																			
																				select
																					sum(p_amount) as the_principals
																					, sum(p_interests) as the_interests
																				from
																					lawful_35_principals
																				where
																					p_lid = '".$lawful_row["LID"]."'
																			
																			");
																
																
																$the_final_money = $owned_money + $interest_money +$payback_money +$total_pending_interest
																							+$m33_principal_row[the_principals]+$m33_principal_row[the_interests]
																							+$m35_principal_row[the_principals]+$m35_principal_row[the_interests];
																
																
															}else{																
															
																								
																$the_final_money = $owned_money + $interest_money +$payback_money +$total_pending_interest;
															
															}
															
															
															
															
															/*echo $the_final_money . " = ".$owned_money 
															." + ". $interest_money 
															." + ".$payback_money 
															. " + " . $total_pending_interest;*/
															
															//$the_final_money = number_format($the_final_money,2);
															$the_final_money = round($the_final_money,2);
														
															if($the_final_money < 0){
														?>
                                                        	 -
                                                                
                                                        <?php }else{?>
                                                        
	                                                      	 
                                                                                       
                                                        <?php }?>
                                                        
                                                      
                                                        <?Php 
														
															
														
															
															
															//yoes 20160117 -- remove this for AA accessibility
															if(1==0){
																if(floor($the_final_money) > 0){
																	echo "<font color='red'>";
																}else if($the_final_money < 0){
																	echo "<font color='green'>";
																	$the_final_money = $the_final_money * -1;
																}else{
																	echo "<font>";
																}
															}
															
															echo formatNumber($the_final_money);
															
															//yoes 20160117 -- remove this for AA accessibility
															if(1==0){
																echo "</font>";
															}
															
															?>
                                                            
                                                            
                                                        
                                                
                                                
                                                <?php }//starting_money > 0?>
                                                
                                                
                                                
                    			
                    
                    </td>
                    <td width="<?php echo $w75?>"  valign="top"><div align="center">
                      <?php if($lawful_row["LawfulStatus"] == 1){echo 'X';}?>
                    </div></td>
               
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php if($lawful_row["LawfulStatus"] == 2){echo 'X';}?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php if($lawful_row["LawfulStatus"] == 0){echo 'X';}?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php if($lawful_row["LawfulStatus"] == 3){echo 'X';}?></div></td>
                <td width="<?php echo $w50?>"  valign="top"><?php if($lawful_row["Status"] == 0){echo "ปิดกิจการ";}else{echo "&nbsp;";}?></td>
              </tr>
     
     
     			
     	
     	<?php 
				
				//yoes 20140822  -- reset this VAR
				$paid_from_last_bill = 0;
				$last_payment_date = "";
				
				
				$owned_money = 0;
				$interest_money = 0;
				$payback_money = 0;
				$total_pending_interest = 0;
				
				
			} 
		?>
	  </tbody>
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>

</body>
</html>
