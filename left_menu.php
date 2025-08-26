<?php 

$this_page = $_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING'];
$this_script_name = $_SERVER['SCRIPT_NAME'];

//echo $this_page;

if(!isset($sess_userid)){ 

?>
<div id="pageNav"> 
    <div id="sectionLinks"> 
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">เข้าสู่ระบบ</div>
    <a href="index.php" style="margin-left:10px; font-weight: normal;">login เข้าสู่ระบบ</a>
    </div> 
    
    <div id="sectionLinks"> 
    
    
    
    <?php
	
	
		$today_date = date("Ymd");
		//see if this is a time for submit a document
		$submit_date_from = "".
							getFirstItem("select var_value from vars where var_name = 'submit_date_from_year'")
							.
							addLeadingZeros(getFirstItem("select var_value from vars where var_name = 'submit_date_from_month'"),2)
							.
							addLeadingZeros(getFirstItem("select var_value from vars where var_name = 'submit_date_from_day'"),2)
							."";
			
	
		$submit_date_to = "".
					getFirstItem("select var_value from vars where var_name = 'submit_date_to_year'")
					.
					addLeadingZeros(getFirstItem("select var_value from vars where var_name = 'submit_date_to_month'"),2)
					.
					addLeadingZeros(getFirstItem("select var_value from vars where var_name = 'submit_date_to_day'"),2)
					."";				
					
		//
		//echo $today_date."--";
		//echo $submit_date_from . "--";	
		//echo $submit_date_to . "--";			
	
	?>
    
    <?php //if( $today_date >= $submit_date_from && $today_date <= $submit_date_to){
	
	//yoes 20160106 - close this for now
	if(1==0){  
	?>
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">สำหรับสถานประกอบการ</div>
    <div id="sectionLinks">
    <a href="view_register_term.php"  style="margin-left:10px; font-weight: normal;"> 
	  		สมัครเข้าใช้งาน
      </a>
      
     <!-- <a href="submit_forms.php" style="margin-left:10px; font-weight: normal;"> 
	  		ส่งเอกสารการปฏิบัติตามกฏหมาย
      </a>-->
    </div>
    
    <?php }?>
    
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">สถานประกอบการ</div>
    <div id="sectionLinks">
    	<a href="payment_calculate_hire.php" style="margin-left:10px;font-weight: normal;">คำนวณเงิน</a>    
	</div>

<?php }else{ 

	$highlight_style = "background-image: none; background-color:#CCFFCC";

?>
<div id="pageNav"> 
   
    
    <?php if($sess_accesslevel != 4){ //company wont see these?>


	<?php if($sess_accesslevel == 1 && 1==0){ //yoes 20151118 -- disabled this for now?>
    
    <?php 
	
	if(date("m") >= 9){
		$the_end_year = date("Y")+1; //new year at month 9
	}else{
		$the_end_year = date("Y");
	}
	?>
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">ภาพรวมระบบปี <?php echo $the_end_year+543;?></div>
    
    <div style="padding: 0 0 0 10px; font-size: 11px;">
    ปฏิบัติตามกฏหมาย:<br />
    <a href="org_list.php?LawfulFlag=1&ddl_year=<?php echo $the_end_year?>">
    <strong style="color:#060;"><?php 
	
	
		//same sql as per org list page
		echo number_format(getFirstItem("
			
			SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '2016' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and y.LawfulStatus = '1'
		
		
		"),0);
	
	
	?>/<?php 
	
	
		//same sql as per org list page
		echo number_format(getFirstItem("
			
			SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '2016' where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1
		
		
		
		"),0);
	
	
	?> แห่ง</strong></a>
    <br />
    ผู้ใช้งานระบบรออนุมัติใช้งาน: <a href="user_list.php?user_enabled=0"><strong style="color:#900;"><?php 
	
	
	
		echo getFirstItem("
		
			SELECT count(*) FROM users a left outer join company b on a.user_meta = b.cid where user_enabled like '%0%' 
		
		");
	
	
	?> คน</strong></a>
    <br />
    ข้อมูลจากสถานประกอบการ<br />ยังไม่บันทึกเข้าระบบ: <a href="org_list_approve.php?lawful_submitted=1&ddl_year=<?php echo $the_end_year?>"><strong style="color:#900;"><?php 
	
	
	
		echo getFirstItem("
		
			SELECT count(*) FROM company z LEFT outer JOIN companytype b ON z.CompanyTypeCode = b.CompanyTypeCode LEFT outer JOIN provinces c ON z.province = c.province_id JOIN lawfulness y ON z.CID = y.CID and y.Year = '2016' left join lawfulness_company xxx on z.CID = xxx.CID where 1=1 and 1=1 and z.CompanyTypeCode < 200 and BranchCode < 1 and xxx.Year = '2016' and (xxx.lawful_submitted = '1')
		
		");
	
	
	?> แห่ง</strong></a>
    
    </div>
    
    
    <?php }?>
    
    
    
     <div id="sectionLinks"> 
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699"><?php echo $the_company_word;?></div>
    <a href="org_list.php" style=" margin-left:10px; font-weight: normal; <?php if(
										(
											strpos($this_script_name, "org_list.php") 
											&& !strpos($this_page, "mode=search")
											&& !strpos($this_page, "mode=letters")
											&& !strpos($this_page, "mode=payment")
											&& !strpos($this_page, "mode=announce")
											
											
											//NEW AS OF 20140215
											&& !strpos($this_page, "mode=email")
											
											
										)
										|| (
											strpos($this_script_name, "organization.php")
											&& !strpos($this_page, "mode=new")
										)
										){echo $highlight_style;}
										?>" >
                                        รายชื่อ<?php echo $the_company_word;?>
                                        </a>
    <a href="org_list.php?mode=search" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_page, "mode=search")){echo $highlight_style;}?>">ค้นหารายชื่อ<?php echo $the_company_word;?></a>
    
<?php if($sess_accesslevel != 5 && $sess_accesslevel != 8){ //exec wont see these?>
    <a href="organization.php?mode=new" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_page, "organization.php?mode=new")){echo $highlight_style;}?>">เพิ่มข้อมูล<?php echo $the_company_word;?></a>

    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">การส่งจดหมายแจ้ง</div>
    <a href="org_list.php?mode=letters" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_page, "mode=letters") && !strpos($this_page, "type=hold")){echo $highlight_style;}?>">การส่งจดหมายแจ้ง<?php echo $the_company_word;?></a>

    <a href="import_letter.php" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_page, "import_letter")){echo $highlight_style;}?>">นำเข้าข้อมูลจดหมายแจ้งสถานประกอบการ</a>

    <a href="letter_list.php" style="margin-left:10px;font-weight: normal;<?php if((strpos($this_script_name, "letter_list.php") || strpos($this_script_name, "view_letter.php")) && !strpos($this_page, "type=hold")){echo $highlight_style;}?>">จดหมายแจ้ง<?php echo $the_company_word;?>ทั้งหมด</a>
    
    <?php if($sess_accesslevel != 6 && $sess_accesslevel != 7){ //GOV wont see these?>
    <a href="payment_list.php" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_script_name, "payment_list.php")){echo $highlight_style;}?>">ใบเสร็จรับเงินทั้งหมด</a>
    <?php }?>
    
    <?php if($sess_accesslevel !=3){?>
        <a href="org_list.php?mode=announce" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_page, "mode=announce")){echo $highlight_style;}?>">เพิ่มการประกาศผ่านสื่อ</a>
        <a href="announce_list.php" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_script_name, "announce_list.php") || strpos($this_script_name, "view_announce.php")){echo $highlight_style;}?>">การประกาศผ่านสื่อทั้งหมด</a>
    <?php } 
    }
    if($sess_accesslevel == 1 || $sess_accesslevel == 2 ||($sess_accesslevel == 3) ||  $sess_accesslevel == 8 ){?>   
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">การดำเนินการตามกฎหมาย</div>
    
    <?php if($sess_accesslevel != 8 ){?>
    <a href="collection_create.php" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_page, "collection_create.php")){echo $highlight_style;}?>">การส่งจดหมายทวงถาม</a>
    <a href="collection_list.php" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_script_name, "collection_list.php")){echo $highlight_style;}?>">จดหมายทวงถามทั้งหมด</a>
    <a href="schedulecollectionemail_list.php" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_script_name, "schedulecollectionemail_list.php")){echo $highlight_style;}?>">ประวัติการส่งอีเมลล์ทวงถาม</a>    
    <?php }?> 
    
    <?php if($sess_accesslevel != 2){?>     
    <a href="notice_create.php" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_script_name,"notice_create.php")){echo $highlight_style;}?>">การแจ้งโนติส</a>
    <a href="holding_create.php" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_script_name, "holding_create.php")){echo $highlight_style;}?>">การแจ้งอายัด</a>
    <a href="proceedings_create.php" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_script_name, "proceedings_create.php")){echo $highlight_style;}?>">การส่งพนักงานอัยการ/ยื่นขอรับเงินกรณีล้มละลาย</a>
    <a href="litigation_list.php" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_script_name, "litigation_list.php") || (strpos($this_script_name, "view_letter.php")&& strpos($this_page, "type=hold"))){echo $highlight_style;}?>">การดำเนินคดีตามกฎหมายทั้งหมด</a>
	<?php }?>
   <?php 
    }
}

    if($sess_accesslevel == 4 && 1==0 ){ //company can only see these ?>
     <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">ดาวน์โหลดเอกสาร</div>
    
    
		<?php 
            //this vars comes from header_html
            if($this_logged_in_province == "1"){
        
        ?>
        <a href="form_bangkok_2015.pdf" style="margin-left:10px;font-weight: normal;" target="_blank">แบบรายงานการปฏิบัติตามกฏหมาย</a>
        
        
        <?php }else{ ?>
        
        <a href="form_province_2015.pdf" style="margin-left:10px;font-weight: normal;" target="_blank">แบบรายงานการปฏิบัติตามกฏหมาย</a>
        
    
        <?php } ?>   
   <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699"><a href="view_law_doc.php" style="padding:0px; background-image: none; border: none; color: #006699;" >กฎหมายและเอกสารที่เกี่ยวข้อง</a></div>
   	
   
   <?php } ?>
    
    <?php if($sess_accesslevel != 4){ ?>
    
    	 <?php if($sess_accesslevel == 6 || $sess_accesslevel == 7){ ?>
            <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">รายงาน</div>
                <a href="view_reports_gov.php" style="margin-left:10px;font-weight: normal;">รายงานทั้งหมด</a>
		<?php }else{ ?>
      	  <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">รายงาน</div>
                <a href="view_reports.php" style="margin-left:10px;font-weight: normal;">รายงานทั้งหมด</a>
        
        <?php }?>

    <?php }?>
    
    
    
     <?php if($sess_accesslevel == 1 || $sess_accesslevel == 3 ){ ?>
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">สถานประกอบการ</div>
    
    	
    	<a href="org_list_approve.php" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_script_name, "org_list_approve.php")){echo $highlight_style;}?>">สถานประกอบการยื่นแบบออนไลน์</a>
        
        
    
    	<!--<a href="register_list.php" style="margin-left:10px;font-weight: normal;">ผู้ใช้งานสถานประกอบการ</a>-->
        
        
        <?php if($sess_accesslevel == 1 ){ ?>
			<a href="org_list.php?mode=email" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_page, "mode=email")){echo $highlight_style;}?>">ระบบส่งจดหมายแจ้งสถานประกอบการออนไลน์ (Email) </a>
            
            
            <a href="manage_email.php" style="margin-left:10px;font-weight: normal;">ระบบแจ้งเตือนสถานะสถานประกอบการ (Email Scheduler) </a>
            
		<?php } ?>
        
    <?php }?>
    
    
     <?php if($sess_accesslevel == 2){ //yoes 20160622 ?>
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">การจัดการข้อมูล</div>
    	<!--<a href="import_org.php" style="margin-left:10px;font-weight: normal;">ข้อมูล<?php echo $the_company_word;?>/ค่าจ้างขั้นต่ำ</a>-->
        
        <a href="merge_company.php" style="margin-left:10px;font-weight: normal;">รวมข้อมูลสถานประกอบการ (โรงเรียนเอกชน)</a>
        
    
     <?php }?>
    
    <?php if($sess_accesslevel == 1){ ?>
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">การจัดการข้อมูล</div>
    	<!--<a href="import_org.php" style="margin-left:10px;font-weight: normal;">ข้อมูล<?php echo $the_company_word;?>/ค่าจ้างขั้นต่ำ</a>-->
        
        <a href="ktb_sync.php" style="margin-left:10px;font-weight: normal;">สรุปยอดการชำระเงินรายวัน KTB Online</a>
        
        <a href="merge_company.php" style="margin-left:10px;font-weight: normal;">รวมข้อมูลสถานประกอบการ (โรงเรียนเอกชน)</a>
        
        <a href="import_org.php" style="margin-left:10px;font-weight: normal;">การนำเข้าข้อมูลสถานประกอบการ (ประกันสังคม และ กรมการจัดหางาน)</a>
        <a href="import_org_new.php" style="margin-left:10px;font-weight: normal;">การนำเข้าข้อมูลสถานประกอบการ (ประกันสังคม)</a>
        
        <a href="import_org_school.php" style="margin-left:10px;font-weight: normal;">การนำเข้าข้อมูลสถานประกอบการ (โรงเรียนเอกชน)</a>
        
        <a href="import_org_no_head.php" style="margin-left:10px;font-weight: normal;">สถานประกอบการ ที่มีสาขาย่อย แต่ไม่มีสำนักงานใหญ่</a>
        
        <a href="import_skip_org.php" style="margin-left:10px;font-weight: normal;">การนำเข้าข้อมูลหน่วยงานภาครัฐ</a>
        <a href="import_wage.php" style="margin-left:10px;font-weight: normal;">จัดการค่าจ้างขั้นต่ำ</a>
        <a href="import_date.php" style="margin-left:10px;font-weight: normal;">จัดการช่วงเวลาส่งเอกสารออนไลน์ของสถานประกอบการ</a>
        <a href="config_sending_letter.php" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_page, "config_sending_letter.php")){echo $highlight_style;}?>">จัดการช่วงเวลาส่งจดหมายเตือนสถานประกอบการ</a>
        
        <a href="manage_zone_list.php" style="margin-left:10px;font-weight: normal;">จัดการพื้นที่การทำงาน</a>
        <a href="import_list.php?mode=ktb" style="margin-left:10px;font-weight: normal;">รายการนำเข้าข้อมูลจากธนาคาร</a>
        <a href="import_list.php?mode=nepfund" style="margin-left:10px;font-weight: normal;">รายการนำเข้าข้อมูลจากระบบใบเสร็จ</a>
        <a href="export_list.php" style="margin-left:10px;font-weight: normal;">รายการส่งออกข้อมูลไประบบใบเสร็จ</a>


        <a href="manage_default_emails.php" style="margin-left:10px;font-weight: normal;">ระบบจัดการ email</a>
    <?php }?>
    
    
    <?php if($sess_accesslevel == 2){ ?>
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">การจัดการข้อมูล</div>
    	
        <a href="import_org_no_head.php" style="margin-left:10px;font-weight: normal;">สถานประกอบการ ที่มีสาขาย่อย แต่ไม่มีสำนักงานใหญ่</a>
        
    <?php }?>
    
    
     <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">สถานประกอบการ</div>
    	<a href="payment_calculate_hire.php" style="margin-left:10px;font-weight: normal;">คำนวณเงิน</a>
    
    
    </div> 
</div>
<?php } ?>

