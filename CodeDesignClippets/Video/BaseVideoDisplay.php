<?php


$video_controller_data = '
<?php 
    include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
    $factory = new Factory();
    $v = $factory->createVideo();
    $v->setVideoId($dynamicVideoId);
    $videoInfo = $v->getVideo();

?>
';
$video_controller = fopen(__DIR__.'\controller\VideoController.php', "w");
fwrite($video_controller, $video_controller_data);

$video_model_data = '
<?php
class Video {
    private $videoId;
    private $image;
    private $videoLink;
    private $videoLnkTxt;
    private $factory;
    private $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    public function getVideoId() {return $this->videoId;}
    public function getImage() {return $this->image;}
    public function getVideoLink() {return $this->videoLink;}
    public function getVideoLnkTxt() {return $this->videoLnkTxt;}
    public function setVideoId($videoId) {
        $videoId = (filter_var($videoId, FILTER_VALIDATE_INT));
        if($videoId < 0 || $videoId > 10000 || empty($videoId)) $videoId = 1;
        $this->videoId=$videoId;
    }
    public function setImage($image) {
        $image = sanitize_text_field($image);
        $this->image= Sanitize::cleanTextSomeSpecialNumbers($image);
    }
    public function setVideoLink($videoLink) {
        $videoLink = sanitize_text_field($videoLink);
        $this->videoLink= Sanitize::cleanTextSomeSpecialNumbers($videoLink);
    }
    public function setVideoLnkTxt($videoLnkTxt) {
        $videoLnkTxt = sanitize_text_field($videoLnkTxt);
        $this->videoLnkTxt=Sanitize::cleanTextForUrl($videoLnkTxt);
    }

    //fetch specific video from DB
    private function fetchVideoFromDb(){
        try {
            $query = "CALL sp_fetch_video(\'%d\');"; 
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getVideoId()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Video.php", $this->wpdb->last_error, "sp_fetch_video", $this->wpdb);
            }
        } catch (Exception $e) {
            $this->err->addError("Video.php", $e, "fetchVideoFromDb", $this->wpdb);
        }
    }

    //set video objects and return an array of objects (if needed)
    public function setVideo($result){
        $aList = array();
        try {
            foreach ($result as $k => $v) {
                $vid = new Video();
                $vid->setImage($v["image"]);
                $vid->setVideoLink($v["videoLink"]);
                $vid->setVideoLnkTxt($v["videoLnkTxt"]);
                array_push($aList, $vid);
            }
        } catch (Exception $e) {
            $this->err->addError("Video.php", $e, "setVideo", $this->wpdb);
        }
        return $aList;
    }

    //insert video object
    public function insertVideo($result){
        try {
            $query = "CALL sp_insert_video(\'%s\', \'%s\',\'%s\');"; 
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $result[0]->getImage(), $result[0]->getVideoLink(), $result[0]->getVideoLnkTxt()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Video.php", $this->wpdb->last_error, "sp_insert_video", $this->wpdb);
            }
        } catch (Exception $e) {
            $this->err->addError("Video.php", $e, "insertVideo", $this->wpdb);
        }
    }

    public function getVideo(){
        $result = $this->fetchVideoFromDb();
        return $this->setVideo($result);
    }
}
?>
';
$video_model = fopen(__DIR__.'\model\company\Video.php', "w");
fwrite($video_model, $video_model_data);

$video_view_data = '
<?php include_once get_theme_file_path(\'controller/VideoController.php\');  ?>
<video playsinline="" preload="metadata" controls="controls" autoplay="autoplay" muted="true" class="w100 h100 iph_h40 mbv_ha" width="100" height="100" 
        poster="<?php echo $videoInfo[0]->getImage(); ?>">
        <source type="video/mp4" src="<?php echo $videoInfo[0]->getVideoLink(); ?>">
        <a href="#"></a> 
</video>
';
$video_view = fopen(__DIR__.'\view\VideoView.php', "w");
fwrite($video_view, $video_view_data);

$video_factory_data = '
/*add to top section of Factory class*/
include_once get_theme_file_path("model/company/Video.php"); 

/*add to the Factory class */
public static function createVideo(){
    return new Video();
}
';
$video_factory = fopen(__DIR__.'\model\commonFunctions\Factory.php', "a");
fwrite($video_factory, $video_factory_data);


?>
