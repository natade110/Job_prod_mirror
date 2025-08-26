<?php

$DEBT_INTEREST_RATE = 7.5;

define('WARNING_YEAR_RANGE', 4);
// Web Site URL without trailing slash
define('WEB_URL', 'http://122.155.197.65');

$SEQUESTRATION_TYPE = (object)array('Money'=>1, 'Property'=>2, 'Car' => 3, 'Other' => 4);

$COMPANY_LAW_STATUS = (object)array(
		'ไม่เข้าข่าย'=>0, 
		'ยังไม่ดำเนินการ'=>1, 
		'แจ้งโนติส' =>2, 
		'อายัดทรัพย์สิน' => 3, 
		'ถอนอายัด' => 31,
		'ส่งพนักงานอัยการ'=>4, 
		'ศาลสั่งฟ้อง'=>5,
		'ยื่นขอรับชำระหนี้ล้มละลาย'=>6,
		'ชำระหนี้ครบแล้ว'=>9);

$USER_ACCESS_LEVEL = (object)array(
		'เจ้าหน้าที่_พมจ'=>3,
		'เจ้าหน้าที่_พก'=>2,		
		'ผู้ดูแลระบบ' => 1,
		'ผู้บริหาร'=>5,
		'เจ้าหน้าที่สถานประกอบการ'=>4,
		'ผู้ดูแลระบบ_สศส'=>6,
		'เจ้าหน้าที่_สศส' => 7,
		'เจ้าหน้าที่งานคดี' => 8
);

$PROCEEDINGSTATUS_STATUS = (object)array(
		'ดำเนินการ'=>1, 
		'ศาลสั่งฟ้อง' =>2, 
		'ศาลยกฟ้อง'=>9);

$PROCEEDING_TYPE = (object)array(
		'ส่งพนักงานอัยการ'=>1,
		'ยื่นขอชำระหนี้ล้มละลาย' =>2,
		'ศาลสั่งฟ้อง'=> 3
);

$LAWFUL_STATUS =  (object)array(
		'ทำตามกฏหมาย' => 1,
		'ไม่ทำตามกฏหมาย' => 0,
		'ปฏิบัติตามกฎหมายแต่ไม่ครบตามอัตราส่วน' => 2,
		'ไม่เข้าข่ายจำนวนลูกจ้าง' => 3
);

$SEQUESTRATION_DOCUMENT_TYPE = (object)array(
		'แจ้งโนติส' => 4,
		'อายัดทรัพย์สิน' => 5,
		'ถอนอายัด' => 6,
		'ส่งพนักงานอัยการ' => 1,
		'ศาลสั่งฟ้อง' => 3,
		'ยื่นขอรับชำระหนี้ล้มละลาย' => 2 
);


define('LAWSTATUS_LOG_CHANGE_TYPE_ADD', 'add');
define('LAWSTATUS_LOG_CHANGE_TYPE_DELETE', 'delete');
define('LAWSTATUS_LOG_CHANGE_TYPE_CANCELLED_ADD', 'cancelled add');

define('LAWSTATUS_LOG_DOCUMENT_TYPE_COMPANY', 'company');
define('LAWSTATUS_LOG_DOCUMENT_TYPE_RECEIPT', 'receipt');
define('LAWSTATUS_LOG_DOCUMENT_TYPE_NOTICE', 'noticedocument');
define('LAWSTATUS_LOG_DOCUMENT_TYPE_SEQUESTRATION', 'sequestration');
define('LAWSTATUS_LOG_DOCUMENT_TYPE_PROCEEDING', 'proceedings');
define('LAWSTATUS_LOG_DOCUMENT_TYPE_CANCELLED_SEQUESTRATION', 'cancelledsequestration');

define('BANGKOK_PROVINCE_CODE', '10');

