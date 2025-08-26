<?php

	include "db_connect.php";
	
	header('Content-Type: text/html; charset=utf-8');
	
	$the_lid = $_POST[the_lid];
	$the_cid = $_POST[the_cid];
	$the_year = $_POST[the_year];
	
	
	$the_ratio = $_POST[the_ratio];
	$the_33 = $_POST[the_33];
	$the_35 = $_POST[the_35];
	
	                                     
	
	
	$sql = "
	
		
		select
			*
		from
			company a
				join lawfulness b
					on a.cid = b.cid
					and
					b.year = '$the_year'
					and
					a.cid = '$the_cid'
	
	
	";
	
	$company_row = getFirstRow($sql);
	

?>

<div align="center"> <strong>รายงานผลการปฏิบัติตามกฎหมาย <?php echo formatCompanyName( $company_row[CompanyNameThai],  $company_row[CompanyTypeCode]);?></strong> <br />
  <br />


<table border="1"  cellpadding="5" style="border-collapse:collapse;" >
                            
                              <?php if($sess_accesslevel !=4){?>
                              <?php } ?>
                              
                              <tr>
                                <td>สถานประกอบการ</td>
                                <td>
                                
                                
                                <strong><?php 
								
								
								echo formatCompanyName($company_row[CompanyNameThai],$company_row[CompanyTypeCode]);
								
								
								?></strong></td>
                              </tr>
                              <tr>
                                <td>เลขทะเบียนนายจ้าง</td>
                                <td><strong><?php 
								
								echo $company_row[CompanyCode];
								
								?></strong></td>
                              </tr>
                              <tr>
                                    <td>สำหรับปี</td>
                                    <td><strong><?php 
										//**toggle payment
										
										
										echo $the_year+543;
										
										
										// ddl_year_payments will only allow to add payment year 2015?></strong></td>
    </tr>
                              <tr>
                                <td>สถานะการปฏิบัติตามกฏหมาย</td>
                                <td>
								
								<strong><?php 
								
								
									echo getLawfulText($company_row[LawfulStatus]);?></strong>
                                
                                </td>
                              </tr>
                              
                              
                              <tr>
                                <td>จำนวนลูกจ้าง</td>
                                <td>
								
								<strong><?php 
								
								
									echo number_format($company_row[Employees],0);?></strong>
                                
                                </td>
                              </tr>
                              
                              <tr>
                                <td>อัตราส่วนลูกจ้างต่อคนพิการ</td>
                                <td>
								
								<strong><?php 
								
									//print_r($_POST);
								
									echo number_format($_POST[the_ratio],0);?></strong>
                                
                                </td>
                              </tr>
                              
                              
                                <tr>
                                <td>รับคนพิการเข้าทำงานตาม ม.33</td>
                                <td>
								
								<strong><?php 
								
								
									echo number_format($_POST[the_33],0);?></strong>
                                
                                </td>
                              </tr>
                                                           
                              
                               <tr>
                                <td>ให้สัมปทานฯ ตาม ม.35</td>
                                <td>
								
								<strong><?php 
								
								
									echo number_format($_POST[the_35],0);?></strong>
                                
                                </td>
                              </tr>
                              
                              
                               <tr>
                                <td>จ่ายเงินแทนการรับคนพิการ ม.34</td>
                                <td>
								
                                
                                	
                                    <table border="1"  cellpadding="5" style="border-collapse: collapse; " >
                                                              
                                                              
                                                              <tr>
                                                                <td>#</td>
                                                                <td>ใบเสร็จเล่มที่</td>
                                                                <td>เลขที่ใบเสร็จ</td>
                                                                <td>จำนวนเงินที่จ่าย</td>
                                                              </tr>
                                                              
                                                             
                                                              <?php 
                                                                            
                                                                                
                                                                                $related_reciept_sql = "
																				
																					select
                                                                                        *
                                                                                    from
																						
																						lawfulness a
																							
																							join
																								payment b
																								on
																								a.lid = b.lid
																								and
																								b.lid = '$the_lid'
																								
																							join
		                                                                                        receipt c
																								
																								on b.RID = c.RID
                                                                                   			
																				   	
																					
																					
																				   ";
                                                                            
                                                                            
                                                                                $related_reciept = mysql_query($related_reciept_sql);
                                                                                
                                                                                
                                                                                $count_receipt = 1;
                                                                            
                                                                            while($related_array = mysql_fetch_array($related_reciept)){
                                                                                
                                                                                
                                                                                $count_receipt++;
                                                                            ?>
                                                                            
                                                                            
                                                                                <tr>
                                                                                <td>
                                                                                
                                                                                <div align="right">
                                                                                <?php echo $count_receipt;?>
                                                                                </div>
                                                                                
                                                                                </td>
                                                                                <td><?php 
                                                                                    
                                                                                    echo $related_array[BookReceiptNo];
                                                                                
                                                                                ?></td>
                                                                                <td><?php 
                                                                                    
                                                                                    echo $related_array[ReceiptNo];
                                                                                
                                                                                ?></td>
                                                                                
                                                                                 <td>
																				 
																				 <div align="right">
																				 <?php 
                                                                                    
                                                                                    echo number_format($related_array[Amount],2);
                                                                                
                                                                                ?>
                                                                                </div>
                                                                                </td>
                                                                              </tr>
                                                                            
                                                                            <?php											
                                                                                
                                                                            }
                                                                            
                                                                            ?>
                                                              
                                  </table>

								
                                </td>
                              </tr>
                              
  </table>
    
    

<br />

<?php if($_POST[do_print]){ ?>

	<script>
		window.print();
	</script>

<?php }else{ ?>
<form method="post" target="_blank">
	<input name="do_print" type="submit"  value="พิมพ์รายงาน" />   
    
     <input type="hidden" name="the_lid" value="<?php echo $the_lid; ?>" />
    <input type="hidden" name="the_cid" value="<?php echo $the_cid;?>"/>
    <input type="hidden" name="the_year" value="<?php echo $the_year; ?>"/>
    
     <input type="hidden" name="the_ratio" value="<?php echo $the_ratio; ?>"/>
    <input type="hidden" name="the_33" value="<?php echo $the_33; ?>"/>
    <input type="hidden" name="the_35" value="<?php echo $the_35; ?>"/>
                                                   
                                                                             
</form>  
<?php }?>  
 
</div>
