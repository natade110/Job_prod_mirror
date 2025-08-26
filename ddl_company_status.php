<select name="Status" id="bankruptSelect">
	<option value="" >-- select --</option>
	<?php
    
    $values_array = array(
					"0"
					,"1"
					,"2"
					,"3"
					);
    
    $caption_array = array(
					"ปิดกิจการ"
					,"เปิด"
					,"ย้าย"
					,"ล้มละลาย"
					);
    
    
    
    for($i=0; $i<count($caption_array);$i++){
    
    ?>              
        <option <?php if($_POST["Status"] == $values_array[$i] || $output_values["Status"] == $values_array[$i]){echo "selected='selected'";}?> value="<?php echo $values_array[$i];?>"><?php echo $caption_array[$i];?></option>
    
    <?php
    }
    ?>
</select>

		 
