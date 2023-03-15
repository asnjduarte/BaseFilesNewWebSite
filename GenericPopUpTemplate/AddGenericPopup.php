<?php

$small_view_data = '
<div id="<?php echo $v->getBoxId() ?>" class="z200 fxd jqShortenFrmRght dNo  <?php if($v->getIsHide() == "1") echo \'dNo_\'; echo $v->getClass();?> ">
    <div class="bx100 flx fxd jcse aic z200 tc">
        <div class="gBoxShadow w95 h80 p20 rel dis flx aic jcse lp1 iph_ha mbh_w80 mbl_w95 ">
            <a href="#" parent_id="<?php echo $v->getBoxId() ?>" class="gvpBtnCancel wh2 abs ptr r1v"><div class="clsBtn tran300 bx100 cnnr"></div></a>
            <div class="w30 iph_ha">
                <h3 class="lf2"><?php echo $v->getH2()?></h3>
            </div>
            <div class="w80"><?php echo $v->getTxt()?></div>
            <div class="flx w30 jcc mt2vw">
                <a class="w45" target="_blank" href="<?php echo $v->getUrlBtn(); ?>"><button class="gBoxShadow gBxGr w100 p20 lp1 f1_5"><?php echo $v->getBtn1() ?></button></a>
            </div>
        </div>
    </div>
</div>
';
$small_view = fopen(__DIR__.'\view\SmallGenericPopUpView.php', "w");
fwrite($small_view, $small_view_data);

$big_view_data = '
<div id="<?php echo $v->getBoxId() ?>" class="z200 fxd jqShortenFrmRght dNo  <?php if($v->getIsHide() == "1") echo \'dNo_\'; echo $v->getClass();?> ">
    <div class="bx100 spinnerBg"></div>
    <div class="w100 h90 flx fxd jcse aic z200 tc">
        <div class="gBoxShadow h80 p20 mbl_w95 rel lp1 iph_ha mbh_w80 dis">
            <a href="#" parent_id="<?php echo $v->getBoxId() ?>" class="gvpBtnCancel wh2 abs ptr r1v"><div class="clsBtn tran300 bx100 cnnr"></div></a>
            <div class="h80 iph_ha">
                <h1 class="f3_5 mtb1"><?php echo $v->getH1()?></h1>
                <h2 class="lf2 mtb1"><?php echo $v->getH2()?></h2>
                <div class="f1_5 p20"><?php echo $v->getTxt() ?></div>
                <img alt="logo" class="lw20 mbh_w20v" src="<?php echo $v->getImg() ?>">
            </div>
            <div class="flx w100 jcc mt2vw">
                <a class="w45" target="_blank" href="<?php echo $v->getUrlBtn(); ?>"><button class="gBoxShadow gBxGr w100 p20 lp1 f1_5"><?php echo $v->getBtn1() ?></button></a>
            </div>
        </div>
    </div>
</div>
';
$big_view = fopen(__DIR__.'\view\BigGenericPopUpView.php', "w");
fwrite($big_view, $big_view_data);

$generic_view_data = '
<?php foreach ($popups as $k => $v) {
    if ($v->getClass() == "bgGenericPopUp") {
        include get_theme_file_path(\'view/BigGenericPopUpView.php\'); 
    } else {
        include get_theme_file_path(\'view/SmallGenericPopUpView.php\'); 
    }
} ?>
';
$generic_view = fopen(__DIR__.'\view\GenericPopUpView.php', "w");
fwrite($generic_view, $generic_view_data);

$generic_model_data = '
<?php 
class GenericPopUp{

    private $popupId;
    private $boxId;
    private $btn1;
    private $btn2;
    private $txt;
    private $img;
    private $urlBtn;
    private $h2;
    private $h1;
    private $class;
    private $startDayTime; 
    private $endDayTime;
    private $waitTime;
    private $isHide;
    private $isInfinite;
    private $factory;
    private $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }
    
    public function getPopUpId(){return $this->popupId;}
    public function setPopUpId($popupId){$this->popupId = $popupId;}
    public function getBoxId(){return $this->boxId;}
    public function setBoxId($boxId){$this->boxId = $boxId;}
    public function getBtn1(){return $this->btn1;}
    public function setBtn1($btn1){$this->btn1 = $btn1;}
    public function getBtn2(){return $this->btn2;}
    public function setBtn2($btn2){$this->btn2 = $btn2;}
    public function getTxt(){return $this->txt;}
    public function setTxt($txt){$this->txt = $txt;}
    public function getImg(){return $this->img;}
    public function setImg($img){$this->img = $img;}
    public function getUrlBtn(){return $this->urlBtn;}
    public function setUrlBtn($urlBtn){$this->urlBtn = $urlBtn;}
    public function getH2(){return $this->h2;}
    public function setH2($h2){$this->h2 = $h2;}
    public function getH1(){return $this->h1;}
    public function setH1($h1){$this->h1 = $h1;}
    public function getClass(){return $this->class;}
    public function setClass($class){$this->class = $class;}
    public function getStartDayTime(){return $this->startDayTime;}
    public function setStartDayTime($startDayTime){$this->startDayTime = $startDayTime;}
    public function getEndDayTime(){return $this->endDayTime;}
    public function setEndDayTime($endDayTime){$this->endDayTime = $endDayTime;}
    public function getWaitTime(){return $this->waitTime;}
    public function setWaitTime($waitTime){$this->waitTime = $waitTime;}
    public function getIsHide(){return $this->isHide;}
    public function setIsHide($isHide){$this->isHide = $isHide;}
    public function getIsInfinite(){return $this->isInfinite;}
    public function setIsInfinite($isInfinite){$this->isInfinite = $isInfinite;}

    public function fetchSpecificGenericPopupFromDb(){ 
        try {
            /*fetch a specific popup*/
            $query = "CALL sp_fetch_specific_generic_popup(\'%s\');"; //fetch data from db
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getBoxId()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("GenericPopUp.php", $this->wpdb->last_error, "sp_fetch_generic_popup", $this->wpdb);
            }
        } catch (Exception $e) {
            $this->err->addError("GenericPopUp.php", $e, "fetchGenericPopupFromDb", $this->wpdb);
        }

        return $result;
    }

    public function fetchAllActvGenericPopupFromDb(){ 
        try {
            /*fetch all active popups*/
            $query = "CALL sp_fetch_generic_popup(\'%d\');"; //fetch data from db
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, 1), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("GenericPopUp.php", $this->wpdb->last_error, "sp_fetch_generic_popup", $this->wpdb);
            }
        } catch (Exception $e) {
            $this->err->addError("GenericPopUp.php", $e, "fetchGenericPopupFromDb", $this->wpdb);
        }

        return $result;
    }

    public function setGenericPopUp($result){
        $aList = array();
        try {
            foreach ($result as $k => $v) {
                $gpu = new GenericPopUp();
                $gpu->setBtn1($v["btn1"]);
                $gpu->setBtn2($v["btn2"]);
                $gpu->setTxt($v["txt"]);
                $gpu->setImg($v["img"]);
                $gpu->setUrlBtn($v["urlBtn"]);
                $gpu->setH2($v["h2"]);
                $gpu->setH1($v["h1"]);
                $gpu->setClass($v["class"]);
                $gpu->setStartDayTime($v["startDayTime"]);
                $gpu->setEndDayTime($v["endDayTime"]);
                $gpu->setWaitTime($v["waitTime"]);
                $gpu->setIsHide($v["isHide"]);
                $gpu->setIsInfinite($v["isInfinite"]);
                array_push($aList, $gpu);
            }
        } catch (Exception $e) {
            $this->err->addError("GenericPopUp.php", $e, "setGenericPopUp", $this->wpdb);
        }

        return $aList;
    }

    public function getGenericPopup($val) {
        if ($val == "all")
             $result = $this->fetchAllActvGenericPopupFromDb();
        else 
            $result = $this->fetchSpecificGenericPopupFromDb();
        
        
        return $this->setGenericPopUp($result);
    }
}
?>
';
$generic_model = fopen(__DIR__.'\model\company\GenericPopUp.php', "w");
fwrite($generic_model, $generic_model_data);

$generic_controller_data = '
<?php 
    include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
    $factory = new Factory();
    $popup = $factory->createGenericPopUp();
    $popups = $popup->getGenericPopup("all");
?>
';
$generic_controller = fopen(__DIR__.'\controller\GenericPopUpController.php', "w");
fwrite($generic_controller, $generic_controller_data);

$generic_factory_data = '
/*Add section to includes in the Factory class*/
include_once get_theme_file_path("model/company/GenericPopUp.php"); 

/*Add section to the Factory class*/
public static function createGenericPopUp(){
    return new GenericPopUp();
}
';
$generic_factory = fopen(__DIR__.'\model\commonFunctions\Factory.php', "a");
fwrite($generic_factory, $generic_factory_data);

$generic_js_data = '
/*start generic popup*/
setTimeout(function(){
    $(\'.bgGenericPopUp\').removeClass("dNo");
    $(\'.bgGenericPopUp\').addClass("jqWidenFrmLft");
}, 5000);
setTimeout(function(){
    $(\'.smGenericPopUp\').removeClass("dNo");
    $(\'.smGenericPopUp\').addClass("jqWidenFrmLft");
}, 2000);
/*end generic popup*/
';
$generic_js = fopen(__DIR__.'\js\footerBundle.js', "a");
fwrite($generic_js, $generic_js_data);

?>
