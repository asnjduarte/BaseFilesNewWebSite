<?php

$servername = "localhost:10047";
$username = "root";
$password = "root";
$dbname = "local";

$table_prefix = "wp";
$companyName = "ND Test";
$contactUsPageTxt = "Contact Us";
$contactUsPageLnk = "contact-us";

$ajax_data = '
//add section inside of !empty($_SERVER...) 
//contact us information has been submitted
if(isset( $_POST["submitted"] )) {
    $factory = new Factory();

    //insert data into db
    $contact = $factory->createContact();
    $contact->setContact($_POST["firstName"], $_POST["lastName"], $_POST["email"], $_POST["phone"], $_POST["comment"], $_POST["user_registration_privacy_check"], false);
    $contact->insertContact();

    //send email
    $email = $factory->createEmail();
    $body = "Thank you " . $_POST["firstName"] . " for contacting us! We may contact you via your email at " . $_POST["email"] . " or via your phone number at " . $_POST["phone"] . 
    "We will answer your question: " . $_POST["comment"] . ", shortly!";
    $email->setEmail($_POST["email"], "Gracias de contactarnos!", $body);
    $email->sendEmail();

    echo json_encode(array("message"=>"Thank you for contacting us " . $_POST["firstName"] . "! We will contact you shortly.")); 
}
';
$ajax = fopen(__DIR__.'\ajax\AjaxCalls.php', "a");
fwrite($ajax, $ajax_data);

$css_data = '
.t30 {top:30px;}
.l5 {left:5px;}
.ptb5 {padding-top:5px; padding-bottom:5px;}
';
$css = fopen(__DIR__.'\css\Contact.css', "w");
fwrite($css, $css_data);

$js_data = '
$(document).ready(function(){ 
    $(\'#wp-send-email\').on(\'click\',function(e){ 
    	e.preventDefault();
    	if (checkContactFields()) {
    	    const urlParams = new URLSearchParams(window.location.search);
			$(\'#wp-send-email\').val(\'Enviando Correo\');
	        $(\'#spinner\').addClass("active-spinner");
    		$.ajax({
    			type:\'POST\',
    			url:\'?page_id=68\',
    			dataType: "json",
    			headers: {\'Csrftoken\': $(\'meta[name="csrf-token"]\').attr(\'content\')},
    			data: $(\'#contactForm\').serialize(),
    			success: function(result){
    			    $(\'#contactMessageId\').html("");
    				$(\'#contactMessageId\').append( \'<div class="feedbck-box lf1 w100 ptb5">\' + result["message"] + \'</div>\');
    				
    			},error: function (request, status, error) {
					alert("Error");
				}
    		}).done(function(result){
			    $(\'#wp-send-email\').val(\'Send Email\');
			    $(\'#spinner\').removeClass("active-spinner");
    		});
    	}
    });
    
    $(\'#email\').on(\'input\',function(e){
	    email_error.textContent = "";
	    $(\'#email_error\').removeClass(\'error\');
    });
    $(\'#commentsText\').on(\'input\',function(e){
	    comment_error.textContent = "";
	    $(\'#comment_error\').removeClass(\'error\');
    });	
    $(\'#user_registration_privacy_check\').on(\'input\',function(e){
	    comment_error.textContent = "";
	    $(\'#privacy_error\').removeClass(\'error\');
    });	

});

function checkContactFields(){
	var email = document.forms["contactForm"]["email"];
	var comment = document.forms["contactForm"]["commentsText"];
	var privacy = document.querySelector("#user_registration_privacy_check");
	var email_error = document.getElementById("email_error");
	var comment_error = document.getElementById("comment_error");
	var privacy_err = document.getElementById("privacy_error")
	var returnValue = true;
	
	if (email.value == "") {
			email_error.textContent = "Please enter a username";
			email.focus();
			returnValue = false;
			$(\'#email_error\').addClass(\'error\');
		}
	if (comment.value == "") {
			comment_error.textContent = "Please enter a password";
			comment.focus();
			returnValue = false;
			$(\'#comment_error\').addClass(\'error\');
			
	}
	if (privacy.checked == false) {
	    privacy_err.textContent = "Please select the privacy terms to continue";
	    privacy.focus();
		returnValue = false;
		$(\'#privacy_error\').addClass(\'error\');
	}
	return returnValue;
}
';
$js = fopen(__DIR__.'\js\Contact.js', "w");
fwrite($js, $js_data);

$user_dir = __DIR__.'\model\user';
if (!file_exists($user_dir)) {
    mkdir($user_dir, 0777, true);
}

$contact_data = '
<?php

class Contact {

    private $firstName;
    private $lastName;
    private $email;
    private $phone;
    private $createDate;
    private $comment;
    private $isPrivacyAgreed;
    private $wasUserContacted;
    private $factory;
    private $err;
    private $sanitize;
    private $contactUsId;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
        $this->sanitize = $this->factory->createSanitize();
    }

    public function getContactUsId() {return $this->contactUsId;}
    public function getFirstName(){return $this->firstName;}
    public function getLastName(){return $this->lastName;}
    public function getEmail(){return $this->email;}
    public function getPhone(){return $this->phone;}
    public function getCreateDate(){return $this->createDate;}
    public function getComment(){return $this->comment;}
    public function getIsPrivacyAgreed(){return $this->isPrivacyAgreed;}
    public function getWasUserContacted(){return $this->wasUserContacted;}

    private function setFirstName($firstName){
        $firstName = sanitize_text_field($firstName);
        $this->firstName = Sanitize::cleanTextNoNumberNoSpecial($firstName);
    }
    private function setLastName($lastName){
        $lastName = sanitize_text_field($lastName);
        $this->lastName = Sanitize::cleanTextNoNumberNoSpecial($lastName);
    }
    private function setEmail($email){
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $this->email = $email;
    }
    private function setPhone($phone){
        $this->phone = ($phone);
    }
    private function setComment($comment){
        $comment = sanitize_text_field($comment);
        $this->comment = Sanitize::cleanTextSomeSpecial($comment);
    }
    private function setIsPrivacyAgreed($isPrivacyAgreed){
        $isPrivacyAgreed = filter_var($isPrivacyAgreed, FILTER_VALIDATE_BOOLEAN);
        $this->isPrivacyAgreed = $isPrivacyAgreed;
    }
    
    private function setWasUserContacted($wasUserContacted){
        $wasUserContacted = filter_var($wasUserContacted, FILTER_VALIDATE_BOOLEAN);
        $this->wasUserContacted = $wasUserContacted;
    }
    private function seContactUsId($contactUsId) {$this->contactUsId = $contactUsId;}

    //set the contact info of object
    public function setContact($firstName, $lastName, $email, $phone, $comment, $isPrivacyAgreed, $wasUserContacted) {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setPhone($phone);
        $this->setComment($comment);
        $this->setIsPrivacyAgreed($isPrivacyAgreed);
        $this->setWasUserContacted($wasUserContacted);
    }

    //encrypt and insert contact into db
    public function insertContact(){ 
        try { 
            $encrypt = $this->factory->createEncryption();
            $query="CALL sp_insert_contact_info(\'%s\', \'%s\', \'%s\', \'%s\',\'%s\', \'%s\', \'%s\', \'%s\')";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query,  $encrypt->encryptTxt($this->getFirstName()), $encrypt->encryptTxt($this->getLastName()), $encrypt->encryptTxt($this->getEmail()),  $encrypt->encryptTxt($this->getPhone()), $this->getComment(), $this->getIsPrivacyAgreed(), $this->getWasUserContacted(), base64_encode($encrypt->getKey())),ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $query = "CALL sp_insert_error(\'%s\', \'%s\', \'%s\', \'%s\')";
                $this->wpdb->get_results($this->wpdb->prepare($query, \'insert contact\', $this->wpdb->last_error , \'sp_insert_contact_info\', session_id()),ARRAY_A);
            }
        } catch (Exception $e) {
            $this->err->addError("Contact.php", $e, "getActiveCountryDb()", $this->wpdb);
        } 
    }

    //fetch list of contacts from db
    private function fetchContactUserInfoFromDb() {
        try { 
            $query="CALL sp_fetch_contact_us_info()";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query),ARRAY_A);
            //error if db error is encountered, the general catch exception will not catch db errors
            if($this->wpdb->last_error !== \'\') {
                $query = "CALL sp_insert_error(\'%s\', \'%s\', \'%s\', \'%s\')";
                $this->wpdb->get_results($this->wpdb->prepare($query, \'Contact.php\', $this->wpdb->last_error , \'fetchContactUserInfoFromDb\', session_id()),ARRAY_A);
            }
        } catch (Exception $e) {
            $this->err->addError("Contact.php", $e, "fetchContactUserInfoFromDb", $this->wpdb);
        } 
        return $result;
    }

    //set list of contacts fetched from db
    public function setContactUserInfo() {
        //fetch user to contact from the db
        $userContactInfoList = $this->fetchContactUserInfoFromDb();
        
        //create a list of contact objects
        $userList = array();
        $decrypt = $this->factory->createEncryption();
        foreach ($userContactInfoList as $k => $v) {
            $c = new Contact();
            //set the key from the db for each contact
            $decrypt->setKey(base64_decode($v["eKey"]));

            //decrypt and set the contact info
            $c->setContact($decrypt->decryptString($v["firstName"]), $decrypt->decryptString($v["lastName"]), $decrypt->decryptString($v["email"]), $decrypt->decryptString($v["phone"]), $v["comment"], 1, 0);
            $c->seContactUsId($v["contactUsId"]);
            array_push($userList, $c);
        }
        return $userList;
    }
}
?>
';
$contact = fopen(__DIR__.'\model\user\Contact.php', "w");
fwrite($contact, $contact_data);

$view_data = '
<?php include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); ?>	
<?php include get_theme_file_path(\'controller/FooterControllerDb.php\');  ?>
<div class="lf1 pt3">
<div class="w100 ha flx rel mb_Flxdc mb_ha">
	<div class="h700 w50 abs logoFoto baf abs mb_w100 cnnr o1"></div>
	<div class="w50 ma p20 mb_w100 z1">
		<div class="gBx gBxShd2Wht">
			<h2>Directions</h2>
			<div>
				<div class="ptb5"><?php echo $companyInfo[5]->getName();?></div>
			</div>
		</div>
        <div class="gBx gBxShd2Wht">
			<h2>Subscribe</h2>
			<div>to our email list <span><a href="#">Here</a></span>!</div>
		</div>
        <div class="gBx gBxShd2Wht">
			<h2>Contact us via</h2>
			<div class="flx fwrap">
				<?php  foreach ($companyInfo as $k => $v) { 
					  if ($v->getType() == 1) { //type 1 = contact?>
						<div class="p10 pl0">
							<a class="flx fwrap aic" href="<?php echo $v->getLink()?>" target="_blank">
							<div class="bx30 <?php echo $v->getImg()?> cnnr"></div>
							<?php echo $v->getValue()?></a>
						</div>
				<? 	 } else if ($v->getType() == 2) { // type 2 = social ?>
					<div class="p10 pl0">
						<a href="<?php echo $v->getLink()?>" target="_blank"><div class="bx30 <?php echo $v->getImg()?> cnnr"></div></a>
					</div>
				<?php }} ?>
			</div>
		</div>
	</div>
	<div class="h700 w50 abs bgDkGr baf r0 mb_w100 mb_ha mb_rel"></div>
	<div class="w50 ma bgDkGr rel p20 tc mb_w100">
		<?php include get_theme_file_path(\'view/contactFormView.php\');   ?>
	</div>
</div>
</div>
';
$view = fopen(__DIR__.'\view\TwoColumnContactView.php', "w");
fwrite($view, $view_data);

$contact_admin_form_view_data = '
<?php

$user = wp_get_current_user();
$allowed_roles = array(\'administrator\', \'editor\');
if( array_intersect($allowed_roles, $user->roles ) ) { 
    include_once get_theme_file_path(\'model/commonFunctions/Factory.php\');

    $factory = new Factory();
    $contact = $factory->createContact();
    $contactList = $contact->setContactUserInfo();
?>

    <div class="flx fwrap pt3">
        <?php foreach ($contactList as $k => $v) { ?>
            <div class="gBx w30 gBxShd2Wht h300 ml05 ofya">
                <div class="p10"><?php echo \'Nombre: \' . $v->getFirstName() . \' \' . $v->getLastName()?></div>
                <div class="p10"><?php echo \'Correo: \' . $v->getEmail() ?></div>
                <div class="p10"><?php echo \'Télefono: \' . $v->getPhone() ?></div>
                <div class="p10"><?php echo \'Comento: \' . $v->getComment() ?></div>
                <div class="p10">
                    <button id="btnIsUserContacted" class="w50 gBoxShadow" type="submit">Usuario contactado</button>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
';
$contact_admin_form_view = fopen(__DIR__.'\view\AdminContactView.php', "w");
fwrite($contact_admin_form_view, $contact_admin_form_view_data);

$contact_form_view_data = '
<h2>Contact Form</h2>
<form action="#" id="contactForm" method="post" class="form">
    <fieldset>
        <div class="lft-rgt tt sos">
            <label for="email" >Email*:</label>
            <input type="text" name="email" id="email" value=""/>
            <div id="email_error" class="val_error"></div>
        </div>
        <div class="lft-rgt tt td50 sos">
            <label for="commentsText">Message*:</label>
            <textarea name="comment" id="commentsText" rows="4" cols="30" class="w100 gBoxShadow lf1"></textarea>
            <div id="comment_error" class="val_error"></div>
        </div>
        <div class="lft-rgt tt td50 sos">
            <input type="checkbox" id="user_registration_privacy_check" name="user_registration_privacy_check" value="true"/>
            <label for="user_registration_privacy_check"> By selecting, you consent to our privacy terms: 
                </br><a class="cDb" href="/politica-de-privacidad/" target="_blank">Terminos</a>
            </label>
            <div id="privacy_error" class="val_error"></div>
        </div>
        <div class="lft-rgt tt td100 sos">
            <label for="firstName">First Name:</label>
            <input type="text" name="firstName" id="firstName" value=""/>
        </div>
        <div class="lft-rgt tt td150 sos">
            <label for="lastName">Last Name:</label>
            <input type="text" name="lastName" id="lastName" value=""/>
        </div>
        
        <div class="lft-rgt tt td200 sos">
            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" value=""/>
        </div>
        <div class="lft-rgt tt td250 sos w35 ma">
            <div id="spinner" class="w100 abs t30 l5"> </div>
            <input id="wp-send-email" class="gBx gBxShd2Wht gBoxShadow w100" type="submit" value="Send Email" name="wp-send-email">
        </div>
    </fieldset>
        <input type="hidden" name="submitted" id="submitted" value="true"/>
</form>
<div id="contactMessageId"></div>
';
$contact_form_view = fopen(__DIR__.'\view\ContactFormView.php', "w");
fwrite($contact_form_view, $contact_form_view_data);

$contact_admin_function_data = '
<?php //admin contact us view
$page_slug = \'admin-contact-view\'; // Slug of the Post
$new_page = array(
    \'post_type\'     => \'page\', 				// Post Type Slug eg: \'page\', \'post\'
    \'post_title\'    => \'Admin Contact View\',	// Title of the Content
    \'post_content\'  => \'[display_admin_contact_view]\',	// Content
    \'post_status\'   => \'publish\',			// Post Status
    \'post_author\'   => 1,					// Post Author ID
    \'post_name\'     => $page_slug			// Slug of the Post
);

if (!get_page_by_path( $page_slug, OBJECT, \'page\')) { // Check If Page Not Exits
    $new_page_id = wp_insert_post($new_page);
}


function fetch_acv(){add_shortcode(\'display_admin_contact_view\', \'fetch_admin_contact_view\');} 
add_action(\'init\', \'fetch_acv\');
function fetch_admin_contact_view() {
    ob_start(); 
    include_once get_theme_file_path(\'view/AdminContactView.php\'); 
    return ob_get_clean();
}
?>
';
$contact_admin_function = fopen(__DIR__.'\functions.php', "a");
fwrite($contact_admin_function, $contact_admin_function_data);

$contact_function_data = '
<?php //contact us
$page_slug = \''.$contactUsPageLnk.'\'; // Slug of the Post
$new_page = array(
    \'post_type\'     => \'page\', 				// Post Type Slug eg: \'page\', \'post\'
    \'post_title\'    => \''.$contactUsPageTxt.'\',	// Title of the Content
    \'post_content\'  => \'[display_contact_view]\',	// Content
    \'post_status\'   => \'publish\',			// Post Status
    \'post_author\'   => 1,					// Post Author ID
    \'post_name\'     => $page_slug			// Slug of the Post
);

if (!get_page_by_path( $page_slug, OBJECT, \'page\')) { // Check If Page Not Exits
    $new_page_id = wp_insert_post($new_page);
}

function fetch_cv(){add_shortcode(\'display_contact_view\', \'fetch_contact_view\');} 
add_action(\'init\', \'fetch_cv\');
function fetch_contact_view() {
    ob_start(); 
    include_once get_theme_file_path(\'view/TwoColumnContactView.php\'); 
    wp_enqueue_style(\'contact-css\', get_template_directory_uri().\'/css/Contact.css\', \'\', microtime());
    wp_enqueue_script(\'contact-js\', get_template_directory_uri().\'/js/Contact.js\', NULL, microtime(), true);
    return ob_get_clean();
}
?>
';
$contact_function = fopen(__DIR__.'\functions.php', "a");
fwrite($contact_function, $contact_function_data);

$contact_factory_data = '
/*add section to the top of the Factory.php*/
include_once get_theme_file_path("model/user/Contact.php"); 

/*add section into the Factory class*/
public static function createContact(){
    return new Contact();
}
';
$contact_factory = fopen(__DIR__.'\model\commonFunctions\Factory.php', "a");
fwrite($contact_factory, $contact_factory_data);


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to create table
$sqlList = array ();
$sql2 =
"
CREATE TABLE `".$table_prefix."_contact_us`(
    `contactUsId` int(11) NOT NULL AUTO_INCREMENT,
    `firstName` text NOT NULL,
    `lastName` text NOT NULL,
    `email` text NOT NULL,
    `phone` text NOT NULL,
    `createDate` datetime NOT NULL,
    `comment` text NOT NULL,
    `isPrivacyAgreed` bit(1) NOT NULL,
    `wasUserContacted` bit(1) NOT NULL,
    `eKey` blob NOT NULL,
    PRIMARY KEY (`contactUsId`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
array_push($sqlList, $sql2);  

$sql3 =
"
CREATE PROCEDURE `sp_insert_contact_info`(IN `f_n` text, IN `l_n` text, IN `e` text, IN `p` text, IN `c` text, IN `ipa` bit, IN `wuc` bit, IN `k` text)
BEGIN
insert into ".$table_prefix."_contact_us(firstName, lastName, email, phone, createDate, comment, isPrivacyAgreed, wasUserContacted, eKey) 
values (f_n, l_n, e, p, CURRENT_TIMESTAMP(), c, ipa, wuc, k);
END
";
array_push($sqlList, $sql3);  

$sql4 =
"
CREATE PROCEDURE `sp_fetch_contact_us_info`()
BEGIN
SELECT cui.contactUsId, cui.firstName, cui.lastName, cui.email, cui.phone, cui.wasUserContacted, cui.comment, cui.eKey
FROM ". $table_prefix . "_contact_us cui 
WHERE cui.wasUserContacted = 0 
LIMIT 10;
END
";
array_push($sqlList, $sql4);  

$sql8 =
"
INSERT INTO `". $table_prefix . "_menu_header` (`link`, `text`, `roleId`) VALUES
('/admin-contact-view/',	'Admin Contact View',	1);
";
array_push($sqlList, $sql8);  

$sql9 =
"
INSERT INTO `". $table_prefix . "_menu_header` (`link`, `text`, `roleId`) VALUES
('/".$contactUsPageLnk."/',	'".$contactUsPageTxt."',	2);
";
array_push($sqlList, $sql9);  

foreach ($sqlList as $k => $v) {
    if ($conn->query($v) === TRUE) {
        echo "Tables created successfully" . PHP_EOL;
      } else {
        echo "Error creating table: " . $conn->error . " " . PHP_EOL;
      }
}
?>
