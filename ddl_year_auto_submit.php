<?php 

if(date("m") >= 9 ){ //|| $sess_accesslevel == 1 || $sess_accesslevel == 2
	$the_end_year = date("Y")+1; //new year at month 9
}else{
	$the_end_year = date("Y");
}

//this default year
$this_ddl_default_year = $the_end_year;





?>
<select name="ddl_year" id="ddl_year" onchange="this.form.submit()">
	<?php for($i=$the_end_year;$i>=$dll_year_start;$i--){?>
    <option value="<?php echo $i;?>" <?php 
		if($_POST["ddl_year"]==$i || $_GET["year"]==$i || $i==strtotime(date('Y'),date('Y')."+1 year")){
			echo 'selected="selected"';
		}elseif($this_year == $i && !$_POST["ddl_year"] && !$_GET["year"]){
				echo 'selected="selected"';
		}
		?>><?php echo $i + 543;?></option>
    <?php } ?>
  </select>