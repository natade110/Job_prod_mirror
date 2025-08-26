

	
	



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ระบบรายงานผลการจ้างงานคนพิการ</title>
<LINK REL='StyleSheet' type='text/css' href='styles.css'>
<link rel="stylesheet" href="emx_nav_left.css" type="text/css">

<script class="jsbin" src="jquery-1.11.1.min.js"></script>
<script src="./jquery_validate/jquery.validate.js"></script>
<script type='text/javascript' src="jquery_ui/jquery-ui.js"></script>

<script type="text/javascript" src="./kendo/kendo.all.min.js"></script>
<script type="text/javascript" src="./kendo/kendo.culture.th-TH.min.js"></script>
<script type="text/javascript" src="./kendo/kendo.calendar.custom.js"></script>
<script type="text/javascript" src="./scripts/site.js"></script>
<script type="text/javascript">
	kendo.culture("th-TH");
</script>
<link rel='stylesheet' id='all-css'  href='jquery_ui/jquery-ui.css' type='text/css' media='all' />

<link rel="stylesheet" type="text/css" href="./jquery.datetimepicker.css"/ >
<script src="./build/jquery.datetimepicker.full.min.js"></script>
<link rel='stylesheet' href='css/kendo.custom.css' type='text/css' media='all' />
<link rel='stylesheet' href='css/font-awesome.min.css' type='text/css' media='all' />
<link rel='stylesheet' href='css/site.css' type='text/css' media='all' />
<!--<script class="jsbin" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>-->
</head>
<style>
  
  #overlay { 
    display:none; 
    position:fixed; 
    background:#333333; 
  }
  #img-load { 
    position:fixed; 
  }
  
</style>
<body style="background-color:#73828C" id="main_body">

<div id="loader" style="display:none">
   <img id="ImageLoader" src="./decors/loading.gif" alt="" /> 
</div>
<table width="1024" align="center" border="0">
<tr>
<td>

<div id="masthead" >
	<table border="0" width="100%">
    	<tr>
        	<td>
            <div style="padding-bottom:10px;" class="logo_text" align="left">ระบบรายงานผลการจ้างงานคนพิการ (สำหรับเจ้าหน้าที่ฯ)</div>
            </td>
            <td valign="bottom">
                        <div  align="right" style="color:#FFFFFF">เข้าระบบโดยชื่อ user: ทดสอบ ข้อมูลยูสเซอร์</div>
                        </td>
         </tr>
    </table>
  
     <div id="globalNav" > 
    <img alt="" src="gblnav_left.gif" height="32" width="4" id="gnl"> <img alt="" src="glbnav_right.gif" height="32" width="4" id="gnr"> 
    <div id="globalLink" > 
      
      
      
       <a href="index.php"  class="glink" style="color:#000000;"> 
	  หน้าแรก</a>
      
      
       <a href="gjob.dep.go.th"  class="glink" style="color:#000000;"> 
      ระบบรายงานผลหน่วยงานภาครัฐ
      </a>
      
		     
      
            	  <a href="org_list.php?mode=search" class="glink" style="color:#000000;">ค้นหาสถานประกอบการ</a>

			                    
                  <!--<a href="organization.php?mode=new"  class="glink" style="color:#000000;"> 
                  เพิ่มข้อมูล</a>-->
                  
                  <a href="org_list.php?mode=letters" class="glink" style="color:#000000;">ส่งจดหมาย</a>
                  <!--<a href="org_list.php?mode=payment" class="glink" style="color:#000000;">ส่งเงิน</a>-->
                            
              	              <a href="org_list.php?mode=announce" class="glink" style="color:#000000;">ประกาศผ่านสื่อ</a>
                            
              
                   	 <a href="view_reports.php" class="glink" style="color:#000000;">รายงาน</a>
                    
                 <a href="http://ejob.dep.go.th/?page_id=4793" target="_blank" class="glink" style="color:#000000;">กฎหมายที่เกี่ยวข้อง</a>
          
         <a href="http://ejob.dep.go.th/?page_id=4811" target="_blank" class="glink" style="color:#000000;">แบบรายงาน</a>
         
          
     
                <a href="faq.php" class="glink" style="color:#000000;">ถาม-ตอบ</a>
                
          
          
                            <a href="user_list.php" class="glink" style="color:#000000;">ผู้ใช้งานระบบ</a>
                
            
      <a href="view_user.php?id=1" class="glink" style="color:#000000;">เปลี่ยนรหัสผ่าน</a>
      
      
            <a href="ebook-Hire System-2557.zip" class="glink" style="color:#000000;">สื่อการสอน</a>
            
      <a href="scrp_do_logout.php" class="glink" style="color:#000000;">ออกจากระบบ</a>
      <!-- end globalNav --> 
    </div>
    <!-- end mast head --> 
</td>
</tr>
</table>

<table align="center" cellpadding="0" cellspacing="0"  style="padding-top: 10px;">
<tr>
<td bgcolor="#FFFFFF">
<img alt="" src="tl_curve_white.gif" height="6" width="6" id="tl" style=""><img alt="" src="tr_curve_white.gif" height="6" width="6" id="tr" style=" float:right"> 

</td>
</tr>
<tr>
<td>
<div id="pagecell1" > 
  <!--pagecell1--> 
 
  
  

<table width="1024" align="center" border="0">
	<tr>
    	<td>
        
         
      <table bgcolor="#FFFFFF" width="100%"  style="padding: 0 5px 5px 5px;
            background: url(nep_logo.jpg) no-repeat 50% 50%;
            
      
" border="0">
        	<tr>
            	<td colspan="2">
                <h1 class="default_h1" style="margin:0; padding:0; "  >
                	                </h1>
                <hr  style="margin-bottom:0; "/>
                </td>
			</tr>
            <tr>
            	<td valign="top" width="225" style="border-right: solid 1px #efefef;">
                	
               		﻿<div id="pageNav"> 
   
    
    

	    
    
    
     <div id="sectionLinks"> 
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">สถานประกอบการ</div>
    <a href="org_list.php" style=" margin-left:10px; font-weight: normal; " >
                                        รายชื่อสถานประกอบการ                                        </a>
    <a href="org_list.php?mode=search" style="margin-left:10px;font-weight: normal; ">ค้นหารายชื่อสถานประกอบการ</a>
    
    <a href="organization.php?mode=new" style="margin-left:10px;font-weight: normal; ">เพิ่มข้อมูลสถานประกอบการ</a>

    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">การส่งจดหมายแจ้ง</div>
    <a href="org_list.php?mode=letters" style="margin-left:10px;font-weight: normal; ">การส่งจดหมายแจ้งสถานประกอบการ</a>

    <a href="import_letter.php" style="margin-left:10px;font-weight: normal; ">นำเข้าข้อมูลจดหมายแจ้งสถานประกอบการ</a>

    <a href="letter_list.php" style="margin-left:10px;font-weight: normal;">จดหมายแจ้งสถานประกอบการทั้งหมด</a>
    
        <a href="payment_list.php" style="margin-left:10px;font-weight: normal;">ใบเสร็จรับเงินทั้งหมด</a>
        
            <a href="org_list.php?mode=announce" style="margin-left:10px;font-weight: normal;">เพิ่มการประกาศผ่านสื่อ</a>
        <a href="announce_list.php" style="margin-left:10px;font-weight: normal;">การประกาศผ่านสื่อทั้งหมด</a>
       
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">การดำเนินการตามกฎหมาย</div>
    
        <a href="collection_create.php" style="margin-left:10px;font-weight: normal; ">การส่งจดหมายทวงถาม</a>
    <a href="collection_list.php" style="margin-left:10px;font-weight: normal;">จดหมายทวงถามทั้งหมด</a>
    <a href="schedulecollectionemail_list.php" style="margin-left:10px;font-weight: normal;">ประวัติการส่งอีเมลล์ทวงถาม</a>    
     
    
         
    <a href="notice_create.php" style="margin-left:10px;font-weight: normal; ">การแจ้งโนติส</a>
    <a href="holding_create.php" style="margin-left:10px;font-weight: normal; ">การแจ้งอายัด</a>
    <a href="proceedings_create.php" style="margin-left:10px;font-weight: normal;">การส่งพนักงานอัยการ/ยื่นขอรับเงินกรณีล้มละลาย</a>
    <a href="litigation_list.php" style="margin-left:10px;font-weight: normal;">การดำเนินคดีตามกฎหมายทั้งหมด</a>
	       
        
    	       	  <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">รายงาน</div>
                <a href="view_reports.php" style="margin-left:10px;font-weight: normal;">รายงานทั้งหมด</a>
        
        
        
    
    
         <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">สถานประกอบการ</div>
    
    	
    	<a href="org_list_approve.php" style="margin-left:10px;font-weight: normal;">สถานประกอบการยื่นแบบออนไลน์</a>
        
        
    
    	<!--<a href="register_list.php" style="margin-left:10px;font-weight: normal;">ผู้ใช้งานสถานประกอบการ</a>-->
        
        
        			<a href="org_list.php?mode=email" style="margin-left:10px;font-weight: normal;">ระบบส่งจดหมายแจ้งสถานประกอบการออนไลน์ (Email) </a>
            
            
            <a href="manage_email.php" style="margin-left:10px;font-weight: normal;">ระบบแจ้งเตือนสถานะสถานประกอบการ (Email Scheduler) </a>
            
		        
        
    
         
        <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">การจัดการข้อมูล</div>
    	<!--<a href="import_org.php" style="margin-left:10px;font-weight: normal;">ข้อมูลสถานประกอบการ/ค่าจ้างขั้นต่ำ</a>-->
        
        <a href="ktb_sync.php" style="margin-left:10px;font-weight: normal;">สรุปยอดการชำระเงินรายวัน KTB Online</a>
        
        <a href="merge_company.php" style="margin-left:10px;font-weight: normal;">รวมข้อมูลสถานประกอบการ (โรงเรียนเอกชน)</a>
        
        <a href="import_org.php" style="margin-left:10px;font-weight: normal;">การนำเข้าข้อมูลสถานประกอบการ (ประกันสังคม และ กรมการจัดหางาน)</a>
        <a href="import_org_new.php" style="margin-left:10px;font-weight: normal;">การนำเข้าข้อมูลสถานประกอบการ (ประกันสังคม)</a>
        
        <a href="import_org_school.php" style="margin-left:10px;font-weight: normal;">การนำเข้าข้อมูลสถานประกอบการ (โรงเรียนเอกชน)</a>
        
        <a href="import_org_no_head.php" style="margin-left:10px;font-weight: normal;">สถานประกอบการ ที่มีสาขาย่อย แต่ไม่มีสำนักงานใหญ่</a>
        
        <a href="import_skip_org.php" style="margin-left:10px;font-weight: normal;">การนำเข้าข้อมูลหน่วยงานภาครัฐ</a>
        <a href="import_wage.php" style="margin-left:10px;font-weight: normal;">จัดการค่าจ้างขั้นต่ำ</a>
        <a href="import_date.php" style="margin-left:10px;font-weight: normal;">จัดการช่วงเวลาส่งเอกสารออนไลน์ของสถานประกอบการ</a>
        <a href="config_sending_letter.php" style="margin-left:10px;font-weight: normal; ">จัดการช่วงเวลาส่งจดหมายเตือนสถานประกอบการ</a>
        
        <a href="manage_zone_list.php" style="margin-left:10px;font-weight: normal;">จัดการพื้นที่การทำงาน</a>
        <a href="import_list.php?mode=ktb" style="margin-left:10px;font-weight: normal;">รายการนำเข้าข้อมูลจากธนาคาร</a>
        <a href="import_list.php?mode=nepfund" style="margin-left:10px;font-weight: normal;">รายการนำเข้าข้อมูลจากระบบใบเสร็จ</a>
        <a href="export_list.php" style="margin-left:10px;font-weight: normal;">รายการส่งออกข้อมูลไประบบใบเสร็จ</a>


        <a href="manage_default_emails.php" style="margin-left:10px;font-weight: normal;">ระบบจัดการ email</a>
        
    
        
    
     <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">สถานประกอบการ</div>
    	<a href="payment_calculate_hire.php" style="margin-left:10px;font-weight: normal;">คำนวณเงิน</a>
    
    
    </div> 
</div>

 
                                        
                </td>                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >ระบบแจ้งเตือนสถานะสถานประกอบการ (Email Scheduler) </h2>
                   
                    
                   

                    <strong>ระบบแจ้งเตือนสถานะสถานประกอบการ (Email Scheduler) </strong>
                    
                  
                    
                  	
                    
                    <form method="post">
                   <table style=" padding:10px 0 0px 0;">
                    
                    <tr>
                    	  <td bgcolor="#efefef">ประจำปี:</td>
                    	  <td><select name="ddl_year" id="ddl_year" >
	    <option value="2019" selected='selected'>2562</option>
        <option value="2018" >2561</option>
        <option value="2017" >2560</option>
        <option value="2016" >2559</option>
        <option value="2015" >2558</option>
        <option value="2014" >2557</option>
        <option value="2013" >2556</option>
        <option value="2012" >2555</option>
        <option value="2011" >2554</option>
      </select></td>
                    	  <td >&nbsp;</td>
                    	  <td>&nbsp;</td>
                  	  </tr>
                    
                    
                     <tr>
                        <td bgcolor="#efefef">จังหวัด: </td>
                        <td colspan="3">

<select name="Province" id="Province">
	
	<option value="">-- select --</option>              
        <option  value="63">กระบี่</option>
    
                  
        <option  value="1">กรุงเทพมหานคร</option>
    
                  
        <option  value="58">กาญจนบุรี</option>
    
                  
        <option  value="11">กาฬสินธุ์</option>
    
                  
        <option  value="30">กำแพงเพชร</option>
    
                  
        <option  value="12">ขอนแก่น</option>
    
                  
        <option  value="51">จันทบุรี</option>
    
                  
        <option  value="52">ฉะเชิงเทรา</option>
    
                  
        <option  value="53">ชลบุรี</option>
    
                  
        <option  value="31">ชัยนาท</option>
    
                  
        <option  value="13">ชัยภูมิ</option>
    
                  
        <option  value="64">ชุมพร</option>
    
                  
        <option  value="65">ตรัง</option>
    
                  
        <option  value="54">ตราด</option>
    
                  
        <option  value="59">ตาก</option>
    
                  
        <option  value="32">นครนายก</option>
    
                  
        <option  value="33">นครปฐม</option>
    
                  
        <option  value="14">นครพนม</option>
    
                  
        <option  value="15">นครราชสีมา</option>
    
                  
        <option  value="66">นครศรีธรรมราช</option>
    
                  
        <option  value="34">นครสวรรค์</option>
    
                  
        <option  value="35">นนทบุรี</option>
    
                  
        <option  value="67">นราธิวาส</option>
    
                  
        <option  value="4">น่าน</option>
    
                  
        <option  value="77">บึงกาฬ</option>
    
                  
        <option  value="16">บุรีรัมย์</option>
    
                  
        <option  value="36">ปทุมธานี</option>
    
                  
        <option  value="60">ประจวบคีรีขันธ์</option>
    
                  
        <option  value="55">ปราจีนบุรี</option>
    
                  
        <option  value="68">ปัตตานี</option>
    
                  
        <option  value="37">พระนครศรีอยุธยา</option>
    
                  
        <option  value="5">พะเยา</option>
    
                  
        <option  value="69">พังงา</option>
    
                  
        <option  value="70">พัทลุง</option>
    
                  
        <option  value="38">พิจิตร</option>
    
                  
        <option  value="39">พิษณุโลก</option>
    
                  
        <option  value="71">ภูเก็ต</option>
    
                  
        <option  value="17">มหาสารคาม</option>
    
                  
        <option  value="18">มุกดาหาร</option>
    
                  
        <option  value="76">ยะลา</option>
    
                  
        <option  value="19">ยโสธร</option>
    
                  
        <option  value="72">ระนอง</option>
    
                  
        <option  value="56">ระยอง</option>
    
                  
        <option  value="62">ราชบุรี</option>
    
                  
        <option  value="20">ร้อยเอ็ด</option>
    
                  
        <option  value="41">ลพบุรี</option>
    
                  
        <option  value="8">ลำปาง</option>
    
                  
        <option  value="9">ลำพูน</option>
    
                  
        <option  value="24">ศรีสะเกษ</option>
    
                  
        <option  value="22">สกลนคร</option>
    
                  
        <option  value="74">สงขลา</option>
    
                  
        <option  value="73">สตูล</option>
    
                  
        <option  value="42">สมุทรปราการ</option>
    
                  
        <option  value="43">สมุทรสงคราม</option>
    
                  
        <option  value="44">สมุทรสาคร</option>
    
                  
        <option  value="48">สระบุรี</option>
    
                  
        <option  value="57">สระแก้ว</option>
    
                  
        <option  value="45">สิงห์บุรี</option>
    
                  
        <option  value="47">สุพรรณบุรี</option>
    
                  
        <option  value="75">สุราษฎร์ธานี</option>
    
                  
        <option  value="23">สุรินทร์</option>
    
                  
        <option  value="46">สุโขทัย</option>
    
                  
        <option  value="25">หนองคาย</option>
    
                  
        <option  value="26">หนองบัวลำภู</option>
    
                  
        <option  value="29">อำนาจเจริญ</option>
    
                  
        <option  value="27">อุดรธานี</option>
    
                  
        <option  value="10">อุตรดิตถ์</option>
    
                  
        <option  value="50">อุทัยธานี</option>
    
                  
        <option  value="28">อุบลราชธานี</option>
    
                  
        <option  value="49">อ่างทอง</option>
    
                  
        <option  value="2">เชียงราย</option>
    
                  
        <option  value="3">เชียงใหม่</option>
    
                  
        <option  value="61">เพชรบุรี</option>
    
                  
        <option  value="40">เพชรบูรณ์</option>
    
                  
        <option  value="21">เลย</option>
    
                  
        <option  value="6">แพร่</option>
    
                  
        <option  value="7">แม่ฮ่องสอน</option>
    
                  
        <option  value="99">ไม่ระบุ</option>
    
    </select>
</td>
                       
                      </tr>
                    
                    	<tr>
                    	  <td bgcolor="#efefef">ประเภทของ email แจ้งเตือน: </td>
                          <td colspan="3">
                          
                          <select name="alert_type" id="alert_type" >
                            <option value="" selected="selected">-- เลือก --</option>
                            <option value="2" >ปฏิบัติตามกฏหมายแล้ว</option>
                            <option value="0" >ไม่ทำตามกฏหมาย</option>
                            <option value="3" >ปฏิบัติตามกฏหมายแต่ไม่ครบอัตราส่วน</option>
                            <option value="1" >พบข้อมูลการใช้สิทธิซ้ำซ้อน</option>
							<option value="6" >การแจ้งแนบไฟล์ สปส 1-10 ส่วนที่ 2</option>

                            
                            
                        </select>
							</td>
                            
                        
                   	  </tr>
                    	<tr>
                    	  <td bgcolor="#efefef">สถานะการส่ง email </td>
                    	  <td colspan="3">  
                          <select name="email_status" id="email_status" >
                            <option value="" selected="selected">แสดงรายชื่อทั้งหมด</option>
                            <option value="1" >แสดงรายชื่อที่ยังไม่เคยได้รับ email เท่านั้น</option>
                            <option value="2" >แสดงรายชื่อที่เคยได้รับ email แล้ว</option>
                           
                            
                        </select></td>
                  	  </tr>
                      
                      
                      
                      
                        
                        
                    	<tr>
                    	  <td colspan="6" align="right">
                          
                           
                            <input type="submit" value="แสดง" name="mini_search"/>
                            
                            
                            |
                            
                            email รออยู่ใน queue การส่ง <a href="mail_queue.php">0</a> ฉบับ
                            
                            
                          <hr />
                          
                          </td>
                   	  </tr>
                      
                      
                    </table>
                    </form>
                                        
                                        
                                                            
                      
                    
                                  
                    <table cellpadding="3" width="800" bgcolor="#FFFFFF" border="1" style="border-collapse: collapse;">
                    
                   		 <tr>
                        
                        
                        	<td style="background-color:#efefef">
                            	เลขที่บัญชีนายจ้าง                            </td>
                        	<td style="background-color:#efefef">
                            	ชื่อ นายจ้างหรือ สถานประกอบการ                      </td>
                            <td style="background-color:#efefef">
                            	จำนวนลูกจ้างรวมทุกสาขา                      </td>
                            
                            <td style="background-color:#efefef">
                            สถานะ
                            </td>
                             <td style="background-color:#efefef">
                            ชื่อผู้ติดต่อ
                            </td>
                            
                             <td style="background-color:#efefef">
                            สถานะการส่ง email
                            </td>
               		  </tr>
                         
                         
                                                   
                         
                    </table>
                     
                     
                                        
                   
                </td>
			</tr>
             
             <tr>
                <td align="right" colspan="2">
                    <hr />
                    <div class="copyright" align="center" ><a href="#" style="font-size:10px;">Help</a> | <a href="#" style="font-size:10px;">About</a> | <a href="#" style="font-size:10px;">Contact</a> | © Copyright 2018 NEP</div>
                    
                    <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-75748320-1', 'auto');
  ga('send', 'pageview');

</script>                </td>
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
	}

function uncheckAll(){
	}
</script>
</body>
</html>