<select name="nep_user" id="nep_user" >
    <option value="">-- เลือก --</option>
    
  
    
    <?php
   
		$get_user_sql = "select *
            from users
			where 
			AccessLevel = 2
            order by user_name asc
            ";
	
    //all photos of this profile
    
  
    $user_result = mysql_query($get_user_sql);
    
    
    
    while ($user_row = mysql_fetch_array($user_result)) {
    
    
    ?>              
        <option <?php if($_POST["user_id"] == $user_row["user_id"] || $output_values["user_id"] == $user_row["user_id"]){echo "selected='selected'";}?> value="<?php echo $user_row["user_id"];?>"
        
        onchange="doUpdateUserZone(<?php echo $user_row["user_id"];?>, <?php echo $district_row[district_area_code];?>);"
        
        ><?php echo $user_row["user_name"] . " - " . $user_row["FirstName"] ." " .$user_row["LastName"] . " : " . $user_row["Department"];?></option>
    
    <?php
    }
    ?>
    
  
</select>