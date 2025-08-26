<?php

	include "db_connect.php";
	include "session_handler.php";
		
	$skip_html_head = 1;
	
	if($sess_accesslevel != 1){		
		header("location: index.php");exit();		
	}
	
	include "header_html.php";
	
?>
<body>

<div class="modal-body">
	<h4><font color=blue>Log การใช้งานระบบ</font></h4>
	
	
	
	
	<div class="table-responsive">
		<table class="table">
			<thead class="thead-light">
				<tr>
				
					<th scope="col"><div align=center>วัน-เวลาที่เรียกใช้งาน</div></th>				
					<th scope="col"><div align=center>ชื่อผู้ใช้งาน</div></th>
					<th scope="col"><div align=center>IP</div></th>
					<th scope="col"><div align=center>Script ที่เรียกใช้งาน</div></th>
					<th scope="col"><div align=center>GET variables</div></th>
					
					
				</tr>
			</thead>
			<tbody>
			
				
				
				<?php		
				
					//select lawful_employees thats not match with sso end_date
					$sql = "
					
						select
							*
						from
							usage_full_log_ejob a
							
						where
							access_datetime > NOW() - INTERVAL 15 MINUTE
							and
							script_name != 'modal_current_online.php'
							and
							script_name != 'modal_current_online_ejob.php'
							
						order by
							access_datetime 
						desc
					
					";
				
					//echo $sql;
					$the_result = mysql_query($sql);
					
					//print_r($the_result);
								
					while ($the_row = mysql_fetch_array($the_result)) {
				
				?>				
					
				<tr>
					<td><div align=center><?php echo $the_row[access_datetime];?></div></td>					
					
					<td><div align=center><?php echo $the_row[user_name];?></div></td>
					<td><div align=center><?php echo $the_row[user_ip];?></div></td>
					<td><div align=center><?php echo $the_row[script_name];?></div></td>
					<td><div align=center><?php echo $the_row[script_get];?></div></td>
					
					
					
				</tr>
						
				<?php } ?>
			
				
				
				
				
			</tbody>
		</table>
	</div>
	
	
</div>


</body>
</html>