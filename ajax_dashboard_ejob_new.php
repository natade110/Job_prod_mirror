<?php

include_once "db_connect.php";
require_once 'c2x_constant.php';
require_once 'c2x_function.php';

$func = $_POST["func"]?$_POST["func"]:$_GET["func"];

if($func){
  $the_limit = 10;
  $the_end_year = ($_POST["the_end_year"]?$_POST["the_end_year"]:$_GET["the_end_year"])*1;
  $the_limit = ($_POST["the_limit"]?$_POST["the_limit"]:$_GET["the_limit"])*1;
  $the_limit = $the_limit ? $the_limit: 10;
}

// Ajax Call Function
if($func =="get_org_on_submit") getOrgOnlineSubmit($the_end_year,$the_limit);
if($func =="get_approval_list") getApprovalList($the_end_year,$the_limit);
if($func =="get_payment_result") getPaymentResult($the_end_year,$the_limit);
if($func =="get_request_add_m34") getRequestAddM34($the_end_year,$the_limit);
if($func =="get_request_edit_m34") getRequestEditM34($the_end_year,$the_limit);
if($func =="get_company_dup") getCompanyDup($the_end_year,$the_limit);
if($func =="get_company_missing_m33_m35") getCompanyMissingM33M35($the_end_year,$the_limit);



if($sess_accesslevel == 3){
 //$user_filter_sql = " and 1=0";
 //yoes 20160118 - special for พมจ users
 //echo "----->". $sess_can_manage_user . "<---";
 //can see users under own province

 $user_filter_sql = " and b.Province = '$sess_meta'";
 $user_filter_sql_approval = " and z.Province = '$sess_meta'";
}

//yoes 20161201 -- pmj only see Bangkok
 if($sess_accesslevel == 2){
 //$user_filter_sql = " and 1=0";
 //yoes 20160118 - special for พมจ users
 //echo "----->". $sess_can_manage_user . "<---";
 //can see users under own province

 $user_filter_sql = " and b.Province = '1'";
 $user_filter_sql_approval = " and z.Province = '1'";
}

if($_POST["zone_id"] && ($sess_accesslevel == 1 || $sess_accesslevel == 5)){

  $my_zone = $_POST["zone_id"];
  $extra_link .= "&zone_id=".($my_zone*1)."";

}

//echo $my_zone;

//yoes 20160118 -- make it so
if($my_zone){

  //build sql for this zone
  $zone_sql = "

    and
    (
      z.District in (

        select
          district_name
        from
          districts
        where
          district_area_code
          in (

            select
              district_area_code
            from
              zone_district
            where
              zone_id = '$my_zone'

          )

      )
      or
      z.district_cleaned in (

        select
          district_name
        from
          districts
        where
          district_area_code
          in (

            select
              district_area_code
            from
              zone_district
            where
              zone_id = '$my_zone'

          )

      )
    )


  ";

  //echo $zone_sql; exit();

}

//$user_filter_sql_approval = ($_POST["user_filter_sql_approval"]?$_POST["user_filter_sql_approval"]:$_GET["user_filter_sql_approval"])*1;
//$zone_sql = ($_POST["zone_sql"]?$_POST["zone_sql"]:$_GET["zone_sql"])*1;







function getOrgOnlineSubmit($the_end_year,$the_limit=10,$limit_start=0,$return_count=false) {
  if($the_limit == -1)
    $this_limit = "";
  else
    $this_limit = "limit $limit_start,".($the_limit+1);
  if($return_count)
    $select_field = "COUNT(*) as c";
  else
    $select_field = "
      z.CID
      , Province
      , CompanyCode
      , CompanyTypeName
      , z.CompanyTypeCode
      , CompanyNameThai
      , province_name
      , LawfulFlag
      , y.LawfulStatus as lawfulness_status
      , y.Employees as lawful_employees
      , xxx.lawful_submitted_on
    ";

  $sql = "


									SELECT
									   $select_field
									FROM
										company z
											LEFT outer JOIN companytype b
												ON z.CompanyTypeCode = b.CompanyTypeCode
											LEFT outer JOIN provinces c
												ON z.province = c.province_id
											JOIN lawfulness y
												ON z.CID = y.CID and y.Year = '$the_end_year'

											left join lawfulness_company xxx
												on z.CID = xxx.CID and xxx.Year = '$the_end_year'

									where
										1=1
										and z.CompanyTypeCode < 200
										and BranchCode < 1
										and xxx.Year = '$the_end_year'
										and (xxx.lawful_submitted = '1')

										$province_sql


									order by lawful_submitted_on asc

									$this_limit

									";
                  $submit_result = mysql_query($sql);
                  if($return_count){
                      $post_row = mysql_fetch_array($submit_result);
                      echo $post_row["c"];

                  } else {
                    $c = 1;
                    // Table Header
                    echo '<tr class="dashboard_new_border_bottom">';
                    echo '  <td valign="middle" >&nbsp;</td>';
                    echo '  <td valign="middle" class="dashboard_new">&nbsp;</td>';
                    echo '  <td width="10%" valign="middle" class="dashboard_new_small" style="text-align: right"> วันที่ยื่น</td>';
                    echo '</tr>';
                    while($post_row = mysql_fetch_array($submit_result)){
                      if($c <= $the_limit || $the_limit == -1){
						$dataUrl = 'data-url="organization.php?id='.$post_row["CID"].'&all_tabs=1&year='.$the_end_year.'"';
                        echo '<tr class="dashboard_new_border_bottom hover" '.$dataUrl.'>';
    										echo '	<td width="0%" valign="middle" >';
    										//echo '		<img src="decors/green.gif" border="0" alt="ทำตามกฏหมาย" title="ทำตามกฏหมาย">';
                        echo getLawfulImage(($post_row["lawfulness_status"]));
    										echo '	</td>';
    										echo '	<td width="90%" valign="middle" class="dashboard_new">';
    										echo '		<span style="color: blue; " >'.$post_row["province_name"].':</span> ';
    										echo formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]);
    										echo '	</td>';
    										echo '	<td width="10%" valign="middle" class="dashboard_new">';
    										echo '		<div align="right" style="color: blue; font-size: 14px;">';
                        echo formatDateThaiShort($post_row['lawful_submitted_on']);
    										echo '		</div>';
    										echo '	</td>';
    										echo '</tr>';
                      } else {
                        echo '<tr class="dashboard_new_border_bottom">';
    										echo '	<td width="0%" valign="middle" colspan="3">';
                        echo '<div align="center" tyle="color: blue; font-size: 14px;">';
                        echo "<a href=\"#\" onclick=\"getAllData('get_org_on_submit'); return false;\">-- ดูรายการทั้งหมด-- </a>";
    										echo '</div></td>';
    										echo '</tr>';
                      }
                      $c++;

                    }
                  }
}


function getApprovalList($the_end_year,$the_limit=10,$limit_start=0,$return_count=false){
  global $user_filter_sql_approval,$zone_sql;
  if($the_limit == -1)
    $this_limit = "";
  else
    $this_limit = "limit $limit_start,".($the_limit+1);
  if($return_count)
    $select_field = "COUNT(*) as c";
  else
    $select_field = "
      a.user_id
      , a.user_created_date
      , a.user_name
      , a.user_created_date
      , z.CompanyNameThai
      , z.CompanyTypeCode
      , p.province_name
      , (select count(*) from files where file_type in ('register_doc_1', 'register_doc_22', 'register_employee_card', 'register_company_card') and file_for=a.user_id) as count_self_doc
    ";

  $sql = "
  SELECT $select_field
    FROM users a left outer join company z on a.user_meta = z.cid
      left join provinces p on z.Province=p.province_id
    where 1=1 and user_enabled like '%0%' and AccessLevel like '%4%'
  $user_filter_sql_approval
  $zone_sql
  order by user_id asc
  $this_limit

  									";

                    $submit_result = mysql_query($sql);
                    if($return_count){
                        $post_row = mysql_fetch_array($submit_result);
                        echo $post_row["c"];

                    } else {
                      $c = 1;
                      // Table Header
                      echo '<tr class="dashboard_new_border_bottom">';
  										echo '  <td width="50%" valign="middle" class="dashboard_new"></td>';
  									  echo '  <td width="20%" valign="middle" class="dashboard_new_small">User Name</td>';
  									  echo '  <td width="20%" valign="middle" class="dashboard_new_small">จำนวนเอกสารยืนยัน</td>';
  										echo '  <td width="10%" valign="middle" class="dashboard_new_small" style="text-align: right">วันที่สมัคร</td>';
                      echo '</tr>';
                      while($post_row = mysql_fetch_array($submit_result)){
                        if($c <= $the_limit || $the_limit == -1){
							$dataUrl = 'data-url="view_user.php?id='.$post_row[user_id].'"';
                          echo '<tr class="dashboard_new_border_bottom hover" '.$dataUrl.'>      ';
      										echo '  <td width="50%" valign="middle" class="dashboard_new">';
                          echo '		<span style="color: blue; " >'.$post_row["province_name"].':</span> ';
      										echo formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]);
      										echo '  </td>      ';
      										echo '  <td width="20%" valign="middle" class="dashboard_new">';
      										echo $post_row[user_name];
      										echo '  </td>';
      										echo '  <td width="20%" valign="middle" class="dashboard_new">';
      										echo '	  <font color="'.($post_row[count_self_doc] < 4? "red":"green").'">'.$post_row[count_self_doc].'/4</font>';
      										echo '  </td>      ';
      										echo '  <td width="10%" valign="middle" class="dashboard_new">';
      										echo '	  <div align="right" style="color: blue; font-size: 14px;">';
                          echo formatDateThaiShort($post_row['user_created_date']);
      										echo '	  </div>';
      										echo '  </td>';
      									  echo '</tr>';
                        } else {
                          echo '<tr class="dashboard_new_border_bottom">';
      										echo '	<td width="0%" valign="middle" colspan="4">';
                          echo '<div align="center" tyle="color: blue; font-size: 14px;">';
                          echo "<a href=\"#\" onclick=\"getAllData('get_approval_list'); return false;\">-- ดูรายการทั้งหมด-- </a>";
      										echo '</div></td>';
      										echo '</tr>';
                        }
                        $c++;

                      }
                    }


}

function getPaymentResult($the_end_year,$the_limit=10,$limit_start=0,$return_count=false){
  global $user_filter_sql_approval,$zone_sql;
  $paymentMethodMapping = getPaymentMethodMapping();
  if($the_limit == -1)
    $this_limit = "";
  else
    $this_limit = "limit $limit_start,".($the_limit+1);
  if($return_count)
    $select_field = "COUNT(*) as c";
  else
    $select_field = "
    b.ServiceRef1,
    b.ServiceRef2,
    c.CompanyCode,
    c.BranchCode,
    c.CompanyNameThai,
    law.Year,
    b.PaymentDate,
    c.Province,
    p.province_name,
    c.CID,
    b.TransactionPrincipalAmount,
    b.TransactionInterestAmount,
    b.TransactionTotalAmount,
    b.PaidTotalAmount,
    b.PaymentMethod,
    ba.bank_name BankName,
    b.ChequeNo,
    COALESCE(r.BookReceiptNo,cp.BookReceiptNo) BookReceiptNo,
    COALESCE(r.ReceiptNo,cp.ReceiptNo) ReceiptNo,
    b.PaymentStatus,
    b.KTBImportDate,
    b.KTBCancelDate,
    b.NEPFundExportDate,
    b.NEPFundImportDate,
    b.NEPFundCancelDate,
    law.LawfulStatus as lawfulness_status,
    b.ReceiptID    ";

  $sql = "
  SELECT
  $select_field
  FROM bill_payment b
  JOIN lawfulness law ON law.LID = b.LID
  JOIN company c ON law.CID = c.CID
  LEFT JOIN bank ba ON ba.BankCode = b.ChequeBankCode
  LEFT JOIN receipt r ON r.RID = b.ReceiptID
  LEFT JOIN cancelled_payment cp ON cp.NEPFundPaymentID = b.NEPFundPaymentID AND b.ReceiptID IS NULL AND b.NEPFundPaymentID IS NOT NULL
  LEFT JOIN provinces p ON c.Province = p.province_id

  where
    b.PaymentStatus = 1

  ORDER BY b.PaymentDate DESC
  ";

  $submit_result = mysql_query($sql);
  if($return_count){
      $post_row = mysql_fetch_array($submit_result);
      echo $post_row["c"];

  } else {
    $c = 1;
    // Table Header
    echo '<tr class="dashboard_new_border_bottom">';
    echo '  <td width="50%" valign="middle" class="dashboard_new"></td>';
    echo '  <td width="10%" valign="middle" class="dashboard_new_small">เล่มที่-เลขที่ใบเสร็จ</td>';
    echo '  <td width="10%" valign="middle" class="dashboard_new_small">สถานะ</td>';
    echo '  <td width="10%" valign="middle" class="dashboard_new_small" style="text-align: right">ยอดเงินที่จ่าย</td>';
    echo '  <td width="10%" valign="middle" class="dashboard_new_small" style="text-align: right">จ่ายโดย</td>';
    echo '  <td width="10%" valign="middle" class="dashboard_new_small" style="text-align: right">วันที่</td>';
    echo '</tr>';
    while($post_row = mysql_fetch_array($submit_result)){
      if($c <= $the_limit || $the_limit == -1){
		$dataUrl = 'data-url="view_payment.php?id='.$post_row[ReceiptID].'"';
        echo '<tr class="dashboard_new_border_bottom hover" '.$dataUrl.'>      ';
        echo '  <td width="50%" valign="middle" class="dashboard_new">';
        echo '		<span style="color: blue; " >'.$post_row["province_name"].':</span> ';
        echo formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]);
        echo '  </td>      ';
        echo '  <td width="10%" valign="middle" class="dashboard_new">';
        echo '<a href="view_payment.php?id='.$post_row[ReceiptID].'" target="_blank">'.$post_row["BookReceiptNo"].'-'.$post_row["ReceiptNo"].'</a>';
        echo '  </td>';
        echo '  <td width="10%" valign="middle" class="dashboard_new">';
        echo getLawfulImage(($post_row["lawfulness_status"]));
        echo '  </td>';
        echo '  <td width="10%" valign="middle" class="dashboard_new">';
        echo formatNumber($post_row["PaidTotalAmount"]);
        echo '  </td>';
        echo '  <td width="10%" valign="middle" class="dashboard_new">';
        echo $paymentMethodMapping[$post_row["PaymentMethod"]];
        echo '  </td>      ';
        echo '  <td width="10%" valign="middle" class="dashboard_new">';
        echo '	  <div align="right" style="color: blue; font-size: 14px;">';
        echo formatDateThaiShort($post_row["PaymentDate"],1,0);
        echo '	  </div>';
        echo '  </td>';
        echo '</tr>';
      } else {
        echo '<tr class="dashboard_new_border_bottom">';
        echo '	<td width="0%" valign="middle" colspan="6">';
        echo '<div align="center" tyle="color: blue; font-size: 14px;">';
        echo "<a href=\"#\" onclick=\"getAllData('get_payment_result'); return false;\">-- ดูรายการทั้งหมด-- </a>";
        echo '</div></td>';
        echo '</tr>';
      }
      $c++;

    }
  }

}

function getRequestAddM34($the_end_year,$the_limit=10,$limit_start=0,$return_count=false){
  global $user_filter_sql_approval,$zone_sql,$sess_accesslevel;
  if($the_limit == -1)
    $this_limit = "";
  else
    $this_limit = "limit $limit_start,".($the_limit+1);
  if($return_count)
    $select_field = "COUNT(*) as c";
  else
    $select_field = "
    *, (select concat(FirstName, ' ', LastName) from users where user_id = a.request_userid) as request_name
  ";

  if($sess_accesslevel == 3){

    //provincial staff only see self-provice
    $request_filter = " and d.Province = '$sess_meta'";

  }

  $sql = "

  SELECT
    $select_field
  FROM
    payment_request a
      join
        receipt_request b
      on
        a.RID = b.RID

      join lawfulness c
        on
        a.LID = c.LID

      join company d
        on
        c.CID = d.CID
      left join users u on a.request_userid = u.user_id
  where
    request_status = 0

    $request_filter
  ";


  $submit_result = mysql_query($sql);
  if($return_count){
      $receipt_row = mysql_fetch_array($submit_result);
      echo $receipt_row["c"];

  } else {
    $c = 1;
    // Table Header
    echo '<tr class="dashboard_new_border_bottom">';
    echo '  <td width="20%" valign="middle" class="dashboard_new">เล่มที่ใบเสร็จ</td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small">เลขที่ใบเสร็จ</td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small">ข้อมูลปี</td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small" style="text-align: right">ประเภทการขอ</td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small" style="text-align: right">ผู้ขอ</td>';
    echo '</tr>';
    while($receipt_row = mysql_fetch_array($submit_result)){
      if($c <= $the_limit || $the_limit == -1){
		  $dataUrl = 'data-url="view_payment.php?id='.$receipt_row[RID].'&view=request"';
        echo '<tr class="dashboard_new_border_bottom hover" '.$dataUrl.'>      ';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo '<a href="view_payment.php?id='.$receipt_row[RID].'&view=request" target="_blank">'.$receipt_row[BookReceiptNo].'</a>';
        echo '  </td>      ';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo '<a href="view_payment.php?id='.$receipt_row[RID].'&view=request" target="_blank">'.$receipt_row[ReceiptNo].'</a>';
        echo '  </td>';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo $receipt_row[ReceiptYear]+543;
        echo '  </td>';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo "เพิ่มใบเสร็จ";
        echo '  </td>';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo $receipt_row[request_name];
        echo '  </td>      ';
        echo '</tr>';
      } else {
        echo '<tr class="dashboard_new_border_bottom">';
        echo '	<td width="0%" valign="middle" colspan="6">';
        echo '<div align="center" tyle="color: blue; font-size: 14px;">';
        echo "<a href=\"#\" onclick=\"getAllData('get_request_add_m34'); return false;\">-- ดูรายการทั้งหมด-- </a>";
        echo '</div></td>';
        echo '</tr>';
      }
      $c++;

    }
  }



}

function getRequestEditM34($the_end_year,$the_limit=10,$limit_start=0,$return_count=false){
  global $user_filter_sql_approval,$zone_sql;
  if($the_limit == -1)
    $this_limit = "";
  else
    $this_limit = "limit $limit_start,".($the_limit+1);
  if($return_count)
    $select_field = "COUNT(*) as c";
  else
    $select_field = "
    b.RID
    , b.BookReceiptNo
    , b.ReceiptNo
    , b.ReceiptYear
    , b.edit_userid
    , b.Amount as edit_type
  ";

  $sql = "
  SELECT
    $select_field
  FROM

    payment a

      join
        receipt_edit_request b
      on
        a.RID = b.RID


      join lawfulness c
        on
        a.LID = c.LID

      join company d
        on
        c.CID = d.CID

  where
    b.edit_status = 0

    $request_filter
  ";

  $submit_result = mysql_query($sql);
  if($return_count){
      $request_row = mysql_fetch_array($submit_result);
      echo $request_row["c"];

  } else {
    $c = 1;
    // Table Header
    echo '<tr class="dashboard_new_border_bottom hover">';
    echo '  <td width="20%" valign="middle" class="dashboard_new">เล่มที่ใบเสร็จ</td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small">เลขที่ใบเสร็จ</td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small">ข้อมูลปี</td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small" style="text-align: right">ประเภทการขอ</td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small" style="text-align: right">ผู้ขอ</td>';
    echo '</tr>';
    while($request_row = mysql_fetch_array($submit_result)){
      if($c <= $the_limit || $the_limit == -1){		  
        $receipt_row = getFirstRow("select * from receipt where RID = '".$request_row[RID]."'");
		$dataUrl = 'data-url="view_payment.php?id='.$receipt_row[RID].'"';
        echo '<tr class="dashboard_new_border_bottom hover" '.$dataUrl.'>      ';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo '<a href="view_payment.php?id='.$receipt_row[RID].'" target="_blank">'.$receipt_row[BookReceiptNo].'</a>';
        echo '  </td>      ';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo '<a href="view_payment.php?id='.$receipt_row[RID].'" target="_blank">'.$receipt_row[ReceiptNo].'</a>';
        echo '  </td>';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo $receipt_row[ReceiptYear]+543;
        echo '  </td>';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo ($request_row[edit_type]? "แก้ไขใบเสร็จ" : "ยกเลิกใบเสร็จ");
        echo '  </td>';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo getFirstItem("select concat(FirstName, ' ', LastName) from users where user_id = '".$request_row[edit_userid]."'") ;
        echo '  </td>      ';
        echo '</tr>';
      } else {
        echo '<tr class="dashboard_new_border_bottom">';
        echo '	<td width="0%" valign="middle" colspan="6">';
        echo '<div align="center" tyle="color: blue; font-size: 14px;">';
        echo "<a href=\"#\" onclick=\"getAllData('get_request_edit_m34'); return false;\">-- ดูรายการทั้งหมด-- </a>";
        echo '</div></td>';
        echo '</tr>';
      }
      $c++;

    }
  }

}

function getCompanyDup($the_end_year,$the_limit=10,$limit_start=0,$return_count=false){
  global $user_filter_sql_approval,$zone_sql;
  if($the_limit == -1)
    $this_limit = "";
  else
    $this_limit = "limit $limit_start,".($the_limit+1);
  if($return_count)
    $select_field = "COUNT(*) as c";
  else
    $select_field = "
      the_code
  ";

  $sql = "
        select
          $select_field
        from

        (
        select
        le_code as the_code
        , le_name as the_name
        , 'l' as the_type

        from
        lawful_employees

        where le_year = '$the_end_year'

        union

        select
        curator_idcard as the_code
        , curator_name as the_name
        , 'c' as the_type
        from
        curator, lawfulness
        where
        curator_lid = lid
        and
        Year = '$the_end_year'

        and
        curator_is_disable = 1


        )                    a


        group by the_code
        having count(the_code) > 1

        order by the_code asc

        $this_limit
  ";

  $submit_result = mysql_query($sql);
  if($return_count){
      $post_row = mysql_fetch_array($submit_result);
      echo $post_row["c"];

  } else {
    $c = 1;
    // Table Header
    echo '<tr class="dashboard_new_border_bottom">';
    echo '  <td width="50%" valign="middle" class="dashboard_new"></td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small">เลขที่บัตรประชาชนที่ซ้ำซ้อน</td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small">มาตราที่ซ้ำซ้อน</td>';
    echo '  <td width="10%" valign="middle" class="dashboard_new_small" style="text-align: right">สถานะ</td>';
    echo '</tr>';
    while($post_row = mysql_fetch_array($submit_result)){
      if($c <= $the_limit || $the_limit == -1){
        $sub_sql = "


													select
															a.cid
															, CompanyNameThai
															, CompanyTypeCode
															, Province
															, LawfulStatus
															, le_code
															, '33' as the_type
													  from
														company a
															join lawfulness b

															  on
																a.cid = b.cid
																and
																b.year = '$the_end_year'

															join
																lawful_employees c
																	on a.cid = c.le_cid
																	and
																	b.year = c.le_year

													  where
													  	le_code = '".$post_row["the_code"]."'
														and
														CompanyTypeCode != '14'
														and
														CompanyTypeCode < 200


													union


													select
															a.cid
															, CompanyNameThai
															, CompanyTypeCode
															, Province
															, LawfulStatus
															, curator_idcard as le_code
															, '35' as the_type
													  from
														company a
															join lawfulness b

															  on
																a.cid = b.cid
																and
																b.year = '$the_end_year'

															join
																curator c
																	on b.lid = c.curator_lid


													  where
													  	curator_idcard = '".$post_row["the_code"]."'
														and
														curator_parent = 0
														and
														CompanyTypeCode != '14'
														and
														CompanyTypeCode < 200




												";
          $sub_result = mysql_query($sub_sql);
          while($sub_row = mysql_fetch_array($sub_result)){
			  $dataUrl = 'data-url="organization.php?id='.$sub_row[cid].'&year='.$the_end_year.'"';
            echo '<tr class="dashboard_new_border_bottom hover" '.$dataUrl.'>      ';
            echo '  <td width="50%" valign="middle" class="dashboard_new">';
            echo '		<span style="color: blue; " >';
            echo getFirstItem("select province_name from provinces where province_id = '".$sub_row["Province"]."'");
            echo ':</span> ';
            echo formatCompanyName($sub_row["CompanyNameThai"],$sub_row["CompanyTypeCode"]);
            echo '  </td>      ';
            echo '  <td width="20%" valign="middle" class="dashboard_new">';
            echo $sub_row["le_code"];
            echo '  </td>';
            echo '  <td width="20%" valign="middle" class="dashboard_new">';
            echo ($sub_row["the_type"] == "33"?"มาตรา 33":"มาตรา 35");
            echo '  </td>      ';
            echo '  <td width="10%" valign="middle" class="dashboard_new">';
            echo getLawfulImage(($sub_row["LawfulStatus"]));
            echo '  </td>';
            echo '</tr>';
          }
      } else {
        echo '<tr class="dashboard_new_border_bottom">';
        echo '	<td width="0%" valign="middle" colspan="4">';
        echo '<div align="center" tyle="color: blue; font-size: 14px;">';
        echo "<a href=\"#\" onclick=\"getAllData('get_company_dup'); return false;\">-- ดูรายการทั้งหมด-- </a>";
        echo '</div></td>';
        echo '</tr>';
      }
      $c++;

    }
  }
}

function getCompanyMissingM33M35($the_end_year,$the_limit=10,$limit_start=0,$return_count=false){
  global $user_filter_sql_approval,$zone_sql;
  if($the_limit == -1)
    $this_limit = "";
  else
    $this_limit = "limit $limit_start,".($the_limit+1);
  if($return_count)
    $select_field = "COUNT(*) as c";
  else
    $select_field = "
    z.CID , Province , CompanyCode , CompanyTypeName, z.CompanyTypeCode , CompanyNameThai , province_name
    , LawfulFlag , y.LawfulStatus as lawfulness_status , y.Employees as lawful_employees
    , y.Year as lawful_year
    , y.lid as lawful_id
  ";

  $sql = "
  SELECT

  $select_field

  FROM
    company z
      LEFT outer JOIN companytype b
        ON z.CompanyTypeCode = b.CompanyTypeCode
      LEFT outer JOIN provinces c
        ON z.province = c.province_id
      JOIN lawfulness y
        ON z.CID = y.CID and y.Year = '$the_end_year'


      where

      z.CompanyTypeCode < 200
      and BranchCode < 1
      and
      (
        z.cid in (


          select
            distinct(le_cid)
          from
            lawful_employees
          where
            le_is_dummy_row = 1
            and
            le_year > 2012
            and
            le_year < 3000

        )

        or

        y.lid in (

          select
            curator_lid
          from
            lawfulness aa
              join
                curator bb
              on aa.lid = bb.curator_lid
              and
              aa.Year > 2012
              and
              aa.Year < 3000
          where
            curator_is_dummy_row = 1

        )

      )


  $province_sql


  order by CompanyNameThai asc

  ";

  $submit_result = mysql_query($sql);
  if($return_count){
      $post_row = mysql_fetch_array($submit_result);
      echo $post_row["c"];

  } else {
    $c = 1;
    // Table Header
    echo '<tr class="dashboard_new_border_bottom">';
    echo '  <td width="50%" valign="middle" class="dashboard_new"></td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small"></td>';
    echo '  <td width="20%" valign="middle" class="dashboard_new_small">ข้อมูลปี</td>';
    echo '  <td width="10%" valign="middle" class="dashboard_new_small" style="text-align: right">สถานะ</td>';
    echo '</tr>';
    while($post_row = mysql_fetch_array($submit_result)){
      if($c <= $the_limit || $the_limit == -1){
        $the_year_array = array();
        $years = "";

        $sql = "select
              distinct(le_year)
            from
              lawful_employees
            where
              le_is_dummy_row = 1
              and
              le_cid = '".$post_row["CID"]."'
              and
              le_year > 2012
              and
              le_year < 3000
            ";

        //echo $sql;

        $dummy_result = mysql_query($sql);

        while ($dummy_row = mysql_fetch_array($dummy_result)){

            array_push($the_year_array, $dummy_row[le_year]);

        }

        $sql = "select
              distinct(a.Year)  as le_year
            from
              lawfulness a
                join
                  curator b
                on
                  a.lid = b.curator_lid
            where
              curator_is_dummy_row = 1
              and
              a.CID = '".$post_row["CID"]."'
              and
              a.Year > 2012
              and
              a.Year < 3000
            ";

        //echo $sql;

        $dummy_result = mysql_query($sql);

        while ($dummy_row = mysql_fetch_array($dummy_result)){

            array_push($the_year_array, $dummy_row[le_year]);

        }

        $the_year_array = array_unique($the_year_array);

        //print_r($the_year_array);


        for($iii=0;$iii<count($the_year_array);$iii++){


          if($iii >= 1){
            $years .= "<br>";
          }

          $years .= ' <a href="organization.php?id='. doCleanOutput($post_row["CID"]) .'&all_tabs=1&year='. $the_year_array[$iii] .'">';

          $years .= $the_year_array[$iii]+543;

          $years .= "</a>";
        }
		$dataUrl = 'data-url="organization.php?id='.$post_row[CID].'&all_tabs=1"';
        echo '<tr class="dashboard_new_border_bottom hover" '.$dataUrl.'>      ';
        echo '  <td width="50%" valign="middle" class="dashboard_new">';
        echo '		<span style="color: blue; " >';
        echo getFirstItem("select province_name from provinces where province_id = '".$post_row["Province"]."'");
        echo ':</span> ';
        echo formatCompanyName($post_row["CompanyNameThai"],$post_row["CompanyTypeCode"]);
        echo '  </td>      ';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        //echo $post_row[user_name];
        echo '  </td>';
        echo '  <td width="20%" valign="middle" class="dashboard_new">';
        echo $years;
        echo '  </td>      ';
        echo '  <td width="10%" valign="middle" class="dashboard_new">';
        echo '	  <div align="right" style="color: blue; font-size: 14px;">';
        echo getLawfulImage(($post_row["lawfulness_status"]),"_grey");
        echo '	  </div>';
        echo '  </td>';
        echo '</tr>';
      } else {
        echo '<tr class="dashboard_new_border_bottom">';
        echo '	<td width="0%" valign="middle" colspan="4">';
        echo '<div align="center" tyle="color: blue; font-size: 14px;">';
        echo "<a href=\"#\" onclick=\"getAllData('get_company_missing_m33_m35'); return false;\">-- ดูรายการทั้งหมด-- </a>";
        echo '</div></td>';
        echo '</tr>';
      }
      $c++;

    }
  }

}

?>
