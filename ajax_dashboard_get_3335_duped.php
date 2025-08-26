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
        <td colspan="5">
            <div align="center" style="color: #060; font-family: 'Microsoft Sans Serif';">
                รายชื่อสถานประกอบการที่ใช้ลูกจ้างคนพิการซ้ำซ้อนและผู้ดูแลซ้ำซ้อน
            </div>
        </td>
    </tr>
    <tr>

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

                เลขที่บัตรประชาชนที่ซ้ำซ้อน

            </div>
        </td>
        <td align="center">

            <div align="center">

                มาตราที่ซ้ำซ้อน

            </div>

        </td>
        <td align="center">
            <div align="center">
                สถานะ
            </div>
        </td>
    </tr>

    <?php

    //yoes 20180104 -- add limit 0,20 and ajax the rest

    $sql = "
									
															 
									  
									
									
									  select
					
											the_code
											
										  from
										
										  (
											select
											le_code as the_code
											, le_name as the_name
											, 'l' as the_type
											
										  from
											lawful_employees
												
											where le_year = '$the_end_year'
										
											union
										
											select
											  curator_idcard as the_code
											  , curator_name as the_name
											  , 'c' as the_type
											from
											  curator, lawfulness
											  where
											  curator_lid = lid
											  and
											  Year = '$the_end_year'
											  
											  and
											  curator_is_disable = 1
											  
										
											  )                    a
										
										
										group by the_code
										having count(the_code) > 1
										
										order by the_code asc
										
										limit 0,$the_limit
									
									";

    //echo $sql;

    $submit_result = mysql_query($sql);

    $cur_year = $the_end_year;

    while($post_row = mysql_fetch_array($submit_result)){

        ?>





        <?php

        $sub_sql = "
												
													
													select
															a.cid
															, CompanyNameThai
															, CompanyTypeCode
															, Province
															, LawfulStatus
															, le_code		
															, '33' as the_type													
													  from
														company a
															join lawfulness b
														
															  on
																a.cid = b.cid
																and
																b.year = '$the_end_year'
																
															join
																lawful_employees c
																	on a.cid = c.le_cid
																	and
																	b.year = c.le_year
																													 
													  where
													  	le_code = '".$post_row["the_code"]."'
														and
														CompanyTypeCode != '14'
														and
														CompanyTypeCode < 200
														
														
													union
													
													
													select
															a.cid
															, CompanyNameThai
															, CompanyTypeCode
															, Province
															, LawfulStatus
															, curator_idcard as le_code		
															, '35' as the_type
													  from
														company a
															join lawfulness b
														
															  on
																a.cid = b.cid
																and
																b.year = '$the_end_year'
																
															join
																curator c
																	on b.lid = c.curator_lid

																													 
													  where
													  	curator_idcard = '".$post_row["the_code"]."'
														and
														curator_parent = 0
														and
														CompanyTypeCode != '14'
														and
														CompanyTypeCode < 200
														
														
														
												
												";


        //echo "<br>".$sub_sql;

        $sub_result = mysql_query($sub_sql);


        while($sub_row = mysql_fetch_array($sub_result)){


            ?>


            <tr>

                <td>




                    <a href="organization.php?id=<?php echo $sub_row["cid"];?>&year=<?php echo $the_end_year;?>" target="_blank">
                        <?php



                        //echo doCleanOutput($post_row["CompanyNameThai"]);


                        echo formatCompanyName($sub_row["CompanyNameThai"],$sub_row["CompanyTypeCode"]);



                        ?>
                    </a>

                </td>
                <td>
                    <?php


                    echo getFirstItem("select province_name from provinces where province_id = '".$sub_row["Province"]."'");

                    ?>


                </td>
                <td>

                    <?php

                    echo $sub_row["le_code"];

                    ?>
                </td>
                <td>


                    <div align="center">

                        <?php

                        if($sub_row["the_type"] == "33"){
                            $this_type = "มาตรา 33";
                        }else{
                            $this_type = "มาตรา 35";
                        }

                        echo $this_type;

                        ?>

                    </div>

                </td>

                <td>

                    <div align="center"><?php //echo $post_row["lawfulness_status"];

                        echo getLawfulImage(($sub_row["LawfulStatus"]));

                        ?></div>

                    <?php

                    //echo getLawfulImageFromLID(($post_row["lawful_id"]));
                    ?>

                </td>
            </tr>




        <?php } //end sub-while?>




        <?php


    }

    ?>

    <?php

    if(!$_POST["do_hide_more"]){
    ?>
    <tr>
        <td colspan="5">
            <div align="center">
                <a href="#" onclick="doGet3335List(10000,1); return false;">.. ดูรายการทั้งหมด ..</a>
            </div>
        </td>
    </tr>
    <?php } ?>


</table>