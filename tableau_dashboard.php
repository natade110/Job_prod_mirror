<?php

	include "db_connect.php";
	
	$url = 'https://203.151.166.143:10443/trusted';
	$data = array('username' => "Administrator"); //, 'key2' => 'value2');
	
	$options = array(
		'http' => array(
			'header'  => "Content-type: application/x-www-form-urlencoded;charset=UTF-8\r\n",
			'method'  => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) { /* Handle error */ }

	var_dump($result);
	
	$tableau_key = $result;
	
	echo $tableau_key;
	
	
	//http://203.151.166.143:8000/views/mis_depfund_20201018/33?:showAppBanner=false&:display_count=n&:showVizHome=n&:origin=viz_share_link
	
?>
<div class="col-md-12" style="">
	<div class="card" style="">
		<div class="ui padded grid" style="height: 80vh; min-height:80vh;">
		
		<?php if($tableau_key > -1 && 1==1){?>...
		<script type='text/javascript' src='https://203.151.166.143:10443/javascripts/api/viz_v1.js'></script>
		<div class='tableauPlaceholder' style='width: 100%; height: 100%;'>
			<object class='tableauViz' width='100%' height='100%' style='display:none;'>
				<param name='host_url' value='https%3A%2F%2F203.151.166.143%3A10443%2F' />
				<param name='embed_code_version' value='3' />
				<param name='site_root' value='' />
				<?php if(1==1){?><param name='name' value='Superstore/Overview' /><?php }?>
				<?php if(1==0){?><param name='name' value='mis_depfund_20201018/33' /><?php }?>
				<param name='tabs' value='yes' />
				<param name='toolbar' value='yes' />
				<param name='showAppBanner' value='false' />
				<param name="ticket" value="<?php echo $tableau_key;?>" /> 
			</object>
		</div>
		<?php }?>
		</div>
	</div>
</div>