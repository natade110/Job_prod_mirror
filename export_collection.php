<?php

include "db_connect.php";

if(is_numeric($_GET["id"])){	
	$this_id = $_GET["id"];
}else{
	exit();
}

header("Content-type: application/ms-excel");
header("Content-Disposition: attachment; filename=collection_letters_$this_id.xls");

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
				FROM collectioncompany a, company b, collectiondocument c
				where 
				a.CID = b.CID
				and
				a.CollectionID = c.CollectionID
				and 
				a.CollectionID ='$this_id'
				
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
		<td><?php echo doCleanOutput($post_row["RequestNo"]);?> </td>
		<td><?php echo doCleanOutput($post_row["GovDocumentNo"]);?> </td>
        
        <td><?php echo doCleanOutput($post_row["Address1"]);?> </td>
        <td><?php echo doCleanOutput($post_row["Soi"]);?> </td>
        <td><?php echo doCleanOutput($post_row["Moo"]);?> </td>
        <td><?php echo doCleanOutput($post_row["Road"]);?> </td>
        <td><?php echo doCleanOutput($post_row["Subdistrict"]);?> </td>
        <td><?php echo doCleanOutput($post_row["District"]);?> </td>
        <td><?php echo getFirstItem("select province_name from provinces where province_id ='".$post_row["Province"]."' ");?> </td>
        <td><?php echo doCleanOutput($post_row["Zip"]);?> </td>
        <td><?php echo doCleanOutput($post_row["ReceiverNo"]);?> </td>
	</tr>
<?php
}	
?></table>