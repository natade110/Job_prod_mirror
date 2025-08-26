<?php 
	
	include "db_connect.php";
	header('Content-Type: text/html; charset=utf-8');

	if(!$_GET[mode]){		
		exit();	
	}
		
	$mode = $_GET[mode];
		
	if($mode == "has_lawfulness"){
		
		$sql = "select * from company_temp_school where is_in_case = 1 and has_lawfulness = 1";
		$title = "เข้าข่ายต้องปฏิบัติตามกฎหมาย <u>แต่มีการปฏิบัติตามกฎหมายแล้ว</u> และจะไม่ถูกนำเข้าระบบ";
		
	}elseif($mode == "the_new"){
		
		$sql = "select * from company_temp_school where is_in_case = 1 and no_main_branch = 0 and is_old_org = 0 and has_lawfulness = 0";
		$title = "ที่เข้าข่ายต้องปฏิบัติตามกฎหมาย มีสำนักงานใหญ่ ยังไม่มีการปฏิบัติตามกฎหมาย <br /><font color='green'>เป็นโรงเรียนใหม่ที่ไม่เคยมีในระบบ</font> และจะถูกนำเข้าระบบ";
		
	}elseif($mode == "existed"){
		
		$sql = "select * from company_temp_school where is_in_case = 1 and no_main_branch = 0 and is_old_org > 0 and has_lawfulness = 0";
		$title = "เข้าข่ายต้องปฏิบัติตามกฎหมาย มีสำนักงานใหญ่ ยังไม่มีการปฏิบัติตามกฎหมาย <font color='red'>เป็นโรงเรียนที่เคยมีในระบบ</font> และจะถูกนำเข้าระบบ";
		
	}elseif($mode == "the_new_no_branch"){
		
		$sql = "select * from company_temp_school where is_in_case = 1 and no_main_branch = 1 and is_old_org = 0 and has_lawfulness = 0";
		$title = "เข้าข่ายต้องปฏิบัติตามกฎหมาย ไม่มีสำนักงานใหญ่ ยังไม่มีการปฏิบัติตามกฎหมาย <font color=green>เป็นโรงเรียนใหม่ที่ไม่เคยมีในระบบ</font> และจะถูกนำเข้าระบบ";
		
	}elseif($mode == "existed_no_branch"){
		
		$sql = "select * from company_temp_school where is_in_case = 1 and no_main_branch = 1 and is_old_org > 0 and has_lawfulness = 0";
		$title = "เข้าข่ายต้องปฏิบัติตามกฎหมาย ไม่มีสำนักงานใหญ่ ยังไม่มีการปฏิบัติตามกฎหมาย <font color=red>เป็นโรงเรียนที่เคยมีในระบบ</font> และจะถูกนำเข้าระบบ";
		
	}else{
		
		exit();	
		
	}
	
	
	$company_result = mysql_query($sql);

?>
	
    <div align="left">
    	<?php echo $title;?>
    </div>
    
    <table border="1">
       <tr>
        	<td colspan="2" bgcolor="#FFE8E9">
            <div align="center">
            ข้อมูลจากไฟล์
            </div>
            </td>
            <td colspan="4" bgcolor="#E7FFD7">
            <div align="center">
            รวมข้อมูลไปยัง...
            </div>
            </td>
        </tr>
    	<tr>
        	<?php if(1==0){?>
                <td>
                <div align="center">
                เลขที่บัญชีนายจ้าง
                </div>
                </td>
                <td>
                <div align="center">
                เลขที่สาขา
                </div>
                </td>
            <?php }?>
            <td>
            <div align="center">
            รหัสโรงเรียน
            </div>
            </td>
            <td>
            <div align="center">
            ชื่อโรงเรียน
            </div>
            </td>
            <td>
            <div align="center">
            เลขที่บัญชีนายจ้าง
            </div>
            </td>
            <td>
            <div align="center">
            เลขที่สาขา
            </div>
            </td>
            <td>
            <div align="center">
            รหัสโรงเรียน
            </div>
            </td>
            <td>
            <div align="center">
            ชื่อสถานประกอบการ/โรงเรียน
            </div>
            </td>
        </tr>
        
    

<?php	
	while($company_row = mysql_fetch_array($company_result)){
	
		
		//echo "ข้อมูลในไฟล์: " . $company_row[CompanyNameThai];	
		
		//merge to what
		$merge_to_row = getFirstRow("select * from company where cid = '".$company_row[is_old_org]."'");
		
		
	?>

		<tr>
        
	        <?php if(1==0){?>
                <td>
                <?php 
                    echo $company_row[CompanyCode];
                ?>
                </td>
                <td>
                <?php echo $company_row[BranchCode];?>
                </td>
            <?php }?>
            
            <td>
            <?php echo $company_row[school_code];?>
            </td>
            <td>
            <?php echo $company_row[CompanyNameThai];?>
            </td>
            
            
            
            <td>
            <?php echo $merge_to_row[CompanyCode];?>
            </td>
            <td>
            <?php echo $merge_to_row[BranchCode];?>
            </td>
            <td>
            <?php echo getFirstItem("select meta_value from company_meta where meta_cid = '$company_row[is_old_org]' and meta_for = 'school_code'");?>
            </td>
            <td>
            <a href="organization.php?id=<?php echo $merge_to_row["CID"];?>" target="_blank">
            <?php echo $merge_to_row[CompanyNameThai];?>
            </a>
            </td>
        </tr>

<?php		
	}

?>

</table>