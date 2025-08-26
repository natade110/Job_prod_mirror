<style>

		.blink_me_warning {
		  animation: blinker_warning 1s linear infinite;
		}

		@keyframes blinker_warning {
		  50% {
			opacity: 0.3;
			/*color: #000000;*/
			/*font-weight: bold;*/
		  }
		}
		
		.sso_warning {
			
			padding-left: 30px; 
			color:#cc00cc;
			font-size: 16px;
			
		}
		
	</style>
	<script>

		function toggle_warining_popup(){
			
			  $( "#warning_popup_table" ).toggle();
			  $( "#warning_popup_hide_btn" ).toggle();
			  $( "#warning_popup_show_btn" ).toggle();
			
		}

	</script>
<div id="warning_popup" style="position: fixed; top: 0;  right: 0;  padding:3px; background-color:#006699; width: 400;    " >
					
	<table  bgcolor="#FFFFFF" width="400" border="1" align="center" cellpadding="0" cellspacing="0" style="border-collapse:collapse; ">					
		<tr>
			<td>
				<div id="warning_popup_hide_btn" align=right style="padding: 5px; display: none;">
					<a href="#" style="font-weight: normal;" onclick="toggle_warining_popup(); return false;">
						ย่อหน้าต่างข้อมูล
					</a>
				</div>
				<div id="warning_popup_show_btn" align=right style="padding: 5px;">
					<a href="#" style="font-weight: normal;" onclick="toggle_warining_popup(); return false;">
						ขยายหน้าต่างข้อมูล
					</a>
				</div>
			</td>
		</tr>
	</table>

					
	<table id="warning_popup_table"  bgcolor="#FFFFFF" width="400" border="1" align="center" cellpadding="10" cellspacing="0" style="border-collapse:collapse; display: none; ">					
		<tr>
			<td>
					
				
				<?php 
				
					//yoes 20181120
					//this variable is from organization_33_detailed_rows.php
					
					//print_r($sso_validated_array);
					
					if($sso_validated_failed){
						
						?>
					<div align="left" style="">
						<strong class="blink_me_warning" style="font-size: 18px; color:#cc00cc; ">!!! พบคนพิการออกจากงานก่อนหมดอายุสัญญาจ้าง</strong>
							
							
							<?php
							
								//print_r($sso_validated_array);
								for($swi = 0; $swi < count($sso_validated_array); $swi++){
							?>
								<div class="sso_warning">- <?php echo $sso_validated_array[$swi];?></div>
							<?php 									
								}
							
							?>
							
						
					</div>
					
					
					<script>
						toggle_warining_popup();
					</script>
					
					
				<?php }?>
				
				<?php 

					//have info inputted from company?
					
					if($sess_accesslevel != 4){

						//only do this for non-company					
													
						//$count_info = countCompanyInfo($output_values["CID"], $this_lawful_year);
						
						$count_info = getFirstItem("select 
							count(*)
						from 
							lawfulness_company 
						where 
							CID = '".$this_cid."' 
							and 
							Year = '$this_lawful_year' 
							
							");
													
					}
							
					if(countCompanyInfo($output_values["CID"], $this_lawful_year) && $sess_accesslevel != 4 && !$is_merged){
					
					
					?>
					
					 <div align="left" style="">
						<strong style="font-size: 18px; color:#C30; ">*** มีการส่งข้อมูลเข้ามาใหม่จากสถานประกอบการ</strong>
					</div>
					
					
					<?php
							
						
					}
					
					
					?>
				
				
			</td>
		</tr>
		
		
		<?php if($this_lawful_year >= 2018){?>
		<tr>
			<td>
			
				<u>ข้อมูลปี <?php echo $this_lawful_year+543;?> ทำการคำนวณสถานะตามกฎหมายใหม่</u>
				<br>
				<u>กรณีถ้าคำนวณข้อมูลรายงานสถานะตามกฎหมายเก่า ปี 54-60 จะได้สถานะดังต่อไปนี้:</u>
				
				
				<?php 
				
					$lawfulness_status_array = getLawfulnessStatusArrayByLID($this_lid, 1);
					
					
					//print_r($lawfulness_status_array);
					
					$lid_row = getFirstRow("select Employees from lawfulness where lid = '$this_lid'");
					
					?>
				
				<table border=1 style="border-collapse: collapse;" cellpadding=5>
					<tr>
						<td bgcolor="#efefef">
							สถานะ:
						</td>
						<td>
							<?php echo getLawfulText($lawfulness_status_array[lawful_status]);?> <?php echo getLawfulImage($lawfulness_status_array[lawful_status]);?>
						</td>
						
					</tr>
					<tr>
						<td bgcolor="#efefef">
							ต้องรับคนพิการ
						</td>
						<td>
							<?php  
							
								$wb_employees = $lid_row[Employees]; //echo " - ".$wb_employees;
								
								echo formatEmployee(getEmployeeRatio($wb_employees,$ratio_to_use));
								
								?> คน
						</td>
						
					</tr>
					<tr>
						<td bgcolor="#efefef">
							จ้างงาน ม33
						</td>
						<td>
							<?php  echo formatEmployee(getHireNumOfEmpFromLid($this_lid, 1));?> คน
						</td>
						
					</tr>
					<tr>
						<td bgcolor="#efefef">
							จ่ายเงินแทน ม34
						</td>
						<td>
							<?php echo formatEmployee($lawfulness_status_array[maimad_paid]);?> คน
						</td>
						
					</tr>
					<tr>
						<td bgcolor="#efefef">
							สัมปทาน ม35
						</td>
						<td>
							<?php  echo formatEmployee(getNumCuratorFromLid($this_lid, 1));?> คน
						</td>
						
					</tr>
					
				</table>
				
				
			
			</td>
		</tr>
		
		<?php }?>
	</table>

</div>
