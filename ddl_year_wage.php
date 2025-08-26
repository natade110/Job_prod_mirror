<?php 

if(date("m") >= 9 ){ //|| $sess_accesslevel == 1 || $sess_accesslevel == 2
	$the_end_year = date("Y")+10; //new year at month 9
	$this_ddl_default_year =  date('Y') +1;
}else{
	$the_end_year = date("Y")+10;
	$this_ddl_default_year =  date('Y') ;
}
//echo date('Y');
?>

<select name="ddl_year" id="ddl_year" >
	<?php for($i= $the_end_year;$i>=$dll_year_start;$i--){
	
		$the_checked = "";
	
		if(!$do_checked_year){
			
			
			//check this as default if have post
			if($i==$this_ddl_default_year ){
				$the_checked = "selected='selected'";
				$do_checked_year = 1;
				
			}
			
		}
	?>
    <option value="<?php echo $i;?>" <?php echo $the_checked;?>><?php echo $i + 543;?></option>
    <?php } ?>
  </select>