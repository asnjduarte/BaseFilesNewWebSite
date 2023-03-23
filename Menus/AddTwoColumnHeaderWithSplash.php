<?php 

$header_css_data = '
/*two column header menu*/
@keyframes moveInFromTopOpac {
    0% {opacity:0; visibility: hidden;}
    100% {opacity:1; visibility: visible;}
}
.main-navigation a, .main-navigation a:hover, .main-navigation a:focus {color:#7e110c;}
.main-navigation a::after, .site-footer a::after {content: \'\';width: 0px;height: 2px;display: block;background: #7e110c;transition: 300ms;}
.main-navigation a:hover::after, .site-footer a:hover::after {width: 100%;}
.main-navigation ul ul, .slide .inFromRght{animation: slideOutFromLft 500ms forwards ; border-radius: 5px;}
@keyframes slideOutFromLft {
	0% {visibility: visible;opacity:1;	transform: translateX(15px); background-color:white;}
	100% {visibility: hidden;opacity:0;transform: translateX(0%);height: 0px;}
}
.main-navigation ul li:hover > ul, .slide:hover .inFromRght{animation: slideInFromRgt 500ms forwards ;}
@keyframes slideInFromRgt {
	0% {visibility: hidden;opacity:0;transform: translateX(0%);	height: 0px;}
	100% {opacity: 1;visibility: visible;transform: translateX(15px);background-color:white;}
}
@media screen and (max-width: 720px) {
  .mbDNo {display:none;}
}
/*two column header menu*/
';
$header_css = fopen(__DIR__.'\css\TwoColumnHeader.css', "w");
fwrite($header_css, $header_css_data);

$header_data = '

<?php include_once get_theme_file_path(\'controller/HeaderMenuController.php\');  ?>
<div class="w100 flx abs z3 mv-rgt0">
    <div id="nav-menu" class="w50 mbDNo">
        <nav class="main-navigation lf1 ">
            <ul class="flx jcc">
                <?php foreach ($hmList as $k => $v) {
                    if ($v->getRoleId() == 2){
                        if ((0==$k%2)) {?>
                        <li><a href="<?php echo $v->getLink();?>" aria-label="Va a la página de <?php echo $v->getText()?>"><?php echo $v->getText()?></a></li>
                    <?}}}?>
                <?php if ( is_user_logged_in() ) { 
                    $current_user = wp_get_current_user(); ?> 
                    <li><a href="<?php echo site_url(\'/?page_id=#\')?>"><?php echo esc_html( $current_user->user_login ) ?></a>
                    <ul>
                        <?php $user = wp_get_current_user();$allowed_roles = array(\'editor\', \'administrator\');
                            foreach ($hmList as $k=> $v) {
                                if( array_intersect($allowed_roles, $user->roles ) ) { 
                                    if ($v->getRoleId() == 1) {?>
                                <li><a href="<?php echo $v->getLink()?>" aria-label="Va a la página de <?php echo $v->getText()?>"><?php echo $v->getText()?></a></li>
                                <?php }}}; ?>
                        <li><a href="<?php echo wp_logout_url(); ?>" aria-label="logout">Logout</a></li>
                    </ul>
                    </li>
                <?php } else { ?>
                    <li><a href="<?php echo site_url(\'/loginregister\') ?>">Inicio</a></li>
                <?php } ?>
            </ul>
        </nav>
    </div>
    <div class="w50">
    <nav class="main-navigation lf1 txtColor mbDNo">
        <ul class="flx jcc">
        <?php foreach ($hmList as $k => $v) {
                    if ($v->getRoleId() == 2){
                        if (($k%2==1)) {?>
                        <li><a href="<?php echo $v->getLink();?>" aria-label="Va a la página de <?php echo $v->getText()?>"><?php echo $v->getText()?></a></li>
                    <?}}}?>    
        </ul>
        </nav>
    </div>
</div>
';
$header = fopen(__DIR__.'\view\TwoColumnHeader.php', "w");
fwrite($header, $header_data);

$header_head_data = '
<div id="gBox1" class="pLoad">
<?php echo do_shortcode(\'[display_menu]\' ); ?>
<?php echo do_shortcode(\'[display_mobile_menu]\');?>
</div>
';
$header_head = fopen(__DIR__.'\header.php', "a");
fwrite($header_head, $header_head_data);

?>

<?php 

$header_func_data = '
<?php //two column header menu code
function fetch_tcm(){add_shortcode(\'display_menu\', \'fetch_two_column\');} 
add_action(\'init\', \'fetch_tcm\');
function fetch_two_column() {
    ob_start(); 
    include_once get_theme_file_path(\'view/TwoColumnHeader.php\'); 
    wp_enqueue_style(\'twoColumnHeader-css\', get_template_directory_uri().\'/css/TwoColumnHeader.css\', \'\', microtime());
    return ob_get_clean();
}
?>
';
$header_func = fopen(__DIR__.'\functions.php', "a");
fwrite($header_func, $header_func_data);


$header_css_data = '
/*two column header menu*/
@keyframes moveInFromTopOpac {
    0% {opacity:0; visibility: hidden;}
    100% {opacity:1; visibility: visible;}
}
.main-navigation a, .main-navigation a:hover, .main-navigation a:focus {color:#7e110c;}
.main-navigation a::after, .site-footer a::after {content: \'\';width: 0px;height: 2px;display: block;background: #7e110c;transition: 300ms;}
.main-navigation a:hover::after, .site-footer a:hover::after {width: 100%;}
.main-navigation ul ul, .slide .inFromRght{animation: slideOutFromLft 500ms forwards ; border-radius: 5px;}
@keyframes slideOutFromLft {
	0% {visibility: visible;opacity:1;	transform: translateX(15px); background-color:white;}
	100% {visibility: hidden;opacity:0;transform: translateX(0%);height: 0px;}
}
.main-navigation ul li:hover > ul, .slide:hover .inFromRght{animation: slideInFromRgt 500ms forwards ;}
@keyframes slideInFromRgt {
	0% {visibility: hidden;opacity:0;transform: translateX(0%);	height: 0px;}
	100% {opacity: 1;visibility: visible;transform: translateX(15px);background-color:white;}
}
@media screen and (max-width: 720px) {
  .mbDNo {display:none;}
}
/*two column header menu*/
';
$header_css = fopen(__DIR__.'\css\TwoColumnHeader.css', "w");
fwrite($header_css, $header_css_data);

$splash_js_data = '

/*add this to the document ready section of footerbundle.js*/
  $(\'#hSplash\').on(\'animationend webkitAnimationEnd\', function() {
      $("#gBox1").removeClass("dNoP");
      $("#gBox1").addClass("pLoad");
      $("#gBox").removeClass("dNoP");
      $("#gBox").addClass("pLoad");
      $("#hSplash").addClass("dNo");
  });
';
$splash_js = fopen(__DIR__.'\js\footerBundle.js', "a");
fwrite($splash_js, $splash_js_data);

$splash_view_data = '
<div id="hSplash" class="pageLoad">
    <div class="bgSplash">
        <div class="logo"></div>
    </div>
    <div class="slidr">
        <div class="slidr__layer"></div>
        <div class="slidr__layer"></div>
    </div>
</div>';
$splash_view_dir = __DIR__.'\view';
if (!file_exists($splash_view_dir)) {
  mkdir($splash_view_dir, 0777, true);
}
$splash_view = fopen($splash_view_dir ."\Splash.php", "w");
fwrite($splash_view, $splash_view_data);

$splash_function_data = '
<?php 
//add splash code
function fetch_splash(){add_shortcode("display_splash", "fetch_splash_view");} 
add_action( "init", "fetch_splash");
function fetch_splash_view() {
    ob_start(); 
    include_once get_theme_file_path("view/Splash.php"); 
    wp_enqueue_style("splash-css", get_template_directory_uri()."/css/Splash.css", "", microtime());
    return ob_get_clean();
}
?>
';
$splash_function = fopen(__DIR__.'\functions.php', "a");
fwrite($splash_function, $splash_function_data);

$splash_css_data = '
/*add splash code*/
.bgSplash {position: fixed;z-index: 500;opacity: 1;visibility: visible;pointer-events: none;transition-delay: .5s;background: #dd6b13;transition: visibility opacity .5s;
    top: 0;bottom: 0;left: 0;right: 0;}
  .pageLoad .logo {opacity: 1; visibility: visible;}
  .logo {z-index:501; display: block;width: 100px;height: 100px;position: absolute;top: 50%;left: 50%;transform: translate(-50%,-50%);background-size: contain!important;
    background: url("http://world-evangelism.local/wp-content/uploads/2022/01/23004494_1664620600263697_802229586128158535_o-1.jpg") no-repeat 50%;
  }
  .pageLoad .slidr {top: 50%;left: 50%;}
  .slidr {width: 100vh;height: 100vw;position: fixed;z-index: 1000;pointer-events: none;transform: translate3d(-50%,-50%,0) rotate(90deg) translate3d(0,100%,0);}
  .pageLoad .slidr__layer {animation: slide-1 1.5s cubic-bezier(.7,0,.3,1) forwards;}
  .slidr__layer {position: absolute;width: 100%;height: 100%;top: 0;left: 0;background: #feb802;}
  .pageLoad .slidr__layer:nth-child(2) {animation-name: slide-2;}
  @keyframes slide-1{
    0%{transform:translateZ(0)}
    30%,70%{transform:translate3d(0,-100%,0);animation-timing-function:cubic-bezier(.7,0,.3,1)}
    to{transform:translate3d(0,-200%,0)}}
  @keyframes slide-2{
    0%,14.5%{transform:translateZ(0)}
    37.5%,62.5%{transform:translate3d(0,-100%,0);animation-timing-function:cubic-bezier(.7,0,.3,1)}
    85.5%,to{transform:translate3d(0,-200%,0)}}
    .slidr__layer+.slidr__layer {
      background: #7e110c;
  }
';
$splash_css = fopen(__DIR__.'\css\Splash.css', "w");
fwrite($splash_css, $splash_css_data);

$splash_header_data = '
<!--add this to in the body tag of your header.php if it is not already added-->
<?php echo do_shortcode("[display_splash]");?>
';
$splash_header = fopen(__DIR__.'\header.php', "a");
fwrite($splash_header, $splash_header_data);

$splash_index_data = '
<!--add this after the header in the index.php does not need a closing div-->
<div id="gBox" class="dNoP lf1">
';
$splash_index = fopen(__DIR__.'\index.php', "a");
fwrite($splash_index, $splash_index_data);



?>
