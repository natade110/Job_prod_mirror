<?php

	include "db_connect.php";
		
	if(is_numeric($_POST["prov"]) && ($_POST["dist"])){
		//$this_id = doCleanInput($_POST["id"]);
		$s = $_POST["prov"]*1;		
		//change province id to province code ...
		$s = getFirstItem("select province_code from provinces where province_id = '$s'");
		$m = $_POST["dist"];
		
		//yoes 20170119 --> also check from name
		$dcode = getFirstItem("
				
				select
					district_code
				from
					districts
				where
					district_name = '$m'
		");
		
		if($dcode){
			
			$m = $dcode;
		}
		
	}elseif(is_numeric($_GET["prov"]) && ($_GET["dist"])){
		
		$s = $_GET["prov"]*1;	
		$s = getFirstItem("select province_code from provinces where province_id = '$s'");
		$m = $_GET["dist"];
		
		//yoes 20170119 --> also check from name
		$dcode = getFirstItem("
				
				select
					district_code
				from
					districts a
				where
					district_name = '$m'
		");
		
		if($dcode){
			
			$m = $dcode;
		}
		
	}else{
		echo "...";
		exit();
	}
?><?php 


//echo "select * from subdistricts where province_code = '$s' and district_code = '$m' and subdistrict_name != 'ข้อมูลไม่ถูกต้อง' order by subdistrict_name asc";


echo "<select name=\"Subdistrict\" id=\"Subdistrict\" onchange=\"\$('#rad_area_1').prop('checked', true);\" required>";
		
			$result = mysql_query("select * from subdistrict where province_code = '$s' and district_code = '$m' and subdistrict_name != 'ข้อมูลไม่ถูกต้อง' order by subdistrict_name asc");
								
			echo "<option value=''>-- เลือก --</option>";					
								
			while($ret = mysql_fetch_array($result)){	
				echo '<option value="'.$ret['subdistrict_name'].'" data-postcode="'.$ret['post_code'].'"> '.$ret['subdistrict_name'].' </option>';

			}
			
		echo '</select>';

?>
<script>
    $("#Subdistrict").change(function () {
        //alert($(this).find(':selected').data('postcode'));
        if($(this).find(':selected').data('postcode') > 0) {
            $('#org_zip').val($(this).find(':selected').data('postcode'));
        }
    });
</script>
