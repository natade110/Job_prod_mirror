<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_112.xls");
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
	
	//$typecode_filter = " and CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	//$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "หน่วยงานภาครัฐ";
}


///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter .= " and CompanyTypeCode >= 200  and CompanyTypeCode < 300";
	$business_type = "หน่วยงานภาครัฐ";
	
	if($_POST["CompanyTypeCode"]){
		
		$typecode_filter .= " and CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'";
			
	}
	
}else{
	
	$typecode_filter .= " and CompanyTypeCode < 200";
	
}




//businesss type
if(isset($_POST["CompanyTypeCode"]) && $_POST["CompanyTypeCode"]){
	
	if($_POST["CompanyTypeCode"] == "0000"){
	
		//not indicated business code?
		//also include NULL
		$business_filter = "
		
					and 
					
					(
					
						a.CompanyTypeCode = '".$_POST["CompanyTypeCode"]."'
						or 
						a.CompanyTypeCode 
						NOT IN (

							SELECT CompanyTypeCode
							FROM companytype
						)
						OR a.CompanyTypeCode =  ''
					)
					
					";
	
	}else{
	
		$business_filter = " and a.CompanyTypeCode = '".$_POST["CompanyTypeCode"]."'";
	}
	
	
	
	$bus_name = getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '".$_POST["CompanyTypeCode"]."'");
	$business_text = ": ". $bus_name;
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


//employees limit
$the_employees_limit = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_$this_year'"),100);



////// starts LOGIC here


$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_$the_year'"),100);

if($is_2013){

	$condition_sql .= " and branchCode < 1";

}



$main_sql = "

			select
				*
				, IF(b.Employees > 0, b.Employees, a.Employees) as company_employees
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
				
				$condition_sql

				$business_filter
				
				$last_modified_sql
				
				$typecode_filter
				
				and
				  (
					
					 IF(b.Employees > 0, b.Employees, a.Employees) < $the_employees_limit
				  )
			
			order by
				province_name, CompanyNameThai asc
			
			
			";


////////			
if($is_2013){

$main_sql = "

			select
				*
				, IF(b.Employees > 0, b.Employees, a.Employees) as company_employees
				
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
				
				$condition_sql
				
				$business_filter
				
				$last_modified_sql
				
				$typecode_filter
				
				and
				  (
					
					IF(b.Employees > 0, b.Employees, a.Employees) < $the_employees_limit
				  )
				
				
			
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
            <strong>รายละเอียดหน่วยงานภาครัฐที่ไม่เข้าข่าย<?php echo $business_text;?> <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />
</strong>
              <br>
</div>
    
    
<table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td width="0" rowspan="3" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="0" rowspan="3" valign="bottom"><div align="center"><strong>เลขทะเบียนหน่วยงาน</strong></div></td>
        <td width="0" rowspan="3" valign="bottom"><div align="center"><strong>ชื่อหน่วยงาน</strong></div></td>
        <td width="0" rowspan="3" valign="bottom"><strong>ประเภทหน่วยงาน</strong></td>
        <td width="0" rowspan="3" valign="bottom"><div align="center"><strong>ที่อยู่</strong></div></td>
        <td width="0" rowspan="3" valign="bottom"><div align="center"><strong>จำนวน<?php echo $the_employees_word;?></strong></div></td>
        <td colspan="4" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>การปฏิบัติตามกฎหมาย</strong></div></td>
        </tr>
      <tr >
        <td width="0" rowspan="2" valign="bottom" ><div align="center"><strong>
          
          มาตรา 33
  <br />(ราย)
          
          
        </strong></div></td>
        <td colspan="2" valign="bottom" ><div align="center"><strong>มาตรา 35</strong></div></td>
        <td width="0" rowspan="2" valign="bottom" ><div align="center"><strong>สถานะ
</strong></div></td>
      </tr>
      <tr >
        <td width="0" valign="bottom" ><div align="center"><strong>คนพิการ<br />
          
          (ราย)
</strong></div></td>
        <td width="0" valign="bottom" ><div align="center"><strong>ผู้ดูแลคนพิการ<br />
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
		
				//echo "<br>". $the_sql;
		
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
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $lawful_row["CompanyCode"];?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $company_name_to_use;?></div>          </td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '".$lawful_row["CompanyTypeCode"]."'");?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $address_to_use;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo $lawful_row["company_employees"]?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right"><?php echo $lawful_row["Hire_NumofEmp"]?></div></td>
                <?php
				
					
					//echo $the_money_sql;
					
					if($money_num_rows){
					
						//echo "hey";
						while ($pmh_row = mysql_fetch_array($paid_money_history_result)) {
						
							
						?>
                        
                        <?php
						
						
						}
					
					}else{
					
					?>
                    
                    <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php echo $curator_usee;?></div></td>
                    <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php echo $curator_user;?></div></td>
                        <td width="<?php echo $w75?>"  valign="top"><div align="center">
                        
                        <?php if($lawful_row["LawfulStatus"] == 0){echo 'ไม่ปฏิบัติตามกฎหมาย';}?>
                        <?php if($lawful_row["LawfulStatus"] == 1){echo 'ปฏิบัติตามกฎหมาย';}?>
                          <?php if($lawful_row["LawfulStatus"] == 2){echo 'ปฏิบัติตามกฏหมายแต่ไม่ครบตามอัตราส่วน';}?>
                        
                        </div></td>
                    <?php
					
					}
				
				?>
              </tr>
     
     
     			 <?php
				
					
					//echo $the_money_sql;
					
					if($money_num_rows){
					
						//echo "hey";
						while ($pmh_row = mysql_fetch_array($paid_money_history_result_2)) {
						
							
						?>
                        <?php
						
						
						}
					
					}
					
					?>
                    
     			
     	
     	<?php }?>
	  </tbody>
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
