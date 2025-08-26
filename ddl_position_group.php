<select name="le_position" id="le_position" onchange="checkPositionList();">
	
	<?php
	
	
		$get_position_sql = "select *
            from position_group
            order by group_name asc
            ";
		
	
    //all photos of this profile
    
  
    $position_result = mysql_query($get_position_sql);
    
    
    
    while ($position_row = mysql_fetch_array($position_result)) {
    
    
    ?>              
        <option <?php if($_POST["le_position"] == $position_row["group_id"] || $leid_row["le_position"] == $position_row["group_id"]){echo "selected='selected'";}?> value="<?php echo $position_row["group_id"];?>"><?php echo $position_row["group_name"];?></option>
    
    <?php
    }
    ?>

    <?php if($sess_accesslevel == "6" || $sess_accesslevel == "7"){ //yoes 20250129?>
        <option <?php if($_POST["le_position"] == "ข้าราชการ"){echo "selected='selected'";}?> value="ข้าราชการ">ข้าราชการ</option>
        <option <?php if($_POST["le_position"] == "พนักงานราชการ"){echo "selected='selected'";}?> value="พนักงานราชการ">พนักงานราชการ</option>
        <option <?php if($_POST["le_position"] == "ลูกจ้างประจำ"){echo "selected='selected'";}?> value="ลูกจ้างประจำ">ลูกจ้างประจำ</option>
        <option <?php if($_POST["le_position"] == "พนักงานมหาวิทยาลัย"){echo "selected='selected'";}?> value="พนักงานมหาวิทยาลัย">พนักงานมหาวิทยาลัย</option>

    <?php }?>

</select>
