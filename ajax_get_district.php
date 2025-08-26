<?php

	include "db_connect.php";
		
	if(is_numeric($_POST["prov"])){
		//$this_id = doCleanInput($_POST["id"]);
		$sold = $_POST["prov"]*1;
		
		//change province id to province code ...
		$s = getFirstItem("select province_code from provinces where province_id = '$sold'");
		
	}elseif(is_numeric($_GET["prov"])){
		
		$sold = $_GET["prov"]*1;		
		
		$s = getFirstItem("select province_code from provinces where province_id = '$sold'");
		
	}else{
		exit();
	}
?><?php 

//echo "select * from districts where province_code = '$s' order by district_name asc";

	echo "<select name=\"District\" id=\"District\" onchange=\"doDistrictChange('".$sold."'); \$('#rad_area_1').prop('checked', true);\" required >";		

			$result = mysql_query("select * from districts where province_code = '$s' and district_name != 'ข้อมูลไม่ถูกต้อง' order by district_name asc");
			
			echo "<option value=''>-- เลือก --</option>";
			
			while($ret = mysql_fetch_array($result)){			
				echo '<option value="'.$ret[district_name].'"> '.$ret['district_name'] .' </option>';
			}

		echo '</select>';		


?>