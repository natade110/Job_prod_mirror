<?php

include "db_connect.php";

//include("Charts/Includes/FusionCharts.php");

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

	$is_html = "1";
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

//if($_POST["CompanyTypeCode"] == "14"){
	
//	$typecode_filter = " and CompanyTypeCode = '14'";
//	$business_type = "หน่วยงานภาครัฐ";
		
//}else{
	//$typecode_filter = " and CompanyTypeCode != '14'";
	//$typecode_filter2 = " and CompanyTypeCode = '14'";
	//$business_type = "สถานประกอบการ";
//}


//$typecode_filter .= " and CompanyTypeCode < 200";

///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter .= " and CompanyTypeCode >= 200  and CompanyTypeCode < 300";
	
}else{
	
	$typecode_filter .= " and CompanyTypeCode < 200";
	
}

if($_POST["CompanyTypeCode"]){
	
	$typecode_filter .= " and CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'";
		
	$the_company_word = getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'");
		
}



////
//yoes 20130820 - add last modify date/time for lawfulness
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

?>

<div align="center">
  <strong>สถิติการปฏิบัติตามกฎหมายของ<?php echo $the_company_word;?> <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</strong>
              <br>
</div>
    
    
  
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td  rowspan="4"  >          <div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td  rowspan="4"  >          <div align="center" style="vertical-align:middle;"><strong>รายการ</strong> </div></td>
        <td colspan="5" ><div align="center"><strong>
          
          <?php 
		
		
		if($sess_accesslevel == "6" || $sess_accesslevel == "7"){
			
			echo "หน่วยงานภาครัฐ";
		
			
			
		}else{
			
			echo "นายจ้างหรือสถานประกอบการ";
			
		}?>
          
          
          
          
        </strong></div></td>
        </tr>
      <tr >
        <td  colspan="2" rowspan="2" valign="top"><div align="center"><strong>จำนวน<br>
          <?php echo $the_company_word;?>	
 </strong></div></td>
        <td colspan="3" valign="top" ><div align="center"><strong>การปฏิบัติตามกฎหมาย	
        </strong></div></td>
        </tr>
      <tr >
        <td valign="top"><div align="center"><strong>มาตรา 33</strong></div></td>
        <td colspan="2" valign="top"><div align="center"><strong>มาตรา 35</strong></div></td>
        </tr>
      <tr >
        <td valign="top"><div align="center"><strong>แห่ง </strong></div></td>
        <td  valign="top"><div align="center"><strong>ร้อยละ </strong></div></td>
        <td  valign="top"><div align="center"><strong>คนพิการ<br />
        (ราย) </strong></div></td>
        <td  valign="top"><div align="center"><strong>คนพิการ<br>
        (ราย) </strong></div></td>
        <td  valign="top"><div align="center"><strong>ผู้ดูแล<br>
          คนพิการ<br>
        (ราย) </strong></div></td>
        </tr>
      <tr >
        <td  valign="top"><div align="center"><strong>1</strong></div></td>
        <td colspan="6"  valign="top"><div align="left"><strong>ปฏิบัติตามกฎหมายครบตามอัตราส่วน</strong></div></td>
        </tr>
      </thead>
      
      <tbody>
      
      <?php
	  
	  
	  	if($sess_accesslevel == 6 || $sess_accesslevel == 7){
			
			$row_name_array = array(
				"รับคนพิการเข้าทำงานครบตามอัตราส่วน (ม.33)"
				//,"ส่งเงินเข้ากองทุนแทนการรับคนพิการเข้าทำงาน (ม.34)"
				,"ให้สัมปทานฯ (ม.35)"
				//,"รับคนพิการเข้าทำงานและส่งเงินเข้ากองทุนฯ (ม.33,34)"
				,"รับคนพิการเข้าทำงานและให้สัมปทานฯ (ม.33,35)"
				
				//,"ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.34,35)"
				//,"รับคนพิการเข้าทำงาน ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.33,34,35)"
				,"ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน"
				
				//,"ไม่เข้าข่ายจำนวนลูกจ้าง"
				
				,"ไม่ปฏิบัติตามกฎหมาย"
				
				
			);
	
			$lawful_filter_array = array(
				" and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '0' $branch_codition"
				//," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '0' $branch_codition"
				," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '0' and Conc_status = '1' $branch_codition"
				//," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '0' $branch_codition"				
				," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '1' $branch_codition"
							
				//," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '1' $branch_codition"
				//," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '1' $branch_codition"
				," and LawfulStatus = '2' $branch_codition"
				
				//," and LawfulStatus = '3' $branch_codition"
				
				," and (LawfulStatus = '0' or LawfulStatus is null) $branch_codition"
				
	
	
			);
			
			
		}else{
			
			$row_name_array = array(
				"รับคนพิการเข้าทำงานครบตามอัตราส่วน (ม.33)"
				,"ส่งเงินเข้ากองทุนแทนการรับคนพิการเข้าทำงาน (ม.34)"
				,"ให้สัมปทานฯ (ม.35)"
				,"รับคนพิการเข้าทำงานและส่งเงินเข้ากองทุนฯ (ม.33,34)"
				,"รับคนพิการเข้าทำงานและให้สัมปทานฯ (ม.33,35)"
				
				,"ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.34,35)"
				,"รับคนพิการเข้าทำงาน ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.33,34,35)"
				,"ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน"
				
				//,"ไม่เข้าข่ายจำนวนลูกจ้าง"
				
				,"ไม่ปฏิบัติตามกฎหมาย"
				
				
			);
	
			$lawful_filter_array = array(
				" and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '0' $branch_codition"
				," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '0' $branch_codition"
				," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '0' and Conc_status = '1' $branch_codition"
				," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '0' $branch_codition"
				
				," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '1' $branch_codition"			
				," and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '1' $branch_codition"
				," and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '1' $branch_codition"
				," and LawfulStatus = '2' $branch_codition"
				
				//," and LawfulStatus = '3' $branch_codition"
				
				," and (LawfulStatus = '0' or LawfulStatus is null) $branch_codition"
				
	
	
			);
			
		}
		
	  	
	  ?>
      
      <?php 
	  
		$all_company_sql = "select 
								count(company.CID)
								
							from 
								lawfulness
								, company
								$province_table
							where 
								company.CID = lawfulness.CID
								
								$typecode_filter
								
								and LawfulStatus != '3'
								$province_filter
								
								$branch_codition
								
								and Year = '$the_year'
								
								$last_modified_sql
										";
										
		$all_company_sql = "select 
								count(company.CID)
								
							from 
							
								company
								JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								 $province_table
							where 
								(lawfulstatus != 3 or lawfulstatus is null)
								$typecode_filter
								$province_filter
								
								$branch_codition
								
								$last_modified_sql
								
										";			
		
										
										//echo $all_company_sql;//exit();
		$all_company = getFirstItem($all_company_sql);	
		
		//echo "<br>".$all_company;
		
		$all_company2_sql = "select 
								count(company.CID)
								
							from 
							
								company
								JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								 $province_table
							where 
								(lawfulstatus != 3 or lawfulstatus is null)
								
								$branch_codition
								
								$typecode_filter2
								$province_filter
								
								$last_modified_sql
								
										"; //echo $all_company_sql; exit();
		$all_company2 = getFirstItem($all_company2_sql);								
		
	  
	  $the_count = 0;
	  $choice_prefix = "1.";
	  
	  for($i=0; $i<count($row_name_array);$i++){
	  
	  	$the_count++;
		
		?>
        
			 <?php if($i == 3){ //end if row 7 -> add total...
			 
			 	//reset the count
				$the_count = 2;
				$choice_prefix = ""; //remove prefix
			 
			 	?>
                  <tr>
                    <td  valign="top" colspan="2"><div align="right"><b>รวมลำดับที่ 1.1-1.3</b></div></td>
                    <td  <?php echo $footer_row?>><div align="right"><strong><?php $total_lawful_company = formatEmployeeReport($total_company); echo $total_lawful_company;?></strong> </div></td>
                    <td  <?php echo $footer_row?>><div align="right"><strong><?php echo floor(($total_percent*100))/100;?></strong> </div></td>
                    <td  <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($sum_emp);?></strong> </div></td>
                    <td  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_usee);?></strong></div></td>
                    <td  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_user);?></strong></div></td>
                  </tr>
              <?php }?>
        
        
        <?php
	  
		
		$company_sql = "select 
							count(company.CID)
						from 
							lawfulness
							, company
							$province_table
						where 
							company.CID = lawfulness.CID
							".$lawful_filter_array[$i]."
							$province_filter
							
							$typecode_filter
							
							and Year = '$the_year'
							
							$branch_codition
							
							$last_modified_sql
							
									";

									//echo "<br><br>".$company_sql; //exit();
									
									
		//$all_company += $this_company;
		 
		 $company2_sql = "select 
							count(company.CID)
						from 
							lawfulness
							, company
							$province_table
						where 
							company.CID = lawfulness.CID
							".$lawful_filter_array[$i]."
							$province_filter
							
							$typecode_filter
							
							and Year = '$the_year'
							
							$branch_codition
							
							$last_modified_sql
							
									"; //echo $company_sql; exit();


		//for "unlawful", do it a hardway
		if($i==count($row_name_array)-1){
			$company_sql = "select 
						
							count(company.CID)
						from 
							company
							JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							 $province_table
						where 
							(lawfulstatus = 0 or lawfulstatus is null)
							
							$typecode_filter
							
							$branch_codition
							$province_filter
							
							$last_modified_sql
							
							";
							
							
							
			 $company2_sql = "select 
						
							count(company.CID)
						from 
							company
							JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							 $province_table
						where 
							(lawfulstatus = 0 or lawfulstatus is null)
							
							$typecode_filter
							
							$branch_codition
							$province_filter
							
							
							$last_modified_sql
							
							";										
		}
											
											
		//echo "<br><br>".$company_sql;
		
		
	  	 $this_company = default_value(getFirstItem("$company_sql"),0);
		 $total_company += $this_company;
									
	  	 $this_company2 = default_value(getFirstItem("$company2_sql"),0);
		 $total_company2 += $this_company2;
		 
		 $this_percent = 0;	
		 $this_percent2 = 0;	
		
		if($this_company > 0){
			$this_percent = ($this_company/$all_company * 100);
			$total_percent += $this_percent;
		}	
		if($this_company2 > 0){
			$this_percent2 = ($this_company2/$all_company2 * 100);
			$total_percent2 += $this_percent2;
		}	
		 
		$this_emp_sql = "select 
						
							sum(Hire_NumofEmp)
						
						from 
							lawfulness
							, company
							 $province_table
						where 
							company.CID = lawfulness.CID
							
							$typecode_filter
							
							$province_filter
							and Year = '$the_year'
							".$lawful_filter_array[$i]."
							
							$branch_codition
							$last_modified_sql
							
									";				
		
		///if($i == 7){
			//echo $this_emp_sql; exit();
		//}
		

		//for "unlawful", do it a hardway
		if($i==count($row_name_array)-1){
		
		
			$company_sql = "select 
						
							sum(Hire_NumofEmp)
						from 
							company
							JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							 $province_table
						where 
							(lawfulstatus = 0 or lawfulstatus is null)
							
							$typecode_filter
							
							$province_filter
							
							
							$branch_codition
							$last_modified_sql
							";
		}
		
		$this_emp = default_value(getFirstItem($this_emp_sql),0);
		$sum_emp += $this_emp;
		
		//----
		$this_emp2_sql = "select 
						
							sum(Hire_NumofEmp)
						
						from 
							lawfulness
							, company
							 $province_table
						where 
							company.CID = lawfulness.CID
							
							$typecode_filter
							
							$province_filter
							and Year = '$the_year'
							".$lawful_filter_array[$i]."
							
							$branch_codition
							$last_modified_sql
							
									";				
			
		$this_emp2 = default_value(getFirstItem($this_emp2_sql),0);
		$sum_emp2 += $this_emp2;
		
		//--------------
		
		$sub_table = "						
			(
			select 
				sum(receipt.Amount) the_sum
				, receipt.RID the_rid 
				, ReceiptYear
				, company.cid the_cid 
				
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
				AND is_payback =0
				and main_flag = 1
				$typecode_filter
				$province_filter
				$branch_codition
				
				$last_modified_sql
				
			group by
				company.cid
				, receipt.RID
			)
			";
		
		$rule_35_sql = "						
			
			select 
						sum(a.the_sum)
					from 
						lawfulness
						, $sub_table a
						, company
					where
						lawfulness.CID = a.the_cid
						and
						lawfulness.CID = company.cid
						and lawfulness.Year = a.ReceiptYear
						
						$last_modified_sql
						
				".$lawful_filter_array[$i]."
			
			";
		//echo "<br><br>".$rule_35_sql;
		
									
		$this_rule_35 = default_value(getFirstItem($rule_35_sql),0);
		$sum_rule_35 += $this_rule_35;
		
		
		
		
		//---------- curator stuffs
		$the_sql = "
												
					select count(*) 
					from 
					curator 
					where 
					
					curator_lid in
					(
					
						select 
							lid
						from 
							lawfulness
							, company
							$province_table
						where 
							company.CID = lawfulness.CID
							".$lawful_filter_array[$i]."
							$province_filter
							
							$typecode_filter
							
							and Year = '$the_year'
							
							$branch_codition
							
							$last_modified_sql
					
					)
					
					and 
					curator_parent = 0
					and
					curator_is_disable = 0
				
				";
				
		
		$curator_user = getFirstItem($the_sql);	
		$total_curator_user += $curator_user;	
		
		
		
		//---------- curator stuffs
		$the_sql = "
												
					select count(*) 
					from 
					curator 
					where 
					
					curator_lid in
					(
					
						select 
							lid
						from 
							lawfulness
							, company
							$province_table
						where 
							company.CID = lawfulness.CID
							".$lawful_filter_array[$i]."
							$province_filter
							
							$typecode_filter
							
							and Year = '$the_year'
							
							$branch_codition
							
							$last_modified_sql
					
					)
					
					and 
					curator_parent = 0
					and
					curator_is_disable = 1
				
				";
				
				
		//echo "<br><br>$the_sql";
		
		$curator_usee = getFirstItem($the_sql);	
		$total_curator_usee += $curator_usee;	
		
		
		
		
	  ?>
      
      <?php
	  
	  	//get number of company to render graph
		
		//lawfulness
		if($i==0){
	  		$company_1 = $this_company;
		}
		if($i==1){
	  		$company_2 = $this_company;
		}
		if($i==2){
	  		$company_3 = $this_company;
		}
		if($i==3){
	  		$company_4 = $this_company;
		}
		if($i==4){
	  		$company_5 = $this_company;
		}
		if($i==5){
	  		$company_6 = $this_company;
		}
		if($i==6){
	  		$company_7 = $this_company;
		}
		
		
		//non-lawfulness
	  	if($i==count($row_name_array)-2){
	  		$this_not_finish_company = $this_company;
		}
	  	if($i==count($row_name_array)-1){
	  		$this_unlawful_company = $this_company;
		}
	  
	  ?>
      
      
      <tr>
        <td  valign="top"><div align="center">
		
        	<?php if(!$choice_prefix){echo "<b>";}?>
				<?php echo $choice_prefix;?><?php echo $the_count;?>
            <?php if(!$choice_prefix){echo "</b>";}?>
            
          </div></td>
        <td  valign="top"><div align="left">
			<?php if(!$choice_prefix){echo "<b>";}?>
				<?php echo $row_name_array[$i];?>
            <?php if(!$choice_prefix){echo "</b>";}?>
            </div></td>
        <td  valign="top"><div align="right"><?php echo formatEmployeeReport($this_company);?> </div></td>
        <td  valign="top"><div align="right"><?php echo floor(($this_percent*100))/100;?> </div></td>
        <td  valign="top"><div align="right"><?php echo formatEmployeeReport($this_emp);?></div></td>
        <td valign="top"><div align="right"><?Php echo formatEmployeeReport($curator_usee);?></div></td>
        <td  valign="top"><div align="right"><?Php echo formatEmployeeReport($curator_user);?></div></td>
        </tr>
      
      
				 
      
      <?php
	  }
	  ?>
	  </tbody>
        
        <?php
			if($_POST["report_format"] == "pdf"){
				//
			}else{
				$footer_row = 'style="border-bottom:double;"';
			}
		?>
        
        <tfoot>
      <tr>
        <td colspan="2" ><div align="right"><strong>รวมลำดับที่ 1-3 ทั้งสิ้น</strong></div></td>
        <td  <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($all_company);?></strong> </div></td>
        <td  <?php echo $footer_row?>><div align="right"><strong>100.00</strong> </div></td>
        <td  <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($sum_emp);?></strong> </div></td>
        <td  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_usee);?></strong></div></td>
        <td  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_user);?></strong></div></td>
        </tr>
      </tfoot>
</table>
    
     
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
