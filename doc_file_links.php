<?php
	
		//yoes 20230214
		$filter_file_id = "";
		
        if($file_id){
			$filter_file_id = "and file_id = '$file_id'";
		}                                            
        //generate files link
        $get_file_sql = "select *
            from files
            where
                file_for = '$this_id'
                and
                file_type = '$file_type'
				$filter_file_id
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
		
		
		
		$have_doc_file = 0;
        
        while ($post_row = mysql_fetch_array($file_result)) {
			
			$have_doc_file++;
			//echo "what";
			
			if(substr($post_row["file_name"],0,4)=="ejob"){
				$path_to_use_2 = "https://ejob.dep.go.th/ejob//".$path_to_use;
				$post_row["file_name"] = substr($post_row["file_name"],5);
			}else{
				$path_to_use_2 = $path_to_use;
			}
        ?>
            <span id="file_<?php echo $post_row["file_id"];?>">
            
               
            <a href="<?php echo "$path_to_use_2/".$post_row["file_name"];?>" target="_blank">
			
				<?php echo end(explode("_",$post_row["file_name"],2));?>
            </a> 
            
            <?php 
			
			//yoes 20160712 -> also add "is_read_only" condition
			//yoes 20160816 -> also add "case_closed" condition
			if($sess_accesslevel != 5 && $sess_accesslevel != 8 && !$disable_delete && !$is_read_only && !$case_closed){//exec can't do all these // yoes 20151110 --> allow company to delete this?>
                <a href="#" title="ลบไฟล์" onclick="doDeleteFile('<?php echo $post_row["file_id"];?>'); return false;"><img src="decors/trashcan_icon.jpg" border="0" /></a>
            <?php }?>
            
            || </span>
            <span id="loading_<?php echo $post_row["file_id"];?>" style="display:none;">
            <img src="decors/loading.gif" border="0" height="15" />
            </span>
        
        <?php }?>
