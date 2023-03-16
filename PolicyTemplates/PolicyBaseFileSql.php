<?php
$servername = "localhost:10059";
$username = "root";
$password = "root";
$dbname = "local";

$table_prefix = "wp";
$companyName = "Tiempos de cambio";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sqlList = array ();
$sql3 =
"
CREATE PROCEDURE `sp_fetch_policy_terms`(IN `pId` int)
BEGIN
SELECT gpt.genericPolicyTermId, gpt.name, gpt.description, gpt.updateDate
FROM  ". $table_prefix . "_policy_link_terms plt LEFT JOIN ". $table_prefix . "_generic_policy_terms gpt
     ON plt.genericPolicyTermId = gpt.genericPolicyTermId
WHERE plt.policyId = pId;
END;;
";
array_push($sqlList, $sql3);

$sql4 =
"
CREATE PROCEDURE `sp_fetch_policy`(IN `plt_id` tinyint)
BEGIN
SELECT p.name as policyName, p.description as policyDescription, gpc.name as policyTermName, gpc.description as policyTermDescription
FROM ". $table_prefix . "_policy_link_terms plt LEFT JOIN ". $table_prefix . "_policy p
  ON plt.policyId = p.policyId LEFT JOIN ". $table_prefix . "_generic_policy_terms gpc
  ON plt.genericPolicyTermId = gpc.genericPolicyTermId
WHERE plt.policyId = plt_id;
END
";
array_push($sqlList, $sql4);

$sql9 =
"
CREATE TABLE `". $table_prefix . "_generic_policy_terms` (
  `genericPolicyTermId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `description` text NOT NULL,
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  `lastUpdatedByUserId` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`genericPolicyTermId`),
  KEY `lastUpdatedByUserId` (`lastUpdatedByUserId`),
  CONSTRAINT `". $table_prefix . "_generic_policy_terms_ibfk_1` FOREIGN KEY (`lastUpdatedByUserId`) REFERENCES `". $table_prefix . "_users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql9);

$sql5 =
"
CREATE TABLE `". $table_prefix . "_policy` (
  `policyId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(35) NOT NULL,
  `description` text NOT NULL,
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  `lastUpdatedByUserId` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`policyId`),
  KEY `lastUpdatedByUserId` (`lastUpdatedByUserId`),
  CONSTRAINT `". $table_prefix . "_policy_ibfk_1` FOREIGN KEY (`lastUpdatedByUserId`) REFERENCES `". $table_prefix . "_users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql5);

$sql7 =
"
CREATE TABLE `". $table_prefix . "_policy_link_terms` (
  `policyLinkId` int(11) NOT NULL AUTO_INCREMENT,
  `policyId` int(11) NOT NULL,
  `genericPolicyTermId` int(11) NOT NULL,
  `createDate` datetime NOT NULL,
  `updateDate` datetime NOT NULL,
  `lastUpdatedByUserId` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`policyLinkId`),
  KEY `policyId` (`policyId`),
  KEY `genericPolicyTermId` (`genericPolicyTermId`),
  KEY `lastUpdatedByUserId` (`lastUpdatedByUserId`),
  CONSTRAINT `wp_policy_link_terms_ibfk_1` FOREIGN KEY (`policyId`) REFERENCES `wp_policy` (`policyId`),
  CONSTRAINT `wp_policy_link_terms_ibfk_2` FOREIGN KEY (`genericPolicyTermId`) REFERENCES `wp_generic_policy_terms` (`genericPolicyTermId`),
  CONSTRAINT `wp_policy_link_terms_ibfk_3` FOREIGN KEY (`lastUpdatedByUserId`) REFERENCES `wp_users` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql7);

foreach ($sqlList as $k => $v) {
    if ($conn->query($v) === TRUE) {
        echo "Tables created successfully" . PHP_EOL;
      } else {
        echo "Error creating table: " . $conn->error . " " . PHP_EOL;
      }
}

$conn->close();

?>
