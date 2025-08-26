<?php

	include "db_connect.php";
		
	$skip_html_head = 1;
	
	include "header_html.php";
	
	$the_leid = $_POST["the_leid"]?$_POST["the_leid"]:$_GET["the_leid"];
	$the_leid = $the_leid*1;
	
	$the_result_array = get33Flows($the_leid);
	$the_date_array = $the_result_array[details];
	//print_r($flow_33_result);
	
?>
<body>

<div class="modal-body">
	<h4>Overflowing text to show scroll behavior - <?php echo $the_leid;?></h4>
	<p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
	<p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
	
	
	<div class="table-responsive">
		<table class="table">
			<thead class="thead-light">
				<tr>
					<th scope="col"><div align=center>วันที่</div></th>
					<th scope="col"><div align=center>ชื่อ-สกุล/เลขที่ใบเสร็จ</div></th>
					<th scope="col"><div align=center>เลขบัตรประชาชน</div></th>
					<th scope="col"><div align=center>ประเภทของวันที่</div></th>
					<th scope="col"><div align=center>รายละเอียด</div></th>
					<th scope="col"><div align=center>เงินต้น</div></th>
					<th scope="col"><div align=center>ดอกเบี้ย</div></th>
					
				</tr>
			</thead>
			<tbody>
			
				<?php
				
						
						for($i=0;$i<count($the_date_array);$i++){
							
							//init for each loop
							$is_entry_date = 0;
							$interest_day_offset = 0;
							$this_principal = 0;
							
							if($the_date_array[$i]["date"] != "0000-00-00"){
					?>
					
						
							<tr>
							<td scope="row"><?php echo formatDateThai($the_date_array[$i]["date"]); ?></td>
							<td><?php 
							
								echo $the_date_array[$i]["le_name"];
								
								if($the_date_array[$i][is_receipt]){
									echo "ใบเสร็จเล่มที่ ".$the_date_array[$i][receipt_row][BookReceiptNo]." เลขที่ ".$the_date_array[$i][receipt_row][ReceiptNo];
								}

								?></td>
							<td><?php echo $the_date_array[$i]["le_code"]; ?></td>
							
							
							<td><?php 								
							
								echo $the_date_array[$i][date_type];
								
							?></td>
							<td>			
							
							<?php
								/*
								if($is_entry_date == 1 && $the_date_array[$i]["date"] < "$the_year-01-01"){
									
									$this_m33_date_diff_before = 0;
									
									echo "ส่วนต่าง ".$this_m33_date_diff_before . " วัน";
									echo "<br>";
									$this_principal = round($this_m33_date_diff_before*$this_year_wage,2);
									echo "คิดเป็นเงินต้น ". $this_principal . " บาท";									
									$total_principal += $this_principal;									
									array_push($principals_array, $this_principal);
							
								}elseif($is_entry_date == 1 && !$the_date_array[$i]["is_child"]){
									
									$this_m33_date_diff_before = dateDiffTs(strtotime($the_date_array[$i-1]["date"]), strtotime($the_date_array[$i]["date"]),0);
									
									echo "ส่วนต่าง ".$this_m33_date_diff_before . " วัน";
									echo "<br>";
									$this_principal = round($this_m33_date_diff_before*$this_year_wage,2);
									echo "คิดเป็นเงินต้น ". $this_principal . " บาท";									
									$total_principal += $this_principal;									
									array_push($principals_array, $this_principal);
									
								}elseif($is_entry_date == 1 && $the_date_array[$i]["is_child"]){
									
									$this_m33_date_diff_before = dateDiffTs(strtotime($the_date_array[$i-1]["date"]), strtotime($the_date_array[$i]["date"]),0) - 1;
									echo "ส่วนต่าง ".$this_m33_date_diff_before . " วัน";
									if($this_m33_date_diff_before <= 45){
										echo "<br>";
										echo "ไม่คิดเงินต้นเพราะเป็นการรับแทนใน 45 วัน";										
									}else{
										echo "<br>";
										$this_principal = round($this_m33_date_diff_before*$this_year_wage,2);
										echo "คิดเป็นเงินต้น ". $this_principal . " บาท";
										$total_principal += $this_principal;										
										array_push($principals_array, $this_principal);
									}
									
								}elseif($the_date_array[$i]["is_last_day_of_year"] && $last_end_date){
																	
									
									$this_m33_date_diff_before = dateDiffTs(strtotime($last_end_date), strtotime($the_date_array[$i]["date"]),0);
									
									echo "ส่วนต่าง ".$this_m33_date_diff_before . " วัน";
									echo "<br>";
									$this_principal = round($this_m33_date_diff_before*$this_year_wage,2);
									echo "คิดเป็นเงินต้น ". $this_principal . " บาท";									
									$total_principal += $this_principal;									
									array_push($principals_array, $this_principal);
									
								}else{
									
									$this_principal = 0;
								}
								
								
								//calculate interests
								if($this_principal){
									
									//interest start-stop date
									//default to 1 april
									$interest_start_date = $year_interest_start_date;
									
									//if have someone that leave before this ...
									if($the_date_array[$i-1]["is_end_date"]){
										$interest_start_date = max($year_interest_start_date,$the_date_array[$i-1]["le_row"][le_end_date]);
										$interest_day_offset = -1;
									}
									
									$current_date = date('Y-m-d');
									$interest_end_date = $current_date;
									
									
									echo "<br>ดอกเบี้ยคิดจากวันที่ " . formatDateThai($interest_start_date) . " ถึง ". formatDateThai($current_date);
									$interest_days = max(dateDiffTs(strtotime($interest_start_date), strtotime($interest_end_date), 1)+$interest_day_offset,0);
									$this_interest = round(($interest_days*(7.5/100/365)*$this_principal),2);
									echo "<br>= ".$interest_days." วัน x 7.5/100/365 x " . $this_principal . " = ".$this_interest." บาท";
									
									$total_interest += $this_interest;
								}
								*/
							?>
							
							<?php if($the_date_array[$i][meta_amount]){ ?>
							
								
								<div align=center>
								<?php
								
									if($the_date_array[$i][meta_amount]){
										echo "จ่ายเงิน ".number_format($the_date_array[$i][meta_amount],2) . " บาท";
									}
									
									
								?>
								</div>
								
							
							
							<?php }?>
							
							</td>
							
							<td>
								<div align=right>
								<?php
								
									if($the_date_array[$i][this_principal]){
										echo number_format($the_date_array[$i][this_principal],2) . " บาท";
									}
									
									
								?>
								</div>
							</td>
							
							<td>
								<div align=right>
								<?php 
								
									if($the_date_array[$i][this_interest]){
										//echo $the_date_array[$i][this_interest] . " บาท";
										echo number_format($the_date_array[$i][this_interest],2) . " บาท";
									}	
								
								?>
								</div>
							
							</td>
							
						</tr>
						
					<?php
						
							}
						
						}
					
					?>
				
				
				<tr class="table-info">
					
					<th colspan=4><div align=center>รวม</div></th>
					<td>
						<?php for($j=0; $j < count($principals_array); $j++){ 	
							//echo "เงินต้น " . ($j+1) . ": " . $principals_array[$j] . " บาท";
						}?>
					</td>
					<td>
					<div align=right>
					<?php 
						
						echo number_format($the_result_array[total_principal],2) . " บาท";
					?>
					</div>
					</td>
					<td>
					<div align=right>
					<?php 
						//echo $total_interest . " บาท";
						echo number_format($the_result_array[total_interest],2) . " บาท";
					?>
					</div></td>
					
				</tr>
				
				<tr class="table-info">
					
					<th colspan=4><div align=center>รวมต้องชำระ</div></th>
					<td>
						
					</td>
					<td colspan=2>
						<div align=right>
						<?php 
							//echo "<b>".($total_principal +$total_interest ). "</b> บาท";
						?>
						<?php 
							//echo $total_interest . " บาท";
							echo "<b>";
							echo number_format($the_result_array[total_principal]+$the_result_array[total_interest],2) . " บาท";
							echo "</b>";
						?>
						</div>
					</td>
					
				</tr>
				
			</tbody>
		</table>
	</div>
	
	
</div>


</body>
</html>