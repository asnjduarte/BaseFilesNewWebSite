<?php

//these values may need to change
$servername = "localhost:10077";
$table_prefix = "wp";
$username = "root";
$password = "root";
$dbname = "local";


$b1Name = "x";
$b1Addr = "x";
$b1AddrNum = 1;
$b1Zip = "x";
$b1City = 1;
$b1CreateDate = date("Y-m-d");
$b1UpdateDate = date("Y-m-d");
$b1LastUpdatedBy = 1;
$b1IsActive = 1;

$b2Name = "x";
$b2Addr = "x";
$b2AddrNum = "1";
$b2Zip = 1;
$b2City = 1;
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

$sqlList = array();
$sql1 = "
CREATE PROCEDURE `sp_update_business`(IN `b_id` int, IN `b_n` varchar(50), IN `b_a` varchar(40), IN `b_ab` int, IN `b_z` varchar(20), IN `ct_od` int, IN `u_id` bigint, IN `ia` tinyint)
BEGIN
UPDATE ". $table_prefix . "_business SET 
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
CREATE PROCEDURE `sp_insert_business`(IN `b_n` varchar(50), IN `b_a` varchar(40), IN `b_an` int, IN `b_z` varchar(20), IN `ct_id` int, IN `u_id` int)
BEGIN
  insert into ". $table_prefix . "_business(bName, bAddr, bAddrNum, bZip, cityId, createDate, lastUpdated, updatedByUserId, isActive) 
  values ( b_n, b_a, b_an,b_z, ct_id , CURDATE(), CURDATE(), u_id, 1);

SELECT LAST_INSERT_ID();
END;;
";
array_push($sqlList, $sql2);

$sql3 = "
CREATE PROCEDURE `sp_fetch_all_businesses`()
BEGIN
SELECT b.businessId, b.bName
FROM ". $table_prefix . "_business b
WHERE b.isActive = 1;
END;;
";
array_push($sqlList, $sql3);

$sql4 = "
CREATE PROCEDURE `sp_fetch_specific_business`(IN `b_id` int)
BEGIN
SELECT b.businessId, b.bName, b.bAddr, b.bAddrNum, b.bZip, b.cityId, b.isActive, c.countryId, c.name
FROM ". $table_prefix . "_business b LEFT JOIN ". $table_prefix . "_city ct 
  ON b.cityId = ct.cityId LEFT JOIN ". $table_prefix . "_state st
  ON ct.stateId = st.stateId LEFT JOIN ". $table_prefix . "_country c
  ON st.countryId = c.countryId
WHERE b.businessId = b_id;
END;;
";
array_push($sqlList, $sql4);

$sql5 = "
CREATE PROCEDURE `sp_fetch_country_by_city`(IN `ct_id` int)
BEGIN
SELECT c.countryId, c.name as countryName
FROM ". $table_prefix . "_city ct LEFT JOIN ". $table_prefix . "_state st
  ON ct.stateId= st.stateId LEFT JOIN ". $table_prefix . "_country c
  ON st.countryId = c.countryId
WHERE ct.cityId = ct_id;
END;;
";
array_push($sqlList, $sql5);

$sql6 = "
CREATE PROCEDURE `sp_fetch_all_active_business_contacts_by_company`(IN `b_id` int)
BEGIN
SELECT bc.businessContactId, bc.bcName, bc.businessId
FROM ". $table_prefix . "_business_contact bc
WHERE bc.isActive = 1 AND bc.businessId = b_id;
END;;
";
array_push($sqlList, $sql6);

$sql7 = "
CREATE PROCEDURE `sp_fetch_specific_business_contact`(IN `bc_id` int)
BEGIN
SELECT bc.businessContactId, bc.bcName, bc.bcNationality, bc.bcCompanyPosition, bc.bcCompanyPositionAbbr, bc.bcPhone, bc.bcPhone2, bc.bcEmail, bc.bcPassport, bc.isActive, bc.businessId 
FROM ". $table_prefix . "_business_contact bc
WHERE bc.businessContactId = bc_id;
END;;
";
array_push($sqlList, $sql7);

$sql8 = "
CREATE TABLE `". $table_prefix . "_business` (
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
  CONSTRAINT `". $table_prefix . "_business_ibfk_1` FOREIGN KEY (`cityId`) REFERENCES `". $table_prefix . "_city` (`cityId`),
  CONSTRAINT `". $table_prefix . "_business_ibfk_2` FOREIGN KEY (`updatedByUserId`) REFERENCES `". $table_prefix . "_users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql8);

$sql9 = "
CREATE TABLE `". $table_prefix . "_business_contact` (
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
  CONSTRAINT `". $table_prefix . "_business_contact_ibfk_1` FOREIGN KEY (`businessId`) REFERENCES `". $table_prefix . "_business` (`businessId`),
  CONSTRAINT `". $table_prefix . "_business_contact_ibfk_3` FOREIGN KEY (`lastModifiedBy`) REFERENCES `". $table_prefix . "_users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql9);

$sql10 = "
CREATE TABLE `". $table_prefix . "_negotiation_role` (
  `negotiationRoleId` int(11) NOT NULL AUTO_INCREMENT,
  `nrName` varchar(11) NOT NULL,
  `nrDescription` text NOT NULL,
  PRIMARY KEY (`negotiationRoleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql10);

$sql11 = "
INSERT INTO `". $table_prefix . "_business` (`businessId`, `bName`, `bAddr`, `bAddrNum`, `bZip`, `cityId`, `createDate`, `lastUpdated`, `updatedByUserId`, `isActive`) VALUES
(1,	'Maranatha Marketing Inc.',	'45615 Tamihi Way',	51,	'V2R 0X4',	10,	'2023-04-17 16:28:37',	'2023-04-17 16:28:37',	1,	1),
(2,	'Life 100',	'NE 191 St., Ste. 500',	2875,	'33180',	11,	'2023-04-18 16:34:03',	'2023-04-18 16:34:03',	1,	1);
";
array_push($sqlList, $sql11);

$sql12 = "
INSERT INTO `". $table_prefix . "_business_contact` (`businessContactId`, `bcName`, `bcNationality`, `bcCompanyPosition`, `bcCompanyPositionAbbr`, `bcPhone`, `bcPhone2`, `bcEmail`, `bcPassport`, `businessId`, `createDate`, `lastUpdated`, `lastModifiedBy`, `isActive`) VALUES
(1,	'Luis L\'Hoist',	'Canadian',	'Chief Executive Officer',	'CEO',	'00-1-236-522-1696',	'52-1-415-153-5601',	'luislhoist@maranathamarketing.net',	'',	1,	'2023-04-19 08:00:19',	'2023-04-19 08:00:19',	1,	1),
(2,	'Juan Miguel',	'American',	'Chief Executive Officer',	'CEO',	'00-1-786-493-7513',	'',	'juanmiguel@maranathamarketing.net',	'',	2,	'2023-04-19 08:01:09',	'2023-04-19 08:01:09',	1,	1);
";
array_push($sqlList, $sql12);

$sql13 = "
INSERT INTO `". $table_prefix . "_negotiation_role` (`negotiationRoleId`, `nrName`, `nrDescription`) VALUES
(1,	'Facilitator',	'Facilitators act as the intermediary between the buyer and seller.'),
(2,	'Buyer',	'The one who is buying the product.'),
(3,	'Seller',	'The one who is selling the product.');
";
array_push($sqlList, $sql13);

$sql14 = "
INSERT INTO `". $table_prefix . "_generic_policy_terms` (`genericPolicyTermId`, `name`, `description`, `createDate`, `updateDate`, `lastUpdatedByUserId`) VALUES
(5,	'AGREEMENT NOT TO DEAL WITHOUT CONSENT',	'The intending parties hereby legally, and irrevocably bind themselves into guarantee to each other that they shall not directly or indirectly interfere with, circumvent or attempt to circumvent, avoid, by-pass or obviate each others’ interest or the interest or relationship between “The Parties” with procedures, seller, buyers, brokers, dealers, distributors, refiners, shippers, financial instructions, technology owners or manufacturers, to change, increase or avoid directly or indirectly payments of established or to be established fees, commissions, or  continuance of pre-established relationship or intervene in un-contracted relationships with man-ufacturers or technology owners with intermediaries entrepreneurs, legal council or initiate buy/sell relationship or transactional relationship that by-passes one of “The Parties” to one another in connection with any ongoing and future transaction or project.',	'2023-04-12 15:46:23',	'2023-04-12 15:46:23',	1),
(6,	'AGREEMENT NOT TO DISCLOSE',	'“The Parties” irrevocably agree that they shall not disclose or otherwise reveal directly or indirectly to a third party any confidential information provided by one party to the other or otherwise acquired, particularly contract terms, product information or manufacturing processes, prices, fees, financial agreement, schedules and information concerning the identity of the sellers, producers, buyers, lenders, borrowers, brokers, distributors, refiners, manufacturers, technology owners, or their representative and specifically individuals names, addresses, principals, or telex/fax/telephone numbers, references product or technology information and/or other information advised by one party(s) to be one another as being confidential or privileged without prior specific written consent of the party(s) providing such information.',	'2023-04-12 15:46:41',	'2023-04-12 15:46:41',	1),
(7,	'AGREEMENT TO HONOR COMMISSIONS',	'Commissions, fees, compensation or remuneration to be paid as part of transaction covering “The Parties” to this agreement, shall be agreed upon by separate written agreement by “The Parties” concerned and shall be paid at the time such contract designated, concluded or monies changing hands between buyers and sellers, unless otherwise agreed among “The Parties”.\r\n<br/>\r\n<br/>\r\n“The Parties” hereby irrevocably and unconditionally agree and guarantee to honor and respect all such fees and remuneration, arrangements made as part of a commission transaction even in the event that “The Parties” are not an integral member to a specific commission and fee, remuneration agreement.',	'2023-04-12 15:47:06',	'2023-04-12 15:47:06',	1),
(8,	'AGREEMENT TO INFORM',	'In specific deals where one of “The Parties” acting as an agent allows the buyer or buyer’s mandate, and the seller to deal directly with one another, the agent shall be informed of the development of the transactions by receiving copies of the correspondence made between the buyer or buyer’s mandate and the seller.',	'2023-04-12 15:47:26',	'2023-04-12 15:47:26',	1),
(9,	'TERM',	'This agreement shall be valid for five (5) years commencing from the date of this agreement.\r\nThis agreement has an option to renew for a further period of five (5) years subject to and upon the terms and conditions agreed between both parties.\r\n<br/>\r\n<br/>\r\nThis agreement shall apply to:\r\n<br/>\r\n<ul>\r\n<li>All transactions originated during the term of this agreement.</li>\r\n<li>All subsequent transactions that are follow up, repeat, extended or renegotiated transactions of transactions originated during the term of this agreement.</li>\r\n</ul>\r\n',	'2023-04-12 15:49:36',	'2023-04-12 15:49:36',	1),
(10,	'ARBITRATION',	'All disputes arising out of or in connection with the present contract shall be finally settled under the rules of arbitration of the “International Chamber of Commerce (ICC)” by one or more “Arbitrators” appointed in accordance with the said rules.\r\n<br/>\r\n<br/>\r\nEvery award shall be binding on “The Parties” and enforceable at law.\r\n<br/>\r\n<br/>\r\nBy submitting the dispute to arbitration under these rules, “The Parties” undertake to carry out any award without delay and shall be deemed to have waived their right to any form of recourse insofar as such waiver can validly be made.\r\n<br/>\r\n<br/>\r\nEach of “The Parties” subject to the declared breach shall be responsible for their own legal expenses until an award is given or settlement is reached, provided however, “That Party” found in default by “The Arbitrator(s)” shall compensate in full the aggrieved party its heirs, assignees and/or designs for the total remuneration received as a result of business conducted with “The Parties” covered by this agreement, plus all its arbitration costs, legal expenses and other charges and damages deemed fair by “The Arbitrator(s)” for bank, lending institutions, corporations, organizations, individuals, lenders, or borrowers, buyers or sellers that were introduced by the named party, notwithstanding any other provisions of the award.\r\n',	'2023-04-14 15:21:17',	'2023-04-14 15:21:17',	1),
(11,	'FORCE MAJURE',	'A party shall not be considered or adjudged to be in violation of this agreement when the violation is due to circumstances beyond its control, including but not limited to act of God, civil disturbances and theft or appropriation of the privileged information or contract(s) without the intervention or assistance of one or more of “The Parties”.',	'2023-04-14 15:21:32',	'2023-04-14 15:21:32',	1),
(12,	'ENTITIES OWNED OR CONTROLLED',	'This agreement shall be binding upon all entities owned or controlled by a party and upon the principal(s), employee(s), assignee(s), family and heirs of each party.\r\n<br/>\r\n<br/>\r\nNeither party shall have the right to assign this agreement without the express written consent of the other.',	'2023-04-14 15:22:09',	'2023-04-14 15:22:09',	1),
(13,	'AGREEMENT NOT TO CIRCUMVENT',	'“The Parties” agree not to circumvent or attempt to circumvent this agreement in an effort to gain fees, commissions, remunerations or considerations to the benefit of the one or more of “The parties” while excluding other or agree to benefit to any other party.\r\n',	'2023-04-14 15:22:52',	'2023-04-14 15:22:52',	1),
(14,	'NOT PARTNERSHIP AGREEMENT',	'This agreement in no way shall be construed as being an agreement of partnership and none of “The Parties” shall have any claim against any separate dealing, venture or assets of any other party or shall any party be liable for any other.',	'2023-04-14 15:23:06',	'2023-04-14 15:23:06',	1),
(15,	'TRANSMISSION OF THIS AGREEMENT',	'Each representative that signs below guarantees that he/she is duly empowered by his/her respectively named company to enter into and be bound by the commitments and obligations contained herein either as individual, corporate body or on behalf of a corporate body.\r\n',	'2023-04-14 15:23:19',	'2023-04-14 15:23:19',	1),
(16,	'BREACH OF AGREEMENT',	'This agreement is valid for all commodities and transactions between parties and the parties agree that any misuse of information supplied in terms of the business relationship, notwithstanding anything contained herein, or any indulgence or relaxation of any clause herein, shall entitle the claimant party to full legal recourse in terms of ICC regulations.\r\n',	'2023-04-14 15:23:35',	'2023-04-14 15:23:35',	1);
";
array_push($sqlList, $sql14);

$sql15 = "
INSERT INTO `". $table_prefix . "_policy` (`policyId`, `name`, `description`, `createDate`, `updateDate`, `lastUpdatedByUserId`) VALUES
(1,	'INTERNATIONAL CHAMBER OF COMMERCE (ICC) NON CIRCUMVENTION AND NON DISCLOSURE WORKING AGREEMENT',	'Whereas, the undersigned parties are mutually desirous of doing business with respect to the arranging, selling and buying and in cooperation with one another and with third parties for their mutual benefit. The documents which are going to follow this agreement like letters of intent, full corporate offers, bank comfort letters, contract terms and conditions, banking details or pre-advised payment instruments and/or any information contained in such documents will not be passed, under any circumstance, onto another intermediary or broker or trader or whatever company or private persons who are not end buyers or end suppliers without prior specific written consent of the party(s) providing such information.\r\n<br/>\r\n<br/>\r\nThis agreement is made and entered into on this date [CHANGE_TO_CURRENT_DATE], shall obligate the undersigned parties and their partners, associates, employers, employees, affiliates, subsidiaries, parent companies, any nominees, representatives, successors, clients and assigns hereinafter referred to as “The Parties” jointly severally, mutually and reciprocally for the terms and conditions expressly state and agree to below, and that this agreement may be referenced from time to time in any document(s), or written agreements, the terms and conditions of this agreement shall apply to any exchange of information written or oral involving financial information, personal or corporate names, contracts initiated by or involving the parties and any addition, renewal, extension, rollover amendment, renegotiations or new agreement hereinafter referred to as “The Transaction” (Project/Transaction) for the purchase of all commodities, products, Equipment.\r\n<br/>\r\n<br/>\r\nNOW, THEREFORE IT IS AGREED\r\n',	'2023-04-12 15:35:52',	'2023-04-12 15:35:52',	1);
";
array_push($sqlList, $sql15);

$sql16 = "
INSERT INTO `". $table_prefix . "_policy_link_terms` (`policyLinkId`, `policyId`, `genericPolicyTermId`, `createDate`, `updateDate`, `lastUpdatedByUserId`) VALUES
(5,	1,	5,	'2023-04-12 15:50:33',	'2023-04-12 15:50:33',	1),
(6,	1,	6,	'2023-04-12 15:50:43',	'2023-04-12 15:50:43',	1),
(7,	1,	7,	'2023-04-12 15:50:48',	'2023-04-12 15:50:48',	1),
(8,	1,	8,	'2023-04-12 15:50:53',	'2023-04-12 15:50:53',	1),
(9,	1,	9,	'2023-04-12 15:51:01',	'2023-04-12 15:51:01',	1),
(10,	1,	10,	'2023-04-14 15:24:20',	'2023-04-14 15:24:20',	1),
(11,	1,	11,	'2023-04-14 15:24:26',	'2023-04-14 15:24:26',	1),
(12,	1,	12,	'2023-04-14 15:24:31',	'2023-04-14 15:24:31',	1),
(13,	1,	13,	'2023-04-14 15:24:36',	'2023-04-14 15:24:36',	1),
(14,	1,	14,	'2023-04-14 15:24:59',	'2023-04-14 15:24:59',	1),
(15,	1,	15,	'2023-04-14 15:25:03',	'2023-04-14 15:25:03',	1),
(16,	1,	16,	'2023-04-14 15:25:11',	'2023-04-14 15:25:11',	1);
";
array_push($sqlList, $sql16);



foreach ($sqlList as $k => $v) {
  if ($conn->query($v) === TRUE) {
      echo "Tables created successfully" . PHP_EOL;
    } else {
      echo "Error creating table: " . $conn->error . " " . PHP_EOL;
    }
}

$conn->close();
?>
