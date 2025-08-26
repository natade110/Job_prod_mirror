<?php

include_once "db_connect.php";
require_once 'c2x_constant.php';
require_once 'c2x_function.php';
//$post_data = utf8_encode($_POST["msg"]);
$msg = json_decode($_POST["msg"]);

if($msg->func){
  $data[msg]          = $msg->msg;
  $data[name]         = $msg->name;
  $data[email]        = $msg->email;
  $data[corp_name]    = $msg->corp_name;
  $data[corp_code]    = $msg->corp_code;
  $data[userid]       = $sess_userid;
  $data[lid]          = $msg->lid;
  $data[cid]          = $msg->cid;
  $data[year]         = $msg->year;
}

// Ajax Call Function
if($msg->func =="sendMessage") sendMessage($data);
if($msg->func =="sendReject") sendReject($data);


function sendMessage($d){
	
	
	
	//yoes 20211212
	
	$lawful_row = getFirstRow("select * from lawfulness_company WHERE cid='".$d[cid]."' and year='".$d[year]."'");
	$sql = "
	
	insert into
		ejob_remarks(
		
			ejr_datetime
			, ejr_remarks
			, ejr_from
			, ejr_to
			, ejr_ejob_lid
			
			, ejr_lid
			, ejr_created_date
		)
		values(
		
			now()		
			, '".doCleanInput($d[msg])."'
			, '".$d[userid]."'
			, '".$d[cid]."' 
			, '".$lawful_row[LID]."'
			
			, 0
			, now()
			
		)
  
  ";
  
  mysql_query($sql);
	
	//yoes 20190123
  if(!$d[email]){
	  
	$d[email] = getFirstItem("
				
				
					select
						user_email
					from
						users
					where
						user_enabled = '1'
						and
						user_meta = '".$d[cid]."'
						and
						AccessLevel = 4
	
				");
	  
  }
	
  $subject = "[ระบบรายงานผลการจ้างงานคนพิการ] ข้อความติดต่อจากผู้ดูแลระบบ";
  $msg_body = "
  เรียน บริษัท ".$d[corp_name]." จำกัด
 <br> เลขที่บัญชีนายจ้าง ".$d[corp_code]."

  <br><br>คุณได้รับข้อความติดต่อจากผู้ดูแลระบบ ระบบรายงานผลการจ้างงานคนพิการ สำหรับสถานประกอบการ ดังต่อไปนี้:

  <br><br>".$d[msg]."

  <br><br>ขอแสดงความนับถือ
  <br>กองกองทุนและส่งเสริมความเสมอภาคคนพิการ
  <br>โทรศัพท์ 02-106-9300, 02-106-9327-31
  ";


  logMessage($d,"message");
  doSEndMail($d[email],$subject,$msg_body);
  returnJson(0);
}


function sendReject($d){
	
	//yoes 20190123
  if(!$d[email]){
	  
	$d[email] = getFirstItem("
				
				
					select
						user_email
					from
						users
					where
						user_enabled = '1'
						and
						user_meta = '".$d[cid]."'
						and
						AccessLevel = 4
	
				");
	  
  }
	
  $subject = "[ระบบรายงานผลการจ้างงานคนพิการ] เจ้าหน้าที่ปฏิเสธข้อมูลการปฏิบัติตามกฏหมาย";
  $msg_body = "
  เรียน บริษัท ".$d[corp_name]." จำกัด
  <br>เลขที่บัญชีนายจ้าง ".$d[corp_code]."

  <br><br>เจ้าหน้าที่ได้ปฏิเสธข้อมูลการปฏิบัติตามกฎหมายที่ท่านส่งเข้ามาผ่าน ระบบรายงานผลการจ้างงานคนพิการ สำหรับสถานประกอบการ โดยมีรายละเอียดดังต่อไปนี้:

  <br><br>".$d[msg]."

  <br><br>ขอแสดงความนับถือ
  
  ";
  
  
  //yoes 20220310 --> change message for company outside of Bangkok
  $company_row = getFirstRow("select * from company join provinces on Province = province_id where cid = '".$d[cid]."'");
  
  if($company_row["Province"] != 1){
	 

	//yoes 20220316 --> get contact number
	$province_contact_details = getFirstItem("select province_contact_details from provinces where province_id = '".$company_row["Province"]."'");
	  
	  
	$msg_body .= "<br>หากมีข้อสงสัยกรุณาติดต่อ สำนักงานพัฒนาสังคมและความมั่นคงของมนุษย์จังหวัด".$company_row["province_name"];
	$msg_body .= "<br>โทรศัพท์ $province_contact_details";
	
	
  }else{
	  
	//yoes 20220316 --> get contact number
	$province_contact_details = getFirstItem("select province_contact_details from provinces where province_id = '".$company_row["Province"]."'");
	  
	$msg_body .= "<br>กองกองทุนและส่งเสริมความเสมอภาคคนพิการ
		<br>โทรศัพท์ $province_contact_details";
  }


  logMessage($d,"reject_message");
  doSEndMail($d[email],$subject,$msg_body);
  
  //yoes 20211015
	
	$lawful_row = getFirstRow("select * from lawfulness_company WHERE cid='".$d[cid]."' and year='".$d[year]."'");
	
	
	doLawfulnessCompanyFullLog($d[userid], $lawful_row[LID], "ajax_send_message_to_company.php");
	
	if($lawful_row[lawful_submitted] == 3){
		
		//$sql = "UPDATE lawfulness_company SET lawful_submitted=0 WHERE cid='".$d[cid]."' and year='".$d[year]."'";
		
		$sql = "
		
			replace into lawfulness_meta(

				meta_lid
				, meta_for
				, meta_value



			) values ( 

				'".$lawful_row[LID]."'
				, 'es-resubmit'
				, '0'

			), ( 

				'".$lawful_row[LID]."'
				, 'es-resubmit-rejected'
				, '1'

			), ( 

				'".$lawful_row[LID]."'
				, 'es-resubmit-rejected-datetime'
				, now()

			), ( 

				'".$lawful_row[LID]."'
				, 'es-resubmit-rejected-remarks'
				, '".doCleanInput($d[msg])."'

			)


		";

		//echo $sql; exit();

		mysql_query($sql) or die(mysql_error());
		//mysql_query($sql);
		
	}else{
		$sql = "UPDATE lawfulness_company SET lawful_submitted=0 WHERE cid='".$d[cid]."' and year='".$d[year]."'";
		mysql_query($sql);
	}
  
  
  //yoes 20211212
  $sql = "
	
	insert into
		ejob_remarks(
		
			ejr_datetime
			, ejr_remarks
			, ejr_from
			, ejr_to
			, ejr_ejob_lid
			
			, ejr_lid
			, ejr_created_date
		)
		values(
		
			'".$lawful_row[lawful_submitted_on]."'			
			, (
			
				select
					lawful_remarks
				from
					lawfulness_company
				where
					cid='".$d[cid]."' 
					and year='".$d[year]."'			
			)
			, '".$d[cid]."'
			, '".$d[userid]."'			 
			, '".$lawful_row[LID]."'
			
			, 0
			, now()
			
		)
  
  ";
  
  mysql_query($sql);
  
  
  $sql = "
	
	insert into
		ejob_remarks(
		
			ejr_datetime
			, ejr_remarks
			, ejr_from
			, ejr_to
			, ejr_ejob_lid
			
			, ejr_lid
			, ejr_created_date
		)
		values(
		
			now()		
			, '".doCleanInput($d[msg])."'
			, '".$d[userid]."'
			, '".$d[cid]."' 
			, '".$lawful_row[LID]."'
			
			, 0
			, now()
			
		)
  
  ";
  
  mysql_query($sql);
  
  //lawful_remarks = CONCAT('ข้อความจากเจ้าหน้าที่ ".date("d-m-").(date("Y")+543).": ".doCleanInput($d[msg])."\r\n',lawful_remarks)
  
  $sql = "UPDATE 
			lawfulness_company 
		SET 
			lawful_remarks = ''
		WHERE 
			cid='".$d[cid]."' 
			and year='".$d[year]."'
		"			
			;
  mysql_query($sql);
  
  
  returnJson(0);
}

function logMessage($d,$msg_type){
	
  $sql = "INSERT INTO lawfulness_messages (msg_sender_userid,	msg_sender_ip,msg_datetime,msg_message,msg_recipient_email,	msg_type) VALUES (";
  $sql .= "'".$d[userid]."',";
  $sql .= "'".$_SERVER['REMOTE_ADDR']."',";
  $sql .= "now(),";
  $sql .= "'".$d[cid]."|".$d[lid].$d[corp_name].$d[corp_code].$d[msg]."',";
  $sql .= "'".$d[email]."',";
  $sql .= "'".$msg_type."'";
  $sql .= ")";
  mysql_query($sql);
}

function returnJson($code){
  header('Content-Type: application/json');
  echo json_encode(array("return_code"=>$code));
}

function sendMessagePrintJS(){
?>


<script>
 
  
	//alert("ssssaaaas");
 
	 function SendRejectToCompany(){
		 
		 var msg = $("#reject_remark").val();
		  if(!msg)
			alert("ท่านยังไม่ได้ กรอกเหตุผล คำแนะนำ ในการกรอกข้อมูลของสถานประกอบการ");
		  else {
			if(confirm("ต้องการปฏิเสธข้อมูลจากสถานประกอบการนี้?")){
			  var corp_name = $("input[name='CompanyNameThai']").val();
			  var corp_code = $('#CompanyCode').val();
			  $("#reject_remark").prop("disabled", true );
			  $("#btnSendRejectToCompany").prop("disabled", true );
			  var msg_data = {
				'func' : 'sendReject',
				'corp_name' : $("input[name='CompanyNameThai']").val(),
				'corp_code' : $('#CompanyCode').val(),
				'msg'       : msg,
				'email'     : $("#contact_email").text(),
				'name'      : $("#contact_name").text(),
				'lid'       : $("input[name='the_lid']").val(),
				'cid'       : $("input[name='the_cid']").val(),
				'year'       : $("input[name='the_year']").val()
			  }
			  $.ajax({
							type: "POST",
							url: "ajax_send_message_to_company.php",
							data: {msg: JSON.stringify(msg_data)},
							cache: false,
				  dataType: 'json',
							success: function(json){
					$("#reject_remark").val('');
					$("#reject_remark").prop("disabled", false );
					$("#btnSendRejectToCompany").prop("disabled", false );
								alert("ทำการปฏิเสธข้อมูลจากสถานประกอบการเรียบร้อยแล้ว");
							}
					});
			}
		  }
	 }
	 
	 function SendMessageToCompany(){
	 
		var msg = $("#comments_remark").val();
		  if(!msg)
			alert("ท่านยังไม่ได้ กรอกข้อความที่ต้องการส่งให้สถานประกอบการ");
		  else {
			if(confirm("ต้องการส่งข้อความให้สถานประกอบการนี้?")){
			  var corp_name = $("input[name='CompanyNameThai']").val();
			  var corp_code = $('#CompanyCode').val();
			  $("#comments_remark").prop("disabled", true );
			  $("#btnSendMessageToCompany").prop("disabled", true );
			  var msg_data = {
				'func' : 'sendMessage',
				'lid'       : $("input[name='the_lid']").val(),
				'cid'       : $("input[name='the_cid']").val(),
				'corp_name' : $("input[name='CompanyNameThai']").val(),
				'corp_code' : $('#CompanyCode').val(),
				'msg'       : msg,
				'email'     : $("#contact_email").text(),
				'name'      : $("#contact_name").text(),
				'year'       : $("input[name='the_year']").val()
			  }
			  $.ajax({
							type: "POST",
							url: "ajax_send_message_to_company.php",
							data: {msg: JSON.stringify(msg_data)},
							cache: false,
				  dataType: 'json',
							success: function(json){
					$("#comments_remark").val('');
					$("#comments_remark").prop("disabled", false );
					$("#btnSendMessageToCompany").prop("disabled", false );
								alert("ส่งข้อความแจ้งสถานประกอบการเรียบร้อยแล้ว");
							}
					});

			}
		  }

	 }
 
	$("#msg_contact_name").text($("#contact_name").text());
	$("#msg_contact_email").text($("#contact_email").text());

	$("#reject_remark").on("change keyup paste",function() {
	if(!$.trim($(this).val()))
		$("#btnSendRejectToCompany").prop("disabled", true );
	else
		$("#btnSendRejectToCompany").prop("disabled", false);
	});

//$("#btnSendRejectToCompany").prop("disabled", true );

  
</script>

<?php

}


?>
