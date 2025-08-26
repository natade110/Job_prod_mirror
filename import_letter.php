<?php

	include "db_connect.php";
	include "session_handler.php";
	

	if($_POST["upload_file"]){

        //echo "whag";

        $file_size = $_FILES["input_file"]['size'];
        $file_type = $_FILES["input_file"]['type'];
        $file_name = $_FILES["input_file"]['name'];
        $new_file_name = date("ymdhis").rand(00,99)."_".$file_name;
        //$file_new_path = $upload_folder.$new_file_name;

        //echo $upload_folder; exit();

        //validation
        if($file_type != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){

            header ("location: import_letter.php?xlsx=no");	 exit();
        }
        if($file_size > 25000000){

            header ("location: import_letter.php?filesize=no");	 exit();
        }


        $do_upload_files = 1;

        /*if(move_uploaded_file($_FILES["input_file"]['tmp_name'], $file_new_path)){

            $do_upload_files = 1;

        }else{
            header ("location: import_letter.php?moveupload=1");	 exit();
        }*/

        //start import excel
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        require_once './PHPExcel/Classes/PHPExcel/IOFactory.php';

        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel = $objReader->load($_FILES["input_file"]['tmp_name']);
        //$objPHPExcel = $objReader->load("notice_org.xlsx");

        //echo $_FILES["input_file"]['tmp_name'];



        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

            //echo "a";

            $rowcount = 0;

            foreach ($worksheet->getRowIterator() as $row) {

                $rowcount++;

                if($rowcount <= 2){
                    continue;
                }

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set

                $column_count = 0;
                $row_value = array();

                foreach ($cellIterator as $cell) {


                    $column_count++;

                    $row_value[$column_count] = doCleanInput((trim($cell->getCalculatedValue())));

                }

                //looped cells -> do insert statement

                if(strlen(doCleanInput($row_value[1])) > 0) {



                    //start query process
                    $Year = doCleanInput($row_value[2]-543);

                    $request_date = (substr(doCleanInput($row_value[3]),0,4)-543)."-".substr(doCleanInput($row_value[3]),4,2)."-".substr(doCleanInput($row_value[3]),6,2);

                    $RequestNum = doCleanInput($row_value[4]);
                    $GovDocumentNo = doCleanInput($row_value[5]);

                    $company_row = getFirstRow("select CID, Province from company where companyCode = '".doCleanInput($row_value[1])."' and branchCode < 1");
                    $CID =  $company_row["CID"];
                    $Province =  $company_row["Province"];

                    $DocBKK1 = '';
                    $DocBKK2 = '';
                    $DocBKK3 = '';
                    $DocPro1 = '';
                    $DocPro2 = '';
                    $DocPro3 = '';

                    if(doCleanInput($row_value[6]) == "ส่งแล้ว"){

                        if($Province == 10){
                            $DocBKK1 = 1;
                            $DocPro1 = 0;
                        }else{
                            $DocBKK1 = 0;
                            $DocPro1 = 1;
                        }

                    }

                    if(doCleanInput($row_value[7]) == "ส่งแล้ว"){

                        if($Province == 10){
                            $DocBKK2 = 1;
                            $DocPro2 = 0;
                        }else{
                            $DocBKK2 = 0;
                            $DocPro2 = 1;
                        }

                    }

                    if(doCleanInput($row_value[8]) == "ส่งแล้ว"){

                        if($Province == 10){
                            $DocBKK3 = 1;
                            $DocPro3 = 0;
                        }else{
                            $DocBKK3 = 0;
                            $DocPro3 = 1;
                        }

                    }

                    if(doCleanInput($row_value[6]) == "ไม่ได้ส่ง"){

                        if($Province == 10){
                            $DocBKK1 = 0;
                            $DocPro1 = 0;
                        }else{
                            $DocBKK1 = 0;
                            $DocPro1 = 0;
                        }

                    }

                    if(doCleanInput($row_value[7]) == "ไม่ได้ส่ง"){

                        if($Province == 10){
                            $DocBKK2 = 0;
                            $DocPro2 = 0;
                        }else{
                            $DocBKK2 = 0;
                            $DocPro2 = 0;
                        }

                    }

                    if(doCleanInput($row_value[8]) == "ไม่ได้ส่ง"){

                        if($Province == 10){
                            $DocBKK3 = 0;
                            $DocPro3 = 0;
                        }else{
                            $DocBKK3 = 0;
                            $DocPro3 = 0;
                        }

                    }

                    $PostRegNum = doCleanInput($row_value[9]);
                    $PostReceiverName = doCleanInput($row_value[10]);
                    //$PostReceivedTime = doCleanInput($row_value[11]);
                    //(substr(doCleanInput($row_value[3]),0,4)-543)."-".substr(doCleanInput($row_value[3]),4,2)."-".substr(doCleanInput($row_value[3]),6,2);
                    $PostReceivedTime = (substr(doCleanInput($row_value[11]),0,4)-543)."-".substr(doCleanInput($row_value[11]),4,2)."-".substr(doCleanInput($row_value[11]),6,2)
                                        ." ".substr(doCleanInput($row_value[11]),9,2)
                                        .substr(doCleanInput($row_value[11]),11,3).":00"
                                        ;

                    $sql = "select RID from documentrequest where 
                            (
                                RequestNum = '".$RequestNum ."' 
                                and 
                                GovDocumentNo = '".$GovDocumentNo."'
                            )
                         limit 0,1";

                    //echo $sql;

                    $existed = getFirstItem($sql);


                    if($existed){

                        $new_letter_id = $existed;

                        //echo "existed: ".$new_letter_id;

                    }else{

                        //create new
                        $the_sql = "
                        
                            insert into 
                              documentrequest(						
						
                                 RequestNum   
                                 , GovDocumentNo                                    
                                
                                
                                , ModifiedDate                                
                                , ModifiedBy
                                , Year
                                , RequestDate
                                
                              )values(
                              
                                '$RequestNum'
                                ,'$GovDocumentNo'
                                
                                , NOW()
                                , '$sess_userid'
                                , '$Year'
                                , '$request_date'
                                
                              
                              )                           
                        
                        
                        ";

                        //echo  $the_sql;
                        mysql_query($the_sql);

                        $new_letter_id = mysql_insert_id();

                    }

                    //start insert docrequestcompany
                    $the_sql = "
                    
                        insert ignore into docrequestcompany(
                        
                            RID
                            , CID                            
							, ModifiedDate
							, ModifiedBy
                        
                        )values(
                        
                            '$new_letter_id'
                            , '$CID'                            
							, NOW()
							, '$sess_userid'
                        
                        )
                    
                    
                    ";

                   // echo  $the_sql;
                    mysql_query($the_sql);


                    //start update docrequestcompany
                    $the_sql = "
                        
                        update
                            docrequestcompany
                        set
                           ModifiedDate = NOW()
							, ModifiedBy = '$sess_userid'
                        where
                          RID = '$new_letter_id'
                          and CID = '$CID' 
                    ";

                    mysql_query($the_sql);


                    $update_array = array(

                        'DocBKK1'
                        , 'DocBKK2'
                        , 'DocBKK3'
                        , 'DocPro1'
                        , 'DocPro2'
                        , 'DocPro3'

                        , 'PostRegNum'
                        , 'PostReceiverName'
                        , 'PostReceivedTime'

                    );
                    
                    for($ii = 0; $ii < count($update_array); $ii++){

                        if(strlen(${$update_array[$ii]})>0){

                            $the_sql = "
                            
                                update
                                    docrequestcompany
                                set
                                   $update_array[$ii] = '".${$update_array[$ii]}."'
                                where
                                  RID = '$new_letter_id'
                                  and CID = '$CID' 
                            ";

                            //echo "<br>".$the_sql;
                            mysql_query($the_sql);

                        }
                    }


                    $row_completed++;
                }


            }

        }

        //echo $data;
        //echo $rowcount;

        //check if name/seq already existed...



    }




?>

<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >การนำเข้าข้อมูลจดหมายแจ้งสถานประกอบการ </h2>
                    
                    <b>1. นำเข้าไฟล์จดหมายแจ้งสถานประกอบการ</b>

                    
                    <form method="post" enctype="multipart/form-data">
                      <table>


                          <tr>
                              <td style="padding: 10px 0;">

                                  <img src="decors/excel_small.jpg"/> <a href="notice_org.xlsx">ตัวอย่างไฟล์นำเข้า</a><br />
                              </td>
                          </tr>

                          <tr>
                        	<td>
                            <input name="input_file" type="file" />
                                <br>
                              <div style="padding: 10px 0;">
                                <input name="upload_file" type="submit" value="นำเข้าไฟล์" />
                              </div>
                            </td>
                        </tr>

                   </table>

                   
                    </form>
                   
                   <?php if($do_upload_files){?>

                       <hr>
                       <div style="color:#003300">นำเข้าข้อมูลสำเร็จ <strong><?php echo $row_completed*1;?> </strong> รายการ</div>
                       <div style="color:#CC3300">นำเข้าข้อมูลไม่สำเร็จ <strong><?php echo $row_failed*1;?></strong> รายการ</div>
                       <?php //echo $row_failed_to_show;?>
                   
                   <?php }?>
                   
                   
                   <br />
					<br /></td>
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

</body>
</html>