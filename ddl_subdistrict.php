<select name="Subdistrict_init" id="Subdistrict_init">
	
	<?php
    
	$get_subdistrict_sql = "
		
		select
			*
		from
			subdistrict
		where
			province_code = '$subdistrict_province_code'
			and
			district_code = '$subdistrict_district_code'
			and
			subdistrict_name != 'ข้อมูลไม่ถูกต้อง'
		order by 
			subdistrict_name 
		asc
	
	";
	
	
	
    //all photos of this profile
    
  
    $subdistrict_result = mysql_query($get_subdistrict_sql);
    
    
    
    while ($subdistrict_row = mysql_fetch_array($subdistrict_result)) {
    
	
		//yoes 20170214 -- custom something here
		if($output_values["Province"] == 1){			
			//$district_row["district_name"] = "เขต".$district_row["district_name"];
			$the_subdistrict = str_replace("แขวง","",$output_values["Subdistrict"]);
		}else{
			
			$the_subdistrict = str_replace("ตำบล","",$output_values["Subdistrict"]);
			$the_subdistrict = str_replace("ต.","",$output_values["Subdistrict"]);
		}
    
    ?>              
        <option <?php if($the_subdistrict == $subdistrict_row["subdistrict_name"]){echo "selected='selected'"; $subdistrict_selected = 1;}?> value="<?php echo $subdistrict_row["subdistrict_name"];?>" data-postcode="<?php echo $subdistrict_row['post_code'];?>"><?php echo $subdistrict_row["subdistrict_name"];?></option>
    
    <?php
    }
    ?>
    
    
    <?php if(!$subdistrict_selected){?>
    	
        <option selected='selected' value="<?php echo $output_values["Subdistrict"];?>"><?php echo $output_values["Subdistrict"];?></option>
    
    <?php }?>
    
</select>



<?php //echo $get_subdistrict_sql;?>
