<?php

	include_once "db_connect.php";
	include_once "session_handler.php";
		
	$skip_html_head = 1;
	
	include "header_html.php";
	
?>
<body>

<div class="modal-body">
	<h4><font color=blue>รายการลูกจ้างคนพิการที่มีการออกจากงานแล้ว (ข้อมูลจากประกันสังคม)</font></h4>
	
	
	
	
	<div class="table-responsive">
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
				
					/*
					
					update
						lawful_employees_sso_end_date a
							join
								lawful_employees b
								on
								a.le_id = b.le_id
							join
								lawfulness c
								on
								b.le_cid = c.cid
								and
								b.le_year = c.year
								
					set
						a.le_origin_cid = b.le_cid
						, a.le_origin_year = b.le_year
						, a.le_origin_name = b.le_name
						, a.le_origin_lawfulStatus = c.lawfulStatus
					
					*/
				
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
						
							
						
						
					
					";
					/*
					
					*/
					//echo $sql;
					$the_result = mysql_query($sql);
								
					while ($the_row = mysql_fetch_array($the_result)) {
				
				?>				
					
				<tr>
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
						
						<?php if($the_row[le_age]){
							
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
									&& $the_row[le_age] <= 60
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
						
				<?php } ?>
			
				
				
				
				
			</tbody>
		</table>
	</div>
	
	
</div>
<script>
	$('#dashboard_end_date').DataTable();
</script>

</body>
</html>