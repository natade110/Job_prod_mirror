<?php

include "db_connect.php";

if(is_numeric($_GET["id"])){	
	$this_id = $_GET["id"];
}else{
	exit();
}

header("Content-type: application/ms-excel");
header("Content-Disposition: attachment; filename=outgoing_letters_$this_id.xls");

?>
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
-->
</style>


<table border="1" cellpadding="3" cellspacing="0" style="font: Verdana, Arial, Helvetica, sans-serif; font-size:12px;">
	<tr>
    	<td bgcolor="#003366"><span class="style1">ลำดับที่</span></td>
		<td bgcolor="#003366"><span class="style1">เลขที่บัญชีนายจ้าง</span></td>
		<td bgcolor="#003366"><span class="style1">ชื่อบริษัท (ภาษาไทย)</span></td>
		<td bgcolor="#003366"><span class="style1">วันที่</span></td>
		<td bgcolor="#003366"><span class="style1">ครั้งที่</span></td>
		<td bgcolor="#003366"><span class="style1">หนังสือเลขที่</span></td>
        
        <td bgcolor="#003366"><span class="style1">สถานที่ตั้งเลขที</span></td>
        <td bgcolor="#003366"><span class="style1">ซอย</span></td>
        <td bgcolor="#003366"><span class="style1">หมู่</span></td>
        <td bgcolor="#003366"><span class="style1">ถนน</span></td>
        <td bgcolor="#003366"><span class="style1">ตำบล/แขวง</span></td>
        <td bgcolor="#003366"><span class="style1">อำเภอ/เขต</span></td>
        <td bgcolor="#003366"><span class="style1">จังหวัด</span></td>
        <td bgcolor="#003366"><span class="style1">รหัสไปรษณีย์</span></td>
        <td bgcolor="#003366"><span class="style1">เลขที่ลงทะเบียน</span></td>
        
        
        
        
	</tr>
<?php

	

	$get_org_sql = "SELECT *, b.CID as companyid
				FROM docrequestcompany a, company b, documentrequest c
				where 
				a.CID = b.CID
				and
				a.RID = c.RID
				and 
				a.RID ='$this_id'
				
				";
	//echo $get_org_sql;
	$org_result = mysql_query($get_org_sql);
	
	//total records 
	$total_records = 0;
	
	while ($post_row = mysql_fetch_array($org_result)) {
		$total_records++;
?>    
<tr>	
		<td><?php echo $total_records;?> </td>
		<td><?php echo doCleanOutput($post_row["CompanyCode"]);?> </td>
		<td><?php echo formatCompanyName(doCleanOutput($post_row["CompanyNameThai"]), ($post_row["CompanyTypeCode"]));?> </td>
		<td><?php echo formatInputDate($post_row["RequestDate"]);?> </td>
		<td><?php echo doCleanOutput($post_row["RequestNum"]);?> </td>
		<td><?php echo doCleanOutput($post_row["GovDocumentNo"]);?> </td>
        
        <td><?php echo doCleanOutput($post_row["Address1"]);?> </td>
        <td><?php echo doCleanOutput($post_row["Soi"]);?> </td>
        <td><?php echo doCleanOutput($post_row["Moo"]);?> </td>
        <td><?php echo doCleanOutput($post_row["Road"]);?> </td>
        
        
        <?php 
		
		
		//yoes 20170321 ---> change this function so it handles District/Subdistrict correctly
		$subdistrict_to_use = $post_row["Subdistrict"];
		
		if($post_row["Province"] == 1){
		
			//if(mb_substr($subdistrict_to_use,0,4,"utf-8") != "แขวง"){
			if(strpos($subdistrict_to_use, 'แขวง') === false){
				
				$subdistrict_to_use = "แขวง" . $subdistrict_to_use;
				
			}
			
		}else{
			
			//echo substr($subdistrict_to_use,0,5);
		
			//if(mb_substr($subdistrict_to_use,0,2,"utf-8") != "ต." && mb_substr($subdistrict_to_use,0,4,"utf-8") != "ตำบล"){
			if(strpos($subdistrict_to_use, 'ต.') === false && strpos($subdistrict_to_use, 'ตำบล') === false){
				
				$subdistrict_to_use = "ต." . $subdistrict_to_use;
				
			}	
			
		}
		
		$district_to_use = $post_row["District"];
		
		if($post_row["Province"] == 1){
		
			//if(mb_substr($district_to_use,0,3,"utf-8") != "เขต"){
			if(strpos($district_to_use, 'เขต') === false){
				
				$district_to_use = "เขต" . $district_to_use;
				
			}
			
		}else{
			
			//echo mb_substr($district_to_use,0,2,"utf-8");
		
			//if(mb_substr($district_to_use,0,2,"utf-8") != "อ." && mb_substr($district_to_use,0,5,"utf-8") != "อำเภอ"){
			if(strpos($district_to_use, 'อ.') === false && strpos($district_to_use, 'อำเภอ') === false){
				
				$district_to_use = "อ." . $district_to_use;
				
			}	
			
		}
		
		
		?>
        
        
        <td><?php echo doCleanOutput($subdistrict_to_use);?> </td>
        <td><?php echo doCleanOutput($district_to_use);?> </td>
        
        
        <td><?php echo getFirstItem("select province_name from provinces where province_id ='".$post_row["Province"]."' ");?> </td>
        <td><?php echo doCleanOutput($post_row["Zip"]);?> </td>
        <td><?php echo doCleanOutput($post_row["PostRegNum"]);?> </td>
	</tr>
<?php
}	
?></table>