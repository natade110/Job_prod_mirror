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
	$branch_codition =  " AND BranchCode < 1 ";

}


if($_POST[chk_non_ratio]){
	$non_ratio_filter = " ,3";
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

if($_POST["CompanyTypeCode"] == "14"){
	
	//$typecode_filter = " and CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	//$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
}

///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){
	
	$typecode_filter .= " and CompanyTypeCode >= 200  and CompanyTypeCode < 300";
	$business_type = "หน่วยงานภาครัฐ";
	$business_code = "เลขทะเบียนหน่วยงาน";
	
	if($_POST["CompanyTypeCode"]){
		
		$typecode_filter .= " and CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'";
		$the_company_word = getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '".doCleanInput($_POST["CompanyTypeCode"])."'");
			
	}
	
}else{
	
	$typecode_filter .= " and CompanyTypeCode < 200 ";
	//$business_code = "เลขทะเบียนหน่วยงาน";
	
}

?>

<div align="center">
            <strong>สรุปประเภทความพิการที่ทำงานอยู่ใน<?php echo $the_company_word;?> <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?>
			
			<?php if($_POST[chk_non_ratio]){ ?>
			
				<br>*รวมข้อมูลจากสถานประกอบการที่ไม่เข้าข่ายลูกจ้าง*
			
			<?php }?>
			
			</strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="3" align="center" valign="top"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
        </tr>
      <tr >
        <td width="50" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="365" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ประเภทความพิการ</strong> </div></td>
        <td width="120" align="center" valign="top" ><div align="center"><strong>ทำงานใน<br />
          <?php echo $business_type;?><br />
(ราย)</strong></div></td>
        </tr>
      </thead>
      
      <?php
	  
	  
	  	if($the_year >= 2013){
			
			
	  		$distype_array =    array(
									"ความพิการทางการเห็น"
									,"ความพิการทางการได้ยินหรือสื่อความหมาย"
									,"ความพิการทางการเคลื่อนไหวหรือร่างกาย"
									,"ความพิการทางจิตใจหรือพฤติกรรม"
									,"ความพิการทางสติปัญญา"
									,"ความพิการทางการเีรียนรู้"
									,"ความพิการทางออทิสติก"
									,"ความพิการซ้ำซ้อน"
									,"-ไม่ได้ระบุความพิการ-"
									);
									
		}else{
		
			$distype_array =    array(
									"ความพิการทางการเห็น"
									,"ความพิการทางการได้ยินหรือสื่อความหมาย"
									,"ความพิการทางการเคลื่อนไหวหรือร่างกาย"
									,"ความพิการทางจิตใจหรือพฤติกรรม หรือออทิสติก"
									,"ความพิการทางสติปัญญา"
									,"ความพิการทางการเรียนรู้"
									,"-ไม่ได้ระบุความพิการ-"
									);
		
		}
	  
		
							
	  ?>
      
      <tbody>
      <?php 
	  
	  for($i=0;$i<count($distype_array);$i++){
	  
	  
	  	if($i == count($distype_array)-1){
			
			
			$desc_filter = "and (1 = 1 ";
			
			for($mm=0;$mm<count($distype_array);$mm++){
				
				$desc_filter .=  " and le_disable_desc != '".$distype_array[$mm]."' ";
				
			}
			
			$desc_filter .=  " or(						
									le_disable_desc = '' 
									or le_disable_desc is null
								)";
								
			$desc_filter .=  " )";
			
			
					
						
					
		}else{
			$desc_filter = "and le_disable_desc = '".$distype_array[$i]."'";	
			
			/*if($distype_array[$i] == "ความพิการทางการเรียนรู้"){
					$desc_filter = "and (le_disable_desc = '".$distype_array[$i]."' or le_disable_desc = 'ความพิการทางการเีรียนรู้')";	
			}*/
		}
		
		if($the_year >= 2018 && $the_year <= 2050 ){
			
			$extra_sql = "
			
					and
						
						le_id not in (
						
							select
								meta_value
							from
								lawful_employees_meta
							where
								meta_for = 'child_of$es_field_name_suffix'
								
						
						)
						
					and
						le_id not in (
						
							select
								meta_leid
							from
								lawful_employees_meta
							where
								meta_for = 'is_extra_33$es_field_name_suffix'
								and
								meta_value = 1
						
						)
			
			";
			
		}
	  
	  	$count_employee_sql = "select 
						
							count(le_id)
						
						from 
							company
							
								join lawfulness c
								on
								c.cid = company.cid
								and
								c.year = '$the_year'
								
							, lawful_employees
							
							
							
							$province_table
						where 
							company.CID = le_cid
							and le_year = '$the_year'
							
							
							$typecode_filter
							
							$province_filter
							
							$branch_codition
							
							$desc_filter
							
							$extra_sql
							
							and
							LawfulStatus in (
								
								0
								, 1
								, 2
								
								$non_ratio_filter
								
							)
							
							";
							
		
							
		$this_count_employee = default_value(getFirstItem($count_employee_sql),0);
		$sum_employee += $this_count_employee;
		
		
		//echo "<br><br> $count_employee_sql";
		
		
		//yoes 20200513 -- no longer use these ... ?
		/*
		$count_employee_2_sql = "select 
						
							count(le_id)
						
						from 
							company
							, lawful_employees
							$province_table
						where 
							company.CID = le_cid
							
							$typecode_filter
							
							$province_filter
							and le_year = '$the_year'
							
							$desc_filter
							
							";
							
		$this_count_employee_2 = default_value(getFirstItem($count_employee_2_sql),0);
		$sum_employee_2 += $this_count_employee_2;
		*/
	  
	  
	  ?>
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $i+1;?></div></td>
        <td width="365" valign="top"><div align="left"><?php echo $distype_array[$i];?>
</div>          </td>
        <td width="120" align="right" valign="top"><div align="right"><?php echo formatEmployeeReport($this_count_employee);?> </div></td>
        </tr>
     <?php } ?>
      
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
        <td colspan="2" align="right" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="120" align="right" <?php echo $footer_row?> ><div align="right"><strong><?php echo formatEmployeeReport($sum_employee);?></strong> </div></td>
        </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
