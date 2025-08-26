<?php
include "db_connect.php";

$query = "


SELECT
    a.cid    
    , a.companycode
    , a.branchcode
    , a.companynamethai
    -- , a.province
    , p.province_name
	, CONCAT(
        COALESCE(a.address1, ''), 
        CASE WHEN a.moo != '' THEN CONCAT(' หมู่ ', a.moo) ELSE '' END,
        CASE WHEN a.soi != '' THEN CONCAT(' ซอย ', a.soi) ELSE '' END,
        CASE WHEN a.road != '' THEN CONCAT(' ถนน ', a.road) ELSE '' END,
        CASE WHEN a.subdistrict != '' THEN CONCAT(' ตำบล ', a.subdistrict) ELSE '' END,
        CASE WHEN a.district != '' THEN CONCAT(' อำเภอ ', a.district) ELSE '' END
    ) AS ที่อยู่
     , a.telephone as โทรศัพท์
	, a.email as อีเมล
	
    , b.employees as จำนวนลูกจ้าง2568
    , CASE 
        WHEN b.employees < 100 THEN 0
        ELSE CEILING(b.employees / 100.0)
      END AS อัตราส่วนคนพิการ
    , b.year
    , b.lawfulstatus
	, b.pay_status as มีการจ่ายเงินมาตรา34
    , b.Hire_NumofEmp AS จำนวนที่รายงานม33การแทนนับตามสิทธิที่ใช้
    , COALESCE(emp_count.actual_employees, 0) AS จำนวนลูกจ้างจริงการแทนนับคนจริง
    , COALESCE(curator_count.total_curators, 0) AS จำนวน_curatorม35
    -- , COALESCE(emp_count.actual_employees, 0) + COALESCE(curator_count.total_curators, 0) AS รวมทั้งหมด
FROM
    company a
    JOIN lawfulness b ON a.cid = b.cid AND b.year = 2025
    JOIN provinces p ON a.province = p.province_id
    LEFT JOIN (
        SELECT 
            le.le_cid,
            COUNT(*) AS actual_employees
        FROM 
            lawful_employees le
        WHERE 
            le.le_year = 2025
            AND NOT EXISTS (
                SELECT 1 
                FROM lawful_employees_meta lem1 
                WHERE lem1.meta_leid = le.le_id 
                AND lem1.meta_for = 'is_extra_33' 
                AND lem1.meta_value = 1
            )
            AND NOT EXISTS (
                SELECT 1 
                FROM lawful_employees_meta lem2 
                WHERE lem2.meta_leid = le.le_id 
                AND lem2.meta_for = 'child_of' 
                AND lem2.meta_value != 0
            )
        GROUP BY le.le_cid
    ) emp_count ON a.cid = emp_count.le_cid
    LEFT JOIN (
        SELECT 
            law.cid,
            COUNT(*) AS total_curators
        FROM 
            lawfulness law
            JOIN curator c ON law.lid = c.curator_lid
        WHERE 
            law.year = 2025
            AND c.curator_parent = 0
            AND NOT EXISTS (
                SELECT 1 
                FROM curator_meta cm1 
                WHERE cm1.meta_curator_id = c.curator_id 
                AND cm1.meta_for = 'child_of' 
                AND cm1.meta_value != 0
            )
            AND NOT EXISTS (
                SELECT 1 
                FROM curator_meta cm2 
                WHERE cm2.meta_curator_id = c.curator_id 
                AND cm2.meta_for = 'is_extra_35' 
                AND cm2.meta_value = 1
            )
        GROUP BY law.cid
    ) curator_count ON a.cid = curator_count.cid
WHERE
	1 = 1
    and CompanyTypeCode < 200
    and CompanyTypeCode != 14
    -- b.LawfulStatus = 1
    -- AND p.province_id = 1	
    /* AND (
        COALESCE(emp_count.actual_employees, 0) + COALESCE(curator_count.total_curators, 0) > CASE 
            WHEN b.employees < 100 THEN 0
            ELSE CEILING(b.employees / 100.0)
        END
    )*/
	-- and
	-- b.pay_status = 0
			


";

echo $query;

$result = mysql_query($query) or die(mysql_error());

// Start table with styling
echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
    <thead>
        <tr style="background-color: #f2f2f2;">';

// Add row number column header
echo '<th style="padding: 10px;">No.</th>';

// Display column headers
$first_row = mysql_fetch_assoc($result);
foreach($first_row as $column_name => $value) {
    echo '<th style="padding: 10px;">' . htmlspecialchars($column_name) . '</th>';
}
echo '</tr></thead><tbody>';

// Display first row data with row number
echo '<tr>';
echo '<td style="padding: 8px;">1</td>'; // First row number
foreach($first_row as $value) {
    echo '<td style="padding: 8px;">' . htmlspecialchars($value) . '</td>';
}
echo '</tr>';

// Display remaining rows with row numbers
$row_number = 2; // Start from 2 since we already displayed row 1
while($row = mysql_fetch_assoc($result)) {
    echo '<tr>';
    echo '<td style="padding: 8px;">' . $row_number . '</td>'; // Row number
    foreach($row as $value) {
        echo '<td style="padding: 8px;">' . htmlspecialchars($value) . '</td>';
    }
    echo '</tr>';
    $row_number++;
}

echo '</tbody></table>';

mysql_free_result($result);
?>