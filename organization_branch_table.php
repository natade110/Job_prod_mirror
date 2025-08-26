<?php //reset sum emplioyees

	$sum_employees = 0;
?>

<table cellpadding="3" style="border-collapse:collapse;" border="1">
                                        <tr bgcolor="#9C9A9C" align="center">
                                             <td>
                                                <div align="center">
                                                    <span class="column_header">
                                                    รหัสสาขา
                                                    </span>
                                                </div>
                                             </td> 
                                            <td>
                                                <div align="center">
                                                    <span class="column_header">
                                                    ชื่อสาขา
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div align="center">
                                                    <span class="column_header">
                                                    จำนวน<?php echo $the_employees_word;?> <?php if($show_comparison){echo "<br>กรอกโดยสถานประกอบการ";}?> (คน)
                                                    </span>
                                                </div>
                                            </td>
                                            
                                            <?php if($show_comparison){ ?>
                                             <td>
                                                <div align="center">
                                                    <span class="column_header">
                                                    จำนวน<?php echo $the_employees_word;?> <br />จากประกันสังคม (คน)
                                                    </span>
                                                </div>
                                            </td>
                                            <?php }?>
                                            
                                        </tr>
                                        
                                        
                                        <?php
										
										
											//yoes 20151013 -> fix to show active branch only
											$get_branch_sql = "select * from 
																	company 
																where 
																	CompanyCode = '".$output_values["CompanyCode"]."' 
																
																	and
																	is_active_branch = 1
																
																order by BranchCode asc";
										
											$branch_result = mysql_query($get_branch_sql);
										
											
										
											//total records 
											$total_records = 0;
										
											while ($branch_row = mysql_fetch_array($branch_result)) {
												
												$branch_employees_origin = $branch_row["Employees"] ;
												
											
												$padding_left = 'style="';
												$is_selected = 0;
												
												if($branch_row["BranchCode"] > 0){
													$padding_left .= 'padding-left:20px;';
												}
												
												if($branch_row["BranchCode"]  == $output_values["BranchCode"]){
													$padding_left .= ' font-weight: bold; color:#009900;';
													$is_selected = 1;
												}
												
												$padding_left .= '"';
											
										?>
                                        
                                        
                                        	 <tr >
                                                 <td <?php echo $padding_left?> >
                                                    <?php echo $branch_row["BranchCode"];?>
                                                 </td> 
                                                <td <?php echo $padding_left?>>
                                                
                                                	<?php if(!$is_selected && $sess_accesslevel != 4){ //yoes 20151102 -- company cant see links!?>
	                                                	<a href="organization.php?id=<?php echo $branch_row["CID"];?><?php
                                                        
															if($show_all_tabs){
																echo "&all_tabs=1";
															}
														
														?>&year=<?php echo $this_year;?>" style="font-weight: normal;">
                                                    <?php }?>
                                                    
                                                    
                                                	    <?php echo $branch_row["CompanyNameThai"];?>
                                                    
                                                    <?php if(!$is_selected && $sess_accesslevel != 4){ //yoes 20151102 -- company cant see links!?>
	                                                	</a>
                                                    <?php }?>
                                                    
                                                    
                                                    
                                                 </td>
                                                 <td <?php echo $padding_left?>>
                                                 	<div align="right">
                                                    
                                                    
                                                    <?php 
													
													
													
													
													if($sess_accesslevel == 4 || $show_company_employees_company == 1){
													
														//yoes 20151021 -- company can edit this
														
														//also for company --> see if there are "temp" value in company_employees_company
														
														
														$temp_value = getFirstItem("
														
																select
																	employees
																from
																	company_employees_company
																where
																	cid = ".$branch_row["CID"]."
																	and
																	lawful_year = ".$this_year."
																
																
																");
																
														//override old data here
														
														if(strlen($temp_value) > 0){
														
															$branch_row["Employees"] = $temp_value;
														
														}
														
													}
													
													
													if($sess_accesslevel == 4 && !$submitted_company_lawful){	//yoes 20151102 -> disallow edit after submit
														
													?>
                                                        
                                                        <img id="cid_<?php echo $branch_row["CID"];?>_saving" src="decors/loading.gif" width="10" height="10" style="display: none;" />
                                                        
                                                        <input id="cid_<?php echo $branch_row["CID"];?>" type="text" style="width:50px; text-align:right;" value="<?php 
															
															
															
															echo $branch_row["Employees"];	
																													
															$sum_employees += $branch_row["Employees"];
															
															?>" onchange="doUpdateCompanyEmployees(<?php echo $branch_row["CID"];?>, <?php echo $this_year;?>);"/>
                                                        
                                                        	
                                                        
                                                       <?php
														
													}else{
														
													
													?>
                                                    
                                                    
														<?php 
                                                        
                                                        echo formatEmployee($branch_row["Employees"]);
                                                        $sum_employees += $branch_row["Employees"];
                                                        
                                                        ?>
                                                    
                                                    <?php 
													
													}
													
													?>
                                                    </div>
                                                 </td>
                                                 
                                                 
                                                 
                                                <?php if($show_comparison){ ?>
                                                 <td>
                                                    <div align="right">
                                                       <?php 
													   
													   	echo formatEmployee($branch_employees_origin);
														
														$sum_employees_origin += $branch_employees_origin;
														
														?>
                                                    </div>
                                                </td>
                                                <?php }?>
                                                
                                                
                                            </tr>
                                        
                                        
                                        <?php										
											
											}
										
										?>
                                        
                                        
                                        
                                        <?php 
										
											// yoes 20151116 
											// -- also for company user -> show a "just added companies" (if there are any)
											if($sess_accesslevel == 4 || $show_company_employees_company == 1){
										
										?>
                                        
                                        
											 <?php
                                            
                                            
                                                //yoes 20151013 -> fix to show active branch only
                                                $get_branch_sql = "select * from 
                                                                        company_company
                                                                    where 
                                                                        CompanyCode = '".$output_values["CompanyCode"]."' 
                                                                    
                                                                        and
                                                                        is_active_branch = 1
                                                                    
                                                                    order by BranchCode asc";
                                            
                                                $branch_result = mysql_query($get_branch_sql);
                                            
                                                
                                            
                                                //total records 
                                                $total_records = 0;
                                            
                                                while ($branch_row = mysql_fetch_array($branch_result)) {
                                                    
                                                    $branch_employees_origin = $branch_row["Employees"] ;
                                                    
                                                
                                                    $padding_left = 'style="';
                                                    $is_selected = 0;
                                                    
                                                    if($branch_row["BranchCode"] > 0){
                                                        $padding_left .= 'padding-left:20px;';
                                                    }
                                                    
                                                    if($branch_row["BranchCode"]  == $output_values["BranchCode"]){
                                                        $padding_left .= ' font-weight: bold; color:#009900;';
                                                        $is_selected = 1;
                                                    }
                                                    
                                                    $padding_left .= '"';
                                                
                                            ?>
                                            
                                            
                                                 <tr >
                                                     <td <?php echo $padding_left?> >
                                                        <?php echo $branch_row["BranchCode"];?>
                                                     </td> 
                                                    <td <?php echo $padding_left?>>
                                                    
                                                        <?php echo $branch_row["CompanyNameThai"];?>
                                                    
                                                    
                                                    
                                                    
                                                     <a title="<?php if($sess_accesslevel == 4){echo "แก้ไขข้อมูล";}else{echo "ดูข้อมูล";}?>" href="organization.php?id=<?php echo $this_cid;?>&bid=<?php echo doCleanOutput($branch_row["CID"]);?>&focus=<?php if($sess_accesslevel == 4){echo "general";}else{echo "input";}?>">
                                                    <img border="0" alt="" src="decors/create_user.gif" height="20">
                                                    </a>
                                                    
                                                    
                                                    <?php if($sess_accesslevel == 4){ //only company can delete this?>
                                                        ||
                                                    
                                                    	<a href="scrp_delete_branch.php?id=<?php echo doCleanOutput($branch_row["CID"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');"><img src="decors/cross_icon.gif" border="0" height="15" /></a>
                                                        
                                                       <?php }?>
                                                       
                                                       
                                                       
                                                        
                                                     </td>
                                                     <td <?php echo $padding_left?>>
                                                        <div align="right">
                                                                                                            
                                                        
                                                            <?php 
                                                        
															//yoes 20151116 -- disallow edit for new branch    
                                                            echo formatEmployee($branch_row["Employees"]);
                                                            $sum_employees += $branch_row["Employees"];
                                                            
                                                            ?>
                                                        
                                                       
                                                        </div>
                                                     </td>
                                                     
                                                     
                                                     
                                                    <?php if($show_comparison){ ?>
                                                     <td>
                                                        <div align="right">
                                                         0
                                                        </div>
                                                    </td>
                                                    <?php }?>
                                                    
                                                    
                                                </tr>
                                            
                                            
                                            <?php										
                                                
                                                }
                                            
                                            ?>
                                        
                                        
                                        <?php } //end if for company_company branches?>
                                        
                                        
                                        
                                        
                                        	<tr >
                                             <td>
                                               
                                             </td> 
                                            <td bgcolor="">
                                                <div align="right">
                                                   
                                                   <?php if($sess_accesslevel == 4){?>
                                                    จำนวนลูกจ้าง รวมทุกสาขา
                                                    <?php }else{?>
                                                    
                                                    รวม
                                                    
                                                    <?php }?>
                                                  
                                                </div>
                                            </td>
                                            <td>
                                                <div align="right">
                                                    <?php echo formatEmployee($sum_employees);?>
                                                </div>
                                                
                                                
                                            </td>
                                            
                                            
                                            
                                            <?php if($show_comparison){ ?>
                                                 <td>
                                                    <div align="right">
                                                       <?php 
													   
													   	echo formatEmployee($sum_employees_origin);
														
														?>
                                                    </div>
                                                </td>
                                                <?php }?>
                                        </tr>
                                        
                                        
                                        <?php if($sess_accesslevel == 4 && !$submitted_company_lawful){?>
                                        
                                        <tr >
                                             <td>
                                               
                                             </td> 
                                            <td bgcolor="">
                                               
                                            </td>
                                            <td>
                                                <div align="right">
                                                   <input id="exit" type="reset" name="form1:exit" value=" ปรับปรุงข้อมูล " onclick="window.location.href='organization.php?<?php echo $this_id;?>&focus=general';" style="width: 115px" />
                                                </div>
                                                
                                            </td>
                                        </tr>
                                        
                                        <script>
												
											function doUpdateCompanyEmployees(what, year){
												
												//alert(what); alert(year);
												//alert( $('#cid_'+what).val());
												$('#cid_'+what+'_saving').css("display","inline");
												$.ajax({ url: './ajax_update_company_employees.php',
													 data: {id: what, year: year, value: $('#cid_'+what).val()},
													 type: 'post',
													 success: function(output) {
																 // alert(output);
																 $('#cid_'+what+'_saving').css("display","none");
															  }
												});
												
											}
										
										
										</script>
                                        
                                        <?php }?>
                                        
                                    </table>