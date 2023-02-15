<?php 

$header_func_data = '
<?php //two column header menu code
function fetch_tcm(){add_shortcode(\'display_menu\', \'fetch_two_column\');} 
add_action(\'init\', \'fetch_tcm\');
function fetch_two_column() {
    ob_start(); 
    include_once get_theme_file_path(\'view/twoColumnHeader.php\'); 
    wp_enqueue_style(\'twoColumnHeader-css\', get_template_directory_uri().\'/css/twoColumnHeader.css\', \'\', microtime());
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
                                if( array_intersect($allowed_roles, $user->roles ) ) { 
                                    if ($v->getRoleId() == 1) {?>
                                <li><a href="<?php echo $v->getLink()?>" aria-label="Va a la página de <?php echo $v->getText()?>"><?php echo $v->getText()?></a></li>
                                <?php }}; ?>
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

?>
