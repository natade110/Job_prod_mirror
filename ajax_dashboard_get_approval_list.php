<?php

include "db_connect.php";


?>
<table border="1" style="border:1px solid #CCC; border-collapse: collapse; <?php
//if(($sess_accesslevel == 3 && !$sess_can_manage_user) || $sess_accesslevel == 8){
if($sess_accesslevel == 8){

    //yoes 20161201 --> allow ALL except 8 to see this
    echo "display: none;";
}
?>">
    <tr>
        <td colspan="5">
            <div align="center"  style=" color:#060">
                สถานประกอบการ สมัครใช้งานระบบ
            </div>
        </td>
    </tr>

    <tr>
        <td align="center">
            <div align="center">
                วันที่สมัคร
            </div>
        </td>
        <td align="center">
            <div align="center">
                ชื่อสถานประกอบการ
            </div>
        </td>
        <td align="center"><div align="center"> จังหวัด </div></td>
        <td align="center">
            <div align="center">
                username
            </div>
        </td>
        <td align="center"><div align="center"> จำนวนเอกสารยืนยันตนที่แนบมา </div></td>
    </tr>


<?php


$the_limit = 10;

if($_POST["the_limit"]){

    $the_limit = $_POST["the_limit"]*1;
}

if($_POST["user_filter_sql_approval"]){
    $user_filter_sql_approval = $_POST["user_filter_sql_approval"]*1;
}

if($_POST["zone_sql"]){
    $zone_sql = $_POST["zone_sql"]*1;
}

//echo $_POST["user_filter_sql_approval"];
//echo $_POST["zone_sql"];

//yoes 20180104 -- limit this to 0,20
$sql = "
									
                                        SELECT * FROM users a left outer join company z on a.user_meta = z.cid where 1=1 and user_enabled like '%0%' and AccessLevel like '%4%' 
                                        
                                        $user_filter_sql_approval
                                        
                                        $zone_sql
                                        
                                        order by user_id desc
                                        
                                        limit 0,$the_limit
									
									";


//echo $sql;

$submit_result = mysql_query($sql);


while($post_row = mysql_fetch_array($submit_result)){


    ?>


    <tr>
        <td >
            <?php echo formatDateThaiShort($post_row["user_created_date"]);?>
        </td>
        <td >
            <?php

            $this_company_row = getFirstRow("select * from company where cid = '".$post_row["user_meta"]."'");

            //echo formatCompanyName($this_company_row["CompanyNameThai"] , $this_company_row["CompanyTypeCode"]);
            //echo $this_company_row["CompanyNameThai"];

            echo formatCompanyName($this_company_row["CompanyNameThai"],$this_company_row["CompanyTypeCode"]);

            ?>
        </td>
        <td ><?php


            echo getFirstItem("select province_name from provinces where province_id = '".$this_company_row["Province"]."'");

            ?></td>
        <td >
            <a href="view_user.php?id=<?php echo doCleanOutput($post_row["user_id"]);?>"><?php echo ($post_row["user_name"]);?></a>
        </td>
        <td >

            <?php

            $count_self_doc = getFirstItem("
															
															
																select
																	count(*)
																from
																	files 
																where
																	file_type in (
																		
																		
																		'register_doc_1'
																		, 'register_doc_22'
																		, 'register_employee_card'
																		, 'register_company_card'
																	
																	)
																	
																and
																
																file_for = '".$post_row["user_id"]."'
															
															
															");


            $the_font_color = "red";
            $the_doc_text = "ยังส่งเอกสารยืนยันตนไม่ครบ";
            $the_doc_text_2 = "";

            if($count_self_doc >= 4){

                $the_font_color = "green";
                $the_doc_text = "ส่งเอกสารยืนยันตนครบแล้ว";
                $the_doc_text_2 = "<br><b>สถานประกอบการส่งเอกสารยืนยันตนครบแล้ว รอการอนุมัติจากเจ้าหน้าที่</b>";
            }



            ?>

            <div align="center" title="<?php echo $the_doc_text;?>">

                <font color=<?php echo $the_font_color;?>>
                    <?php

                    echo $count_self_doc ; //. $the_doc_text_2;

                    ?> / 4

                </font>

            </div>

        </td>
    </tr>



<?php }?>



<?php

if(!$_POST["do_hide_more"]){


?>
    <tr>
        <td colspan="5">
            <div align="center">
                <a href="#" onclick="doGetApprovalList(10000,1); return false;">.. ดูรายการทั้งหมด ..</a>
            </div>
        </td>
    </tr>

<?php } ?>

</table>
