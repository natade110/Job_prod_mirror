<?php include "db_connect.php";?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="th" xml:lang="th">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login ระบบรายงานผลการจ้างงานคนพิการ</title>
</head>

<?php 

	session_start();
	
	if($_POST[user_name]){
		
		$user_name = doCleanInput($_POST["user_name"]);
		$password = doCleanInput($_POST["password"]);
		
		$query="SELECT * 
				FROM users
				WHERE user_name = '$user_name' 
				and user_password = '$password'
				and (AccessLevel = 1 or AccessLevel = 2)
				";
		
		$post_row = getFirstRow($query);
		
		if($post_row){
			
			//have login
			$_SESSION['sess_userid'] = $post_row["user_id"];
			$_SESSION['sess_accesslevel'] = $post_row["AccessLevel"];
			$_SESSION['sess_meta'] = $post_row["user_meta"];
			
		}
		
	}else{
		
	}
	
	
	
	if($sess_userid){		
		$logged_in = 1;	
	}else{
		$logged_in = 0;		
	}

?>

<body>

	
    
    <?php if(!$logged_in){?>
    
    	<h1>Login ระบบรายงานผลการจ้างงานคนพิการ</h1>
    
        <form action="" method="post">
            Login
            <br />
            <label for="user_name">ชื่อผู้ใช้งาน:</label>  <input id="user_name" name="user_name" type="text" required="required" />
            <br />
            <label for="password">รหัสผ่าน:</label> <input id="password" name="password" type="password" required="required" />        
            <?php if($_GET["mode"] == "error_pass"){echo "<br />รหัสผ่านไม่ถูกต้อง!";}?>
            <input name="" type="submit" value="login" />
        </form>   
        
	<?php }else{?>    
    
    
    
    <h1>รายงานที่ 16: สรุปการปฎิบัติตามกฎหมายเรื่องการจ้างงานคนพิการในสถานประกอบการ</h1> 
    
    
        <form name="report_262_form" id="report_262_form" action="report_262.php" method="post" target="_blank" >       
              <table width="100%" border="0">
                              
                                
                              <tr>
                                <td><label for="ddl_year">ปี</label> </td>
                                <td><?php include "ddl_year.php";?></td>
                               
                                <td>
                                <label for="Province">จังหวัด</label></td>
                                <td><?php include "ddl_org_province_report.php";?></td>
                              </tr>
                       
                              
                              
                              <tr>
                                <td > <label for="chk_from">ข้อมูลระหว่างวันที่</label> <input id="chk_from" name="chk_from" type="checkbox" value="1" /> </td>
                                <td colspan="5">
                                <label for="date_from_day">จากวันที่</label>
                                <label for="date_from_month">จากเดือนที่</label>
                                <label for="date_from_year">จากปีที่</label>
                                <?php 
                                    $selector_name = "date_from";
                                    $this_date_time = date("Y-m-d");
                                    include "date_selector.php";?> 
                                
                                
                                
                                <label for="date_to_day">ถึงวันที่</label>
                                <label for="date_to_month">ถึงเดือนที่</label>
                                <label for="date_to_year">ถึงปีที่</label>
                                
                                <?php 
                                    $selector_name = "date_to";
                                    include "date_selector.php";?></td>
                              </tr>
                              <tr>
                                <td >&nbsp;</td>
                                <td colspan="5"><input name="input5" type="submit" value="เรียกดูรายงาน" /></td>
                              </tr>
                              
                              
                              
                            </table>         
        </form>
    
    	<a href="scrp_do_logout.php?a=a">ออกจากระบบ</a>
    	
    <?php }?>

</body>
</html>