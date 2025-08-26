<?php
	
	include "db_connect.php";
	include "session_handler.php";
	include "global.js.php";
	
	if($sess_accesslevel != 1){		
		header("location: index.php");exit();		
	}

?>
<?php include "header_html.php";?>
               
             
               
             
             <td valign="top" >
			 
				<div id="the_modal" v-html="content">
					<?php 
					
						//include "modal_current_online.php";
					
					?>
					{{content}}
				</div>
				
				
				<script>
				
					var the_modal = new Vue({
					  el: '#the_modal',
					  data: {
						order: "x",
						content: '<h4>...</h4>'
					  }
					})
					
				
					function getModal(){
								
						$.ajax({
						  method: "POST",
						  url: "modal_current_online.php"
						})
						  .done(function( html ) {				
							//alert(html);
							the_modal.content = html;
						  });
					  
					}
					
					getModal();
					setInterval(function() {getModal()}, 10000);
					
				
				</script>
					
                             
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
<?php //yoes 20170213 -- set faux cron here ?>
<script>
	//$.get("ajax_do_snapshot.php");
</script>

</body>
</html>