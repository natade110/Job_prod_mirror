<select name="BusinessTypeCode">
	<option value="">-- select --</option>
	<?php
    
    //all photos of this profile
    $get_orgtype_sql = "select *
            from businesstype
            order by BusinessTypeName asc
            ";
    //echo get_orgtype_sql;
    $orgtype_result = mysql_query($get_orgtype_sql);
    
    
    
    while ($orgtype_row = mysql_fetch_array($orgtype_result)) {
    
    
    ?>              
        <option <?php if($_POST["BusinessTypeCode"] == $orgtype_row["BusinessTypeCode"] || $output_values["BusinessTypeCode"] == $orgtype_row["BusinessTypeCode"]){echo "selected='selected'";}?> value="<?php echo $orgtype_row["BusinessTypeCode"];?>">
		<?php echo substr($orgtype_row["BusinessTypeName"],0,120);
			//echo $orgtype_row["BusinessTypeName"];?>
        </option>
    
    <?php
    }
    ?>
</select>