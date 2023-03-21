
<?php

//these values may need to change
$servername = "localhost:10047";
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

// sql to create table
$sqlList = array ();
$sql = "
CREATE TABLE `" . $table_prefix . "_about_us` (
    `aboutUsId` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(50) NOT NULL,
    `description` text NOT NULL,
    `img` varchar(4) NOT NULL,
    PRIMARY KEY (`aboutUsId`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql);

$sql1 = "
INSERT INTO `" . $table_prefix . "_about_us` (`aboutUsId`, `title`, `description`, `img`) VALUES
(1,	'What Is Terrabase',	'<h3>TerraBase</h3>\r\nTerraBase is an intuitive, SQL-compliant relational database application designed for environmental professionals and managers who need to assess and manage chemical, geological, and spatial data at one or more sites.\r\n<br/>\r\n<br/>\r\nTerraBase allows for the automated import of chemical and field data for any media through standard electronic formats delivered from an analytical laboratory or geotechnical data from field activities. Data can be rapidly queried and automatically displayed spatially in ArcView, 3D Analyst. TerraBase is equipped with powerful reporting capabilities allowing the user to print spatial and tabular results in pre-formatted reports as well as output data queries automatically to other applications (Microsoft Excel, Word, Access, Crystal Reports, etc.). TerraBase has the unique capability to efficiently manage up to four levels of analytical data quality that can be combined with the environmental data used to obtain the “best analytical result”.\r\n<br/>\r\n<br/>\r\n<h3>Your Problem</h3>\r\nThe process of identifying data, organizing it, and verifying data accuracy can be a major obstacle. Converting data from a variety of digital and hardcopy formats can also be difficult and costly. Providing distributed access to data within an organization, and controlling access to that data creates additional challenges. How does your organization manage implementation costs and support, or maintain compatibility between various operating systems and software configurations? Capital expenditures for hardware and software purchases are scrutinized today more than ever. Consequently, these barriers restrict the ability of data owners, managers, and consultants from conducting meaningful data analysis on their sites.\r\n<br/>\r\n<br/>\r\n<h3>Our Solution</h3>\r\nTerraBase software helps you organize and assemble both historical and current data streams into a single, accurate data set. Data entry errors are minimized by reducing or eliminating manual data entry and relying on electronic verification and importation of data. Once the data passes our electronic verification process and is imported, users can spatially view, query, or import/export their data from various physical locations. Issues related to implementation, support, distributed access, or capital expenditures for hardware/software are all eliminated using our web-based solutions. With your existing internet access, a database filled with accurate, up-to-date environmental data can be shared among your organization\'s users no matter where they are located or what hardware/software configuration they are using.',	'auv1'),
(2,	'What can Terrabase do for you',	'TerraBase provides a robust database and interface from which to manage all types of environmental data for one or more sites regardless of the media (water, air, soil, etc.). While TerraBase alone can generate a wide variety of standard and customizable print reports, as an ArcView extension it allows “flat” data to become associated with spatial information in two and three dimensions. Moreover, the TerraBase ArcView extensions allow the user complete access to all analytical and geotechnical data from within the GIS application.\r\n\r\n<ul>\r\n<li>TerraBase makes it significantly easier for an environmental scientist or manager to understand environmental data and its spatial relationships. He or she can make appropriate, informed decisions regarding site environmental issues.</li>\r\n<li>Terrabase currently supports three (3) file types for analytical data import: TerraBase level I, TerraBase Level II and TerraBase Delimited. Since most laboratories are capable of producing these formats, the task of manual data entry is virtually eliminated. A fourth format, Super Flat File Format (SF3), is being developed along with companion tools (i.e., Checkmate and Universal Translator) that will allow the user to manage any analytical format. TerraBase Inc. can distribute “format checking” routines which function to ensure data is correctly formatted. TerraBase Inc. also provides services to clients that need assistance in translating legacy data from standard and non-standard formats.</li>\r\n<li>TerraBase is capable of managing chemical, geological, and spatial information for one or more sites eliminating the need to maintain multiple independent databases for reporting and spatial analyses.</li>\r\n<li>TerraBase Inc. also provides clients with a web-enabled version of TerraBase via an ASP business model (www.terrabaseonline.com). This option offers significant savings and convenience over data sharing options and maintenance associated with implementing in-house services that are distributed over a LAN or WAN.</li>\r\n</ul>\r\n',	'auv2'),
(3,	'What solutions does it provide',	'A great deal of the analytical and geotechnical data that must be dealt with by companies and regulatory agencies involved in any activity related to the environment–from materials production (such as chemical corporations) to monitoring and regulatory activities (such as the EPA or DEQ)–is surprisingly difficult to store, manage, and query. This is because it can exist in a wide variety of formats and even if standardized and stored in a database, few databases have the functions necessary to conduct meaningful data analysis.\r\n<br/>\r\nTerraBase allows easy import of a variety of data formats and stores it in a robust database where it can be quickly queried across any number of parameters. With the GIS interface, data that would normally be a simple spreadsheet or report is automatically displayed on a map to show features such as 2D and 3D contours and subsurface detail.',	'auv3');

";
array_push($sqlList, $sql1);

$sql2 = "
CREATE PROCEDURE `sp_fetch_about_us_info`()
BEGIN
SELECT *
FROM " . $table_prefix . "_about_us au;
END;;
";
array_push($sqlList, $sql2);

$sql3 = "
CREATE PROCEDURE `sp_fetch_about_us_specific`(IN `auvId` int)
SELECT *
FROM " . $table_prefix . "_about_us au
WHERE aboutUsId = auvId;;

";
array_push($sqlList, $sql3);


foreach ($sqlList as $k => $v) {
  if ($conn->query($v) === TRUE) {
      echo "Tables created successfully" . PHP_EOL;
    } else {
      echo "Error creating  " . $v ." " . $conn->error . " " . PHP_EOL;
    }
}

?>




