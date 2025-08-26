<?php


//this is a report to use with "org_list"
include "db_connect.php";


if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_24.xls");
	$is_excel = 1;

}else{
	header ('Content-type: text/html; charset=utf-8');
}






////////////////////////
////////////////
////////////////		START BUILDING CONDITIONS // GET THIS FROM ORG_LIST.PHP
/////////////////
/////////////////////////////



$input_fields = array(
	'Employees'
	,'CompanyCode'
	
	,'CompanyNameEng'
	,'Address1'
	
	,'Moo'
	,'Soi'
	,'Road'
	,'Subdistrict'
	,'District'
	
	,'Province'
	,'Zip'
	,'Telephone'
	,'email'
	,'TaxID'
	
	,'CompanyTypeCode'
	,'BusinessTypeCode'
	,'BranchCode'
	,'org_website'
	
	
	,'NoRecipientFlag'
	
	
	
	);
//if has $_post then do filter
//print_r($_POST);
$use_condition = 0;
$condition_sql = " and 1=1";


///yoes 20130730 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$condition_sql .= " and z.CompanyTypeCode >= 200  and z.CompanyTypeCode < 300";
	
}else{
	
	$condition_sql .= " and z.CompanyTypeCode < 200";
	
}




for($i = 0; $i < count($input_fields); $i++){
	
	if(strlen($_POST[$input_fields[$i]])>0){
		
		$use_condition = 1;
		
		if($input_fields[$i] == "Province"  ){
			$condition_sql .= " and z.$input_fields[$i] like '".doCleanInput($_POST[$input_fields[$i]])."'";
		}elseif($input_fields[$i] == "Employees" ){
			$condition_sql .= " and y.$input_fields[$i] >= '".doCleanInput($_POST[$input_fields[$i]])."'";
		}else{
			$condition_sql .= " and z.$input_fields[$i] like '%".doCleanInput($_POST[$input_fields[$i]])."%'";
		}
		
	}
}	

//special search condition for company name th
//make it so it filter as %LIKE% instead
if(strlen($_POST["CompanyNameThai"]) > 0){
	
	
	$name_exploded_array = explode(" ",doCleanInput($_POST["CompanyNameThai"]));
	
	//print_r($name_exploded_array);
	for($i=0; $i<count($name_exploded_array);$i++){
	
		if(strlen(trim($name_exploded_array[$i]))>0){
			//echo $name_exploded_array[$i];
			$use_condition = 1;
			$condition_sql .= " and z.CompanyNameThai like '%".doCleanInput($name_exploded_array[$i])."%'";
			
		}
	
	}
	
}

if(strlen($_POST["LawfulFlag"]) > 0){

	//added Yoes april 03
	$lawful_condition = " and y.LawfulStatus = '".$_POST["LawfulFlag"]."'";

	//if non-lawful then also show records that didn't have lawfulness
	if($_POST["LawfulFlag"] == "0"){
		$lawful_condition = " and (y.LawfulStatus = '0' or y.LawfulStatus is null)";
	}

}

					
//
if($have_search_id){
	$use_condition = 1;
	$condition_sql .= " and z.CID = '".doCleanInput($_GET["search_id"])."'";
}

//echo $condition_sql;
//save condition to session
//$_SESSION["org_list_condition"] = $condition_sql;

if($mode == "add_company_announce"){
	
	$get_announce_org_sql = "select * from announcecomp where AID = '$this_gov_doc_id'";
	
	$announce_org_result = mysql_query($get_announce_org_sql);
	
	while ($announce_org_post_row = mysql_fetch_array($announce_org_result)) {
		$the_cid = $announce_org_post_row["CID"];
		$condition_sql .= " and CID != '$the_cid'";
	}						
	
}

$cur_year = date("Y"); 
	
if(isset($_POST['ddl_year'])){
	$cur_year = $_POST['ddl_year'];
}

if($for_year){
	$cur_year = $for_year;
}
												
//echo $cur_year;


//YEAR - BRANCH thing
//echo $cur_year;
if($cur_year >= 2013){

	//show main branch only
	$condition_sql .= " and BranchCode < 1";
	$is_2013 = 1;

}


if($_POST["advanced_search"] == 1){

	$the_outer_join = " left outer ";

}



////////////////////////
////////////////
////////////////		END BUILDING CONDITIONS
/////////////////
/////////////////////////////


$the_year = $cur_year;



////// starts LOGIC here


$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_$the_year'"),100);

if($is_2013){

	$condition_sql .= " and branchCode < 1";

}



$main_sql = "

			select
				*
				, y.Employees as company_employees
				, z.CID as the_company_cid
				
				, y.LID as the_lid
			from
				company z
				, lawfulness y
				, provinces c
			where
				z.CID = y.CID
				and
				y.Year = '$the_year'
				
				and 
				z.Province = c.province_id
				
				$province_filter
				
				$last_modified_sql
				
				$condition_sql
				
				$lawful_condition

			
			order by
				province_name, CompanyNameThai asc
			
			
			";


////////			
if($is_2013){



$main_sql = "

			select
				*
				, y.Employees as company_employees
				, z.CID as the_company_cid
				
				, y.LID as the_lid
			from
				company z
				, lawfulness y
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
				z.CID = y.CID
				and
				y.Year = '$the_year'
				
				and 
				z.Province = c.province_id
				
				$province_filter
				
				$condition_sql
				
				$last_modified_sql
				
				$lawful_condition
				
				and
				z.companyCode = e.the_sub_companycode

			
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
<table border="0" align="center">
  <tr>
    <td><table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
      <thead>
        <tr>
          <td rowspan="2"  valign="top"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
          <td colspan="5"  valign="top"><div align="center"><strong>สถานประกอบการ</strong></div></td>
          <td rowspan="2"  valign="top"><div align="center" style="vertical-align:middle;"><strong>อัตราส่วนที่ต้องจ้างคนพิการ <?php echo $ratio_to_use;?>:1 (ราย) </strong></div></td>
          <?php if($_POST["report_33"]){?>
          <td colspan="2"  valign="top" bgcolor="#E6F4FF"><div align="center"><strong>รับคนพิการเข้าทำงาน ม.33</strong></div></td>
          <?php }?>
          <?php if($_POST["report_34"]){?>
          <td colspan="2"  valign="top" bgcolor="#FFFFEC"><div align="center"><strong>จ่ายเงินเข้ากองทุน ม.34</strong></div></td>
          <?php }?>
          <?php if($_POST["report_35"]){?>
          <td colspan="3" valign="top" bgcolor="#FFF2EA" ><div align="center"><strong>ให้สัมปทาน ม.35</strong></div></td>
          <?php }?>
          <?php if($_POST["see_lawfulness"]){?>
          <td colspan="4"  valign="top" bgcolor="#EFFFDF"><div align="center"><strong>การปฏิบัติตามกฏหมาย</strong></div></td>
          <?php }?>
          </tr>
        <tr>
          <td  valign="top"><div align="center" style="vertical-align:middle;"><strong>เลขทะเบียนนายจ้าง</strong></div></td>
          <td  valign="top"><div align="center"><strong>ชื่อสถานประกอบการ</strong></div></td>
          <td  valign="top"><div align="center"><strong>จำนวนสาขา(แห่ง)</strong></div></td>
          <td  valign="top"><div align="center"><strong>ที่อยู่(สำนักงานใหญ่)</strong></div></td>
          <td  valign="top"><div align="center"><strong>จำนวนลูกจ้างทั้งหมด(ราย)</strong></div></td>
          <?php if($_POST["report_33"]){?>
          <td  valign="top" bgcolor="#E6F4FF"><div align="center" style="vertical-align:middle;"><strong>จำนวนคนพิการ (ราย)</strong></div></td>
          <td  valign="top" bgcolor="#E6F4FF" >&nbsp;</td>
          <?php }?>
          
          <?php if($_POST["report_34"]){?>
          <td  valign="top" bgcolor="#FFFFEC">จำนวนเงินรวม (บาท)</td>
          
          <!--- this is just a REFERENCE COLUMN so we get curator values
          <td  valign="top" bgcolor="#E6F4FF"><div align="center" style="vertical-align:middle;"><strong>จำนวนคนพิการ (ราย)</strong></div></td>
          <td  valign="top" bgcolor="#E6F4FF" >&nbsp;</td>
          <td valign="top" ><div align="center"><strong>คนพิการ<br />
            (ราย) </strong></div></td>
          <td valign="top" ><div align="center"><strong>ผู้ดูแลคนพิการ<br />
            (ราย) </strong></div></td>
            -->
            
          <td  valign="top" bgcolor="#FFFFEC"><strong>ยอดเงินค้างชำระ</strong>(บาท)</td>
          <?php }?>
          
           <?php if($_POST["report_35"]){?>
          <td valign="top" bgcolor="#FFF2EA" ><div align="center"><strong>คนพิการ<br />
            (ราย) </strong></div></td>
          <td valign="top" bgcolor="#FFF2EA" ><div align="center"><strong>ผู้ดูแลคนพิการ<br />
            (ราย) </strong></div></td>
          <td  valign="top" bgcolor="#FFF2EA">&nbsp;</td>
          <?php }?>
          
          <td  valign="top" bgcolor="#EFFFDF"><div align="center"><strong>ปฎิบัติตามกฎหมาย </strong></div></td>
          <td  valign="top" bgcolor="#EFFFDF"><div align="center"><strong>ปฎิบัติตามกฎหมาย<br />
            แต่ไม่ครบอัตราส่วน </strong></div></td>
          <td  valign="top" bgcolor="#EFFFDF"><div align="center"><strong>ไม่ปฏิบัติตามกฎหมาย </strong></div></td>
          <td  valign="top" bgcolor="#EFFFDF"><div align="center"><strong>ไม่เข้าข่ายจำนวน<br />
            ลูกจ้างตามกฎหมาย </strong></div></td>
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
          <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $lawful_row["CompanyCode"]?></div></td>
          <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $company_name_to_use;?></div></td>
          <td width="<?php echo $w75?>"  valign="top"><div align="right">
            <?php 
																echo getFirstItem("select count(*) from company where CompanyCode = '".$lawful_row["CompanyCode"]."'");
															?>
          </div></td>
          <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
          <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo $lawful_row["company_employees"]?></div></td>
          <td width="<?php echo $w75?>"  valign="top"><div align="right">
            <?php 
																					echo $final_employee; 
																					$extra_employee = $final_employee;?>
          </div></td>
          
          
          
          
          
          <?php if($_POST["report_33"]){?>
          
          
              <td width="<?php echo $w75?>"  valign="top" bgcolor="#E6F4FF"><div align="right">
                <?php 
                    
                                                            echo $lawful_row["Hire_NumofEmp"];
                                                            $extra_employee = $extra_employee - $lawful_row["Hire_NumofEmp"];
                    
                                                            ?>
              </div></td>
              <td width="<?php echo $w75?>"  valign="top" bgcolor="#E6F4FF"  style="padding:0; margin:0;">
              
              <table border="1" cellspacing="0">
                <?php 
                        
                            //create table for lawful_employees
                            $le_sql = "
                                
                                    select 
                                        * 
                                    from 
                                        lawful_employees		
                                    where
                                        le_cid = '".$lawful_row["the_company_cid"]."'		
                                    and
                                        le_year = '".$cur_year."'
                                
                            ";
                            
                            $le_result = mysql_query($le_sql);	
                            
                            while ($le_row = mysql_fetch_array($le_result)) {
                            
                                ?>
                <tr>
                  <td><?php echo $le_row["le_code"]; ?></td>
                  <td><?php echo $le_row["le_name"]; ?></td>
                  <td><?php echo $le_row["le_position"]; ?></td>
                  <td><?php echo $le_row["le_wage"]; ?></td>
                  <td><?php echo getWageUnit($le_row["le_wage_unit"]); ?></td>
                  <td><?php echo formatDateThai($le_row["le_start_date"]); ?></td>
                  </tr>
                <?php
                            
                            }
                        
                        
                        
                        ?>
              </table></td>
          
          
          
          
          <?php }//if($_POST["report_33"]){?>
          
          
          
          <?php if($_POST["report_34"]){?>
          
          
           <?php 
		   			//want to see 34, but no 33, we has to populate 33 here to get employees number
                    if(!$_POST["report_33"]){
						echo $lawful_row["Hire_NumofEmp"];
						$extra_employee = $extra_employee - $lawful_row["Hire_NumofEmp"];
					}
			?>
          
          <td width="<?php echo $w75?>"  valign="top" bgcolor="#FFFFEC"><div align="right"><?php echo formatMoneyReport($paid_money);?></div></td>
         
         
         
         <!--- this is just a REFERENCE COLUMN so we get curator values
          <td width="<?php echo $w75?>"  valign="top"><div align="center">
            <?php 
				
										echo $curator_usee;
										$extra_employee = $extra_employee - $curator_usee;
										
										?>
          </div></td>
          <td width="<?php echo $w75?>"  valign="top"><div align="center">
            <?php
											
											echo $curator_user;
											$extra_employee = $extra_employee - $curator_user;
											
											if($extra_employee < 0){
												$extra_employee = 0;
											}
					
					
											?>
          </div></td>
          -->
          
          <td width="<?php echo $w75?>" align="right"  valign="top" bgcolor="#FFFFEC"><?php
							
							
							
							//start recript....
							$this_lawful_year = $the_year;
							$this_id = $lawful_row["the_company_cid"];
							
							//reset this
							$paid_money = 0;
							
							
							
							if($this_lawful_year == 2011){
								
								//use wage-rate by province instead
								$wage_rate = default_value(getFirstItem("select province_54_wage from provinces where province_id = '".$lawful_row["Province"]."'"),0);										
								$wage_rate = $wage_rate/2;
								//echo $wage_rate ;
							}else{
							
								$wage_rate = default_value(getFirstItem("select var_value from vars where var_name = 'wage_".$this_lawful_year."'"),159);
							
							}
							//echo $wage_rate;
							
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
							
							
							$owned_money = $start_money - $paid_money ;//+$payback_money
							
							
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
																
																
																	
																
																$interest_date = getInterestDate($last_payment_date, $this_lawful_year, $receipt_row["ReceiptDate"]);
																
	
																$last_payment_date_to_show = $last_payment_date;
																$last_payment_date = $receipt_row["ReceiptDate"];
																
																if($this_lawful_year >= 2012){ //only show interests when 2012+
																	$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
																}else{
																	$interest_money = 0;
																}
																
																
																
																
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
													if($this_lawful_year > 2011){
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
														$interest_money = doGetInterests($interest_date,$owned_money,$year_date);
													}else{
														$interest_money = 0;
													}
													
													?>
            <?php 
															$the_final_money = $owned_money + $interest_money +$payback_money +$total_pending_interest;
															
															//$the_final_money = number_format($the_final_money,2);
															$the_final_money = round($the_final_money,2);
														
															if($the_final_money < 0){
														?>
            -
            <?php }else{?>
            <?php }?>
            <?Php 
														
															
														
															
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
															
															?>
            <?php }//starting_money > 0?></td>
            
            
         <?php }//if($_POST["report_34"]){?>
            
            
            
            <?php if($_POST["report_35"]){?>
            
            
          <td width="<?php echo $w75?>"  valign="top" bgcolor="#FFF2EA"><div align="center">
            <?php 
				
										echo $curator_usee;
										
										
										?>
          </div></td>
          <td width="<?php echo $w75?>"  valign="top" bgcolor="#FFF2EA"><div align="center">
            <?php
											
											echo $curator_user;
											
					
					
											?>
          </div></td>
          <td width="<?php echo $w75?>"  valign="top" bgcolor="#FFF2EA" style="padding:0; margin:0;">
            
            
            <table border="1" cellspacing="0">
              <?php
                            
                        
                        $sql = "select * from curator where curator_lid = '".$lawful_row["the_lid"]."' and curator_parent = 0 order by curator_id asc";
                        
                        
                        $org_result = mysql_query($sql);
                        while ($post_row = mysql_fetch_array($org_result)) {			
                            
                           
                    ?>
              
              <tr>
                
                
                <td>
                  <?php echo doCleanOutput($post_row["curator_name"]);?>
                  </td>
                <td>
                  
                  <?php if($post_row["curator_is_disable"] == 1){?>
                  คนพิการ
                  <?php }else{ ?>
                  ผู้ดูแลคนพิการ 
                  <?php }?>
                  
                  
                  </td>
                
                
                <td>
                  <?php echo doCleanOutput($post_row["curator_event"]);?>
                  </td>
                
                <td>
                  <?php echo formatNumber($post_row["curator_value"]);?>
                  </td>
                
                <td>
                  <?php echo formatDateThai($post_row["curator_start_date"]);?>
                  </td>
                
                <td>
                  <?php echo formatDateThai($post_row["curator_end_date"]);?>
                  </td>
                
                <td>
                  <?php 
                                    
							
							echo number_format(dateDiffTs(strtotime($post_row["curator_start_date"]), strtotime($post_row["curator_end_date"])),0);
							
							?> 
                  </td>
                
                
                </tr>
              
              
              <?php
                    }
                ?>
              </table>
            
            
            
            
            
            
          </td>
          
          <?php }//if($_POST["report_35"]){?>
          
          
          
          
           <?php if($_POST["see_lawfulness"]){?>
          <td  valign="top" bgcolor="#EFFFDF"><div align="center">
            <?php if($lawful_row["LawfulStatus"] == 1){echo 'X';}?>
          </div></td>
          <td  valign="top" bgcolor="#EFFFDF"><div align="center">
            <?php if($lawful_row["LawfulStatus"] == 2){echo 'X';}?>
          </div></td>
          <td  valign="top" bgcolor="#EFFFDF"><div align="center">
            <?php if($lawful_row["LawfulStatus"] == 0){echo 'X';}?>
          </div></td>
          <td  valign="top" bgcolor="#EFFFDF"><div align="center">
            <?php if($lawful_row["LawfulStatus"] == 3){echo 'X';}?>
          </div></td>
          
          <?php }// <?php if($_POST["see_lawfulness"]){?>
          
          
          </tr>
        <?php }?>
      </tbody>
      <tfoot>
      </tfoot>
    </table></td>
    <td>&nbsp;</td>
  </tr>
</table>
