<?php
/************RUN THE FILE************/
// I am running this file in Visual Studio Code. This will create or update 4 files in your project
//1. Add "AddSplashPageBaseFile.php to your root project folder
//2. Type the following in a new terminal: php .\AddSplashPageBaseFile.php

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
$splash_view = fopen($splash_view_dir ."\splash.php", "w");
fwrite($splash_view, $splash_view_data);

$splash_function_data = '
<?php 
//add splash code
function fetch_splash(){add_shortcode("display_splash", "fetch_splash_view");} 
add_action( "init", "fetch_splash");
function fetch_splash_view() {
    ob_start(); 
    include_once get_theme_file_path("view/splash.php"); 
    wp_enqueue_style("splash-css", get_template_directory_uri()."/css/splash.css", "", microtime());
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
$splash_css = fopen(__DIR__.'\css\splash.css', "w");
fwrite($splash_css, $splash_css_data);

$splash_header_data = '
<!--add this to in the body tag of your header.php if it is not already added-->
<?php echo do_shortcode("[display_splash]");?>
';
$splash_header = fopen(__DIR__.'\header.php', "a");
fwrite($splash_header, $splash_header_data);

?>
