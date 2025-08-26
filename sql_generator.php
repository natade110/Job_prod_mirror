<?php
	
	include "db_connect.php";
?>
<?php if(!isset($_POST["company_list"])){ ?>
<form method="post" target="_blank">
<table>
	<tr>
  <td valign="top">
          Company info- one per line<br />
          
        <textarea name="company_list" id="company_list" cols="75" rows="10" ></textarea>
      <br /></td>
        
	</tr>
</table>
<input name="input" type="submit" />
</form>
<?php } ?>
<?php 
//print_r($_POST["company_list"]);
if(isset($_POST["company_list"])){
	
	function cleanInput($what){
		return trim(str_replace("||","\,",$what));
	}
	
	//print_r($_POST);
	
	$company_to_split = str_replace("\r\n", "|**|", $_POST["company_list"]); 
	$company_to_split = str_replace("\r", "|**|", $_POST["company_list"]);

	$company_array = explode("|**|",$company_to_split);
	
	//print_r($company_array);
	
	for($i =1; $i<count($company_array);$i++){
	
		$this_array = explode(",",$company_array[$i]);
		
		//print_r($this_array);
		$province_sql = ("select province_id from provinces where province_name = '".($this_array[7])."'");
		//$province_sql = ("select province_id from provinces where province_name = 'อุดรธานี'");
	
		//rand(100,900)."-".rand(100,900)."-".rand(100,900)
		echo "<br>insert into company(
		
					CompanyCode
					
					,BranchCode
					,CompanyTypeCode
					
					,CompanyNameThai
					,Address1
					,Subdistrict
					,District
					,Province
					
					,Zip
					,Telephone
					,Employees
					
					,BusinessTypeCode
					
					
					,LawfulFlag
		
				)values(
					'".cleanInput($this_array[0])."'
					,'".($this_array[1])."'
					,'".addLeadingZeros(trim($this_array[2]),2)."'
					,'".cleanInput($this_array[3])."'
					,'".cleanInput($this_array[4])."'
					,'".cleanInput($this_array[5])."'
					,'".cleanInput($this_array[6])."'
					,'".default_value(getFirstItem($province_sql),$this_array[7])."'
					
					,'".$this_array[8]."'
					,'".$this_array[9]."'
					,'".$this_array[11]."'
					,'".addLeadingZeros($this_array[10],4)."'
					
					
					,'3'
				
				);";
				
				//echo getFirstItem("select province_id from provinces where province_name = '".$this_array[5]."'");
				//echo $province_sql;
	}
	
	
	
}?>
