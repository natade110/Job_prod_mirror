<?php

include "db_connect.php";

//print_r($_POST);

if($_GET["report_format"] == "excel"){

	header("Content-type: application/ms-excel");
	header("Content-Disposition: attachment; filename=report_28.xls");
	
	$is_excel = 1;

}else{

	header ('Content-type: text/html; charset=utf-8');
}



$main_sql = "

				select
					*
					, b.Employees as lawful_employees
					, a.CID as the_cid
				from
					company a
					, lawfulness b
					, companytype c
					, provinces d
				where
					a.CID = b.CID
					and
					a.CompanyTypeCode = c.CompanyTypeCode
					and
					a.Province = d.province_id
					and
					b.Year = '2012'
					and
					a.CompanyTypeCode != '14'
					and
					b.Employees >= 100

			";
			
			
//echo $main_sql;			



?>

    
    <table border="1" align="center" cellpadding="5" cellspacing="0" <?php if(!$is_pdf){?>style="font-size:14px;"<?php }else{?>style="font-size:28px;"<?php }?>>
   	  <thead>
      							

      <tr >
        <td><div align="center" ><strong>CompanyTypeName</strong> </div></td>
        <td ><div align="center" ><strong>CompanyNameThai</strong> </div></td>
        <td><div align="center" ><strong>Address1</strong> </div></td>
        <td><div align="center" ><strong>Subdistrict</strong></div></td>
        <td><div align="center"><strong>District</strong></div></td>
        
        <td><div align="center"><strong>Zip</strong></div></td>
        <td><div align="center"><strong>province</strong></div></td>
        <td><div align="center"><strong>Telephone</strong></div></td>
        
        <td><div align="center"><strong>จำนวนลูกจ้าง</strong></div></td>
        <td><div align="center"><strong>อัตราส่วนลูกจ้างต่อคนพิการ</strong></div></td>
        <td><div align="center"><strong>จำนวนคนพิการที่ทำงานปัจจุบัน</strong></div></td>
        <td><div align="center"><strong>จำนวนคนพิการที่รับเพิ่ม</strong></div></td>
        
      </tr>
      </thead>
      
      <tbody>
      
      
      <?php
		  $lawful_result = mysql_query($main_sql);	
		  
		  while ($lawful_row = mysql_fetch_array($lawful_result)) {
		  
		  
		  	$current_employed = getFirstItem("
														SELECT 
															count(*)
														FROM 
															lawful_employees
														where
															le_cid = '".$lawful_row["the_cid"]."'
															and le_year = '2012'"); 
															
			$the_ratio = getEmployeeRatio($lawful_row["lawful_employees"],100);
			
			
			//
			$this_district = $lawful_row["District"];			
			$this_district = str_replace("อ.", "", $this_district);
			$this_district = str_replace("อำเภอ", "", $this_district);
			$this_district = str_replace("เขต", "", $this_district);
			
			$this_sub_district = $lawful_row["Subdistrict"];
			$this_sub_district = str_replace("ต.", "", $this_sub_district);
			$this_sub_district = str_replace("ตำบล", "", $this_sub_district);
			$this_sub_district = str_replace("แขวง", "", $this_sub_district);
		  
	  ?>
      
      <tr>					

        <td><?php echo $lawful_row["CompanyTypeName"]?></td>
        <td><?php echo $lawful_row["CompanyNameThai"]?></td>
        <td><?php echo $lawful_row["Address1"]?></td>
        
        <td><?php echo $this_sub_district;?></td>
        <td><?php echo $this_district;?></td>
        
        <td><?php echo $lawful_row["Zip"]?></td>
        <td><?php echo $lawful_row["province_name"]?></td>
        <td><?php echo $lawful_row["Telephone"]?></td>
        
        
        <td><?php echo $lawful_row["lawful_employees"]?></td>
        <td><?php echo $the_ratio?></td>
        <td><?php echo $current_employed;?></td>
        <td><?php echo $the_ratio - $current_employed;?></td>
      </tr>
      
      
      <?php }?>
	  </tbody>
        
        
        
        <tfoot>
      </tfoot>
</table>
