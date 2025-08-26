<select name="check_bank" id="select" <?php echo $the_disabled;?>>
    <option value="" >-- select --</option>
	<?php
    
    //all photos of this profile
    $get_bank_sql = "select *
            from bank
            order by bank_name asc
            ";
    //echo get_bank_sql;
    $bank_result = mysql_query($get_bank_sql);
    
    while ($bank_row = mysql_fetch_array($bank_result)) {
    
    
    ?>              
        <option <?php if($_POST["bank_id"] == $bank_row["bank_id"] || $this_bank_id == $bank_row["bank_id"]){echo "selected='selected'";}?> value="<?php echo $bank_row["bank_id"];?>"><?php echo $bank_row["bank_name"];?></option>
    
    <?php
    }
    ?>
</select>