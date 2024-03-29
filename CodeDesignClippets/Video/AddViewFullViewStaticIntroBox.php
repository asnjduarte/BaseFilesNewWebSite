<?php


$video_full_view_data = '
<div class="w100 ha tc mv-rgt0">
    <div id="gBox2" class="w100 h400 t10 jcfe abs flx dNoP tt sos tran2 iphha">
        <div class="bgGrOpac w60 h100 p10 iphw100 mbhdNo">
            <h2>Company Overview</h2>
            <div>TerraBase, Incorporated is a privately-held software development and data management services firm, established and founded in Baton Rouge, Louisiana in 1990. Our expertise in  analytical chemistry and associated environmental sciences provide our clients with valuable guidance and assistance with data quality objectives, data validation, data mining, data management and the latest generation of software tools for our industry. TerraBase Inc. does not own or work for laboratories, nor does it attempt to take the place of traditional consulting firms. We do not provide consulting services for data interpretation. We provide high quality, cost-effective, data managment services and software to industry, consultants, state and federal government agencies.
                TerraBase Inc.’s web-enabled and traditional software solutions are the ultimate tools for the next generation of Environmental Data Managers. Check out our solutions.
            </div>
        </div>
    </div>
    <?php include get_theme_file_path('view/VideoView.php');  ?>
</div>
';
$video_full_view = fopen(__DIR__.'\view\ViewFullScreenWithStaticIntroBoxView.php', "w");
fwrite($video_full_view, $video_full_view_data);

$video_index_data = '
<!--add to index.php -->
<div>
<?php $dynamicVideoId = 1;
    include_once get_theme_file_path(\'view/VideoFullScreenWithIntroBoxView.php\'); 
?>
</div>
';
$video_index = fopen(__DIR__.'\index.php', "a");
fwrite($video_index, $video_index_data);


$video_js_data = '
<!--add to js.php -->
$(\'.pLoad .mv-rgt0\').on(\'animationend webkitAnimationEnd\', function() {
  setTimeout(function(){
    $(".tran2").removeClass("dNoP");
    $(".tran2").addClass("mv-rgt0");
}, 1500);
});
';
$video_js = fopen(__DIR__.'\FooterBundle.js', "a");
fwrite($video_js, $video_js_data);



?>
