<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ระบบรายงานผลการจ้างงานคนพิการ</title>
<LINK REL='StyleSheet' type='text/css' href='styles.css'>
<link rel="stylesheet" href="emx_nav_left.css" type="text/css">

<script class="jsbin" src="jquery-1.10.0.min.js"></script>
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
 
  
  

<?php

	//get this script name
	$this_page = $_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING'];
	$this_script_name = $_SERVER['SCRIPT_NAME'];

?>
<table width="1024" align="center" border="0">
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
                	
                </h1>
                <hr  style="margin-bottom:0; "/>
                </td>
			</tr>
            <tr>
            	