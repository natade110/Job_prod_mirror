<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_112.xls");
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




//businesss type
if(isset($_POST["BusinessTypeCode"]) && $_POST["BusinessTypeCode"]){
	
	if($_POST["BusinessTypeCode"] == "0000"){
	
		//not indicated business code?
		//also include NULL
		$business_filter = "
		
					and 
					
					(
					
						a.BusinessTypeCode = '".$_POST["BusinessTypeCode"]."'
						or 
						a.BusinessTypeCode 
						NOT IN (

							SELECT BusinessTypeCode
							FROM businesstype
						)
						OR a.BusinessTypeCode =  ''
					)
					
					";
	
	}else{
	
		$business_filter = " and a.BusinessTypeCode = '".$_POST["BusinessTypeCode"]."'";
	}
	
	
	
	$bus_name = getFirstItem("select BusinessTypeName from businesstype where BusinessTypeCode = '".$_POST["BusinessTypeCode"]."'");
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


			
			
	//new condition as of 20140520
	$main_sql = "

			select
				*
				, b.Employees as company_employees
				, a.CID as the_company_cid
				, b.LID as the_lid
			from
				company a
				 left outer join lawfulness b on a.CID = b.CID
				, provinces c
			where
				
				b.Year = '$the_year'
				
				and 
				a.Province = c.province_id
				
				and LawfulStatus = '3'
				
				$province_filter
				
				$condition_sql

				$business_filter
				
				$last_modified_sql
				
				$typecode_filter
				
			order by
				province_name, CompanyNameThai asc
			
			
			";


$the_ratio = getThisYearRatio($the_year);

////////			
if($is_2013 || 1==1){ // yoes 20170204 --- default sql for all years


		//new conditon as of 20140520

		$main_sql = "
		
		
				select
					companyCode
					, branchCode
					, companyNameThai
					, companyTypeCode
					, province_name
					, b.Year					
					, lawfulStatus
					, le_count
					, payment_count
					, curator_count	
					
					, hire_status
					, pay_status
					, conc_status
					
					, hire_NumofEmp
					
					, b.Employees
									
				from
					company a
						join lawfulness b
							on a.cid = b.cid
							and b.Year = '$the_year'
						
						left join provinces c
							on a.Province = c.province_id
							
							
						left join
						
							(
								
								select
									le_cid
									, le_year
									, count(le_cid) as le_count
								from								
									lawful_employees 
										
								where
									le_year = '$the_year'
									
								group by
									le_cid
									, le_year
									
								
							) d
			
							on
							d.le_cid = b.cid
							and
							d.le_year = b.year
							
							
						left join
						
							(
								
								select
									curator_lid
									, count(curator_id) as curator_count
								from								
									curator 										
								where
									curator_parent = 0
								group by
									curator_lid
									
								
							) e
			
							on
							e.curator_lid = b.lid
							
							
						left join
						
							(
								
								select
									pid
									, lid
									, aa.rid
									, count(pid) as payment_count
								from
									payment aa
										join receipt bb
											on aa.RID = bb.RID
								
								group by
									pid
									, lid
									, aa.rid
									
								
							) f
							
							on f.lid = b.lid
							
			
				where
				
					
					(
					
						(
							lawfulStatus = 0
							and
							(
							
								hire_status > 0
								or
								pay_status > 0
								or
								conc_status > 0					
							
							)
							
							and												
							b.Employees >= $the_ratio
							
						)
						or
						(
							(lawfulStatus = 1 || lawfulStatus = 2)
							and
							(
							
								hire_status = 0
								and
								pay_status = 0
								and
								conc_status = 0					
							
							)
							
							and												
							b.Employees >= $the_ratio
							
						)
						
						or
						(
							
							hire_status = 1
							and												
							( le_count < 1 or le_count is null)
							
							and												
							b.Employees >= $the_ratio
							
						)
						
						or
						(
							
							hire_status = 0
							and												
							le_count > 0
							
							and												
							b.Employees >= $the_ratio
							
						)
						
						or
						(
							
							hire_NumofEmp != le_count
							
							and												
							b.Employees >= $the_ratio
							
						)
						
						
						or
						(
							
							conc_status = 1
							and												
							( curator_count < 1 or curator_count is null)
							
							and												
							b.Employees >= $the_ratio
							
						)
						
						or
						(
							
							conc_status = 0
							and												
							curator_count > 0
							
							and												
							b.Employees >= $the_ratio
							
						)
						
						
						or
						(
							
							pay_status = 1
							and												
							( payment_count < 1 or payment_count is null)
							
							and												
							b.Employees >= $the_ratio
							
						)
						
						or
						(
							
							pay_status = 0
							and												
							payment_count > 0
							
							and												
							b.Employees >= $the_ratio
							
						)
						
					
						or
						(
							
							lawfulStatus != 3
							and												
							b.Employees < $the_ratio
							
						)
					)
					
					$province_filter
					
					$condition_sql
					
					$business_filter
					
					$last_modified_sql
					
					$typecode_filter
					
				
				
	
				order by
					CompanyNameThai asc
				
				
				
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
            <strong>รายงานตรวจสอบสถานะการปฏิบัติตามกฎหมายที่ไม่ถูกต้องในระบบ <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />
</strong>
              <br>
</div>
    
    
<table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td width="0" rowspan="2" align="center" valign="bottom"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom"><div align="center"><strong>เลขทะเบียนนายจ้าง</strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom"><div align="center"><strong>สาขา</strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom"><div align="center"><strong>ชื่อสถานประกอบการ</strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom"><div align="center"><strong>ที่อยู่</strong></div></td>
        <td width="0" rowspan="2" align="center" valign="bottom"><div align="center"><strong>สถานะ </strong></div></td>
        <td colspan="2" align="center" valign="bottom" ><div align="center"><strong>สถานะการแสดงผลผิด</strong></div></td>
        </tr>
      <tr >
        <td width="0" align="center" valign="bottom" ><div align="center"><strong>กรณี
</strong></div></td>
        <td width="0" align="center" valign="bottom" ><div align="center"><strong>ปี</strong></div></td>
        </tr>
      </thead>
      
      <tbody>
      
      
      
      <?php
		  $lawful_result = mysql_query($main_sql);	
		  
		  while ($lawful_row = mysql_fetch_array($lawful_result)) {
		  
			$row_count++;
			$company_name_to_use = formatCompanyName($lawful_row["companyNameThai"],$lawful_row["companyTypeCode"]);
			
			//
			
			$final_employee = getEmployeeRatio( $lawful_row["company_employees"],$ratio_to_use);
			
			$type_35_to_use = getFirstItem("select count(*) from curator where curator_lid = '".$lawful_row["LID"]."'");
			
			
			
			
			$this_row = 0;
			
			
				
			
			//yoes 20170113
			//calculate statuses
			if(
				$lawful_row[hire_status] == "1"
				&& 
				$lawful_row[le_count]*1 < 1
				
				){
				
				$the_status = "มีการทำ ม33 แต่ไม่มีรายละเอียดการคนพิการ ม.33";	
				
			}elseif(
				$lawful_row[hire_status] == 0
				&& 
				$lawful_row[le_count]*1 > 0
				
				){
				
				$the_status = "ไม่มีการทำ ม33 แต่มีรายละเอียดการคนพิการ ม.33";	
				
			}elseif(
			
				$lawful_row[hire_NumofEmp]*1 != $lawful_row[le_count]*1
				
				){
				
				$the_status = "จำนวน ม33 ในหน้าการปฎิบัติ ไม่ตรงกับจำนวนรายละเอียดคนพิการ";	
				
			}elseif(
			
				$lawful_row[conc_status] == "1"
				&& 
				$lawful_row[curator_count]*1 < 1
				
				){
				
				$the_status = "มีการทำ ม35 แต่ไม่มีรายละเอียดการคนพิการ ม.35";	
				
			}elseif(
				$lawful_row[conc_status] == 0
				&& 
				$lawful_row[curator_count]*1 > 0
				
				){
				
				$the_status = "ไม่มีการทำ ม35 แต่มีรายละเอียดการคนพิการ ม.35";	
				
			}elseif(
			
				$lawful_row[pay_status] == "1"
				&& 
				$lawful_row[payment_count]*1 < 1
				
				){
				
				$the_status = "มีการทำ ม34 แต่ไม่มีรายละเอียดการจ่ายเงิน ม.34";	
				
			}elseif(
				$lawful_row[pay_status] == 0
				&& 
				$lawful_row[payment_count]*1 > 0
				
				){
				
				$the_status = "ไม่มีการทำ ม34 แต่มีรายละเอียดการจ่ายเงิน ม.34";	
				
			}elseif(
			
				$lawful_row[lawfulStatus] == "0"
				&& 
				$lawful_row[hire_status] + $lawful_row[pay_status] + $lawful_row[conc_status] > 0 
				
				){
				
				$the_status = "มีการทำ ม33 ม34 หรือ ม35 แต่สถานะเป็นไม่ปฏิบัติ";	
				
			}elseif(
			
				($lawful_row[lawfulStatus] == "1" || $lawful_row[lawfulStatus] == "2")
				&& 
				(
					$lawful_row[hire_status] + $lawful_row[pay_status] + $lawful_row[conc_status] < 1										
				)
				
				){
				
				$the_status = "ไม่มีการทำ ม33 ม34 หรือ ม35 แต่สถานะเป็นปฏิบัติ";	
				
			}elseif(
			
				$lawful_row[lawfulStatus] != "3"
				&& 
				$lawful_row[Employees]  < $the_ratio
				
				){
				
				
				//yoes 20170320 --- ask with user on how to decide this???
				$the_status = "จำนวนลูกจ้างไม่เข้าข่ายแต่สถานะไม่แสดงเป็น 'ไม่เข้าข่าย'";	
				
			}else{
				
				$the_status = "--";
					
			}
			
			
	  ?>
      
              <tr>
                <td width="<?php echo $w50?>"  valign="top"><div align="center"><?php echo $row_count;?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $lawful_row["companyCode"];?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php echo $lawful_row["branchCode"];?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $company_name_to_use;?></div>          </td>
                <td width="<?php echo $w75?>"  valign="top"><div align="left"><?php echo $lawful_row["province_name"];?></div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="center"><?php echo getLawfulText($lawful_row["lawfulStatus"]);?></div></td>
               
                    
                    <td width="<?php echo $w75?>" align="center"  valign="top"><?php 
					
						
						//echo $lawful_row[lawfulStatus];
						
						echo $the_status;
					
					?></td>
                    <td width="<?php echo $w75?>" align="right"  valign="top">
                    <div align="center">
                    
                    <?php 
					
						echo $lawful_row["Year"]+543;
						
					?>
                    </div>
                    
                    </td>
                   
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
