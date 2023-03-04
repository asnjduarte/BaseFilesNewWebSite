<?php


$username = "root";
$password = "root";
$dbname = "local";

//all the values that will need to change
$servername = "localhost:10047";
$table_prefix = "wp";
$countryAbbr = "MX";
$country = "Mexico";
$company = "Tiempos de cambio";
$telTxt = "Télefono";
$telVal = "479 110 6160";
$telLnk = "4791106160";
$emTxt = "Correo";
$emVal = "info.tcmaranatha@gmail.com";
$fbLnk = "tiempos.decambio.14";
$ytLnk = "@tiemposdecambiomaranatha9494";
$termsTxt = "Términos y condiciones";
$termsVal = "Politica de privacidad";
$termsLnk = "politica-de-privacidad";
$lnk1Txt = "Inicio";
$lnk1Lnk = "/";
$lnk1Perms = 2; //general permissions
$lnk2Txt = "Nuestra Fe";
$lnk2Lnk = "/nuestra-fe/";
$lnk2Perms = 2;
$lnk3Txt = "Acerca de Nosotros";
$lnk3Lnk = "/acerca-de-nosotros/";
$lnk3Perms = 2;
$lnk4Txt = "Agregar reporte de una iglesia";
$lnk4Lnk = "/agregar-reporte-de-una-iglesia/";
$lnk4Perms = 1; //admin
$lnk5Txt = "Ministerios";
$lnk5Lnk = "/mapa-de-ministerios/";
$lnk5Perms = 2;
$lnk6Txt = "Agregar reporte de una tienda";
$lnk6Lnk = "/agregar-reporte-de-una-tienda/";
$lnk6Perms = 1;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sqlList = array ();
$sql = "
CREATE PROCEDURE `sp_fetch_active_country`()
BEGIN
SELECT c.countryId, c.name
FROM " . $table_prefix . "_country c;
END;;
";
array_push($sqlList, $sql);
$sql2 = "
CREATE PROCEDURE `sp_fetch_company_info`()
BEGIN
SELECT c.countryId, c.name as companyName, cl.linkName, cl.value, cl.img, cl.link, cl.type
FROM ". $table_prefix . "_company c LEFT JOIN ". $table_prefix . "_company_links cl 
  ON c.companyId = cl.companyId;
END;;
";
array_push($sqlList, $sql2);

$sql3 = "
CREATE PROCEDURE `sp_fetch_menu_header`()
BEGIN
SELECT mh.menuHeaderId, mh.link, mh.text, mh.roleId
FROM ". $table_prefix . "_menu_header mh
ORDER BY mh.roleId desc;
END;;
";
array_push($sqlList, $sql3);

$sql4 =
"
CREATE PROCEDURE `sp_insert_error`(IN `e_name` text, IN `e_description` text, IN `s_proc` varchar(35), IN `s_id` varchar(50))
insert into ". $table_prefix . "_error(errorName, errorDescription, storedProcedure, createDate, sessionId) 
values ( e_name,e_description, s_proc, CURRENT_TIMESTAMP(),s_id);
";
array_push($sqlList, $sql4);

$sql9 =
"
CREATE TABLE `". $table_prefix . "_country` (
    `countryId` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(35) NOT NULL,
    `isActive` bit(1) NOT NULL,
    `abbr` varchar(3) NOT NULL,
    PRIMARY KEY (`countryId`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql9);

$sql10 =
"
INSERT INTO `". $table_prefix . "_country` (`countryId`, `name`, `isActive`, `abbr`) VALUES
(1,	'" . $country ."',	CONV('1', 2, 10) + 0,	'". $countryAbbr ."');
";
array_push($sqlList, $sql10);

$sql5 =
"
CREATE TABLE `". $table_prefix . "_company` (
    `companyId` int(11) NOT NULL AUTO_INCREMENT,
    `countryId` int(11) unsigned NOT NULL,
    `name` varchar(50) NOT NULL,
    PRIMARY KEY (`companyId`),
    KEY `countryId` (`countryId`),
    CONSTRAINT `". $table_prefix . "_company_ibfk_1` FOREIGN KEY (`countryId`) REFERENCES `". $table_prefix . "_country` (`countryId`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;  
";
array_push($sqlList, $sql5);

$sql6 =
"
INSERT INTO `". $table_prefix . "_company` (`companyId`, `countryId`, `name`) VALUES
(1,	1,	'".$company."');
";
array_push($sqlList, $sql6);

$sql7 =
"
CREATE TABLE `". $table_prefix . "_company_links` (
    `linkId` int(11) NOT NULL AUTO_INCREMENT,
    `linkName` varchar(35) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `value` varchar(35) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `img` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `link` text NOT NULL,
    `companyId` int(11) NOT NULL,
    `type` int(11) NOT NULL,
    PRIMARY KEY (`linkId`),
    KEY `companyId` (`companyId`),
    CONSTRAINT `". $table_prefix . "_company_links_ibfk_1` FOREIGN KEY (`companyId`) REFERENCES `". $table_prefix . "_company` (`companyId`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql7);

$sql8 =
"
INSERT INTO `". $table_prefix . "_company_links` (`linkId`, `linkName`, `value`, `img`, `link`, `companyId`, `type`) VALUES
(1,	'".$telTxt."',	'".$telVal."',	'wh',	'https://api.whatsapp.com/send?phone=".$telLnk."',	1,	1),
(2,	'".$emTxt."',	'".$emVal."',	'em',	'mailto:',	1,	1),
(3,	'Facebook',	'Facebook',	'fb',	'https://www.facebook.com/".$fbLnk."',	1,	2),
(4,	'YouTube',	'YouTube',	'yt',	'https://www.youtube.com/".$ytLnk."',	1,	2),
(5,	'".$termsTxt."',	'".$termsVal."',	'tyc',	'/".$termsLnk."/',	1,	0);
";
array_push($sqlList, $sql8);

$sql12 =
"
CREATE TABLE `". $table_prefix . "_error` (
    `errorId` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `errorName` text NOT NULL,
    `errorDescription` text NOT NULL,
    `storedProcedure` varchar(35) NOT NULL,
    `createDate` datetime DEFAULT NULL,
    `sessionId` varchar(50) NOT NULL,
    PRIMARY KEY (`errorId`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;  
";
array_push($sqlList, $sql12);

$sql13 =
"
CREATE TABLE `". $table_prefix . "_menu_header` (
    `menuHeaderId` int(11) NOT NULL AUTO_INCREMENT,
    `link` text NOT NULL,
    `text` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `roleId` int(11) NOT NULL,
    PRIMARY KEY (`menuHeaderId`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql13);

$sql14 =
"
INSERT INTO `". $table_prefix . "_menu_header` (`menuHeaderId`, `link`, `text`, `roleId`) VALUES
(1,	'".$lnk1Lnk."',	'" . $lnk1Txt ."',	". $lnk1Perms ."),
(2,	'".$lnk2Lnk."',	'" . $lnk2Txt ."',	". $lnk2Perms ."),
(3,	'".$lnk3Lnk."',	'" . $lnk3Txt ."',	". $lnk3Perms ."),
(4,	'".$lnk4Lnk."',	'" . $lnk4Txt ."',	". $lnk4Perms ."),
(5,	'".$lnk5Lnk."',	'" . $lnk5Txt ."',	". $lnk5Perms ."),
(6,	'".$lnk6Lnk."',	'" . $lnk6Txt ."',	". $lnk6Perms .")
";
array_push($sqlList, $sql14);

$sql15 =
"
CREATE TABLE `". $table_prefix . "_role` (
    `roleId` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(15) NOT NULL,
    `description` text NOT NULL,
    PRIMARY KEY (`roleId`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql15);

$sql16 =
"
INSERT INTO `". $table_prefix . "_role` (`roleId`, `name`, `description`) VALUES
(1,	'Administrator',	'admins'),
(2,	'Viewer',	'all general users will be considered as viewers.');
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
