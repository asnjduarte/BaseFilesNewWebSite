<?php

//these values may need to change
$servername = "localhost:10077";
$table_prefix = "wp";
$username = "root";
$password = "root";
$dbname = "local";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sqlList = array();

$sql4 = "
INSERT INTO `". $table_prefix . "_country` (`countryId`, `name`, `isActive`, `abbr`) VALUES
(2,	'Mexico',	CONV('1', 2, 10) + 0,	'MX'),
(3,	'Canada',	CONV('1', 2, 10) + 0,	'CN');
";
array_push($sqlList, $sql4);

$sql1 = "
CREATE TABLE `". $table_prefix . "_state` (
  `stateId` int(11) NOT NULL AUTO_INCREMENT,
  `sName` varchar(35) NOT NULL,
  `countryId` int(11) unsigned NOT NULL,
  `sAbbr` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`stateId`),
  KEY `countryId` (`countryId`),
  CONSTRAINT `". $table_prefix . "_state_ibfk_1` FOREIGN KEY (`countryId`) REFERENCES `". $table_prefix . "_country` (`countryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql1);

$sql2 = "
INSERT INTO `". $table_prefix . "_state` (`stateId`, `sName`, `countryId`, `sAbbr`) VALUES
(1,	'Aguascalientes',	2,	'AG'),
(2,	'Baja California',	2,	'BC'),
(3,	'Baja California Sur',	2,	'BS'),
(4,	'Campeche',	2,	'CM'),
(5,	'Chiapas',	2,	'CS'),
(6,	'Chihuahua',	2,	'CH'),
(7,	'Coahuila',	2,	'CO'),
(8,	'Colima',	2,	'CL'),
(9,	'Durango',	2,	'DG'),
(10,	'Guanajuato',	2,	'GT'),
(11,	'Guerrero',	2,	'GR'),
(12,	'Hidalgo',	2,	'HG'),
(13,	'Jalisco',	2,	'JA'),
(14,	'México',	2,	'EM'),
(15,	'Mexico City',	2,	'DF'),
(16,	'Sinaloa',	2,	'SI'),
(17,	'Alaska',	1,	NULL),
(18,	'Alabama',	1,	NULL),
(19,	'Arizona',	1,	NULL),
(20,	'Arkansas',	1,	NULL),
(21,	'California',	1,	NULL),
(22,	'Colorado',	1,	NULL),
(23,	'Connecticut',	1,	NULL),
(24,	'Delaware',	1,	NULL),
(25,	'Florida',	1,	NULL),
(26,	'Georgia',	1,	NULL),
(27,	'Hawaii',	1,	NULL),
(28,	'Idaho',	1,	NULL),
(29,	'Illinois',	1,	NULL),
(30,	'Indiana',	1,	NULL),
(31,	'Iowa',	1,	NULL),
(32,	'Kansas',	1,	NULL),
(33,	'Kentucky',	1,	NULL),
(34,	'Louisiana',	1,	NULL),
(35,	'Maine',	1,	NULL),
(36,	'Maryland',	1,	NULL),
(37,	'Massachusetts',	1,	NULL),
(38,	'Michigan',	1,	NULL),
(39,	'Minnesota',	1,	NULL),
(40,	'Mississippi',	1,	NULL),
(41,	'Missouri',	1,	NULL),
(42,	'Montana',	1,	NULL),
(43,	'Nebraska',	1,	NULL),
(44,	'Nevada',	1,	NULL),
(45,	'New Hampshire',	1,	NULL),
(46,	'New Jersey',	1,	NULL),
(47,	'Michoacán',	2,	'MI'),
(48,	'Morelos',	2,	'MO'),
(49,	'Nayarit',	2,	'NA'),
(50,	'Nuevo León',	2,	'NL'),
(51,	'Oaxaca',	2,	'OA'),
(52,	'Puebla',	2,	'PU'),
(53,	'Querétaro',	2,	'QT'),
(54,	'Quintana Roo',	2,	'QR'),
(55,	'San Luis Potosí',	2,	'SL'),
(56,	'Sonora',	2,	'SO'),
(57,	'Tabasco',	2,	'TB'),
(58,	'Tamaulipas',	2,	'TM'),
(59,	'Tlaxcala',	2,	'TL'),
(60,	'Veracruz',	2,	'VE'),
(61,	'Yucatán',	2,	'YU'),
(62,	'Zacatecas',	2,	'ZA'),
(63,	'Ontario',	3,	'ON'),
(64,	'Quebec',	3,	'QC'),
(65,	'Nova Scotia',	3,	'NS'),
(66,	'New Brunswick',	3,	'NB'),
(67,	'Manitoba',	3,	'MB'),
(68,	'British Columbia',	3,	'BC'),
(69,	'Prince Edward Island',	3,	'PE'),
(70,	'Saskatchewan',	3,	'SK'),
(71,	'Alberta',	3,	'AB'),
(72,	'Newfoundland and Labrador',	3,	'NL'),
(73,	'Yukon',	1,	'YT'),
(74,	'Northwest Territories',	1,	'NT'),
(75,	'Nunavut',	1,	'NU');
";
array_push($sqlList, $sql2);

$sql3 = "
CREATE TABLE `". $table_prefix . "_city` (
  `cityId` int(11) NOT NULL AUTO_INCREMENT,
  `cName` varchar(40) NOT NULL,
  `stateId` int(11) NOT NULL,
  PRIMARY KEY (`cityId`),
  KEY `stateId` (`stateId`),
  CONSTRAINT `". $table_prefix . "_city_ibfk_1` FOREIGN KEY (`stateId`) REFERENCES `". $table_prefix . "_state` (`stateId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql3);

$sql4 = "
INSERT INTO `". $table_prefix . "_city` (`cityId`, `cName`, `stateId`) VALUES
(1,	'Anchorage',	17),
(2,	'San Miguel De Allende',	10),
(3,	'Leon',	10),
(4,	'Irapuato',	10),
(5,	'Aguascalientes',	1),
(6,	'Asientos',	1),
(7,	'Calvillo',	1),
(8,	'Calvillo',	1),
(9,	'El Llano',	1),
(10,	'Chilliwack',	68),
(11,	'Aventura',	25);
";
array_push($sqlList, $sql4);

foreach ($sqlList as $k => $v) {
  if ($conn->query($v) === TRUE) {
      echo "Tables created successfully" . PHP_EOL;
    } else {
      echo "Error creating table: " . $conn->error . " " . PHP_EOL;
    }
}

$conn->close();
?>
