<?php

	//echo "hello";exit();
	
	if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
		$location = 'https://job.dep.go.th';
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: ' . $location);
		exit;
	}
	
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") {
		//$location = 'http://job.dep.go.th';
		//header('HTTP/1.1 301 Moved Permanently');
		//header('Location: ' . $location);
		//exit;
	}
	
	
	
	$time_start = microtime(true);
	//header("location: ./law_system"); exit();
	
	include "db_connect.php";
	
	session_start();
	if(isset($_SESSION['sess_userid'])){
		$sess_userid = $_SESSION['sess_userid'];
	}
	if(isset($sess_userid) && $sess_accesslevel != 4 && $sess_accesslevel != 18){
		//header("location: org_list.php");
		header("location: dashboard.php");
	}
	
	if( $sess_accesslevel == 4 || $sess_accesslevel == 18){		
		header("Location: org_list.php");
	}

?>
<?php include "header_html.php";?>
               
             
               
             
             <td valign="top" >
			 
					
					<table align="center" >
						<tr>
							<td>
								<div class="auth-box" style="maddrgin-bottom: 90%; width: 500px; align: center;"  >
										<div id="loginform">
											<div class="logo">
												<span class="db"><img src="./dep_logo.jpg" alt="logo" /></span>
												<br><br>
												<h2 class="font-medium mb-3"><b>Login <br>สำหรับเจ้าหน้าที่ระบบรายงานผลการจ้างงานคนพิการ</b></h2>
											</div>
											<!-- Form -->
											<div class="row">
												<div class="col-12">
													<form class="form-horizontal mt-3" id="loginform" action="scrp_do_login.php" method="post">
														<div class="input-group mb-3">
															<div class="input-group-prepend">
																<span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
															</div>
															<input name="user_name" type="text" class="form-control form-control-lg" placeholder="ชื่อผู้ใช้งาน" aria-label="Username" aria-describedby="basic-addon1">
														</div>
														<div class="input-group mb-3">
															<div class="input-group-prepend">
																<span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
															</div>
															<input name="password" type="password" class="form-control form-control-lg" placeholder="รหัสผ่าน" aria-label="Password" aria-describedby="basic-addon1">
														</div>
														
														
														
														<div class="form-group text-center">
															<div class="col-xs-12 pb-3">
																<?php if($_GET[mode] == "error_pass"){ ?>
																	<font color=red>** ชื่อผู้ใช้งาน หรือ รหัสผ่านไม่ถูกต้อง</font><br><br>
																<?php }?>
																<?php if($_GET[mode] == "pending"){ ?>
																	<font color=red>** your account are pending approval.</font><br><br>
																<?php }?>
																<button class="btn btn-block btn-lg btn-info" type="submit">เข้าสู่ระบบ</button>
																<br>
																<a href="view_register_password.php" id="to-recover" class="text-dark " style="color: blue !important;"><i class="fa fa-lock mr-1"></i> ลืมรหัสผ่าน คลิกที่นี่</a>
															</div>
														</div>
														
														<div class="form-group mb-0 mt-2">
														
															<div class="col-sm-12 text-center">
																
																
																
																<hr>
																ต้องการเข้าใช้งานระบบรายงานผลการจ้างงาน<b><u>สำหรับสถานประกอบการ</b></u> <u><a href="http://ejob.dep.go.th/ejob/">กรุณาคลิกที่นี่</a></u>
															</div>
														</div>
														
														<hr>
														
														
													</form>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
						</table>
                             
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
<?php //yoes 20170213 -- set faux cron here ?>
<script>
	//$.get("ajax_do_snapshot.php");
	//$.get("ajax_meta_cron.php");
</script>

</body>
</html>
<?php $time_end = microtime(true); 
$execution_time = ($time_end - $time_start)/60;
		//echo $execution_time; exit(); ?>