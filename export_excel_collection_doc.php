<?php
include "db_connect.php";

if(is_numeric($_GET["id"]) && (!is_null($_GET["cid"]))){
	$collection_id = $_GET["id"];
	$cids = preg_split("/[_]/", $_GET["cid"]);
	$cidFilter = implode(",", $cids);
	$post_row = getFirstRow("select c.* , l.Year,f.file_name from collectiondocument c
							inner join lawfulness l on c.LID = l.LID 
							left join files f on c.CollectionID = f.file_for
							where c.CollectionID  = '$collection_id' limit 0,1");
	
	$output_fields = array('CollectionID','Year','RequestDate','RequestNo','GovDocumentNo','DocumentDetail','file_name','Reciever','RecievedDate');
	
	for($i = 0; $i < count($output_fields); $i++){
		$output_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
	}
	
	$condition_sql = "c.GovDocumentNo = '".$output_values["GovDocumentNo"]."' 
					and c.RequestNo = ".$output_values["RequestNo"]." 
					and c.RequestDate = '".$output_values["RequestDate"]." 00:00:00' and l.Year = '".$output_values["Year"]."'";
	
}else{
	exit();
}

header("Content-type: application/ms-excel");
header("Content-Disposition: attachment; filename=collection_doc.xls");

?>

<style type="text/css">

	.style1 {color: #FFFFFF}

</style>

<table border="1" cellpadding="3" cellspacing="0" style="font: Verdana, Arial, Helvetica, sans-serif; font-size:12px;">
	<tr>
    	<td bgcolor="#003366"><span class="style1">ลำดับ</span></td>
		<td bgcolor="#003366"><span class="style1">รหัส</span></td>
		<td bgcolor="#003366"><span class="style1">ชื่อบริษัท (ภาษาไทย)</span></td>
		<td bgcolor="#003366"><span class="style1">สถานะ</span></td>
		<td bgcolor="#003366"><span class="style1">ครั้งที่</span></td>
		<td bgcolor="#003366"><span class="style1">หนังสือเลขที่</span></td>
	</tr>
	
	<?php
	$total_records = 0;
	
	$get_collecion_sql = "select c.* ,l.Year,l.LID,l.LawfulStatus as lawfulness_status ,com.CompanyCode,com.CompanyNameThai,com.CompanyTypeCode from collectiondocument c
	inner join lawfulness l on c.LID = l.LID
	left join company com on l.CID = com.CID
	where $condition_sql and l.CID in($cidFilter)
	order by com.CompanyNameThai asc";
	
	$collection_result = mysql_query($get_collecion_sql);
	
	while ($post_row_q = mysql_fetch_array($collection_result)) {
		$total_records++;
	?> 
	<tr>	
		<td><?php echo $total_records;?> </td>
		<td><?php echo doCleanOutput($post_row_q["CompanyCode"]);?> </td>
		<td><?php echo formatCompanyName(doCleanOutput($post_row_q["CompanyNameThai"]), ($post_row_q["CompanyTypeCode"]));?> </td>
		<td><?php echo getLawfulText($post_row_q["lawfulness_status"]);?> </td>
		<td><?php echo doCleanOutput($post_row_q["RequestNo"]);?> </td>
		<td>
			<?php 
			$str = "'".$post_row_q["GovDocumentNo"]."'";
			echo $str;
			?> 
		</td>
	</tr>
	<?php }	?>
</table>
