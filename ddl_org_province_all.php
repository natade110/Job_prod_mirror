<select name="Province" id="Province">
	
	<?php
    
	//yoes 20160908 - now province see all
	
	$get_province_sql = "select *
		from provinces
		order by province_name asc
		";
		
	echo '<option value="">-- ทั่วประเทศ --</option>';
	
	
    //all photos of this profile   
  
    $province_result = mysql_query($get_province_sql);
    
    
    
    while ($province_row = mysql_fetch_array($province_result)) {
    
    
    ?>              
        <option <?php if($_POST["Province"] == $province_row["province_id"] || $output_values["Province"] == $province_row["province_id"] || $output_values["user_meta"] == $province_row["province_id"]){echo "selected='selected'";}?> value="<?php echo $province_row["province_id"];?>"><?php echo $province_row["province_name"];?></option>
    
    <?php
    }
    ?>
</select>
