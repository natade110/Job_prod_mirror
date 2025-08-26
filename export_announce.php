<?php

include "db_connect.php";

if(is_numeric($_GET["id"])){	
	$this_id = $_GET["id"];
}else{
	exit();
}

header("Content-type: application/ms-excel");
header("Content-Disposition: attachment; filename=announcement_$this_id.xls");

?>
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
-->
</style>


<table border="1" cellpadding="3" cellspacing="0" style="font: Verdana, Arial, Helvetica, sans-serif; font-size:12px;">
	<tr>
    	<td bgcolor="#003366"><span class="style1">ลำดับที่</span></td>
		<td bgcolor="#003366"><span class="style1">ชื่อสถานประกอบการ</span></td>
		<td bgcolor="#003366"><span class="style1">ประเภทกิจการ</span></td>
		<td bgcolor="#003366"><span class="style1">จังหวัด</span></td>
        
        
        
        
	</tr>
<?php

	

	$get_org_sql = "SELECT *, b.CID as companyid
				FROM announcecomp a, company b, announcement c
				where 
				a.CID = b.CID
				and
				a.AID = c.AID
				and 
				c.AID ='$this_id'
				
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
		
		<td><?php echo formatCompanyName(doCleanOutput($post_row["CompanyNameThai"]), ($post_row["CompanyTypeCode"]));?> </td>
		<td><?php echo getFirstItem("select BusinessTypeName from businesstype where BusinessTypeCode ='".$post_row["BusinessTypeCode"]."' ");?> </td>
		
        <td><?php echo getFirstItem("select province_name from provinces where province_id ='".$post_row["Province"]."' ");?> </td>
       
	</tr>
<?php
}	
?></table>