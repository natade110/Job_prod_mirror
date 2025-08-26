<?php

include "db_connect.php";

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

	header ('Content-type: text/html; charset=utf-8');
}

$the_year = "2011";

if(isset($_POST["ddl_year"])){
	$the_year = $_POST["ddl_year"];
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
	
	$typecode_filter = " and CompanyTypeCode = '14'";
	$business_type = "หน่วยงานภาครัฐ";
		
}else{
	$typecode_filter = " and CompanyTypeCode != '14'";
	$business_type = "สถานประกอบการ";
}

?>

<div align="center">
            <strong>สรุปประเภทความพิการที่ทำงานอยู่ในสถานประกอบการและหน่วยงานของรัฐ  <?php echo $province_text;?> ประจำปี <?php echo $the_year_to_use;?><br />สำนักงานส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการแห่งชาติ</strong>
              <br>
</div>
    
    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      <tr>
        <td colspan="4"><div align="center"><strong><?php echo $business_type;?></strong></div></td>
      </tr>
      <tr >
        <td valign="top" width="50"><br /><div align="center" style="vertical-align:middle;"><strong>ลำดับที่ </strong></div></td>
        <td width="365" valign="top"><br /><div align="center" style="vertical-align:middle;"><strong>ประเภทความพิการ</strong> </div></td>
        <td width="120" ><div align="center"><strong>ทำงานใน<br />
          สถานประกอบการ<br />
(ราย)</strong></div></td>
        <td width="120" ><div align="center"><strong>ทำงานใน<br />
         หน่วยงานภาครัฐ
<br />
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
									,"ความพิการทางการเรียนรู้"
									,"ความพิการทางออทิสติก"
									);
									
		}else{
		
			$distype_array =    array(
									"ความพิการทางการเห็น"
									,"ความพิการทางการได้ยินหรือสื่อความหมาย"
									,"ความพิการทางการเคลื่อนไหวหรือร่างกาย"
									,"ความพิการทางจิตใจหรือพฤติกรรม หรือออทิสติก"
									,"ความพิการทางสติปัญญา"
									,"ความพิการทางการเีรียนรู้"
									);
		
		}
	  
		
							
	  ?>
      
      <tbody>
      <?php 
	  
	  for($i=0;$i<count($distype_array);$i++){
	  
	  	$count_employee_sql = "select 
						
							count(le_id)
						
						from 
							company
							, lawful_employees
							$province_table
						where 
							company.CID = le_cid
							and CompanyTypeCode != '14'
							$province_filter
							and le_year = '$the_year'
							
							
							and le_disable_desc = '".$distype_array[$i]."'
							";
							
		$this_count_employee = default_value(getFirstItem($count_employee_sql),0);
		$sum_employee += $this_count_employee;
		
		
		$count_employee_2_sql = "select 
						
							count(le_id)
						
						from 
							company
							, lawful_employees
							$province_table
						where 
							company.CID = le_cid
							and CompanyTypeCode = '14'
							$province_filter
							and le_year = '$the_year'
							and le_disable_desc = '".$distype_array[$i]."'
							";
							
		$this_count_employee_2 = default_value(getFirstItem($count_employee_2_sql),0);
		$sum_employee_2 += $this_count_employee_2;
	  
	  
	  ?>
      <tr>
        <td width="50" valign="top"><div align="center"><?php echo $i+1;?></div></td>
        <td width="365" valign="top"><div align="left"><?php echo $distype_array[$i];?>
</div>          </td>
        <td width="120" valign="top"><div align="right"><?php echo formatEmployeeReport($this_count_employee);?> </div></td>
        <td width="120" valign="top"><div align="right"><?php echo formatEmployeeReport($this_count_employee_2);?> </div></td>
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
        <td colspan="2" ><div align="right"><strong>รวมทั้งสิ้น</strong></div></td>
        <td width="120" <?php echo $footer_row?> ><div align="right"><strong><?php echo formatEmployeeReport($sum_employee);?></strong> </div></td>
        <td width="120" <?php echo $footer_row?> ><div align="right"><strong><?php echo formatEmployeeReport($sum_employee_2);?></strong> </div></td>
      </tr>
      </tfoot>
</table>
    
    
    
<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?>    </div>
