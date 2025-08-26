<?php 
$do_checked_year = 0;

if(date("m") >= 9 ){ //|| $sess_accesslevel == 1 || $sess_accesslevel == 2
	$the_end_year = date("Y")+1; //new year at month 9
}else{
	$the_end_year = date("Y");
}


//this default year
$this_ddl_default_year = $the_end_year;

if(!$dll_year_start){
	$dll_year_start = 2011;	
}

?>
<select name="ddl_year" id="ddl_year" >
	<?php for($i= $the_end_year;$i>=$dll_year_start;$i--){
	
		$the_checked = "";
	
		if(!$do_checked_year){
			
			
			//check this as default if have post
			if($i==$output_values["Year"] || $i==$output_values["ReceiptYear"] || $i==$_GET["for_year"] || $i==$_POST["ddl_year"]){
				$the_checked = "selected='selected'";
				$do_checked_year = 1;
			}elseif($i==$this_ddl_default_year && (!$output_values["Year"] && !$output_values["ReceiptYear"] && !$_GET["for_year"] && !$_POST["ddl_year"])){
				$the_checked = "selected='selected'";
				$do_checked_year = 1;
				
			}
			
		}
	?>
    <option value="<?php echo $i;?>" <?php echo $the_checked;?>><?php echo $i + 543;?></option>
    <?php } ?>
  </select>