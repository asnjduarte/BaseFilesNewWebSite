<?php

$video_full_screen_view_data = '
<div class="w100 h800 tc nblc video-row">
    <?php include get_theme_file_path(\'view/VideoView.php\');  ?>
</div>
';
$video_full_screen_view = fopen(__DIR__.'\view\VideoFullScreenView.php', "w");
fwrite($video_full_screen_view, $video_full_screen_view_data);

$video_index_data = '
<!-- add code wherever you like and change the video id if you have another video -->
<div>
    <?php $dynamicVideoId = 1;
        include_once get_theme_file_path(\'view/VideoFullScreenView.php\'); 
    ?>
</div>
';
$video_index = fopen(__DIR__.'\index.php', "a");
fwrite($video_index, $video_index_data);

?>
