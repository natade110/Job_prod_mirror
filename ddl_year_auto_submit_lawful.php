<?php 

if(date("m") >= 9){
	$the_end_year = date("Y")+1; //new year at month 9
}else{
	$the_end_year = date("Y");
}

if(!$is_main_branch){

	//non-main branches didnt go farther than 2012
	$the_end_year = 2012;

}


//company info starts at 2014
if($sess_accesslevel == 4){
	$dll_year_start = 2016;
	
	if($sess_userid == 1750 || $sess_userid == 1766 || $sess_userid == 1770 || $sess_userid == 1788 || $sess_userid == 1770){ //adhoc test
		$the_end_year = date("Y")+1;
	}
}



//this default year
$this_ddl_default_year = $the_end_year;

 //yoes 20160613 
if($this_year > 3000){
	
	echo "<b>".($this_year+543-1000)."</b>";
	
}else{
 
 ?>

<select name="ddl_year" id="ddl_year" onchange="this.form.submit()">
	<?php 
	
	
		$do_checked_year = 0;
		
		for($i= $the_end_year;$i>=$dll_year_start;$i--){
	
			$the_checked = "";
		
			if(!$do_checked_year){
			
			
				//check this as default if have post
				if($_POST["ddl_year"]==$i || $_GET["year"]==$i || $i==strtotime(date('Y'),date('Y')."+1 year") ){
					$the_checked = "selected='selected'";
					$do_checked_year = 1;
				}elseif($this_year == $i && !$_POST["ddl_year"] && !$_GET["year"]){
					$the_checked = "selected='selected'";
					$do_checked_year = 1;
						
				}
			
			}
	?>
    <option value="<?php echo $i;?>" <?php echo $the_checked;?>><?php echo $i + 543;?></option>
    <?php } ?>
  </select> 
 
 
<?php } //end else year > 3000?>