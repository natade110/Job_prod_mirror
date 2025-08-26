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





////// starts LOGIC here


$ratio_to_use = default_value(getFirstItem("select var_value from vars where var_name = 'ratio_$the_year'"),100);

if($is_2013){

	$condition_sql .= " and branchCode < 1";

}



$main_sql = "

			select
				BusinessTypeName
			from
				company a
					join lawfulness b 
						on a.CID = b.CID
					left outer join provinces c 
						on a.Province = c.province_id
					left outer join businesstype d 
						on a.BusinessTypeCode = d.BusinessTypeCode
						
			where
				
				b.Year = '$the_year'				
				
				$province_filter
				
				$condition_sql

				$business_filter
				
				$last_modified_sql
				
				$typecode_filter

			group by
				BusinessTypeName
			
			order by
				BusinessTypeName asc
			
			
			";


//echo $main_sql;

////////			
if($is_2013 ){



	//yoes 20140515 - fix it so it has same count logic as report 1
	//is it correct? - maybe
	$main_sql = "



			select
				ifnull(businesstypename, '- ไม่ระบุ -')  as BusinessTypeName				
				, ifnull(f.group_name,ifnull(ff.group_name, le_position )) as the_position
				
				, SUM(case when le_education = 9 then 1 else 0 end) as the_9
				, SUM(case when le_education = 8 then 1 else 0 end) as the_8
				, SUM(case when le_education = 7 then 1 else 0 end) as the_7
				, SUM(case when le_education = 6 then 1 else 0 end) as the_6
				, SUM(case when le_education = 5 then 1 else 0 end) as the_5
				, SUM(case when le_education = 4 then 1 else 0 end) as the_4
				, SUM(case when le_education = 3 then 1 else 0 end) as the_3
				, SUM(case when le_education = 2 then 1 else 0 end) as the_2
				, SUM(case when le_education = 1 then 1 else 0 end) as the_1
				, SUM(case when le_education not in (1,2,3,4,5,6,7,8,9) then 1 else 0 end) as the_0
				
				, SUM(case when le_gender = 'm' then 1 else 0 end) as the_m
				, SUM(case when le_gender = 'f' then 1 else 0 end) as the_f
				, SUM(case when le_gender = '' then 1 else 0 end) as the_null
			from
				company a
					join lawfulness b 
						on a.CID = b.CID
					left outer join provinces c 
						on a.Province = c.province_id
					left outer join businesstype d 
						on a.BusinessTypeCode = d.BusinessTypeCode
						
					join lawful_employees e
						on
						b.cid = e.le_cid
						and
						b.year = e.le_year
					
					left outer join
						position_group f
							on
							e.le_position_group = f.group_id
							
					LEFT OUTER JOIN position_group ff
						ON e.le_position = ff.group_id
					
						
			where
				
				b.Year = '$the_year'
								
				$province_filter
				
				$condition_sql
				
				$business_filter
				
				$last_modified_sql
				
				$typecode_filter

			group by
				ifnull(businesstypename, '- ไม่ระบุ -') 
				, ifnull(f.group_name,ifnull(ff.group_name, le_position ))
			order by
				ifnull(businesstypename, '- ไม่ระบุ -')  asc
				, ifnull(f.group_name,ifnull(ff.group_name, le_position )) asc
			
			
			
			";



}			
			



			
//echo $main_sql;	//exit();	

if($is_pdf || $is_excel){

	$w50 = 50;
	$w75 = 75;
	$w100 = 100;
	$w125 = 125;
	$w350 = 350;
	
}



?>

<div align="center">
            <strong>สถิติการจ้างงานคนพิการ ตามประเภทกิจการ แบ่งตามตำแหน่งงาน <?php echo $business_text;?> <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />
</strong>
              <br>
</div>
    
    
<table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:18px;"<?php }?>>
   	  <thead>
      
      <tr >
        <td width="0" rowspan="2" align="center" valign="bottom"><strong>ประเภทกิจการ</strong></td>
        <td width="0" rowspan="2" align="center" valign="bottom"><div align="center"><strong>ตำแหน่งงาน</strong></div></td>
        <td colspan="10" align="center" valign="bottom"><strong>การศึกษา(ราย)</strong></td>
        <td colspan="2" align="center" valign="bottom"><div align="center"><strong>เพศ(ราย)</strong></div></td>
        </tr>
      <tr >
        <td align="center" valign="bottom"><div align="center"><strong>ปริญญาเอก</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>ปริญญาโท</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>ปริญญาตรี</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>อนุปริญญา</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>ปวส ปวช</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>มัธยมปลาย</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>มัธยมต้น</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>ประถม</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>ไม่มีการศึกษา</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>อื่นๆ</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>ชาย</strong></div></td>
        <td align="center" valign="bottom"><div align="center"><strong>หญิง</strong></div></td>
      </tr>
      </thead>
      
      <tbody>
      
      
      
      <?php
		  $lawful_result = mysql_query($main_sql);	
		  
		  while ($lawful_row = mysql_fetch_array($lawful_result)) {
		  
			
			
	  ?>
      
              <tr>
                <td width="<?php echo $w75?>"  valign="top"><?php 
					
					echo $lawful_row[BusinessTypeName];
				
				?></td>
                <td width="<?php echo $w75?>"  valign="top"><?php 
					
					echo $lawful_row[the_position];
					//echo $lawful_row[group_name];
				
				?></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right">
                  <?php 
					
					echo number_format($lawful_row[the_9],0);
				
				?>
                </div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right">
                  <?php 
					
					echo number_format($lawful_row[the_8],0);
				
				?>
                </div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right">
                  <?php 
					
					echo number_format($lawful_row[the_7],0);
				
				?>
                </div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right">
                  <?php 
					
					echo number_format($lawful_row[the_6],0);
				
				?>
                </div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right">
                  <?php 
					
					echo number_format($lawful_row[the_5],0);
				
				?>
                </div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right">
                  <?php 
					
					echo number_format($lawful_row[the_4],0);
				
				?>
                </div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right">
                  <?php 
					
					echo number_format($lawful_row[the_3],0);
				
				?>
                </div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right">
                  <?php 
					
					echo number_format($lawful_row[the_2],0);
				
				?>
                </div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right">
                  <?php 
					
					echo number_format($lawful_row[the_1],0);
				
				?>
                </div></td>
                <td width="<?php echo $w75?>"  valign="top"><div align="right">
                  <?php 
					
					echo number_format($lawful_row[the_0],0);
				
				?>
                </div></td>
                <td width="<?php echo $w75?>"  valign="top">
				<div align="right">
				<?php 
					
					echo number_format($lawful_row[the_m],0);
				
				?>
                </div>
                </td>
                <td width="<?php echo $w75?>"  valign="top">
                
                <div align="right">
               <?php 
					
					echo number_format($lawful_row[the_f],0);
				
				?>
                </div>
                </td>      
                           
              </tr>
     			
     	
     	<?php }?>
	  </tbody>
        
        <tfoot>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
