<select name="Section" id="Section">
	
	<?php
    
	
	$get_Section_sql = "select *
		from province_section
		order by section_id asc
		";
		
	echo '<option value="">-- ทุกภาค--</option>';

	
    //all photos of this profile
    
    //echo get_orgtype_sql;
    $Section_result = mysql_query($get_Section_sql);
    
    
    
    while ($Section_row = mysql_fetch_array($Section_result)) {
    
    
    ?>              
        <option <?php if($_POST["Section"] == $Section_row["section_id"] || $output_values["Section"] == $Section_row["section_id"] || $output_values["user_meta"] == $Section_row["section_id"]){echo "selected='selected'";}?> value="<?php echo $Section_row["section_id"];?>"><?php echo $Section_row["section_name"];?></option>
    
    <?php
    }
    ?>
</select>