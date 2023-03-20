<?php


//these values may need to change
$servername = "localhost:10065";
$table_prefix = "wp";
$username = "root";
$password = "root";
$dbname = "local";
$chatBotMailTo = "nathanduarte.work@gmail.com";
$chatBotPhone = 4776984888;
$chatBotPhoneTxt = "+52 (477)-6984-4888";
$chatBotWhats = 4776984888;
$chatBotWhatsTxt = "+52 (477)-6984-4888";
$nameOfContactUsPage = "contact-us";
$chatBotQuestion1 = "What is your contact information?";
$chatBotAnswer1 = "Email is the best option to contact us: <a class=\"cDBLBH\" href=\"mailto:". $chatBotMailTo ."\" target=\"/blank\">Email</a>, <a class=\"cDBLBH\" href=\"tel:+".$chatBotPhone."\">".$chatBotPhoneTxt."</a>, <a class=\"cDBLBH\" href=\"https://api.whatsapp.com/send?phone=".$chatBotWhats."\">".$chatBotWhatsTxt."</a>, or feel free to look <a class=\"cDBLBH\" href=\"/".$nameOfContactUsPage."\">Our Team</a> for specific individual contact info. You can also enter your email and message in this chat and we will contact you! '";
$chatBotQuestion2 = "What products do you sell?; I want to know more about your products; What do you sell";
$chatBotAnswer2 = "Currently, we are in the process to commercialize mangoes. However, there are other products we are currently working to commercialize.'";
$chatBotQuestion3 = "What is the Gospel?; I want to know more about the gospel";
$chatBotBlogPage = "/blog";
$chatBotAnswer3 = "Take a look at this : <a href=\"/".$chatBotBlogPage."\">BLOG</a>. If you would like to speak with someone contact: ".$chatBotMailTo."'";
$chatBotQuestion4 = "What is your company about?; I want to know more about your company";
$chatBotAnswer4 = "Maranatha Marketing Inc. is a company that was formed for the purpose of accelerating world evangelism through marketing and commercializing products world-wide. Take a look at this <a href=\"".$chatBotBlogPage."\">BLOG</a> for more info or feel free to contact ".$nameOfContactUsPage.".'";



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sqlList = array ();
$sql = "
CREATE TABLE `" . $table_prefix . "_guest_chat` (
    `guestChatId` int(11) NOT NULL,
    `email` text NOT NULL,
    `title` varchar(50) DEFAULT NULL,
    `message` text NOT NULL,
    `isActive` bit(1) NOT NULL,
    `createDate` datetime NOT NULL,
    `updateDate` datetime NOT NULL,
    `adminJoined` tinyint(4) NOT NULL,
    `sessionId` varchar(50) NOT NULL,
    `isQueued` bit(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql);

$sql2 = "
ALTER TABLE `" . $table_prefix . "_guest_chat`
MODIFY `guestChatId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;
";
array_push($sqlList, $sql2);

$sql3 = "
CREATE TABLE `" . $table_prefix . "_admin_typing_display` (
    `guestChatId` int(11) NOT NULL,
    `isAdminTyping` bit(1) NOT NULL,
    `createDate` datetime NOT NULL,
    `updateDate` datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";
array_push($sqlList, $sql3);

$sql4 = "
ALTER TABLE `" . $table_prefix . "_admin_typing_display`
ADD PRIMARY KEY (`guestChatId`);
";
array_push($sqlList, $sql4);

$sql5 = "
ALTER TABLE `" . $table_prefix . "_admin_typing_display`
ADD CONSTRAINT `adt_gcid_gcid` FOREIGN KEY (`guestChatId`) REFERENCES `" . $table_prefix . "_guest_chat` (`guestChatId`);
";
array_push($sqlList, $sql5);

$sql6 = "
CREATE PROCEDURE `sp_guest_fetch_is_admin_typing`(IN `gc_id` int)
BEGIN
SELECT atd.guestChatId, atd.isAdminTyping
FROM " . $table_prefix . "_admin_typing_display atd 
WHERE atd.guestChatId = gc_id;
END;;
";
array_push($sqlList, $sql6);

$sql7 = "
CREATE PROCEDURE `sp_iu_admin_typing_display`(IN `gc_id` int, IN `iat` tinyint, IN `s_id` varchar(50))
BEGIN
INSERT INTO " . $table_prefix . "_admin_typing_display (guestChatId, isAdminTyping,createDate, updateDate) 
VALUES(gc_id, iat, CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP()) ON DUPLICATE KEY UPDATE 
isAdminTyping=iat, updateDate=CURRENT_TIMESTAMP();
END;;
";
array_push($sqlList, $sql7);

$sql8 = "
CREATE PROCEDURE `sp_fetch_is_online_on`()
BEGIN
SELECT COUNT(cp.chatPermissionsId) as isActive
FROM " . $table_prefix . "_chat_permissions cp
WHERE cp.isActive = 1;
END;;
";
array_push($sqlList, $sql8);

$sql9 = "
CREATE TABLE `" . $table_prefix . "_chat_qa` (
    `chatQaId` int(11) NOT NULL,
    `title` text NOT NULL,
    `txt` text NOT NULL,
    `parentId` int(11) NOT NULL,
    `currentId` int(11) NOT NULL,
    `nextParentId` int(11) NOT NULL,
    `nextCurrentId` int(11) NOT NULL,
    `previousParentId` int(11) NOT NULL,
    `previousCurrentId` int(11) NOT NULL,
    `isSupportOnline` int(11) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql9);

$sql10 = "
ALTER TABLE `" . $table_prefix . "_chat_qa`
  ADD PRIMARY KEY (`chatQaId`);
";
array_push($sqlList, $sql10);

$sql22 = "
CREATE PROCEDURE `sp_fetch_1_chat_qa`(IN `isOnline` int)
BEGIN
SELECT cq.title, cq.txt, cq.parentId, cq.currentId, cq.nextParentId, cq.nextCurrentId, cq.previousParentId, cq.previousCurrentId
FROM " . $table_prefix . "_chat_qa cq
WHERE cq.parentId = 1 and cq.isSupportOnline = isOnline;
END;;
";
array_push($sqlList, $sql22);

$sql11 = "
CREATE TABLE `" . $table_prefix . "_chat_close_reason` (
    `closeChatReasonId` int(11) NOT NULL,
    `reason` varchar(50) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

";
array_push($sqlList, $sql11);

$sql12 = "
INSERT INTO `" . $table_prefix . "_chat_close_reason` (`closeChatReasonId`, `reason`) VALUES
(1, 'Question answered'),
(2, 'Too many in queue'),
(3, 'Slow response time of support'),
(4, 'Waiting too long for chat support');
";
array_push($sqlList, $sql12);

$sql13 = "
ALTER TABLE `" . $table_prefix . "_chat_close_reason`
  ADD PRIMARY KEY (`closeChatReasonId`);
";
array_push($sqlList, $sql13);

$sql14 = "
CREATE PROCEDURE `sp_fetch_chat_close_reasons`()
BEGIN
SELECT ccr.closeChatReasonId, ccr.reason
FROM " . $table_prefix . "_chat_close_reason ccr;
END;;
";
array_push($sqlList, $sql14);

$sql15 = "
CREATE PROCEDURE `sp_fetch_chat_close_reasons`()
BEGIN
SELECT ccr.closeChatReasonId, ccr.reason
FROM " . $table_prefix . "_chat_close_reason ccr;
END;;
";
array_push($sqlList, $sql15);

$sql16 = "
CREATE TABLE `" . $table_prefix . "_chatbot` (
  `chatId` int(11) NOT NULL,
  `question` text COLLATE utf8_unicode_ci NOT NULL,
  `answer` text COLLATE utf8_unicode_ci NOT NULL,
  `createDate` datetime NOT NULL,
  `language` varchar(4) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";
array_push($sqlList, $sql16);

$sql17 = "
ALTER TABLE `" . $table_prefix . "_chatbot`
ADD PRIMARY KEY (`chatId`);
";
array_push($sqlList, $sql17);

$sql18 = "
CREATE PROCEDURE `sp_fetch_chatbot_spec_question`(IN `input` varchar(4))
BEGIN
SELECT SUBSTRING(question,1, LOCATE(';', question)-1) as question FROM " . $table_prefix . "_chatbot
WHERE chatID NOT IN (13, 14)
AND language = input
ORDER BY answer desc;
END;;
";
array_push($sqlList, $sql18);

$sql19 = "
INSERT INTO `" . $table_prefix . "_chatbot` (`chatId`, `question`, `answer`, `createDate`, `language`) VALUES
(1, '".$chatBotQuestion1."', '".$chatBotAnswer1."', '0000-00-00 00:00:00', 'en'),
(2, '".$chatBotQuestion2."', '".$chatBotAnswer2."', '0000-00-00 00:00:00', 'en'),
(3, '".$chatBotQuestion3."', '".$chatbotAnswer3."', '2021-03-09 10:27:42', 'es'),
(4, '".$chatBotQuestion4."', '".$chatBotAnswer4."', '2021-03-09 10:27:42', 'es'),
";
array_push($sqlList, $sql19);

$sql20 = "
CREATE TABLE `" . $table_prefix . "_chat_permissions` (
  `chatPermissionsId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `isActive` tinyint(1) NOT NULL, 
  `havePermissions` bit(1) NOT NULL,
  `lastUpdated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql20);

$sql21 = "
INSERT INTO `" . $table_prefix . "_chat_permissions` (`chatPermissionsId`, `userId`, `isActive`, `havePermissions`, `lastUpdated`) VALUES
(1, 1, 1, b'1', '2022-08-29 15:22:39')
";
array_push($sqlList, $sql21);

$sql23 = "
INSERT INTO `" . $table_prefix . "_chat_qa` (`chatQaId`, `title`, `txt`, `parentId`, `currentId`, `nextParentId`, `nextCurrentId`, `previousParentId`, `previousCurrentId`, `isSupportOnline`) VALUES
(1, 'Hello, maranatha! \r\nSupport is online if you\'d like to chat!', 'Would you like help from support?', 1, 1, 2, 1, 1, 1, 1),
(2, 'Hello, maranatha! \r\nSupport is online if you\'d like to chat!', 'Would you like help from our chatbot to help answer some frequently asked questions?', 1, 2, 3, 1, 1, 1, 1),
(3, 'Hello, maranatha! \r\nSupport is offline, but our chat bot can answer some frequently asked questions!', 'Would you like help from our chatbot?', 1, 3, 3, 1, 1, 1, 0),
(4, 'Hello, maranatha! \r\nSupport is offline, but our chat bot can answer some frequently asked questions!', 'Do you want to leave a message for our support team?\r\n', 1, 4, 4, 1, 1, 1, 0),
(5, 'Fantastic! Let\'s get a rep for you, but first what is your email?', 'Enter email', 2, 1, 2, 2, 1, 1, 1),
(6, 'Great! Now, what is your question?', 'Enter question', 2, 2, 9, 1, 2, 1, 1),
(7, 'What is your question for the chatbot?', 'Enter question', 3, 1, 9, 2, 1, 0, 1),
(8, 'Great! We will reach out to you, what is your email?', 'Enter email', 4, 1, 4, 2, 1, 1, 1),
(9, 'Great! What is your question?', 'Enter question', 4, 2, 9, 2, 1, 0, 1);
";
array_push($sqlList, $sql23);

$sql23 = "
INSERT INTO `" . $table_prefix . "_chat_qa` (`chatQaId`, `title`, `txt`, `parentId`, `currentId`, `nextParentId`, `nextCurrentId`, `previousParentId`, `previousCurrentId`, `isSupportOnline`) VALUES
(1, 'Hello, maranatha! \r\nSupport is online if you\'d like to chat!', 'Would you like help from support?', 1, 1, 2, 1, 1, 1, 1),
(2, 'Hello, maranatha! \r\nSupport is online if you\'d like to chat!', 'Would you like help from our chatbot to help answer some frequently asked questions?', 1, 2, 3, 1, 1, 1, 1),
(3, 'Hello, maranatha! \r\nSupport is offline, but our chat bot can answer some frequently asked questions!', 'Would you like help from our chatbot?', 1, 3, 3, 1, 1, 1, 0),
(4, 'Hello, maranatha! \r\nSupport is offline, but our chat bot can answer some frequently asked questions!', 'Do you want to leave a message for our support team?\r\n', 1, 4, 4, 1, 1, 1, 0),
(5, 'Fantastic! Let\'s get a rep for you, but first what is your email?', 'Enter email', 2, 1, 2, 2, 1, 1, 1),
(6, 'Great! Now, what is your question?', 'Enter question', 2, 2, 9, 1, 2, 1, 1),
(7, 'What is your question for the chatbot?', 'Enter question', 3, 1, 9, 2, 1, 0, 1),
(8, 'Great! We will reach out to you, what is your email?', 'Enter email', 4, 1, 4, 2, 1, 1, 1),
(9, 'Great! What is your question?', 'Enter question', 4, 2, 9, 2, 1, 0, 1);
";
array_push($sqlList, $sql23);

$sql24 = "
CREATE PROCEDURE `sp_fetch_admin_chat_status`(IN `u_id` int)
BEGIN
SELECT cp.isActive
FROM " . $table_prefix . "_chat_permissions cp
WHERE userId = u_id;
END;;
";
array_push($sqlList, $sql24);

$sql25 = "
CREATE PROCEDURE `sp_fetch_chat_admin`(IN `userVal` int)
BEGIN
SELECT cp.userId, cp.isActive, cp.havePermissions, cp.lastUpdated
FROM " . $table_prefix . "_chat_permissions cp LEFT JOIN " . $table_prefix . "_users u
  ON cp.userId = u.ID
WHERE cp.havePermissions = 1 AND cp.userId = userVal;
END;;
";
array_push($sqlList, $sql25);

$sql26 = "
CREATE PROCEDURE `sp_fetch_chat_admin`(IN `userVal` int)
BEGIN
SELECT cp.userId, cp.isActive, cp.havePermissions, cp.lastUpdated
FROM " . $table_prefix . "_chat_permissions cp LEFT JOIN " . $table_prefix . "_users u
  ON cp.userId = u.ID
WHERE cp.havePermissions = 1 AND cp.userId = userVal;
END;;
";
array_push($sqlList, $sql26);

$sql27 = "
CREATE PROCEDURE `sp_fetch_active_chats`()
BEGIN
SELECT gc.guestChatId, gc.email, gc.title, gc.message, gc.isActive, gc.createDate
FROM " . $table_prefix . "_guest_chat gc
WHERE gc.isActive = 1
ORDER BY createDate asc;

UPDATE " . $table_prefix . "_guest_chat gc SET
    gc.isQueued = 1
WHERE gc.isActive = 1;
END;;
";
array_push($sqlList, $sql27);

foreach ($sqlList as $k => $v) {
  if ($conn->query($v) === TRUE) {
      echo "Tables created successfully" . PHP_EOL;
    } else {
      echo "Error creating  " . $v ." " . $conn->error . " " . PHP_EOL;
    }
}

?>

