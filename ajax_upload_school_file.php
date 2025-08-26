<?php

	include "db_connect.php";
	include "session_handler.php";
	
	error_reporting(1);
	ini_set('max_execution_time', 600);
	ini_set("memory_limit","256M");
	
	$step = $_GET[step];
	
	$have_current_batch = getFirstItem("select var_value from vars where var_name = 'upload_school_file' and var_value > now()");
	//echo $have_current_batch;
	if($have_current_batch){
		
		
		if($step == 1){

			echo "<font color='#0099CC'>ระบบกำลังทำการ upload ไฟล์ กรุณารอซักครู่...</font>";
			echo "<img src='decors/bigrotation2.gif' width='20' />";
			exit();
		
		}
		
		if($step == "del"){
			//nothing
		}
		
		if($step == 2){

			echo "<font color='#0099CC'>ระบบกำลังทำการ upload ไฟล์ กรุณารอซักครู่...</font>";
			echo "<img src='decors/bigrotation2.gif' width='20' />";
			?>
            
             <script>
			  setTimeout(function(){
				   window.location.reload(1);
				}, 10000);
			  </script>
            
            <?php
			exit();
		
		}
		
		if($step == 3){
				
			echo "<font color='#0099CC'>ระบบกำลังทำการ upload ไฟล์ กรุณารอซักครู่...</font>";
			echo "<img src='decors/bigrotation2.gif' width='20' />";
			exit();
				
		}
		
	}else{
		
			if($step == 1){
			?>
            
            <input  name="upload_file" type="submit" value="อัพโหลดไฟล์โรงเรียนเอกชน"/>  
            
            <?php	
			}
		
		
			if($step == "del"){
			?>
			
			
              &nbsp;&nbsp;&nbsp;
                <a href="scrp_import_school_delete_file.php" onclick="return confirm('ต้องการลบไฟล์นี้ทิ้ง?');">
                    <img src="decors/cross_icon.gif" width="15" />
                </a>
            
			<?php
			}
		
			if($step == 2){
		?>
        
        	<form method="post" action="upload_school.php" >
                 <input  name="upload_file" type="submit" value="ตรวจสอบไฟล์"/>  
              </form>
             
        
        <?php
		
			}
	
			
			if($step == 3){
				?>
                
                <form method="post" action="upload_import_school.php">
                 <input  name="upload_file" type="submit" value="นำข้อมูลเข้าระบบ" onclick="return confirm('หลังจากนำข้อมูลเข้าระบบแล้ว จะไม่สามารถแก้ไขข้อมูลที่นำเข้าไปแล้วได้ - ต้องการทำต่อไป?');"/> 
                </form>  
                
                <?php
				
			}
	
		exit();	
	}
	
	
?>