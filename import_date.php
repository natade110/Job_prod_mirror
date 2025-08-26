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

//manager submit date
if($_POST["submit_date"]){
	
	
	$sql = "replace into vars values('submit_date_from_day','".($_POST["submit_date_from_day"]*1)."')";
	mysql_query($sql) or die (mysql_error());
	$sql = "replace into vars values('submit_date_from_month','".($_POST["submit_date_from_month"]*1)."')";
	mysql_query($sql) or die (mysql_error());
	$sql = "replace into vars values('submit_date_from_year','".($_POST["submit_date_from_year"]*1)."')";
	mysql_query($sql) or die (mysql_error());
	
	$sql = "replace into vars values('submit_date_to_day','".($_POST["submit_date_to_day"]*1)."')";
	mysql_query($sql) or die (mysql_error());
	$sql = "replace into vars values('submit_date_to_month','".($_POST["submit_date_to_month"]*1)."')";
	mysql_query($sql) or die (mysql_error());
	$sql = "replace into vars values('submit_date_to_year','".($_POST["submit_date_to_year"]*1)."')";
	mysql_query($sql) or die (mysql_error());
	
}















	
?>	



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >
                    จัดการช่วงเวลาส่งเอกสารออนไลน์ของสถานประกอบการ 
                    
                    
                  </h2>
                   
                    
                    
                    
                    
                    <form method="post" >
                    <hr />
                
                    <strong>จัดการช่วงเวลาส่งเอกสารออนไลน์ของสถานประกอบการ</strong>
                    
                    <br />
                    
                    <?php 
						$selector_name = "submit_date_from";
						
						
						$this_date_time = "".
									getFirstItem("select var_value from vars where var_name = 'submit_date_from_year'")
									."-".
									getFirstItem("select var_value from vars where var_name = 'submit_date_from_month'")
									."-".
									getFirstItem("select var_value from vars where var_name = 'submit_date_from_day'")
									."";
						
						include "date_selector.php"; ?> 
                      ถึง  <?php 
					  
					  	$selector_name = "submit_date_to";
						$this_date_time = "".
									getFirstItem("select var_value from vars where var_name = 'submit_date_to_year'")
									."-".
									getFirstItem("select var_value from vars where var_name = 'submit_date_to_month'")
									."-".
									getFirstItem("select var_value from vars where var_name = 'submit_date_to_day'")
									."";
						
					  	include "date_selector.php"; 
						?>
                    
                    <input name="submit_date" type="hidden" value="1" />
                    <input name="" type="submit" value="แก้ไขข้อมูล" />
                    </form>
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