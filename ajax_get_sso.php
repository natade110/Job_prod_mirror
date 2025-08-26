<?php

	//include "db_connect.php";

	//then show all tables
	//echo "<br>=================<br>all tables in this DB is:";
	
	if($_POST["name"] == "John" && $_POST["location"] == "Boston"){
		header("Access-Control-Allow-Origin: *");
	}
	
	$the_id = "5200501031625";
	if($_POST["the_id"] && is_numeric($_POST["the_id"])){
		$the_id = $_POST["the_id"]*1;
		$CompanyCode = $_POST["CompanyCode"]*1;
	}elseif($_GET["the_id"] && is_numeric($_GET["the_id"])){
		$the_id = $_GET["the_id"]*1;
		$CompanyCode = $_GET["CompanyCode"]*1;
	}else{
	
		echo "..";
		exit();	
		
	}
	
	
	
	$the_id = addslashes(substr($the_id,0,13));
	
	$the_count = 0;
	
	function formatEmployStatusDesc($what){
	
		$to_show = "-";
		
		switch ($what){
			case "1" : $to_show = "จ้างงาน"; break;
			case "0" : $to_show = "ไม่ได้จ้างงาน"; break;
			
		}
		
		return $to_show;
		
	}
	
	
	function formatDateThai($date_time, $have_space = 1, $show_time = 0){

		if(!$date_time){
			return "";	
		}
		
		$date_time = str_replace('/', '-', $date_time);
	
		if($date_time != "0000-00-00"){
		   $this_selected_year = date("Y", strtotime($date_time));
		   $this_selected_month = date("m", strtotime($date_time));
		   $this_selected_day = date("d", strtotime($date_time));
	   }else{
		   $this_selected_year = 0;
		   $this_selected_month = 0;
		   $this_selected_day = 0;
	   }
		
		//$month_to_show = $this_selected_month;
		
		if($this_selected_month == "01"){
			$month_to_show = "มกราคม";
		}elseif($this_selected_month == "02"){
			$month_to_show = "กุมภาพันธ์";
		}elseif($this_selected_month == "03"){
			$month_to_show = "มีนาคม";
		}elseif($this_selected_month == "04"){
			$month_to_show = "เมษายน";
		}elseif($this_selected_month == "05"){
			$month_to_show = "พฤษภาคม";
		}elseif($this_selected_month == "06"){
			$month_to_show = "มิถุนายน";
		}elseif($this_selected_month == "07"){
			$month_to_show = "กรกฎาคม";
		}elseif($this_selected_month == "08"){
			$month_to_show = "สิงหาคม";
		}elseif($this_selected_month == "09"){
			$month_to_show = "กันยายน";
		}elseif($this_selected_month == "10"){
			$month_to_show = "ตุลาคม";
		}elseif($this_selected_month == "11"){
			$month_to_show = "พฤศจิกายน";
		}elseif($this_selected_month == "12"){
			$month_to_show = "ธันวาคม";
		}
		
		if($have_space == "0"){
			$date_thai = $this_selected_day . "" . $month_to_show . "" . ($this_selected_year);
		}else{
			$date_thai = $this_selected_day . " " . $month_to_show . " " . ($this_selected_year);
		}
		
		
		//yoes 20151021
		if($show_time){
			$date_thai .= " ".date("H:i:s", strtotime($date_time));
		}
	
		return $date_thai;
	
	}
	
	
	function show($obj,$k) {
		//return "$k: ".$obj->$k."<br>";
		return $obj->$k;
	}
	
	function print_xml($title,$xml){
		echo "<b>$title</b></br><hr>";
		echo xml_highlight($xml);
		echo "<br><br>";
	
	}
	
	
	
	//yoes 20170909 -- instead of showing output -> show seleting table insteae
					
	//echo $the_output; exit();					
			
	//if($the_count > 0){			
	
	header('Content-Type: text/html; charset=utf-8');
	$wsdl = "sso/EmployeeEmployments.wsdl";
	$options = array(
		"trace"         => 1, 
		"encoding"	=> "utf-8",
		'location' => 'https://wsg.sso.go.th/DBforService/services/EmployeeEmployments'	
	);
	
	$username = "deptest";
	$password = "vLNfg0cS";	
	$ssoNum = $the_id;
	
	
	$client = new SoapClient($wsdl,$options);
	try {
		$result = $client->getServ38($username,$password,$ssoNum);
		//print_xml("REQUEST",$client->__getLastRequest());
		//print_xml("RESPONSE",$client->__getLastResponse());
	}
	catch(SoapFault  $e){
		echo $e->getMessage;
		//print_xml("REQUEST",$client->__getLastRequest());
		//print_xml("RESPONSE",$client->__getLastResponse());
	}

?>


<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; ">
		
			
            <?php if(1==1){ ?>
            <tr bgcolor="#9C9A9C" align="center">
			  <td align="center" colspan="8" style="color: #fff;">
              <div align="center">ข้อมูลการทำงานจากประกันสังคม</div></td>
			 
 		    </tr>
            <?php } ?>
            
            
			<tr bgcolor="#9C9A9C" align="center">
				<td align="center" style="color: #fff;">
					ลำดับที่
				</td>
				<td align="center" style="color: #fff;">สถานประกอบการ</td>
                <td align="center" style="color: #fff;">
					เลขที่บัญชีนายจ้าง
				</td>
				<td align="center" style="color: #fff;">
					สาขา
				</td>
                <td align="center" style="color: #fff;">
					สถานะการจ้างงาน
				</td>
				<td align="center" style="color: #fff;">
					วันที่เข้างาน
				</td>
			
				<td align="center" style="color: #fff;">
					วันที่ลาออก
				</td>
				
                
                <td align="center" style="color: #fff;">
					
				</td>
				
				
			</tr>
            
            
            <?php 
			
				//for($i=0;$i < count($result_array);$i++){
				foreach($result->result->employments as $employment){
					
					$seq++;
			?>
		
        	<tr bgcolor="#ffffff" align="center">
				<td>
					<div align="center">
						<?php echo $seq; ?>
					</div>
				</td>
				<td><?php 
					
					echo show($employment,"companyName");
					
					?></td>
                <td>
					<?php 
					
					
					echo show($employment,"accNo");
					
					?>
				</td>
				<td>
					<?php 
					
					echo show($employment,"accBran") ;
					
					?>
				</td>
				<td>
					<?php 
					
					echo show($employment,"employStatusDesc");
					
					?>
				</td>
                <td>
					<?php 
					
					//echo formatDateThai($result_array[$i]["expStartDate"]);
					
					//echo show($employment,"expStartDate");
					
					$expStartDate = show($employment,"expStartDate");
					echo  formatDateThai($expStartDate);
					
					?>
                    <input id="sso_start_date_day_<?php echo $seq;?>" type="hidden" value="<?php echo substr( $expStartDate,0,2);?>" />
                    <input id="sso_start_date_month_<?php echo $seq;?>" type="hidden" value="<?php echo substr( $expStartDate,3,2);?>" />                    
                    <input id="sso_start_date_year_<?php echo $seq;?>" type="hidden" value="<?php echo substr( $expStartDate,6,4)-543;?>" />
				</td>
					
				<td>
					<?php 
					
					//echo formatDateThai($result_array[$i]["empResignDate"]);
					//echo show($employment,"empResignDate");
					
					
					$empResignDate = show($employment,"empResignDate");
					echo  formatDateThai($empResignDate);
					
					?>
                    
                    <input id="sso_end_date_day_<?php echo $seq;?>" type="hidden" value="<?php echo substr($empResignDate,0,2);?>" />
                    <input id="sso_end_date_month_<?php echo $seq;?>" type="hidden" value="<?php echo substr($empResignDate,3,2);?>" />
                     <input id="sso_end_date_year_<?php echo $seq;?>" type="hidden" value="<?php echo substr($empResignDate,6,4)-543;?>" />
                    
				</td>	
                
                <td>
                
               	<?php
				
				if($CompanyCode != trim(show($employment,"accNo"))){
					//company code not matched -> do nothing		
				}else{
					
					?>
                    
                    <a href="#" onclick="populateSSODates(<?php echo $seq;?>); return false;">เลือกข้อมูล</a>
                    
                <?php	
					
				}
				
				?> 
                
                </td>
			
			
			</tr>
        
            
            <?php }?>
            
            
            <?php if(!$seq){?>
            
            
            	<tr bgcolor="#ffffff" align="center">
                  <td colspan="8">ไม่พบข้อมูลการทำงาน</td>
              </tr>
            
            
            <?php }?>
            
</table>
<script>
	
	function populateSSODates(what){
		
		//alert('what');
		
		//alert($("#sso_start_date_day_"+what).val()*1);
		
		$("#le_date_day").val($("#sso_start_date_day_"+what).val());
		$("#le_date_month").val($("#sso_start_date_month_"+what).val());
		$("#le_date_year").val($("#sso_start_date_year_"+what).val());
		
		
		$("#le_end_date_day").val($("#sso_end_date_day_"+what).val());
		$("#le_end_date_month").val($("#sso_end_date_month_"+what).val());
		$("#le_end_date_year").val($("#sso_end_date_year_"+what).val());
		
		$( "#sso_result" ).html('');
	}

</script>


	