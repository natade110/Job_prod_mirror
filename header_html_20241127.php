<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>

	<!-- yoes added 20191127 -->
	  <link rel = "stylesheet" type = "text/css" href = "semantic.css" />
		<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
		
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		
		
			<style>

				hr.style13 {
					height: 10px;
					border: 0;
					box-shadow: 0 10px 10px -10px #8c8b8b inset;
				}

				hr.style14 {
				  border: 0;
				  height: 1px;
				  background-image: -webkit-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
				  background-image: -moz-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
				  background-image: -ms-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
				  background-image: -o-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
				  margin: 30px 0;
				}
			</style>
			
			
			
			 <script
				  src="https://code.jquery.com/jquery-3.4.1.min.js"
				  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
				  crossorigin="anonymous"></script>
			
		<!-- yoes added 20191127 -->

	   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	   
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
		   
		   
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<!-- Tell the browser to be responsive to screen width -->
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="description" content="">
			<meta name="author" content="">
			<!-- Favicon icon -->
			<link rel="icon" type="image/png" sizes="16x16" href="./favicon.ico">
			<title> ระบบรายงานผลการจ้างงานคนพิการ</title>
			<!-- Custom CSS -->
			<link rel="stylesheet" type="text/css" href="./assets/extra-libs/multicheck/multicheck.css">
			<link href="./assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
			<link href="./dist/css/style.min.css" rel="stylesheet">
			<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
			<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
			<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- vue cnd thing -->
		<script src="./vue.js"></script>
		<script src="./axios.min.js"></script>
		<script src="./axios.min.map"></script>
		<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.20.0-0/axios.min.js"></script>	-->


		
		<!--This page plugins -->
		


</head>

<?php if(!$skip_html_head){?>
<body style="background-color:#517ff0" id="main_body">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" >
	
        <header class="topbar" data-navbarbg="skin5" style="background-color: #517ff0;">
            
			
			<nav class="navbar top-navbar navbar-expand-md navbar-dark" style="background-color: #517ff0;">
                <div class="navbar-header" data-logobg="skin5" style="background-color: #517ff0;">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand" href="#"></a>
                        <!-- Logo icon -->
                        <b class="logo-icon p-l-10">
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon 
                            <img src="./assets/images/logo-icon.png" alt="homepage" class="light-logo" />-->
                           
                        </b>
                        <!--End Logo icon -->
                         <!-- Logo text -->
                        <span class="logo-text">
                             <!-- dark Logo text -->
                             <img src="./logo101.png" alt="homepage" class="light-logo" height="45" />
                             <!-- <img src="./logohire51.png" alt="homepage" class="light-logo" height="45" /> -->
                             <!-- <span class="logo-text" style="color: #4267b2;"> HAII </span>-->
                        </span>
                        <!-- Logo icon -->
                        <!-- <b class="logo-icon"> -->
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <!-- <img src="./assets/images/logo-text.png" alt="homepage" class="light-logo" /> -->
                            
                        <!-- </b> -->
                        <!--End Logo icon -->
                    
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContents" data-navbarbg="skin5" style="background-color: #517ff0;" >
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-left mr-auto" style="background-color: #517ff0;">
                        <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar">
						<i class="mdi mdi-menu font-24" ></i></a></li>
                        <!-- ============================================================== -->
                        <!-- create new -->
                        <!-- ============================================================== -->
                        
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                       
                       
                        <div class="navbar-header" data-logobg="skin5" style="background-color: #517ff0; width: 100% ">
							<!-- This is for the sidebar toggle which is visible on mobile only -->
							<a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
							<!-- ============================================================== -->
							<!-- Logo -->
							<!-- ============================================================== -->
							<a class="navbar-brand" href="index.php">
								<!-- Logo icon -->
								<b class="logo-icon p-l-10">
									<!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
									<!-- Dark Logo icon 
									<img src="./assets/images/logo-icon.png" alt="homepage" class="light-logo" />-->

								</b>
								<!--End Logo icon -->
								 <!-- Logo text -->
								 <span class="logo-text" style="color: #fff" >  
								 
								 
									
									 <?php if(isset($sess_userid)){ ?>
										  
										  
										<a href="index.php" style="color: #fff; font-size: 14px;">หน้าแรก |</a>
										
										<a href="gjob.dep.go.th" style="color: #fff; font-size: 14px;">ระบบรายงานผลหน่วยงานภาครัฐ |</a>
										
										
										
										<?php if($sess_accesslevel != 4){ //company won;t see these?>
										  <a href="org_list.php?mode=search" style="color: #fff; font-size: 14px;">ค้นหา<?php echo $the_company_word;?> | </a>

											  
										  <?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){ ?>
											<a href="view_reports_gov.php" style="color: #fff; font-size: 14px;">รายงาน |</a>
										  <?php }else{ ?>
											 <a href="view_reports.php" style="color: #fff; font-size: 14px;">รายงาน |</a>
										  <?php }?>
										  
									  <?php } ?>
									  
									  
									  <?php
									  
										//echo "can manage user?". $sess_can_manage_user;
									  
									   if($sess_accesslevel != 4){ //company won;t see these?>
									   
										  <?php
											
											//yoes 20190221 -> allow ส่วนกลาง to see this

											if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_can_manage_user){ //only admin will see this // yoes 20141007 -- add ability for พมจ to edit users //yoes 20161201 - pmj wont see this ||  $sess_can_manage_user?>
											<a href="user_list.php"  style="color: #fff; font-size: 14px;">ผู้ใช้งานระบบ |</a>
										  <?php }?>
									  
									  <?php } ?>
									  
									  
										<a href="view_user.php?id=<?php echo $sess_userid; ?>"  style="color: #fff; font-size: 14px;">เปลี่ยนรหัสผ่าน |</a>
										
										 <a href="scrp_do_logout.php"  style="color: #fff; font-size: 14px;">ออกจากระบบ</a>
									  
									 
									 <?php }else{?>
									 
									 
										ระบบรายงานผลการจ้างงานคนพิการ (สำหรับเจ้าหน้าที่ฯ)
										
										
									 <?php }?>
								 
								 
								 </span>
								<!-- Logo icon -->
								<!-- <b class="logo-icon"> -->
									<!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
									<!-- Dark Logo icon -->
									<!-- <img src="./assets/images/logo-text.png" alt="homepage" class="light-logo" /> -->

								<!-- </b> -->
								<!--End Logo icon -->
							</a>
							
							
							
							
							
							<!-- ============================================================== -->
							<!-- End Logo -->
							<!-- ============================================================== -->
							<!-- ============================================================== -->
							<!-- Toggle which is visible on mobile only -->
							<!-- ============================================================== -->
							<a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
						</div>


                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
		<?php 

			$this_page = $_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING'];
			$this_script_name = $_SERVER['SCRIPT_NAME'];

			//echo $this_page;
			
			include "top_menu.php";

		?>
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper" >
           
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
			
			 <div class="page-content container-fluid" style="background-color: #fff; overflow: scroll;" align=center>
			 
				<table width=100%>
					<tr>
					
<?php } // ends !$skip_html_head?>					