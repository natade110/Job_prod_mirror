<?php

	include "db_connect.php";
	include "session_handler.php";
	
	//yoes 20160622 -- check permisison
	if($sess_accesslevel != 1 && $sess_accesslevel != 2){
		header("location: index.php");	
		exit();
	}
	
	
	$duped_id = $_GET[school_code]*1;
	
?>


<?php include "header_html.php";?>






                <td valign="top" style="padding-left:5px;">
                
                	
                	
                    
                    
                    
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >การรวมข้อมูลโรงเรียนที่ซ้ำซ้อน</h2>
                    
                    
                    
                    <?php 
						
						if($_GET[main_cid] && $_GET[duped_cid]){
							
					?>
                    
                    	
                        <font color="green">
                    	ทำการรวมข้อมูลโรงเรียนเสร็จสิ้น
                        </font>
                        
                        <br />
                        
                        ข้อมูลทั้งหมดของ 
                        
                        <b>
                        <?php 
						
							echo getFirstItem("
								
								select
									CompanyNameThai
								from
									company
								where
									cid = $_GET[duped_cid]
								
							");						
						
						?>
                        </b>
                        
                        
                        ได้ถูกนำไปรวมกับ
                        
                        
                        <a href="organization.php?id=<?php echo $_GET[main_cid]?>">
                        
                        <?php 
						
							echo getFirstItem("
								
								select
									CompanyNameThai
								from
									company
								where
									cid = $_GET[main_cid]
								
							");						
						
						?>
                        </a>
                        
                        แล้ว
                    
                    
                    	<hr />
                    
                    <?php						
			
			
							$merged = 1;
							
						}
					
					?>
                        
                        
                    
                    <?php if(!$merged){?>
                        
					<form method="post">
                        <?php 
                        
                            if(!$_POST["company_branch_code_01"]){
                                $_POST["company_branch_code_01"] = "000000";	
                            }
                            if(!$_POST["company_branch_code_02"]){
                                $_POST["company_branch_code_02"] = "000000";	
                            }
                            
                        ?>
                        
                        <strong>
                         
                            1. ใส่เลขที่บัญชีนายจ้างและเลขที่สาขาของโรงเรียนเอกชนที่ต้องการรวมข้อมูล
                         
                         </strong>
                         
                         
                         <br />
                         โรงเรียนแรก: 
                         <br /> เลขที่บัญชีนายจ้าง
                         <input name="company_code_01" type="text" id="company_code_01" maxlength="10" value="<?php echo $_POST["company_code_01"];?>"  />
                         เลขที่สาขา 000000
                         
                         <?php if(1==0){?>
                         <input name="company_branch_code_01" type="text" id="company_branch_code_01" maxlength="6" value="<?php echo $_POST["company_branch_code_01"];?>"  />
                         <?php }?>
                         
                         <input name="company_branch_code_01" type="hidden" id="company_branch_code_01" maxlength="6" value="000000"  />
                         
                         <br />
                        โรงเรียนที่สอง: 
                        <br />เลขที่บัญชีนายจ้าง
                         <input name="company_code_02" type="text" id="company_code_02" maxlength="10" value="<?php echo $_POST["company_code_02"];?>"  />
                          เลขที่สาขา 000000
                          
                          <?php if(1==0){?>
                         <input name="company_branch_code_02" type="text" id="company_branch_code_02" maxlength="6" value="<?php echo $_POST["company_branch_code_02"];?>"  />
                         <?php }?>
                         
                          <input name="company_branch_code_02" type="hidden" id="company_branch_code_02" maxlength="6" value="000000"  />
                         
                         <br />
                         <input type="submit" value="คลิกที่นี่เพื่อไปขั้นตอนต่อไป" />
                     
                     
                     </form>
                    
                    <hr />
                    
                    <?php }//end if is_merged?>
                    
                    
                    <?php 
					
					if($_POST[company_code_01] && $_POST[company_code_02]){
						
					?>
                    <form id="myForm" action="scrp_merge_school.php" method="post">
                    
                     <strong>
                     
                        2. เลือกโรงเรียนที่จะใช้เป็นข้อมูลหลักจากรายชื่อด้านล่าง <br />- โรงเรียนที่ไม่ได้ถูกเลืกจะถูกเอาออกจากระบบ และข้อมูลการปฏิบัติตามกฎหมายทั้งหมดจะถูกย้ายไปอยู่กับโรงเรียนที่ถูกเลือก
                     
                     </strong>
                    
                    
                    <table cellpadding="5" bgcolor="#FFFFFF" border="1" style=" margin: 10px 0; border: 1px solid #000; border-collapse: collapse;">
                    	
                        
                        <tr>
                        
                        	<td align="center" bgcolor="#efefef" style="text-align: center">
                            
                           	
                           
                            </td>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                            
                            #
                           
                            </td>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                            
                            ชื่อโรงเรียน
                           
                            </td>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                              
                              เลขที่บัญชีนายจ้าง
                            </td>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                            
                           เลขที่สาขา
                            </td>
                            <td align="center" bgcolor="#efefef" style="text-align: center">ประเภทธุรกิจ</td>
                            <td align="center" bgcolor="#efefef" style="text-align: center"> รหัสโรงเรียน </td>
                            
                            <?php if(1==0){?>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                            
                           ประเภทโรงเรียน
                            </td>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                            
                           school_locate
                            </td>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                            
                           school_charity
                            </td>
                            <?php }?>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                            จำนวนครู
                           
                            </td>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                            จำนวนครูสัญญาจ้าง
                           
                            </td>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                            
                           จำนวนลูกจ้าง                      
                            </td>
                            <td align="center" bgcolor="#efefef" style="text-align: center">
                            
                           
                           วันที่สร้างข้อมูลโรงเรียน
                            </td>
                            
                        </tr>
                        
                        <?php 
						
						//render company names
						/*$sql = "
						
								select
									*
								from
									company a
										join company_meta b
											on a.cid = b.meta_cid
											and
											meta_for = 'school_code'
											and
											meta_value = '$duped_id'
											
									order by 
										cid desc
										
							
							";*/
						
						
						$sql = "
							
							select
								*
							from
								company a
							where
								(
									CompanyCode ='".$_POST[company_code_01]."' and BranchCode = '".$_POST[company_branch_code_01]."'
								)
								or
								(
									CompanyCode ='".$_POST[company_code_02]."' and BranchCode = '".$_POST[company_branch_code_02]."'
								)
								
								and
								cid not in (
								
									select
										meta_cid
									from
										company_meta
									where
										meta_for = 'merged_to'
								
								)
								
							order by 
								cid desc
						
						
						";
						
						
						$duped_result = mysql_query($sql);
						$duped_result_2 = mysql_query($sql);
						
						
						 
						
						while($duped_row = mysql_fetch_array($duped_result)){
							
							$school_count++;
							
							//get all meta for this CID
							$sql = "select * from company_meta where meta_cid = '".$duped_row["CID"]."'";
							
							
							$meta_result = mysql_query($sql) or die(mysql_error());
							$meta_array = array();
							
							while($meta_row = mysql_fetch_array($meta_result)){
							
								$meta_array[$meta_row[meta_for]] = $meta_row[meta_value];
								
							}
							
							//print_r($meta_array);
							
							//also check whether this is school or not
							if($meta_array[is_school]){
								
								$is_school++;	
								
							}
							
							
							
						?>
                    	<tr>
                        
                        	
                        
                        	<td>
                            <input name="main_cid" type="radio" value="<?php echo $duped_row[CID];?>" />
                            
                            <input name="main_cid_<?php echo $school_count;?>" type="hidden" value="<?php echo $duped_row[CID];?>" />
                           
                            </td>
                            
                            <td>
                            <?php 
								
								echo $school_count;
							
							?>
                            </td>
                            
                       	  <td>
                          	
                            <a href="organization.php?id=<?php echo $duped_row[CID];?>" target="_blank">
								<?php                                 
                                    echo $duped_row[CompanyNameThai];                                    
                                ?>
                            </a>
                          
                          </td>
                            
                            <td>
								<?php                                 
                                    echo $duped_row[CompanyCode];                                    
                                ?>                           
                            </td>
                            
                              <td>
								<?php                                 
                                    echo $duped_row[BranchCode];                                    
                                ?>                           
                            </td>
                              <td>
                              
                              <?php 
							  
							  echo getFirstItem("select CompanyTypeName from companytype where CompanyTypeCode = '". $duped_row[CompanyTypeCode]."'");
							  ?>
                              
                              </td>
                              <td><?php                                 
                                    echo $meta_array[school_code];                                    
                                ?></td>  
                            
                            <?php if(1==0){?>
                                <td>
                                    <?php                                 
                                        echo $meta_array[school_type];                                    
                                    ?>                           
                                </td>  
                                
                                <td>
                                    <?php                                 
                                        echo $meta_array[school_locate];                                    
                                    ?>                           
                                </td> 
                                
                                <td>
                                    <?php                                 
                                        echo $meta_array[school_charity];                                    
                                    ?>                           
                                </td>  
                            <?php }?>
                            
                            <td>
                            	<div align="right">
								<?php                                 
                                    echo $meta_array[school_teachers];                                    
                                ?>                 
                                </div>          
                            </td> 
                            
                             <td>
								<div align="right">
								<?php                                 
                                    echo $meta_array[school_contract_teachers];                                    
                                ?>
                                </div>                           
                            </td> 
                            
                             <td>
                             	<div align="right">
								<?php                                 
                                    
									
									if($meta_array[is_school]){
										echo $meta_array[school_employees];
									}else{
										echo $duped_row[Employees];										
									}
									
                                ?>                 
                                </div>          
                            </td> 
                            
                            <td>
								<?php                         
								
									if($duped_row[CreatedDateTime] !=  "0000-00-00 00:00:00"){       
                                    	echo formatDateThai($duped_row[CreatedDateTime],0); 
									}
                                ?>                           
                            </td> 
                            
                            
                                                      
                        </tr>
                        <?php }?>
                        
                        
                       
                    </table>
                        
                        
                        <?php 
						if(!$is_school){
							
							echo "<font color=red>
									
									สถานประกอบการที่ต้องการทำการรวมข้อมูล ไม่ประกอบด้วยโรงเรียนเอกชนอย่างน้อยหนึ่งแห่ง - กรุณาใส่เลขที่บัญชีนายจ้างที่ประกอบด้วยโรงเรียนเอกชน
									
								</font>";
								
						}
						?>
                        
                        
                        
                        <?php 
						
						if($school_count < 2){
							
							echo "<font color=red>
									
									กรุณาใส่เลขที่บัญชีนายจ้างทั้งสองแห่งให้ถูกต้อง และเลขที่บัญชีนายจ้างที่ใส่จะต้องเป็นสถานประกอบการที่ยังไม่ถูกรวมข้อมูลไปแล้ว
									
								</font>";
								
							$is_school = 0;
							
							
						}
						
						?>
                        
                        
                        
                        
                        <hr />
                        
                        
                   <?php if($is_school){?>
                         <strong>
                     
                        3. ข้อมูลการปฏบัติตามกฏหมายของทุกปี(ถ้ามี) จะถูกรวมไปไว้ในโรงเรียนที่ถูกเลือก - ข้อมูลการปฏิบัติตามกฏหมายจะถูกรวมตามตารางสรุปด้านล่าง
                     
                     </strong>
                     
                     
                     
                     <table cellpadding="5" bgcolor="#FFFFFF" border="1" style="  margin: 10px 0; border: 1px solid #000; border-collapse: collapse;">
                    
                    
                    	<tr>
                    	  <td rowspan="2" align="center" bgcolor="#efefef" style="text-align: center">
                           	   ชื่อโรงเรียน                          </td>
                               
                               
                          <?php 
						  $year_start = 2011;
						  
						  if(date("m") >= 9 || $sess_accesslevel == 1 || $sess_accesslevel == 2){
								$the_end_year = date("Y")+1; //new year at month 9
							}else{
								$the_end_year = date("Y");
							}
						  
						  ?>
                            
                            <?php for($i= $the_end_year;$i>=$dll_year_start;$i--){?>   
                                  <td colspan="4" align="center" bgcolor="#efefef" style="text-align: center">
                                    ปี <?php echo $i+543;?>
                                    
                                    </td>
                            <?php }?>
                   	   </tr>
                    	<tr>
                        
                        	 <?php for($i= $the_end_year;$i>=$dll_year_start;$i--){?>   
                             
									<?php if(1==1){?>
                                     <td align="center" bgcolor="#efefef" style="text-align: center">
                                      จำนวนครู
                                      <br />และลูกจ้าง
                                    </td>
                                    <?php }?>
                                
                                   <td align="center" bgcolor="#efefef" style="text-align: center">
                                       ม.33<br />(คน)
                                    </td>
                                    <td align="center" bgcolor="#efefef" style="text-align: center">
                                        ม.34<br />(บาท)
                                    </td>
                                    <td align="center" bgcolor="#efefef" style="text-align: center">
                                        ม.35<br />(คน)
                                    </td>
                            
                            <?php }?>
                            
                            
                        </tr>
                        
						 <?php 
						 
						 $the_employees_array = array();
						 
						 $the_33_array = array();
						 $the_34_array = array();
						 $the_35_array = array();
						 
                         while($duped_row = mysql_fetch_array($duped_result_2)){
                         ?>
                         
                         
                         	<tr>
                        
                        	
                            
                                <td>
                                <?php echo $duped_row[CompanyNameThai];?>
                               
                                </td>
                                
                                
                                
                                 <?php for($i= $the_end_year;$i>=$dll_year_start;$i--){?>   
                                 
                                                 
                                                <?php 
                                                
                                                $this_year = $i;
                                                
                                                $this_lid = getFirstItem("select lid from lawfulness where cid = '".$duped_row[CID]."' and Year = '$this_year'");
                                                
                                                ?>
                                                
                                                <?php if(1==1){?>
                                                <td>
                                                
                                                <div align="right">
                                                <?php 
                                                
                                                    //echo $this_lid ;
                                                    $this_employees = getFirstItem("
                                                    
                                                        select
                                                            Employees 
                                                        from
                                                            lawfulness
                                                        where
                                                            lid = '$this_lid'
                                                    
                                                    ");
                                                   
												   
												   //yoes 20160623 --> just merge employees 
													/*if($this_employees > $the_employees_array[$this_year]){
														$the_employees_array[$this_year] = $this_employees;
													}*/
													
													$the_employees_array[$this_year] += $this_employees;
													 
													
                                                    echo number_format($this_employees,0);
                                                
                                                ?>
                                                </div>
                                                
                                                </td>
                                                
                                                <?php }?>
                                                
                                                <td>
                                                
                                                <div align="right">
                                                <?php 
                                                
                                                    //echo $this_lid ;
                                                    $this_33 = getFirstItem("
                                                    
                                                        select
                                                            count(*) 
                                                        from
                                                            lawful_employees
                                                        where
                                                            le_cid = '".$duped_row[CID]."'
                                                            and
                                                            le_year = '$this_year'
                                                    
                                                    ");
													
													$the_33_array[$this_year] += $this_33;
                                                    
                                                    echo number_format($this_33,0);
                                                
                                                ?>
                                                </div>
                                                
                                                </td>
                                                
                                                <td>
                                                
                                                 <div align="right">
                                                        <?php 
                                                        
                                                        //same sql with scrp_get_34_from_lid.php
                                                        $the_sql = "select 
                                                                sum(receipt.amount) as receipt_amount
                                                                
                                                                 from payment, receipt , lawfulness
                                                                    where 
                                                                    receipt.RID = payment.RID
                                                                    and
                                                                    lawfulness.LID = payment.LID
                                                                    
                                                                    and
                                                                    lawfulness.lid = '".$this_lid."' 
                                                                    
                                                                    and
                                                                    is_payback != 1
                                                                    and 
                                                                    main_flag = 1
                                                                    ";
                                                                    
                                                            //echo $the_sql;
                                                            
                                                            $this_34 = getFirstItem($the_sql);
															
															$the_34_array[$this_year] += $this_34;
                                                        
                                                            echo number_format($this_34,0);
                                                        ?>
                                                    </div>
                                                
                                                </td>
                                                
                                                 <td>
                                                
                                               <div align="right">
                                                    <?php 
                                                    
                                                    
                                                        //echo $this_lid ;
                                                        $this_35 = getFirstItem("
                                                        
                                                            select
                                                                count(*) 
                                                            from
                                                                curator
                                                            where
                                                                curator_lid = '$this_lid'
                                                                and
                                                                curator_parent = 0
                                                        
                                                        ");
                                                        
														$the_35_array[$this_year] += $this_35;
														
                                                        echo number_format($this_35,0);
                                                    
                                                    ?>
                                                 </div>   
                                                
                                                </td>
                                                
                                   <?php } //ENDS: for($i= $the_end_year;$i>=$dll_year_start;$i--){?> 
                            
                            </tr>
                         
                         <?php }?>
                         
                         <tr>
                        
                        	
                            
                                <td>
                               
                               
                              <strong> รวมการปฏิบัติตามกฏหมาย</strong>
                               
                                </td>
                                
                                
                                
                                 <?php for($i= $the_end_year;$i>=$dll_year_start;$i--){?>   
                                 
                                                 
                                                <?php 
                                                
                                                $this_year = $i;
                                                
                                               
                                                
                                                ?>
                                                
                                                <td>
                                                
                                                <div align="right" style="font-weight: bold; border-bottom: 1px dotted #000;"
                                                
                                                title="จะนำจำนวนครูและลูกจ้างที่มากที่สุดมาใช้"
                                                
                                                >
                                                <?php 
                                                                                                       
                                                    echo number_format($the_employees_array[$this_year],0);
                                                
                                                ?>
                                                </div>
                                                
                                                </td>
                                                
                                                <td>
                                                
                                                <div align="right" style="font-weight: bold;">
                                                <?php 
                                                                                                       
                                                    echo number_format($the_33_array[$this_year],0);
                                                
                                                ?>
                                                </div>
                                                
                                                </td>
                                                
                                                <td>
                                                
                                                 <div align="right" style="font-weight: bold;">
                                                        <?php 
                                                        
                                                        echo number_format($the_34_array[$this_year],0);
                                                        ?>
                                                    </div>
                                                
                                                </td>
                                                
                                                 <td>
                                                
                                               <div align="right" style="font-weight: bold;">
                                                    <?php 
                                                    
                                                    
                                                         echo number_format($the_35_array[$this_year],0);
                                                    
                                                    ?>
                                                 </div>   
                                                
                                                </td>
                                                
                                   <?php } //ENDS: for($i= $the_end_year;$i>=$dll_year_start;$i--){?> 
                            
                            </tr>
                     
                     </table>
      
      
      					 <hr />
                         <strong>
                         
                         4. หลังจากตรวจสอบข้อมูลพร้อมแล้ว - คลิกที่ปุ่มด้านล่างเพื่อทำการรวมข้อมูลโรงเรียน
                         
                         </strong>
                    
                    	<div style="padding-top: 10px;">
	                    	<input type="submit" value="ทำการรวมข้อมูลโรงเรียน" onclick="return doCheckRadio(); " />
                            <input type="hidden" name="duped_id" value="<?php echo $duped_id?>" />
                        </div>
                        
                        
                        <script>
						
							function doCheckRadio(){
								
								//alert($('input[name=main_cid]:checked', '#myForm').val());
								if($('input[name=main_cid]:checked', '#myForm').val() == undefined){
									//alert('no');	
									alert("กรุณาเลือกโรงเรียนที่จะใช้เป็นข้อมูลหลักจากข้อ (2)");
									return false;
								}else{
									//alert('yes');	
									return confirm('ต้องการรวมข้อมูลโรงเรียนหรือไม่  การรวมข้อมูลจะไม่สามารถทำการย้อนกลับเป็นเหมือนเดิมได้');
								}
								
								
							}
						
						</script>
                    	
                    </form>     
                        
                        
                <?php }//end if if($_POST[company_code_01] && $_POST[company_code_02]){?>
                
                
            <?php }//$end is schhol?>
                    
                        
                    </td>
      		</tr>
             
             <tr>
                <td align="right" colspan="2">
                    <?php include "bottom_menu.php";?>
                </td>
            </tr>  
            
		</table>                            
       
        </td>
    </tr>
    
</table>   



</div><!--end page cell-->
</td>
</tr>
</table>


</body>
</html>