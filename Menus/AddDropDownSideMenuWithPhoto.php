<?php

$ddsmwp_view_data = '
<?php include get_theme_file_path(\'controller/HeaderMenuController.php\');  ?>
<div id="b-header" class="window mbMenu">
    <div class="r0-v2-menu h100">
        <div class="r0-v2-menu-1 p1 trans300 w60 h100 o1 r0__<?php echo $hmList[0]->getText()?>">
            <div class="p1 h100">
                <?php foreach($hmList as $k => $v) { 
                    $txt = "";
                    if(!is_user_logged_in()) {
                        if($v->getRoleId() != 2) continue;
                        $txt = str_replace(" ","_",strtolower($v->getText()));    
                        
                    } else {
                        $txt = str_replace(" ","_",strtolower($v->getText()));
                    }?>
                    <div class="r0-v2-menu-1-slides r0_<?php echo $txt; if ($k == 0) echo \' r0-show-width\';?> " id="r0_<?php echo $txt?>"><div class="bx100 cnnr <?php echo $txt?>"></div></div>
                <?php } ?>
                
            </div>
        </div>
        <div class="r0-v2-menu-2 trans300 w40 h100 r0__<?php echo $hmList[0]->getText()?>">
            <ul class="r0-v2-menu-2-box show-main-menu" data-menu-id="1">
                <?php  foreach($hmList as $k => $v) { 
                    if($v->getRoleId() != 2) {continue;}
                    $txt = str_replace(" ","_",strtolower($v->getText()));?>
                    
                    <li class="r0-v2-menu-item" id="<?php echo $txt?>"><a href="<?php echo $v->getLink()?>"><?php echo $v->getText(); ?></a></li>
                <?php } ?>
                <?php if ( is_user_logged_in() ) { 
                        $current_user = wp_get_current_user(); ?> 
                        <li class="r0-v2-menu-item sub-item skip" id="login"  onclick="" >
                            <a href="javascript:void(0)" data-menu-id="2">Más></a>
                        </li>
                    <?php } else { ?>
                        <li class="r0-v2-menu-item sub-item skip" id="login">
                            <a href="\login" data-menu-id="99">
                            <button class="main-menu-login-button lf1">Member Login</button>
                            </a></li>
                    <?php } ?>
                </li>
            </ul>
            <?php if ( is_user_logged_in() ) { ?>
            <ul class="r0-v2-menu-2-box hide-main-menu" data-menu-id="2">
                <li class="r0-v2-menu-item sub-item" id="inicio"><a href="javascript:void(0)" data-menu-id="1" onclick="">< Regresa</a></li>
                <?php foreach($hmList as $k => $v) { 
                    if ($v->getRoleId() != 1) continue;
                    $txt = str_replace(" ","_",strtolower($v->getText()));?>
                    <li class="r0-v2-menu-item" id="<?php echo $txt?>"><a href="<?php echo $v->getLink()?>"><?php echo $v->getText(); ?></a></li>
                <?php } ?>
                <li class="r0-v2-menu-item" id="logout"><a href="<?php echo wp_logout_url( home_url() ); ?>">Logout</a></li>
            </ul>
            <?php } ?>
        </div>
    </div>
    <div class="header abs">
        <div id="b-container" class="burger-container">
            <div id="burger">
                <div class="bbar topBar"></div>
                <div class="bbar btmBar"></div>
            </div>
        </div>
    </div>
</div>
';

$ddsmwp_css_general_data = '
.window, .r0-v2-menu, r0-v2-menu-1-slides {display:none;}
.header {top: 0px;right: 0px;}
.burger-container {	height:50px;width: 50px;padding-top: 25px;}
#burger {width: 100%;height: .7vw;padding-left: .5vw;padding-right: .5vw;}
#burger .bbar {	height: 1px;background: #7e110c;transition: all 0.4s cubic-bezier(0.4, 0.01, 0.165, 0.99);transition-delay: 0.2s;}
.btmBar {transform: translateY(6px) rotate(0deg);}
.menu-item {margin-top: 15px;opacity: 0;line-height: 1.1;}
.o1 { opacity:.1;}
.show-menu-bar {display: block;visibility: visible;animation: showMenuBar 300ms linear;position: fixed;right: 0px;top: 0px;z-index:1;}
@keyframes showMenuBar {
	0% {right: -4%;opacity: 0;visibility: hidden;}
	100% {right: 0px;opacity: 1;visibility: visible;}
}
.r0-show-width {animation: 300ms slideInFromLeft forwards;}
@keyframes slideInFromLeft {
    0% {opacity: 0;transform: translateX(-100%);}
    100% {opacity: 1;transform: translateX(0);}
  }
.r0-v2-menu-item {margin-top: 4px;opacity: 0;line-height: 1.25;}
.r0-v2-menu-item a {font-size: 4vw;}
.r0-v2-menu-2-1-ttl {line-height: 1.5;letter-spacing: 0.38rem;font-size: 1.2vw;}
.r0-v2-menu-2-1-txt {line-height: 1;letter-spacing: 0.1rem;font-size: .75vw;padding: 0px .4vw;}
.menu-opened .r0-v2-menu-2-box .r0-v2-menu-item {opacity: 1;}
.menu-opened .main-menu-login-button {font-size: 3.85vw;line-height: 1.25;}
.menu-opened {height: 100%;width: 100%;transition-delay: 0.25s;}
.menu-opened #burger {transform: rotate(-90deg);transition: all 0.4s cubic-bezier(0.4, 0.01, 0.165, 0.99);}
.menu-opened .burger-container #burger .topBar {transform: translateY(4px) rotate(45deg);transition-delay: .35s;}
.menu-opened .burger-container #burger .btmBar {transform: translateY(3px) rotate(-45deg);transition-delay: .35s;}
.menu-opened .r0-v2-menu {display: flex;}
.menu-opened .header {animation: open-h 1s forwards;}
@keyframes open-h {
    0% {height: 0px;animation-delay: 500ms;}
    100% {height: 75%;}
}
.menu-opened .hide-main-menu {opacity: 0; animation: anim-hide 1s forwards; display: none;}
@keyframes anim-hide {
	0% {transform: translateX(0%);}
	100% {transform: translateX(200%);}
}
.menu-opened .show-main-menu {opacity: 1;animation: anim-show 1s forwards;}
@keyframes anim-show {
	0% {transform: translateX(200%);}
	100% {transform: translateX(0%);}
}
@keyframes slideInFromRight {
    0% {opacity: 0;transform: translateX(100%);}
    100% {opacity: 1;transform: translateX(0);}
  }
  @keyframes slideInFromTop {
    0% {opacity: 0;transform: translateY(-10%);}
    100% {opacity: 1;transform: translateY(0);}
  }
  @keyframes slideInFromBot {
    0% {opacity: 0;transform: translateY(10%);}
    100% {opacity: 1;transform: translateY(0);}
  }
@media screen and (min-width: 1600px) {
    /*.window {height: 2vw;width: 2.5vw;}*/
}
@media screen and (max-width: 720px) {
    .mbMenu {display: block;visibility: visible;animation: showMenuBar 300ms linear;position: fixed;right: 0px;top: 0px;z-index:1;}
}
';
$ddsmwp_css_general = fopen(__DIR__.'\css\DropDownSideMenuWithPhoto.css', "w");
fwrite($ddsmwp_css_general, $ddsmwp_css_general_data);

$ddsmwp_view = fopen(__DIR__.'\view\DropDownSideMenuWithPhoto.php', "w");
fwrite($ddsmwp_view, $ddsmwp_view_data);

$hmList = array("inicio", "nuestra_fe", "acerca_de_nosotros", "ingresar", "ministerios");
$color_data = "";
$background_data = "";
foreach ($hmList as $k => $v) {
    $color_data = '#r0__' . $v . ', .r0__' . $v . " {background-color:white;}" . PHP_EOL . $color_data ;
    $background_data = '.' . $v . '{background-image:url(\'/wp-content/uploads/2023/02/' . $v . '.jpg\');}' . PHP_EOL . $background_data;
}
$drop_down_side_general_color_css = fopen(__DIR__.'\css\DropDownSideMenuWithPhoto.css', "a");
fwrite($drop_down_side_general_color_css, $color_data);
fwrite($drop_down_side_general_color_css, $background_data);

$ddsmwp_function_data = '
<?php 
/*add mobile drop down side menu with photo*/
function fetch_ddsm(){add_shortcode(\'display_mobile_menu\', \'fetch_drop_down_side_menu\');} 
add_action( \'init\', \'fetch_ddsm\');
function fetch_drop_down_side_menu() {
    ob_start(); 
    include_once get_theme_file_path(\'view/DropDownSideMenuWithPhoto.php\'); 
    wp_enqueue_style(\'dropDownSideMenu-css\', get_template_directory_uri().\'/css/DropDownSideMenuWithPhoto.css\', \'\', microtime());
    return ob_get_clean();
}
?>
';
$ddsmwp_function = fopen(__DIR__.'\functions.php', "a");
fwrite($ddsmwp_function, $ddsmwp_function_data);

$ddsmwp_js_data = '
/*Add new drop down side menu to the document ready section*/
var skipped = 0;
var clicked = 0;
var menu_item_id = 0;
if($(\'.skip\').on(\'click\', function() {
    skipped = 1;
})
);
if($(\'.sub-item\').on(\'click\', function() {
    clicked = 1;
})
);
if($(\'.r0-v2-menu-item\').on(\'click\', function() {
    menu_item_id = $(this).index();
}));
$(\'.r0-v2-menu-2-box\').click(function(){
  alert ("menu 2");
  alert(menu_item_id);
    //if (skipped == "0") {
        var header = document.getElementById(\'b-header\'),
            burger = document.getElementById(\'b-container\');
        
        var values_index = $(this).index();
        var selectedId = $(\'.sub-item a\', this).attr(\'data-menu-id\');
        alert("Current index: " + values_index);
        $(\'.r0-v2-menu-2-box\').each(function(index) {
            var typeId = $(this).attr(\'data-menu-id\');
            if (values_index == $(this).index()) {
                if((values_index == "0" && menu_item_id == "4") || (values_index == "1" && menu_item_id == "0")) {
                    $(this).removeClass(\'show-main-menu\');
                    $(this).addClass(\'hide-main-menu\');
                }
            } 
            if (selectedId == typeId) {
                if((values_index == "0" && menu_item_id == "4") || (values_index == "1" && menu_item_id == "0")) {
                    if ($(this).hasClass(\'hide-main-menu\')){ 
                        $(this).removeClass(\'hide-main-menu\');
                        $(this).addClass(\'show-main-menu\');
                        if(clicked != "1") {
                            header.classList.toggle(\'menu-opened\');
                        }
                    }
                }
            }
        })
    //}
});
/*display foto on hover*/
$(\'.r0-v2-menu-item\').mouseover(function() {
    myvar = this.id;
    $("div.r0-v2-menu-1-slides").hide();
    $("div.r0-v2-menu-1-slides").removeClass("r0-show-width");
    $("div.r0_"+myvar).addClass("r0-show-width");
    $("div.r0-v2-menu-1").removeClass (function (index, className) {
        return (className.match (/(^|\s)r0__\S+/g) || []).join(\' \');
    });
    $("div.r0-v2-menu-2").removeClass (function (index, className) {
        return (className.match (/(^|\s)r0__\S+/g) || []).join(\' \');
    });		
    $("div.r0-v2-menu-1").addClass("r0__"+myvar);
    $("div.r0-v2-menu-2").addClass("r0__"+myvar);
        $("#r0_"+myvar).show();
});

/*display navigation on scroll down*/
window.onscroll = function() {myFunction()};
var menu = document.getElementById("b-header");
var site_header = document.getElementById("site-header");
var sticky2 = menu.offsetTop;

function myFunction() {
if (window.pageYOffset> sticky2) {
    menu.classList.remove("hide-menu-bar");
    menu.classList.add("show-menu-bar");
} else {
    menu.classList.remove("show-menu-bar");
    menu.classList.add("hide-menu-bar");
}
}

(function(){
var header = document.getElementById(\'b-header\'),
    burger = document.getElementById(\'b-container\');

burger.onclick = function() {
    header.classList.toggle(\'menu-opened\');
}
}());
/*end drop down side menu with photo*/
';
$ddsmwp_js = fopen(__DIR__.'\js\FooterBundle.js', "a");
fwrite($ddsmwp_js, $ddsmwp_js_data);


?>
