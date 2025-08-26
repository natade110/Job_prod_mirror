<?php
	
	include "db_connect.php";
	//include "functions.php";
	
	//print_r($_POST);
	
	if($_POST["year"]){
	
		$the_year = $_POST["year"];
		
		//first, read sso table based on inputted parameters
		$sql = "select * from company ";
		
		$result = mysql_query($sql);
		
		$inserted = 0;
		$updated = 0;
		
		while ($post_row = mysql_fetch_array($result)) {
		
			//see if company already have lawful ness
			if(getFirstItem("select count(*) from lawfulness 
								where 
								Year = '2011'
								and CID = '".$post_row["CID"]."'
								
								") > 0){
				continue;								
			}
			
			
			$the_sql = "
			
				insert into lawfulness
				(
				
					Year
					,CID
					,LawfulStatus
				
				
				)values(
				
					'2011'
					,'".$post_row["CID"]."'
					,'3'
				
				);
				";
			mysql_query($the_sql);
			
				
		}
		
		
		echo "<br>data imported";
	}
	
	
?>
<form action="" method="post">

Import data of year <select name="year">
<option value="2011">2011</option>
<option value="2010">2010</option>
</select> from SSO table

<input name="" type="submit" value="import" />

</form>
