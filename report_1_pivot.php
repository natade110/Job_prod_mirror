<?php

include "db_connect.php";
include "session_handler.php";
include("Charts/Includes/FusionCharts.php");

//ini_set('memory_limit', '2048M');

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

if(isset($_GET["year"])){

	$year = $_GET["year"]*1;
	$the_year = $year;

}else{

	$the_year = "2011";

}



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
	$province_detailed_34_filter = " and a.Province in (
	
		select
			province_id
		from
			provinces
		where
			section_id = '".$_POST["Section"]."'
	
	)"; //yoes 20200702 -- add this statement
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
	//echo $province_filter;
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

//bank 20221223
include "org_type_filter.php";

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
        <td  rowspan="4" align="center" ><div align="center" style="vertical-align:middle;"><strong>สสว. </strong></div></td>
        <td  rowspan="4" align="center" ><div align="center" style="vertical-align:middle;"><strong>จังหวัด</strong> </div></td>
        <!-- <td colspan="10" align="center" ><div align="center"><strong>ประจำปี 2563</strong></div></td> -->
        </tr>
      	<tr >
        <td  colspan="1" rowspan="2" align="center" valign="top"><div align="center"><strong>จำนวนสถานประกอบการ<br>ที่มีลูกจ้างตั้งแต่ 100 คน ขึ้นไป (แห่ง)</strong></div></td>
		<td  colspan="1" rowspan="2" align="center" valign="top"><div align="center"><strong>อัตราส่วนที่ต้องรับ<br>คนพิการเข้าทำงาน (ราย)</strong></div></td>
		<td  colspan="1" rowspan="2" align="center" valign="top"><div align="center"><strong>รับคนพิการเข้าทำงานตาม ม.33</strong></div></td>
		<td  colspan="1" rowspan="2" align="center" valign="top"><div align="center"><strong>ส่งเงินเข้ากองทุนฯ ตาม ม.34</strong></div></td>
		<td  colspan="1" rowspan="2" align="center" valign="top"><div align="center"><strong>ให้สัมปทานฯ ตาม ม.35</strong></div></td>
		<td  colspan="1" rowspan="2" align="center" valign="top"><div align="center"><strong>รับคนพิการ ตาม ม.33 และให้สัมปทานตาม ม.35</strong></div></td>
		<td  colspan="1" rowspan="2" align="center" valign="top"><div align="center"><strong>ส่งเงินตาม ม.34 และให้สัมปทานตาม ม.35</strong></div></td>
		<td  colspan="1" rowspan="2" align="center" valign="top"><div align="center"><strong>รับคนพิการตาม ม.33 ส่งเงิน ตาม ม.34 และให้สัมปทานฯ ตาม ม.35</strong></div></td>
		<td  colspan="1" rowspan="2" align="center" valign="top"><div align="center"><strong>ปฏิบัติตามกฎหมาย แต่ไม่ครบอัตราส่วน</strong></div></td>
		<td  colspan="1" rowspan="2" align="center" valign="top"><div align="center"><strong>ไม่ปฏิบัติตามกฎหมาย</strong></div></td>
      </thead>
      
      <tbody>
      
      <?php

		function getProvinceId($province_name, $province_name_array) {
			// Check if the province_name exists in the array
			if (in_array($province_name, $province_name_array)) {
				// Search for the province_id based on the province_name
				$province_id = array_search($province_name, $province_name_array);
				return $province_id;
			} else {
				return "0";
			}
		}

		$sql = "select province_id, province_name FROM provinces";
		$province_result = mysql_query($sql);
		$province_name_array = array();
		while ($row = mysql_fetch_assoc($province_result)) {
			// Add province_name and province_id to the array
			$province_name_array[$row['province_id']] = $row['province_name'];
		}
		// Free the result set
		mysql_free_result($province_result);

		// print_r($province_name_array); 
		// // echo $province_name_array[1];
		// exit();

	  
	  	if($_POST["chk_non_ratio"]){
	  
			$row_name_array = array(
				"จำนวนสถานประกอบการที่มีลูกจ้างตั้งแต่ 100 คน ขึ้นไป (แห่ง)"
				,"อัตราส่วนที่ต้องรับคนพิการเข้าทำงาน (ราย)"
				,"รับคนพิการเข้าทำงานตาม ม.33"
				,"ส่งเงินเข้ากองทุนฯ ตาม ม.34"
				,"ให้สัมปทานฯ ตาม ม.35"
				
				,"รับคนพิการ ตาม ม.33 และให้สัมปทานตาม ม.35"
				,"ส่งเงินตาม ม.34 และให้สัมปทานตาม ม.35"
				,"รับคนพิการตาม ม.33 ส่งเงิน ตาม ม.34 และให้สัมปทานฯ ตาม ม.35"
				
				,"ปฏิบัติตามกฎหมาย แต่ไม่ครบอัตราส่วน"
				
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
								
								$CompanyType_filter

								
										";		
			
		}else{
			
			$row_name_array = array(
				"จำนวนสถานประกอบการที่มีลูกจ้างตั้งแต่ 100 คน ขึ้นไป (แห่ง)"
				,"อัตราส่วนที่ต้องรับคนพิการเข้าทำงาน (ราย)"
				,"รับคนพิการเข้าทำงานตาม ม.33"
				,"ส่งเงินเข้ากองทุนฯ ตาม ม.34"
				,"ให้สัมปทานฯ ตาม ม.35"
				
				,"รับคนพิการ ตาม ม.33 และให้สัมปทานตาม ม.35"
				,"ส่งเงินตาม ม.34 และให้สัมปทานตาม ม.35"
				,"รับคนพิการตาม ม.33 ส่งเงิน ตาม ม.34 และให้สัมปทานฯ ตาม ม.35"
				
				,"ปฏิบัติตามกฎหมาย แต่ไม่ครบอัตราส่วน"
				
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
				
				//," and LawfulStatus = '3' $branch_codition"
				
				," and (LawfulStatus = '0' or LawfulStatus is null) $branch_codition"
					
	
			);
			
			
			$all_company_sql = "select 
								count(company.CID)
								
							from 
							
								company
								JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								 left join provinces on company.Province = provinces.province_id
							where 
								(lawfulstatus != 3 or lawfulstatus is null)
							and company.Province = '".$desired_province_id."'
								$typecode_filter
								
								
								$branch_codition
								
								$last_modified_sql
								
								$school_filter
								
								$CompanyType_filter

								
										";		
										
									//echo $all_company_sql;
			
			
		}
	  	
	  ?>
      
      <?php 
	  
										
			

		
		$all_company = getFirstItem($all_company_sql);
		
		//echo "<br><br>$all_company_sql";	
		
		//echo "<br>count all company = ".$all_company;
		
		$all_company2_sql = "select 
								count(company.CID)
								
							from 
							
								company
								JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								left join provinces on company.Province = provinces.province_id

								where 
								(lawfulstatus != 3 or lawfulstatus is null)
								
								and company.Province = '".$desired_province_id."'
								
								$typecode_filter2
								$province_filter
								
								$last_modified_sql
								
								$school_filter
								$CompanyType_filter
										"; //echo $all_company_sql; exit();
		$all_company2 = getFirstItem($all_company2_sql);								
		
		
		//echo "<br>count all company2 = ".$all_company2;
	  
	  $the_count = 0;
	  $choice_prefix = "1.";
	  
	  
		//yoes 20200916 -- add loop for extra report cound
		if(!$row_data){
			$row_data = array();
		}
		
	  
	  for($i=1; $i<count($province_name_array);$i++){
	  
	  	$the_count++;
		
		?>
        

        
        
        <?php

		$desired_province_name = $province_name_array[$i];
		$desired_province_id = getProvinceId($desired_province_name, $province_name_array); 

		$company_sql = "select 
								count(company.CID)
								
							from 
							
								company
								JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
							
								
								 left join provinces on company.Province = provinces.province_id
							where 
								(lawfulstatus != 3 or lawfulstatus is null)
							and company.Province = '".$desired_province_id."'
								$typecode_filter
								
								
								$branch_codition
								
								$last_modified_sql
								
								$school_filter
								
								$CompanyType_filter

								
										";		
	  
				
		//echo "<br><br>$i :: ".$company_sql;
		
		
	  	 $this_company = default_value(getFirstItem("$company_sql"),0);
		 
		// echo "<br>this company: $this_company";
		
		//echo "<br><br>$i :: ".$company2_sql;
		 
	
		 $total_company += $this_company;
									
	  	
	

			//yoes 20160328 --> add อัตราส่วนที่ต้องรับคนพิการเข้าทํางาน(ราย)
			$sql_all_ratio_1 = "
			select sum(if(lawfulness.employees % $the_limit <= $half_limit, floor(lawfulness.employees/$the_limit), ceil(lawfulness.employees/$the_limit)))                    as company_ratio
			from lawfulness, company
			where lawfulstatus != 3 
			and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '0' $branch_codition
			and company.CID = lawfulness.CID and company.Province = '$desired_province_id' $typecode_filter
			and Year = '$the_year' $last_modified_sql $school_filter $CompanyType_filter
			";

			$sql_all_ratio_2 = "
			select sum(if(lawfulness.employees % $the_limit <= $half_limit, floor(lawfulness.employees/$the_limit), ceil(lawfulness.employees/$the_limit)))                    as company_ratio
			from lawfulness, company
			where lawfulstatus != 3 
			and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '0' $branch_codition
			and company.CID = lawfulness.CID and company.Province = '$desired_province_id' $typecode_filter
			and Year = '$the_year' $last_modified_sql $school_filter $CompanyType_filter
			";	
			
			$sql_all_ratio_3 = "
			select sum(if(lawfulness.employees % $the_limit <= $half_limit, floor(lawfulness.employees/$the_limit), ceil(lawfulness.employees/$the_limit)))                    as company_ratio
			from lawfulness, company
			where lawfulstatus != 3 
			and LawfulStatus = '1' and Hire_status = '0' and pay_status = '0' and Conc_status = '1' $branch_codition
			and company.CID = lawfulness.CID and company.Province = '$desired_province_id' $typecode_filter
			and Year = '$the_year' $last_modified_sql $school_filter $CompanyType_filter
			";	

			$sql_all_ratio_4 = "
			select sum(if(lawfulness.employees % $the_limit <= $half_limit, floor(lawfulness.employees/$the_limit), ceil(lawfulness.employees/$the_limit)))                    as company_ratio
			from lawfulness, company
			where lawfulstatus != 3 
			and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '0' $branch_codition
			and company.CID = lawfulness.CID and company.Province = '$desired_province_id' $typecode_filter
			and Year = '$the_year' $last_modified_sql $school_filter $CompanyType_filter
			";	

			$sql_all_ratio_5 = "
			select sum(if(lawfulness.employees % $the_limit <= $half_limit, floor(lawfulness.employees/$the_limit), ceil(lawfulness.employees/$the_limit)))                    as company_ratio
			from lawfulness, company
			where lawfulstatus != 3 
			and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '1' $branch_codition
			and company.CID = lawfulness.CID and company.Province = '$desired_province_id' $typecode_filter
			and Year = '$the_year' $last_modified_sql $school_filter $CompanyType_filter
			";	

			$sql_all_ratio_6 = "
			select sum(if(lawfulness.employees % $the_limit <= $half_limit, floor(lawfulness.employees/$the_limit), ceil(lawfulness.employees/$the_limit)))                    as company_ratio
			from lawfulness, company
			where lawfulstatus != 3 
			and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '1' $branch_codition
			and company.CID = lawfulness.CID and company.Province = '$desired_province_id' $typecode_filter
			and Year = '$the_year' $last_modified_sql $school_filter $CompanyType_filter
			";		
			
			$sql_all_ratio_7 = "
			select sum(if(lawfulness.employees % $the_limit <= $half_limit, floor(lawfulness.employees/$the_limit), ceil(lawfulness.employees/$the_limit)))                    as company_ratio
			from lawfulness, company
			where lawfulstatus != 3 
			and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '1' $branch_codition
			and company.CID = lawfulness.CID and company.Province = '$desired_province_id' $typecode_filter
			and Year = '$the_year' $last_modified_sql $school_filter $CompanyType_filter
			";
			
			$sql_all_ratio_8 = "
			select sum(if(lawfulness.employees % $the_limit <= $half_limit, floor(lawfulness.employees/$the_limit), ceil(lawfulness.employees/$the_limit)))                    as company_ratio
			from lawfulness, company
			where lawfulstatus != 3 
			and LawfulStatus = '2' $branch_codition
			and company.CID = lawfulness.CID and company.Province = '$desired_province_id' $typecode_filter
			and Year = '$the_year' $last_modified_sql $school_filter $CompanyType_filter
			";	

			$sql_all_ratio_9 = "
			select sum(if(lawfulness.employees % $the_limit <= $half_limit, floor(lawfulness.employees/$the_limit), ceil(lawfulness.employees/$the_limit)))                    as company_ratio
			from lawfulness, company
			where lawfulstatus != 3 
			and (LawfulStatus = '0' or LawfulStatus is null) $branch_codition
			and company.CID = lawfulness.CID and company.Province = '$desired_province_id' $typecode_filter
			and Year = '$the_year' $last_modified_sql $school_filter $CompanyType_filter
			";	
			
			$all_ratio_1 = getFirstItem($sql_all_ratio_1);
			$all_ratio_2 = getFirstItem($sql_all_ratio_2);
			$all_ratio_3 = getFirstItem($sql_all_ratio_3);
			$all_ratio_4 = getFirstItem($sql_all_ratio_4);
			$all_ratio_5 = getFirstItem($sql_all_ratio_5);
			$all_ratio_6 = getFirstItem($sql_all_ratio_6);
			$all_ratio_7 = getFirstItem($sql_all_ratio_7);
			$all_ratio_8 = getFirstItem($sql_all_ratio_8);
			$all_ratio_9 = getFirstItem($sql_all_ratio_9);
			
			$all_ratio = $all_ratio_1 + $all_ratio_2 + $all_ratio_3 + $all_ratio_4 + $all_ratio_5 + $all_ratio_6 + $all_ratio_7 + $all_ratio_8 + $all_ratio_9;
			//echo $sql_all_ratio; 
			//$all_ratio = getFirstItem($sql_all_ratio);
			$total_all_ratio += $all_ratio;


			
			
			$this_emp_sql = "select 
					count(company.CID)
					
				from 
				
					company
					JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
				
					
					left join provinces on company.Province = provinces.province_id
				where 
					(lawfulstatus != 3 or lawfulstatus is null)
				and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '0' $branch_codition
				and company.Province = '".$desired_province_id."'
					$typecode_filter
					
					
					$branch_codition
					
					$last_modified_sql
					
					$school_filter
					
					$CompanyType_filter

			
					";							

		
		$this_emp = default_value(getFirstItem($this_emp_sql),0);
		$sum_emp += $this_emp;
		//echo $sum_emp;
		
		
		
		

		
		
		
		
		//---------- curator stuffs
		  
		//yoes 20190113
		//yoes 20190410 -- this is fking slow so change it
		// if($the_year >= 2018 && $the_year <= 2050 ){ //&& 1==0
			
		// 	$new_law_35_join_condition = "
			
		// 		left join
			
		// 			(
					
		// 				SELECT distinct(meta_curator_id) as the_child_curator
		// 						  FROM   curator_meta
		// 						  WHERE  meta_for = 'child_of'
		// 								 AND meta_value != 0
					
		// 			) aa
					
		// 			on 
					
		// 			curator_id = the_child_curator
					
		// 		left join
				
		// 			(
					
		// 						 SELECT distinct(meta_curator_id)
		// 						  FROM   curator_meta
		// 						  WHERE  meta_for = 'is_extra_35'
		// 								 AND meta_value = 1
					
		// 			) bb
					
		// 			on 
					
		// 			curator_id = meta_curator_id
			
		// 	";
			
			
		// 	$new_law_35_where_condition = "

		// 			and (
		// 					the_child_curator is null 
		// 					or 
		// 					the_child_curator = ''
		// 				)
		// 			and (
		// 					meta_curator_id is null 
		// 					or 
		// 					meta_curator_id = ''
		// 				)

		// 	";
		// }
		  
		  
		//  //select users
		// $the_sql = "
												
		// 			select count(*) 
		// 			from 
		// 			curator 
					
		// 				$new_law_35_join_condition
						
		// 			where 
					
		// 			curator_lid in
		// 			(
					
		// 				select 
		// 					lid
		// 				from 
		// 					lawfulness
		// 					, company
		// 					, provinces
		// 				where 
		// 					company.CID = lawfulness.CID
		// 					and provinces.province_id = $desired_province_id
		// 					and company.Province = provinces.province_id
		// 					and LawfulStatus = '1' and Hire_status = '0' and pay_status = '0' and Conc_status = '1' $branch_codition							
		// 					$typecode_filter
							
		// 					and Year = '$the_year'
							
		// 					$branch_codition
							
		// 					$last_modified_sql
							
		// 					$school_filter
					
		// 					$CompanyType_filter

		// 			)
					
		// 			and 
		// 			curator_parent = 0
		// 			and
		// 			curator_is_disable = 0
					
		// 			$new_law_35_where_condition
					
		// 		";
				
		$the_sql = "select 
				count(company.CID)
				
			from 
			
				company
				JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
			
				
				 left join provinces on company.Province = provinces.province_id
			where 
				(lawfulstatus != 3 or lawfulstatus is null)
			and LawfulStatus = '1' and Hire_status = '0' and pay_status = '0' and Conc_status = '1' $branch_codition
			and company.Province = '".$desired_province_id."'
				$typecode_filter
				
				
				$branch_codition
				
				$last_modified_sql
				
				$school_filter
				
				$CompanyType_filter
	
				
						";			
				
		//echo "<br>curator_user :: $the_sql";
				
		
		$curator_user = getFirstItem($the_sql);	
		$total_curator_user += $curator_user;	



		$the_sql_34 = "select 
				count(company.CID)
				
			from 
			
				company
				JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
			
				
				 left join provinces on company.Province = provinces.province_id
			where 
				(lawfulstatus != 3 or lawfulstatus is null)
			and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '0' $branch_codition
			and company.Province = '".$desired_province_id."'
				$typecode_filter
				
				
				$branch_codition
				
				$last_modified_sql
				
				$school_filter
				
				$CompanyType_filter
	
				
						";			
				
		//echo "<br>curator_user :: $the_sql";
				
		
		$pay_34 = getFirstItem($the_sql_34);	
		$total_pay_34 += $pay_34;	
		
		
		$the_sql_33_35 = "select 
			count(company.CID)
			
		from 
		
			company
			JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )
		
			
			left join provinces on company.Province = provinces.province_id
		where 
			(lawfulstatus != 3 or lawfulstatus is null)
		and LawfulStatus = '1' and Hire_status = '1' and pay_status = '0' and Conc_status = '1' $branch_codition
		and company.Province = '".$desired_province_id."'
			$typecode_filter
			
			
			$branch_codition
			
			$last_modified_sql
			
			$school_filter
			
			$CompanyType_filter

			
					";			
			
	//echo "<br>curator_user :: $the_sql";
			

	$detail_33_35 = getFirstItem($the_sql_33_35);	
	$total_detail_33_35 += $detail_33_35;	

		
	$the_sql_34_35 = "select 
			count(company.CID)
			
		from 

			company
			JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )

			
			left join provinces on company.Province = provinces.province_id
		where 
			(lawfulstatus != 3 or lawfulstatus is null)
		and LawfulStatus = '1' and Hire_status = '0' and pay_status = '1' and Conc_status = '1' $branch_codition
		and company.Province = '".$desired_province_id."'
			$typecode_filter
			
			
			$branch_codition
			
			$last_modified_sql
			
			$school_filter
			
			$CompanyType_filter

			
					";			
			
		//echo "<br>curator_user :: $the_sql";
			

		$detail_34_35 = getFirstItem($the_sql_34_35);	
		$total_detail_34_35 += $detail_34_35;	
		

		$the_sql_33_34_35 = "select 
			count(company.CID)
			
		from 

			company
			JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )

			
			left join provinces on company.Province = provinces.province_id
		where 
			(lawfulstatus != 3 or lawfulstatus is null)
		and LawfulStatus = '1' and Hire_status = '1' and pay_status = '1' and Conc_status = '1' $branch_codition
		and company.Province = '".$desired_province_id."'
			$typecode_filter
			
			
			$branch_codition
			
			$last_modified_sql
			
			$school_filter
			
			$CompanyType_filter

			
					";			
			
		//echo "<br>curator_user :: $the_sql";
			

		$detail_33_34_35 = getFirstItem($the_sql_33_34_35);	
		$total_detail_33_34_35 += $detail_33_34_35;	



		$the_sql_lawful_2 = "select 
			count(company.CID)
			
		from 

			company
			JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )

			
			left join provinces on company.Province = provinces.province_id
		where 
			(lawfulstatus != 3 or lawfulstatus is null)
		and LawfulStatus = '2' $branch_codition
		and company.Province = '".$desired_province_id."'
			$typecode_filter
			
			
			$branch_codition
			
			$last_modified_sql
			
			$school_filter
			
			$CompanyType_filter

			
					";			
			
		//echo "<br>curator_user :: $the_sql";
			

		$detail_law_2 = getFirstItem($the_sql_lawful_2);	
		$total_lawful_2 += $detail_law_2;	

		$the_sql_lawful_3 = "select 
		count(company.CID)
		
			from 

				company
				JOIN lawfulness ON (company.CID = lawfulness.CID and Year = '$the_year' )

				
				left join provinces on company.Province = provinces.province_id
			where (lawfulstatus != 3 or lawfulstatus is null)
			and (LawfulStatus = '0' or LawfulStatus is null) $branch_codition
			and company.Province = '".$desired_province_id."'
				$typecode_filter
								
				$last_modified_sql
				
				$school_filter
				
				$CompanyType_filter

				
						";			
				
			//echo "<br>curator_user :: $the_sql";
				

			$detail_law_3 = getFirstItem($the_sql_lawful_3);	
			$total_lawful_3 += $detail_law_3;	
		
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
		
		//filter for detailed page
		$filter_i = $i;
		
		//extra for non-ratio report
		if($_POST["chk_non_ratio"] && $i == 8){
			$filter_i = 9;
		}
		
		if($_POST["chk_non_ratio"] && $i == 9){
			$filter_i = 8;
		}
	  
	  	
	  
	  ?>
      
      
      <tr>
        <td  valign="top"><div align="center">
		
				<?php echo $the_count;?>           
          </div></td>
        <td  valign="top"><div align="left">
		<?php 	
				echo $province_name_array[$i];
		?>
            </div></td>
        <td align="right"  valign="top"><div align="right">
		<?php 
			if(!$this_company){ 

				$this_company = "-";

			}

			?>
		
			<?php echo $this_company;?> 
        
        
       		 <?php if($chk_details){ ?>
            <a href="report_1_details.php?year=<?php echo $the_year;?>&filter=<?php echo $filter_i; ?>&data=99" target="_blank" title="แสดงรายละเอียด">...</a>
            <?php }?>  
        
        
        </div></td>
		<td align="right"  valign="top">
		<div align="right">

			<?php 
			if(!$all_ratio || $all_ratio == "0"){ 
				$all_ratio = "-";

			}

			?>
				
			<?php echo $all_ratio; ?>
		</div>
		</td>

		<!-- 33 -->
		<td align="right"  valign="top"><div align="right">
			<?php 
			if(!$this_emp || $this_emp == "0"){ 

				$this_emp = "-";

			}

			?>
				<?php echo $this_emp; ?>
		
		</div></td>

		<!-- 34 -->

		<td align="right"  valign="top">
		<?php 
			if(!$pay_34 || $pay_34 == "0"){ 

				$pay_34 = "-";

			}

			?>
				<?php echo $pay_34; ?>


		</td>

			
		<!-- 35 -->
		<td align="right"  valign="top">
		<?php 
			if(!$curator_user || $curator_user == "0"){ 

				$curator_user = "-";

			}

			?>
		<?Php 

				echo $curator_user;
			
		?>
		</td>

		<td align="right"  valign="top">
		<?php 
			if(!$detail_33_35 || $detail_33_35 == "0"){ 

				$detail_33_35 = "-";

			}

			?>
		<?Php 
		echo $detail_33_35;
		?>
		</td>

		<td align="right"  valign="top">
		<?php 
			if(!$detail_34_35 || $detail_34_35 == "0"){ 

				$detail_34_35 = "-";

			}

			?>
		<?Php 
		echo $detail_34_35;
		?>
		</td>

		<td align="right"  valign="top">
		<?php 
			if(!$detail_33_34_35 || $detail_33_34_35 == "0"){ 

				$detail_33_34_35 = "-";

			}

			?>
		<?Php 
		echo $detail_33_34_35;
		?>
		</td>

		<td align="right"  valign="top">
		<?php 
			if(!$detail_law_2 || $detail_law_2 == "0"){ 

				$detail_law_2 = "-";

			}

			?>
		<?Php 
		echo $detail_law_2;
		?>
		</td>

		<td align="right"  valign="top">
		<?php 
			if(!$detail_law_3 || $detail_law_3 == "0"){ 

				$detail_law_3 = "-";

			}

			?>
		<?Php 
		echo $detail_law_3;
		?>
		</td>

		
      
				 
      
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
        <td colspan="2" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($total_company);?></strong> </div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?php echo formatEmployeeReport($total_all_ratio);?></strong> </div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?Php echo $sum_emp;?></strong> </div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?Php echo $total_pay_34;?></strong></div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?Php echo formatEmployeeReport($total_curator_user);?></strong></div></td>
        <td align="right"  <?php echo $footer_row?>><div align="right"><strong><?Php echo $total_detail_33_35;?></strong></div></td>
        <td align="right"  <?php echo $footer_row?>><strong><?php echo $total_detail_34_35; ?></strong></td>
        <td align="right"  <?php echo $footer_row?>><strong><?php echo $total_detail_33_34_35; ?></strong></td>
        <td align="right"  <?php echo $footer_row?>><strong><?php echo $total_lawful_2; ?></strong></td>
        <td align="right"  <?php echo $footer_row?>><strong><?php echo $total_lawful_3; ?></strong></td>

        </tr>
      </tfoot>
</table>
    
    
<?php if($is_html){?>
             
        <script type="text/javascript" src="Charts/FusionCharts.js"></script>
            
        <?php 
        
        
        $strXML = "<chart palette='2' piefillAlpha='34' pieBorderThickness='3' hoverFillColor='FDCEDA' pieBorderColor='FFFFFF' baseFontSize='9' useHoverColor='1' caption='' >     <category label='การปฏิบัติตามกฎหมายของสถานประกอบการ'  fillColor='DBFF6C' >        		<category label='ปฏิบัติตามกฎหมายครบตามอัตราส่วน' fillColor='DBFF6C' value='".deleteCommas($total_lawful_company)."'>					   <category label='(ม.33)' value='$company_1' >				  			   </category>			   			   <category label='(ม.34)' value='$company_2'>							   </category>			   			   <category label='(ม.35)' value='$company_3'>							   </category>			   <category label='(ม.33,34)' value='$company_4'>							   </category>			   <category label='(ม.33,35)' value='$company_5'>							   </category>			   <category label='(ม.34,35)' value='$company_6'>							   </category>			   <category label='(ม.33,34,35)' value='$company_7'>							   </category>			   			           </category>						     <category label='ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน' fillColor='FFFF00'  value='".deleteCommas($this_not_finish_company)."' >        		  				     </category>	      <category label='ไม่ปฏิบัติตามกฎหมาย' fillColor='DAEDFC' link='Details.asp?CIO' value='".deleteCommas($this_unlawful_company)."'>        				     </category>	 		 	   </category>        </chart>";
        
        //echo renderChart("Charts/MultiLevelPie.swf","",$strXML, "donut_1", 800, 800, false, true);
        
        
        $strXML = "<chart caption='ร้อยละการปฏิบัติตามกฎหมายของสถานประกอบการ' showPercentValues='1' formatNumberScale='0' >    <set label='ครบตามอัตราส่วน' value='".deleteCommas($total_lawful_company)."' color='40FF00'/>   <set label='ไม่ครบตามอัตราส่วน' color='FFFF00'  value='".deleteCommas($this_not_finish_company)."' />   <set label='ไม่ปฏิบัติตามกฎหมาย' value='".deleteCommas($this_unlawful_company)."' color='FF0000'/></chart>";
        
        $strXML2 = "<chart caption='ร้อยละการปฏิบัติตามกฎหมายครบตามอัตราส่วน แบ่งตามมาตรา' showPercentValues='1' palette='3' >    <set label='ม.33' value='$company_1' color='40FF00'/><set label='ม.34' value='$company_2' color='00FFFF' /><set label='ม.35' value='$company_3' color='FFFF00' /><set label='ม.33,34' value='$company_4' color='FF0000'/><set label='ม.33,35' value='$company_5' color='0000FF' /><set label='ม.34, 35' value='$company_6' color='#FACC2E' /><set label='ม.33,34,35' value='$company_7' color='FF00FF' /> </chart>";
        ?>    
        
        
        <script type="text/javascript">
           var chart1 = new FusionCharts("Charts/Pie3D.swf", "ChId1", "550", "400", "0", "1");
           chart1.setXMLData("<?php echo $strXML;?>");
           chart1.render("chart1div");
        </script>
        
        <script type="text/javascript">
           var chart2 = new FusionCharts("Charts/Pie3D.swf", "ChId2", "550", "400", "0", "1");
           chart2.setXMLData("<?php echo $strXML2;?>");
           chart2.render("chart2div");
        </script>
    
    
<?php }?>    


<?php if($_POST[chk_sum_company]){?>
<div align="center">
<br />
   <strong> จำนวนสถานประกอบการที่ปฏิบัติตามกฎหมายครบตามอัตราส่วน...</strong>
<br />
มีการรับคนพิการเข้าทำงานครบตามอัตราส่วน (ม.33): <?php echo formatEmployeeReport($company_1+$company_4+$company_5+$company_7);?> แห่ง
    <br />
    มีการส่งเงินเข้ากองทุนแทนการรับคนพิการเข้าทำงาน (ม.34): <?php echo formatEmployeeReport($company_2+$company_4+$company_6+$company_7);?> แห่ง
    <br />
    มีการให้สัมปทานฯ (ม.35): <?php echo formatEmployeeReport($company_3+$company_5+$company_6+$company_7);?> แห่ง
</div>



	<?php if(1==1){// yoes 20200913 -- for special stuffs?>
	<table border="1" >
		<tr>
			<td>
				<?php echo getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");?>
			</td>
			<td>
				<?php echo $total_lawful_employees_count;?>
			</td>
			<td>
				<?php echo $total_all_ratio;?>				
			</td>
			<td>
			</td>
			<td>
				<?php echo $row_data[0][this_emp]+$row_data[7][this_emp];?>
			</td>
			<td>
			</td>
			<td>
				<?php echo $row_data[2][curators]+$row_data[7][curators];?>
			</td>
			
			<td>
			</td>
			<td>
				<?php echo $row_data[4][this_emp];?>
			</td>
			<td>
				<?php echo $row_data[4][curators];?>
			</td>
			<td>
			</td>
			<td>
				<?php echo $row_data[3][this_emp];?>
			</td>
			<td>
				<?php echo $row_data[3][all_34];?>
			</td>
			<td>
			</td>
			<td>
				<?php echo $row_data[5][curators];?>
			</td>
			<td>
				<?php echo $row_data[5][all_34];?>
			</td>
			<td>
			</td>
			
			<td>
				<?php echo $row_data[6][this_emp];?>
			</td>
			
			<td>
				<?php echo $row_data[6][all_34];?>
			</td>
			<td>
				<?php echo $row_data[6][curators];?>
			</td>
			
			<td>
			</td>
			
			<td>
				<?php echo $row_data[1][all_34]+$row_data[7][all_34];?>
			</td>
			
			<td>
			</td>
			
			<td>
				<?php echo $sum_rule_35;?>
			</td>
			
			<td>
			</td>
			<td>
			</td>
			<td>
			</td>
			
			<td>
			</td>
			<td>
			</td>			
			
		</tr>
	<table>
	<?php }?>

<?php }?>


    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>