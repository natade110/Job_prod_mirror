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
	
	$typecode_filter = " and (a.CompanyTypeCode = '14'";
	$typecode_filter .= " or a.CompanyTypeCode >= 200  or a.CompanyTypeCode < 300)";
	
}else{
	$typecode_filter = " and a.CompanyTypeCode != '14'";
	$typecode_filter .= " and a.CompanyTypeCode < 200";
	
}


//yoes 20221222
if($_POST["chk_non_ratio"]){
	
	$non_ratio_filter = " ";
	
}else{
	
	$non_ratio_filter = " and b.lawfulStatus != 3 ";		
}


?>

<div align="center">
            <strong>สรุปกิจกรรมตามมาตรา 35 <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?></strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="5" align="center" valign="top"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
        </tr>
      <tr >
        <td width="50" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="365" align="center" valign="middle"><div align="center" style="vertical-align:middle;"><strong>ประเภทความพิการ</strong> </div></td>
        <td width="120" align="center" valign="top" ><div align="center"><strong>คนพิการใช้สิทธิฯ</strong></div></td>
		<td width="120" align="center" valign="top" ><div align="center"><strong>ผู้ดูแลใช้สิทธิฯ</strong></div></td>
		<td width="120" align="center" valign="top" ><div align="center"><strong>รวม</strong></div></td>
		
        </tr>
      </thead>
      
      <?php
	  
	  
	  	if($the_year >= 2013){
	  
	  		$distype_array =    array(
									"การให้สัมปทาน"
									,"จัดสถานที่จำหน่ายสินค้าหรือบริการ"
									,"จัดจ้างเหมาช่วงงาน"
									,"ฝึกงาน"
									,"การจัดให้มีอุปกรณ์หรือสิ่งอำนวยความสะดวก"
									,"การจัดให้มีบริการล่ามภาษามือ"
									,"การให้ความช่วยเหลืออื่นใด"
									,"-ไม่ได้ระบุกิจกรรม-"
									);
									
		}else{
		
			$distype_array =    array(
									"การให้สัมปทาน"
									,"จัดสถานที่จำหน่ายสินค้าหรือบริการ"
									,"จัดจ้างเหมาช่วงงาน"
									,"ฝึกงาน"
									,"การจัดให้มีอุปกรณ์หรือสิ่งอำนวยความสะดวก"
									,"การจัดให้มีบริการล่ามภาษามือ"
									,"การให้ความช่วยเหลืออื่นใด"
									,"-ไม่ได้ระบุกิจกรรม-"
									);
		
		}
	  
		
							
	  ?>
      
      <tbody>
      <?php 
	  
	  for($i=0;$i<count($distype_array);$i++){
	  
	  
	  	if($i == count($distype_array)-1){
						
			
			$desc_filter = "and (1 = 1 ";
			
			//yoes 20221222
			$desc_filter_user = "and (1 = 1 ";
			
			for($mm=0;$mm<count($distype_array);$mm++){
				
				$desc_filter .=  " and curator_event != '".$distype_array[$mm]."' ";
				
				$desc_filter_user .=  " and aa.curator_event != '".$distype_array[$mm]."' ";
				
			}
			
			$desc_filter .=  " or(						
									curator_event = '' 
									or curator_event is null
								)";
								
			$desc_filter .=  " )";
			
			
			$desc_filter_user .=  " or(						
									aa.curator_event = '' 
									or aa.curator_event is null
								)";
								
			$desc_filter_user .=  " )";
			
			
					
						
					
		}else{
			$desc_filter = "and curator_event = '".$distype_array[$i]."'";	
			
			//yoes 20221222
			$desc_filter_user = "and aa.curator_event = '".$distype_array[$i]."'";	
		}
		
		if($the_year >= 2018 && $the_year <= 2050 ){ //&& 1==0
			
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
	  
	  	$count_employee_sql = " 
						
		select
			count(curator_id)
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
				
				where curator_parent = 0		
				
				
				and
				curator_is_disable = 1
							
							
							
							$typecode_filter
							
							$province_filter
							
							$branch_codition
							
							$desc_filter
							
							$new_law_35_where_condition
							
							
							$non_ratio_filter
							";
							
		
							
		$this_count_employee = default_value(getFirstItem($count_employee_sql),0);
		$sum_employee += $this_count_employee;
		
		
		//yoes 20221222
		//count curator_user
		$count_user_sql = " 
						
		select
			count(aa.curator_id)
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
				
				join
					curator z
						on
						z.curator_parent = aa.curator_id
				
			where
			
				aa.curator_parent = 0						
				
				and
				aa.curator_is_disable = 0
							
							
							
							$typecode_filter
							
							$province_filter
							
							$branch_codition
							
							$desc_filter_user
							
							$new_law_35_where_condition
							
							$non_ratio_filter
							";
		
		
		//echo "<br>".$count_user_sql;
		
		$this_count_user = default_value(getFirstItem($count_user_sql),0);
		$sum_user += $this_count_user;
		
	  
	  
	  ?>
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $i+1;?></div></td>
        <td width="365" valign="top"><div align="left"><?php echo $distype_array[$i];?>
</div>          </td>
        
		<td width="120" align="right" valign="top"><div align="right"><?php echo formatEmployeeReport($this_count_employee);?> </div></td>
		<td width="120" align="right" valign="top"><div align="right"><?php echo formatEmployeeReport($this_count_user);?></div></td>
		<td width="120" align="right" valign="top"><div align="right"><?php echo formatEmployeeReport($this_count_employee+$this_count_user);?></div></td>
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
		<td width="120" align="right" <?php echo $footer_row?> ><div align="right"><strong><?php echo formatEmployeeReport($sum_user);?></strong> </div></td>
		<td width="120" align="right" <?php echo $footer_row?> ><div align="right"><strong><?php echo formatEmployeeReport($sum_employee+$sum_user);?></strong> </div></td>
        </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
