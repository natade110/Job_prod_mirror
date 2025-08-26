 
    <?php 
	
		//yoes 20160215
		//sepcial case for พมจ
		if($sess_accesslevel == 3){
			
			//first - only see own province
			$ddl_zone_province_code = getFirstItem("select province_code from provinces where province_id = '$sess_meta'"); 			
			$ddl_zone_condition .= " and zone_province_code = '$ddl_zone_province_code'";
			
			
			//second - if got own zone -> then can choose to see own zone
			$ddl_zone_have_zone = getFirstItem("select zone_id where user_id = '$sess_userid'");
			
			if($ddl_zone_have_zone){
				$ddl_zone_condition .= " and zone_id in (
				
											select zone_id where user_id = '$sess_userid'
										
										)";
			}
			
		}
	
	?>
    

<select name="zone_id" id="zone_id">
                                                        
   
                                   
    <option value="">-- ไม่ระบุ --</option>
    <?php
        
     $get_zone_sql = "
            select 
                *
            from 
                zones
			where
				1=1
				$ddl_zone_condition
            
            order by 
                zone_name asc
            ";                                          
    
  
    $zone_result = mysql_query($get_zone_sql);                                        
    
    while ($zone_row = mysql_fetch_array($zone_result)) {                                        
    
    ?>              
        <option <?php if($my_zone == $zone_row["zone_id"]){echo "selected='selected'";}?> value="<?php echo $zone_row["zone_id"];?>"><?php echo $zone_row["zone_name"];?></option>
    
    <?php
    }
    ?>
</select>
