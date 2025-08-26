<?php 

	//yoes 20160120 --> override something
	if($selected_value){
		$origin_curator_row_to_fill = $curator_row_to_fill["curator_disable_desc"];
		$curator_row_to_fill["curator_disable_desc"] = $selected_value;
	}

?>
<select name="le_disable_desc<?php echo $dis_type_suffix;?>" id="le_disable_desc<?php echo $dis_type_suffix;?>">
	<?php if($do_hide_blank_dis != 1){ //hide first item if we wanted to ?>
	<option value="" >-- select --</option>
    <?php } $do_hide_blank_dis = 0;//switch this to false so it not effect subsequent ddl_disable_type}?>
   
   
    <option  value="ความพิการทางการเห็น" <?php if($leid_row["le_disable_desc"]=="ความพิการทางการเห็น" || $curator_row_to_fill["curator_disable_desc"]=="ความพิการทางการเห็น"){?>selected="selected"<?php }?>>ความพิการทางการเห็น</option>
    <option value="ความพิการทางการได้ยินหรือสื่อความหมาย" <?php if($leid_row["le_disable_desc"]=="ความพิการทางการได้ยินหรือสื่อความหมาย"  || $curator_row_to_fill["curator_disable_desc"]=="ความพิการทางการได้ยินหรือสื่อความหมาย"){?>selected="selected"<?php }?>>ความพิการทางการได้ยินหรือสื่อความหมาย</option>
    <option value="ความพิการทางการเคลื่อนไหวหรือร่างกาย" <?php if($leid_row["le_disable_desc"]=="ความพิการทางการเคลื่อนไหวหรือร่างกาย"   || $curator_row_to_fill["curator_disable_desc"]=="ความพิการทางการเคลื่อนไหวหรือร่างกาย"){?>selected="selected"<?php }?>>ความพิการทางการเคลื่อนไหวหรือร่างกาย</option>
    
    <?php if($this_lawful_year >= 2013){?>
	
	
    
    <option value="ความพิการทางจิตใจหรือพฤติกรรม" <?php if($leid_row["le_disable_desc"]=="ความพิการทางจิตใจหรือพฤติกรรม" || $curator_row_to_fill["curator_disable_desc"]=="ความพิการทางจิตใจหรือพฤติกรรม"){?>selected="selected"<?php }?>>ความพิการทางจิตใจหรือพฤติกรรม</option>
    
    
    <?php }else{?>
    <option value="ความพิการทางจิตใจหรือพฤติกรรม หรือออทิสติก" <?php if($leid_row["le_disable_desc"]=="ความพิการทางจิตใจหรือพฤติกรรม หรือออทิสติก"|| $curator_row_to_fill["curator_disable_desc"]=="ความพิการทางจิตใจหรือพฤติกรรม หรือออทิสติก"){?>selected="selected"<?php }?>>ความพิการทางจิตใจหรือพฤติกรรม หรือออทิสติก</option>
    
    <?php }?>
    
    
    <option value="ความพิการทางสติปัญญา" <?php if($leid_row["le_disable_desc"]=="ความพิการทางสติปัญญา"|| $curator_row_to_fill["curator_disable_desc"]=="ความพิการทางสติปัญญา"){?>selected="selected"<?php }?>>ความพิการทางสติปัญญา</option>
    <option value="ความพิการทางการเีรียนรู้" <?php if($leid_row["le_disable_desc"]=="ความพิการทางการเีรียนรู้"|| $curator_row_to_fill["curator_disable_desc"]=="ความพิการทางการเีรียนรู้"){?>selected="selected"<?php }?>>ความพิการทางการเรียนรู้</option>
    
     <?php if($this_lawful_year >= 2013){?>
     
     <option value="ความพิการทางออทิสติก" <?php if($leid_row["le_disable_desc"]=="ความพิการทางออทิสติก"|| $curator_row_to_fill["curator_disable_desc"]=="ความพิการทางออทิสติก"){?>selected="selected"<?php }?>>ความพิการทางออทิสติก</option>
     <option value="ความพิการซ้ำซ้อน" <?php if($leid_row["le_disable_desc"]=="ความพิการซ้ำซ้อน"|| $curator_row_to_fill["curator_disable_desc"]=="ความพิการซ้ำซ้อน"){?>selected="selected"<?php }?>>ความพิการซ้ำซ้อน</option>
    <?php }?>
    
   
    <?php if($is_report_page){?>
	
	<option value="null" >*** ไม่ได้ระบุประเภทความพิการ</option>
    
	
	<?php }?>
	
</select>
<?php 

	//yoes 20160120 --> override something
	if($selected_value){
		$curator_row_to_fill["curator_disable_desc"] = $origin_curator_row_to_fill;
	}

?>