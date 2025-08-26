<?php

	include "db_connect.php";
	include "session_handler.php";
	
	if($_GET["mode"]=="search"){
		$mode = "search";
		
	}elseif($_GET["mode"]=="letters"){
		$mode = "letters";
	}

?>
 <?php
	
	//MAIN SQL
	
	//default conditions -> select non-payback only
	$condition_sql = ' and is_payback != 1';
	
	
	//make it so it filter as %LIKE% instead
		if(strlen($_POST["ReceiptNo"]) > 0){
			
			
			$name_exploded_array = explode(" ",doCleanInput($_POST["ReceiptNo"]));
			
			//print_r($name_exploded_array);
			for($i=0; $i<count($name_exploded_array);$i++){
			
				if(strlen(trim($name_exploded_array[$i]))>0){
					//echo $name_exploded_array[$i];
					$use_condition = 1;
					$condition_sql .= " and ReceiptNo like '%".doCleanInput($name_exploded_array[$i])."%'";
					
				}
			
			}
			
		}
	
	//book receipt no filter
	if(strlen($_POST["BookReceiptNo"]) > 0){
		$condition_sql .= " and BookReceiptNo = '".doCleanInput($_POST["BookReceiptNo"])."'";
	}
	
	////"Year" filter
	if(strlen($_POST["ddl_year"]) > 0){
		$condition_sql .= " and ReceiptYear = '".($_POST["ddl_year"])."'";
	}
	
	////"PaymentMethod" filter
	if(strlen($_POST["PaymentMethod"]) > 0){
		$condition_sql .= " and PaymentMethod = '".($_POST["PaymentMethod"])."'";
	}
	
	////"Amount" filter
	if(strlen($_POST["Amount"]) > 0){
		$condition_sql .= " and Amount = '".($_POST["Amount"])."'";
	}
	
	//"date" filter
	if($_POST["ReceiptDate_year"] > 0 && $_POST["ReceiptDate_month"] > 0 && $_POST["ReceiptDate_day"] > 0){
		$this_date_time = $_POST["ReceiptDate_year"]."-".$_POST["ReceiptDate_month"]."-".$_POST["ReceiptDate_day"];
		$condition_sql .= " and ReceiptDate = '$this_date_time 00:00:00'";
   }else{
		$this_date_time = "0000-00-00";
   }	
	
	// Pagination Stuffs
	if($sess_accesslevel == '3'){
		$the_sql = "SELECT count(*)
						FROM receipt
						
						WHERE
							RID
						IN 
						(
							SELECT distinct(RID)
							FROM 
							payment a, lawfulness b, company c
							where
							a.LID = b.LID
							and
							b.CID = c.CID
							and
							c.Province = '$sess_meta'
						)
						$condition_sql 
						
						";
		
		
	}else{
		$the_sql = "SELECT count(*)
						FROM receipt
						
						WHERE
							RID
						IN 
						(
							SELECT distinct(RID)
							FROM 
							payment
							
						)
						$condition_sql 
						
						";
		
	}
	
	//echo $the_sql;
	
	$record_count_all = getFirstItem($the_sql);
	
	$per_page = 20;
	$num_page = ceil($record_count_all/$per_page);
	
	$cur_page = 1;
	if(is_numeric($_POST["start_page"]) && $_POST["start_page"] <= $num_page && $_POST["start_page"] > 0){
		$cur_page = $_POST["start_page"];
	}
		
	$starting_index = 0;
	if($cur_page > 1){
		$starting_index = ($cur_page-1) * $per_page;						
	}
	
	$the_limit = "limit $starting_index, $per_page";
							
						
	if($sess_accesslevel == '3'){
		$get_org_sql = "SELECT *
						FROM receipt
						
						WHERE
							RID
						IN 
						(
							SELECT distinct(RID)
							FROM 
							payment a, lawfulness b, company c
							where
							a.LID = b.LID
							and
							b.CID = c.CID
							and
							c.Province = '$sess_meta'
						)
						$condition_sql 
						order by RID desc
						$the_limit
						";
		
		
	}else{
		$get_org_sql = "SELECT *
						FROM receipt
						
						WHERE
							RID
						IN 
						(
							SELECT distinct(RID)
							FROM 
							payment
							
						)
						$condition_sql 
						order by RID asc
						$the_limit
						";
		
	}
	
	
	//echo $get_org_sql;
	$org_result = mysql_query($get_org_sql);
	
	//total records 
	//$total_records = getFirstItem("");
	$total_records = 0;




?>


<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0;"  >
                    ใบเสร็จรับเงินทั้งหมด
                    
                    
                  </h2>
                    <br />
                    
                     <form method="post">
                    <table style=" padding:10px 0 0px 0;">
                    
                        <tr>
                          <td bgcolor="#efefef">ใบเสร็จเล่มที่:</td>
                          <td><input type="text" name="BookReceiptNo" style="width:100px" value="<?php echo $_POST["BookReceiptNo"];?>" /></td>
                          <td bgcolor="#efefef">ใบเสร็จเลขที่:</td>
                          <td><input type="text" name="ReceiptNo" value="<?php echo $_POST["ReceiptNo"];?>" /></td>
                          <td bgcolor="#efefef">&nbsp;</td>
                        </tr>
                        <tr>
                          <td bgcolor="#efefef">สำหรับปี:</td>
                          <td><?php include "ddl_year_with_blank.php";?></td>
                              <td bgcolor="#efefef">วิธีชำระเงิน:</td>
                              <td bgcolor=""><?php $have_blank = 1 ;include "ddl_pay_type.php";?></td>
                              <td bgcolor="#efefef">&nbsp;</td>
                        </tr>
                        <tr>
                          <td bgcolor="#efefef">จำนวนเงิน:</td>
                          <td><input type="text" name="Amount" value="<?php echo $_POST["Amount"];?>" /></td>
                          <td bgcolor="#efefef">วันที่ชำระเงิน:</td>
                          <td><?php
											   
							   $selector_name = "ReceiptDate";
							   
							   
							  // echo $this_date_time;
							 
							   if($this_date_time != "0000-00-00"){
								   $this_selected_year = date("Y", strtotime($this_date_time));
								   $this_selected_month = date("m", strtotime($this_date_time));
								   $this_selected_day = date("d", strtotime($this_date_time));
							   }else{
								   $this_selected_year = 0;
								   $this_selected_month = 0;
								   $this_selected_day = 0;
							   }
							   
							   include ("date_selector.php");
							   
							   ?></td>
                          <td bgcolor="#efefef"><input type="submit" value="แสดง" name="mini_search"/></td>
                        </tr>
                          <tr>
                          	<td colspan="5">
                            	<div align="left">
                                <select name="start_page" onchange="this.form.submit()">
                                    <?php 
                                        for($i = 1; $i <= $num_page; $i++){
                                    ?>
                                    <option value="<?php echo $i;?>" <?php if($_POST["start_page"]==$i){echo "selected='selected'";}?>>หน้าที่ <?php echo $i;?></option>
                                    <?php
                                        }
                                    ?> 
                                </select>
                                </div>                            </td>
                          </tr>
                    </table>
                    </form>
                    
                    <table border="1"  cellspacing="0" cellpadding="5" style="border-collapse:collapse; ">
                    	<tr bgcolor="#9C9A9C" align="center" >
                        	
           	  <td >
                            	<div align="center"><span class="column_header">ลำดับที่</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">ใบเสร็จเล่มที่</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">ใบเสร็จเลขที่</span> </div></td>
                            
                      <td>
                            	<div align="center"><span class="column_header">สำหรับปี</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">วิธีชำระเงิน</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">จำนวนเงิน</span> </div></td>
                      <td>
                            	<div align="center"><span class="column_header">วันที่ชำระเงิน</span> </div></td>
                             
                          
                           
                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 && 1==0){ //exec wont see these?>
                          
                          	<?php }?>
                            
                            <?php 
								if(1 ==0){ // yoes 20151001 -- aloow admin to delete this
								
								
								?>
                          		<td><div align="center"><span class="column_header">ลบข้อมูล</span></div></td>  
                            <?php }?>
                          
                    	</tr>
                       <?php
						   
						$total_records = $starting_index;
						while ($post_row = mysql_fetch_array($org_result)) {
					
							$total_records++;
							
						?>     
                        <tr bgcolor="#ffffff" align="center" >
                        	
                       	  <td >
                            <div align="center"><a href="view_payment.php?id=<?php echo doCleanOutput($post_row["RID"]);?>"><?php echo $total_records;?></a> </div></td>
                            
                           <td>
                            	<?php echo ($post_row["BookReceiptNo"]);?>                            </td>
                            <td>
                            	<a href="view_payment.php?id=<?php echo doCleanOutput($post_row["RID"]);?>"><?php echo ($post_row["ReceiptNo"]);?></a>                            </td>
                            
                            <td>
                            	<?php echo formatYear($post_row["ReceiptYear"]);?>                            </td>
                            <td>
                            	<?php echo formatPaymentName($post_row["PaymentMethod"]);?>                            </td>
                            <td>
                            	<div align="right"><?php echo formatNumber($post_row["Amount"]);?></div>                   </td>
                           
                         	<td>
                            	<?php echo formatDateThai(getFirstItem("select PaymentDate from payment where RID ='".$post_row["RID"]."'"));?>                             </td>
                            
                            
                            <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8 && 1==0){ //exec wont see these?>
                            
                              <?php }?>
                              
                              
                               <?php 
							   	//if($sess_accesslevel ==1){ // yoes 20151001 -- aloow admin to delete this
								//**toggles_paymen
								if(!$post_row["NEPFundPaymentID"] && 1==0){ // yoes 20151002 -- bring this back
								//if(!$post_row["NEPFundPaymentID"] && ($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3)){ // yoes 20160111 -- bring this back
								
								?>
                               
                               	<td>
                              <div align="center"><a href="scrp_delete_receipt.php?id=<?php echo doCleanOutput($post_row["RID"]);?>" title="ลบข้อมูล" onclick="return confirm('คุณแน่ใจหรือว่าจะลบข้อมูล? การลบข้อมูลถือเป็นการสิ้นสุดและคุณจะไม่สามารถเรียกข้อมูลกลับมาได้');"><img src="decors/cross_icon.gif" border="0" /></a> </div></td>
                               
                               <?php 
							   //}else{
							   }elseif($post_row["NEPFundPaymentID"]){
								   
								?>
                               
                               	<td>                              		
                                    เป็นข้อมูลจากระบบกองทุนฯ                                
                                </td>
                               
                               <?php }?>
                              
                        </tr>
                        <?php } //end loop to generate rows?>
				  </table>
                                       
                    <?php 
					
					//20140127
					//see if have del_id, if so - update lawfulness for all orgs in that RID
					if(is_numeric($_GET["del_count"]) && $_GET["del_count"] > 0){
					
						//if have deletion, also disable the page...
						?>
                        
                        
                        	<div id="overlay"> 
                               <div id="img-load" style="color:#FFFFFF; text-align:center">
                                <img src="./decors/bigrotation2.gif"  />
                                
                                
                                <br />
                                <strong>กำลังปรับปรุงข้อมูล...</strong>
                                
                                
                                
                                </div>
                            </div>
                            
                            <script>
                            $t = $("#main_body");
                            
                            $("#overlay").css({
                              opacity: 0.5,
                              top: 0,
                              left: 0,
                              width: $t.outerWidth(),
                              height: $t.outerHeight()
                            });
                            
                            $("#img-load").css({
                              top:  (380),
                              left: ($t.width() / 2 -110)
                            });
                            
                            //$t.mouseover(function(){
                               $("#overlay").fadeIn();
                            //}
                            //);
                            </script>
                        
                        
                        <?php	
						
						$del_count = $_GET["del_count"]*1;
						$the_year = $_GET["year"]*1;
						
						for($i=1;$i<=$del_count;$i++){
						
						?>
                        
                        <iframe width="1" height="1" src="./organization.php?id=<?php echo $_GET["org".$i];?>&focus=lawful&year=<?php echo $the_year;?>&auto_post=1"></iframe>
                        
                        <?php
						
						}
						
						//dummy iframe to catch on-load events
						?>
                        
                        <iframe width="1" height="1" src="./organization.php?id=<?php echo $_GET["org".$i];?>&focus=lawful&year=<?php echo $the_year;?>" onload='$("#overlay").hide();'></iframe>
                        
                        <?php
						
					
					}			
					
					
					
					?>
                    
                    
                    
                  
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

<script language="javascript">

function checkOrUncheck(){
	if(document.getElementById('chk_all').checked == true){
		checkAll();
	}else{
		uncheckAll();
	}
}

function checkAll(){
	<?php echo $js_do_check; ?>
}

function uncheckAll(){
	<?php echo $js_do_uncheck; ?>
}
</script>
</body>
</html>