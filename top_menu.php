<?php 

$this_page = $_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING'];
$this_script_name = $_SERVER['SCRIPT_NAME'];

//echo $this_page;
?><aside class="left-sidebar" data-sidebarbg="skin5" style="sssdwidth: 300px;">
	<!-- Sidebar scroll-->
	<div class="scroll-sidebar">
		<!-- Sidebar navigation-->
		<nav class="sidebar-nav">
			<ul id="sidebarnav" class="p-t-30">
			
						
						
						<?php if(!isset($sess_userid)){?>
							
							<li class="sidebar-item">
								<a class="sidebar-link waves-effect waves-dark sidebar-link" href="index.php"
									aria-expanded="false">
									<i class="fas fa-angle-double-left"></i><span class="hide-menu">เข้าสู่ระบบ</span></a>
							</li>
							
							
							
							
							<li class="sidebar-item">
								<a class="sidebar-link waves-effect waves-dark sidebar-link" href="payment_calculate_hire.php"
									aria-expanded="false">
									<i class="mr-2 mdi mdi-calculator"></i><span class="hide-menu">คำนวณเงิน ปี 54-61</span></a>
							</li>
							
							
							
						
						<?php }else{ ?>
			
						<li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                aria-expanded="false">
                                <i class="mdi mdi-store"></i>
                                <span class="hide-menu">

                                    <?php if($sess_accesslevel == "6" || $sess_accesslevel == "7"){?>
                                        หน่วยงานภาครัฐ
                                    <?php }else{?>
                                        สถานประกอบการ
                                    <?php }?>

                                </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="org_list.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">รายชื่อ<?php echo $the_company_word;?></span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="org_list.php?mode=search" class="sidebar-link">
                                        <i class="mdi mdi-view-parallel"></i>
                                        <span class="hide-menu">ค้นหารายชื่อ<?php echo $the_company_word;?></span>
                                    </a>
                                </li>
								
								<?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){ ?>
                                <li class="sidebar-item">
                                    <a href="organization.php?mode=new" class="sidebar-link">
                                        <i class="mdi mdi-view-day"></i>
                                        <span class="hide-menu">เพิ่มข้อมูล<?php echo $the_company_word;?></span>
                                    </a>
                                </li>
								<?php }?>
								
                            </ul>
                        </li>
						
						
								<?php if($sess_accesslevel != 6 && $sess_accesslevel != 7){ //GOV wont see these?>
                                <li class="sidebar-item">
									<a href="payment_list.php" class="sidebar-link">
                                        <i class="mdi mdi-receipt"></i>
                                        <span class="hide-menu">ใบเสร็จรับเงินทั้งหมด</span>
                                    </a>
									
								</li>
								<?php } ?>
						
						<?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){ //exec wont see these?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                aria-expanded="false">
                                <i class="mdi mdi-email"></i>
                                <span class="hide-menu">การส่งจดหมายแจ้ง</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="org_list.php?mode=letters" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu"> การส่งจดหมายแจ้ง<?php echo $the_company_word;?></span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="import_letter.php" class="sidebar-link">
                                        <i class="mdi mdi-view-parallel"></i>
                                        <span class="hide-menu"> นำเข้าข้อมูลจดหมายแจ้งสถานประกอบการ</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="letter_list.php" class="sidebar-link">
                                        <i class="mdi mdi-view-parallel"></i>
                                        <span class="hide-menu"> จดหมายแจ้ง<?php echo $the_company_word;?>ทั้งหมด</span>
                                    </a>
                                </li>
								
								
								<?php if($sess_accesslevel !=3){?>
                                <li class="sidebar-item">
                                    <a href="org_list.php?mode=announce" class="sidebar-link">
                                        <i class="mdi mdi-view-parallel"></i>
                                        <span class="hide-menu"> เพิ่มการประกาศผ่านสื่อ</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="announce_list.php" class="sidebar-link">
                                        <i class="mdi mdi-view-parallel"></i>
                                        <span class="hide-menu"> การประกาศผ่านสื่อทั้งหมด</span>
                                    </a>
                                </li>
								<?php }?>
                            </ul>
                        </li>
						<?php }?>
						
						
						<?php if($sess_accesslevel == 1 || $sess_accesslevel == 2 ||($sess_accesslevel == 3) ||  $sess_accesslevel == 8 ){?>   
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                aria-expanded="false">
                                <i class="mdi mdi-tune-vertical"></i>
                                <span class="hide-menu">การดำเนินการตามกฎหมาย</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
							
								<?php if($sess_accesslevel != 8 ){?>
                                <li class="sidebar-item">
                                    <a href="collection_create.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">การส่งจดหมายทวงถาม</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="collection_list.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">จดหมายทวงถามทั้งหมด</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="schedulecollectionemail_list.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">ประวัติการส่งอีเมล์ทวงถาม</span>
                                    </a>
                                </li>
								<?php }?>
								
								<?php if($sess_accesslevel != 2){?> 
                                <li class="sidebar-item">
                                    <a href="notice_create.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">การแจ้งโนติส</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="holding_create.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">การแจ้งอายัด</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="proceedings_create.php?mode=1" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">การส่งพนักงานอัยการ</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="proceedings_create.php?mode=2" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">การยื่นขอรับเงินกรณีล้มละลาย </span>
                                    </a>
                                </li>
								
								<li class="sidebar-item">
                                    <a href="litigation_list.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">การดำเนินคดีตามกฎหมายทั้งหมด </span>
                                    </a>
                                </li>
								
								<?php }?>
                            </ul>
                        </li>
						<?php }?>
						
						<?php if($sess_accesslevel != 4){ ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                aria-expanded="false">
                                <i class="mdi mdi-clipboard-text"></i>
                                <span class="hide-menu">รายงาน</span>
                            </a>
							<?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){ ?>
							 <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="view_reports_gov.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">รายงานทั้งหมด </span>
                                    </a>
                                </li>
                            </ul>
							<?php }else{?>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="view_reports.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">รายงานทั้งหมด </span>
                                    </a>
                                </li>
								
								<?php if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 5){ ?>
								<li class="sidebar-item">
                                    <a href="https://misfund.dep.go.th/mis_dashboard_hire.php" target="_blank" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">Dashboard ภาพรวม </span>
                                    </a>
                                </li>
								<?php }?>
								
                            </ul>
							<?php }?>
                        </li>
						<?php }?>
						
						
						<?php if($sess_accesslevel == 1 || $sess_accesslevel == 2 || $sess_accesslevel == 3 ){ ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                aria-expanded="false">
                                <i class="mdi mdi-store"></i>
                                <span class="hide-menu">สถานประกอบการออนไลน์ (ejob)</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="org_list_approve.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">สถานประกอบการยื่นแบบออนไลน์</span>
                                    </a>
                                </li>
								 <?php if($sess_accesslevel == 1 ){ ?>
                                <li class="sidebar-item">
                                    <a href="org_list.php?mode=email" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">ระบบส่งจดหมายแจ้งสถานประกอบการออนไลน์ (Email)</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="manage_email.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">ระบบแจ้งเตือนสถานะสถานประกอบการ (Email Scheduler)</span>
                                    </a>
                                </li>
								 <?php }?>
                            </ul>
                        </li>
						<?php }?>
						
						<?php if($sess_accesslevel == 1){ ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                aria-expanded="false">
                                <i class="mdi mdi-archive"></i>
                                <span class="hide-menu">การจัดการข้อมูล</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="ktb_sync.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">สรุปยอดการชำระเงินรายวัน KTB Online</span>
                                    </a>
                                </li>
								<li class="sidebar-item">
                                    <a href="merge_company_all.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">รวมข้อมูลสถานประกอบการ</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="merge_company.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">รวมข้อมูลสถานประกอบการ (โรงเรียนเอกชน)</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="import_org.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">การนำเข้าข้อมูลสถานประกอบการ (ประกันสังคม และ
                                            กรมการจัดหางาน)</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="import_org_new.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">การนำเข้าข้อมูลสถานประกอบการ (ประกันสังคม)</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="import_org_school.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">การนำเข้าข้อมูลสถานประกอบการ (โรงเรียนเอกชน)</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="import_org_no_head.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">สถานประกอบการที่มีสาขาย่อย แต่ไม่มีสำนักงานใหญ่</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="import_skip_org.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">การนำเข้าข้อมูลหน่วยงานภาครัฐ</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="import_wage.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">จัดการค่าจ้างขั้นต่ำ</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="import_date.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">จัดการช่วงเวลาส่งเอกสารออนไลน์สถานประกอบการ</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="config_sending_letter.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">จัดการช่วงเวลาส่งจดหมายเตือนสถานประกอบการ</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="manage_zone_list.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">จัดการพื้นที่ทำงาน</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="import_list.php?mode=ktb" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">รายการนำเข้าข้อมูลจากธนาคาร</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="import_list.php?mode=nepfund" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">รายการนำเข้าข้อมูลจากระบบใบเสร็จ</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="export_list.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">รายการส่งออกข้อมูลไประบบใบเสร็จ</span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="manage_default_emails.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">ระบบจัดการ email</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
						
						
						<li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                aria-expanded="false">
                                <i class="mdi mdi-auto-fix"></i>
                                <span class="hide-menu">สำหรับผู้ดูแลระบบ</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="admin_full_usage_log.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">Log การใช้งานระบบ</span>
                                    </a>
                                </li>
								<li class="sidebar-item">
                                    <a href="admin_full_usage_log_ejob.php" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu">Log การใช้งานระบบ (E-JOB)</span>
                                    </a>
                                </li>
								
                                
                            </ul>
                        </li>
						
						<?php }?>
						
                        <!--<li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                aria-expanded="false">
                                <i class="mdi mdi-store"></i>
                                <span class="hide-menu">สถานประกอบการ</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
									<a class="sidebar-link waves-effect waves-dark sidebar-link" href="organization_public.php"
										aria-expanded="false">
										<i class="mr-2 mdi mdi-calculator"></i><span class="hide-menu">คำนวณเงิน ปี 62-64</span></a>
								</li>

                            </ul>
                        </li>-->
                        <?php if($sess_accesslevel != 6){ ?>
						
						<li class="sidebar-item">
							<a class="sidebar-link waves-effect waves-dark sidebar-link" href="payment_calculate_hire.php"
								aria-expanded="false">
								<i class="mr-2 mdi mdi-calculator"></i><span class="hide-menu">คำนวณเงิน ปี 54-61</span></a>
						</li>

                        <?php } ?>




                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="scrp_do_logout.php"
                                aria-expanded="false">
                                <i class="fas fa-angle-double-left"></i><span class="hide-menu">ออกจากระบบ</span></a>
                        </li>
						
						
			
						<?php }?>
			</ul>
		</nav>
		<!-- End Sidebar navigation -->
	</div>
	<!-- End Sidebar scroll-->
</aside>
