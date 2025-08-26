<select name="CompanyTypeCode">
	<option value="">-- select --</option>
	<?php
    
    
	
	if($sess_accesslevel == 6 || $sess_accesslevel == 7){

		//gov only	
		$get_orgtype_sql = "select *
				from companytype
				where
				CompanyTypeCode >= 200
				order by CompanyTypeName asc
				";
			
	}else{
		
		
		//company only	
		$get_orgtype_sql = "select *
				from companytype
				where
				CompanyTypeCode < 200
				order by CompanyTypeName asc
				";
		
	}
			
			
    //echo get_orgtype_sql;
    $orgtype_result = mysql_query($get_orgtype_sql);
    
    
    
    while ($orgtype_row = mysql_fetch_array($orgtype_result)) {
    
    
    ?>              
        <option <?php if($_POST["CompanyTypeCode"] == $orgtype_row["CompanyTypeCode"] || $output_values["CompanyTypeCode"] == $orgtype_row["CompanyTypeCode"]){echo "selected='selected'";}?> value="<?php echo $orgtype_row["CompanyTypeCode"];?>"><?php echo $orgtype_row["CompanyTypeName"];?></option>
    
    <?php
    }
    ?>
</select>