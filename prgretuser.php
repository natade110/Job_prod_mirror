<?php

$is_utf8_page = 1; //this is an utf8 page

/*****************************************************
* © copyright 1999 - 2003 Interactive Arts Ltd.
*
* All materials and software are copyrighted by Interactive Arts Ltd.
* under British, US and International copyright law. All rights reserved.
* No part of this code may be reproduced, sold, distributed
* or otherwise used in whole or in part without prior written permission.
*
*****************************************************/
######################################################################
#
# Name:         prgretuser.php
#
# Description:  Returns individual member adverts from the search screen
#
# Version:      7.2
#
######################################################################
include('db_connect.php');
//include('session_handler.inc');
include('functions.php');


//20140331
//added new functions
include('tll_functions.php');



include($CONST_NETWORK_INCLUDE_ROOT.'/functions.php');
include_once __INCLUDE_CLASS_PATH."/class.Network.php";
include_once __INCLUDE_CLASS_PATH."/class.Gallery.php";
include('error.php');
include(__INCLUDE_CLASS_PATH.'/class.StaticProfile.php');
save_request();
$userid=formGet('userid');

/* enable to allow on approved profile to see this, but it 'll make it so guest can no longer see the page
$result=mysql_query("
        SELECT *
        FROM adverts
        LEFT JOIN members ON adv_userid=mem_userid
        WHERE adv_userid = '$Sess_UserId'
        AND adv_approved = 1
        AND mem_confirm = 1"
        ,$link) or die(mysql_error());
if (mysql_num_rows($result) < 1){
        $error_message=PRGRETUSER_TEXT10;
        error_page($error_message,GENERAL_USER_ERROR);
}*/

//yoes may 08
//fixed so no get->userid will redirect to index.php
if(!isset($_GET["userid"])){
	if(isset($Sess_UserId)){
		header("Location: $CONST_LINK_ROOT/home.php");	
	}else{
		header("Location: $CONST_LINK_ROOT/index.php");	
	}
}

$advuser=$userid; // set for hasprofile
if ($userid != $Sess_UserId && isset($Sess_UserId)) {

	
    $query="SELECT * FROM encounters_2 WHERE enc_userid = '$userid' AND enc_viewerid='$Sess_UserId'";
    $result=mysql_query($query,$link) or die(mysql_error());
    $TOTAL = mysql_num_rows($result);
    if ($TOTAL == 0) {
        $tempdate=date("Y-m-d");
        $result=mysql_query("INSERT INTO encounters_2 (enc_userid, enc_viewerid, enc_viewdate) VALUES ('$userid','$Sess_UserId','$tempdate')",$link) or die(mysql_error());
    } else {
        $tempdate=date("Y-m-d");
        $result=mysql_query("UPDATE encounters_2 SET enc_viewdate='$tempdate' WHERE enc_userid = '$userid' AND enc_viewerid='$Sess_UserId'",$link) or die(mysql_error());
    }
	
}



# Select the main portion of the advert
$result=mysql_query("
    SELECT
        *,
        (YEAR(CURDATE())-YEAR(adv_dob)) - (RIGHT(CURDATE(),5) < RIGHT(adv_dob,5)) AS age,
        mem_lastvisit, unix_timestamp(mem_timeout) AS session_active
    FROM adverts
    LEFT JOIN members ON (adv_userid=mem_userid)
    LEFT JOIN geo_country ON (adv_countryid = gcn_countryid)
    LEFT JOIN geo_city ON (adv_cityid = gct_cityid)
    LEFT JOIN geo_state ON (adv_stateid = gst_stateid)
    WHERE adv_userid = '$userid'
	and (adv_approved = 1 or adv_approved = 6)
	"
    ,$link);
	

# retrieve the template
if (isset($_SESSION['Sess_UserId'])){
	//echo "memeber";
	$area = 'Member32_seo';
}else{
	//echo "guest";
	$area = 'guest32_seo';
}	
	
	
if (mysql_num_rows($result) < 1){
    $error_message=PRGRETUSER_TEXT8;
    error_page($error_message,GENERAL_USER_ERROR);
}
include_once __INCLUDE_CLASS_PATH."/class.Adverts.php";
$adv = new Adverts();
$adv->InitByObject(mysql_fetch_object($result));
$adv->SetImage('medium');
$sql_array = $adv;

$st_profile = new StaticProfile($sql_array->adv_username);

///////
///by Yoes: add to check current member's member status
///////
if($sql_array->adv_expiredate >= date("Y-m-d")){
	//is premium
	$this_member_status = "<img border='0' src='$CONST_IMAGE_ROOT"."$CONST_IMAGE_LANG/mem_gold.gif' width='$CONST_MEMIMAGE_WIDTH' height='$CONST_MEMIMAGE_HEIGHT'>";
}else{
	//is not premium
	$this_member_status = "<img border='0' src='$CONST_IMAGE_ROOT"."$CONST_IMAGE_LANG/mem_silver.gif' width='$CONST_MEMIMAGE_WIDTH' height='$CONST_MEMIMAGE_HEIGHT'>";
}
///////
///
///////

# check for a profile
include($CONST_INCLUDE_ROOT.'/languages/has_profile_'.$_SESSION['lang_id'].'.inc.php');

# fetch the my match info
if(isset($Sess_UserId)){	
	$result=mysql_query("SELECT * FROM mymatch WHERE mym_userid=$Sess_UserId") or die(mysql_error());
	if (mysql_num_rows($result) > 0) {
		$sql_me=mysql_fetch_object($result);
		#make the calculation
		$score=0;
		if ($sql_me->mym_gender == $sql_array->adv_sex) $score+=50;
		if (($sql_array->age >= $sql_me->mym_agemin) && ($sql_array->age <= $sql_me->mym_agemax)) $score+=10;
		if (($sql_array->adv_height >= $sql_me->mym_minheight) && ($sql_array->adv_height <= $sql_me->mym_maxheight)) $score+=10;
		if (($sql_array->adv_bodytype == $sql_me->mym_bodytype) OR ($sql_me->mym_bodytype=='Not stated')) $score+=10;
		if (($sql_array->adv_seeking == $sql_me->mym_relationship) OR ($sql_me->mym_mym_relationship=='Not stated')) $score+=10;
		if (($sql_array->adv_smoker == $sql_me->mym_smoker) OR ($sql_me->mym_smoker=='Not stated')) $score+=10;
			if ($score < 60) $myscore=PRGRETUSER_POOR;
			elseif ($score < 80) $myscore=PRGRETUSER_FAIR;
			elseif ($score < 100) $myscore=PRGRETUSER_GOOD;
	} else {
			$myscore=PRGRETUSER_NO_DATA;
	}
}

# fetch the thir match info

$result=mysql_query("SELECT * FROM mymatch WHERE mym_userid=$userid") or die(mysql_error());
if (mysql_num_rows($result) > 0) {

	$sql_they=mysql_fetch_object($result);
	if ($sql_they->mym_gender=='M') $mygender= PRGSTATS_MALES;
	elseif ($sql_they->mym_gender=='F') $mygender= PRGSTATS_FEMALES;
	elseif ($sql_they->mym_gender=='C') $mygender= PRGSTATS_COUPLE;
	
	if(isset($Sess_UserId)){
	
		$result=mysql_query("SELECT (YEAR(CURDATE())-YEAR(adv_dob)) - (RIGHT(CURDATE(),5) < RIGHT(adv_dob,5)) AS age, adv_height, adv_bodytype, adv_seeking, adv_sex, adv_smoker FROM adverts WHERE adv_userid=$Sess_UserId") or die(mysql_error());
		$sql_me=mysql_fetch_object($result);
		#make the calculation
		$score=0;
		if ($sql_they->mym_gender == $sql_me->adv_sex) $score+=50;
		if (($sql_me->age >= $sql_they->mym_agemin) && ($sql_me->age <= $sql_they->mym_agemax)) $score+=10;
		if (($sql_me->adv_height >= $sql_they->mym_minheight) && ($sql_me->adv_height <= $sql_they->mym_maxheight)) $score+=10;
		if (($sql_me->adv_bodytype == $sql_they->mym_bodytype) OR ($sql_they->mym_bodytype=='Not stated')) $score+=10;
		if (($sql_me->adv_seeking == $sql_they->mym_relationship) OR ($sql_they->mym_relationship=='Not stated')) $score+=10;
		if (($sql_me->adv_smoker == $sql_they->mym_smoker) OR ($sql_they->mym_smoker=='Not stated')) $score+=10;
		if ($score < 60) $theyscore=PRGRETUSER_POOR;
		elseif ($score < 80) $theyscore=PRGRETUSER_FAIR;
		elseif ($score < 100) $theyscore=PRGRETUSER_GOOD;
		
	}
		
} else {
	$theyscore=PRGRETUSER_NO_DATA;
}


?>
<?=$skin->ShowHeader($area)?>
<table width="<?php print("$CONST_TABLE_WIDTH"); ?>" align="<?php print("$CONST_TABLE_ALIGN"); ?>" border="0" cellspacing="<?php print("$CONST_TABLE_CELLSPACING"); ?>" cellpadding="<?php print("$CONST_TABLE_CELLPADDING"); ?>">
  <tr>
    <td align="right">
      <?php 
	  	//require_once("$CONST_INCLUDE_ROOT/user_status.inc.php");
		echo $this_member_status;
	  ?>
    </td>
  </tr>
  <tr>
    <td class="pageheader"><?php echo $sql_array->mem_forename." (".$sql_array->adv_userid.")"; ?></td>
  </tr>
  <tr>
    <td> <table width="100%"  border="0" cellpadding="<?php print("$CONST_SUBTABLE_CELLPADDING"); ?>" cellspacing="<?php print("$CONST_SUBTABLE_CELLSPACING"); ?>">
        <tr >
          <td colspan="2" class="tdhead"><?php echo to_UTF8((stripslashes("$sql_array->adv_title"))); ?>&nbsp;</td>
        </tr>
        <tr>
          <td width="50%" align="center" valign="top" >
          <table width="100%" border="0" cellpadding="4" cellspacing="0">
              <tr>
                <td height="200" align="center" nowrap  class="retimage">
                  <?php
				  
		//hot list thing
		if(isset($Sess_UserId)){
        	$pics=mysql_query("SELECT * FROM hotlist WHERE hot_userid = $userid AND hot_advid = $Sess_UserId AND hot_private='Y'",$link);
        	$show_private=mysql_num_rows($pics);
		}else{

			$show_private=0;
		}
		
        include_once __INCLUDE_CLASS_PATH."/class.Picture.php";
        $picture = new Picture();
        $aPicture=$picture->GetListByMember($userid);
        $no_of_pics = count($aPicture);
        if ($no_of_pics > 0) {
            $pic_no=0;
            foreach ($aPicture as $sql_pic_array) {
                $pic_no++;
                if($show_private || $sql_pic_array->pic_private != 'Y')
                {
                    $medium = $sql_pic_array->GetInfo('medium');
                    $full = $sql_pic_array->GetInfo('');
                    $url_src .= ",\"javascript:MDM_openWindow('prgdisplaypic.php?thePic=$full->Path','Photograph','width=1,height=1')\"";
                    $img_src .= ",'$CONST_LINK_ROOT$medium->Path'";
                    $dimensions = "width=$medium->w";
					
                }
                else
                {
                    $img_src .= ",'$CONST_LINK_ROOT$sql_pic_array->private_file'";
                    $url_src .= ",\"#\"";
                    $dimensions = "width=".CONST_THUMBS_MEDIUM_W;
                }
            }
            $loading_img_url = "$CONST_IMAGE_ROOT"."$CONST_IMAGE_LANG/photo_loading.gif";
            ?>
                  <script language=javascript>
                function show_image(name,name1,cur_image_num){
                    url = eval(name + '_img_src[' + cur_image_num + ']');					
                    url1 = eval(name + '_url_src[' + cur_image_num + ']');					
                    img = document.getElementById(name);
                    img.src = loading_img_url;
                    link_url = document.getElementById(name1);
                    link_url.href = url1;
                    if(<?=$no_of_pics?> > 1)
                    {
                        pic_caption = document.getElementById(name+'_caption');
                        pic_caption.innerHTML = cur_image_num + ' <?=ADVERTISE_PHOTO_NUM_OF?> <?=$no_of_pics?>';
                    }
                    window.setTimeout('document.getElementById(\'' + name + '\').src = \'' + url + '\'', 1);
                }
				
				function set_glass_url(cur_image_num){
					url1 = eval('mainpicture_url_src[' + cur_image_num + ']');					
					objGlass = document.getElementById("glass_01");
                    objGlass.href = url1;
				}
				
				
                mainpicture_img_src = new Array(0<?=$img_src?>);
                mainpicture_url_src = new Array(0<?=$url_src?>);
                loading_img_url = '<?=$loading_img_url?>';
                imgLoading = new Image();
                imgLoading.src = loading_img_url;
            </script> <table width="100%" height="" border="0" cellpadding="2" cellspacing="0">
                    <tr>
                      <td align="center" valign="middle" > <table  border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td class="imageframe"><a id="bigpicture" href="javascript:MDM_openWindow('prgdisplaypic.php?thePic=<?=$sql_pic_array->pic_picture?>','Photograph','width=1,height=1')"><img id="mainpicture" name='pic' border=0 src='<?=$loading_img_url?>?<?=time() ?>'  width="120" height="160"></a></td>
                          </tr>
                        </table></td>
                    </tr>
                    <tr>
                      <td height="20" align="center" valign="bottom" >
                        <?php if ($no_of_pics > 1) { ?>
                        <a href="#" onClick="if (--cur_image_num<1) cur_image_num=<?=$no_of_pics?>;show_image('mainpicture','bigpicture',cur_image_num);set_glass_url(cur_image_num);"><img src="<?=$CONST_IMAGE_ROOT?><?=$CONST_IMAGE_LANG?>/btn_previous.gif" border=0></a>
						&nbsp;
						<span id="mainpicture_caption"></span>
						&nbsp;
						<a href="#" onClick="if (++cur_image_num><?=$no_of_pics?>) cur_image_num=1;show_image('mainpicture','bigpicture',cur_image_num);set_glass_url(cur_image_num);"><img src="<?=$CONST_IMAGE_ROOT?><?=$CONST_IMAGE_LANG?>/btn_next.gif" border=0></a>
                        <?php } ?>
						<a id="glass_01" href="javascript:MDM_openWindow('prgdisplaypic.php?thePic=<?=$sql_pic_array->pic_picture?>','Photograph','width=1,height=1')"><img id="glass_icon_picture" border="0" src="<?=$CONST_LINK_ROOT?>/skins/blue/images/magnify-glass-b.gif" width="15" height="25" alt="Enlarge <?php echo ucfirst($sql_array->mem_forename);?>'s photograph by clicking here."></a>						
                      </td>
                    </tr>
                  </table>
				<script language=javascript>
					cur_image_num = 1;							
					show_image('mainpicture','bigpicture',cur_image_num); 					
					set_glass_url(cur_image_num);
				</script>
                  <?php } else { ?>
                  <table  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td class="imageframe"><img id=mainpicture name='pic' border=0 src='<?=$CONST_LINK_ROOT?><?=$sql_array->adv_picture->Path?>'></td>
                    </tr>
                  </table>
                  <?php } ?>
                  <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                      <td align="center">
                        <?php
    if ($CONST_VIDEOS == 'Y') {
        include_once __INCLUDE_CLASS_PATH."/class.Video.php";
        $video = new Video();
        $aVideo=$video->GetListByMember($userid);
        if (count($aVideo) > 0) {
            $sql_videos = array_shift($aVideo);
            $vid_info = $sql_videos->File->getInfo('medium');
            if ($sql_videos->vid_private == 'N' || $show_private) {
               print("<a href='$CONST_LINK_ROOT$vid_info->Path'><img src='$CONST_LINK_ROOT$sql_videos->title_file' border=0></a>");
            } else {
               print("<img src='$CONST_LINK_ROOT$sql_videos->private_file' border=0>");
            }
        }
    }
?>
                        <?php
    if ($CONST_AUDIOS == 'Y') {
        include_once __INCLUDE_CLASS_PATH."/class.Audio.php";
        $audio = new Audio();
        $aAudios=$audio->GetListByMember($userid);
        if (count($aAudios) > 0) {
            $sql_audios = array_shift($aAudios);
            $aud_info = $sql_audios->File->getInfo('medium');
            if ($sql_audios->aud_private == 'N' || $show_private) {
               print("<a href='$CONST_LINK_ROOT$aud_info->Path'><img src='$CONST_LINK_ROOT$sql_audios->title_file' border=0></a>");
            } else {
               print("<img src='$CONST_LINK_ROOT$sql_audios->private_file' border=0>");
            }
        }
    }
?>
                      </td>
                    </tr>
                  </table>
                  <?
                  $gallery = new Gallery();
                  if (count($gallery->GetListByMember($userid))){
                  ?>
                  <a href="<?=$CONST_GALLERY_LINK_ROOT?>/gallery.php?user_id=<?=$userid?>">Gallery</a>
                  <?}?>
                </td>
              </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="4">
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="tdhead"><?php echo PRGRETUSER_MESSAGE?> </td>
              </tr>
              <tr>
                <td>
                <div style="overflow:hidden; width: 265px; max-width: 265px;">
                <?php print to_UTF8($sql_array->adv_comment_full); ?></div></td>
                
              </tr>
              <?php print("$rowhead " . to_UTF8($personality) . " $rowfoot"); ?>
            </table>

          </td>
          <td width="50%" align="center" valign="top"  > <table width="100%" border="0" cellspacing="0" cellpadding="4">
              <tr>
                <td align="right"><a href='#' onClick="window.open('<?=$CONST_LINK_ROOT?>/add2hotlist.php?userid=<?=$userid?>&handle=<?=$sql_array->adv_username?>','','toolbar=no,menubar=no,height=150,width=200,left='+(screen.width/2-100)+',top='+(screen.height/2-75)+'');return false;" title="<?=PRGRETUSER_TEXT7?>">
                  <?php
            if (isset($Sess_UserId)) {
                  
                  include("member_icons.php");
				  //include("off_line_im_icon.php");
				  
				  //yoes july 08 add flashing im icon if member acutally online
				  include "online_me.inc.php";
				  
				  }else{
				  	include("guest_icons.php");
				  }
				  
                  $temptime=mktime (date("H"),date("i")-30,date("s"),date("m") ,date("d"),date("Y"));
                  if ($sql_array->mem_timeout >= date('YmdHis',$temptime) && $USERPLANE_IM && $Sess_UserId != $sql_array->adv_userid ) { ?>
				  
                  <a href="#" onClick="up_launchIC( '<?php echo( $Sess_UserId ) ?>', '<?echo $sql_array->adv_userid?>' ); return false;" title="Launch IM Now!"><img border='0' src='<?=$CONST_IMAGE_ROOT?><?=$CONST_IMAGE_LANG?>/addimfriend.gif' align="absmiddle"></a>
				  
                  <?php } ?>
                </td>
              </tr>
            </table>
            <table width="100%" border="0" cellpadding="4" cellspacing="0" >
              <tr>
                <td nowrap class="tdhead"><?php echo PRGRETUSER_DETAILS?></td>
              </tr>
              <tr>
                <td nowrap ><span class="rettext"><?php echo ADVERTISE_AGE ?>:
                  </span>
                  <?php
                print($sql_array->age);
         ?>
                </td>
              </tr>
              <tr>
                <td nowrap ><span class="rettext"><?php echo ADVERTISE_SIGN ?>:
                  </span>
                  <?php
                print(get_sign($sql_array->adv_dob));
         ?>
                </td>
              </tr>
              <tr>
                <td nowrap ><span class="rettext"><?php echo CUPID_REGION?>: </span>
                  <?=$sql_array->adv_region?>
                </td>
              </tr>
              <tr>
                <td nowrap ><span class="rettext"><?php echo GENERAL_CITY?>: </span>
                  <?php echo $sql_array->adv_location?> </td>
              </tr>
              <tr>
                <td nowrap ><span class="rettext"><?php echo ADVERTISE_SEEKING?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_seeking,"SKG")); ?></td>
              </tr>
              <tr>
                <td nowrap ><span class="rettext"><?php echo GENERAL_HEIGHT?>:</span>
                  <?
                $cm = $sql_array->adv_height;
                if (is_numeric($cm)) {
                    $in_inches = round($cm/2.54);
                    $in_feets = floor($cm/30.48);
                    if ($in_inches-$in_feets*12 == 12) $in_feets++;
                    $cur_i = $in_feets."'".($in_inches-$in_feets*12)."&quot;";
                    $height = $cur_i." (".$cm.ADVERTISE_CM.")";
                } else $height = PRGAMENDAD_NOT_STATED;
                print("$height");
                ?>
                </td>
              </tr>
              <tr>
                <td nowrap ><span class="rettext"><?php echo OPTION_EYE_COLOR?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_eyecolor,"EYE")); ?></td>
              </tr>
              <tr>
                <td nowrap ><span class="rettext"><?php echo OPTION_HAIR_COLOR?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_haircolor,"EYE")); ?></td>
              </tr>
              <tr>
                <td nowrap ><span class="rettext"><?php echo OPTION_BODY_TYPE?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_bodytype,"BDY")); ?></td>
              </tr>
              <tr>
                <td nowrap ><span class="rettext"><?php echo OPTION_RELIGION?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_religion,"RLG")); ?></td>
              </tr>
              <tr >
                <td ><span class="rettext"><?php echo OPTION_ETHNICITY?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_ethnicity,"ETH")); ?></td>
              </tr>
              <tr >
                <td ><span class="rettext"><?php echo OPTION_SMOKER?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_smoker,"SMK")); ?></td>
              </tr>
              <tr >
                <td ><span class="rettext"><?php echo ADVERTISE_DRINKING?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_drink,"DNK")); ?></td>
              </tr>
              <tr >
                <td ><span class="rettext"><?php echo ADVERTISE_MARITAL?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_marital,"MRT")); ?></td>
              </tr>
              <tr >
                <td ><span class="rettext"><?php echo ADVERTISE_CHILDREN?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_children,"CHL")); ?></td>
              </tr>
              <tr >
                <td ><span class="rettext"><?php echo OPTION_EDUCATION?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_education,"EDU")); ?></td>
              </tr>
              <tr >
                <td ><span class="rettext"><?php echo PRGRETUSER_PROFESSION?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_profession,"EMP")); ?></td>
              </tr>
              <tr >
                <td ><span class="rettext"><?php echo OPTION_INCOME?>:</span>
                  <?php print(transl_valueUTF8($sql_array->adv_income,"INC")); ?></td>
              </tr>
              <tr >
                <td ><span class="rettext"><?php echo PRGRETUSER_LASTVISIT?>:</span>
                  <?php print $sql_array->lastvisit?></td>
              </tr>
              <tr >
                <td >&nbsp;</td>
              </tr>
            </table>
            <?php if ( $option_manager->GetValue('snetwork')  && isset($Sess_UserId)) { ?><?php include_once "$CONST_NETWORK_INCLUDE_ROOT/action.inc.php"?><?php } ?>
            <table width="98%" border="0" align="center" cellpadding="2" cellspacing="0">
                <tr><td>
                <?php if ( $option_manager->GetValue('blogs') && isset($Sess_UserId) ) { ?>
                <input type="button" name="blog" class=button value="<?=$MENU_BLOGS?>" onClick="window.location='<?=$CONST_BLOG_LINK_ROOT?>/blogs.php?user_id=<?=$userid?>'">
                <?php } else { ?>
                </td></tr>
                <?php } ?>
            </table>
          </td>
        </tr>
        <tr>
          <td align="center" valign="top"  ><table width="100%" border="0" cellspacing="0" cellpadding="4">
              <td class="tdhead"><?php echo PRGRETUSER_MYMATCH?></td>
              </tr>
              <tr>
                <td><span class="rettext"><?php echo PRGRETUSER_GENDER?>:</span>
                  <?php echo $mygender ?></td>
              </tr>
              <tr>
                <td><span class="rettext">
                  <?=PRGRETUSER_AGES?>
                  :</span><?php echo $sql_they->mym_agemin?> - <?php echo $sql_they->mym_agemax ?></td>
              </tr>
              <tr>
                <td><span class="rettext">
                  <?=PRGRETUSER_SMOKER?>
                  :</span><?php print(transl_valueUTF8($sql_they->mym_smoker,"SMK")); ?></td>
              </tr>
              <tr>
                <td><span class="rettext">
                  <?=PRGRETUSER_RELATIONSHIP?>
                  :</span><?php print(transl_valueUTF8($sql_they->mym_relationship,"SKG")); ?></td>
              </tr>
              <tr>
                <td><span class="rettext">
                  <?=PRGRETUSER_HEIGHT?>
                  :</span>
                  <?
                $cm = $sql_they->mym_minheight;
                if (is_numeric($cm)) {
                    $in_inches = round($cm/2.54);
                    $in_feets = floor($cm/30.48);
                    if ($in_inches-$in_feets*12 == 12) $in_feets++;
                    $cur_i = $in_feets."'".($in_inches-$in_feets*12)."&quot;";
                    $height = $cur_i." (".$cm.ADVERTISE_CM.")";
                } else $height = $sql_they->mym_minheight;
                print("$height");
                ?>
                  -
                  <?
                $cm = $sql_they->mym_maxheight;
                if (is_numeric($cm)) {
                    $in_inches = round($cm/2.54);
                    $in_feets = floor($cm/30.48);
                    if ($in_inches-$in_feets*12 == 12) $in_feets++;
                    $cur_i = $in_feets."'".($in_inches-$in_feets*12)."&quot;";
                    $height = $cur_i." (".$cm.ADVERTISE_CM.")";
                } else $height = $sql_they->mym_maxheight;
                print("$height");
                ?>
                </td>
              </tr>
              <tr>
                <td><span class="rettext">
                  <?=PRGRETUSER_BODYTYPE?>
                  :</span><?php print(transl_valueUTF8($sql_they->mym_bodytype,"BDY")); ?></td>
              </tr>
            </table></td>
          <td align="center" valign="top"  ><table width="100%" border="0" cellspacing="0" cellpadding="4">
              <td class="tdhead">&nbsp;</td>
              </tr>
              <tr>
                <td><span class="rettext">
                  <?=PRGRETUSER_COMMENT?>
                  :</span><div style="overflow:hidden; width:250 px; max-width: 250px;"><?php echo to_UTF8(stripslashes(nl2br($sql_they->mym_comment))); ?></div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
			  <? if(isset($Sess_UserId)){ ?>
              <tr>
                <td class="rettext"><?php echo PRGRETUSER_THEYMATCH ?>&nbsp; <?php echo $myscore ?>&nbsp;<?php echo PRGRETUSER_MEMATCH ?>&nbsp;<?php echo $theyscore ?></td>
              </tr>
			  <? } ?>
            </table></td>
        </tr>
        <?
		/*<tr align="center">
          <td colspan="2" class="tdfoot">
            <?php
            if (isset($Sess_UserId))
                print ("<a href='$CONST_LINK_ROOT/search.php'>".SEARCH_NEW."</a>");
            if(can_navigate_back())
                print (" | <a href=\"".get_prev_page_url()."\">".get_back_link_name()."</a>");        
		?>
          </td>
        </tr>
		*/?>
      </table>
      <? $network = new Network();
      if ($option_manager->GetValue('snetwork') && $network->checkRelations($Sess_UserId,$advuser) == NETWORK_SINGLE_DUAL ){?>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="left" class="tdhead"><?php echo SOCIAL_NETWORK_SECTION_NAME ?></td>
        </tr>
        <tr>
          <td align="left" >
            <?=display_network_friends($advuser)?>
          </td>
        </tr>
      </table>
      <br>
      <? } ?>      
	 
		<?php		
		//assign "redirect to" to the panel
		$redirect_to = "prgretuser";		
		include('ratingmeetmatch.inc.php');
		?>			

    </td>
  </tr>
</table>


<?php 

//yoes 20140331
//The profile is now seen by a visitor
//only do this if member is online
//only do this if member is actually online
if ($sql_array->isOnline && $Sess_UserId != $sql_array->adv_userid) {
	$sql = "insert into view_profile_pending_ic value('".doCleanInput($Sess_UserId)."','".doCleanInput($userid)."','".doCleanInput($_SERVER['REMOTE_ADDR'])."',now())";
	
	//echo $sql;
	mysql_query($sql) ;
}

?>

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
_uacct = "UA-1634572-1";
urchinTracker();
</script>

<?=$skin->ShowFooter($area)?>
