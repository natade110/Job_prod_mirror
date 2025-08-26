 <select name="PaymentMethod" id="PaymentMethod" onchange="doToggleMethod();">
                                    
                                    <?php if($have_blank){ //use this in payment_list.php?>
                                    <option value="">....</option>
                                    <?php }?>
                                    <option value="Cash" <?php if($_POST["PaymentMethod"]=="Cash"){?>selected="selected"<?php }?>>เงินสด</option>
                                    <option value="Cheque" <?php if($_POST["PaymentMethod"]=="Cheque"){?>selected="selected"<?php }?>>เช็ค</option>
                                    <option value="Note" <?php if($_POST["PaymentMethod"]=="Note"){?>selected="selected"<?php }?>>ธนาณัติ</option>
									<option value="NET" <?php if($_POST["PaymentMethod"]=="NET"){echo "selected='selected'";}?>>KTB Netbank</option>
                                  </select>