<?php

include "db_connect.php";

if($_POST["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_12.xls");

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
	$branch_codition =  " AND a.BranchCode < 1 ";

}

$the_year_to_use = formatYear($the_year);

$province_text = "ทั่วประเทศ";
$province_filter = "";		
if(isset($_POST["Province"]) && $_POST["Province"] != "" && $_POST["rad_area"] == "province"){
	$province_filter = " and a.province = '".$_POST["Province"]."'";
	if($_POST["Province"] != "1"){
		$province_prefix = "จังหวัด";
	}
	$province_text = "$province_prefix".getFirstItem("select province_name from provinces where province_id = '".$_POST["Province"]."'");
}

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
	
	$province_filter = " and a.section_id = '".$_POST["Section"]."'";
	$province_text = "".getFirstItem("select section_name from province_section where section_id = '".$_POST["Section"]."'");
}


///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter = " and (a.CompanyTypeCode = '14'";
	$typecode_filter .= " or a.CompanyTypeCode >= 200  or a.CompanyTypeCode < 300)";
	
}else{
	$typecode_filter = " and a.CompanyTypeCode != '14'";
	$typecode_filter .= " and a.CompanyTypeCode < 200";
	
}



//yoes 20160126
//other criterias


//yoes 20160209 -- speeding this up


if($_POST["le_code"]){
	
	$sql = "
		select
			curator_parent
		from
			curator
		where
			curator_parent != 0
			and
			curator_idcard like '%". doCleanInput($_POST["le_code"]) ."%'
			
	";
	
	$le_code_result = mysql_query($sql);
	
	while($le_code_row = mysql_fetch_array($le_code_result)){
		
		$le_id_list .= ",".$le_code_row[curator_parent];
		
	}
	

	$other_filter .= " 
	
		and 
		
			(
				curator_idcard like '%". doCleanInput($_POST["le_code"]) ."%'
		
				or
				
				curator_id in (
				
					0
					$le_id_list
				)
			)
						
			 ";
			 
	

}

if($_POST["le_disable_desc"] == "null"){

	//$other_filter .= " and (curator_disable_desc is null or curator_disable_desc = '' ) and curator_is_disable = 1";
	
	
	$sql = "
		select
			curator_parent
		from
			curator
		where
			curator_parent != 0
			and
			(curator_disable_desc like is null or curator_disable_desc = '')
			
	";
	
	$le_code_result = mysql_query($sql);
	
	while($le_code_row = mysql_fetch_array($le_code_result)){
		
		$le_id_list .= ",".$le_code_row[curator_parent];
		
	}
	
	$other_filter .= " 
	
		and 
		
			(
				(
					(
					curator_disable_desc  is null 
					or curator_disable_desc = ''
					)
					
					and 
					curator_is_disable = 1
				)
				
				or
				
				curator_id in (
				
					0
					$le_id_list
				)
			)
						
			 ";
	

}elseif($_POST["le_disable_desc"]){

	

	//$other_filter .= " and curator_disable_desc like '%". doCleanInput($_POST["le_disable_desc"]) ."%' and curator_is_disable = 1";
	$sql = "
		select
			curator_parent
		from
			curator
		where
			curator_parent != 0
			and
			curator_disable_desc like '%". doCleanInput($_POST["le_disable_desc"]) ."%'
			
	";
	
	$le_code_result = mysql_query($sql);
	
	while($le_code_row = mysql_fetch_array($le_code_result)){
		
		$le_id_list .= ",".$le_code_row[curator_parent];
		
	}
	
	$other_filter .= " 
	
		and 
		
			(
				(
				curator_disable_desc like '%". doCleanInput($_POST["le_disable_desc"]) ."%'
				and curator_is_disable = 1
				)
				
				or
				
				curator_id in (
				
					0
					$le_id_list
					
				)
			)
						
			 ";
	
	

}

if($_POST["CompanyCode"]){

	$other_filter .= " and CompanyCode like '%". trim(doCleanInput($_POST["CompanyCode"])) ."%'";

}

if($_POST["CompanyNameThai"]){


	$other_filter .= " and CompanyNameThai like '%". trim(doCleanInput($_POST["CompanyNameThai"])) ."%'";

}

//new criteria for m35
if($_POST["curator_event"] == "อื่นๆ"){

	$other_filter .= " 
						and curator_event  NOT IN (
	
							'การจัดให้มีบริการล่ามภาษามือ'
							,'การฝึกงาน'
							,'การให้ความช่วยเหลืออื่นใด'
							,'การให้สัมปทาน'
							,'จัดจ้างเหมาช่วงงาน'
							,'จัดสถานที่จำหน่ายสินค้าหรือบริการ'
							,'ฝึกงาน'
							
							,'การให้สัมปทาน'
							,'จัดสถานที่จำหน่ายสินค้าหรือบริการ'
							,'จัดจ้างเหมาช่วงงาน'
							,'ฝึกงาน'
							,'การจัดให้มีอุปกรณ์หรือสิ่งอำนวยความสะดวก'
							,'การจัดให้มีบริการล่ามภาษามือ'
							,'การให้ความช่วยเหลืออื่นใด'
						
						)
						";


}elseif($_POST["curator_event"]){


	$other_filter .= " and curator_event like '%". doCleanInput($_POST["curator_event"]) ."%'";

}




//yoes 20160119 -- variables
$the_limit = 100;
$half_limit = $the_limit/2;

$the_cost_per_person = 300*365;

$the_wage = 300;
$year_date = 365;

?>

<div align="center">
  <strong>รายละเอียดการปฏิบัติตามกฎหมาย ในมาตรา 35 
  <br /> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	
   	 <tr >
   	   <td rowspan="2"  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ลำดับ</strong></div></td>
   	   <td colspan="6"  align="center" valign="middle"><strong>รายละเอียดผู้ใช้สิทธิ</strong></td>
   	   <td colspan="5"  align="center" valign="middle"><strong>รายละเอียดคนพิการที่ถูกใช้สิทธิ</strong></td>
   	   <td colspan="6"  align="center" valign="middle"><strong>รายละเอียดการใช้สิทธิ</strong></td>
   	   <td colspan="2"  align="center" valign="middle"><strong>รายละเอียดบริษัทที่ใช้สิทธิ</strong></td>
      </tr>
   	 <tr >
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ชื่อ - นามสกุล</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>เพศ</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>อายุ</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>เลขบัตรประชาชน</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ผู้ใช้สิทธิเป็น</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ลักษณะความพิการ</strong></div></td>
       
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ชื่อ - นามสกุล</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>เพศ</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>อายุ</strong></div></td>       
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>เลขบัตรประชาชน</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ลักษณะความพิการ</strong></div></td>
       
       					

       
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>วันเริ่มต้นสัญญา</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>วันสิ้นสุดสัญญา</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ระยะเวลา (วัน)</strong></div></td>       
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>กิจกรรม</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>มูลค่า (บาท)</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>หมายเหตุ</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ชื่อสถานประกอบการ</strong></div></td>
       <td  align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ที่อยู่</strong></div></td>
       
      </tr>
   	 
   	 
   
     
      
    
      
     
     <?php 
	 
	 
	 if($the_year >= 2018 && $the_year <= 2050 && !$other_filter){ //&& 1==0
			
			$new_law_35_join_condition = "
			
				left join
			
					(
					
						SELECT distinct(meta_curator_id) as the_child_curator
								  FROM   curator_meta
								  WHERE  meta_for = 'child_of'
										 AND meta_value != 0
					
					) aaa
					
					on 
					
					aa.curator_id = the_child_curator
					
				left join
				
					(
					
								 SELECT distinct(meta_curator_id)
								  FROM   curator_meta
								  WHERE  meta_for = 'is_extra_35'
										 AND meta_value = 1
					
					) bbb
					
					on 
					
					aa.curator_id = meta_curator_id
			
			";
			
			
			$new_law_35_where_condition = "

					and (
							the_child_curator is null 
							or 
							the_child_curator = ''
						)
					and (
							meta_curator_id is null 
							or 
							meta_curator_id = ''
						)

			";
		}
	 
	 
	 //yoes 20160119 -- loop for all provinces
	 $conc_sql = "
	 
	 	select
			*
		from
			curator aa
				join
					lawfulness b
						on 
						aa.curator_lid = b.lid
						and b.year = '$the_year'
				join
					company a
						on
						a.cid = b.cid
						
						
				$new_law_35_join_condition
		where
		
				curator_parent = 0				
				
				$province_filter
				
				$typecode_filter
				
				$branch_codition
				
				
				
				$other_filter
				
				
				$new_law_35_where_condition
				
				
	 
	 ";
	 
	 //
	 //echo $conc_sql;  exit();
	 $conc_result = mysql_query($conc_sql) or die($mysql_error);
	 
	 
	 
	 while($conc_row = mysql_fetch_array($conc_result)){
		
		$row_count++;
		
		?>
        
        
        <tr >
           <td align="center"   valign="middle"><?php echo $row_count;?></td>
           <td   valign="middle"><?php echo doCleanOutput($conc_row[curator_name]);?></td>
           <td   valign="middle"><?php echo formatGender($conc_row[curator_gender]);?></td>
           <td align="right"   valign="middle"><?php echo doCleanOutput($conc_row[curator_age]);?></td>
           <td   valign="middle"><?php echo doCleanOutput($conc_row[curator_idcard]);?></td>           
           <td   valign="middle"><?php echo formatCuratorType($conc_row[curator_is_disable]);?></td>
          
           
           <?php if($conc_row[curator_is_disable]){
			   
			   //disabled have no child
			   ?>
           
            <td   valign="middle"><?php echo doCleanOutput($conc_row[curator_disable_desc]);?></td>
            
            <td   valign="middle">-</td>  
           <td   valign="middle">-</td>  
           <td align="right"  valign="middle">-</td>  
           <td   valign="middle">-</td>  
           <td   valign="middle">-</td>  
           
           <?php }else{			   
			   
			   //non-disabled have child
			    $child_row = getFirstRow("select * from curator where curator_parent = '".$conc_row[curator_id]."'");
			   
			   ?>
           
           <td   valign="middle">-</td>  
           <td   valign="middle"><?php echo doCleanOutput($child_row[curator_name]);?></td>
           <td   valign="middle"><?php echo formatGender($child_row[curator_gender]);?></td>
           <td align="right"   valign="middle"><?php echo doCleanOutput($child_row[curator_age]);?></td>  
           <td   valign="middle"><?php echo doCleanOutput($child_row[curator_idcard]);?></td>      
           
          <td   valign="middle"><?php echo doCleanOutput($child_row[curator_disable_desc]);?></td>
          
          
          
           <?php }?>
           
           <td   valign="middle"><?php echo formatDateThai($conc_row[curator_start_date]);?></td>
           <td   valign="middle"><?php echo formatDateThai($conc_row[curator_end_date]);?></td>
           <td   align="right"  valign="middle"><?php echo number_format(dateDiffTs(strtotime($conc_row["curator_start_date"]), strtotime($conc_row["curator_end_date"])),0);?></td>
           <td   valign="middle"><?php echo doCleanOutput($conc_row[curator_event]);?></td>
           <td  align="right"  valign="middle"><?php echo formatNumber($conc_row[curator_value]);?></td>
           
           <td   valign="middle">
           
           
            <?php 
			   
			  
		 	//yoes 20190113
		 	//see if have parent or child..
		     $parent_curator_id = getParentOfCurator($conc_row[curator_id]);
			 $child_curator_id = getChildOfCurator($conc_row[curator_id]);
			   
			   
			  ?>
           	
           	<?php if($parent_curator_id){
			   $parent_row = getFirstRow("select * from curator where curator_id = '".$parent_curator_id."'");	
			   ?>
           		ใช้สิทธิแทน <?php echo $parent_row[curator_idcard] . " : " . $parent_row[curator_name];?>	
           	<?php }?>
           	
           	<?php if($child_curator_id){
			   
			   $child_row = getFirstRow("select * from curator where curator_id = '".$child_curator_id."'");	
			   ?>
           		ใช้สิทธิแทนโดย <?php echo $child_row[curator_idcard] . " : " . $child_row[curator_name];?>	
           	<?php }?>
           	
           	
           	
           </td>
           
           
           <td   valign="middle"><?php 
		   
				echo formatCompanyName($conc_row["CompanyNameThai"],$conc_row["CompanyTypeCode"]);
				
			?></td>
            
            
            <td   valign="middle"><?php 
		   
				echo getAddressText($conc_row);
				
			?></td>

           
          </tr>
        
        
     <?php

		
		
	 }
	 
	 
	 ?>
     
     
     
    
      
    
	
        
        <?php
			if($_POST["report_format"] == "pdf"){
				//
			}else{
				$footer_row = 'style="border-bottom:double;"';
			}
		?>
        
      
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
