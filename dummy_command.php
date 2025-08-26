<?php

	include "db_connect.php";
	
	$query = "


update lawfulness_company set Employees = ( select COALESCE(sum(employees), 0 ) from company_employees_company where cid in ( select cid from company where companyCode = '1000544184' ) and lawful_year= '2025' ) + ( select COALESCE( sum( employees ) , 0 ) from company_company where year = 2025 and CompanyCode = '1000544184' ) where cid = '' and year = '2025' 

    ";



echo $query;

mysql_query($query) or die(mysql_error());

