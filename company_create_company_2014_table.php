<?php

include "db_connect.php";

$sql = "
CREATE TABLE IF NOT EXISTS company_2014_from_ictmerlin (
  `Employees` int(5) NOT NULL,
  `CompanyCode` varchar(255) NOT NULL,
  `CompanyNameThai` varchar(255) NOT NULL,
  `CompanyNameEng` varchar(255) NOT NULL,
  `Address1` text NOT NULL,
  `Moo` varchar(255) NOT NULL,
  `Soi` varchar(255) NOT NULL,
  `Road` varchar(255) NOT NULL,
  `Subdistrict` varchar(255) NOT NULL,
  `District` varchar(255) NOT NULL,
  `Province` int(11) NOT NULL,
  `Zip` varchar(255) NOT NULL,
  `Telephone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `TaxID` varchar(255) NOT NULL,
  `CompanyTypeCode` varchar(5) NOT NULL,
  `BranchCode` varchar(255) NOT NULL,
  `org_website` varchar(255) NOT NULL,
  `LastModifiedDateTime` datetime NOT NULL,
  `BusinessTypeCode` varchar(5) NOT NULL,
  `LawfulFlag` int(1) NOT NULL DEFAULT '2',
  `NoRecipientFlag` tinyint(1) NOT NULL,
  `LastModifiedBy` varchar(255) NOT NULL,
  `Status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0 = closed, 1 = open, 2 = moved',
  `ContactPerson1` text NOT NULL,
  `ContactPhone1` text NOT NULL,
  `ContactEmail1` text NOT NULL,
  `ContactPosition1` text NOT NULL,
  `ContactPerson2` text NOT NULL,
  `ContactPhone2` text NOT NULL,
  `ContactEmail2` text NOT NULL,
  `ContactPosition2` text NOT NULL,
 
  UNIQUE KEY `CompanyCode` (`CompanyCode`,`BranchCode`),
  KEY `CompanyTypeCode` (`CompanyTypeCode`),
  KEY `BusinessTypeCode` (`BusinessTypeCode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ";

mysql_query($sql) or die(mysql_error());

echo "table company_2014_from_ictmerlin created!";

?>