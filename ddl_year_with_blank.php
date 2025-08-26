<?php 

if(date("m") >= 9 ){ //|| $sess_accesslevel == 1 || $sess_accesslevel == 2
	$the_end_year = date("Y")+1; //new year at month 9
}else{
	$the_end_year = date("Y");
}

?>
<select name="ddl_year" id="ddl_year" >
	<option value="" selected="selected">...</option>
	<?php for($i= $the_end_year;$i>=$dll_year_start;$i--){
		$the_checked = "";
		
		//check this as default if have post
		if($i==$_POST["ddl_year"]){
			$the_checked = "selected='selected'";
		}
	?>
    <option value="<?php echo $i;?>" <?php echo $the_checked;?>><?php echo $i + 543;?></option>
    <?php } ?>
  </select>