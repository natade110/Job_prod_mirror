<?php

if($sess_accesslevel == 1){
	//$starttime = microtime(true);
}?>


<table id="organization_35_details_table<?php echo $is_extra_table;?>" width="750" border="1" cellspacing="0" cellpadding="3" style="border-collapse:collapse; 

<?php if(1==1){?>display:none;<?php }?>" align="center">                        
                        
                        
                         <tr bgcolor="#efefef">
                             <td><a href="#" id="le"></a><div align="center">ลำดับที่</div></td>
                              <td><div align="center">ชื่อ-นามสกุล</div></td>
                              <td><div align="center">เพศ</div></td>
                              <td><div align="center">อายุ</div></td>
                              <td><div align="center">เลขที่บัตรประชาชน</div></td>
                              <td><div align="center">ผู้ใช้สิทธิเป็น</div></td>
                              <td><div align="center">เลขที่สัญญา</div></td>
                              <td><div align="center">วันเริ่มต้นสัญญา-วันสิ้นสุดสัญญา</div></td>
                              <td><div align="center">ระยะเวลา</div></td>
                              <td><div align="center">กิจกรรม</div></td>
                              <td><div align="center">มูลค่า (บาท)</div></td>
                              <td><div align="center">รายละเอียด</div></td>
                              
                               <?php 
							   
							   
							   //echo "$submitted_company_lawful && $sess_accesslevel != 5 && $sess_accesslevel != 8 && !$is_read_only && (!$case_closed || $is_extra_table";
							   
							   //yoes 20160318 -- fix this condition for compnay
							   if($sess_accesslevel != 4){ // non-company never submit company lawfu...
								   $company_lawful_submitted_for_m35 = 0;
							   }else{
									$company_lawful_submitted_for_m35 = 1;   
							   }
							   
							   if(!$company_lawful_submitted_for_m35  && $sess_accesslevel != 5 && !$is_read_only && (!$case_closed || $is_extra_table)){
							   ?>
                             
                              <td><div align="center">ลบข้อมูล</div></td>
                              <td><div align="center">แก้ไขข้อมูล</div></td>
                              <?php }?>
                              
                        </tr> 
                        
                                    
                        <?php
                       
                            //get main curator
							//yoes 20181107 --> on get parents curator (no_meta)
                            $sql = "
									select 
										* 
									from 
										$curator_table_name 
									where 
										curator_lid = '".$lawful_values["LID"]."' 
									and 
										curator_parent = 0 
										
									and
										curator_id not in (
										
											select
												meta_curator_id
											from
												curator_meta
											where
												meta_for = 'child_of'
												and
												meta_value != 0
										
										)
										
										
									order by 
										curator_id asc";
                            //echo $sql;
							

                            
                            $org_result = mysql_query($sql);
                            $total_records = 0;
							
							$m35_rows_array = array();
							
                            while ($post_row = mysql_fetch_array($org_result)) {

								array_push($m35_rows_array, $post_row);

							}
							
							//print_r($m35_rows_array);
							
							
							for($i_m35 = 0; $i_m35 < count($m35_rows_array) ; $i_m35++){
                                
								$post_row = $m35_rows_array[$i_m35];
								
								//print_r($post_row);
								
								$total_records++;
                        
								if($the_bg == "bgcolor='#ffffff'"){
									$the_bg = "bgcolor='#F8F8F8'";
								}else{
									$the_bg = "bgcolor='#ffffff'";
								}
								
								
																
								
								//start render row								
								include "organization_35_detailed_rows.php";

								
								
								
								$child_curator_id_array = array();
								$child_curator_id_array = getChildrenOfCurator($post_row[curator_id]);
																
								
								//print_r($child_curator_id_array);
								
								for($i_child = 0; $i_child < count($child_curator_id_array); $i_child++){
									
									$post_row = getFirstRow("select * from curator where curator_id = '".$child_curator_id_array[$i_child]."'");
									include "organization_35_detailed_rows.php";
									
								}
                                
							
						
							}//end loop for curator 
					   
					   
					   ?>
                        
                      </table>
                      
<?php 

if($sess_accesslevel == 1){
	//$endtime = microtime(true);
	//$timediff = $endtime - $starttime;
	
	//echo $timediff;
}

?>