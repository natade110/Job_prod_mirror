
	<?php
		$this_cancreate = (isset($this_cancreate))? $this_cancreate : false;
		$this_parent_table = isset($this_parent_table)? $this_parent_table : "";
		function getAttachFileName( $fileName)
		{
			$array = explode('_', $fileName, 2);
			return end($array);
		}
	                                  
        //generate files link
        $get_file_sql = "select *
            from files
            where
                file_for = '$this_id'
                and
                file_type = '$file_type'
                
            order by file_id desc
            
            ";
            
        $file_result = mysql_query($get_file_sql);        
        
        $path_to_use = $hire_docfile_relate_path;
       
        
        while ($post_row = mysql_fetch_array($file_result)) {
        ?>
        <span id="file_<?php echo $post_row["file_id"];?>">
        
			
		<a href="<?php echo "$path_to_use/".$post_row["file_name"];?>" target="_blank"><?php echo getAttachFileName($post_row["file_name"]);?></a> 
        
        <?php if($this_cancreate){//exec can't do all these // yoes 20151110 --> allow company to delete this?>
            <a href="#" title="ลบไฟล์" onclick="doDeleteFile('<?php echo $post_row["file_id"];?>', '<?php echo $this_parent_table?>'); return false;"><img src="decors/trashcan_icon.jpg" border="0" /></a>
        <?php }?>
        
        || </span>
        <span id="loading_<?php echo $post_row["file_id"];?>" style="display:none;">
        <img src="decors/loading.gif" border="0" height="15" />
        </span>
        <?php }?>
