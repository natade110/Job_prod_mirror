<?php 

if(date("m") >= 9 ){ //|| $sess_accesslevel == 1 || $sess_accesslevel == 2
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
}



//this default year
$this_ddl_default_year = $the_end_year;

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
			
			
			
			//special on "dummy" page => only show dropdown for year with lawfulness
			$have_lawful_year = "
			
							select
								count(*)
							from
								lawfulness
							where
								cid = '".$output_values["CID"]."'						
								and
								year = $i
							
							";
							
			if(getFirstItem($have_lawful_year)){
			 
			
	?>
		    <option value="<?php echo $i;?>" <?php echo $the_checked;?>><?php echo $i + 543;?></option>
            
            
          
            
    <?php 
			
			} //end if(getFirstItem($have_lawful_year)){
	
		} ?>
  </select>