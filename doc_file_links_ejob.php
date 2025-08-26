
	<?php
	
		
                                            
        //generate files link
        $get_file_sql = "select *
            from files
            where
                file_for = '$this_id'
                and
                file_type = '$file_type'
				and
				file_for > 0
                
            order by file_id desc
            
            ";
		
		//echo $get_file_sql ;
            
        $file_result = mysql_query($get_file_sql) or die(mysql_error());
		
		//print_r($file_result);
        
        if($file_type == "announce_docfile"){
            $path_to_use = $announce_docfile_relate_path;
        }else{
            $path_to_use = $hire_docfile_relate_path;
        }
		
		//yoes 20160501
		//use ejob's path
		$path_to_use = "https://ejob.dep.go.th/ejob/".$path_to_use;
		
		$have_doc_file = 0;
        
        while ($post_row = mysql_fetch_array($file_result)) {
			
			$have_doc_file++;
			//echo "what";
        ?>
            <span id="file_<?php echo $post_row["file_id"];?>">
            
                
            <a href="<?php echo "$path_to_use/".$post_row["file_name"];?>"><?php echo end(explode("_",$post_row["file_name"],2));?></a> 
            
            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$disable_delete){//exec can't do all these // yoes 20151110 --> allow company to delete this?>
                <a href="#" title="ลบไฟล์" onclick="doDeleteFile('<?php echo $post_row["file_id"];?>'); return false;"><img src="decors/trashcan_icon.jpg" border="0" /></a>
            <?php }?>
            
            || </span>
            <span id="loading_<?php echo $post_row["file_id"];?>" style="display:none;">
            <img src="decors/loading.gif" border="0" height="15" />
            </span>
        
        <?php }?>
