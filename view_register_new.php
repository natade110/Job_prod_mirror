<?php

		
	include "db_connect.php";
	include "scrp_config.php";
	
	
	if(is_numeric($_GET["id"]) && $sess_accesslevel == 1){
		
		$mode = "edit";	
		$this_id = $_GET["id"];
		
		$post_row = getFirstRow("select * 
								from 
									register
								where 
									register_id  = '$this_id'
								limit 0,1");
								
		//vars to use
		$output_fields = array(
						
						'register_id'
						,'register_name'
						,'register_password'
						
						,'register_org_name'
						,'register_province'
						,'register_contact_name'
						,'register_contact_phone'
						,'register_position'
						,'register_email'

						
						);
				//echo "asdasd";
		for($i = 0; $i < count($output_fields); $i++){
			//clean all inputs
			//echo $i;
			$register_values[$output_fields[$i]] .= doCleanOutput($post_row[$output_fields[$i]]);
		}								
		
	}else{
	
		//only has "ADD" mode for now
		$mode = "add";	
		$this_id = "new";
	
	}
	
?>

<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" lang="th">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" lang="th">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html lang="th">
<!--<![endif]-->
<center><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- bluebaselink -->
<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:15px"
     data-ad-client="ca-pub-5233975676304559"
     data-ad-slot="1283070830"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></center>

<script class="jsbin" src="jquery-1.11.1.min.js"></script>

<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width" />
<title>การจ้างงานคนพิการในสถานประกอบการ | จ้างงานคนพิการ</title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="http://prjob.nep.go.th/xmlrpc.php" />
<!--[if lt IE 9]>

<script src="http://prjob.nep.go.th/wp-content/themes/twentytwelve/js/html5.js" type="text/javascript"></script>
<![endif]-->
<link rel="alternate" type="application/rss+xml" title="การจ้างงานคนพิการในสถานประกอบการ &raquo; Feed" href="http://prjob.nep.go.th/?feed=rss2" />
<link rel="alternate" type="application/rss+xml" title="การจ้างงานคนพิการในสถานประกอบการ &raquo; ความเห็น Feed" href="http://prjob.nep.go.th/?feed=comments-rss2" />

<link rel='stylesheet' id='twentytwelve-style-css'  href='http://prjob.nep.go.th/wp-content/themes/twentytwelve/style.css?ver=3.7.1' type='text/css' media='all' />
<!--[if lt IE 9]>
<link rel='stylesheet' id='twentytwelve-ie-css'  href='http://prjob.nep.go.th/wp-content/themes/twentytwelve/css/ie.css?ver=20121010' type='text/css' media='all' />
<![endif]-->
<link rel="EditURI" type="application/rsd+xml" title="RSD" href="http://prjob.nep.go.th/xmlrpc.php?rsd" />
<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="http://prjob.nep.go.th/wp-includes/wlwmanifest.xml" /> 
<meta name="generator" content="WordPress 3.7.1" />
	<style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>






<link rel="stylesheet" type="text/css" href="skeleton/skeleton.css" />
<script src="http://prjob.nep.go.th/skeleton/stuHover.js" type="text/javascript"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <meta name="description" content="Parallax Content Slider with CSS3 and jQuery" />
        <meta name="keywords" content="slider, animations, parallax, delayed, easing, jquery, css3, kendo UI" />
        <meta name="author" content="Codrops" />
        <link rel="shortcut icon" href="../favicon.ico"> 
        <link rel="stylesheet" type="text/css" href="http://prjob.nep.go.th/css/demo.css" />
        <link rel="stylesheet" type="text/css" href="http://prjob.nep.go.th/css/style2.css" />
		<script type="text/javascript" src="http://prjob.nep.go.th/js/modernizr.custom.28468.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Economica:700,400italic' rel='stylesheet' type='text/css'>
		<noscript>
			<link rel="stylesheet" type="text/css" href="http://prjob.nep.go.th/css/nojs.css" />
		</noscript>
</script>
<style type="text/css">
<!--
.pageheader
       {
          width:100%;
          height:50px;
          background-color: ;  
		  opacity:1.0;
          filter:alpha(opacity=100); /* For IE8 and earlier */
          position:fixed;
          top:0;
          z-index:99;  
}
body p {
	text-align: center;
}
body {
	background: no-repeat center top fixed;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
	background-image: url(http://prjob.nep.go.th/images/bg.jpg);
	font-size: 12px;
	background-color: #FFF;
	color: #ffffff;
	/*text-align: center;*/
}
.normal {
	font-size: 18px;
}
body p {
	text-align: left;
	color: #ffffff;
}

A:link { color: #2525252; text-decoration:none}
A:visited { color:#909; text-decoration: none}
A:hover {color: #252525}
-->
</style>
<link href="http://prjob.nep.go.th/SpryAssets/SpryMenuBarHorizontal.css" rel="stylesheet" type="text/css">
<script src="http://prjob.nep.go.th/SpryAssets/SpryMenuBar.js" type="text/javascript"></script>
<script type="text/javascript">
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
</script>





</head>



<body class="home blog custom-font-enabled">
<div id="page" class="hfeed site">
	<header id="masthead" class="site-header" role="banner">
		<hgroup>
        
        
        	<table>
            	<tr>
                	<td>
                    <img src="http://prjob.nep.go.th/images/logo_pk.png" width="150" height="145">
                    </td>
                    <td>
                    <img src="http://prjob.nep.go.th/wp-content/uploads/2015/08/edit-text-prjob-20Aug.png" width="801" height="128">
                    </td>
                </tr>
            </table>
			
		</hgroup>

		

		<nav id="site-navigation" class="main-navigation" role="navigation">
        	
			<h3 class="menu-toggle">เมนู</h3>
			<a class="assistive-text" href="#content" title="ข้ามไปยังเนื้อหา">ข้ามไปยังเนื้อหา</a>
            
            <div align="right" style="font-size: 12px; padding-bottom:5px;">
            เข้าสู่ระบบรายงานผลการจ้างงานคนพิการ  
            
            </div>
            
            <form action="scrp_do_login.php" method="post">
                 <div align="right" style="font-size: 11px; padding-bottom:5px;">
                ชื่อผู้ใช้:  <input name="user_name" type="text" style="padding: 0; width: 100px; font-size: 11px;" />
                
                รหัสผ่าน: <input name="password" type="password"  style="padding: 0; width: 100px; font-size: 11px;"/>
                </div>
                
                <div align="right" style="font-size: 11px; padding-bottom:5px;">
                    
                 <input name="" type="submit" value="เข้าสู่ระบบ" style="padding: 0; width: 70px; font-size: 12px;"/>
                 
                 
                 <?php if($_GET["mode"] == "error_pass"){echo "<br><font color='#F7FE2E'>ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง!</font>";}?>
                                
				<?php if($_GET["mode"] == "pending"){echo "<br><font color='#F7FE2E'>User Name รอเปิดการใช้งาน โดยผู้ดูแลระบบ</font>";}?> 
                
                <?php if($_GET["mode"] == "disabled"){echo "<br><font color='#F7FE2E'>User Name ไม่ได้รับอนุญาตให้ใช้งานระบบ</font>";}?> 
                 
                 
                 <input name="return_url" type="hidden" value="new_index.php" />
                </div>
            </form>
            
            <div align="right" style="font-size: 11px; padding-bottom:5px;">
            	
                <a href="view_register_term_new.php" style="color: #fefefe; text-decoration: underline; font-size: 11px;">สมัครใช้งาน</a>
                |
                <a href="view_register_password_new.php" style="color: #fefefe; text-decoration: underline; font-size: 11px;">ลืมรหัสผ่าน</a>
            
            </div>
            
            
            
			<div class="menu-menu-1-container"><ul id="menu-menu-1" class="nav-menu"><li id="menu-item-10" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-10"><a href="new_index.php">หน้าแรก</a></li>
<li id="menu-item-11" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-11"><a href="#">เกี่ยวกับกองทุน</a></li>
<li id="menu-item-346" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-346"><a href="#">กฎหมายที่เกี่ยวข้อง</a></li>
<li id="menu-item-333" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-333"><a href="#">ดาวน์โหลด</a></li>
<li id="menu-item-179" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-179"><a href="#">หน่วยงานที่เกี่ยวข้อง</a></li>
<li id="menu-item-47" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-47"><a href="#">ตรวจสอบคนพิการ</a></li>
<li id="menu-item-43" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-43"><a href="#">ติดต่อกองทุน</a></li>
<li id="menu-item-487" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-487"><a href="#">เว็บไซต์ พก.</a></li>
</ul></div>		</nav><!-- #site-navigation -->

			</header><!-- #masthead -->

	<div id="main" class="wrapper">
    
    
	<div id="primary" class="site-content">
		<div id="content" role="main">

							
	<article id="post-32" class="post-32 page type-page status-publish hentry">
		<!--<header class="entry-header">
												<h1 class="entry-title"></h1>
		</header>-->
        <header class="entry-header">
			
            <div align="center">
				<h1 class="entry-title">
                <?php if($mode == "edit"){?>
                    แก้ไขข้อมูล User สถานประกอบการ
                    <?php }else{?>
                	สถานประกอบการสมัครเข้าใช้งาน
                    <?php }?>
                </h1>
			</div>
			
		</header><!-- .entry-header -->
        
        
        
		<div class="entry-content"  >
        
        
         <?php 
						if($_GET["user_added"]=="user_added"){
							
							$register_id = $_GET["id"];
							$register_row = getFirstRow("select * from users where user_id = '$register_id'");
							
							
					?>							
                         <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* เพิ่มข้อมูลการใช้งานเสร็จสิ้น</div>
                         
                         <table border="0">
                          <tr>
                            <td colspan="2"><hr /><strong>ข้อมูลการใช้งานระบบ</strong>
                            
                            <!--
                            <br />คุณสามารถ <a href="submit_forms.php">ส่งเอกสารการปฏิบัติตามกฏหมาย</a> ได้ด้วย user name และ password ด้านล่าง
                            -->
                            
                            <hr /></td>
                           </tr>
                          <tr>
                            <td>User name:</td>
                            <td><?php echo $register_row["user_name"];?></td>
                          </tr>
                          <tr>
                            <td>Password:</td>
                            <td><?php echo $register_row["user_password"];?></td>
                          </tr>
                         <tr>
                            <td>ชื่อสถานประกอบการ:</td>
                            <td><?php 
							
							$my_company_row = getFirstRow("select CompanyNameThai, CompanyTypeCode from company where CID = '".$register_row["user_meta"]."'");
							
							echo formatCompanyName($my_company_row["CompanyNameThai"],$my_company_row["CompanyTypeCode"]);
							
							?></td>
                          </tr>
                          
                           <tr>                            
                            <td colspan="2">
                            <hr />
                            
                            <span style="color:#fff; ">
                           	กรุณารอการยืนยัน เริ่มการใช้งานผ่านทาง email ของคุณ (<?php echo $register_row["user_email"];?>)
                            </span>
                            
                            <hr />
                            </td>
                          </tr>
                          
                        </table>

                         
                    <?php
						}					
					?>
                   
                   	<?php 
						if($_GET["updated"]=="updated"){
					?>							
                <div style="color:#006600; padding:5px 0 0 0; font-weight: bold;">* แก้ไขข้อมูลเสร็จสิ้น</div>
                    <?php
						}					
					?>
                    <?php 
						if($_GET["duped"]=="duped"){
					?>							
                <div style="color:#CC3300; padding:5px 0 0 0; font-weight: bold;">* User Name ที่ใช้สมัครมีอยู่ในระบบแล้ว กรุณาใช้ user name อื่นในการสมัคร - ลืมรหัสผ่าน <a href="view_register_password.php">คลิกที่นี่</a></div>
                    <?php
						}					
					?>
                    
                    <?php 
						if($_GET["mailed"]=="mailed"){
					?>							
                <div style="color:#CC3300; padding:5px 0 0 0; font-weight: bold;">* Email ที่ใช้สมัครมีอยู่ในระบบแล้ว กรุณาใช้ email อื่นในการสมัคร - ลืมรหัสผ่าน <a href="view_register_password.php">คลิกที่นี่</a></div>
                    <?php
						}					
					?>
			
            
           <form 
                	method="post" 
                    id="view_user_form" 
                    action="scrp_update_register_new.php" 
                    onsubmit="return validate_register(this);"               
					
                    enctype="multipart/form-data"
                    
                
                >
                     <input name="register_id" type="hidden" value="<?php echo $this_id;?>" />
                     
                     <script>
					 
					
					 $().ready(function() {
						 
						 //alert("whaattt");
						 // validate signup form on keyup and submit
						$("#view_user_form").validate({
							
							
							rules: {
								register_contact_name: "required",
								register_contact_lastname: "required",
								register_contact_phone: {
									required: true,
									number: true
								},
								register_email: {
									required: true,
									email: true
								},
								register_position: {
									required: true
								},
								register_employee_card: {
								  required: true,
								  accept: "image/*"
								},
								register_id_card: {
								  required: true,
								  accept: "image/*"
								},
								user_commercial_code: {
									required: true,
									number: true,
									maxlength: 13,
									minlength: 13
									
								}
							},
							messages: {
								register_contact_name: "กรุณาใส่ ชื่อผู้ติดต่อ",
								register_contact_lastname: "กรุณาใส่ นามสกุลผู้ติดต่อ",
								register_contact_phone: "กรุณาใส่ เบอร์โทรศัพท์ ที่เป็นตัวเลขเท่านั้น",
								register_email: "กรุณาใส่ email ให้ถูกต้อง",
								register_position: "กรุณาใส่ ตำแหน่ง ให้ถูกต้อง",
								register_employee_card: "กรุณาแนบรูป เป็นไฟล์ jpg, gif หรือ png เท่านั้น",
								register_id_card: "กรุณาแนบรูป  เป็นไฟล์ jpg, gif หรือ png เท่านั้น",
								user_commercial_code: "กรุณาใส่ เลขทะเบียนนิติบุคคล เป็นตัวเลข 13 หลักเท่านั้น"
							}
						});
						 
						 
						 
					 }); /**/
					 
					 </script>
                   <table border="0" cellpadding="0">
                        <tr>
                          <td> <hr /><table border="0" style="padding:0px 0 0 50px;" >
                              <tr>
                                <td colspan="4">
                                	<hr />
                                	<span style="font-weight: bold">ข้อมูลการใช้งานระบบ</span>                                </td>
                              </tr>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">User Name</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  
                                  		<?php if($mode == "edit"){?>
                                        
											<?php echo $register_values["register_name"];?> 
	                                   
                                        <?php }else{?>
	                                   <input 
                                       
                                       	name="register_name" type="text" id="register_name" value="<?php echo $output_values["user_name"];?>"
                                       
                                       	onchange="doCheckUserName();"
                                        
                                         />
	                                   <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>
                                       
                                       <br />
                                       
                                       <span class="style86" id="register_name_used" style="padding: 10px 0 10px 0; display: none;"><font color="red">user name นี้ถูกใช้งานแล้ว - กรุณาใช้ user name อื่น</font></span>
	                                   <?php }?>
                                  
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              
                              <script>
							  	
								function doCheckUserName(){
								
									//alert($('#register_name').val());	
									$.ajax({ url: './ajax_check_user_name.php',
										 data: {user_name: $('#register_name').val()},
										 type: 'post',
										 success: function(output) {
											 //alert(output);
											 if(output == 1){
												$('#register_name_used').css("display",""); 
											 }else{
												 $('#register_name_used').css("display","none"); 
											 }
											 //
										  }
									});
									
								}
							  
							  </script>
                              
                             
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">Password</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_password" type="password" id="register_password"  value="<?php echo $register_values["register_password"];?>"  />
                                  <font color="red">*</font>
                                </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              
                              <?php if($mode != "edit"){?>
                              <tr>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">ยืนยัน Password</span></td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                
                                  <input name="register_password_2" type="password" id="register_password_2"  value=""  />
                                  <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span></span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                              <?php }?>
                              
                             
                              
                             
                              
                            
                              <tr>
                                <td colspan="4"><hr />
                                <span style="font-weight: bold">ข้อมูสถานประกอบการ</span></td>
                              </tr>
                              
                              
                              <tr id="tr_textbox">
                                <td >เลขที่บัญชีนายจ้าง </td>
                                <td><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_org_code" type="text" id="register_org_code" value="<?php echo $register_values["register_org_code"];?>"  />
                                  
                                  <input name="register_org_name" type="hidden" id="register_org_name" value="<?php echo $register_values["register_org_name"];?>"  />
                                  <input name="register_cid" type="hidden" id="register_cid" value="<?php echo $register_values["register_cid"];?>"  />
                                  <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span><br />
                                  <input id="btn_get_data" type="button" value="ตรวจสอบเลขที่บัญชีนายจ้าง" onClick="return doGetData();" />
                                  
                                  
                                  <script>
											
												function doGetData(){
												
													var the_id = "";
													
													//
													the_id = the_id + document.getElementById('register_org_code').value;
												
													var checkOK = "1234567890";
												   var checkStr = the_id;
												   var allValid = true;
												   for (i = 0;  i < checkStr.length;  i++)
												   {
													 ch = checkStr.charAt(i);
													 for (j = 0;  j < checkOK.length;  j++)
													   if (ch == checkOK.charAt(j))
														 break;
													 if (j == checkOK.length)
													 {
													   allValid = false;
													   break;
													 }
												   }
												   if (!allValid)
												   {
													 alert("เลขที่บัญชีนายจ้างต้องเป็นเลข 10 หลักเท่านั้น");
													 document.getElementById('register_org_code').focus();
													 return (false);
												   }
													
													
													if(the_id.length != 10)
													{
														alert("เลขที่บัญชีนายจ้างต้องเป็นเลข 10 หลักเท่านั้น");
														document.getElementById('register_org_code').focus();
														return (false);
													}
												
													//alert("do get data");
													//document.getElementById('btn_get_data').style.display = 'none';
													//document.getElementById('img_get_data').style.display = '';
													
													var parameters = "the_id="+the_id;
													//alert(parameters);
													//return false;
													//send requests
													http_request = false;
													 if (window.XMLHttpRequest) { // Mozilla, Safari,...
														 http_request = new XMLHttpRequest();
														 if (http_request.overrideMimeType) {										
															http_request.overrideMimeType('text/html');
														 }
													  } else if (window.ActiveXObject) { // IE
														 try {
															http_request = new ActiveXObject("Msxml2.XMLHTTP");
														 } catch (e) {
															try {
															   http_request = new ActiveXObject("Microsoft.XMLHTTP");
															} catch (e) {}
														 }
													  }
													  if (!http_request) {
														 alert('Cannot create XMLHTTP instance');
														 return false;
													  }
													
													http_request.onreadystatechange = alertContents3;
													http_request.open('POST', "./ajax_get_company.php", true);
													http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded;");
													http_request.setRequestHeader("Content-length", parameters.length);
													http_request.setRequestHeader("Connection", "close");
													
													http_request.send(parameters);
													
													return true;
												
												}
												
												function alertContents3(){
													
													if (http_request.readyState == 4) {
													
														if (http_request.status == 200) {
															
															//alert("response recieved");
															//return false;
															
															if(http_request.responseText == "no_result"){
															
																alert("ไม่พบข้อมูลบัญชีนายจ้าง");
																//no result
																
															}else{
															
																var JSONFile = http_request.responseText;  
																eval(JSONFile); 	
																
																//alert(someVar.company_name_thai);
																alert("เลขบัญชีนายจ้างถูกต้อง");
																//document.getElementById('le_age').value = someVar.BIRTH_DATE;
																document.getElementById('tr_textbox').style.display = 'none';
																document.getElementById('tr_result').style.display = '';
																
																document.getElementById('tr_result_2').style.display = '';
																
																document.getElementById('span_org_code').innerHTML = document.getElementById('register_org_code').value;
																document.getElementById('span_org_name').innerHTML = someVar.company_name_thai;
																
																document.getElementById('register_org_name').value = someVar.company_name_thai;
																document.getElementById('register_cid').value = someVar.company_cid;
															
																
															
															}
															//
															
														} else {
															alert('การเชื่อมต่อผิดพลาด โปรดลองอีกครั้ง');
														}
													}
												
												}
											
											</script>
                                  
                                  
                                  
                                </span></td>
                                <td></td>
                                <td></td>
                              </tr>
                              
                              <tr id="tr_result" style="display: none;">
                                <td >เลขที่บัญชีนายจ้าง</td>
                                <td><span id="span_org_code" style="font-weight: bold;"></span></td>
                                <td>ชื่อบริษัท (ภาษาไทย)</td>
                                <td><span id="span_org_name" style="font-weight: bold;"></span></td>
                              </tr>
                              
                              <tr id="tr_result_2" style="display: none;">
                                <td >เลขทะเบียนนิติบุคคลของกระทรวงพาณิชย์</td>
                                <td colspan="3"><span id="span_org_code" style="font-weight: bold;">
                                
                             	   <input name="user_commercial_code" type="text" id="user_commercial_code" value="<?php echo $register_values["user_commercial_code"];?>" maxlength="13"  />
                             	   <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span></span></td>
                               
                              </tr>
                              
                              <tr>
                                <td colspan="4"><hr />
                                <strong>ข้อมูลผู้ติดต่อ</strong></td>
                              </tr>
                              
                              <tr>
                                <td valign="top">ชื่อ</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_contact_name" type="text" id="register_contact_name" value="<?php echo $register_values["register_contact_name"];?>" />
                                <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                                <td valign="top">นามสกุล</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_contact_lastname" type="text" id="register_contact_lastname" value="<?php echo $register_values["register_contact_lastname"];?>" />
                                <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                              </tr>
                              <tr>
                               <td valign="top">เบอร์โทรศัพท์</td>
                                <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                  <input name="register_contact_phone" type="text" id="register_contact_phone" value="<?php echo $register_values["register_contact_phone"];?>" />
                                <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                </span></span></td>
                                
                                <td valign="top">อีเมล์</td>
                                 <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                   <input name="register_email" type="text" id="register_email" value="<?php echo $register_values["register_email"];?>" 
                                   
                                   onchange="doCheckEmail();"
                                   
                                   />
                                 <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                 </span></span>
                                 
                                 <br />
                                 <span class="style86" id="email_used" style="padding: 10px 0 10px 0; display: none;"><font color="red">email นี้ถูกใช้งานแล้ว - กรุณาใช้ email อื่น</font></span>
                                 
                                 </td>
                              </tr>
                              
                              
                               <script>
							  	
								function doCheckEmail(){
								
									//alert($('#register_name').val());	
									$.ajax({ url: './ajax_check_email.php',
										 data: {email: $('#register_email').val()},
										 type: 'post',
										 success: function(output) {
											 //alert(output);
											 if(output == 1){
												$('#email_used').css("display",""); 
											 }else{
												 $('#email_used').css("display","none"); 
											 }
											 //
										  }
									});
									
								}
							  
							  </script>
                              
                              
                               <tr>
                                 
                                 <td valign="top">ตำแหน่ง</td>
                                 <td valign="top"><span class="style86" style="padding: 10px 0 10px 0;"><span class="style86" style="padding: 10px 0 10px 0;">
                                   <input name="register_position" type="text" id="register_position" value="<?php echo $register_values["register_position"];?>" />
                                 <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span>                                 </span></span></td>
                                 <td valign="top">&nbsp;</td>
                                <td valign="top">&nbsp;</td>
                               </tr>
                               
                               
                               <tr>
                                <td colspan="4"><hr />
                                <strong>แนบเอกสารยืนยันตัวเอง</strong></td>
                              </tr>
                              
                               <tr>
                                 <td valign="top">1) บัตรประจำตัวพนักงาน<br /> 
                                 หรือเอกสารการยืนยันเป็นพนักงาน <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span></td>
                                 <td valign="top" colspan="3">
                                 
                                 <input name="register_employee_card"  type="file"  />
                                 </td>
                                
                               </tr>
                               
                               <tr>
                                 <td valign="top">2) บัตรประจำตัวประชาชน <span class="style86" style="padding: 10px 0 10px 0;"><font color="red">*</font></span></td>
                                 <td valign="top" colspan="3">
                                 
                                 <input name="register_id_card"  type="file" />
                                 </td>
                                
                               </tr>
                              
                              
                          </table></td>
                        </tr>
                        
                        
                        
                        
                        <tr>
                          <td><hr />
                              <div align="center">
                              
                              	<?php if($mode == "edit"){?>
                                <input type="submit" value="แก้ไขข้อมูล" />
                                <?php }else{?>
                                <input type="submit" value="สมัครเข้าใช้งาน" />
                   				 <?php }?>
                                
                          </div></td>
                        </tr>
                        
                        
                        
                        <?php if($sess_accesslevel == 1){?>
                        
                        <tr>
                          <td><hr />
                              <div align="center">
                              
                              <a href="report_20.php?mod_register_id=<?php echo $register_values["register_id"];?>" target="_blank">
                              ดูรายงานการบันทึกข้อมูลเจ้าหน้าที่ของสถานประกอบการ
                              </a>
                                
                          </div></td>
                        </tr>
                        
                         <tr>
                          <td>
                          
                          <hr />
                          <strong>เอกสารที่เคยส่งไปแล้ว</strong>
                          
                          </td>
                        </tr>
                        
                        <tr>
                            <td>
                            
                            	<table border="0" cellpadding="5" style="border-collapse:collapse;">
                                  <tr bgcolor="#9C9A9C" align="center" >
                                    <td><span class="column_header">สำหรับปี</span></td>
                                    <td><span class="column_header">ไฟล์</span></td>
                                    <td><span class="column_header">วันที่ส่งไฟล์</span></td>
                                    <td><span class="column_header"></span></td>
                                    <td>&nbsp;</td>
                                  </tr>
                                  
                                  <?php
								  
								  	$pay_sql = "select 
													* 
												from 
													modify_history_register 
												where 
													mod_register_id = '".$register_values["register_id"]."'
													and mod_type = 3
												order by mod_year desc
												";
												
									//echo $pay_sql;
								  
								  	$pay_result = mysql_query($pay_sql);
						
									while ($pay_row = mysql_fetch_array($pay_result)) {

								  
								  ?>
                                  <tr>
                                    <td><?php echo formatYear($pay_row["mod_year"]);?></td>
                                    <td><a href="register_doc/<?php echo $pay_row["mod_file"];?>"><?php echo $pay_row["mod_file"];?></a></td>
                                    <td><?php echo formatDateThai($pay_row["mod_date"]);?></td>
                                    <td><?php echo $pay_row["mod_desc"];?></td>
                                    <td></td>
                                  </tr>
                                  <?php }?>
                                  
                                </table>

                            
                            
                            </td>
                        </tr>
                        
                        
                        <?php }//$sess_accesslevel == 1?>
                        
                      </table>
                      
                </form>
                   <script language='javascript'>
						<!--
						function validate_register(frm) {
							
							
							if($('#register_name_used').css("display") != "none"){
								alert("กรุณาเลือกชื่อ user name ใหม่");
								frm.register_name.focus();
								return false;	
							}
							if($('#email_used').css("display") != "none"){
								alert("กรุณาเลือกชื่อ email ใหม่");
								frm.register_email.focus();
								return false;	
							}
							
							
							
							<?php if($mode == "add"){ ?> 
							
							
							
							
							
							if(frm.register_name.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อ user name");
								frm.register_name.focus();
								return (false);
							}
							
							
							var checkOK = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.-_";
						   var checkStr = frm.register_name.value;
						   var allValid = true;
						   for (i = 0;  i < checkStr.length;  i++)
						   {
							 ch = checkStr.charAt(i);
							 for (j = 0;  j < checkOK.length;  j++)
							   if (ch == checkOK.charAt(j))
								 break;
							 if (j == checkOK.length)
							 {
							   allValid = false;
							   break;
							 }
						   }
						   if (!allValid)
						   {
							 alert("ชื่อ user name สามารถเป็นภาษาอังกฤษหรือตัวเลขเท่านั้น");
							 frm.register_name.focus();
							 return (false);
						   }


							if(frm.register_password.value != frm.register_password_2.value)
							{
								alert("กรุณาใส่ข้อมูล: ยืนยัน password ใหม่ไม่ถูกต้อง");
								frm.register_password_2.focus();
								return (false);
							}
							
							
							if(frm.register_org_name.value.length < 1)
							{
								alert("เลขที่บัญชีนายจ้างไม่ถูกต้อง กรุณาใส่เลขที่บัญชีนายจ้าง และทำการ 'ตรวจสอบเลขที่บัญชีนายจ้าง' อีกครั้ง");								
								return (false);
							}
							
							
							if(frm.register_employee_card.value.length < 1){
								alert("กรุณาแนบไฟล์: บัตรประจำตัวพนักงาน หรือเอกสารการยืนยันเป็นพนักงาน");								
								return (false);
							}
							
							if(frm.register_id_card.value.length < 1){
								alert("กรุณาแนบไฟล์: บัตรประจำตัวประชาชน");								
								return (false);
							}
							
							
							<?php } ?>
							
							
							if(frm.register_password.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: password");
								frm.register_password.focus();
								return (false);
							}
							
							
							
							
							if(frm.register_org_code.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: เลขที่บัญชีนายจ้าง");
								frm.register_org_code.focus();
								return (false);
							}
							
							//----
							if(frm.Province.selectedIndex == 0)
							{
								alert("กรุณาใส่ข้อมูล: จังหวัด");
								frm.Province.focus();
								return (false);
							}
							
							
							if(frm.register_contact_name.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: ชื่อผู้ติดต่อ");
								frm.register_contact_name.focus();
								return (false);
							}
							
							if(frm.register_contact_lastname.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: นามสกุลผู้ติดต่อ");
								frm.register_contact_lastname.focus();
								return (false);
							}
														
							if(frm.register_contact_phone.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: เบอร์โทรศัพท์");
								frm.register_contact_phone.focus();
								return (false);
							}
							
							if(frm.register_email.value.length < 1)
							{
								alert("กรุณาใส่ข้อมูล: อีเมล์");
								frm.register_email.focus();
								return (false);
							}
							
							
							
							
							
							//----
							return(true);									
						
						}
						-->
					
					</script>    
                    
                    <?php if($_GET["user_added"]=="user_added"){ ?>
                         <script>
                         document.getElementById("view_user_form").style.display = "none";
						 </script>
                                        
                    <?php }?>      
          
					</div><!-- .entry-content -->
		<footer class="entry-meta">
					</footer><!-- .entry-meta -->
	</article><!-- #post -->
							
		</div><!-- #content -->
	</div><!-- #primary -->


	</div><!-- #main -->

	
   

	<p>&nbsp;</p>
     <table width="1025" height="80" border="0" align="center">
       <tr>
         <td width="256" height="76" align="center">
         
                  
         <a href="http://prjob.nep.go.th?p=516" target="_blank">
         <img src="http://prjob.nep.go.th/images/money.png" height="58">
         </a>
         
         </td>
         <td width="256" align="center">
         
         
                  
         
         <a href="http://prjob.nep.go.th?p=520" target="_blank" >
         <img src="http://prjob.nep.go.th/images/m222222.png" width="239" height="58">
         </a>
         
         </td>
         <td width="256" align="center">
         
         
                  
         <a href="http://prjob.nep.go.th?p=522" target="_blank">
         <img src="http://prjob.nep.go.th/images/mofbfn.png" width="239" height="58">
          </a>
          
          
         </td>
         <td width="256" align="center">
         
         
                  
         <a href="http://prjob.nep.go.th?p=518" >
         <img src="http://prjob.nep.go.th/images/modsfdf.png" width="239" height="58">
         </a>
         
         
         
         
         </td>
         </tr>
       </table>
     <p>&nbsp;</p>
     
     
    
     <table width="1025" height="138" border="0" bgcolor="252525" align="center" style="text-align:left;">
       <tr>
        <td width="18">&nbsp;</td>
        <td width="342" valign="top"><font size="3px" color="#FFFFFF">
        		</td>
        <td width="37">&nbsp;</td>
<center><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- mobile banner -->
<ins class="adsbygoogle"
     style="display:inline-block;width:320px;height:50px"
     data-ad-client="ca-pub-5233975676304559"
     data-ad-slot="9358067630"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></center>       
     

  <!--
        <td width="179" valign="top"><font size="3px" color="#FFFFFF"><br>
            กองทุนฯ<font size="2px" color="#707070">
            <br><br>
            - เกี่ยวกับกองทุน<br>
            - รายงาน &amp; สถิติ<br>
            - ข่าวประชาสัมพันธ์</td>
        <td width="218" valign="top"><font size="3px" color="#FFFFFF"><br>
            สถานประกอบการ 
            <font size="2px" color="#707070"> 
            <br><br>- กฎหมายการจ้างงานคนพิการ
            <br>- ประกาศสำหรับสถานประกอบการ
            <br>- ข่าวประชาสัมพันธ์
            <br>- ประกาศรับสมัครงาน(คนพิการ)
            <br>- ดาวน์โหลด</td>
        <td width="205" valign="top"><font size="3px" color="#FFFFFF">
        <br>คนพิการ
        <font size="2px" color="#707070">
        <br><br>- ประกาศรับสมัครงาน(คนพิการ)
        <br>- ประกาศสำหรับคนพิการ
        <br>- ข่าวคนพิการ</td>
        -->
        
       <tr>
         <td colspan="6">&nbsp;</td>
      </table>
    <p>&nbsp;</p>

</body>
</html>