<?php

include "db_connect.php";

include("Charts/Includes/FusionCharts.php");

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
	
	$chk_details = $_POST["chk_details"];
	
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
	$province_detailed_34_filter = " and a.Province = '".$_POST["Province"]."'"; //yoes 20160125
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
	$province_filter = " and company.Province = provinces.province_id and provinces.section_id = '".$_POST["Section"]."'";
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}

//if($_POST["CompanyTypeCode"] == "14"){
	
//	$typecode_filter = " and CompanyTypeCode = '14'";
//	$business_type = "หน่วยงานภาครัฐ";
		
//}else{
	$typecode_filter = " and CompanyTypeCode != '14'";
	$typecode_filter2 = " and CompanyTypeCode = '14'";
	$business_type = "สถานประกอบการ";
//}


$typecode_filter .= " and CompanyTypeCode < 200";



//yoes 20160614 -- more conditions
//yoes 20160614 -- start to use common includes here
include "report_school_filter.inc.php";



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

//yoes 20160119 -- variables
$the_limit = getThisYearRatio($the_year);

$half_limit = $the_limit/2;

//catch-all wage
$the_wage = getThisYearWage($the_year);
//echo "the wage: ".$the_wage . " : ";
$the_cost_per_person = $the_wage*365;

$year_date = 365;

?>


<div align="center">
  <strong>สถิติการปฏิบัติตามกฎหมายของสถานประกอบการเอกชน <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <?php if($is_html && 1==0){?>
    <table align="center">
    	<tr>
        	<td>
            <div id="chart1div">
               ...
            </div>
            </td>
            <td>
            <div id="chart2div">
               ...
            </div>
            </td>
        </tr>
    </table>
    <?php }?>
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td  rowspan="2" align="center"  >          <div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td  rowspan="2" align="center"  >          <div align="center" style="vertical-align:middle;"><strong>รายการ</strong> </div></td>
        <td align="center" valign="top"><div align="center"><strong>จำนวน<br>
          สถานประกอบการ	
 </strong></div></td>
        <td align="center" valign="top">&nbsp;</td>
        <td align="center" valign="top">&nbsp;</td>
        </tr>
      <tr >
        <td align="center" valign="top"><div align="center"><strong>แห่ง </strong></div></td>
        <td align="center" valign="top" bgcolor="#E7FFCE"><div align="center"><strong>แห่ง </strong></div></td>
        <td align="center" valign="top" bgcolor="#E8F3FF"><div align="center"><strong>แห่ง </strong></div></td>
        </tr>
      <tr >
        <td  valign="top"><div align="center"><strong>1</strong></div></td>
        <td  valign="top"><div align="left"><strong>ปฏิบัติตามกฎหมายครบตามอัตราส่วน</strong></div></td>
        <td  valign="top">
        <div align="center">
        	ข้อมูลวันนี้
            <br />(<?php echo formatDateThai(date("Y-m-d"))?>)
        </div>
        </td>
        <td  valign="top" bgcolor="#E7FFCE"><div align="center"> ข้อมูล ณ วันที่<br />
        (<?php echo formatDateThai(date('Y-m-d',strtotime(date("Y-m-d") . "-1 days")))?>) </div></td>
        <td  valign="top" bgcolor="#E8F3FF"><div align="center"> ข้อมูล ณ วันที่ <br />
        (<?php echo formatDateThai(date('Y-m-d',strtotime(date("Y-m-d") . "-2 days")))?>) </div></td>
        </tr>
      </thead>
      
      <tbody>
      
      <?php
	  
	  	if($_POST["chk_non_ratio"]){
	  
			$row_name_array = array(
				"รับคนพิการเข้าทำงานครบตามอัตราส่วน (ม.33)"
				,"ส่งเงินเข้ากองทุนแทนการรับคนพิการเข้าทำงาน (ม.34)"
				,"ให้สัมปทานฯ (ม.35)"
				,"รับคนพิการเข้าทำงานและส่งเงินเข้ากองทุนฯ (ม.33,34)"
				,"รับคนพิการเข้าทำงานและให้สัมปทานฯ (ม.33,35)"
				
				,"ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.34,35)"
				,"รับคนพิการเข้าทำงาน ส่งเงินเข้ากองทุนฯ และให้สัมปทานฯ (ม.33,34,35)"
				,"ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน"
				
				,"ไม่เข้าข่ายจำนวนลูกจ้าง"
				
				,"ไม่ปฏิบัติตามกฎหมาย"
				
				//,"มีการทำตามกฏหมาย แต่ไม่มีการ check flag ม.33,ม.34 หรือ ม.35"
				
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
				
				," and LawfulStatus = '3' $branch_codition"
				
				," and (LawfulStatus = '0' or LawfulStatus is null) $branch_codition"
				
				//," and LawfulStatus != '3' and Hire_status = '0' and pay_status = '0' and Conc_status = '0' $branch_codition"
				
	
			);
			
			
			$all_company_sql = "select 
								count(company.CID)
								
							from 
							
								company
								JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								 $province_table
							where 
								1=1
								$typecode_filter
								$province_filter
								
								$branch_codition
								
								$last_modified_sql
								
								$school_filter
								
								
										";		
										
			
			$all_company_sql_snapshot = "select 
								count(company_snapshot.CID)
								
							from 
							
								company_snapshot
								JOIN lawfulness_snapshot ON (company_snapshot.CID = lawfulness_snapshot.CID and Year = '$the_year' )
							
								
								 $province_table
							where 
								1=1
								$typecode_filter
								$province_filter
								
								$branch_codition
								
								$last_modified_sql
								
								$school_filter
								
								and
							
								date(company_snapshot.snapshot_date) = SUBDATE(CURDATE(),1)
								and
								date(lawfulness_snapshot.snapshot_date) = SUBDATE(CURDATE(),1)
								
								
										";		
			
			
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
								
								$school_filter
								
								
								
								
										";		
										
										
										
			$all_company_sql_snapshot = "select 
								count(company_snapshot.CID)
								
							from 
							
								company_snapshot
								JOIN lawfulness_snapshot ON (company_snapshot.CID = lawfulness_snapshot.CID and Year = '$the_year' )
							
								
								 $province_table
							where 
								(lawfulstatus != 3 or lawfulstatus is null)
								$typecode_filter
								$province_filter
								
								$branch_codition
								
								$last_modified_sql
								
								$school_filter
								
								and
							
								date(company_snapshot.snapshot_date) = SUBDATE(CURDATE(),1)
								and
								date(lawfulness_snapshot.snapshot_date) = SUBDATE(CURDATE(),1)
								
										";												
										
									//	echo $all_company_sql;
			
			
		}
	  	
	  ?>
      
      <?php 
	  
										
			

		
		$all_company = getFirstItem($all_company_sql);
		
		//echo "<br><br>$all_company_sql";	
		//echo "<br>count all company2 = ".$all_company2;
		
		$all_company_snapshot_sql_array = array();
		
		$all_company_snapshot_sql_array[0] = $all_company_sql_snapshot;
		
		for($mmmm = 2;$mmmm <=4; $mmmm++){
			
			$all_company_snapshot_sql_array[$mmmm-1] = str_replace("SUBDATE(CURDATE(),1)", "SUBDATE(CURDATE(),$mmmm)",$all_company_sql_snapshot);
			
		}
		
		
		$all_company_snap[0] = getFirstItem($all_company_snapshot_sql_array[0]);
		$all_company_snap[1] = getFirstItem($all_company_snapshot_sql_array[1]);
		
		
		
	  
	  $the_count = 0;
	  $choice_prefix = "1.";
	  
	  for($i=0; $i<count($row_name_array);$i++){
	  
	  	$the_count++;
		
		?>
        
			 <?php if($i == 7){ //end if row 7 -> add total...
			 
			 	//reset the count
				$the_count = 2;
				$choice_prefix = ""; //remove prefix
			 
			 	?>
                  <tr>
                    <td  valign="top" colspan="2"><div align="right"><b>รวมลำดับที่ 1.1-1.7</b></div></td>
                    <td align="right"  <?php echo $footer_row?>><div align="right"><strong>
					
					
						<?php 
								$total_lawful_company = formatEmployeeReport($total_company); 
								
								
								echo $total_lawful_company;?>
                        
                        
                        </strong> </div></td>
                    <td align="right" bgcolor="#E7FFCE"  <?php echo $footer_row?>><div align="right"><strong>
                     
                     
                     
                     <?php echo formatEmployeeReport($total_company_snap[0]);?>
                     
                    </strong></div></td>
                    <td align="right" bgcolor="#E8F3FF"  <?php echo $footer_row?>><div align="right"><strong>
                      
					  <?php echo formatEmployeeReport($total_company_snap[1]);?>
                      
                    </strong></div></td>
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
							
							$school_filter
							
									";

		//echo "<br><br>".$company_sql; //exit();
		
		$company_snapshot_sql = "select 
							count(company_snapshot.CID)
						from 
							lawfulness_snapshot
							, company_snapshot
							$province_table
						where 
							company_snapshot.CID = lawfulness_snapshot.CID
							".$lawful_filter_array[$i]."
							$province_filter
							
							$typecode_filter
							
							and Year = '$the_year'
							
							$branch_codition
							
							$last_modified_sql
							
							$school_filter
							
							and
							
							date(company_snapshot.snapshot_date) = SUBDATE(CURDATE(),1)
							and
							date(lawfulness_snapshot.snapshot_date) = SUBDATE(CURDATE(),1)
							
									";
		
				
		
		//print_r($company_snapshot_sql_array);
		
		//echo "<br>".$company_snapshot_sql_array[0];
		
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
							
							$school_filter
							
							";
							
							
			$company_snapshot_sql = "select 
						
							count(company_snapshot.CID)
						from 
							company_snapshot
							JOIN lawfulness_snapshot ON (company_snapshot.CID = lawfulness_snapshot.CID and Year = '$the_year' )
							 $province_table
						where 
							(lawfulstatus = 0 or lawfulstatus is null)
							
							
							
							$typecode_filter
							
							
							$branch_codition
							$province_filter
							
							$last_modified_sql
							
							$school_filter
							
							and
							
							date(company_snapshot.snapshot_date) = SUBDATE(CURDATE(),1)
							and
							date(lawfulness_snapshot.snapshot_date) = SUBDATE(CURDATE(),1)
							
									";
							
													
		}
		
		
		$company_snapshot_sql_array = array();
		
		$company_snapshot_sql_array[0] = $company_snapshot_sql;
		
		for($mmmm = 2;$mmmm <=4; $mmmm++){
			
			$company_snapshot_sql_array[$mmmm-1] = str_replace("SUBDATE(CURDATE(),1)", "SUBDATE(CURDATE(),$mmmm)",$company_snapshot_sql);
			
		}
											
											
		//echo "<br><br>$i :: ".$company_sql;
		
		
	  	 $this_company = default_value(getFirstItem("$company_sql"),0);
		 
		 
		 $this_company_snap = array();
		 
		 $this_company_snap[0] = default_value(getFirstItem($company_snapshot_sql_array[0]),0);
		 $this_company_snap[1] = default_value(getFirstItem($company_snapshot_sql_array[1]),0);
		 
		// echo "<br>this company: $this_company";
		
		//echo "<br><br>$i :: ".$company2_sql;
		 
		 
		 $total_company += $this_company;
									
									
  		//$total_company_snap = array();
		//wont declare array here -> php will know that this is array
	  	$total_company_snap[0] += $this_company_snap[0];
		$total_company_snap[1] += $this_company_snap[1];
		
		
		
		
		
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
            
        <td align="right"  valign="top"><div align="right">
		
			<?php echo formatEmployeeReport($this_company);?> 
        
        
        </div></td>
        
        <td align="right"  valign="top" bgcolor="#E7FFCE"><div align="right"> 
		
			<?php echo formatEmployeeReport($this_company_snap[0]);?> 
          
        </div></td>
        <td align="right"  valign="top" bgcolor="#E8F3FF">
        
        
        	<?php echo formatEmployeeReport($this_company_snap[1]);?>
         
        </div></td>
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
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($all_company);?></strong> </div></td>
        <td align="right" bgcolor="#E7FFCE"  <?php echo $footer_row?>><div align="right"><strong>
		
		
		<?php echo formatEmployeeReport($all_company_snap[0]);?>
        
        
        </strong></div></td>
        <td align="right" bgcolor="#E8F3FF"  <?php echo $footer_row?>><div align="right"><strong>
		
		
		<?php echo formatEmployeeReport($all_company_snap[1]);?>
        
        
        </strong></div></td>
        </tr>
      </tfoot>
</table>



<?php 

	
	if($_POST[do_compare_01] && $_POST[do_compare_02]){
	?>
    
    
<div align="center" style="margin-top:20px;">
    	<hr />
  <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td rowspan="2" style="text-align: center">
              สถานประกอบการ
            </td>
            <td colspan="5" style="text-align: center">ข้อมูล ณ วันที่ <br />
            (
			
			<?php 
			
				//echo $_POST[do_compare_01];
				//echo " - ".$_POST[do_compare_02];
			
				if($_POST[do_compare_01] == 1){
					$the_offset_01 = 0;
					
					
					$left_table = "lawfulness";
					
				}elseif($_POST[do_compare_01] == 2){
					$the_offset_01 = 1;
					
					$left_table = "lawfulness_snapshot";
					
					$left_snapshot_date = " and date(a.snapshot_date) = '".date('Y-m-d',strtotime(date("Y-m-d") . "-1 days"))."' ";
				}
				
				if($_POST[do_compare_02] == 2){
					$the_offset_02 = 1;
					
					$right_table = "lawfulness_snapshot";
					
					$right_snapshot_date = " and date(b.snapshot_date) = '".date('Y-m-d',strtotime(date("Y-m-d") . "-1 days"))."' ";
					
					
				}elseif($_POST[do_compare_02] == 3){
					$the_offset_02 = 2;
					
					$right_table = "lawfulness_snapshot";
					
					$right_snapshot_date = " and date(b.snapshot_date) = '".date('Y-m-d',strtotime(date("Y-m-d") . "-2 days"))."' ";
				}
				
				//echo formatDateThai(date("Y-m-d"));
				echo formatDateThai(date('Y-m-d',strtotime(date("Y-m-d") . "-".$the_offset_01." days")));
			
			?>) </td>
            <td colspan="5" bgcolor="#E7FFCE" style="text-align: center">ข้อมูล ณ วันที่<br />
            (<?php 
			
				echo formatDateThai(date('Y-m-d',strtotime(date("Y-m-d") . "-".$the_offset_02." days")));
			
			
				
			?>) </td>
        </tr>
        <tr>
          <td style="text-align: center">จำนวนลูกจ้าง</td>
          <td style="text-align: center">การปฎิบัติ<br />
            ตามกฎหมาย</td>
          <td style="text-align: center">ม.33</td>
          <td style="text-align: center">ม.34</td>
          <td style="text-align: center">ม.35</td>
          <td bgcolor="#E7FFCE" style="text-align: center">จำนวนลูกจ้าง</td>
          <td bgcolor="#E7FFCE" style="text-align: center">การปฎิบัติ<br />
            ตามกฎหมาย</td>
          <td bgcolor="#E7FFCE" style="text-align: center">ม.33</td>
          <td bgcolor="#E7FFCE" style="text-align: center">ม.34</td>
          <td bgcolor="#E7FFCE" style="text-align: center">ม.35</td>
        </tr>
            
        <?php 
			
				$sql = "
				
					select
						
						a.lid
						, a.cid
						, a.LawfulStatus as lawful_status_now
						, b.LawfulStatus as lawful_status_then
						
						, a.hire_status as hire_status_now
						, a.pay_status as pay_status_now
						, a.conc_status as conc_status_now
						
						, a.employees as employees_now
						
						, b.hire_status as hire_status_then
						, b.pay_status as pay_status_then
						, b.conc_status as conc_status_then
						
						, b.employees as employees_then
						
					from
						$left_table a
							left join 
								$right_table b
								
								on a.lid = b.lid
								
								$left_snapshot_date
								
								$right_snapshot_date
								
								
					where
						a.year = '$the_year'
						
						and
						a.LawfulStatus != b.LawfulStatus
					
						
					union
					
					
					select
						
						a.lid
						, a.cid
						, a.LawfulStatus as lawful_status_now
						, b.LawfulStatus as lawful_status_then
						
						, a.hire_status as hire_status_now
						, a.pay_status as pay_status_now
						, a.conc_status as conc_status_now
						
						
						, a.employees as employees_now
						
						, b.hire_status as hire_status_then
						, b.pay_status as pay_status_then
						, b.conc_status as conc_status_then
						
						, b.employees as employees_then
						
					from
						$left_table a
							right join 
								$right_table b
								
								on a.lid = b.lid
								
								$left_snapshot_date
								
								$right_snapshot_date
								
					where
						b.year = '$the_year'
						
						and
						a.LawfulStatus != b.LawfulStatus
				
				";
				
				//echo $sql;
			
			
			?>
            
            
            
            <?php 
			
				
				$diff_result = mysql_query($sql);
			
			
				while($diff_row = mysql_fetch_array($diff_result)){?>
                
                
                <tr>
                  <td style="text-align: left"><?php 
				  	
					$company_row = getFirstRow("select * from company where cid = '".$diff_row[cid]."'");
				  
				  	echo formatCompanyName($company_row["CompanyNameThai"], $company_row["CompanyTypeCode"]);
				  
				  ?></td>
                  <td style="text-align: center"><?php 
					  	
						echo $diff_row[employees_now];
					  
					  ?></td>
                  <td style="text-align: center">
                  
	                  <?php 
					  	
						echo getLawfulText($diff_row[lawful_status_now]);
					  
					  ?>
                  
                  </td>
                  <td style="text-align: center"><?php 
					  	
						echo intToDoNotDo($diff_row[hire_status_now]);
					  
					  ?></td>
                  <td style="text-align: center"><?php 
					  	
						echo intToDoNotDo($diff_row[pay_status_now]);
					  
					  ?></td>
                  <td style="text-align: center"><?php 
					  	
						echo intToDoNotDo($diff_row[conc_status_now]);
					  
					  ?></td>
                  <td bgcolor="#E7FFCE" style="text-align: center"><?php 
					  	
						echo $diff_row[employees_then];
					  
					  ?></td>
                  <td bgcolor="#E7FFCE" style="text-align: center"><?php 
					  	
						echo getLawfulText($diff_row[lawful_status_then]);
					  
					  ?></td>
                  <td bgcolor="#E7FFCE" style="text-align: center"><?php 
					  	
						echo intToDoNotDo($diff_row[hire_status_then]);
					  
					  ?></td>
                  <td bgcolor="#E7FFCE" style="text-align: center"><?php 
					  	
						echo intToDoNotDo($diff_row[pay_status_then]);
					  
					  ?></td>
                  <td bgcolor="#E7FFCE" style="text-align: center"><?php 
					  	
						echo intToDoNotDo($diff_row[conc_status_then]);
					  
					  ?></td>
                </tr>
                
                
             <?php
					
				}
				
				
				
			?>
            
            
  </table>
   
   </div>
    
<?php    	
		
	}

?>  
    




    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
