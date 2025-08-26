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
<table width="1024px" align="center" border="0">
<tr>
<td>

<div id="masthead" >

	
	<table border="0" width="100%">
    	<tr>
        	<td>
            <div style="padding-bottom:10px;" class="logo_text" align="left">ระบบรายงานผลการจ้างงานคนพิการ (สำหรับเจ้าหน้าที่ฯ)</div>
            </td>
            <td valign="bottom">
            <?php if($_SESSION['sess_userfullname']){ ?>
            <div  align="right" style="color:#FFFFFF">เข้าระบบโดยชื่อ user: <?php echo $sess_userfullname?></div>
            <?php } ?>
            </td>
         </tr>
    </table>
	
  
   <?php if(isset($sess_userid)){ ?>
  <div id="globalNav" > 
    <img alt="" src="gblnav_left.gif" height="32" width="4" id="gnl"> <img alt="" src="glbnav_right.gif" height="32" width="4" id="gnr"> 
    <div id="globalLink" > 
      
      
      
       <a href="index.php"  class="glink" style="color:#000000;"> 
	  หน้าแรก</a>
      
      
       <a href="gjob.dep.go.th"  class="glink" style="color:#000000;"> 
      ระบบรายงานผลหน่วยงานภาครัฐ
      </a>
      
		<?php 
			
			if($sess_accesslevel == 4){
			
				$this_logged_in_province = getFirstItem("select Province from company where CID = '$sess_meta' limit 0,1");        

        		if($this_logged_in_province == "1"){        
        ?>
                 <a href="http://prjob.nep.go.th/?page_id=37" target="_blank" class="glink" style="color:#000000;">ดาวน์โหลดเอกสาร</a>
        	<?php }else{ ?>
                 <a href="http://prjob.nep.go.th/?page_id=37" target="_blank" class="glink" style="color:#000000;">ดาวน์โหลดเอกสาร</a>
        	<?php } ?>   
        
        <?php } ?>
     
      
      <?php if($sess_accesslevel != 4){ //company won;t see these?>
      	  <a href="org_list.php?mode=search" class="glink" style="color:#000000;">ค้นหา<?php echo $the_company_word;?></a>

			  <?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){ //exec won;t see these?>
                  
                  <!--<a href="organization.php?mode=new"  class="glink" style="color:#000000;"> 
                  เพิ่มข้อมูล</a>-->
                  
                  <a href="org_list.php?mode=letters" class="glink" style="color:#000000;">ส่งจดหมาย</a>
                  <!--<a href="org_list.php?mode=payment" class="glink" style="color:#000000;">ส่งเงิน</a>-->
              <?php }?>
              
              <?php if($sess_accesslevel != 3 && $sess_accesslevel != 5 && $sess_accesslevel != 8){ //provincial and exec won;t see these?>
	              <a href="org_list.php?mode=announce" class="glink" style="color:#000000;">ประกาศผ่านสื่อ</a>
              <?php } ?>
              
              
          <?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){ ?>
          	<a href="view_reports_gov.php" class="glink" style="color:#000000;">รายงาน</a>
          <?php }else{ ?>
         	 <a href="view_reports.php" class="glink" style="color:#000000;">รายงาน</a>
          <?php }?>
          
      <?php } ?>
           <a href="http://ejob.dep.go.th/?page_id=4793" target="_blank" class="glink" style="color:#000000;">กฎหมายที่เกี่ยวข้อง</a>
          
         <a href="http://ejob.dep.go.th/?page_id=4811" target="_blank" class="glink" style="color:#000000;">แบบรายงาน</a>
         
          
     
      <?php if($sess_accesslevel != 6 && $sess_accesslevel != 7 && $sess_accesslevel != 4){ //company won;t see these?>
          <a href="faq.php" class="glink" style="color:#000000;">ถาม-ตอบ</a>
      <?php }?>
          
          
          
      <?php
	  
	  	//echo "can manage user?". $sess_can_manage_user;
	  
	   if($sess_accesslevel != 4){ //company won;t see these?>
          <?php if($sess_accesslevel == 1 || $sess_can_manage_user){ //only admin will see this // yoes 20141007 -- add ability for พมจ to edit users //yoes 20161201 - pmj wont see this ||  $sess_can_manage_user?>
            <a href="user_list.php" class="glink" style="color:#000000;">ผู้ใช้งานระบบ</a>
          <?php }?>
      
      <?php } ?>
      
      <a href="view_user.php?id=<?php echo $sess_userid; ?>" class="glink" style="color:#000000;">เปลี่ยนรหัสผ่าน</a>
      
      
      <?php if($sess_accesslevel == 4){?>
      <a href="คู่มือการใช้งาน ระบบรายงานผลการจ้างงานคนพิการ สำหรับ สถานประกอบการ 2559.pdf" target="_blank" class="glink" style="color:#000000;">สื่อการสอน</a>
      <?php }else{?>
      <a href="ebook-Hire System-2557.zip" class="glink" style="color:#000000;">สื่อการสอน</a>
      <?php }?>
      
      <a href="scrp_do_logout.php" class="glink" style="color:#000000;">ออกจากระบบ</a>
      <!-- end globalNav --> 
    </div>
    <!-- end mast head --> 
<?php }else{ ?>
 <div id="globalNav"> 
    <img alt="" src="gblnav_left.gif" height="32" width="4" id="gnl"> <img alt="" src="glbnav_right.gif" height="32" width="4" id="gnr"> 
    <div id="globalLink"> 
       <a href="index.php"  class="glink" style="color:#000000;"> 
	  		login เข้าสู่ระบบ
      </a>
      
      
      <!-- end globalNav --> 
    </div>
    <!-- end mast head --> 
<?php }?>
</td>
</tr>
</table>



<table align="center" cellpadding="0" cellspacing="0"  style="padding-top: 10px; width: 95%;">
<tr>
<td bgcolor="#FFFFFF">
<img alt="" src="tl_curve_white.gif" height="6" width="6" id="tl" style=""><img alt="" src="tr_curve_white.gif" height="6" width="6" id="tr" style=" float:right"> 

</td>
</tr>
<tr>
<td>
<div id="pagecell1" style="width: 100%" > 
  <!--pagecell1--> 
 
  
  

<?php

	//get this script name
	$this_page = $_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING'];
	$this_script_name = $_SERVER['SCRIPT_NAME'];

?>
<table width="100%" align="center" border="0">
	<tr>
    	<td>
        
         
      <table bgcolor="#FFFFFF" width="100%"  style="padding: 0 5px 5px 5px;
      <?php if(!strpos($this_page, "organization.php") && !strpos($this_page, "view_reports.php")) {?>
      background: url(nep_logo.jpg) no-repeat 50% 50%;
      <?php }?>
      
      
" border="0">
        	<tr>
            	<td colspan="2">
                <h1 class="default_h1" style="margin:0; padding:0; "  >
                	<?php 
						if(isset($sess_userid)){ 
							
							//echo $this_script_name;
							
							if(strpos($this_page, "mode=letters") 
								|| strpos($this_script_name, "letter_list.php")
								|| strpos($this_page, "mode=payment") 
								|| strpos($this_script_name, "payment_list.php")
								||strpos($this_page, "mode=announce") 
								|| strpos($this_script_name, "announce_list")
								){
								echo "การดำเนินการตามกฎหมาย";							
							}elseif(strpos($this_script_name, "org_list.php")
							 || strpos($this_script_name, "organization.php")){
								echo "<?php echo $the_company_word;?>";							
							}
						
						}else{ ?>

                    	Login
                        
					<?php }?>
                </h1>
                <hr  style="margin-bottom:0; "/>
                </td>
			</tr>
            <tr>
            	<td valign="top" width="225" style="border-right: solid 1px #efefef;">
                	
               		<?php include "left_menu.php"; ?> 
                                        
                </td>