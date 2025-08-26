<select name="le_position" id="le_position" onchange="checkPositionList();">
    <?php
    // Manual entries
    $manual_positions = array(
        31 => 'ข้าราชการ',
        32 => 'พนักงานราชการ',
        33 => 'ลูกจ้างประจำ',
        34 => 'พนักงานมหาวิทยาลัย'
    );

    // Add manual entries
    foreach ($manual_positions as $group_id => $group_name) {
        $selected = ($_POST["le_position"] == $group_id || $leid_row["le_position"] == $group_id) ? "selected='selected'" : "";
        echo "<option value=\"$group_id\" $selected>$group_name</option>";
    }

    // Existing database entries
    $get_position_sql = "select * from position_group order by group_name asc";
    $position_result = mysql_query($get_position_sql);
    
    while ($position_row = mysql_fetch_array($position_result)) {
        $selected = ($_POST["le_position"] == $position_row["group_id"] || $leid_row["le_position"] == $position_row["group_id"]) ? "selected='selected'" : "";
        echo "<option value=\"{$position_row['group_id']}\" $selected>{$position_row['group_name']}</option>";
    }
    ?>
</select>
