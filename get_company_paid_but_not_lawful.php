<table border="1">
  <tr bgcolor="#CCCCCC">
    <td>CompanyCode</td>
    <td>CompanyNameThai</td>
    <td>BranchCode</td>
  </tr>  

<?php

	include "db_connect.php";
	
	$get_org_sql = "
					
					
					SELECT 
					a.CID
					,a.CompanyCode
					, a.CompanyNameThai
					, a.BranchCode
					FROM company a, lawfulness b
					WHERE 
					a.CID = b.CID
					and Year = '2013'
					AND LawfulStatus = 2
					AND pay_status = 1
				
					";
	
	$org_result = mysql_query($get_org_sql);
	
	while ($post_row = mysql_fetch_array($org_result)) {
			
	?>

	<tr>
        <td><a href="organization.php?id=<?php echo $post_row["CID"];?>&focus=lawful&year=2013?" target="_blank"><?php echo $post_row["CompanyCode"];?></td>
        <td><?php echo $post_row["CompanyNameThai"];?></td>
        <td><?php echo $post_row["BranchCode"];?></td>
      </tr>

<?php    

							
	}	

?>
</table>