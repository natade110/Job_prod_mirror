<select name="school_type" id="school_type">
	
    <option value="ไม่ระบุ" <?php if($output_values["school_type"] == "ไม่ระบุ"){echo "selected='selected'";}?>>-- ไม่ระบุ ---</option>
    <option value="สามัญ" <?php if($output_values["school_type"] == "สามัญ"){echo "selected='selected'";}?>>สามัญ</option>
    <option value="อาชีวะ" <?php if($output_values["school_type"] == "อาชีวะ"){echo "selected='selected'";}?>>อาชีวะ</option>
	
</select>