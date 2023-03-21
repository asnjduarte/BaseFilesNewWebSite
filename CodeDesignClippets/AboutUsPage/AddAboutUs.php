<?php

$about_us_view_data = '
<?php include get_theme_file_path(\'model/aboutUsModel.php\'); ?>
<div class="w100 ha rel flx mv-rgt0 mbFlxdc">
    <div id="auvImg" class="bx100 cnnr <?php echo $aui[0]["img"];?> abs bpr"></div>
    <div class="w50 ha rel z1 flx aic fwrap acc bgtwhr lgt-rgt sos tt jcc mbhw100">
        <div class="abs bx100 z-1"></div>
        <?php foreach($aui as $aui1k => $aui1v) { ?>
            <div class="auv gBoxShadow p1 w80 m10 trans300"><?php echo $aui1v["title"];?></div>
        <?php } ?>
    </div>
    <div id="auvBx" class="w50 z1 flx jcc aic flxdc rel lft-rgt tt sos p10 mbhw100">
        <div class="abs bx100 z-1 bgwh o9"></div>
        <h3 id="auvTtl" class="mv-lft"><?php echo $aui[0]["title"];?></h3>
        <div id="auvDsc" class="mv-lft"><?php echo $aui[0]["description"];?></div>
    </div>
</div>
';
$about_us_view = fopen(__DIR__.'\view\aboutUsView.php', "w");
fwrite($about_us_view, $about_us_view_data);

$about_us_model_data = '
<?php 
session_start();
global $wpdb;
	$query = "CALL sp_fetch_about_us_info();";
    $aui = $wpdb->get_results($wpdb->prepare($query), ARRAY_A);
?>
';
$about_us_model = fopen(__DIR__.'\model\aboutUsModel.php', "w");
fwrite($about_us_model, $about_us_model_data);


$about_us_js_data = '
$(document).ready(function(){
	$(".auv").on("click", function(e){
        $.ajax({
            url: \'?page_id=29\',
            type: \'post\',
            dataType: \'json\',
            headers: {\'CsrfToken\': $(\'meta[name="csrf-token"]\').attr(\'content\')},
            data: {
                fetch_about_us:"true", 
                auvId:$(this).index()
            },
            success:function(response){
                $("#auvImg").removeClass(function (index,className) {
                    return (className.match (/(^|\s)auv\S+/g) || []).join(' ');
                });
                $("#auvImg").addClass(response[0]["img"]);
                $("#auvTtl").html(response[0]["title"]);
                $("#auvDsc").html(response[0]["description"]);
                $("#auvTtl").toggleClass("mv-rgt");
                $("#auvDsc").toggleClass("mv-rgt");
                $("#auvTtl").toggleClass("mv-lft");
                $("#auvDsc").toggleClass("mv-lft");
            }
        });
    });
});

';
$about_us_js = fopen(__DIR__.'\js\aboutUs.js', "w");
fwrite($about_us_js, $about_us_js_data);

$about_us_css_data = '
.bgtr {background: linear-gradient(0deg, rgba(113,2,2,0.6591678907891281) 0%, rgba(255,255,255,1) 100%);}
.auv1 {background-image:url(\'http://terrabase.com/wp-content/themes/TerraBase/images/xmain_logo.jpg.pagespeed.ic.qPtjLkdzQH.jpg\');}
.auv2 {background-image:url(\'/wp-content/uploads/2023/03/Untitled-design-1.jpg\');}
.auv3 {background-image:url(\'/wp-content/uploads/2023/03/Untitled-design-2.jpg\');}
.bpr {background-position:right;}
.o9 {opacity: .9;}
.site-header__menu a, .site-header__menu a:hover, a:focus {color: white;}
@keyframes slideInFromRgt {
	0% {visibility: hidden;opacity:0;transform: translateX(0%);	height: 0px;}
	100% {opacity: 1;visibility: visible;transform: translateX(15px);}
}
.m10 {margin:10px;}
.acc {align-content: center;}
.bgtwhr.is-visible {animation: changeWhR 2s forwards; }
@keyframes changeWhR {
	0% {background-color:white; color:#01631b;}
	100% {background-color:#01631b; color:white;}
}
.bgwh {background-color: white;}
.jcfe {justify-content: flex-end;}
.h400 {height:400px;}
.bgGrOpac{background: linear-gradient(0deg, rgba(1,99,27,0.4992121848739496) 0%, rgba(1,99,27,0.5048144257703081) 100%); color:white;}
.t10 {top:10%;}
.z-1 {z-index: -1;}
.bgtwhr.is-visible {animation: changeWhR 1s forwards; }
@keyframes changeWhR {
	0% {background-color:white; color:#01631b;}
	100% {background-color:#01631b; color:white;}
}
';
$about_us_css = fopen(__DIR__.'\css\aboutUs.css', "w");
fwrite($about_us_css, $about_us_css_data);



$about_us_function_data = '
<?php 
/*add mobile drop down side menu with photo*/
function fetch_auv(){add_shortcode(\'display_about_us\', \'fetch_about_us_view\');} 
add_action( \'init\', \'fetch_auv\');
function fetch_about_us_view() {
    ob_start(); 
    include_once get_theme_file_path(\'view/aboutUsView.php\'); 
    wp_enqueue_style(\'aboutUs-css\', get_template_directory_uri().\'/css/aboutUs.css\', \'\', microtime());
    wp_enqueue_script(\'aboutUs-js\', get_template_directory_uri().\'/js/aboutUs.js\', NULL, microtime(), true);
    return ob_get_clean();
}
?>
';
$about_us_function = fopen(__DIR__.'\functions.php', "a");
fwrite($about_us_function, $about_us_function_data);

?>
