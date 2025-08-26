<select name="school_charity" id="school_charity">
	
    <option value="ไม่ระบุ" <?php if($output_values["school_charity"] == "ไม่ระบุ"){echo "selected='selected'";}?>>-- ไม่ระบุ ---</option>
    
    
    <?php for($schi = 3; $schi <= 7; $schi++){?>
	    <option value="กศ<?php echo $schi;?>" <?php if($output_values["school_charity"] == "กศ".$schi){echo "selected='selected'";}?>>กศ<?php echo $schi;?></option>
    <?php }?>

	
</select>