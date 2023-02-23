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

$sql10 =
"
INSERT INTO `". $table_prefix . "_generic_policy_terms` (`genericPolicyTermId`, `name`, `description`, `createDate`, `updateDate`, `lastUpdatedByUserId`) VALUES
(1,	'Información que es recogida',	'Nuestro sitio web podrá recoger información personal como: Nombre, información de contacto como su dirección de correo electrónico y número de teléfono. El único lugar donde recopilamos esta información es desde nuestra página de contacto.',	'2023-02-23 13:53:20',	'2023-02-23 13:53:20',	1),
(2,	'Uso de la información recogida',	'Simplemente usamos esta información para comunicarnos con usted.',	'2023-02-23 14:04:40',	'2023-02-23 14:04:40',	1),
(3,	'Solicitud para la eliminación de datos',	'El reglamento general de protección de datos (GDPR) exige que la empresa proporcione una forma en la que el usuario pueda solicitar la eliminación de sus datos. Es por ello que " . $companyName . " comprometido con esta normativa te brinda las instrucciones para que, dado el caso, tus datos queden eliminados de nuestra plataforma. Envía un mensaje a nosotros y el motivo por el cual solicitan la eliminación. Nosotros te confirmaremos con un correo cuando el proceso haya concluido.',	'2023-02-23 14:05:09',	'2023-02-23 14:05:09',	1),
(4,	'Cookies',	'Una cookie se refiere a un fichero que es enviado con la finalidad de solicitar permiso para almacenarse en su ordenador, al aceptar dicho fichero se crea y sirve entonces para tener información respecto al tráfico web, también facilita las futuras visitas a una web recurrente. Otra función que tienen las cookies es que con ellas las web pueden reconocerte individualmente y por tanto brindarte un mejor servicio personalizado. Nuestro sitio web emplea las cookies para poder identificar las páginas que son visitadas y su frecuencia. Esta información es empleada únicamente para análisis estadístico. Usted puede eliminar las cookies en cualquier momento desde su ordenador. Sin embargo las cookies ayudan a proporcionar un mejor servicio de los sitios web, estás no dan acceso a información de su ordenador ni de usted, a menos de que usted así lo quiera y la proporcione directamente. Usted puede aceptar o negar el uso de cookies, sin embargo la mayoría de navegadores aceptan cookies automáticamente pues sirve para tener un mejor servicio web. También usted puede cambiar la configuración de su ordenador para declinar las cookies. Si se declinan es posible que no pueda utilizar algunos de nuestros servicios.',	'2023-02-23 14:05:36',	'2023-02-23 14:05:36',	1);
";
array_push($sqlList, $sql10);

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

$sql6 =
"
INSERT INTO `". $table_prefix . "_policy` (`policyId`, `name`, `description`, `createDate`, `updateDate`, `lastUpdatedByUserId`) VALUES
(1,	'Policia de Privacidad',	'La presente política de privacidad establece los términos en que " . $companyName . " usa y protege la información que es proporcionada por sus usuarios al momento de utilizar su sitio web. Esta compañía está comprometida con la seguridad de los datos de sus usuarios. Cuando le pedimos llenar los campos de información personal con la cual usted pueda ser identificado, lo hacemos asegurando que sólo se emplea de acuerdo a los términos de este documento. Sin embargo, esta Política de Privacidad puede cambiar con el tiempo o ser actualizada por lo que le recomendamos y enfatizamos revisar continuamente esta página para asegurarse que está de acuerdo con dichos cambios.\"',	'2023-02-23 14:19:39',	'2023-02-23 14:19:39',	1)
";
array_push($sqlList, $sql6);

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

$sql8 =
"
INSERT INTO `". $table_prefix . "_policy_link_terms` (`policyLinkId`, `policyId`, `genericPolicyTermId`, `createDate`, `updateDate`, `lastUpdatedByUserId`) VALUES
(1,	1,	1,	'2023-02-23 14:31:47',	'2023-02-23 14:31:47',	1),
(2,	1,	2,	'2023-02-23 14:31:57',	'2023-02-23 14:31:57',	1),
(3,	1,	3,	'2023-02-23 14:32:02',	'2023-02-23 14:32:02',	1),
(4,	1,	4,	'2023-02-23 14:32:11',	'2023-02-23 14:32:11',	1);
";
array_push($sqlList, $sql8);  

foreach ($sqlList as $k => $v) {
    if ($conn->query($v) === TRUE) {
        echo "Tables created successfully" . PHP_EOL;
      } else {
        echo "Error creating table: " . $conn->error . " " . PHP_EOL;
      }
}

$conn->close();

?>
