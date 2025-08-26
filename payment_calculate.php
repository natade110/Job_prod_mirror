<?php

	include "db_connect.php";
	include "scrp_config.php";
	
	
?>
<?php 
	include "header_html_blank.php";
	
?>

<?php 
				
	//init styffs	  
	if(date("m") >= 9){
		$the_year = date("Y")+1; //new year at month 9
	}else{
		$the_year = date("Y");
	}
	
	$this_lawful_year = $the_year;
	
	if(is_numeric(deleteCommas($_POST["Employees"]))){
		
		$the_year = $_POST["ddl_year"]*1;
		
		$employee_to_use = deleteCommas($_POST["Employees"])  *1;
		
		$ratio_employees = getEmployeeRatio($employee_to_use,$ratio_to_use);
		
		
		//echo $ratio_employees;
	}
	
	
	//echo $the_year;
	
	$ratio_to_use = getThisYearRatio($the_year);
	//echo $ratio_to_use;
						
 ?>
                      
                      
<td valign="top" colspan="2" align="center">
  
  
  <div align="center">
    <form method="post">
      <table cellpadding="3" style="margin:0px 0 0 20px;" align="center" >
  	 <tr>
  	   <td colspan="5" >
       
       <div style="margin-bottom:10px;">
       
	       <h2 class="default_h1" style="margin:0; padding:0 0 0px 0; font-size: 26px;"  > คำนวณเงิน </h2>
       
       </div>
       
       </td>
	   </tr>
   	  <tr>
       	  <td>
                        
            ประจำปี
                        
          </td>
          <td>
          
          	<?php include "ddl_year_auto_submit.php";?>                       
                        
                        
          </td>
          <td width="25">&nbsp;</td>
          
          
          <?php if($the_year == 2011){?>
          
          <td>จังหวัด</td>
          <td><?php 
		  
		  	//include "ddl_org_province_no_null.php";
		  
		  ?>
          
          <select name="Province" id="Province" onChange="changeProvinceWage(); reCalculateValue();">
          
          		<option <?php if($_POST['Province']==1){?>selected='selected'<?php }?> value='1'>กรุงเทพมหานคร</option>
<option <?php if($_POST['Province']==2){?>selected='selected'<?php }?> value='2'>สมุทรปราการ</option>
<option <?php if($_POST['Province']==3){?>selected='selected'<?php }?> value='3'>นนทบุรี</option>
<option <?php if($_POST['Province']==4){?>selected='selected'<?php }?> value='4'>ปทุมธานี</option>
<option <?php if($_POST['Province']==5){?>selected='selected'<?php }?> value='5'>พระนครศรีอยุธยา</option>
<option <?php if($_POST['Province']==6){?>selected='selected'<?php }?> value='6'>อ่างทอง</option>
<option <?php if($_POST['Province']==7){?>selected='selected'<?php }?> value='7'>ลพบุรี</option>
<option <?php if($_POST['Province']==8){?>selected='selected'<?php }?> value='8'>สิงห์บุรี</option>
<option <?php if($_POST['Province']==9){?>selected='selected'<?php }?> value='9'>ชัยนาท</option>
<option <?php if($_POST['Province']==10){?>selected='selected'<?php }?> value='10'>สระบุรี</option>
<option <?php if($_POST['Province']==11){?>selected='selected'<?php }?> value='11'>ชลบุรี</option>
<option <?php if($_POST['Province']==12){?>selected='selected'<?php }?> value='12'>ระยอง</option>
<option <?php if($_POST['Province']==13){?>selected='selected'<?php }?> value='13'>จันทบุรี</option>
<option <?php if($_POST['Province']==14){?>selected='selected'<?php }?> value='14'>ตราด</option>
<option <?php if($_POST['Province']==15){?>selected='selected'<?php }?> value='15'>ฉะเชิงเทรา</option>
<option <?php if($_POST['Province']==16){?>selected='selected'<?php }?> value='16'>ปราจีนบุรี</option>
<option <?php if($_POST['Province']==17){?>selected='selected'<?php }?> value='17'>นครนายก</option>
<option <?php if($_POST['Province']==18){?>selected='selected'<?php }?> value='18'>สระแก้ว</option>
<option <?php if($_POST['Province']==19){?>selected='selected'<?php }?> value='19'>นครราชสีมา</option>
<option <?php if($_POST['Province']==20){?>selected='selected'<?php }?> value='20'>บุรีรัมย์</option>
<option <?php if($_POST['Province']==21){?>selected='selected'<?php }?> value='21'>สุรินทร์</option>
<option <?php if($_POST['Province']==22){?>selected='selected'<?php }?> value='22'>ศรีสะเกษ</option>
<option <?php if($_POST['Province']==23){?>selected='selected'<?php }?> value='23'>อุบลราชธานี</option>
<option <?php if($_POST['Province']==24){?>selected='selected'<?php }?> value='24'>ยโสธร</option>
<option <?php if($_POST['Province']==25){?>selected='selected'<?php }?> value='25'>ชัยภูมิ</option>
<option <?php if($_POST['Province']==26){?>selected='selected'<?php }?> value='26'>อำนาจเจริญ</option>
<option <?php if($_POST['Province']==27){?>selected='selected'<?php }?> value='27'>หนองบัวลำภู</option>
<option <?php if($_POST['Province']==28){?>selected='selected'<?php }?> value='28'>ขอนแก่น</option>
<option <?php if($_POST['Province']==29){?>selected='selected'<?php }?> value='29'>อุดรธานี</option>
<option <?php if($_POST['Province']==30){?>selected='selected'<?php }?> value='30'>เลย</option>
<option <?php if($_POST['Province']==31){?>selected='selected'<?php }?> value='31'>หนองคาย</option>
<option <?php if($_POST['Province']==32){?>selected='selected'<?php }?> value='32'>มหาสารคาม</option>
<option <?php if($_POST['Province']==33){?>selected='selected'<?php }?> value='33'>ร้อยเอ็ด</option>
<option <?php if($_POST['Province']==34){?>selected='selected'<?php }?> value='34'>กาฬสินธุ์</option>
<option <?php if($_POST['Province']==35){?>selected='selected'<?php }?> value='35'>สกลนคร</option>
<option <?php if($_POST['Province']==36){?>selected='selected'<?php }?> value='36'>นครพนม</option>
<option <?php if($_POST['Province']==37){?>selected='selected'<?php }?> value='37'>มุกดาหาร</option>
<option <?php if($_POST['Province']==38){?>selected='selected'<?php }?> value='38'>เชียงใหม่</option>
<option <?php if($_POST['Province']==39){?>selected='selected'<?php }?> value='39'>ลำพูน</option>
<option <?php if($_POST['Province']==40){?>selected='selected'<?php }?> value='40'>ลำปาง</option>
<option <?php if($_POST['Province']==41){?>selected='selected'<?php }?> value='41'>อุตรดิตถ์</option>
<option <?php if($_POST['Province']==42){?>selected='selected'<?php }?> value='42'>แพร่</option>
<option <?php if($_POST['Province']==43){?>selected='selected'<?php }?> value='43'>น่าน</option>
<option <?php if($_POST['Province']==44){?>selected='selected'<?php }?> value='44'>พะเยา</option>
<option <?php if($_POST['Province']==45){?>selected='selected'<?php }?> value='45'>เชียงราย</option>
<option <?php if($_POST['Province']==46){?>selected='selected'<?php }?> value='46'>แม่ฮ่องสอน</option>
<option <?php if($_POST['Province']==47){?>selected='selected'<?php }?> value='47'>นครสวรรค์</option>
<option <?php if($_POST['Province']==48){?>selected='selected'<?php }?> value='48'>อุทัยธานี</option>
<option <?php if($_POST['Province']==49){?>selected='selected'<?php }?> value='49'>กำแพงเพชร</option>
<option <?php if($_POST['Province']==50){?>selected='selected'<?php }?> value='50'>ตาก</option>
<option <?php if($_POST['Province']==51){?>selected='selected'<?php }?> value='51'>สุโขทัย</option>
<option <?php if($_POST['Province']==52){?>selected='selected'<?php }?> value='52'>พิษณุโลก</option>
<option <?php if($_POST['Province']==53){?>selected='selected'<?php }?> value='53'>พิจิตร</option>
<option <?php if($_POST['Province']==54){?>selected='selected'<?php }?> value='54'>เพชรบูรณ์</option>
<option <?php if($_POST['Province']==55){?>selected='selected'<?php }?> value='55'>ราชบุรี</option>
<option <?php if($_POST['Province']==56){?>selected='selected'<?php }?> value='56'>กาญจนบุรี</option>
<option <?php if($_POST['Province']==57){?>selected='selected'<?php }?> value='57'>สุพรรณบุรี</option>
<option <?php if($_POST['Province']==58){?>selected='selected'<?php }?> value='58'>นครปฐม</option>
<option <?php if($_POST['Province']==59){?>selected='selected'<?php }?> value='59'>สมุทรสาคร</option>
<option <?php if($_POST['Province']==60){?>selected='selected'<?php }?> value='60'>สมุทรสงคราม</option>
<option <?php if($_POST['Province']==61){?>selected='selected'<?php }?> value='61'>เพชรบุรี</option>
<option <?php if($_POST['Province']==62){?>selected='selected'<?php }?> value='62'>ประจวบคีรีขันธ์</option>
<option <?php if($_POST['Province']==63){?>selected='selected'<?php }?> value='63'>นครศรีธรรมราช</option>
<option <?php if($_POST['Province']==64){?>selected='selected'<?php }?> value='64'>กระบี่</option>
<option <?php if($_POST['Province']==65){?>selected='selected'<?php }?> value='65'>พังงา</option>
<option <?php if($_POST['Province']==66){?>selected='selected'<?php }?> value='66'>ภูเก็ต</option>
<option <?php if($_POST['Province']==67){?>selected='selected'<?php }?> value='67'>สุราษฎร์ธานี</option>
<option <?php if($_POST['Province']==68){?>selected='selected'<?php }?> value='68'>ระนอง</option>
<option <?php if($_POST['Province']==69){?>selected='selected'<?php }?> value='69'>ชุมพร</option>
<option <?php if($_POST['Province']==70){?>selected='selected'<?php }?> value='70'>สงขลา</option>
<option <?php if($_POST['Province']==71){?>selected='selected'<?php }?> value='71'>สตูล</option>
<option <?php if($_POST['Province']==72){?>selected='selected'<?php }?> value='72'>ตรัง</option>
<option <?php if($_POST['Province']==73){?>selected='selected'<?php }?> value='73'>พัทลุง</option>
<option <?php if($_POST['Province']==74){?>selected='selected'<?php }?> value='74'>ปัตตานี</option>
<option <?php if($_POST['Province']==75){?>selected='selected'<?php }?> value='75'>ยะลา</option>
<option <?php if($_POST['Province']==76){?>selected='selected'<?php }?> value='76'>นราธิวาส</option>


          
          </select>
          
          
          </td>
          
          <?php }?>
          
          
   	  </tr>
   	  	
         <?php if($the_year == 2011){?>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>อัตราค่าแรง</td>
            <td><strong id="province_wage">0</strong> บาท/วัน</td>
 	    </tr>
        <?php }?>
        
                	<tr>
                	  <td valign="top">จำนวนลูกจ้างทั่วประเทศ
                      
                       <br>(ข้อมูล ณ วันที่ 
					   
                       <?php if($the_year == 2011){?>
                       		31 ธันวาคม 2553
                       <?php }elseif($the_year == 2012){?>
                       		1 ตุลาคม 2554   
                      <?php }elseif($the_year == 2013){?>
                       		1 ตุลาคม 2555                       
                       <?php }else{ ?>                       
						   <?php //echo formatDateThai(date("Y-m-d"));?>
                           <?php echo "1 ตุลาคม " . formatYear($the_year-1) ?>
                      <?php }?>
                       
                       )
                      </td>
                	  <td valign="top"> 
                      		<input 
                      			name="Employees" type="text" id="Employees" 
                                style="width:75px; text-align: right;" 
                                
                                onchange="addEmployeeCommas('Employees');" 
                                
                                onkeyup="reCalculateRatio(); reCalculateValue();"
                                value="<?php echo number_format(deleteCommas($_POST["Employees"]) *1,0);?>" size="25" 
                                 />
                                 
                                 <input type="hidden"  id="ratio_to_employ" />
                                 
                                 
                                 <script>
								 function addEmployeeCommas(for_what){
									//alert(toCurrency(document.getElementById(for_what).value));
									document.getElementById(for_what).value = toEmployee(document.getElementById(for_what).value);
								}
								
															
								function toEmployee(num) {
									  var sign;
									  var cents;
									  var i;
									
									  num = num.toString().replace(/\$|\,/g, '');
									  if (isNaN(num)) {
										num = "0";
									  }
									  sign = (num == (num = Math.abs(num)));
									  num = Math.floor(num * 100 + 0.50000000001);
									  cents = num % 100;
									  num = Math.floor(num / 100).toString();
									  if (cents < 10) {
										cents = '0' + cents;
									  }
									
									  for (i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++) {
										num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
									  }
									
									  return (((sign) ? '' : '-') + '' + num);
									}
								
								 </script>
                                 
                                 </td>
                	  <td valign="top">คน</td>
                	  <td valign="top">อัตราส่วนที่ต้องรับคนพิการ</td>
                	  <td valign="top">
                      <?php echo default_value( $ratio_to_use,0);?> :1 = <strong id="employee_ratio">0</strong> คน
                      </td>
              	  </tr>
                	<tr>
                	  <td>รับคนพิการเข้าทำงานแล้ว</td>
                	  <td><input name="the_33" id="the_33" style="width:75px;text-align: right;" type="text" 
                      
                      onkeyup="reCalculateValue();"                       
                      value="<?php echo number_format(deleteCommas($_POST["the_33"]) *1,0);?>"  
                      
                      
                     <?php if(1==1){?> 
                     	onchange="addEmployeeCommas('the_33');"
                     <?php }?>
                               
                      /></td>
                	  <td>คน</td>
                	  <td>ให้สัมปทานฯ ตาม ม.35</td>
                	  <td><input name="the_35" id="the_35" style="width:75px;text-align: right;" type="text" 
                      
                      onkeyup="reCalculateValue();" 
                     
                      value="<?php echo number_format(deleteCommas($_POST["the_35"]) *1,0);?>"  
                      
                      <?php if(1==1){?> 
                     	onchange="addEmployeeCommas('the_35');"
                     <?php }?>
                       /> 
                	  คน/สัญญา</td>
              	  </tr>
                  
                  
                  <?php 
				  
				  	$the_33 = $_POST["the_33"]*1;
					$the_35 = $_POST["the_35"]*1;
				  
				  	$extra_employee = $ratio_employees - $the_33 - $the_35;
					
					if($extra_employee < 0){
						$extra_employee = 0;
					}
				  
				  	$wage_rate = 222;
				  	$year_date = 365;
					
					//$start_money = $extra_employee*$wage_rate*$year_date;
				  
				  ?>
                	<tr>
                	  <td>จำนวนเงินที่ต้องจ่าย(เงินต้น)
                      <!--<br>
               	      (จ่ายภายในวันที่ 31 ม.ค. 2557)--> </td>
                	  <td colspan="4">
                	    
                	    
                	    
                	    <span id="final_value_to_show">
               	        <?Php echo formatNumber($start_money);?>
               	        </span> บาท
                	    
               	      <input type="hidden" size="5" name="final_value" id="final_value" /></td>
               	    </tr>
                	<tr>
                	  <td>จำนวนงวด</td>
                	  <td>
                	    <select name="interval" id="interval">
                        
                        
                	      <option value="1" <?php if($_POST["interval"] == 1){?>selected="selected"<?php }?>>1</option>
                          
                          <?php
						  
						  		//if(!($the_year < $this_lawful_year)){
							  	
									//only allow interval for current lawful year
								if(1==1){
								
							  	?>
                          
                          
                              <option value="2" <?php if($_POST["interval"] == 2){?>selected="selected"<?php }?>>2</option>
                              <option value="3" <?php if($_POST["interval"] == 3){?>selected="selected"<?php }?>>3</option>
                              <option value="4" <?php if($_POST["interval"] == 4){?>selected="selected"<?php }?>>4</option>
                              <option value="5" <?php if($_POST["interval"] == 5){?>selected="selected"<?php }?>>5</option>
                              <option value="6" <?php if($_POST["interval"] == 6){?>selected="selected"<?php }?>>6</option>
                              <option value="7" <?php if($_POST["interval"] == 7){?>selected="selected"<?php }?>>7</option>
                              <option value="8" <?php if($_POST["interval"] == 8){?>selected="selected"<?php }?>>8</option>
                              <option value="9" <?php if($_POST["interval"] == 9){?>selected="selected"<?php }?>>9</option>
                              <option value="10" <?php if($_POST["interval"] == 10){?>selected="selected"<?php }?>>10</option>
                              <option value="11" <?php if($_POST["interval"] == 11){?>selected="selected"<?php }?>>11</option>
                              <option value="12" <?php if($_POST["interval"] == 12){?>selected="selected"<?php }?>>12</option>
                          
                          <?php }?>
                          
           	          </select></td>
                	  <td>&nbsp;</td>
                	  <td>&nbsp;</td>
                	  <td></td>
              	  </tr>
                	<tr>
                	  <td><input type="submit" name="button" id="button" value="คำนวณ"></td>
                	  <td></td>
                	  <td></td>
                	  <td>&nbsp;</td>
                	  <td></td>
              	  </tr>
  </table>
</form>
  
   
   <table cellpadding="3" style="margin:0px 0 0 20px; border-collapse:collapse;<?php if(!$_POST["final_value"]){?>display: none;<?php }?>" border="1" cellspacing="0"  >
                	  <tr>
                	    <td rowspan="2" align="center" style="text-align: center">จำนวนงวด</td>
                	    <td rowspan="2" align="center" style="text-align: center">วันที่จ่าย</td>
                	    <td rowspan="2" align="center" bgcolor="#EEFFEC" style="text-align: center">จำนวนเงินที่ต้องจ่าย</td>
                	    <td colspan="3" align="center" style="text-align: center">รายละเอียดการส่งเงิน</td>
     </tr>
                	  <tr>
                	    <td align="center" style="text-align: center">จ่ายเงินต้น</td>
                	    <td align="center" style="text-align: center">ดอกเบี้ย</td>
                	    <td align="center" bgcolor="#FEE7E9" style="text-align: center">เงินต้นคงเหลือ</td>
                	   
              	    </tr>
                    
                    <?php 
					
					$interval = $_POST["interval"];
					
					
					
					//$interval = 4;
					if($the_year < $this_lawful_year){
					
						//$interval = 1;
					
					}
					
					$start_money = deleteCommas($_POST["final_value"]);
					$start_money_part = $start_money/$interval;
										
					//echo $interval;
					//echo "<br>".$start_money;
					//echo "<br>".$start_money_part;
					
					//calculate new money part...
					$start_money_part_array = array();
					
					$part_so_far = 0;
					
					for($i=0;$i<$interval;$i++){
						
						//
						//$part_to_push = round($start_money_part);
						$part_to_push = round($start_money_part,2);
						
						if($i == $interval -1){
						
							//for i is last interval
							//ccalculate real last interval
							$part_to_push = $start_money - $part_so_far;
							
						}
						
						array_push($start_money_part_array,$part_to_push);
						
						$part_so_far += $part_to_push;
						
					}
					
					//print_r($start_money_part_array);
					
					
					$from_what_date = date("Y-m-d");		
					
					if($the_year < $this_lawful_year){
					
						//$interval = 1;
						$to_what_date = "$this_lawful_year-10-01 00:00:00";
					
					}else{
						
						$to_what_date = "$the_year-10-01 00:00:00";
					}
					
					$total_length_date = dateDiffTs(strtotime(date($from_what_date)), strtotime(date($to_what_date))) ;	
										
					//echo $total_length_date;					
					
					$days_per_interval = floor($total_length_date/$interval);
					//echo ($days_per_interval);
					
					
					$part_used_to_get_pending = 0;
					
					for($ii=1; $ii <= $interval; $ii++){
						
						
						//echo $i;
						
						//has to rework on start money
						//$pending_start_money = $start_money - (($ii-1)*$start_money_part_array[$ii-1]);
						$pending_start_money = $start_money - $part_used_to_get_pending;
						
						$part_used_to_get_pending += $start_money_part_array[$ii-1];
						
						$pending_start_money_to_show = $start_money - $part_used_to_get_pending;
						
						
						?>
                    
                    
                         <tr>
                            <td ><div align="center"><?php echo $ii;?></div></td>
                           <td><?php 
                                
                                $selector_name = $ii;
								
								$next_date = ($ii - 1) * $days_per_interval;
								//echo $next_date;
								
								$this_date_time = date('Y-m-d', strtotime("+$next_date days"));
								
                                include "date_selector_js.php";
                                ?>
                                
                              <input type="hidden" id="<?php echo $ii;?>_is_date" value="0" size="5" />
                                
                                
                           </td>
                           
                            <td bgcolor="#EEFFEC" style="text-align: right">
                            
                            	<span id="<?php echo $ii;?>_total_money_to_show">
									  0
                              </span>
                            
                            	<input type="hidden" id="<?php echo $ii;?>_total_money" value="0" size="5" />
                                
                            
                            </td>
                            
                            <td align="right" style="text-align: right">
							
                            <?php echo formatNumber($start_money_part_array[$ii-1]);?>
                            
                            
                           </td>
                            <td style="text-align: right">
							
                                <span id="<?php echo $ii;?>_interest_money_to_show">
									  0
                              </span>
                            	<input type="hidden" id="<?php echo $ii;?>_interest_money" value="0" size="5" />
                              
                            
                            </td>
                            <td bgcolor="#FEE7E9" style="text-align: right">
                            	
                                
                                  <div align="right">
                           		  <?php echo formatNumber($pending_start_money_to_show);?>
                              </div>
                                
                                <input type="hidden" id="<?php echo $ii;?>_start_money" value="<?php echo ($pending_start_money);?>" size="5" />
							
                                
                           </td>
                           
                        </tr>
                    
                    <?php }?>
                    
                   
                	 
  </table>
  
  
  <br />
  
    <table cellpadding="3" style="margin:0px 0 0 20px; border-collapse:collapse;" border="0" cellspacing="0"  >
    
    	<tr>
        	<td valign="top">
            	<font style="color:#060">
            	หมายเหตุ: 
                </font>
            </td>
            
            <td>
            
            1. โปรแกรมช่วยคํานวณการส่งเงินเข้ากองทุนฯเท่านั้น
            <br />
2. กรุณาตรวจสอบความถูกอีกครั้ง ก่อนการส่งเงินเข้ากองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ
            
            </td>
        </tr>
	</table>        

  
  
  </div>
  
  
  </td>
            </tr>
            
             <tr>
                <td align="right" colspan="2">
                    <?php //include "bottom_menu.php";?>
                </td>
            </tr>  
            
		</table>                            
        
        </td>
    </tr>
    
</table>    

<script>

	function getTodayInterests(start_money, interest_days, interest_rate){
		
		//alert(start_money);
		//alert(interest_days);
		result_interest = start_money * interest_rate / <?php echo $ratio_to_use;?> / 365 * interest_days;
		result_interest = roundNumber(result_interest,2);
		//alert(result_interest);
		return result_interest;
		
	}


	function roundNumber(rnum, rlength) { 
		var newnumber = Math.round(rnum * Math.pow(10, rlength)) / Math.pow(10, rlength);
		return newnumber;
	}
	
	//getTodayInterests(162060 ,277, 7.5);


	function check_ddl_values(what){
		//alert(what);
		var e = document.getElementById(what + "_day");
		var this_day = e.options[e.selectedIndex].value;
		
		var e = document.getElementById(what + "_month");
		var this_month = e.options[e.selectedIndex].value -1;
		
		//alert(this_month);
		
		var e = document.getElementById(what + "_year");
		var this_year = e.options[e.selectedIndex].value;
		
		//this_day_month_year = this_day + "-" + this_month + "-" + this_year;
		//alert(this_day_month_year);
		
		if(this_day > 0 && this_month >= 0 && this_year > 0){
			
			
			document.getElementById(what + "_is_date").value = 1;	
			start_money = parseFloat(document.getElementById(what + "_start_money").value);
			
			//alert(start_money);
			var penalty_date = new Date (<?php echo $the_year;?>, 0, 31);	
			var selected_date = new Date (this_year, this_month, this_day);	
			
			//alert(penalty_date);
			//alert(selected_date);
			
			days_till_penalty = getDateDiffs(penalty_date,selected_date);
			//alert(days_till_penalty);					
			
			
			if(what == 1 || days_till_penalty <= 0){
				
				//first interval always calculate from penalty date
				var Date1 = penalty_date;	
				//alert(Date1);
					
				
			}else{
				
				last_what = what - 1;
				
				if(document.getElementById(last_what + "_is_date").value == 1){
				
					var e = document.getElementById(last_what + "_day");
					var last_day = e.options[e.selectedIndex].value;
					
					var e = document.getElementById(last_what + "_month");
					var last_month = e.options[e.selectedIndex].value -1;
					
					var e = document.getElementById(last_what + "_year");
					var last_year = e.options[e.selectedIndex].value;
					
					var Date1 = new Date (last_year, last_month, last_day);	
					
					last_days_till_penalty = getDateDiffs(penalty_date,Date1);
					
					if(last_days_till_penalty < 0){
						Date1 = penalty_date;
					}
					
				}else{
				
					return false;
					
				}
				
				
			}
						
			
			//alert(Date1);	
			
			var Date2 = new Date (this_year, this_month, this_day);
			
			//alert(Date2);	
			
			interest_days = getDateDiffs(Date1, Date2);
			
			//alert(interest_days);
			
			if(interest_days > 0){
				this_interest_money = getTodayInterests(start_money, interest_days, 7.5);					
			}else{
				this_interest_money = 0;
			}
			
			<?php if($the_year == 2011){?>
			this_interest_money = 0;
			<?php }?>
			
			//alert(this_interest_money);
			
			document.getElementById(what + "_interest_money").value = this_interest_money;		
			document.getElementById(what + "_interest_money_to_show").innerHTML = this_interest_money.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
			//alert(this_interest_money + parseFloat(start_money));
				
				
			
			if(what == <?php echo $interval*1?>){
				this_interest_money = this_interest_money + <?php echo $start_money_part_array[$interval-1]*1;?>;
			}else{				
				this_interest_money = this_interest_money + <?php echo $start_money_part*1;?>;
			}
			
			
			
			document.getElementById(what + "_total_money").value = this_interest_money;
			document.getElementById(what + "_total_money_to_show").innerHTML = this_interest_money.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
			
			
		}else{
			document.getElementById(what + "_is_date").value = 0;
		}
		
		//alert(this_day_month_year);
		//var Date1 = new Date (this_year, this_month, this_day);
		//var Date2 = new Date (2013, 12, 31);
		
		//days = getDateDiffs(Date1, Date2);
		
		//alert(days);
		
	}
	
	function getDateDiffs(Date1,Date2){
		var Days = Math.floor((Date2.getTime() - Date1.getTime())/(1000*60*60*24));
		//alert(Days);	
		return Days;
	}


	 function reCalculateRatio(){
          
		 // alert("what");                                                  
		
			employee_to_use = document.getElementById("Employees").value; 
			
			employee_to_use = employee_to_use.replace(/,/g,"");
			//
			//alert(employee_to_use);
			
			if(employee_to_use > 0){
				if(employee_to_use > <?php echo $ratio_to_use;?> || employee_to_use == <?php echo $ratio_to_use;?>){
					left_over = employee_to_use % <?php echo $ratio_to_use;?>;
					if(left_over <= <?php echo $ratio_to_use;?>/2){
						ratio_to_use = Math.floor(employee_to_use/<?php echo $ratio_to_use;?>);
					}else{
						ratio_to_use = Math.ceil(employee_to_use/<?php echo $ratio_to_use;?>);
					}
				}else{
					ratio_to_use = 0;
				}
			}
			
			document.getElementById("employee_ratio").innerHTML = toEmployee(ratio_to_use);
			document.getElementById("ratio_to_employ").value = ratio_to_use;
		
	}
	
	function reCalculateValue(){
		
		// $extra_employee; x  echo $wage_rate; x  echo $year_date; = </div></td>
		extra_employee = document.getElementById("ratio_to_employ").value ;
		
		//alert( document.getElementById("ratio_to_employ").value);
		
		extra_employee = parseFloat(extra_employee.replace(',', ''));
		
		//alert(extra_employee);
		
		//alert(parseFloat(document.getElementById("the_33").value.replace(',', '')));
		
		extra_employee = extra_employee - parseFloat(document.getElementById("the_33").value.replace(',', '')) ;
		extra_employee = extra_employee - parseFloat(document.getElementById("the_35").value.replace(',', '')) ;
		
		
		
		//alert(extra_employee);
		
		<?php 			
			if($the_year == 2011){					
		?>	
			wage_rate = document.getElementById("province_wage").innerHTML;		
		<?php }elseif($the_year == 2012){?>
			wage_rate = 159;			
		<?php }elseif($the_year == 2013){?>
			wage_rate = 222;
		<?php }elseif($the_year == 2014){?>
			wage_rate = 300;
		<?php }else{?>
			wage_rate = 300;
		<?php }?>
		
		
		//alert(wage_rate);
		
		
		year_date = 365;
		
		final_value = 0;
		
		//alert(extra_employee); return;
		
		if(extra_employee > 0){
			
			
			<?php 
			
				if($the_year == 2011){			
			
			?>		
				final_value = extra_employee * (wage_rate/2) * year_date ;			
			<?php }else{ ?>
				final_value = extra_employee * wage_rate * year_date ;
			<?php }?>
		
		}
		
		document.getElementById("final_value").value = final_value;
		document.getElementById("final_value_to_show").innerHTML = final_value.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
		
	}


	
	
	
	function reCalculateAllValues(){
	
		<?php for($ii=1; $ii <= $interval; $ii++){?>
			check_ddl_values('<?php echo $ii?>');
		<?php }?>
		return 1;
	}
	
	
	
	
	function changeProvinceWage(){
		
		var e = document.getElementById("Province");
		var this_wage_id = e.options[e.selectedIndex].value;
		//alert(this_wage);
		
		if(this_wage_id==1){this_wage = 215}
		if(this_wage_id==2){this_wage = 215}
		if(this_wage_id==3){this_wage = 215}
		if(this_wage_id==4){this_wage = 215}
		if(this_wage_id==5){this_wage = 190}
		if(this_wage_id==6){this_wage = 174}
		if(this_wage_id==7){this_wage = 182}
		if(this_wage_id==8){this_wage = 176}
		if(this_wage_id==9){this_wage = 167}
		if(this_wage_id==10){this_wage = 193}
		if(this_wage_id==11){this_wage = 196}
		if(this_wage_id==12){this_wage = 189}
		if(this_wage_id==13){this_wage = 179}
		if(this_wage_id==14){this_wage = 169}
		if(this_wage_id==15){this_wage = 193}
		if(this_wage_id==16){this_wage = 183}
		if(this_wage_id==17){this_wage = 170}
		if(this_wage_id==18){this_wage = 173}
		if(this_wage_id==19){this_wage = 183}
		if(this_wage_id==20){this_wage = 166}
		if(this_wage_id==21){this_wage = 162}
		if(this_wage_id==22){this_wage = 160}
		if(this_wage_id==23){this_wage = 171}
		if(this_wage_id==24){this_wage = 166}
		if(this_wage_id==25){this_wage = 165}
		if(this_wage_id==26){this_wage = 163}
		if(this_wage_id==27){this_wage = 165}
		if(this_wage_id==28){this_wage = 167}
		if(this_wage_id==29){this_wage = 171}
		if(this_wage_id==30){this_wage = 173}
		if(this_wage_id==31){this_wage = 169}
		if(this_wage_id==32){this_wage = 163}
		if(this_wage_id==33){this_wage = 166}
		if(this_wage_id==34){this_wage = 167}
		if(this_wage_id==35){this_wage = 166}
		if(this_wage_id==36){this_wage = 164}
		if(this_wage_id==37){this_wage = 165}
		if(this_wage_id==38){this_wage = 180}
		if(this_wage_id==39){this_wage = 169}
		if(this_wage_id==40){this_wage = 165}
		if(this_wage_id==41){this_wage = 163}
		if(this_wage_id==42){this_wage = 163}
		if(this_wage_id==43){this_wage = 161}
		if(this_wage_id==44){this_wage = 159}
		if(this_wage_id==45){this_wage = 166}
		if(this_wage_id==46){this_wage = 163}
		if(this_wage_id==47){this_wage = 166}
		if(this_wage_id==48){this_wage = 168}
		if(this_wage_id==49){this_wage = 168}
		if(this_wage_id==50){this_wage = 162}
		if(this_wage_id==51){this_wage = 165}
		if(this_wage_id==52){this_wage = 163}
		if(this_wage_id==53){this_wage = 163}
		if(this_wage_id==54){this_wage = 166}
		if(this_wage_id==55){this_wage = 180}
		if(this_wage_id==56){this_wage = 181}
		if(this_wage_id==57){this_wage = 167}
		if(this_wage_id==58){this_wage = 215}
		if(this_wage_id==59){this_wage = 215}
		if(this_wage_id==60){this_wage = 172}
		if(this_wage_id==61){this_wage = 179}
		if(this_wage_id==62){this_wage = 172}
		if(this_wage_id==63){this_wage = 174}
		if(this_wage_id==64){this_wage = 184}
		if(this_wage_id==65){this_wage = 186}
		if(this_wage_id==66){this_wage = 221}
		if(this_wage_id==67){this_wage = 172}
		if(this_wage_id==68){this_wage = 185}
		if(this_wage_id==69){this_wage = 173}
		if(this_wage_id==70){this_wage = 176}
		if(this_wage_id==71){this_wage = 173}
		if(this_wage_id==72){this_wage = 175}
		if(this_wage_id==73){this_wage = 173}
		if(this_wage_id==74){this_wage = 170}
		if(this_wage_id==75){this_wage = 172}
		if(this_wage_id==76){this_wage = 171}

		
		document.getElementById("province_wage").innerHTML = this_wage;
	}
	
	<?php if($the_year == 2011){?>
	
		changeProvinceWage();
	
	<?php }?>


	//below are on-load functions
	reCalculateRatio();
	reCalculateValue();
	reCalculateAllValues();

</script>



</body>
</html>
