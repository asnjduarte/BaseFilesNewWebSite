<?php

/*create folders*/
$ajax_dir = __DIR__.'\ajax';
if (!file_exists($ajax_dir)) {
    mkdir($ajax_dir, 0777, true);
}

$view_dir = __DIR__.'\view';
if (!file_exists($view_dir)) {
    mkdir($view_dir, 0777, true);
}

$model_dir = __DIR__.'\model';
if (!file_exists($model_dir)) {
    mkdir($model_dir, 0777, true);
}

$css_dir = __DIR__.'\css';
if (!file_exists($css_dir)) {
    mkdir($css_dir, 0777, true);
}

$js_dir = __DIR__.'\js';
if (!file_exists($js_dir)) {
    mkdir($js_dir, 0777, true);
}

$controller_dir = __DIR__.'\controller';
if (!file_exists($controller_dir)) {
    mkdir($controller_dir, 0777, true);
}

$commonFunctions_dir = __DIR__.'\model\commonFunctions';
if (!file_exists($commonFunctions_dir)) {
    mkdir($commonFunctions_dir, 0777, true);
}

//used for database 
$migrations_dir = __DIR__.'\migrations';
if (!file_exists($migrations_dir)) {
    mkdir($migrations_dir, 0777, true);
}

//used for the footer 
$company_dir = __DIR__.'\model\company';
if (!file_exists($company_dir)) {
    mkdir($company_dir, 0777, true);
}

//used for the footer 
$country_dir = __DIR__.'\model\country';
if (!file_exists($country_dir)) {
    mkdir($country_dir, 0777, true);
}
echo "Folders have been created";

$function_data = '

<?php

/** Basic Content Security Policy**/
function pagely_security_headers( $headers ) {
    $headers[\'X-XSS-Protection\'] = \'1; mode=block\';
    $headers[\'X-Content-Type-Options\'] = \'nosniff\';
    return $headers;
}

add_filter( \'wp_headers\', \'pagely_security_headers\' );
add_action( \'send_headers\', \'send_frame_options_header\', 10, 0 ); //x-frame-options
/** End CSP **/

/** Disable word press feed **/
function wpb_disable_feed() {
wp_die( __(\'No feed available,please visit our <a href="\'. get_bloginfo(\'url\') .\'">homepage</a>!\') );
}
 
add_action(\'do_feed\', \'wpb_disable_feed\', 1);
add_action(\'do_feed_rdf\', \'wpb_disable_feed\', 1);
add_action(\'do_feed_rss\', \'wpb_disable_feed\', 1);
add_action(\'do_feed_rss2\', \'wpb_disable_feed\', 1);
add_action(\'do_feed_atom\', \'wpb_disable_feed\', 1);
add_action(\'do_feed_rss2_comments\', \'wpb_disable_feed\', 1);
add_action(\'do_feed_atom_comments\', \'wpb_disable_feed\', 1);
/** End **/

/** Enables the HTTP Strict Transport Security (HSTS) header in WordPress. */
function enable_strict_transport_hsts_preload() {header( "Strict-Transport-Security: max-age=31536000; includeSubDomains; preload" );}
add_action(\'send_headers\', \'enable_strict_transport_hsts_preload\' );
/** End **/

/*Limit excerpt length*/
function tn_custom_excerpt_length( $length ) {
    return 10;
    }
    add_filter( \'excerpt_length\', \'tn_custom_excerpt_length\', 999 );
/*Limit excerpt length*/

/****************************remove admin bar*************************/
function remove_admin_bar() {
if (!current_user_can(\'administrator\') && !is_admin()) {
  show_admin_bar(false);
}
}
add_action(\'after_setup_theme\', \'remove_admin_bar\');
/****************************remove admin bar*************************/

/**Block access to wp-admin for non admin users**/
function block_wp_admin() {
	if ( is_admin() && ! current_user_can( \'administrator\' ) && ! ( defined( \'DOING_AJAX\' ) && DOING_AJAX ) ) {
		wp_safe_redirect( home_url() );
		exit;
	}
}
add_action( \'admin_init\', \'block_wp_admin\' );
/**Block access to wp-admin for non admin users**/

function my_scripts() {

    wp_enqueue_style(\'main_styles\', get_stylesheet_uri(), \'\', microtime());
    wp_enqueue_style(\'google-font-montserrat\', \'https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap\', false );
    wp_enqueue_script(\'jqry_min_3.6.0\', \'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js\', NULL, \'1.0.0\', false);
    wp_enqueue_script(\'footer-script-bundle\', get_template_directory_uri().\'/js/FooterBundle.js\', NULL, microtime(), true);
    wp_enqueue_style(\'bootstrap_styles\', \'https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css\', \'\', microtime());
    wp_enqueue_script(\'slim_js\', \'https://code.jquery.com/jquery-3.3.1.slim.min.js\', NULL, microtime(), true);
    wp_enqueue_script(\'popper_js\', \'https://cdn.jsdelivr.net/npm/popper.js@1.14.3/dist/umd/popper.min.js\', NULL, microtime(), true);
    wp_enqueue_script(\'bootstrap_js\', \'https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js\', NULL, microtime(), true);
    
    if( is_page( array(5)) || is_front_page() ){  
        //wp_enqueue_style(\'indx-css\', get_template_directory_uri().\'/css/indx.css\', \'\', microtime());
        //wp_enqueue_script(\'indx-js\', get_template_directory_uri().\'/js/indx.js\', NULL, microtime(), true);
    } 

    $user = wp_get_current_user();
    $allowed_roles = array(\'administrator\', \'editor\');
    if( array_intersect($allowed_roles, $user->roles ) ) { 

    }
}

add_action(\'wp_enqueue_scripts\', \'my_scripts\' );
?>
';
$function = fopen(__DIR__.'\functions.php', "w");
fwrite($function, $function_data);


$css_data = '
/*Theme Name: Maranatha
Author: Nathan
Version: 1.2
*/
.bx30 {width:30px; height:30px;}
.cnnr {background-size: contain;}
.mv-rgt0 {visibility: hidden;}
.pLoad .mv-rgt0 {animation:moveInFromTopOpac 1s forwards;}
.lft-rgt {opacity: 0; -webkit-transform: translateX(-4em) rotateZ(0deg); transform: translateX(-4em) rotateZ(0deg);}
.lft-rgt.is-visible {opacity: 1;transform: rotateZ(0deg);}
.tt {transition: transform 4s .5s cubic-bezier(0,1,.3,1), opacity .25s .25s ease-out; transition: transform 4s .5s cubic-bezier(0,1,.3,1), opacity .25s .25s ease-out, -webkit-transform 4s .25s cubic-bezier(0,1,.3,1)}
.td50 {transition-delay: 50ms;}
.td100 {transition-delay: 100ms;}
.td150 {transition-delay: 150ms;}
.td200 {transition-delay: 200ms;}
.td250 {transition-delay: 250ms;}
/*footer*/
.wh {background-image:url(\'/wp-content/uploads/2023/images/social/whats.png\');}
.em {background-image:url(\'/wp-content/uploads/2023/images/social/email.png\');}
.fb {background-image:url(\'/wp-content/uploads/2023/images/social/fb.png\');}
.yt {background-image:url(\'/wp-content/uploads/2023/images/social/youtube.png\');}
.tyc {background-image:url(\'/wp-content/uploads/2023/images/social/papers.png\');}
.in {background-image:url(\'/wp-content/uploads/2023/images/social/inst.png\');}
.logoFoto {background-image:url(\'/wp-content/uploads/2023/images/social/tdclogo.jpg\');}
/*end footer*/
@media screen and (min-width: 1600px) {
    
}
@media screen and (max-width:1024px) {

}
@media screen and (max-width: 820px) {

}
';
$css = fopen(__DIR__.'\style.css', "w");
fwrite($css, $css_data);

$index_data = '
<?php 
get_header();?>

<div class="h700"></div>
<?php get_footer();

?>
';
$index = fopen(__DIR__.'\index.php', "w");
fwrite($index, $index_data);

$header_data = '
<html lang="es"	<?php language_attributes(); ?>>
<?php the_custom_header_markup();  if(session_id() == \'\') {session_start();}?>
<head>
	<?php wp_head(); ?>
	<meta name="csrf-token" content="<?php echo $_SESSION[\'token\']; ?>"/>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

';
$header = fopen(__DIR__.'\header.php', "w");
fwrite($header, $header_data);

$decrypt_data = '
<?php class Encryption {
    private $key;
    private $factory;
    private $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
        $this->key = openssl_random_pseudo_bytes(16);
    }

    public function getKey() {return $this->key;}
    public function setKey($key){$this->key = $key;}

    public function decryptString($val) {
        $c = base64_decode($val);
        $ivlen = openssl_cipher_iv_length($cipher="aes-256-ctr");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $password = openssl_decrypt($ciphertext_raw, $cipher, $this->getKey(), $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac("sha256", $ciphertext_raw, $this->getKey(), $as_binary=true);
            $validPassword = $password;
            $query = "CALL sp_insert_error(\'%s\', \'%s\', \'%s\', \'%s\')";
            $this->wpdb->get_results($this->wpdb->prepare($query, \'Decrypt\', $val , \'decryptString\', session_id()),ARRAY_A);
        return $validPassword;
    }

    public function encryptTxt ($plaintext) {
        $key = $this->getKey();
        $ivlen = openssl_cipher_iv_length($cipher="aes-256-ctr");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac(\'sha256\', $ciphertext_raw, $key, $as_binary=true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
        return $ciphertext;
    }
}?>';
$decrypt = fopen(__DIR__.'\model\commonFunctions\Encryption.php', "w");
fwrite($decrypt, $decrypt_data);
echo "Decrypt created and class added";

$email_data = '
<?php 

class Email{ 
    private $emailTo;
    private $subject;
    private $body;
    private $headers;
    private $factory;
    private $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
        $this->headers = array("Content-Type: text/html; charset=UTF-8");
    }

    public function setEmailTo($additionalEmail) {
        $email = "";
        if(!empty($additionalEmail))
            $email = "," . $additionalEmail;
        $this->emailTo = "gnat1120@gmail.com, nathanduarte@maranathamarketing.net" . $email;
    }
    public function getEmailTo() {return $this->emailTo;}
    public function setSubject($subject) {$this->subject = $subject;}
    public function getSubject() {return $this->subject;}
    public function setBody($body) {$this->body = $body;}
    public function getBody() {return $this->body;}
    public function getHeaders() {return $this->headers;}

    public function setEmail($emailTo, $subject, $body){
        $this->setEmailTo($emailTo);
        $this->setSubject($subject);
        $this->setBody($body);
    }

    public function sendEmail(){
        wp_mail($this->getEmailTo(), $this->getSubject(), $this->getBody(), $this->getHeaders());
    }
}
?>
';
$email = fopen(__DIR__.'\model\commonFunctions\Email.php', "w");
fwrite($email, $email_data);
echo "Email created and class added";

$factory_data = '

<?php
include_once get_theme_file_path("model/commonFunctions/LogError.php"); 
include_once get_theme_file_path("model/commonFunctions/PopUp.php");
include_once get_theme_file_path("model/commonFunctions/Email.php");
include_once get_theme_file_path("model/commonFunctions/Encryption.php"); 
include_once get_theme_file_path("model/commonFunctions/StopWatch.php"); 
include_once get_theme_file_path("model/commonFunctions/Sanitize.php"); 
include_once get_theme_file_path("model/HeaderMenu.php"); 
include_once get_theme_file_path("model/company/Company.php"); 


class Factory {

    public function __construct(){}
    
    public static function createErr(){
        return new LogError();
    }
    
    public static function createPopUp(){
        return new PopUp();
    }
    
    public static function createEmail(){
        return new Email();
    }

    public static function createHeaderMenu(){
        return new HeaderMenu();
    }

    public static function createCompany(){
        return new Company();
    }

    public static function createEncryption(){
        return new Encryption();
    }

    public static function createSanitize(){
        return new Sanitize();
    }

}
?>

';
$factory = fopen(__DIR__.'\model\commonFunctions\Factory.php', "w");
fwrite($factory, $factory_data);
echo "Factory created and class added";

$error_data = '
<?php

class LogError {
    private $page;
    private $err;
    private $func;

    public function __constructor() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    public function getPage() {return $this->page;}
    public function getErr() {return $this->err;}
    public function getFunction() {return $this->func;}

    public function setPage($page) {
        $page = sanitize_text_field($page);
        $this->page = $page;
    }
    public function setErr($err) {
        $err = sanitize_text_field($err);
        $this->err = $err;
    }
    public function setFunction($func) {
        $func = sanitize_text_field($func);
        $this->func = $func;
    }
    
    private function setErrorLog($p, $e, $f) {
        $this->setPage($p);
        $this->setErr($e);
        $this->setFunction($f);
    }
    public function addError($p, $e, $f,$db) {

        $this->setErrorLog($p, $e, $f);
        $query = "CALL sp_insert_error(\'%s\', \'%s\', \'%s\', \'%s\')";
        $db->get_results($db->prepare($query, $this->getPage(), $this->getErr(), $this->getFunction(), session_id()),ARRAY_A);
    }
}
?>
';
$error = fopen(__DIR__.'\model\commonFunctions\LogError.php', "w");
fwrite($error, $error_data);
echo "Error created and class added";

$popUp_data = '
<?php

class PopUp {
    private $boxId;
    private $h2;
    private $h3;
    private $txt;
    private $btn1;
    private $btn2;
    private $urlBtn1;
    protected $factory;
    protected $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    private function setBoxId($bid) {$this->boxId = $bid;}
    public function getBoxId(){return $this->boxId;}
    private function setH2($h2) {$this->h2 = $h2;}
    public function getH2(){return $this->h2;}
    private function setH3($h3) {$this->h3 = $h3;}
    public function getH3(){return $this->h3;}
    private function setTxt($txt) {$this->txt = $txt;}
    public function getTxt(){return $this->txt;}
    private function setBtn1($btn1) {$this->btn1 = $btn1;}
    public function getBtn1(){return $this->btn1;}
    private function setBtn2($btn2) {$this->btn2 = $btn2;}
    public function getBtn2(){return $this->btn2;}
    private function setUrlBtn1($urlBtn1) {$this->urlBtn1 = $urlBtn1;}
    public function getUrlBtn1(){return $this->urlBtn1;}

    public function getPopUp($cid, $bid) {
        $popup = new PopUp();
        try {
            $query = "CALL sp_fetch_generic_popup(\'%d\', \'%s\');";
            $stmt = $this->wpdb->get_results($this->wpdb->prepare($query, $cid, $bid), ARRAY_A);
            $popup->setBoxId($stmt[0]["boxId"]);
            $popup->setH2($stmt[0]["h2"]);
            $popup->setH3($stmt[0]["h3"]);
            $popup->setTxt($stmt[0]["txt"]);
            $popup->setBtn1($stmt[0]["btn1"]);
            $popup->setBtn2($stmt[0]["btn2"]);
            $popup->setUrlBtn1($stmt[0]["urlBtn1"]);
        } catch (Exception $e) {
            $this->err->setErrorLog("PopUp.php", $e, "getPopUp");
            $this->err->addError($this->wpdb);
        }
        return $popup;
    }
}
?>
';
$popUp = fopen(__DIR__.'\model\commonFunctions\PopUp.php', "w");
fwrite($popUp, $popUp_data);
echo "PopUp created and class added";

$stop_watch_data = '
<?php
class StopWatch {
  /**
   * @var $startTimes array The start times of the StopWatches
   */
  private static $startTimes = array();

  /**
   * Start the timer
   * 
   * @param $timerName string The name of the timer
   * @return void
   */
  public static function start($timerName = "default") {
    self::$startTimes[$timerName] = microtime(true);
  }

  /**
   * Get the elapsed time in seconds
   * 
   * @param $timerName string The name of the timer to start
   * @return float The elapsed time since start() was called
   */
  public static function elapsed($timerName = "default") {
    return microtime(true) - self::$startTimes[$timerName];
  }
}
?>
';
$stopWatch = fopen(__DIR__.'\model\commonFunctions\StopWatch.php', "w");
fwrite($stopWatch, $stop_watch_data);
echo "StopWatch created and class added";

$splash_js_data = '
$(document).ready(function(){
 
 });

 /* sanitize with jquery */
 function txtOnly() {
    //var regExp = 
    var string = $(this).val().replace(/[^a-zA-ZáíóúéÁÍÓÚÉñÑ ]/g,""); 
    $(this).val(string);
   }
   $(\'.txtOnly\').keyup(txtOnly);
   
   function numOnly() { 
    var num = $(this).val().replace(/\D/g,""); 
    $(this).val(\'(\' + num.substring(0,3) + \') \' + num.substring(3,6) + \'-\' + num.substring(6,10)); 
   }
   $(\'[type="tel"]\').keyup(numOnly);
   
   function acceptedChar() {
    var string = $(this).val().replace(/[^a-zA-Z#0-9áíóúéÁÍÓÚÉñÑ!:\/.\-; ]/g,\'\'); 
    $(this).val(string);
   }
   $(\'.acceptedChar\').keyup(acceptedChar);

   function acceptedUrl() {
    var string = $(this).val().replace(/[^_0-9A-Za-záíéóúñÁÍÉÓÚÑ!.?;:=#&\- \/]/g,\'\'); 
    $(this).val(string);
   }
   $(\'.acceptedUrl\').keyup(acceptedUrl);
   
   function emailChar() {
    var string = $(this).val().replace(/[^a-zA-ZáíóúéÁÍÓÚÉñÑ@0-9._\-]/g,\'\'); 
    $(this).val(string);
   }
   $(\'.emailChar\').keyup(emailChar);

   function acceptedNumbers() {
    var string = $(this).val().replace(/[^0-9:\-]/g,\'\'); 
    $(this).val(string);
   }
   $(\'.acceptedNumbers\').keyup(acceptedNumbers);
   /* end sanitize with jquery */
   
   /*Scroll animation*/
(function(){
    // Detect request animation frame
var scroll = window.requestAnimationFrame ||
         function(callback){ window.setTimeout(callback, 1000/60)};
   var elementsToShow = document.querySelectorAll(\'.sos\');
   function loop() {
 elementsToShow.forEach(function (element) {
   if (isElementInViewport(element)) {
     element.classList.add(\'is-visible\');
   } else {
     element.classList.remove(\'is-visible\');
   }
 });

 scroll(loop);
}
// Call the loop for the first time
loop();

// Helper function from: http://stackoverflow.com/a/7557433/274826
function isElementInViewport(el) {
 // special bonus for those using jQuery
 if (typeof jQuery === "function" && el instanceof jQuery) {
   el = el[0];
 }
 var rect = el.getBoundingClientRect();
 return (
   (rect.top <= 0
     && rect.bottom >= 0)
   ||
   (rect.bottom >= (window.innerHeight || document.documentElement.clientHeight) &&
     rect.top <= (window.innerHeight || document.documentElement.clientHeight))
   ||
   (rect.top >= 0 &&
     rect.bottom <= (window.innerHeight || document.documentElement.clientHeight))
 );
}
}());
/*End Scroll animation*/
';
$splash_js = fopen(__DIR__.'\js\FooterBundle.js', "w");
fwrite($splash_js, $splash_js_data);

/*used for the database*/
$migration_data = '
<?php declare(strict_types=1);

echo __DIR__;
return [
    \'migrations_paths\' => [
        \'migrations_directory\' => __DIR__ . \'\Migrations\',
        \'migrations_namespace\' => \'\Migrations\',
    ],
    
]
?>
';
$migrations = fopen(__DIR__.'\migrations.php', "w");
fwrite($migrations, $migration_data);
echo "migrations created and class added";

/*used for the database*/
$migration_db_data = '
<?php declare(strict_types=1);

require __DIR__ . \'/vendor/autoload.php\';

return [
    \'dbname\'=>\'local\',
    \'user\'=> \'root\',
    \'password\'=> "",
    \'host\'=> \'localhost:10060\',
    \'driver\'=> \'pdo_mysql\',
]
?>
';
$migration_db = fopen(__DIR__.'\migration-db.php', "w");
fwrite($migration_db, $migration_db_data);
echo "migration_db created and class added";

$header_controller_data = '
<?php 
    include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
    $factory = new Factory();
    $hMenu = $factory->createHeaderMenu();
    $hmList = $hMenu->setHeaderMenuData();
    
    if (empty($_SESSION[\'token\'])) {
        if (function_exists(\'mcrypt_create_iv\')) {
            $_SESSION[\'token\'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        } else {
            $_SESSION[\'token\'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
    }
?>
';
$header_controller = fopen(__DIR__.'\controller\HeaderMenuController.php', "w");
fwrite($header_controller, $header_controller_data);

$header_model_data = '
<?php

class HeaderMenu {
    private $link;
    private $text;
    private $roleId;
    private $factory;
    private $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    public function getLink(){return $this->link;}
    public function getText(){return $this->text;}
    public function getRoleId(){return $this->roleId;}
    
    public function setLink($link){$this->link = $link;}
    public function setText($text){$this->text = $text;}
    public function setRoleId($roleId){$this->roleId = $roleId;}

    public function getHeaderMenuDataDb() {
        try { 
            $query = "CALL 	sp_fetch_menu_header();";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query), ARRAY_A);
        } catch (Exception $e) {
            $this->err->addError("HeaderMenu.php", $e, "getHeaderMenuData() - sp_fetch_menu_header", $this->wpdb);
        } 
        return $result;
    }

    public function setHeaderMenuData() {
        $result = $this->getHeaderMenuDataDb();
        $list = array();
        if(!empty($result)) {
            foreach($result as $k => $v) {
                $hMenu = new HeaderMenu();
                $hMenu->setLink($v["link"]);
                $hMenu->setText($v["text"]);
                $hMenu->setRoleId($v["roleId"]);
                array_push($list,$hMenu);
            }
        } else {
            $this->err->addError("HeaderMenu.php", "fetched getHeaderMenuDataDb array is empty", "setHeaderMenuData()", $this->wpdb);
        }
        return $list;
    }

}
?>
';
$header_model = fopen(__DIR__.'\model\HeaderMenu.php', "w");
fwrite($header_model, $header_model_data);

$footer_model_company_data = '
<?php
class Company {
    private $countryId;
    private $name;
    private $linkName;
    private $value;
    private $img;
    private $link;
    private $type;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    public function getCountryId(){return $this->countryId;}
    public function setCountryId($countryId){$this->countryId = $countryId;}
    public function getName(){return $this->name;}
    public function setName($name){$this->name = $name;}
    public function getLinkName(){return $this->linkName;}
    public function setLinkName($linkName){$this->linkName = $linkName;}
    public function getValue(){return $this->value;}
    public function setValue($value){$this->value = $value;}
    public function getImg(){return $this->img;}
    public function setImg($img){$this->img = $img;}
    public function getLink(){return $this->link;}
    public function setLink($link){$this->link = $link;}
    public function getType(){return $this->type;} //type is a manual value, where 1 = contact, 2 = social media, 0 = miscellaneous
    public function setType($type){$this->type = $type;}

    public function getCompanyInfoDb() {
        try { 
            $query = "CALL 	sp_fetch_company_info();";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query), ARRAY_A);
        } catch (Exception $e) {
            $this->err->addError("Company.php", $e, "getCompanyInfoDb() - sp_fetch_company_info", $this->wpdb);
        } 
        return $result;
    }

    public function setCompanyInfo() {
        $result = $this->getCompanyInfoDb();
        $list = array();
        if(!empty($result)) {
            foreach($result as $k => $v) {
                $company = new Company();
                $company->setName($v["companyName"]);
                $company->setLinkName($v["linkName"]);
                $company->setValue($v["value"]);
                $company->setImg($v["img"]);
                $company->setLink($v["link"]);
                $company->setType($v["type"]);
                array_push($list,$company);
            }
        } else {
            $this->err->addError("Company.php", "fetched getCompanyInfoDb array is empty", "setCompanyInfo()", $this->wpdb);
        }
        return $list;
    }

}
?>
';
$footer_model_company = fopen(__DIR__.'\model\company\Company.php', "w");
fwrite($footer_model_company, $footer_model_company_data);

$footer_model_country_data = '
<?php 
class Country {
    private $countryId;
    private $name;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    public function getCountryId(){return $this->countryId;}
    public function setCountryId($countryId){$this->countryId = $countryId;}
    public function getName(){return $this->name;}
    public function setName($name){$this->name = $name;}

    public function getActiveCountryDb() {
        try { 
            $query = "CALL 	sp_fetch_active_country();";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query), ARRAY_A);
        } catch (Exception $e) {
            $this->err->addError("Country.php", $e, "getActiveCountryDb() - sp_fetch_active_country", $this->wpdb);
        } 
        return $result;
    }

    public function setActiveCountry() {
        $result = $this->getActiveCountryDb();
        $list = array();
        if(!empty($result)) {
            foreach($result as $k => $v) {
                $country = new Country();
                $country->setCountryId($v["countryId"]);
                $country->setName($v["name"]);
                array_push($list,$country);
            }
        } else {
            $this->err->addError("Country.php", "fetched getActiveCountryDb array is empty", "setActiveCountry()", $this->wpdb);
        }
        return $list;
    }
}
?>
';
$footer_model_country = fopen(__DIR__.'\model\country\Country.php', "w");
fwrite($footer_model_country, $footer_model_country_data);

$footer_data = '
<?php echo do_shortcode(\'[display_footer]\' ); ?>
';
$footer = fopen(__DIR__.'\footer.php', "w");
fwrite($footer, $footer_data);

$sanitize_data = '
<?php

class Sanitize {
    private static $val;

    public function __construct(){}

    public static function cleanNumeric($val){
        self::$val = preg_replace("/[^0-9()]/", \'\', $val);
        return self::$val;
    }

    public static function cleanTextNoNumberNoSpecial($val){
        self::$val = preg_replace(\'/[^A-Za-záíéóúñÁÍÉÓÚÑ ]/\', \'\', $val);
        return self::$val;
    }

    public static function cleanTextSomeSpecial($val) {
        self::$val = preg_replace(\'/[^A-Za-záíéóúñÁÍÉÓÚÑ!.?;\- ]/\', \'\', $val);
        return self::$val;
    }
    
    public static function cleanTextSomeSpecialNumbers($val) {
        self::$val = preg_replace(\'/[^_0-9A-Za-záíéóúñÁÍÉÓÚÑ!.?;\- \/]/\', \'\', $val);
        return self::$val;
    }

    public static function cleanTextForUrl($val) {
        self::$val = preg_replace(\'/[^_0-9A-Za-záíéóúñÁÍÉÓÚÑ!.?;:=\- \/]/\', \'\', $val);
        return self::$val;
    }
    
    public static function cleanLocation($val) {
	self::$val = preg_replace(\'/[^0-9A-Za-záíéóúñÁÍÉÓÚÑ#.,\- ]/\', \'\', $val);
	return self::$val;
    }
    
    public static function validateDate($date, $format = \'Y-m-d\') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    public static function validateTime($date, $format = \'H:i\') {
        //if date is being pulled from the database use this format
        if (strlen($date) > 5) {$format = \'H:i:s\';}

        //create time format
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
?>
';
$sanitize = fopen(__DIR__.'\model\commonFunctions\Sanitize.php', "w");
fwrite($sanitize, $sanitize_data);

$ajax_data = '
<?php
if(session_id() == \'\') {session_start();}
/*
Template Name: ajaxCalls
*/
?>
<?php include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 


header(\'Content-Type: application/json\');
$headers = apache_request_headers();
if (isset($headers[\'Csrftoken\'])) {
    if ($headers[\'Csrftoken\'] !== $_SESSION[\'token\']) { //ensure that the call came from a user that has a session token
        echo json_encode(array("message"=>"Wrong CSRF token"));
    } else {
        if(!empty($_SERVER[\'HTTP_X_REQUESTED_WITH\']) &&  strtolower($_SERVER[\'HTTP_X_REQUESTED_WITH\']) == \'xmlhttprequest\')  //ensure it is an ajax call 
        {
            
        }
    }
} else { 
    echo json_encode(array("message"=>"csrftoken not detected"));
}

?>
';
$ajax = fopen(__DIR__.'\ajax\AjaxCalls.php', "w");
fwrite($ajax, $ajax_data);

$default_page_data = '
<?php get_header();
	/*Template Name: default*/ ?>

			<?php the_content(); ?>
	<?php 
get_footer();
?>
';
$default_page = fopen(__DIR__.'\page.php', "w");
fwrite($default_page, $default_page_data);



?>
