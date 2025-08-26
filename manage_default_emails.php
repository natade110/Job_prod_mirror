<?php

	include "db_connect.php";
	include "session_handler.php";
	
	
?>



<?php include "header_html.php";?>
                <td valign="top" style="padding-left:5px;">
                	
                    <h2 class="default_h1" style="margin:0; padding:0 0 10px 0;"  >ระบบการจัดการ email</h2>
                   
                    
                   

                    <strong>จัดการพื้นที่การทำงาน</strong>
                  
                    
                  	
                    
                    
                    <table cellpadding="3">
                    
                   		 <tr>
                        
                        
                        	<td style="background-color:#efefef">
                            	ประเภทของ email                            </td>
                        	<td style="background-color:#efefef">
                            	ระดับของผู้ใช้งานที่ได้รับ email
                            </td>

                         </tr>
                         
                         
                         <tr>                        
                        
                        	<td >      
                            
                           แจ้งเตือน: การยื่นเอกสารออนไลน์มาจากสถานประกอบการ</td>
                        	
                            <td >
                            
                            <?php
							
								createCheckBoxes(1);
							
							?>
                            
                            	
                             </td>
                            
                        	
                      </tr>
                         <tr bgcolor="#efefef">
                           <td >แจ้งเตือน: การส่งข้อมูลผู้ใช้งานเข้ามาจากผู้ใช้งานสถานประกอบการ</td>
                           <td >
                           
                           <?php
							
								createCheckBoxes(2);
							
							?>
                           
                           </td>
                         </tr>
                         
                         <tr>
                           <td >แจ้งเตือน: การขอปรับปรุงข้อมูลการจ่ายเงินตามมาตรา 34</td>
                           <td >
                           
                           <?php
							
								createCheckBoxes(3);
							
							?>
                           
                           </td>
                         </tr>


                        <tr>
                            <td >แจ้งเตือน: การขอเพิ่มข้อมูลการจ่ายเงินตามมาตรา 34</td>
                            <td >

                                <?php

                                createCheckBoxes(4);

                                ?>

                            </td>
                        </tr>


                        <tr>
                            <td >แจ้งเตือน: การขอยกเลิกข้อมูลการจ่ายเงินตามมาตรา 34</td>
                            <td >

                                <?php

                                createCheckBoxes(5);

                                ?>

                            </td>
                        </tr>
                        
                         
                       
                         
                         
                         
                         
                       
                    </table>
                    
                    
                    <hr />
                    
                    <div align="center">
                        <form method="post">
                            <input id="exit" type="submit" style="width: 115px" value="ปรับปรุงข้อมูล" />
                        </form>
                    </div>
                    
                    
                   
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


<?php
	
	function createCheckBoxes($mail_type = 0){

	    $user_role_array = array(

	        "เจ้าหน้าที่ พมจ. (ได้รับ email ของสถานประกอบการภายในจังหวัด)"
            , "เจ้าหน้าที่ พก. (ได้รับ email ของสถานประกอบการภายใน กทม.)"
            , "ผู้ดูแลระบบ"
            , "ผู้บริหาร"
            , "เจ้าหน้าที่งานคดี"

        );

        $user_role_id = array(

            3
        , 2
        , 1
        , 5
        , 8

        );

        for($i=0; $i < count($user_role_array);$i++){

            if($i > 0){echo "<br>";}

            $checked = '';

            if(getFirstItem("select * from user_email where user_id = '".$user_role_id[$i]."' and email_id = '".$mail_type."' and mail_enabled = 1")){

                $checked = 'checked';
            }

            echo '<input type="checkbox" name="checkbox" id="chk_'.$user_role_id[$i].$mail_type.'" '.$checked.' onClick="doToggleUserEmail('.$user_role_id[$i].','.$mail_type.');"/>' . $user_role_array[$i] ;

        }


		
	}

?>

<script>

    function doToggleUserEmail(user_id, email_id){

        //alert(user_id + ':' + email_id);
        var chk_id = 'chk_' + user_id + email_id;
        //alert($('#' + chk_id).is(":checked"));
        var mail_enabled = $('#' + chk_id).is(":checked");

        if(mail_enabled == true){
            mail_enabled = 1;
        }else{
            mail_enabled = 0;
        }
        //alert(mail_enabled);
       // return;

        $.ajax({ url: './ajax_update_user_email.php',
            data: {user_id: user_id, email_id: email_id, mail_enabled: mail_enabled},
            type: 'post',
            success: function(output) {
                //alert(output);
                //$('#cid_'+what+'_saving').css("display","none");
            }
        });






    }


</script>

</body>
</html>