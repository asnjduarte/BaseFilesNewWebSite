<?php

//these values may need to change
$servername = "localhost:10070";
$table_prefix = "wp";
$username = "root";
$password = "root";
$dbname = "local";


$b1Name = "Maranatha Marketing Inc.";
$b1Addr = "45615 Tamihi Way";
$b1AddrNum = 51;
$b1Zip = "V2R 0X4";
$b1City = 10;
$b1CreateDate = date("Y-m-d");
$b1UpdateDate = date("Y-m-d");
$b1LastUpdatedBy = 1;
$b1IsActive = 1;

$b2Name = "Life 100";
$b2Addr = "NE 191 St., Ste. 500";
$b2AddrNum = "2875";
$b2Zip = 33180;
$b2City = 11;
$b2CreateDate = date("Y-m-d");
$b2UpdateDate = date("Y-m-d");
$b2LastUpdatedBy = 1;
$b2IsActive = 1;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//wp_business - x
//wp_business_contact - x
//wp_negotiation_role - x

//sp_update_business - x
//sp_insert_business - x
//sp_fetch_all_businesses - x
//sp_fetch_specific_business - x
//sp_fetch_country_by_city - x
//sp_fetch_all_active_business_contacts_by_company - x
//sp_fetch_specific_business_contact - x

//controller\BusinessesController.php
//controller\SpecificBusinessController.php
//js\ncnda.js
//js\ncndaForm.js
//model\business\Businesses.php
//view\policy\NcndaForm.php
//view\policy\NcndaInitialQuestion.php
//view\policy\NcndaView.php

$sql1 = "
DELIMITER ;;
CREATE PROCEDURE `sp_update_business`(IN `b_id` int, IN `b_n` varchar(50), IN `b_a` varchar(40), IN `b_ab` int, IN `b_z` varchar(20), IN `ct_od` int, IN `u_id` bigint, IN `ia` tinyint)
BEGIN
UPDATE . $table_prefix ._business SET 
  bName = b_n,
  bAddr= b_a,
  bAddrNum= b_ab,
  bZip= b_z,
  cityId= ct_od,
  lastUpdated = CURDATE(),
  updatedByUserId = u_id,
  isActive = ia
WHERE businessId = b_id;

SELECT CASE WHEN ROW_COUNT() > 0 THEN 1 ELSE 0 END AS updated;
END;;
";
array_push($sqlList, $sql1);

$sql2 = "
DELIMITER ;;
CREATE PROCEDURE `sp_insert_business`(IN `b_n` varchar(50), IN `b_a` varchar(40), IN `b_an` int, IN `b_z` varchar(20), IN `ct_id` int, IN `u_id` int)
BEGIN
  insert into . $table_prefix ._business(bName, bAddr, bAddrNum, bZip, cityId, createDate, lastUpdated, updatedByUserId, isActive) 
  values ( b_n, b_a, b_an,b_z, ct_id , CURDATE(), CURDATE(), u_id, 1);

SELECT LAST_INSERT_ID();
END;;
";
array_push($sqlList, $sql2);

$sql3 = "
DELIMITER ;;
CREATE PROCEDURE `sp_fetch_all_businesses`()
BEGIN
SELECT b.businessId, b.bName
FROM . $table_prefix ._business b
WHERE b.isActive = 1;
END;;
";
array_push($sqlList, $sql3);

$sql4 = "
DELIMITER ;;
CREATE PROCEDURE `sp_fetch_specific_business`(IN `b_id` int)
BEGIN
SELECT b.businessId, b.bName, b.bAddr, b.bAddrNum, b.bZip, b.cityId, b.isActive, c.countryId, c.name
FROM . $table_prefix ._business b LEFT JOIN . $table_prefix ._city ct 
  ON b.cityId = ct.cityId LEFT JOIN . $table_prefix ._state st
  ON ct.stateId = st.stateId LEFT JOIN . $table_prefix ._country c
  ON st.countryId = c.countryId
WHERE b.businessId = b_id;
END;;
";
array_push($sqlList, $sql4);

$sql5 = "
DELIMITER ;;
CREATE PROCEDURE `sp_fetch_country_by_city`(IN `ct_id` int)
BEGIN
SELECT c.countryId, c.name as countryName
FROM . $table_prefix ._city ct LEFT JOIN . $table_prefix ._state st
  ON ct.stateId= st.stateId LEFT JOIN . $table_prefix ._country c
  ON st.countryId = c.countryId
WHERE ct.cityId = ct_id;
END;;
";
array_push($sqlList, $sql5);

$sql6 = "
DELIMITER ;;
CREATE PROCEDURE `sp_fetch_all_active_business_contacts_by_company`(IN `b_id` int)
BEGIN
SELECT bc.businessContactId, bc.bcName, bc.businessId
FROM . $table_prefix ._business_contact bc
WHERE bc.isActive = 1 AND bc.businessId = b_id;
END;;
";
array_push($sqlList, $sql6);

$sql7 = "
DELIMITER ;;
CREATE PROCEDURE `sp_fetch_specific_business_contact`(IN `bc_id` int)
BEGIN
SELECT bc.businessContactId, bc.bcName, bc.bcNationality, bc.bcCompanyPosition, bc.bcCompanyPositionAbbr, bc.bcPhone, bc.bcPhone2, bc.bcEmail, bc.bcPassport, bc.isActive, bc.businessId 
FROM . $table_prefix ._business_contact bc
WHERE bc.businessContactId = bc_id;
END;;
";
array_push($sqlList, $sql7);

$sql8 = "
CREATE TABLE `. $table_prefix ._business` (
  `businessId` int(11) NOT NULL AUTO_INCREMENT,
  `bName` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `bAddr` varchar(40) NOT NULL,
  `bAddrNum` int(11) NOT NULL,
  `bZip` varchar(20) NOT NULL,
  `cityId` int(11) NOT NULL,
  `createDate` datetime NOT NULL,
  `lastUpdated` datetime NOT NULL,
  `updatedByUserId` bigint(20) unsigned NOT NULL,
  `isActive` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`businessId`),
  KEY `cityId` (`cityId`),
  KEY `updatedByUserId` (`updatedByUserId`),
  CONSTRAINT `. $table_prefix ._business_ibfk_1` FOREIGN KEY (`cityId`) REFERENCES `. $table_prefix ._city` (`cityId`),
  CONSTRAINT `. $table_prefix ._business_ibfk_2` FOREIGN KEY (`updatedByUserId`) REFERENCES `. $table_prefix ._users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql8);

$sql9 = "
CREATE TABLE `. $table_prefix ._business_contact` (
  `businessContactId` int(11) NOT NULL AUTO_INCREMENT,
  `bcName` varchar(35) NOT NULL,
  `bcNationality` varchar(35) NOT NULL,
  `bcCompanyPosition` varchar(35) NOT NULL,
  `bcCompanyPositionAbbr` varchar(6) NOT NULL,
  `bcPhone` varchar(20) NOT NULL,
  `bcPhone2` varchar(20) NOT NULL,
  `bcEmail` varchar(50) NOT NULL,
  `bcPassport` text NOT NULL,
  `businessId` int(11) NOT NULL,
  `createDate` datetime NOT NULL,
  `lastUpdated` datetime NOT NULL,
  `lastModifiedBy` bigint(20) unsigned NOT NULL,
  `isActive` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`businessContactId`),
  KEY `businessId` (`businessId`),
  KEY `lastModifiedBy` (`lastModifiedBy`),
  CONSTRAINT `. $table_prefix ._business_contact_ibfk_1` FOREIGN KEY (`businessId`) REFERENCES `. $table_prefix ._business` (`businessId`),
  CONSTRAINT `. $table_prefix ._business_contact_ibfk_3` FOREIGN KEY (`lastModifiedBy`) REFERENCES `. $table_prefix ._users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql9);

$sql10 = "
CREATE TABLE `. $table_prefix ._negotiation_role` (
  `negotiationRoleId` int(11) NOT NULL AUTO_INCREMENT,
  `nrName` varchar(11) NOT NULL,
  `nrDescription` text NOT NULL,
  PRIMARY KEY (`negotiationRoleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql10);

$sql11 = "
INSERT INTO `. $table_prefix ._business` (`businessId`, `bName`, `bAddr`, `bAddrNum`, `bZip`, `cityId`, `createDate`, `lastUpdated`, `updatedByUserId`, `isActive`) VALUES
(1,	'Maranatha Marketing Inc.',	'45615 Tamihi Way',	51,	'V2R 0X4',	10,	'2023-04-17 16:28:37',	'2023-04-17 16:28:37',	1,	1),
(2,	'Life 100',	'NE 191 St., Ste. 500',	2875,	'33180',	11,	'2023-04-18 16:34:03',	'2023-04-18 16:34:03',	1,	1);
";
array_push($sqlList, $sql11);

$sql12 = "
INSERT INTO `. $table_prefix ._business_contact` (`businessContactId`, `bcName`, `bcNationality`, `bcCompanyPosition`, `bcCompanyPositionAbbr`, `bcPhone`, `bcPhone2`, `bcEmail`, `bcPassport`, `businessId`, `createDate`, `lastUpdated`, `lastModifiedBy`, `isActive`) VALUES
(1,	'Luis L\'Hoist',	'Canadian',	'Chief Executive Officer',	'CEO',	'00-1-236-522-1696',	'52-1-415-153-5601',	'luislhoist@maranathamarketing.net',	'',	1,	'2023-04-19 08:00:19',	'2023-04-19 08:00:19',	1,	1),
(2,	'Juan Miguel',	'American',	'Chief Executive Officer',	'CEO',	'00-1-786-493-7513',	'',	'juanmiguel@maranathamarketing.net',	'',	2,	'2023-04-19 08:01:09',	'2023-04-19 08:01:09',	1,	1);
";
array_push($sqlList, $sql12);

$sql13 = "
INSERT INTO `. $table_prefix ._negotiation_role` (`negotiationRoleId`, `nrName`, `nrDescription`) VALUES
(1,	'Facilitator',	'Facilitators act as the intermediary between the buyer and seller.'),
(2,	'Buyer',	'The one who is buying the product.'),
(3,	'Seller',	'The one who is selling the product.');
";
array_push($sqlList, $sql13);

?>
