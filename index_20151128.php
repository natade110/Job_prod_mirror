<?php

	include "db_connect.php";
	
	session_start();
	if(isset($_SESSION['sess_userid'])){
		$sess_userid = $_SESSION['sess_userid'];
	}
	if(isset($sess_userid)){
		
		//yoes 20151117 --- first page of admin is dashboard page
		if($sess_accesslevel == 1 || $sess_accesslevel == 3){
			header("Location: dashboard.php");
			exit();
		}else{
			header("location: org_list.php");
			exit();	
		}
		
	}

?>
<?php include "header_html.php";?>
               
               
               
             
             <td valign="top">
                              <form action="scrp_do_login.php" method="post">
                    <table align="center" style="padding:15px 0 15px 0;">
                        <tr>
                            <td colspan="2">
                            <strong>Login</strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            User name: 
                            </td>
                            <td>
                            <input name="user_name" type="text" />
                            </td>
                         </tr>
                         <tr>
                            <td>
                            Password:
                            </td>
                            <td>
                            <input name="password" type="password" />
                            <?php if($_GET["cont"]){?>
                            <input name="cont" type="hidden" value="<?php echo $_GET["cont"];?>" />
                            <?php } ?>
                            </td>
                         </tr>
                         <tr>
                            <td colspan="2" align="right">
                                <?php if($_GET["mode"] == "error_pass"){echo "ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง!";}?>
                                
                                <?php if($_GET["mode"] == "pending"){echo "User Name รอเปิดการใช้งาน โดยผู้ดูแลระบบ";}?> 
                                
                                <?php if($_GET["mode"] == "disabled"){echo "User Name ไม่ได้รับอนุญาตให้ใช้งานระบบ";}?> 
                                
                                <input name="" type="submit" value="login" /> | <a href="view_register_password.php">ลืมรหัสผ่าน</a>
                            </td>
                         </tr>
                    </table>            
                    </form>  
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

</body>
</html>