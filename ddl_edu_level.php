<select name="le_education" id="le_education" onchange="checkEduList();">
	
	<?php
	
	
		$get_edu_sql = "select *
            from education_level
            order by edu_id asc
            ";
		
	
    //all photos of this profile
    
  
    $edu_result = mysql_query($get_edu_sql);
    
    
    
    while ($edu_row = mysql_fetch_array($edu_result)) {
    
    
    ?>              
        <option <?php if($_POST["le_education"] == $edu_row["edu_id"] || $leid_row["le_education"] == $edu_row["edu_id"]){echo "selected='selected'";}?> value="<?php echo $edu_row["edu_id"];?>"><?php echo $edu_row["edu_name"];?></option>
    
    <?php
    }
    ?>
</select>
