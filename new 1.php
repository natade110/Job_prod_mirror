<?php

	include_once "db_connect.php";
	include_once "session_handler.php";
		
	$skip_html_head = 1;
	
	include "header_html.php";
	
	if($sess_accesslevel == 3){
	
		$modal_diff_33_sql = " and d.province = '$sess_meta'";			
										
	}else{
		
		$show_all_provinces = 1;
		
	}
	
?>
<body>

<div class="modal-body">
	<h4><font color=blue>รายการลูกจ้างคนพิการที่มีการออกจากงานแล้ว (ข้อมูลจากประกันสังคม)</font></h4>
	
	<?php
	
		//select lawful_employees thats not match with sso end_date
		$sql = "
		
			select
				
				*
			from
				lawful_employees_sso_end_date c
					
					left join
						lawful_employees a
						on
						a.le_id = c.le_id
				
					join
						lawfulness b
						on
						c.le_origin_cid = b.cid
						and
						c.le_origin_year = b.year					
					
					join
						company d
						on
						b.cid = d.cid
					join
						provinces z
							on
							d.province = z.province_id
						
			where
				c.le_origin_end_date != c.le_checked_end_date
			
				$modal_diff_33_sql
			order by
				z.province_name asc,
				c.le_checked_datetime desc
				
			
			
		
		";
		/*
		
		*/
		//echo $sql;
		$the_result = mysql_query($sql);
		
		
		//echo mysql_num_rows($the_result);
	
	
	?>
	
	<div align=center style="padding-top: 20px; font-size: 24px;">
	
		
	
		<font id="hhjjkk_1" style="display: none;" >ทั้งหมด <b><?php echo number_format(mysql_num_rows($the_result),0);?></b> รายการ - <a href="#" style="font-weight: normal; font-size: 24px; text-decoration: underline;" onClick="$('#the_34_exit_table').toggle();$('#hhjjkk_1').toggle(); $('#hhjjkk_2').toggle();return false;">คลิกที่นี่เพื่อแสดงรายการ</a></font>
		
		<font id="hhjjkk_2" >ทั้งหมด <b><?php echo number_format(mysql_num_rows($the_result),0);?></b> รายการ - <a href="#" style="font-weight: normal; font-size: 24px; text-decoration: underline;" onClick="$('#the_34_exit_table').toggle();$('#hhjjkk_1').toggle(); $('#hhjjkk_2').toggle(); return false;">คลิกที่นี่เพื่อซ่อนรายการ</a></font>
		
	</div>
	
	
	<div id="the_34_exit_table" class="table-responsive" >
		<table class="table" id="dashboard_end_date">
			<thead class="thead-light">
				<tr>
				
					<th scope="col"><div align=center>สถานประกอบการ</div></th>					
					<th scope="col"><div align=center>ปี</div></th>
					<th scope="col"><div align=center>เลขบัตรประชาชน</div></th>
					<th scope="col"><div align=center>ชื่อ-สกุล/อายุ </div></th>
					<th scope="col"><div align=center>วันที่ออกจากงาน(ระบบจ้างงาน)</div></th>
					
					<th scope="col"><div align=center>วันที่ออกจากงาน(ประกันสังคม)</div></th>
					<th scope="col"><div align=center>วันที่ตรวจสอบข้อมูล/สถานะที่เปลี่ยน ณ วันที่ตรวจสอบ</div></th>
					<th scope="col"><div align=center>สถานะปัจจุบัน</div></th>
					
				</tr>
			</thead>
			<tbody>
			
				
				
				<?php		
				
					
					//yoes 20230108
					$last_province = "";
					
					$numnum_array = array();
								
					while ($the_row = mysql_fetch_array($the_result)) {
				
						$current_province = $the_row["province_id"];
						
						
						if($current_province != $last_province && $show_all_provinces){
							
							$numnum_array[$the_row[province_id]]["key"] = $the_row[province_id];
							
							?>
							
								<script>
									$('#the_34_exit_table tbody tr.member_<?php echo $last_province;?>').hide();
								</script>
							<?php
							
							$count_provice_sql = "
								
								select
									
									count(*)
								from
									lawful_employees_sso_end_date c
										
										left join
											lawful_employees a
											on
											a.le_id = c.le_id
									
										join
											lawfulness b
											on
											c.le_origin_cid = b.cid
											and
											c.le_origin_year = b.year					
										
										join
											company d
											on
											b.cid = d.cid
										join
											provinces z
												on
												d.province = z.province_id
											
								where
									c.le_origin_end_date != c.le_checked_end_date
								
									and
									z.province_id = '".$the_row[province_id]."';
							
							";
							
							
							$numnum = getFirstItem($count_provice_sql)*1;
							
				?>				
						<tr>
							
							<td colspan=3>
								<div align=left>
									<font style="font-weight: normal; font-size: 24px; text-decoration: xx;" >
										<?php echo $the_row[province_name];?>
									</font>
								</div>
							</td>
							<td colspan=2>
								<div align=right>
									
									
									
									<a href="#" style="font-weight: normal; font-size: 24px; text-decoration: underline;" 
									onClick="$('#the_34_exit_table tbody tr.member_<?php echo $current_province;?>').toggle(); return false;">
									
									
										<span id="numnum_<?php echo $the_row[province_id];?>"><?php echo number_format($numnum,0) ?></span>
									</a> 
									
									<font style="font-weight: normal; font-size: 24px; text-decoration: xx;" >
											รายการ
									</font>
								</div>
							</td>
							<td colspan=3>
							</td>
							
						</tr>
							
				
				
				<?php
				
						
						}
						
					//yoes 20210414
					//hide some redundant rows
					
					$do_hide_the_row = 0;
					
					
					
					if(
						
							//hide statuses that already match
							// == show status that is unmatched
							
							/*(
								$the_row[le_changed_lawfulStatus] == $the_row[LawfulStatus]								
								&&
								$the_row[le_changed_lawfulStatus] != 0
								&&
								$the_row[LawfulStatus] != 0
								
							)*/
							
							
							// Hide row if changed back
							
							//||
							
							(
							
								//$the_row[le_origin_lawfulStatus] == 1
								//&&
								//$the_row[le_changed_lawfulStatus] != 1
								//&&
								$the_row[LawfulStatus] == 1
								&&
								substr($the_row[le_checked_datetime],0,10) < date('Y-m-d', strtotime('-7 days'))
							
							)
							
							
							
							// OR
							// show age that more than 60
							// and is last checked within 7 days ago
							
							||
							
							(
							
								(
									$the_row[le_age] > 60
								)
								&&
								substr($the_row[le_checked_datetime],0,10) < date('Y-m-d', strtotime('-7 days'))
							)
							
							
							// OR
							// show le_employees that is deleted
							// and is last checked within 7 days ago
							
							||
							
							(
							
								(
									$the_row[le_cid] == ""
								)
								&&
								substr($the_row[le_checked_datetime],0,10) < date('Y-m-d', strtotime('-7 days'))
							)
							
						
						
						
						){
							
							$do_hide_the_row = 1;				
				
						}
				
				if(!$do_hide_the_row){
						
					//yoes 20230115
					$numnum_array[$the_row[province_id]]++;
					
					
				?>
				
				<tr class="member_<?php echo $current_province;?>">
					<td><div align=center>
						<a href="organization.php?id=<?php echo $the_row[CID];?>&focus=lawful&year=<?php echo $the_row[Year];?>">
							<?php echo $the_row[CompanyNameThai];?> 
						</a><br><?php echo $the_row[province_name];?> 
					</div></td>
					<td><div align=center><?php echo $the_row[Year]+543;?></div></td>
					<td><div align=center>
						
						<a href="organization.php?id=<?php echo $the_row[CID];?>&focus=lawful&year=<?php echo $the_row[Year];?>&leid=<?php echo $the_row[le_id];?>&le=le">
						
							<?php echo $the_row[le_code];?>
						
						</a>
						
						</div></td>
					<td><div align=center><?php echo $the_row[le_origin_name];?>
						
						<?php
						
						//print_r($the_row);
						//echo "cid: '" . $the_row[le_cid] . "'";

						if($the_row[le_age]){
							
							echo "<br>อายุ ";
							
							if($the_row[le_age] > 60){
								echo "<b><font color=blue>";
									echo $the_row[le_age];
								echo "</font></b>";
							}else{
									echo $the_row[le_age];
							}

							echo " ปี";
							
						}	?>
						
					</div></td>
					<td><div align=center><?php echo $the_row[le_origin_end_date]=='0000-00-00'?"---":formatDateThai($the_row[le_origin_end_date]);?></div></td>
					
					<td><div align=center><?php 
					
						if($the_row[le_checked_end_date] == "9999-12-31"){
							if($the_row[le_age] <= 60){
								echo "<font color=red>ไม่พบการทำงานที่สถานประกอบการนี้</font>";
							}else{
								echo "<font color=>ไม่พบการทำงานที่สถานประกอบการนี้</font>";
							}
						}else{
							echo formatDateThai($the_row[le_checked_end_date]);
						}
						
						?></div></td>					
					<td><div align=center>
							<?php echo formatDateThai($the_row[le_checked_datetime],1,1);?>
							
							<?php 
								//yoes 20210322 -- do update to actual db
								
								//echo "($the_row[le_checked_end_date] != $the_row[le_end_date])";
								
								if(	
									$the_row[le_checked_end_date] != $the_row[le_end_date] 
									//&& $the_row[le_id] == 394603 
									&& $the_row[le_age] < 60
									&& !$the_row[le_changed_lawfulStatus]
									&& $the_row[le_checked_end_date] > $the_row[le_start_date]
									//--  && $the_row[le_checked_end_date] != "9999-12-31"
									){
									
									if($the_row[le_checked_end_date] == "9999-12-31"){
										
										//$update_sql = "";
										
										
										//do delete
										//do update
										/*$update_sql = "
										
											delete from
												lawful_employees
											
											where
												le_id = '".$the_row[le_id]."'
												and
												1=0
											limit 
												1
										
										";*/
										
										//yoes 20210323
										//dont do delete => do mark as extra instead
										$update_sql = "
											
											replace into
												lawful_employees_meta (

													meta_leid
													, meta_for
													, meta_value
												
												)values(
												
													'".$the_row[le_id]."'
													, 'is_extra_33'
													, '1'
												
												
												)				
										
											";
										
									}else{
										
										//do update
										$update_sql = "
										
											update
												lawful_employees
											set
												le_end_date = '".$the_row[le_checked_end_date]."'
												
											where
												le_id = '".$the_row[le_id]."'
											limit 
												1
										
										";
										
									}
									
									
									
									//echo $update_sql;
									
									//keep log first,
									//lawfullness log
									doLawfulnessFullLog(1, $the_row[LID], "modal_diff_lawful_employees_end_date");
									// le log
									//echo $the_row[le_id];
									doLawfulEmployeesFullLog(1, $the_row[le_id], "modal_diff_lawful_employees_end_date");
									// before/after log
									
									
									
									
									//do full log before update									
									mysql_query($update_sql);// or die(mysql_error());									
									//also do lawfulness updates
									resetLawfulnessByLID($the_row[LID]);
										/**/
										
									$new_status = getFirstItem("select lawfulStatus from lawfulness where lid = '".$the_row[LID]."'");
										
									
									$update_sql = "
										
										update
											lawful_employees_sso_end_date
										set
											le_changed_lawfulStatus = '$new_status'
										where
											le_id = '".$the_row[le_id]."'
											
									
									";
									
									mysql_query($update_sql);// or die(mysql_error());
									
									$the_row[le_changed_lawfulStatus] = $new_status;
									$the_row[LawfulStatus] = $new_status;
									
								}
								
							
							?>
							
							
							<br><?php echo getLawfulImage($the_row[le_origin_lawfulStatus]);?>
							<!-- -->
							<?php if($the_row[le_changed_lawfulStatus]){ ?>
								-> <?php echo getLawfulImage($the_row[le_changed_lawfulStatus]);?>
							<?php } ?>
							
						</div></td>
						
						<td><div align=center>
							<?php echo getLawfulImage($the_row[LawfulStatus]);?>
						</div>
						</td>
					
					
					
					
				</tr>
				
				
					<?php
					
						}
					
					?>
						
				<?php 
				
					$last_province = $the_row[province_id];
				
				} 
				
				
				//yoes 20230108 - close final loop
				
				if($show_all_provinces){
						
					//for($iii = 0; $iii < count($numnum_array); $iii++){
					foreach ($numnum_array as $key => $value) {
						
					?>
						
						<script>
							$('#numnum_<?php echo $key;?>').html($value);
						</script>
					
					
					<?php
						
					}
					
					?>
					
						<script>
							$('#the_34_exit_table tbody tr.member_<?php echo $last_province;?>').hide();
						</script>
						
						
				
				<?php
				
					}
				
				?>
			
				
				
				
				
			</tbody>
		</table>
	</div>
	
	<?php //print_r($numnum_array); ?>
	
	
</div>
<script>
	$('#dashboard_end_date').DataTable();
</script>

</body>
</html>