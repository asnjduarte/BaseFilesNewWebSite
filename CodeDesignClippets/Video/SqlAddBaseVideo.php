<?php

//these values may need to change
$servername = "localhost:10047";
$table_prefix = "wp";
$username = "root";
$password = "root";
$dbname = "local";

$videoImg = "/wp-content/uploads/2023/images/videoTemplate/video1.jpg";
$video = "/wp-content/uploads/2023/03/DOOA-Terra-Base-Relaxing-Maintenance-Session.mp4";
$videoText = "Video 1";



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sqlList = array ();
$sql = "
CREATE TABLE `" . $table_prefix . "_video` (
  `videoId` int(11) NOT NULL AUTO_INCREMENT,
  `image` text NOT NULL,
  `videoLink` text NOT NULL,
  `videoLnkTxt` text NOT NULL,
  `createDate` datetime NOT NULL,
  PRIMARY KEY (`videoId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql);

$sql1 = "
INSERT INTO `" . $table_prefix . "_video` (`videoId`, `image`, `videoLink`, `videoLnkTxt`, `createDate`) VALUES
(1,	'".$videoImg."',	'".$video."',	'".$videoText."',	'2023-03-16 17:04:20')
";
array_push($sqlList, $sql1);

$sql2 = "
CREATE PROCEDURE `sp_insert_video`(IN `i` text, IN `vl` text, IN `vlt` text)
BEGIN
insert into " . $table_prefix . "_video(image, videoLink, videoLnkTxt, createDate) 
values (i, vl, vlt, CURRENT_TIMESTAMP());

SELECT LAST_INSERT_ID() lastInsert;
END;;
";
array_push($sqlList, $sql2);

$sql3 = "
CREATE PROCEDURE `sp_fetch_video`(IN `vId` int)
BEGIN
SELECT videoId, image, videoLink, videoLnkTxt, createDate
FROM " . $table_prefix . "_video
WHERE videoId = vId;
END;;
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


