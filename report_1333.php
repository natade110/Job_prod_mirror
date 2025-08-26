<?php
require_once 'db_connect.php';
require_once 'session_handler.php';
require_once 'c2x_include.php';

if($_POST["report_format"] == "excel"){
    header("Content-type: application/ms-excel");
    header("Content-Disposition: attachment; filename=report_1333.xls");
    $is_excel = 1;
}elseif($_POST["report_format"] == "words"){
    header("Content-type: application/vnd.ms-word");
    header("Content-Disposition: attachment;Filename=report_1333.doc");
}elseif($_POST["report_format"] == "pdf"){
    $is_pdf = 1;
}else{
    header ('Content-type: text/html; charset=utf-8');
}

$the_year = "2011";
if(isset($_POST["ddl_year"]) && ($_POST["ddl_year"] != 0)){
    $the_year = $_POST["ddl_year"];
    $year_filter = " and lawfulness.Year = '$the_year'";
}

if($the_year >= 2013){
    $is_2013 = 1;
    $branch_codition = " AND company.BranchCode < 1 ";
}

// Filters
$province_filter = "";
if(isset($_POST["Province"]) && $_POST["Province"] != "" && $_POST["rad_area"] == "province"){
    $province_filter = " and company.Province = '".$_POST["Province"]."'";
}

if(isset($_POST["Section"]) && $_POST["Section"] != "" && $_POST["rad_area"] == "section"){
    $province_table = ", provinces";
    $province_filter = " and company.Province = provinces.province_id and provinces.section_id = '".$_POST["Section"]."'";
}

if($_POST["CompanyTypeCode"] == "14"){

    //$typecode_filter = " and CompanyTypeCode = '14'";
    $business_type = "หน่วยงานภาครัฐ";

}else{
    //$typecode_filter = " and CompanyTypeCode != '14'";
    $business_type = "สถานประกอบการ";
}

///yoes 201300813 - add GOV only filter
if($sess_accesslevel == 6 || $sess_accesslevel == 7){

    $typecode_filter = " and (company.CompanyTypeCode = '14'";
    $typecode_filter .= " or company.CompanyTypeCode >= 200  or company.CompanyTypeCode < 300)";

}else{
    $typecode_filter = " and company.CompanyTypeCode != '14'";
    $typecode_filter .= " and company.CompanyTypeCode < 200";

}

// Date filters if needed
if($_POST["date_from_year"] > 0 && $_POST["chk_from"]){
    $filter_from = " and ReceiptDate >= '{$_POST["date_from_year"]}-{$_POST["date_from_month"]}-{$_POST["date_from_day"]} 00:00:01'";
}

if($_POST["date_to_year"] > 0 && $_POST["chk_from"]){
    $filter_to = " and ReceiptDate <= '{$_POST["date_to_year"]}-{$_POST["date_to_month"]}-{$_POST["date_to_day"]} 23:59:59'";
}



$querySQL = "
    SELECT 
        company.CompanyCode,
        lawfulness.Year,
        company.CompanyNameThai,
        company.CompanyTypeCode,
        company.Address1 as company_address1,
        company.Moo as company_moo,
        company.Soi as company_soi,
        company.Road as company_road,
        company.Subdistrict as company_subdistrict,
        company.District as company_district,
        company.Province as company_province,
        company.Zip as company_zip,
        cyc.Address1 as contact_address1,
        cyc.Moo as contact_moo,
        cyc.Soi as contact_soi,
        cyc.Road as contact_road,
        cyc.Subdistrict as contact_subdistrict,
        cyc.District as contact_district,
        cyc.Province as contact_province,
        cyc.Zip as contact_zip,
        receipt.ReceiptNo,
        receipt.BookReceiptNo,
        payment.PaymentDate,
        receipt.ReceiptDate,
        receipt.Amount as PaidAmount
    FROM company
    JOIN lawfulness ON company.CID = lawfulness.CID  
    JOIN payment ON lawfulness.LID = payment.LID
    JOIN receipt ON payment.RID = receipt.RID
    LEFT JOIN company_by_year_company cyc ON 
        company.CID = cyc.CID 
        AND lawfulness.Year = cyc.year 
        AND cyc.row_type = 1
    WHERE 
        1=1
        $year_filter
        $province_filter
        $branch_codition
        $typecode_filter   
        $filter_from
        $filter_to
        AND payment.PaymentMethod = 'WS'
    ORDER BY payment.PaymentDate DESC";

//echo $querySQL;

$queryResult = mysql_query($querySQL);
?>

<div align="center">
    <strong>รายงานที่อยู่จัดส่งใบเสร็จกรณีชำระผ่านระบบ e-service  <?php

        if($year_filter) {
            echo " ประจำปี ".formatYear($the_year);
        }else{

            echo " ข้อมูลทุกปี ";

        }

        // เพิ่มการแสดงช่วงวันที่ถ้ามีการกรอง
        if($_POST["chk_from"]) {
            $date_from = $_POST["date_from_year"]."-".$_POST["date_from_month"]."-".$_POST["date_from_day"];
            $date_to = $_POST["date_to_year"]."-".$_POST["date_to_month"]."-".$_POST["date_to_day"];

            echo "<br>ข้อมูลระหว่างวันที่ ".formatDateThai($date_from)." ถึง ".formatDateThai($date_to);
        }

        ?></strong>
    <br>
</div>

<table border="1" align="center" cellpadding="5" cellspacing="0" style="border-collapse:collapse;font-size:<?php echo !$is_pdf ? 14:28; ?>px">
    <thead>
    <tr bgcolor="#d3d3d3">
        <td align="center"><strong>ลำดับที่</strong></td>
        <td align="center"><strong>ปีการปฏิบัติตามกฎหมาย</strong></td>
        <td align="center"><strong>ชื่อที่อยู่สถานประกอบการ</strong></td>
        <td align="center"><strong>เลขบัญชีนายจ้าง</strong></td>
        <td align="center"><strong>ที่อยู่สำหรับส่งใบเสร็จ</strong></td>
        <td align="center"><strong>เลขที่ใบเสร็จ</strong></td>
        <td align="center"><strong>เล่มที่ใบเสร็จ</strong></td>
        <td align="center"><strong>วันที่ชำระ</strong></td>
        <td align="center"><strong>วันที่ออกใบเสร็จ</strong></td>
        <td align="center"><strong>ยอดเงิน</strong></td>
    </tr>
    </thead>
    <tbody>
    <?php
    $seq = 0;
    while ($row = mysql_fetch_array($queryResult)) {
        $seq++;

        // Format full address
        // สร้าง array เพื่อส่งให้ getAddressText()
        $is_contact_address = false;
        if ($row['contact_address1']) { // ถ้ามีที่อยู่จาก company_by_year_company
            $address_to_use = array(
                'Address1' => $row['contact_address1'],
                'Moo' => $row['contact_moo'],
                'Soi' => $row['contact_soi'],
                'Road' => $row['contact_road'],
                'Subdistrict' => $row['contact_subdistrict'],
                'District' => $row['contact_district'],
                'Province' => $row['contact_province'],
                'Zip' => $row['contact_zip']
            );
            $is_contact_address = true;
        } else { // ใช้ที่อยู่จาก company
            $address_to_use = array(
                'Address1' => $row['company_address1'],
                'Moo' => $row['company_moo'],
                'Soi' => $row['company_soi'],
                'Road' => $row['company_road'],
                'Subdistrict' => $row['company_subdistrict'],
                'District' => $row['company_district'],
                'Province' => $row['company_province'],
                'Zip' => $row['company_zip']
            );
        }

        $companyAddress = getAddressText($address_to_use);

        ?>
        <tr>
            <td align="center"><?php echo $seq; ?></td>
            <td align="center"><?php echo $row["Year"]+543; ?></td>
            <td align="left"><?php echo formatCompanyName($row["CompanyNameThai"],$row["CompanyTypeCode"]);?></td>
            <td align="center"><?php echo $row["CompanyCode"]; ?></td>
            <td align="left"><?php
                if($is_contact_address) {
                    echo '<span style="color:blue;">'.$companyAddress.'</span>';
                } else {
                    echo $companyAddress;
                }
                ?></td>
            <td align="center"><?php echo $row["ReceiptNo"]; ?></td>
            <td align="center"><?php echo $row["BookReceiptNo"]; ?></td>
            <td align="center"><?php echo formatDateThai($row["PaymentDate"]); ?></td>
            <td align="center"><?php echo formatDateThai($row["ReceiptDate"]); ?></td>
            <td align="right"><?php echo formatNumber($row["PaidAmount"]); ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<div align="right">ข้อมูล ณ วันที่ <?php echo formatDateThai(date("Y-m-d"));?></div>