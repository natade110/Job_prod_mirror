<?php //echo $output_values["Province"];?>

<select name="District_init" id="District_init" onchange="doDistrictChange($('#province_code').val());">
	
	<?php
    
	$get_district_sql = "
		
		select
			*
		from
			districts
		where
			province_code = '$district_province_code'
			and
			district_name != 'ข้อมูลไม่ถูกต้อง'
		order by 
			district_name 
		asc
	
	";
	
	
	
    //all photos of this profile
    
  
    $district_result = mysql_query($get_district_sql);
    
    
    
    while ($district_row = mysql_fetch_array($district_result)) {
    
	
		//yoes 20170214 -- custom something here
		if($output_values["Province"] == 1){			
			//$district_row["district_name"] = "เขต".$district_row["district_name"];
			$output_values["District"] = str_replace("เขต","",$output_values["District"]);
		}else{
			
			if($output_values["District"] == "อ.เมือง"){
				$the_district = "เมือง".$district_province_name;
			}else{			
				$the_district = str_replace("อำเภอ.","",$output_values["District"]);	
				$the_district = str_replace("อ.","",$output_values["District"]);	
			}
		}
    
    ?>              
        <option <?php if($the_district == $district_row["district_name"]){echo "selected='selected'"; $district_selected = 1;}?> value="<?php echo $district_row["district_name"];?>"><?php echo $district_row["district_name"];?></option>
    
    <?php
    }
    ?>
    
    <?php if(!$district_selected){?>
    	
        <option selected='selected' value="<?php echo $output_values["District"];?>"><?php echo $output_values["District"];?></option>
    
    <?php }?>
    
</select>

<?php //echo $get_district_sql;?>
