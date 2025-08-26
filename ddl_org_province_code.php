<?php 
	$ddl_province_id = (isset($ddl_selector_name))? $ddl_selector_name : "ProvinceCode";
?>


<select name="<?php echo $ddl_province_id ?>" id="<?php echo $ddl_province_id ?>">
	
	<?php
    
	
		$get_province_sql = "select *
            from provinces
            order by province_name asc
            ";
			
		echo '<option value="">-- select --</option>';
	
    //all photos of this profile
    
  
    $province_result = mysql_query($get_province_sql);
    
    
    
    while ($province_row = mysql_fetch_array($province_result)) {
    
    
    ?>              
        <option <?php if($_POST["Province"] == $province_row["province_id"] || $output_values["Province"] == $province_row["province_id"] || $output_values["user_meta"] == $province_row["province_id"]){echo "selected='selected'";}?> value="<?php echo $province_row["province_code"];?>"><?php echo $province_row["province_name"];?></option>
    
    <?php
    }
    ?>
</select>
