<?php

	include "db_connect.php";
	
	session_start();
	if(isset($_SESSION['sess_userid'])){
		$sess_userid = $_SESSION['sess_userid'];
	}
	if(isset($sess_userid)){
		header("location: org_list.php");
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
                                <?php if($_GET["mode"] == "error_pass"){echo "invalid username or password!";}?> <input name="" type="submit" value="login" />
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