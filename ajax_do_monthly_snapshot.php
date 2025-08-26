<?php
include_once "db_connect.php";
include_once "functions.php";

// Get last month's last day date in YYYYMMDD format
$last_month_end = date('Ymd', strtotime('last day of last month'));

// Tables to snapshot
$tables_to_snapshot = array(
    'company',
    'lawfulness',
    'lawful_employees',
    'receipt',
    'curator'
);

// Function to check if snapshot exists
function checkSnapshotExists($table_name, $snapshot_suffix) {
    $check_sql = "SHOW TABLES LIKE '{$table_name}_snap_{$snapshot_suffix}'";
    $result = mysql_query($check_sql);
    return mysql_num_rows($result) > 0;
}

// Function to create snapshot table
function createSnapshot($table_name, $snapshot_suffix) {
    // Drop if exists
    $drop_sql = "DROP TABLE IF EXISTS {$table_name}_snap_{$snapshot_suffix}";
    mysql_query($drop_sql);

    // Create table and copy data in one statement
    $create_sql = "CREATE TABLE {$table_name}_snap_{$snapshot_suffix} 
                   SELECT * FROM {$table_name}";
    //echo $create_sql; exit();

    mysql_query($create_sql) or die(mysql_error());

    return true;
}


// Response array
$response = array(
    'success' => true,
    'messages' => array()
);

// Check if snapshots needed
$snapshots_needed = false;
foreach ($tables_to_snapshot as $table) {
    if (!checkSnapshotExists($table, $last_month_end)) {
        $snapshots_needed = true;
        break;
    }
}

if (!$snapshots_needed) {
    $response['messages'][] = "All snapshots for period ending {$last_month_end} already exist";
} else {
    // Process each table
    foreach ($tables_to_snapshot as $table) {
        try {
            // Check if snapshot exists
            if (!checkSnapshotExists($table, $last_month_end)) {
                // Create snapshot
                if (createSnapshot($table, $last_month_end)) {
                    $response['messages'][] = "Created snapshot {$table}_snap_{$last_month_end}";

                    // Cleanup old snapshots
                    //cleanupOldSnapshots($table);
                } else {
                    throw new Exception("Failed to create snapshot for {$table}_snap_{$last_month_end} - MySQL Error: " . mysql_error());
                }
            } else {
                $response['messages'][] = "Snapshot {$table}_snap_{$last_month_end} already exists";
            }
        } catch (Exception $e) {
            $response['success'] = false;
            $response['messages'][] = $e->getMessage();
        }
    }

    // Log snapshot creation
    if ($response['success']) {
        $log_sql = "INSERT INTO generic_log (log_type, log_date, log_meta) VALUES (
            'monthly_snapshot',
            NOW(),
            'Created snapshots for period ending " . $last_month_end . "'
        )";
        mysql_query($log_sql);
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);