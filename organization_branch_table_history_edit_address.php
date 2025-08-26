<?php //reset sum emplioyees

	$sum_employees = 0;
?>

<table style="border-collapse:collapse;" border="1">
                                       <tr>
                                    	<td colspan="10">
                                     	<div align="center" style=" color:#060">
											<b><font color=blue>ประวัติการเปลี่ยนที่อยู่ </font></b>
                                        </div>
                                        </td>
                                    </tr>
									
									
                                    
                                     <tr>
                                    	       
										<td align="center"> 
                                        
                                        <div align="center">
                                     	 ชื่อสถานประกอบการ
                                          </div>
                                          
                                          </td>											   
                                        
                                        <td align="center"> 
                                        
                                        <div align="center">
                                     	 จังหวัดที่ส่ง
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center"> 
                                        
                                        <div align="center">
                                     	 จังหวัดที่รับ
                                          </div>
                                          
                                          </td>
                                          
                                          
                                          <td align="center">
                                        
                                        <div align="center">
                                        
										วันที่ส่ง
                                        
                                        </div>
                                        </td>
                                        
                                        
                                     

                                        </td>
										
										
										
										
										
                                    </tr>
									
									<tr>
                                       <td>
                                       	<?php echo formatCompanyName($log_row["CompanyNameThai"],$log_row["CompanyTypeCode"]);?>
									   

									   
									   </td>
                                       <td align="center" style="text-align: center"> <?php 
									   	
										echo getFirstItem("select province_name from provinces where province_id = '".$log_row["Province"]."'");
									   
									   ?></a></td>
                                       <td align="center" style="text-align: center"><?php 
									   
										$province_accetp = getFirstItem("select province_name from provinces where province_id = '".$log_row["Province_accepted"]."'");
									   	if($province_accetp == '0'){
										
										echo ""; }
										else{
										echo getFirstItem("select province_name from provinces where province_id = '".$log_row["Province_accepted"]."'");	
										
										}
	
									   
									   ?></td>
                                       <td align="center" style="text-align: center"><?php 
									   
									   
										echo $log_row[edit_date];
										   
									   ?></td>
                       
							
                                     </tr>
									
                                        
                                    </table>