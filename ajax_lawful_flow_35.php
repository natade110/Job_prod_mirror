<?php

	include "db_connect.php";
		
	$skip_html_head = 1;
	
	include "header_html.php";
	
	//yoes 20200615 -- add "do interests statement"
	if($_GET[do_update_interests]){
		
		$select_sql = "
		
			select
				cid
				, year
			from
				lawful_35_principals a
					join
						lawfulness b
							on
							a.p_lid = b.lid
			where
				p_interests is null
			limit	
				0,1
		
		";
		
		$principal_row = getFirstRow($select_sql);
		
		$_POST["the_cid"] = $principal_row[cid];
		$_POST["the_year"] = $principal_row[year];
		
		
			
		
	}
	
	$the_cid = $_POST["the_cid"]?$_POST["the_cid"]:$_GET["the_cid"];
	$the_cid = $the_cid*1;
	
	$the_year = $_POST["the_year"]?$_POST["the_year"]:$_GET["the_year"];
	$the_year = $the_year*1;
	
	//$the_result_array = get33Flows($the_leid);
	$the_date_array = $the_result_array[details];
	//print_r($flow_35_result);
	
	$lawful_row = getFirstRow("
	
		select
			*
		from
			lawfulness l
		where
			l.year = '$the_year'
			and
			l.cid = '".$the_cid."'
	
	
	");
	
	print_r($lawful_row);
	
	$this_lid = $lawful_row[LID];
	
		
	generate35PrincipalFromLID($this_lid);
	
	
	syncPaymentMeta($this_lid,1,"m35");
	
	
	
	//show 33 details
	echo "<br> - echo $this_lid;";
														
		$principal_35_sql = "
			
			select
				*
			from
				lawful_35_principals
			where
				p_lid = '$this_lid'
		
		";
		
		$principal_35_result = mysql_query($principal_35_sql);
		
		while($principal_35_row = mysql_fetch_array($principal_35_result)){
			
			
			//yoes 20200618 try get interests function here...
			$interests_row = generateInterestsFromPrincipals($this_lid, $principal_35_row[p_from],  $principal_35_row[p_to], "m35");
			
			echo "<br> ---- <br>";
			print_r($interests_row);
			echo "<br> ---- <br>";
			
			
			$p_from = $principal_35_row[p_from];
			$p_to = $principal_35_row[p_to];
			
			
			$le_row_from = getFirstRow("select * from curator where curator_id = '".$p_from."'");
			$le_row_to = getFirstRow("select * from curator where curator_id = '".$p_to."'");
			
			
			echo "<br>";
			//print_r($principal_35_row);
			echo "<b>จ่ายแทน " . $p_from . "(".$le_row_from[le_code].") ถึง " . $p_to . "(".$le_row_to[le_code].") (".($this_lid.$p_from.$p_to).")" ;
			
			echo " -> start_date: " . $le_row_from[le_end_date] . " ";
			echo " -> end_date: " . $le_row_to[le_start_date] . " ";
			echo " เงินต้น  " . $principal_35_row[p_amount] . " บาท </b>";
			
			
			
			
			$total_35_principal += $principal_35_row[p_amount];
			
			$pending_principal = $principal_35_row[p_amount];
			$this_interest = 0;
			
			
			echo " ดอกเบี้ยคงเหลือของ row นี้ " . $this_row_interests . " บาท";
			
		}
		
		
		
		echo "<br> - รวมเงินต้นทั้งหมด = " . $total_35_principal;
		echo "<br> - รวมเงินดอกเบี้ยทั้งหมด = " . $total_35_pending_interests;
	
	
	
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
<?php if($_GET[do_update_interests] && $the_cid && $the_year){ ?>
<script>
	location.reload();
</script>
<?php } ?>
