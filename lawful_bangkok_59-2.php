<HTML >
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
  	<style>
	body {
		font-size: 14px;
		/*line-height: 30px;
		margin: 0px;
		padding: 0px;*/
	}
	
	@page{
		/*top-margin: 5px;*/
	}
	
	.underline{
		border-bottom: 1px dotted #000;	
	}
	</style>
  
</HEAD>
<body >
<?php 
	
	include "db_connect.php";
	
	//select company information
	//print_r($_POST);
	
	$company_row = getFirstRow("select * from company where cid = '$_POST[the_cid]'");
	
	//print_r($company_row);
	
	//yoes 20151123 --> also see which province is this company
	$is_provincial = 0;
	$province_name = "กรุงเทพมหานคร";
	$doc_prefix = "๐";
	$the_dear = "อธิบดีกรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ";
	$the_organization = "กรมส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ";
	
	if($company_row[Province] != 1){
		
		$is_provincial = 1;
		$province_name = getFirstItem("select province_name from provinces where province_id = '$company_row[Province]'");
		$doc_prefix = "๑";
		$the_dear = "พัฒนาสังคมและความมั่นคงของมนุษย์จังหวัด".$province_name;
		$the_organization = "สํานักงานพัฒนาสังคมและความมั่นคงของมนุษย์จังหวัด".$province_name;
	}

	//echo $province_name;


?>
<table border="0" align="center" cellpadding="3" cellspacing="0" width="100%"   >

            <tr>
              <td valign="top" width="300">
              	ที่……………………
              </td>
              <td valign="top">
              	ชื่อสถานประกอบการ <span style="font-size:12xp;" class="underline"><?php 
				
					$company_name_to_use = formatCompanyName($company_row["CompanyNameThai"],$company_row["CompanyTypeCode"]); 
					echo $company_name_to_use;
				
				?></span>
                
                
                
              </td>
              <td valign="top" width="70" style="font-size: 18px;">
              
              	
              	จพ <?php echo $doc_prefix;?>-๑
              </td>
              
            </tr>
            <tr>
              <td valign="top">&nbsp;</td>
              <td valign="top">
              	ที่อยู่ <span style="font-size:12xp;" class="underline"><?php $address_to_use = getAddressText($company_row); echo $address_to_use;?></span>
              </td>
              <td valign="top">&nbsp;</td>
            </tr>
            
            
</table>    



<table border="0" align="center" cellpadding="3" cellspacing="0" width="100%"  style="margin-top:5px;" >

            <tr>
              <td valign="top" align="center" >
              <div align="center" >
              	วันที่ ………………………………..
                </div>
              </td>
            </tr>
            
</table>    

<table border="0" align="center" cellpadding="5" cellspacing="0" width="100%"  style="margin-top:5px;" >

            <tr>
              <td valign="top"  >
              เรื่อง &nbsp;การปฏิบัติตามกฎหมายการจ้างงานคนพิการ ประจําปี ๒๕๕๙
              </td>
            </tr>
            <tr>
              <td valign="top"  >
             เรียน <?php echo $the_dear;?>
              </td>
            </tr>
            
</table>    


<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%"  style="margin-top:5px;" >

            <tr>
              <td valign="top" width="90">
              	<table  >
                	<tr>
                    	<td>
                        		สิ่งที่ส่งมาด้วย
                        </td>
                    </tr>
                </table>
              </td>
               <td valign="top" width="20">
              
                
                 <table  >
                	<tr>
                    	<td>
                        		๑.
                        </td>
                    </tr>
                 </table>
                
              </td>
              <td valign="top" >
                
                <table  >
                	<tr>
                    	<td>
                        	 แบบดําเนินการตามมาตรา ๓๓ (จพ <?php echo $doc_prefix;?>-๒) พร้อมสําเนาสัญญาจ้างงาน, สําเนาบัตร
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	 ประจําตัวคนพิการ, สปส.๑-๑๐ ส่วนที่ ๑ ประจําเดือนตุลาคม ๒๕๕๘
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        	 และส่วนที่ ๒ ที่ระบุชื่อคนพิการของเดือนมกราคม ๒๕๕๙ จํานวน <span class="underline"><?php echo getFirstItem("
							 												
																			select count(*) from lawful_employees_company
																			where 
																			le_cid = '$_POST[the_cid]'
																			and
																			le_year = '$_POST[the_year]'
																		
																		");?></span> ชุด
                        </td>
                    </tr>
                </table>
                
                
                
              </td>
            
              
            </tr>
            <tr>
              <td valign="top">&nbsp;</td>
              <td valign="top"><table  >
                <tr>
                  <td>๒. </td>
                </tr>
              </table></td>
              <td valign="top" >
              
              
              <?php 
			  	
				//try get payment type
				$company_pay_data = getFirstRow("select * from payment_company where CID = '$_POST[the_cid]' and Year = '$_POST[the_year]'");
			  
			  
			  ?>
              
              
              	<table  >
                <tr>
                  <td>แบบดําเนินการตามมาตรา๓๔ (จพ <?php echo $doc_prefix;?>-๓) พร้อมด้วย</td>
                </tr>
                <tr >
                  <td style="padding-top:5px;">
                  
                  <?php 
				  
				  	if($company_pay_data[PaymentMethod] == "Cheque"){
						?>
                       
					 &#9745;  เช็คธนาคาร...........................เลขที่...........................<span class="underline"><?php //echo $company_pay_data[RefNo];?> ลงวันที่............................ </td>                       
                        
                  <?php
					}else{
				  
				  ?>                 
                  
	                 &#9744;  เช็คธนาคาร...........................เลขที่............................. ลงวันที่............................ </td>
                 
                 <?php }?>
                </tr>
                <tr>
                  <td>
                  
                  <?php 
				  
				  	if($company_pay_data[PaymentMethod] == "Cash"){
						echo " &#9745;";
					}else{
						echo " &#9744;";	
					}
				  
				  ?>
                  
                   เงินสด</td>
                </tr>
                <tr>
                  <td>
                  <?php 
				  
				  	if($company_pay_data[PaymentMethod] == "Note"){
						echo " &#9745;";
					}else{
										  
				  ?>
                  
	                   &#9744; ธนาณัติเลขที่........................ลงวันที่..........................<span class="underline"><?php }?>
                   
                   
                   </td>
                   
                </tr>
              </table></td>
            </tr>
            <tr>
              <td valign="top">&nbsp;</td>
              <td valign="top"><table  >
                <tr>
                  <td>๓. </td>
                </tr>
              </table></td>
              <td valign="top" ><table  >
                <tr>
                  <td>สําเนาหนังสือแจ้งผลอนุมัติการใช้สิทธิตามมาตรา ๓๕ พร้อมสําเนาสัญญาสัมปทานฯ</td>
                </tr>
                <tr >
                  <td style="padding-top:5px;">สําเนาบัตรประจําตัวคนพิการและสําเนาบัตรประชาชนผู้ดูแลคนพิการจํานวน <span class="underline"><?php echo getFirstItem("
							 												
																			select count(*) from curator_company
																			where 
																			curator_lid = '$_POST[the_lid]'
																			and curator_parent = 0																			
																		
																		");?> </span> ชุด </td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td valign="top">&nbsp;</td>
              <td valign="top"><table  >
                <tr>
                  <td>๔. </td>
                </tr>
              </table></td>
              <td valign="top" ><table  >
                <tr>
                  <td> แบบรายละเอียดจํานวนลูกจ้าง (จพ <?php echo $doc_prefix;?>-๔) เฉพาะสถานประกอบการที่มีสาขา</td>
                </tr>
                
                
                <?php 
				
				
					//yoes 20151025
					//try get branch - active only
					
					$the_company_code = getFirstItem("select CompanyCode from company where cid = '$_POST[the_cid]'");

					//get active only
					$get_branch_sql = "select * from 
											company 
										where 
											CompanyCode = '".$the_company_code."' 										
											and
											is_active_branch = 1
										
										order by BranchCode asc";
				
					$branch_result = mysql_query($get_branch_sql);
					
					
					//yoes 20160816 --> also get from TEMP branch
					//get active only
					$get_branch_sql_temp = "select * from 
											company_company
										where 
											CompanyCode = '".$the_company_code."' 										
											and
											is_active_branch = 1
										
										order by BranchCode asc";
				
					$branch_result_temp = mysql_query($get_branch_sql_temp);
					
					//get total branch number
					$num_system_brach = mysql_num_rows($branch_result)+mysql_num_rows($branch_result_temp);
					
				
				?>
                
                
                <tr >
                  <td style="padding-top:5px;">จํานวน <span class="underline"><?php echo $num_system_brach;?></span> ฉบับ </td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td valign="top">&nbsp;</td>
              <td colspan="2" valign="top">ตามที่<?php echo $the_organization;?>ได้มีหนังสือขอให้สถานประกอบการปฏิบัติตามกฎหมาย</td>
            </tr>
            <tr>
              <td colspan="3" valign="top">ในการจ้างงานคนพิการ ประจําปี ๒๕๕๙ โดยให้รายงานผลการปฏิบัติภายใน
              วันที่  ๓๑ มกราคม ๒๕๕๙ นั้น </td>
            </tr>
            <tr>
              <td valign="top">&nbsp;</td>
              <td colspan="2" valign="top">ในการนี้ ( ชื่อสถานประกอบการ ) ..<?php echo $company_name_to_use;?>.. </td>
            </tr>
            <tr>
              <td colspan="3" valign="top">เลขทะเบียนนายจ้าง(ตามกองทุนประกันสังคม ๑๐ หลัก) ..<?php echo $company_row["CompanyCode"]?>.. </td>
            </tr>
            <tr>
              <td colspan="3" valign="top">ขอแจ้งรายละเอียดเกี่ยวกับการปฏิบัติตามกฎหมาย ดังต่อไปนี้ </td>
            </tr>
           
            
            
</table>

<table border="0" align="center" cellpadding="0" cellspacing="0" width="100%"  style="margin-top:5px;" >

            <tr>
           	  <td valign="top" width="90">
              	<table  >
                	<tr>
                    	<td>&nbsp;
                        		
                      </td>
                    </tr>
                </table>
              </td>
               <td valign="top" >
              
                
                 <table cellpadding="3" border="0" >
                	<tr>
                    	<td>
                        		๑.
                        </td>
                        <td>จํานวนลูกจ้างที่มิใช่คนพิการในสถานประกอบการทั้งหมด</td>
                        <td width="10" align="right" >=</td>
                        <td align="center" style="border-bottom: 1px dotted #000000;"><div align="center" ><?php echo formatEmployee($_POST[employee_to_use])?></div></td>
                        <td>คน</td>
                    </tr>
                	<tr>
                	  <td>&nbsp;</td>
                	  <td><span style="padding-top:5px;">(ณ วันที่ ๑ ตุลาคม ๒๕๕๘) </span></td>
                	  <td>&nbsp;</td>
              	  </tr>
                	<tr>
                	  <td>๒.</td>
                      
                	  <td>จํานวนคนพิการที่สถานประกอบการต้องรับเข้าทํางาน</td>
                      <td align="right">=</td>
                	  <td align="center" style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $_POST[final_employee]?></span></td>
                      <td>คน</td>
              	  </tr>
                	<tr>
                	  <td>&nbsp;</td>
                	  <td ><span style="padding-top:5px;">ตามอัตราส่วน (๑๐๐ : ๑) </span></td>
                	  <td>&nbsp;</td>
              	  </tr>
                	<tr>
                	  <td>๓.</td>                      
                	  <td>มีคนพิการทํางาน ตามมาตรา ๓๓ แล้ว</td>
                      <td align="right">=</td>
                	  <td align="center" style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $_POST[hire_numofemp]?></span></td>
                      <td>คน</td>
              	  </tr>
                	<tr>
                	  <td>๔.</td>                      
                	  <td>ขอส่งเงินเข้ากองทุนฯ ตามมาตรา ๓๔ พร้อมดอกเบี้ย (ถ้ามี)</td>
                      <td align="right">=</td>
                	  <td align="center" style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $_POST[extra_emp]?></span></td>
                      <td>คน</td>
              	  </tr>
                	<tr>
                	  <td>&nbsp;</td>
                	  <td>&nbsp;</td>
                      <td align="right">=</td>
                	  <td align="center" style="border-bottom: 1px dotted #000000;"><span style="padding-top:0px;"><span class=""><?php echo formatNumber($_POST[final_money])?></span> </span></td>
                       <td>บาท</td>
              	  </tr>
                	<tr>
                	  <td>๕.</td>
                	  <td>จัดให้มีการสัมปทานฯตามมาตรา ๓๕</td>
                       <td align="right">=</td>
                	  <td align="center" style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $_POST[curator_user]?></span> </td>
                      <td>คน</td>
              	  </tr>
                	<tr>
                	  <td>&nbsp;</td>
                	  
                      <td colspan="2" align="right">มูลค่า</td>
                	  <td style="border-bottom: 1px dotted #000000;"></td>
                      <td>บาท</td>
              	  </tr>
                 </table>
                
              </td>
  </tr>
</table>

<table>  
            <tr>
              <td valign="top" width="90">&nbsp;</td>
              <td valign="top" ><table cellpadding="3" >
                <tr>
                  <td colspan="3">ทั้งนี้เห็นว่าได้ปฏิบัติตามกฎหมายกําหนด </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td><span style="padding-top:5px;">&#9675; ครบถ้วน &nbsp;&nbsp; &#9675;ไม่ครบถ้วน </span></td>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td colspan="3">จึงเรียนมาเพื่อโปรดพิจารณา</td>
                </tr>
              </table></td>
            </tr>
            
</table>            


<div align="center">
<img src="lawful_sign.jpg" height="145px;" />
</div>
                


<pagebreak orientation="landscape" type="NEXT-ODD"  />




<table border="0" align="center" cellpadding="3" cellspacing="0" width="100%"   >
  <tr>
    <td valign="top" width="300"></td>
    <td valign="top"></td>
    <td valign="top" width="70" style="font-size: 18px;"> จพ <?php echo $doc_prefix;?>-๒ </td>
  </tr>
  
</table>
<table border="0" align="center" cellpadding="5" cellspacing="0" width="100%"  style="margin-top:5px;" >
  <tr>
    <td valign="top" align="center"  >แบบรายงานผลการปฏิบัติตามกฎหมายในการจ้างงานคนพิการ ประจําปี ๒๕๕๙</td>
  </tr>
  
</table>




<table border="0" align="center" cellpadding="0" cellspacing="0" width="80%"   >
  <tr>
    <td valign="bottom" align="left" width="150" >ชื่อสถานประกอบการ</td>
    <td valign="bottom" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $company_name_to_use?></span> </td>
    <td valign="bottom" align="left" width="150" >ประเภทกิจการ</td>
    <td valign="bottom" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php 
	
																							
																							echo getFirstItem("																							
																							
																								select
																									BusinessTypeName
																								from
																									businesstype
																								where
																									BusinessTypeCode
																									=
																									'$company_row[BusinessTypeCode]'																									
																							
																							" );
																							
																							?></span> </td>
  </tr>  
</table>
  

<table border="0" align="center" cellpadding="0" cellspacing="0" width="80%"   >
  <tr>
    <td valign="bottom" align="left" width="350" >เลขทะเบียนนายจ้าง (ตามกองทุนประกันสังคม ๑๐ หลัก) </td>
    <td valign="bottom" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $company_row["CompanyCode"]?></span> </td>
  </tr>  
</table>
  
  
 
<table border="0" align="center" cellpadding="0" cellspacing="0" width="80%"   >
  <tr>
    <td valign="top" align="left" width="50" >ที่ตั้งเลขที่</td>
    <td valign="top" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $company_row["Address1"]?></span> </td>
    <td valign="top" align="left" width="50" >ซอย</td>
    <td valign="top" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $company_row["Soi"]?></span> </td>
  </tr>  
</table> 



<table border="0" align="center" cellpadding="0" cellspacing="0" width="80%"   >
  <tr>
   
    <td valign="top" align="center"  style="border-bottom: 1px dotted #000000;"><?php echo $company_row["Subdistrict"]?> <?php echo $company_row["District"]?> </td>
    
  </tr>  
</table> 
  
<table border="0" align="center" cellpadding="0" cellspacing="0" width="80%"   >  
 
  <tr>
    <td valign="top" align="left" width="50" >จังหวัด</td>
    <td valign="top" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $province_name?></span> </td>
    <td valign="top" align="left" width="50" >หมายเลขโทรศัพท์</td>
    <td valign="top" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $company_row["Telephone"]?></span> </td>
  </tr>  


</table>



<table border="0" align="center" cellpadding="0" cellspacing="0" width="60%"   >
  <tr>
    <td valign="bottom" align="left" width="150" >ลูกจ้างที่ไม่พิการจํานวน</td>
    <td valign="bottom" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo formatEmployee($_POST[employee_to_use])?></span> </td>
    <td valign="bottom" align="left" width="150" >ลูกจ้างพิการจํานวน</td>
    <td valign="bottom" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo formatEmployee($_POST[hire_numofemp])?></span> </td>
  </tr>  
</table>

<br>

<table border="1" align="center" cellpadding="5" cellspacing="0" width="100%"  style="border-collapse:collapse; font-size: 12px; padding-top: 10px;" >
	<tr>
    	<td rowspan="2" align="center">
        	ที่ 
        </td>
        <td rowspan="2" align="center">
        	ชื่อ – สกุล (คนพิการ) 
        </td>
        <td rowspan="2" align="center">
        	เพศ

        </td>
        <td rowspan="2" align="center">
        	อายุ(ปี)
        </td>
        <td rowspan="2" align="center">
        	การศึกษา
        </td>
        <td rowspan="2" align="center">
        เลขทะเบียนคนพิการ<br />
(ตามบัตรประจําตัวคนพิการ)
        </td>
        <td rowspan="2" align="center">
        	ลักษณะความพิการ
        </td>
        <td colspan="2" align="center">
        	ระยะเวลาการจ้าง
        </td>
        <td rowspan="2" align="center">
        ค้าจ้าง/<br />
เงินเดือน
        </td>
        <td rowspan="2" align="center">
        	ตําแหน่งงาน 
        </td>
       
    </tr>
	<tr>
	  <td align="center">วันเริ่มงาน</td>
	  <td align="center">วันสิ้นสุด </td>
  </tr>
  
  <?php
  
  
  $sql = "
  		
		select 
			* 
		from 
			lawful_employees_company
		where 
			le_cid = '$_POST[the_cid]'
			and
			le_year = '$_POST[the_year]'  
  
  	";
	
	$employees_result = mysql_query($sql);
	
	
  
  $the_count = 0;
  //for($i=0;$i<9;$i++){
	 while($employees_row = mysql_fetch_array($employees_result)){
		 
		 $the_count++;
		 
	  ?>
	<tr>
	  <td align="center"><?php echo $the_count;?></td>
	  <td align="left"><?php echo $employees_row[le_name];?></td>
	  <td align="left"><?php echo formatGender($employees_row[le_gender]);?></td>
	  <td align="left"><?php echo $employees_row[le_age];?></td>
	  <td align="left"><?php echo $employees_row[le_education];?></td>
	  <td align="left"><?php echo $employees_row[le_code];?></td>
	  <td align="left"><?php echo $employees_row[le_disable_desc];?></td>
	  <td align="left"><?php echo formatDateThaiShort($employees_row[le_start_date]);?></td>
	  <td align="left">&nbsp;</td>
	  <td align="left"><?php echo formatNumber($employees_row[le_wage]);?></td>
	  <td align="left"><?php echo $employees_row[le_position];?></td>
  </tr>
  <?php }?>
  
  
  <?php 
  
  	//yoes 20151025 -- add extra rows here?
  	$left_over = 11 -  $the_count;
  
  	//$left_over = -1;
  
  	for($i=0;$i<$left_over ;$i++){
		
		?>
        
        <tr>
          <td align="left">&nbsp;</td>
          
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
          <td align="left">&nbsp;</td>
      </tr>
     
     
    <?php
		
		
	}
  
  ?>
  
</table>    

<br />

<div align="center">
<img src="lawful_sign_2.jpg" height="125px;" />
</div>


<pagebreak orientation="portrait" type="NEXT-EVEN"  />



<table border="0" align="center" cellpadding="3" cellspacing="0" width="100%"   >
  <tr>
    <td valign="top" width="300"></td>
    <td valign="top"></td>
    <td valign="top" width="70" style="font-size: 18px;"> จพ <?php echo $doc_prefix;?>-๓ </td>
  </tr>
</table>


<table border="0" align="center" cellpadding="0" cellspacing="0" width="90%"   >
  <tr>
    <td valign="top" align="center" colspan="2"  >แบบส่งเงินเข้ากองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ ประจําปี ๒๕๕๙</td>
  </tr>
  <tr>
    <td valign="top" align="left" width="150" >ชื่อนายจ้าง/สถานประกอบการ  </td>
    <td valign="top" align="center" width="380" style="border-bottom: 1px dotted #000000;"><?php echo $company_name_to_use;?> </td>
  </tr>  
</table>

<table border="0" align="center" cellpadding="0" cellspacing="0" width="90%"   >
  <tr>
    <td valign="bottom" align="left" width="350" >เลขทะเบียนนายจ้าง (ตามกองทุนประกันสังคม ๑๐ หลัก) </td>
    <td valign="bottom" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $company_row["CompanyCode"]?></span> </td>
  </tr>  
</table>
  
  
 
<table border="0" align="center" cellpadding="0" cellspacing="0" width="90%"   >
  <tr>
    <td valign="top" align="left" width="50" >ที่ตั้งเลขที่</td>
    <td valign="top" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $company_row["Address1"]?></span> </td>
    <td valign="top" align="left" width="50" >ซอย</td>
    <td valign="top" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $company_row["Soi"]?></span> </td>
  </tr>  
</table> 



<table border="0" align="center" cellpadding="0" cellspacing="0" width="90%"   >
  <tr>
   
    <td valign="top" align="center"  style="border-bottom: 1px dotted #000000;"><?php echo $company_row["Subdistrict"]?> <?php echo $company_row["District"]?> </td>
    
  </tr>  
</table> 
  
<table border="0" align="center" cellpadding="0" cellspacing="0" width="90%"   >  
 
  <tr>
    <td valign="top" align="left" width="50" >จังหวัด</td>
    <td valign="top" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $province_name?></span> </td>
    <td valign="top" align="left" width="50" >หมายเลขโทรศัพท์</td>
    <td valign="top" align="center"  style="border-bottom: 1px dotted #000000;"><span class=""><?php echo $company_row["Telephone"]?></span> </td>
  </tr>  


</table>


<br>

<table border="1" align="center" cellpadding="5" cellspacing="0" width="100%"  style="border-collapse:collapse; font-size: 12px;" >
  <tr>
    <td align="center" width="500">รายละเอียด</td>
    <td align="center"> จำนวน<br />
      (คน/บาท)</td>
    <td align="center">หมายเหตุ</td>
  </tr>
 
  <tr>
    <td>๑. จํานวนลูกจ้างที่มิใช่คนพิการในสถานประกอบการ (ณ วันที่ ๑ ตุลาคม ๒๕๕๘)</td>
    <td align="center"><?php echo formatEmployee($_POST[employee_to_use])?></td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>๒. จํานวนคนพิการที่สถานประกอบการต้องรับเข้าทํางานตามอัตราส่วน (๑๐๐:๑) </td>
    <td align="center"><?php echo $_POST[final_employee]?></td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>๓. มีคนพิการที่ทํางานอยู่แล้ว ตามมาตรา ๓๓ </td>
    <td align="center"><?php echo $_POST[hire_numofemp]?></td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>๔. ให้สัมปทานฯ ตามมาตรา ๓๕ </td>
    <td align="center"><?php echo $_POST[curator_user]?></td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>๕ ส่งเงินเข้ากองทุนฯตามมาตรา ๓๔ (๓๐๐ x ๓๖๕ x จํานวนคนพิการที่ไม่ได้รับเข้าทํางาน)</td>
    <td align="center">300 x 365 x <?php echo  $_POST[final_employee] - ($_POST[hire_numofemp]+$_POST[curator_user]) ?></td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;&nbsp;&nbsp;(วิธีคํานวณ อัตราต่ําสุดของอัตราค่าจ้างขั้นต่ําตามกฎหมายว่าด้วยการ </td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;&nbsp;&nbsp;คุ้มครองแรงงานที่ใช้บังคับครั้งหลังสุดในปีก่อนปีที่มีหน้าที่ส่งเงินเข้ากองทุนฯ </td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;&nbsp;&nbsp;คูณด้วยสามร้อยหกสิบห้า และคูณด้วยจํานวนคนพิการที่ไม่ได้รับเข้าทํางาน) </td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>๖. จํานวนเงินที่ต้องส่งเข้ากองทุนฯ รวมเป็นเงินทั้งสิ้น </td>
    <td align="center"><?php echo formatNumber($_POST[final_money])?></td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;&nbsp;&nbsp;โดยขอส่งเป็น</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>    
	
    
    &nbsp;&nbsp;&nbsp; <?php 
    
    if($company_pay_data[PaymentMethod] == "Cheque"){						    
  		echo "&#9745;";    
    }else{    
    	echo "&#9744;";     
    }
    ?>     เช็คขีดคร่อมสั่งจ่าย &ldquo;กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ&rdquo;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;&nbsp;&nbsp;(A FUND FOR EMPOWERMENT OF PERSONS WITH DISABILITIES) </td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;&nbsp;&nbsp;
    <?php 
    
    if($company_pay_data[PaymentMethod] == "Cash"){						    
  		echo "&#9745;";    
    }else{    
    	echo "&#9744;";     
    }
    ?>    
    &nbsp;เงินสด</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;&nbsp;&nbsp;
    
    <?php 
    
    if($company_pay_data[PaymentMethod] == "Note"){						    
  		echo "&#9745;";    
    }else{    
    	echo "&#9744;";     
    }
    ?>    
    
     ธนาณัติสั่งจ่าย &ldquo;กองทุนส่งเสริมและพัฒนาคุณภาพชีวิตคนพิการ&rdquo;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" align="center">
    
    	
    	<img src="lawful_sign_3.jpg" height="105px;" />
        
    </td>
  </tr>
  
</table>
<table border="1" align="center" cellpadding="5" cellspacing="0" width="100%"  style="border-collapse:collapse; font-size: 12px; margin: 5px 0;" >

  <tr>
    <td colspan="3" align="center">
    
    	
    	<img src="lawful_sign_4.jpg" height="255px;" />
        
    </td>
  </tr>
  
</table>

<div align="center">
<img src="lawful_sign_5.jpg" height="25px;" />
</div>




<pagebreak orientation="portrait" type="NEXT-ODD"  />

<table border="0" align="center" cellpadding="3" cellspacing="0" width="100%"   >
  <tr>
    <td valign="top" width="300"></td>
    <td valign="top"></td>
    <td valign="top" width="70" style="font-size: 18px;"> จพ <?php echo $doc_prefix;?>-๔ </td>
  </tr>
</table>
<table border="0" align="center" cellpadding="5" cellspacing="0" width="100%"  style="margin-top:5px;" >
  <tr>
    <td valign="top" align="center"  >รายละเอียดจํานวนลูกจ้างแต่ละสาขาของสถานประกอบการ</td>
  </tr>
  <tr>
    <td valign="top" align="center"  >เฉพาะกรณีที่มีสาขา (รวมสํานักงานใหญ่ด้วย)
</td>
  </tr>
</table>
<table border="1" align="center" cellpadding="5" cellspacing="0" width="100%"  style="border-collapse:collapse; font-size: 12px; margin-bottom: 10px;" >
  <tr>
    <td align="center" width="50" >ลําดับ</td>
    <td align="center">เลขทะเบียนนายจ้าง</td>
    <td align="center">เลขสาขา </td>
    <td align="center" width="180" >จํานวนลูกจ้างที่มิใช่คนพิการ  <br />
    ณ ๑ ต.ค.๕๘ (คน) </td>
    <td align="center" width="130">หมายเหตุ </td>
  </tr>
  
  
    
  
   <?php
   
   
   //this one we get from beginning of script
   
   $the_count = 0;
   
   while($branch_row = mysql_fetch_array($branch_result)){
    //for($i=0;$i<25;$i++){
		
		 $the_count++;
		 
		 
		 //temp employees in company
		 $temp_value = getFirstItem("
														
				select
					employees
				from
					company_employees_company
				where
					cid = '$branch_row[CID]'
					and
					lawful_year = '$_POST[the_year]'
				
				
				");
				
		//override old data here
		
		if(strlen($temp_value) > 0){
		
			$branch_row["Employees"] = $temp_value;
		
		}
		
		$sum_employees += $branch_row["Employees"];
		
		?>
  <tr>
    <td><?php echo $the_count; //echo $branch_row[CID];?></td>
    <td align="left"><?php echo $branch_row["CompanyCode"];?></td>
    <td align="left"><?php echo $branch_row["BranchCode"];?></td>
    <td align="right"><?php echo formatEmployee($branch_row["Employees"]);?></td>
    <td align="left">&nbsp;</td>
  </tr>
  <?php }?>
  
  
  <?php
  
  	
//yoes 20160816 --> TEMP BRANCH
  
   while($branch_row = mysql_fetch_array($branch_result_temp)){
    //for($i=0;$i<25;$i++){
		
		 $the_count++;
		 
		
		$sum_employees += $branch_row["Employees"];
		
		?>
  <tr>
    <td><?php echo $the_count; //echo $branch_row[CID];?></td>
    <td align="left"><?php echo $branch_row["CompanyCode"];?></td>
    <td align="left"><?php echo $branch_row["BranchCode"];?></td>
    <td align="right"><?php echo formatEmployee($branch_row["Employees"]);?></td>
    <td align="left">&nbsp;</td>
  </tr>
  <?php }?>
  
   <?php 
  
  	//yoes 20151025 -- add extra rows here?
  	$left_over = 25 -  $the_count;
  
  	//$left_over = -1;
  
  	for($i=0;$i<$left_over ;$i++){
		
		?>
        
        <tr>
        <td></td>
        <td align="left"></td>
        <td align="left"></td>
        <td align="right"></td>
        <td align="left">&nbsp;</td>
      </tr>
 
        
        
        <?php }?>
  
   <tr>
    <td colspan="3" align="right">รวมทั้งสิ้น</td>
    <td align="right"><?php echo formatEmployee($sum_employees);?></td>
    <td align="center">&nbsp;</td>
  </tr>
    
</table>  

 <div align="center">
<img src="lawful_sign_6.jpg" height="125px;" />
</div>
 
</body>        
</HTML>