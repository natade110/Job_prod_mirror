<?php
	
	include "db_connect.php";
	//include "functions.php";
	
	//print_r($_POST);
	
	if($_POST["year"]){
	
		$the_year = $_POST["year"];
		
		//first, read sso table based on inputted parameters
		$sql = "select * from companysso where DataYear = '$the_year' ";
		
		$result = mysql_query($sql);
		
		$inserted = 0;
		$updated = 0;
		
		while ($post_row = mysql_fetch_array($result)) {
		
		
			//see if insert or update
			//$get_count = getFirstItem("select count(CID) from company where CompanyCode = '".$post_row["CompanyCode"]."'");
			
			if($get_count > 0){
				$do_update = 1;
			}else{
				$do_update = 0;
			}
		
			//if($do_update == 1){
		
				$update_sql = "
						
						update company
							
							set
							
							CompanyTypeCode = '".$post_row["CompanyTypeCode"]."'
							
							,CompanyNameThai = '".$post_row["CompanyNameThai"]."'
							,Address1 = '".$post_row["Address1"]."'
							,Subdistrict = '".$post_row["Subdistrict"]."'
							,District = '".$post_row["District"]."'
							,Province = '".$post_row["Province"]."'
							
							,Zip = '".$post_row["Zip"]."'
							,Telephone = '".$post_row["Telephone"]."'
							,Employees = '".$post_row["Employees"]."'
							
							,BusinessTypeCode = '".$post_row["BusinessTypeCode"]."'
							
							,LastModifiedDateTime = NOW()
							
							where CompanyCode = '".$post_row["CompanyCode"]."'
							
							limit 1
					
						";
						
						//$updated++;
		
			//}else{
				//for each result, replace into company table
				$the_sql = "
				
							insert into company
							(
							
								CompanyCode
							
								,CompanyTypeCode
								
								,CompanyNameThai
								,Address1
								,Subdistrict
								,District
								,Province
								
								,Zip
								,Telephone
								,Employees
								
								,BusinessTypeCode
								
								,LastModifiedDateTime
								
							
							)values(
							
								'".$post_row["CompanyCode"]."'
							
								,'".$post_row["CompanyTypeCode"]."'
								
								,'".$post_row["CompanyNameThai"]."'
								,'".$post_row["Address1"]."'
								,'".$post_row["Subdistrict"]."'
								,'".$post_row["District"]."'
								,'".$post_row["Province"]."'
								
								,'".$post_row["Zip"]."'
								,'".$post_row["Telephone"]."'
								,'".$post_row["Employees"]."'
								
								,'".$post_row["BusinessTypeCode"]."'
								
								,NOW()
							
							);
							";
							
							//$inserted++;
							
			//}
					
			//echo $the_sql;	
			if(!mysql_query($the_sql)){
				
				//cant insert, try update
				if(!mysql_query($update_sql)){
					echo  "<br> error for CID <b>".$post_row["CompanyCode"]."</b>:" . mysql_error();
				}else{
					//can update
					$updated++;
				}
				
			}else{
				//can insert
				$inserted++;
			}
				
		}
		
		
		echo "<br>data of year $the_year imported! -> $inserted rows inserted, $updated rows updated";
	}
	
	
?>
<form action="" method="post">

Import data of year <select name="year">
<option value="2011">2011</option>
<option value="2010">2010</option>
</select> from SSO table

<input name="" type="submit" value="import" />

</form>
