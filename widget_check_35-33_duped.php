 <?php 							  
		//yoes 20160301 only check dupe on non-dummy rows
		//yoes 20171130 and card_id !== 0
		if(!$post_row["curator_is_dummy_row"] && $this_curator_idcard*1 != 0){								
  ?>
	  
	   <?php 
	   
	   
							  
								//see if this le_id already in another ID
								
								
								$sql = "select 
											* 
										from 
											lawful_employees a
											
												join
												company b
												on
												le_cid = cid												
												and 
												b.CompanyTypeCode < 200
											
											
										where 
											le_code = '$this_curator_idcard'
											and 
											le_year = '$this_lawful_year'
											and
											le_is_dummy_row = 0
										";
							  
								if($this_lawful_year >= 2018 && $this_lawful_year < 2500){
			
									//check if there are other LEID within own time perios
									
									$dupe_curator_start_date = $post_row["curator_start_date"];
									$dupe_curator_end_date = $post_row["curator_end_date"];
									
									
									//yoes 20210204
									if(curatorIs6MonthsTraining($post_row[curator_id])){
										$dupe_curator_start_date = $this_lawful_year."-01-01";
										$dupe_curator_end_date = $this_lawful_year."-12-31";
									}
									
									
									if($dupe_curator_end_date == "0000-00-00"){
										$dupe_curator_end_date = "2500-01-01";
									}
									
									//yoes 20200617 -- add condiction for (le_end_date = '0000-00-00') (still wodking)
									$sql .= " 
									
										and
										(
											(le_start_date BETWEEN '".$dupe_curator_start_date."' AND '".$dupe_curator_end_date."')
											or
											(le_end_date BETWEEN '".$dupe_curator_start_date."' AND '".$dupe_curator_end_date."')
											or
											(le_start_date <= '".$dupe_curator_start_date."' and le_end_date = '0000-00-00')
											or
											(le_start_date <= '".$dupe_curator_start_date."' AND le_end_date >= '".$dupe_curator_end_date."')
											
										)
										";
								}
																
								//echo $sql;
							  
								$le_result = mysql_query($sql);
								
								while ($le_row = mysql_fetch_array($le_result)) {
									
									$have_duplicate_33 = 1; 
									$have_duplicate_35 = 1; 
									
									if($sess_accesslevel == 1){
										//echo $sql;
									}
							
							  
							  ?>
							  
							  
								 <?php 
						
									//yoes 20151118 -- make it so company can see link
									if($sess_accesslevel == 4){
									
									?>
									
									
									
										
                                        
                                        
                                        <?php 
                        
										//yoes 20160503 --- more detailed message
										// $this_cid comes from organization.php
										if($this_cid == $le_row["le_cid"]){					
											?>
											
											 <font color="#CC3300"><strong>! มีการใส่ข้อมูลคนพิการคนนี้ลงไปใน ม.33 แล้ว</strong></font>
											
											<?php
											
										}else{
											
											?>
											
											 <span style="color:#990000" title="กรุณาติดต่อเจ้าหน้าที่เพื่อตรวจสอบข้อมูลเพิ่มเติม">
                                            ! คนพิการนี้มีการทำมาตรา 33 ในบริษัทอื่นแล้ว <br />
                                            </span>
											
										<?php
											
										}
									
									?>
								  
									
									
									<?php }else{ ?>
								  
									
                                      
                                      
                                        <?php 
                        
										//yoes 20160503 --- more detailed message
										if($this_cid == $le_row["le_cid"]){					
											?>
											
											 <font color="#CC3300"><strong>! มีการใส่ข้อมูลคนพิการคนนี้ลงไปใน ม.33 แล้ว</strong></font>
											
											<?php
											
										}else{
											
											?>
											
											  <div>
                                                <a href="organization.php?id=<?php echo $le_row["le_cid"];?>&le=le&focus=lawful&year=<?php echo $le_row["le_year"];?>" style="color:#990000; text-decoration:underline;" target="_blank">! พบในมาตรา 33 ของสถานประกอบการอื่น</a>
                                              </div>
											
										<?php
											
										}
									
									?>
								  
								  
								  <?php }?>
							  
							  <?php }?>
							  
	  
	   <?php 							  
			//yoes 20160301 only check dupe on non-dummy rows
			} //end if(!$post_row["curator_is_dummy_row"]){								
	  ?>                