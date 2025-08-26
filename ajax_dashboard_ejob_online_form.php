<?php

include "db_connect.php";

$the_end_year = $_POST["the_end_year"]*1;

$the_limit = 10;

//echo "--".$_POST["the_limit"]."--";

if($_POST["the_limit"]){

    $the_limit = $_POST["the_limit"]*1;
}
?>
<table border="1" style="border:1px solid #CCC; border-collapse: collapse;<?php
if($sess_accesslevel == 8){
    echo "display: none;";
}
?>">
    <tr>
        <td colspan="4">
            <div align="center" style=" color:#060">
                สถานประกอบการ ยื่นแบบออนไลน์
            </div>
        </td>
    </tr>
    <tr>
        <td align="center">
            <div align="center">
                วันที่
            </div>
        </td>
        <td align="center">
            <div align="center">
                ชื่อสถานประกอบการ
            </div>
        </td>
        <td align="center">

            <div align="center">
                จังหวัด
            </div>

        </td>
        <td align="center">
            <div align="center">
                สถานะ
            </div>
        </td>
    </tr>

    <?php

    $sql = "
									
									
									SELECT 
										z.CID 
										, Province 
										, CompanyCode 
										, CompanyTypeName
										, z.CompanyTypeCode 
										, CompanyNameThai 
										, province_name 
										, LawfulFlag 
										, y.LawfulStatus as lawfulness_status 
										, y.Employees as lawful_employees 
									FROM 
										company z 
											LEFT outer JOIN companytype b 
												ON z.CompanyTypeCode = b.CompanyTypeCode 
											LEFT outer JOIN provinces c 
												ON z.province = c.province_id 
											JOIN lawfulness y 
												ON z.CID = y.CID and y.Year = '$the_end_year' 
												
											left join lawfulness_company xxx 
												on z.CID = xxx.CID 
												
									where 
										1=1 
										and z.CompanyTypeCode < 200 
										and BranchCode < 1 
										and xxx.Year = '$the_end_year' 
										and (xxx.lawful_submitted = '1') 
									
										$province_sql
									
									
									order by lawful_submitted_on asc
									
									limit 0,$the_limit
									
									";

    $submit_result = mysql_query($sql);

    $cur_year = $the_end_year;

    while($post_row = mysql_fetch_array($submit_result)){

        ?>

        <tr>
            <td>

                <div align="center">

                    <?php echo


                    formatDateThaiShort(
                        getFirstItem("
                                                        select 
                                                            lawful_submitted_on 
                                                        from 
                                                            lawfulness_company 
                                                        where 
                                                            CID = '".$post_row["CID"]."' 
                                                            and 
                                                            Year = '$the_end_year'")
                    );

                    ?>
                </div>

            </td>
            <td>
                <a href="organization.php?id=<?php echo doCleanOutput($post_row["CID"]);?>&all_tabs=1&year=<?php echo $cur_year;?>"><?php



                    //echo doCleanOutput($post_row["CompanyNameThai"]);


                    echo formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]);



                    ?></a>
            </td>
            <td>
                <?php


                echo getFirstItem("select province_name from provinces where province_id = '".$post_row["Province"]."'");

                ?>

            </td>
            <td>

                <div align="center"><?php //echo $post_row["lawfulness_status"];

                    echo getLawfulImage(($post_row["lawfulness_status"]));

                    ?></div>

            </td>
        </tr>

        <?php


    }

    ?>


    <?php

    if(!$_POST["do_hide_more"]){
    ?>
    <tr>
        <td colspan="5">
            <div align="center">
                <a href="#" onclick="doGetEjobList(10000,1); return false;">.. ดูรายการทั้งหมด ..</a>
            </div>
        </td>
    </tr>
    <?php } ?>


</table>