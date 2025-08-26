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
	
	if(1==1){  
	?>
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">สำหรับสถานประกอบการ</div>
    <div id="sectionLinks">
    <a href="view_register.php"  style="margin-left:10px; font-weight: normal;"> 
	  		สมัครเข้าใช้งาน
      </a>
      
      <a href="submit_forms.php" style="margin-left:10px; font-weight: normal;"> 
	  		ส่งเอกสารการปฏิบัติตามกฏหมาย
      </a>
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
    <div id="sectionLinks"> 
    
    <?php if($sess_accesslevel != 4){ //company wont see these?>
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699"><?php echo $the_company_word;?></div>
    <a href="org_list.php" style=" margin-left:10px; font-weight: normal; <?php if(
										(
											strpos($this_script_name, "org_list.php") 
											&& !strpos($this_page, "mode=search")
											&& !strpos($this_page, "mode=letters")
											&& !strpos($this_page, "mode=payment")
											&& !strpos($this_page, "mode=announce")
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
    
    <?php if($sess_accesslevel != 5){ //exec wont see these?>
    <a href="organization.php?mode=new" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_page, "organization.php?mode=new")){echo $highlight_style;}?>">เพิ่มข้อมูล<?php echo $the_company_word;?></a>
    <?php }?>
    
    
    
     
    
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">การดำเนินการตามกฎหมาย</div>
    
    
    <?php if($sess_accesslevel != 5){ //exec wont see these?>
    <a href="org_list.php?mode=letters" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_page, "mode=letters") && !strpos($this_page, "type=hold")){echo $highlight_style;}?>">การส่งจดหมายแจ้ง<?php echo $the_company_word;?></a>
    <?php }?>
    
    <a href="letter_list.php" style="margin-left:10px;font-weight: normal;<?php if((strpos($this_script_name, "letter_list.php") || strpos($this_script_name, "view_letter.php")) && !strpos($this_page, "type=hold")){echo $highlight_style;}?>">จดหมายแจ้ง<?php echo $the_company_word;?>ทั้งหมด</a>
    
     <?php if($sess_accesslevel != 5 && $sess_accesslevel != 6 && $sess_accesslevel != 7){ //exec wont see these?>
    <a href="org_list.php?mode=letters&type=hold" style="margin-left:10px;font-weight: normal; <?php if(strpos($this_script_name, "org_list.php") && strpos($this_page, "type=hold")){echo $highlight_style;}?>">การแจ้งอายัด</a>
    <?php }?>
    
    
     <?php if($sess_accesslevel != 6 && $sess_accesslevel != 7){ //GOV wont see these?>
     <a href="holding_list.php" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_script_name, "holding_list.php") || (strpos($this_script_name, "view_letter.php")&& strpos($this_page, "type=hold"))){echo $highlight_style;}?>">การแจ้งอายัดทั้งหมด</a>
    
    <a href="payment_list.php" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_script_name, "payment_list.php")){echo $highlight_style;}?>">ใบเสร็จรับเงินทั้งหมด</a>
    
    <?php }?>
    
    
    
    
		<?php if($sess_accesslevel !=3){?>
        
        <?php if($sess_accesslevel != 5){ //exec wont see these?>
        <a href="org_list.php?mode=announce" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_page, "mode=announce")){echo $highlight_style;}?>">เพิ่มการประกาศผ่านสื่อ</a>
        <?php }?>
        <a href="announce_list.php" style="margin-left:10px;font-weight: normal;<?php if(strpos($this_script_name, "announce_list.php") || strpos($this_script_name, "view_announce.php")){echo $highlight_style;}?>">การประกาศผ่านสื่อทั้งหมด</a>
        <?php } ?>
        
        
    
    <?php }?>
	
	<?php if($sess_accesslevel == 4 ){ //company can only see these ?>
     <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">ดาวน์โหลดเอกสาร</div>
    
    
		<?php 
            //this vars comes from header_html
            if($this_logged_in_province == "1"){
        
        ?>
        <a href="form_bangkok.pdf" style="margin-left:10px;font-weight: normal;" target="_blank">แบบรายงานการปฏิบัติตามกฏหมาย</a>
        
        
        <?php }else{ ?>
        
        <a href="form_province.pdf" style="margin-left:10px;font-weight: normal;" target="_blank">แบบรายงานการปฏิบัติตามกฏหมาย</a>
        
    
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
    
    
    
     <?php if($sess_accesslevel == 1 ){ ?>
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">สถานประกอบการ</div>
    	<a href="register_list.php" style="margin-left:10px;font-weight: normal;">ผู้ใช้งานสถานประกอบการ</a>
    <?php }?>
    
    
    <?php if($sess_accesslevel == 1){ ?>
    <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">การจัดการข้อมูล</div>
    	<!--<a href="import_org.php" style="margin-left:10px;font-weight: normal;">ข้อมูล<?php echo $the_company_word;?>/ค่าจ้างขั้นต่ำ</a>-->
        
        <a href="import_org.php" style="margin-left:10px;font-weight: normal;">การนำเข้าข้อมูลสถานประกอบการ (ประกันสังคม และ กรมการจัดหางาน)</a>
        <a href="import_skip_org.php" style="margin-left:10px;font-weight: normal;">การนำเข้าข้อมูลหน่วยงานภาครัฐ</a>
        <a href="import_wage.php" style="margin-left:10px;font-weight: normal;">จัดการค่าจ้างขั้นต่ำ</a>
        <a href="import_date.php" style="margin-left:10px;font-weight: normal;">จัดการช่วงเวลาส่งเอกสารออนไลน์ของสถานประกอบการ</a>
    <?php }?>
    
    
     <div style="font-weight: bold; padding:5px 5px 5px 10px; color:#006699">สถานประกอบการ</div>
    	<a href="payment_calculate_hire.php" style="margin-left:10px;font-weight: normal;">คำนวณเงิน</a>
    
    
    </div> 
</div>
<?php } ?>

