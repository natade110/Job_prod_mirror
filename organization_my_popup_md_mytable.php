<?php			

include "db_connect.php";
include "scrp_config.php";
include "session_handler.php";

$post_row = $_POST;


//print_r($_POST); //exit();



$this_id = $post_row[this_id];
$this_lawful_year = $post_row[this_lawful_year];
$is_read_only = $post_row[is_read_only];

//$this_id = 71462;
//$this_lawful_year = 2020;


if($post_row[search_string]){
	
	$search_string = doCleanInput($post_row[search_string]);
	//echo $search_string;
	
	$search_filter = " and (a.le_code like '%$search_string%' or a.le_name like '%$search_string%')";
	
}

if(!$this_id || !$this_lawful_year){
	
	exit();
}

$this_lid = getFirstItem("select lid from lawfulness where cid = '$this_id' and year = '$this_lawful_year'");

/**/

?><?php /*<script
				  src="https://code.jquery.com/jquery-3.4.1.min.js"
				  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
				  crossorigin="anonymous"></script> */ ?>
				  
					
				  
				  
					<table id="myTable" bgcolor="#FFFFFF" width="1000" border="1" align="center" cellpadding="3" cellspacing="0" style="border-collapse:collapse;   ">                                      
					
									
                                      
                                      <?php if($sess_accesslevel == 5 || $sess_accesslevel == 18 || $is_read_only){?>
									  <tr>
										<td>
											  <div align="center">
												  <table>
														 <tr>
															<td colspan="2">
															
															<input name="" type="button" value="ปิดหน้าต่าง" data-dismiss="modal" onClick="fadeOutMyPopup('my_popup'); return false;" />
															
															</td>
														</tr>
												</table>
												</div> 
											</td>
										</tr>
                                        <?php }?>
                                      

                                    
                                    
                                    
                                    <?php
									
										
					
						
										//YOES 20160615
										if($is_merged){
											$this_lawful_year += 1000;	
										}
										
										
						
										
						
											
										$get_org_sql = "
										
														SELECT 
															a.*
															
															, b.meta_leid as child_meta_leid
															, b.meta_for as child_meta_for
															, b.meta_value as child_meta_value
															
															, c.meta_leid as parent_meta_leid
															, c.meta_for as parent_meta_for
															, c.meta_value as parent_meta_value
															
															, d.meta_for as sso_failed
														FROM 
														
															lawful_employees a
																left join
																	lawful_employees_meta b
																		on a.le_id = b.meta_leid and b.meta_for = 'child_of'
																left join
																	lawful_employees_meta c
																		on a.le_id = c.meta_value and c.meta_for = 'child_of'
																		
																left join
																	lawful_employees_meta d
																		on a.le_id = d.meta_leid and d.meta_for = 'sso_failed'
														
														
														where
															
															(
																le_cid = '$this_id'
																and 
																le_year = '$this_lawful_year'
															)
																															
															$search_filter
															
														order by 
															le_id asc
															
														";
										
										//yoes 20211127
										//this should be much faster
										//this too is so slow
										//if use simple like this = fast
										/*$get_org_sql = "
										
														SELECT 
															a.*
															
															
														FROM 
														
															lawful_employees a
																
														
														
														where
															
															(
																le_cid = '$this_id'
																and 
																le_year = '$this_lawful_year'
															)
																
															
														order by 
															le_id asc
															
														";*/
										//echo $get_org_sql; //exit();
														
										//yoes 20160118 --> extra lawful_employees
										$get_org_sql_extra = "
										
														SELECT 
															*
															, '1' as is_extra_row
														FROM 
														
															lawful_employees_extra
														
														
														where
															le_cid = '$this_id'
															and le_year = '$this_lawful_year'
															
														order by le_id asc
														";
														
											
										
										
										
										//echo $get_org_sql;										
										$org_result = array();
										array_push($org_result,mysql_query($get_org_sql));
										
										if($sess_accesslevel != 4){
											//yoes 20160118 -- non company see extra rows
											array_push($org_result,mysql_query($get_org_sql_extra));
										}
										
										
										//$total_records = 1;
										
										
										$post_row_parent_array = array();										
										$post_row_child_array = array();
										$post_row_array = array();	
										
										
										
										for($result_count = 0; $result_count < count($org_result); $result_count++){
										
											while ($post_row = mysql_fetch_array($org_result[$result_count])) {
																						
												//for parent -> push to parent
												if(!$post_row['child_meta_value']){
													array_push($post_row_parent_array,$post_row);
												}else{
													
													//for child -> push to child
													$post_row_child_array[$post_row['child_meta_leid']] = $post_row;
												
												}
												
												
																						
											
											} //end while $post row
										}//end for result count 
									
									
										
									
									//print_r($post_row_parent_array);
									//print_r($post_row_child_array);
									
									//yoes 20180119									
									//sort array by group
									for($result_count = 0; $result_count < count($post_row_parent_array); $result_count++){
										
										$group_count++; //group count for painting colors
										$post_row_parent_array[$result_count]['group_count'] = $group_count;
										array_push($post_row_array,$post_row_parent_array[$result_count]);
										
										$this_child = $post_row_parent_array[$result_count]['parent_meta_leid'];
										while($this_child){
											
											$post_row_child_array[$this_child]['group_count'] = $group_count;
											array_push($post_row_array,$post_row_child_array[$this_child]);
											$this_child = $post_row_child_array[$this_child]['parent_meta_leid'];
											
											
										}
										
									}
									
									
									$total_records = 1;
									
									
									include_once("organization_33_detailed_rows_js.php");
									
									
									
									if($total_records == 1){ // see if we have to draw headers
									?>

										<style>

											.blink_me {
											  animation: blinker 1s linear infinite;
											}

											@keyframes blinker {
											  50% {
												opacity: 0.3;
												/*color: #000000;*/
												/*font-weight: bold;*/
											  }
											}
										</style>
										<thead>
											<tr bgcolor="#efefef">
											  <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
											  <td><div align="center">ชื่อ</div></td>
											  <td><div align="center">เพศ</div></td>

											  <td><div align="center">อายุ</div></td>
											  <td><div align="center">เลขที่บัตรประชาชน</div></td>
											  <td width="140px"><div align="center">ลักษณะความพิการ</div></td>

											  <td><div align="center">เริ่มบรรจุงาน </div></td>
											  <td><div align="center">ค่าจ้าง </div></td>
											  <td ><div align="center">ตำแหน่งงาน</div></td>
											  <td ><div align="center">การศึกษา</div></td>
											  <td ><div align="center">ไฟล์แนบ</div></td>
											  <?php if($sess_accesslevel != 5 && !$is_read_only && !$case_closed ){?>
											  <td><div align="center">ลบข้อมูล</div></td>
											  <td><div align="center">แก้ไขข้อมูล</div></td>
											  <?php }?>
											</tr>
										</thead>
										<tbody>

									<?php

									}			//ends $total_records == 1	
									
									
									
									for($result_count = 0; $result_count < count($post_row_array); $result_count++){
										
										$post_row = $post_row_array[$result_count];
										
										//yoes 20211018 - move the thing here
										
										
										?>
	
									
											
											<tr >
												
												<td colspan="13" id="td_<?php echo $post_row[le_id]?>">
													<div align="center">...* กำลังดึงข้อมูล <?php echo $post_row[le_id]?> *...</div>
													
													<?php // echo json_encode($post_row);?>
													
												</td>
												
												

											</tr>
											
											<?php //include "organization_33_detailed_rows.php" ;?>
											
										
										
	
									<?php
										// include "organization_33_detailed_rows.php";
										//yoes 20211019 -> injecting other vars
										$post_row[this_id] = $this_id;
										$post_row[this_lawful_year] = $this_lawful_year;
										$post_row[this_lid] = $this_lid;
										
										//echo "***".$this_lid."****";
																					
										
										//$le_vue_call .= " getLeTds_md($post_row[le_id],".json_encode($post_row).");";
										//$le_vue_call .= " getLeTds($post_row[le_id],".json_encode($post_row).");";

                                        echo "<script>";
                                        echo " getLeTds($post_row[le_id]," . json_encode($post_row) . ");";
                                        echo "</script>";


										$total_records++;
										
									}
									
									
									
									?>
									
									</tbody>
									
										
									
									
									
									
									<?php 
									
									//print_r($post_row_array);
									
									
									?>
										                                    
                                    
                                    
                                    <?php if($total_records == 1 && $sess_accesslevel != 4 && $sess_accesslevel != 5 && $sess_accesslevel != 18 && !$is_read_only && !$case_closed){?>
                                     <tr >
                                    	<td colspan="13">
                                        
		                                    <div align="center" id="import_previous_33">
                                            	<form method="post" action="scrp_import_last_lawful_employee.php"  onsubmit="return confirm('ต้องการนำเข้าข้อมูลคนพิการที่ได้รับเข้าทำงานจากปีที่แล้วมาใส่ในปีนี้?');">
                                                	<input name="le_year" type="hidden" value="<?php echo $this_lawful_year;?>" />
                                         			<input name="le_cid" type="hidden" value="<?php echo $this_id; ?>" />
 		                                           	<input name="import_last_le" type="submit" value="นำเข้าข้อมูลจากปีที่แล้ว" />
                                              	</form>  
                                            </div>
                                    	</td>
                                     </tr>
                                    <?php }?>
                                    
                                </table><!-- ends myTable-->
	

<script id="vue_my_popup_md">

	 //$("#myTable_div").empty();

	/*var my_popups = new Vue({
	  el: '#my_popup',
	  data: {
		xxyyzz: 0
		<?php echo $le_vue_data;?>
		  
		  }
	})*/
	
	
	/*function getLeTds_md(id, json, isFade=false){
								
		$.ajax({
		  method: "POST",
		  url: "organization_33_detailed_rows_modal.php",
		  data: json
		})
		  .done(function( html ) {				
			//alert(html);
			//my_popup["content_"+id] = "<tr><td>--"+html+"--</td></tr>";
			//$("#content_"+id).html("<tr><td>--"+html+"--</td></tr>");
			//$("#content_"+id).append(html);
			//$("#content_"+id).html("<tr><td>--ทดสอบ--</td></tr>");
			//$("#content_"+id).html("<tr><td>--"+html+"--</td></tr>");			
			if(isFade){
				// backup cell prop
				var rowColor = $("#"+id+"_main").attr('bgcolor');
				var rowNo = $("#"+id+"_main").find("td:eq(0)").html();				
				
				$("#"+id+"_main").replaceWith(html);

				// recover cell prop
				$("#"+id+"_main").attr('bgcolor',rowColor);
				$("#"+id+"_main").find("td:eq(0)").html(rowNo);

				$("#"+id+"_main").hide();
				$("#"+id+"_main").fadeIn("slow");
			} else  {
				$("#td_"+id).parent().replaceWith(html);
			}
			
			
			$("#import_previous_33").hide();

		  });
		
		//my_popup["content_"+id] = "<tr><td>"+id+"</td></tr>";

	}*/

	//$("#span_le_row_437642").html('<td colspan="13"><div align="center">...* กำลังดึงข้อมูล  *...</div></td>');
	 

	<?php //echo "$le_vue_call"; ?>
</script>

<?php if(1==0){?>
<script src="./assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="./assets/extra-libs/datatables.net-bs4/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
    //$('#myTable').DataTable();
	$('#myTable').DataTable({
		"ordering": false,
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bAutoWidth": false
	});
} );
</script>
<?php } ?>