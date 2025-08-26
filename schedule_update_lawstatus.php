<?php

//function for Company user to create new branch

require_once "db_connect.php";
require_once 'c2x_include.php';

$date = new DateTime();
$date->add(new DateInterval('P3M'));
$currentYear = intval($date->format("Y"));
$warningYear = $currentYear - WARNING_YEAR_RANGE;
$updatedby = getScheduleUserId();

//Insert LawStatus Log
$query = "
INSERT INTO company_lawstatus_log
	(`CID`, `ForYear`, `DocumentID`, `DocumentType`, `PreviousLawStatus`,
	 `ActualLawStatus`, `CalculatedLawStatus`, `ChangeType`, `UpdatedDate`, `UpdatedBy`)
SELECT C.CID, D.Year ForYear, C.CID DocumentID, 'company' DocumentType, c.LawStatus PreviousLawStatus,
		1 ActualLawStatus, 1 CalculatedLawStatus, 'add' ChangeType, NOW() UpdatedDate, '$updatedby' UpdatedBy
FROM company C 
INNER JOIN (
		SELECT 
			l.CID, MIN(Year) Year
		FROM lawfulness l
		WHERE 
			l.Year <= $warningYear
			AND LawfulStatus IN (0,2)
		Group By l.CID
) D on C.CID = D.CID
WHERE 
	C.LawStatus IN (0, 9)";

$resultInsertLog = mysql_query($query);
if ($resultInsertLog === false){
	
}

$query = "
UPDATE company C 
INNER JOIN (
		SELECT 
			l.CID, MIN(Year) Year
		FROM lawfulness l
		WHERE 
			l.Year <= $warningYear
			AND LawfulStatus IN (0,2)
		Group By l.CID
) D on C.CID = D.CID
SET C.LawStatus = 1, C.BeginLitigationYear = D.Year
WHERE 
	C.LawStatus IN (0, 9)
";

$resultUpdateLawStatus = mysql_query($query);
if ($resultUpdateLawStatus === false){
	
}
