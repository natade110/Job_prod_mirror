<?php

	include "db_connect.php";
	include "session_handler.php";
		
	$skip_html_head = 1;
	
	include "header_html.php";
	
?>
<body>

<div class="modal-body" >
	<h4><font color=blue>สถานประกอบการที่ไม่ได้ดำเนินกิจการแล้ว (ข้อมูลจากกรมพัฒนาธุรกิจการค้า)</font></h4>
	
		
	<div class="table-responsive">
		<table class="table" id="zero_config">
			<thead class="thead-light">
				<tr>
				
					<th scope="col"><div align=center>เลขที่บัญชีนายจ้าง</div></th>					
					<th scope="col"><div align=center>ชื่อสถานประกอบการ</div></th>
					
					<th scope="col"><div align=center>สถานะของกิจการ(ระบบจ้างงาน)</div></th>
					
					<th scope="col"><div align=center>สถานะของกิจการ(กรมพัฒนาธุรกิจการค้า)</div></th>
					<th scope="col"><div align=center>วันที่ตรวจสอบข้อมูล</div></th>
					
				</tr>
			</thead>
			<tbody>
			
				
				
				<?php		
				
					//select lawful_employees thats not match with sso end_date
					$sql = "
					
						select
							b.status as dbd_status
							, a.CompanyCode
							, a.CompanyNameThai
							, a.CID
							, b.last_update_date
						from
							company a
								join
									cs_company_dbd b
									on
									a.taxid = b.taxid
									and
									length(a.TaxID) = 13
						where
							a.Status = 1
							and
							b.status != 'ยังดำเนินกิจการอยู่'
							and
							b.status not like '%ดำเนินกิจการ%'
							and
							trim(b.status) != ''
						order by
							b.last_update_date desc
						
					
					";
				
					//echo $sql;
					$the_result = mysql_query($sql);
								
					while ($the_row = mysql_fetch_array($the_result)) {
				
				?>				
					
				<tr>
					<td><div align=center>
						<a href="organization.php?id=<?php echo $the_row[CID];?>&focus=general&year=<?php echo $the_row[Year];?>">
							<?php echo $the_row[CompanyCode];?>
						</a>
					</div></td>
					<td><div align=center>
						<a href="organization.php?id=<?php echo $the_row[CID];?>&focus=general&year=<?php echo $the_row[Year];?>">
							<?php echo $the_row[CompanyNameThai];?>
						</a>
					</div></td>
					<td><div align=center>ดำเนินกิจการ</div></td>
					<td><div align=center><?php echo $the_row[dbd_status];?></div></td>
									
					<td><div align=center><?php echo formatDateThai($the_row[last_update_date],1,1);?></div></td>
					
				</tr>
						
				<?php } ?>
			
				
				
				
				
			</tbody>
		</table>
	</div>
	
	
</div>

<script src="./assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="./assets/extra-libs/datatables.net-bs4/js/dataTables.responsive.min.js"></script>

<script>
	$('#zero_config').DataTable();
</script>


</body>
</html>