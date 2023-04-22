<?php
$event_dir = __DIR__.'\view\event';
if (!file_exists($event_dir)) {
    mkdir($event_dir, 0777, true);
}

$events_ajax_data = '
/*Start EVENTS - add code to AjaxCalls*/
//On country change in Events view, change states and cities
if(isset( $_POST["eventCountryChange"] )) {
    $factory = new Factory();

    //get state info
    $location = $factory->createLocation();
    $location->setCountryId($_POST["eventCountrySelected"]);
    $states = $location->setStateInfo();    

    //create state objects as state array
    $sArr = array();
    foreach ($states as $k => $v) {array_push($sArr, array("stateId"=>$v->getStateId(), "stateName"=>$v->getStateName(), "stateAbbr"=>$v->getStateAbbr()));}

    //get city info
    $location->setStateId($states[0]->getStateId());
    $cities = $location->setCityInfo();

    //create city objects as city array
    $cArr = array();
    foreach ($cities as $k => $v) {array_push($cArr, array("cityId"=>$v->getCityId(), "cityName"=>$v->getCityName()));}
    echo json_encode (array($sArr, $cArr)); 
}

//On state change in Events view, change cities
if(isset( $_POST["eventStateChange"] )) {
    $factory = new Factory();

    //get city
    $location = $factory->createLocation();
    $location->setStateId($_POST["eventStateSelected"]);
    $cities = $location->setCityInfo();    

    //create city objects as city array
    $cArr = array();
    foreach ($cities as $k => $v) {array_push($cArr, array("cityId"=>$v->getCityId(), "cityName"=>$v->getCityName()));}

    echo json_encode (array($cArr)); 
}

//Insert event
if(isset($_POST[\'eventAddition\'])) {
    $factory = new Factory();
    $event = $factory->createEvent();
    $err = $factory->createErr();

    try {
        //create double array to set event data
        $data = array(array("eventId"=>"","cityId"=>$_POST["cityId"], "typeId"=>$_POST["locationType"],"zoneId"=>$_POST["timeZone"],"categoryId"=>$_POST["categoryId"],"evntDate"=>substr($_POST["dateAndTime"],0,10),"evntTime"=>substr($_POST["dateAndTime"],11,5),"location"=>$_POST["location"],"title"=>$_POST["title"],"description"=>$_POST["description"],"link"=>$_POST["link"],"video"=>$_POST["video"],"foto"=>"","linkTxt"=>$_POST["buttonText"],"isActive"=>1, "isRecurring"=>$_POST["isEventRecurring"]));
        
        //get objects of events that have been set
        $result = $event->setEvent($data);

        //insert event and return last inserted row
        $lastInsert = $result[0]->insertEvent();
    } catch (Exception $e) {
        $err->addError("AjaxCalls.php", $e, " eventAddition", $wpdb);
    }
    
    echo json_encode($lastInsert);
} 

//Update event
if(isset($_POST[\'eventUpdate\'])) {
    $factory = new Factory();
    $err = $factory->createErr();
    $event = $factory->createEvent();
    try{
        //set double array to set event data
        $data = array(array("eventId"=>$_POST["eventId"],"typeId"=>$_POST["locationType"],"cityId"=>$_POST["cityId"],"zoneId"=>$_POST["timeZone"],"categoryId"=>$_POST["categoryId"],"evntDate"=>$_POST["date"],"evntTime"=>$_POST["time"],"location"=>$_POST["location"],"title"=>$_POST["title"],"description"=>$_POST["description"],"link"=>$_POST["link"],"video"=>$_POST["video"],"foto"=>"","linkTxt"=>$_POST["buttonText"],"isActive"=>$_POST["isActive"], "isRecurring"=>$_POST["isEventRecurring"]));
        
        //get objects of events that have been set
        $result = $event->setEvent($data);
        
        //update event and return 1 or 0 if updated
        $lastUpdate = $result[0]->updateEvent();
        echo json_encode($lastUpdate);
    } catch (Exception $e) {
        $err->addError("AjaxCalls.php", $e, " eventUpdate", $wpdb);
    }
    
}
/*end EVENTS*/
';
$event_ajax = fopen(__DIR__.'\ajax\AjaxCalls.php', "a");
fwrite($event_ajax, $events_ajax_data);

$event_controller_data = '
<?php
include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
$factory = new Factory();
$event = $factory->createEvent();

//get objects of location type ids 
//1 - Presencial; 2 - Virtual; 3 - both
$locType = $factory->createLocationType();
$locationTypes = $locType->setLocationType();

//get objects of locatin (country, state, and cities)
$location = $factory->createLocation();
$countries = $location->setCountryInfo();

//set country id and fetch the state info for the country
$location->setCountryId($countries[0]->getCountryId());
$states = $location->setStateInfo();

//set state id and fetch the cities info for the state
$location->setStateId($states[0]->getStateId());
$cities = $location->setCityInfo();

//get object of time zones
//1 - PDT, 2 - PST
$timeZone = $factory->createTimeZone();
$timeZones = $timeZone->setTimeZone();

//get objects of categories
//1- discipleship, 2 - evangelism, 3 - business
$category = $factory->createCategory();
$categories = $category->setCategory();
?>
';
$event_controller = fopen(__DIR__.'\controller\EventController.php', "w");
fwrite($event_controller, $event_controller_data);

$event_UD_controller_data = '
<?php
include get_theme_file_path(\'controller/EventController.php\');

//get array of all active events
$result = $event->fetchEvents();

//create objects of all active events
$events = $event->setEvent($result);
?>
';
$event_UD_controller = fopen(__DIR__.'\controller\EventUDController.php', "w");
fwrite($event_UD_controller, $event_UD_controller_data);

$event_dir = __DIR__.'\model\event';
if (!file_exists($event_dir)) {
    mkdir($event_dir, 0777, true);
}

$location_dir = __DIR__.'\model\location';
if (!file_exists($location_dir)) {
    mkdir($location_dir, 0777, true);
}

$category_class_data = '
<?php
class Category {
    private $categoryId;
    private $catName;
    private $factory;
    private $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    public function getCategoryId(){return $this->categoryId;}
    public function setCategoryId($categoryId){$this->categoryId = $categoryId;}
    public function getCatName(){return $this->catName;}
    public function setCatName($catName){$this->catName = $catName;}

    //fetch all categories from db
    public function fetchCategory(){
        try { 
            $query = "CALL 	sp_fetch_category();";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Category.php", $this->wpdb->last_error, "	sp_fetch_category", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("Category.php", $e, "fetchCategory", $this->wpdb);
        } 
        return $result;
        
    }

    //set array of category objects
    public function setCategory(){
        $result = $this->fetchCategory();

        $arr = array();
        if($result) {
            try {
                foreach ($result as $k => $v) {
                    $itm = new Category;
                    $itm->setCategoryId($v["categoryId"]);
                    $itm->setCatName($v["catName"]);
                    array_push($arr, $itm);
                }   
            } catch (Exception $e) {
                $this->err->addError("Category.php", $e, "setCategory", $this->wpdb);
            }
        } else {
            $this->err->addError("Category.php", "fetchCategory result was empty", "setCategory", $this->wpdb);
        }
        return $arr;
    }
}
?>
';
$category_class = fopen(__DIR__.'\model\event\Category.php', "w");
fwrite($category_class, $category_class_data);

$event_class_data = '
<?php
class Event {
    private $eventId;
    private $typeId;
    private $countryId;
    private $stateId;
    private $cityId;
    private $evntDate;
    private $evntTime;
    private $zoneId;
    private $location;
    private $title;
    private $description;
    private $isActive;
    private $categoryId;
    private $link;
    private $video;
    private $foto;
    private $linkTxt;
    private $isEventRecurring;
    private $factory;
    private $err;

    
    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    public function getEventId(){return $this->eventId;}
    public function setEventId($eventId){
        $eventId = filter_var($eventId, FILTER_SANITIZE_NUMBER_INT);
        if ($eventId < 0 || $eventId > 1000000)    
            $eventId = 1;
        $this->eventId = $eventId;
    }
    public function getTypeId(){return $this->typeId;}
    public function setTypeId($typeId){
        $typeId = filter_var($typeId, FILTER_SANITIZE_NUMBER_INT);
        if ($typeId < 0 || $typeId > 30)    
            $typeId = 1;
        $this->typeId = $typeId;
    }

    public function getCountryId(){return $this->countryId;}
    public function setCountryId($countryId){
        $countryId = filter_var($countryId, FILTER_SANITIZE_NUMBER_INT);
        if ($countryId < 0 || $countryId > 500)    
            $countryId = 1;
        $this->countryId = $countryId;}

    public function getStateId(){return $this->stateId;}
    public function setStateId($stateId){
        $stateId = filter_var($stateId, FILTER_SANITIZE_NUMBER_INT);
        if ($stateId < 0 || $stateId > 10000)    
            $stateId = 1;
        $this->stateId = $stateId;}

    public function getCityId(){return $this->cityId;}
    public function setCityId($cityId){
        $cityId = filter_var($cityId, FILTER_SANITIZE_NUMBER_INT);
        if ($cityId < 0 || $cityId > 100000)    
            $cityId = 1;
        $this->cityId = $cityId;}
    public function getEvntDate(){return $this->evntDate;}
    public function setEvntDate($evntDate){
        if (Sanitize::validateDate($evntDate))
            $this->evntDate = $evntDate;
        else {
            $this->err->addError("Event.php", "Event Date is not a valid date: " . $evntDate, "setEvntDate", $this->wpdb);
            throw new Exception(\'Event Date is not a valid date\');
        } 
            
    }
    public function getEvntTime(){return $this->evntTime;}
    public function setEvntTime($evntTime){

        if (Sanitize::validateTime($evntTime)) {
            $this->evntTime = $evntTime;
        }
        else {
            $this->err->addError("Event.php", "Event Time is not a valid time: " . $evntTime, "setEvntTime", $this->wpdb);
            throw new Exception(\'Event Time is not a valid time\');
        }
    }
    public function getZoneId(){return $this->zoneId;}
    public function setZoneId($zoneId){
        $zoneId = filter_var($zoneId, FILTER_SANITIZE_NUMBER_INT);
        if ($zoneId < 0 || $zoneId > 50)    
            $zoneId = 1;
        $this->zoneId = $zoneId;
    }
    public function getLocation(){return $this->location;}
    public function setLocation($location){
        $location = sanitize_text_field($location);
        $location = Sanitize::cleanLocation($location);
        $this->location = $location;
    }
    public function getTitle(){return $this->title;}
    public function setTitle($title){
        $title = sanitize_text_field($title);
        $title = Sanitize::cleanTextNoNumberNoSpecial($title); 
        $this->title = $title;
    }
    public function getDescription(){return $this->description;}
    public function setDescription($description){
        $description = sanitize_text_field($description);
        $description = Sanitize::cleanTextSomeSpecialNumbers($description); 
        $this->description = $description;
        }
    public function getIsActive(){return $this->isActive;}
    public function setIsActive($isActive){
        $isActive = (filter_var($isActive, FILTER_SANITIZE_NUMBER_INT));
        $this->isActive = $isActive;
    }
    public function getCategoryId(){return $this->categoryId;}
    public function setCategoryId($categoryId){
        $categoryId = filter_var($categoryId, FILTER_SANITIZE_NUMBER_INT);
        if ($categoryId < 0 || $categoryId > 200)  
            $categoryId = 1;
        $this->categoryId = $categoryId;
    }
    public function getLink(){return $this->link;}
    public function setLink($link){
        $link = sanitize_text_field($link);
        $link = Sanitize::cleanTextForUrl($link);
        $this->link = $link;
    }
    public function getVideo(){return $this->video;}
    public function setVideo($video){
        $video = sanitize_text_field($video);
        $video = Sanitize::cleanTextForUrl($video);
        $this->video = $video;
    }
    public function getFoto(){return $this->foto;}
    public function setFoto($foto){
        $foto = sanitize_text_field($foto);
        $foto = Sanitize::cleanTextForUrl($foto);
        $this->foto = $foto;
    }
    public function getLinkTxt(){return $this->linkTxt;}
    public function setLinkTxt($linkTxt){
        $linkTxt = sanitize_text_field($linkTxt);
        $linkTxt = Sanitize::cleanTextSomeSpecial($linkTxt);
        $this->linkTxt = $linkTxt;
    }
    public function getIsEventRecurring(){return $this->isEventRecurring;}
    public function setIsEventRecurring($isEventRecurring){
        $isEventRecurring = (filter_var($isEventRecurring, FILTER_SANITIZE_NUMBER_INT));
        $this->isEventRecurring = $isEventRecurring;
    }

    //Fetch and return an array of all active events 
    public function fetchEvents(){
        try { 
            $query = "CALL 	sp_fetch_events();";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Event.php", $this->wpdb->last_error, "sp_fetch_events", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("Event.php", $e, "fetchEvents", $this->wpdb);
        } 
        return $result;
    }

    //Fetch specific active event
    public function fetchSpecificEvent() {
        try { 
            $query = "CALL 	sp_fetch_specific_event();";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getEventId()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Event.php", $this->wpdb->last_error, "sp_fetch_specific_event", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("Event.php", $e, "fetchSpecificEvent", $this->wpdb);
        } 
        return $result;
    }

    //Set and return array of Event objects 
    public function setEvent($result){
        $arr = array();
        if($result) {
            try {
                foreach ($result as $k => $v) {
                    $itm = new Event;
                    $itm->setEventId($v["eventId"]);
                    $itm->setTypeId($v["typeId"]);
                    $itm->setCityId($v["cityId"]);
                    $itm->setEvntDate($v["evntDate"]);
                    $itm->setEvntTime($v["evntTime"]);
                    $itm->setZoneId($v["zoneId"]);
                    $itm->setLocation($v["location"]);
                    $itm->setTitle($v["title"]);
                    $itm->setDescription($v["description"]);
                    $itm->setIsActive($v["isActive"]);
                    $itm->setCategoryId($v["categoryId"]);
                    $itm->setLink($v["link"]);
                    $itm->setVideo($v["video"]);
                    $itm->setFoto($v["foto"]);
                    $itm->setLinkTxt($v["linkTxt"]);
                    $itm->setIsEventRecurring($v["isRecurring"]);
                    array_push($arr, $itm);
                }   
            } catch (Exception $e) {
                $this->err->addError("Event.php", $e, "setEvent", $this->wpdb);
            }
        } else {
            $this->err->addError("Event.php", "setEvent result was empty", " setEvent", $this->wpdb);
        }
        return $arr;
    }

    //Insert event and return last inserted object
    public function insertEvent() {
        try {
            $query = "CALL 	sp_insert_event(\'%d\', \'%d\', \'%s\', \'%s\', \'%d\', \'%s\', \'%s\', \'%s\', \'%s\', \'%d\', \'%d\', \'%s\', \'%s\', \'%s\', \'%s\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getTypeId(), $this->getCityId(), $this->getEvntDate(), $this->getEvntTime(), $this->getZoneId(), $this->getLocation(), $this->getTitle(), $this->getDescription(), $this->getIsActive(), $this->getCategoryId(), $this->getLink(), $this->getVideo(), $this->getFoto(), $this->getLinkTxt(), $this->getIsEventRecurring()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Event.php", $this->wpdb->last_error, "sp_insert_event", $this->wpdb);   
            }
            return $result;
        } catch (Exception $e) {
            $this->err->addError("Event.php", $e, "insertEvent", $this->wpdb);   
        }
    }

    //Update event and return 1 or 0 based on last update
    public function updateEvent(){
        try {
            $query = "CALL 	sp_update_event(\'%d\',\'%d\', \'%d\', \'%s\', \'%s\', \'%d\', \'%s\', \'%s\', \'%s\', \'%d\', \'%d\', \'%s\', \'%s\', \'%s\', \'%s\', \'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getEventId(), $this->getTypeId(), $this->getCityId(), $this->getEvntDate(), $this->getEvntTime(), $this->getZoneId(), $this->getLocation(), $this->getTitle(), $this->getDescription(), $this->getIsActive(), $this->getCategoryId(), $this->getLink(), $this->getVideo(), " ", $this->getLinkTxt(), $this->getIsEventRecurring()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Event.php", $this->wpdb->last_error, "sp_update_event", $this->wpdb);   
            }
            return $result;
        } catch (Exception $e) {
            $this->err->addError("Event.php", $e, "updateEvent", $this->wpdb);   
        }
    }
}
?>
';
$event_class = fopen(__DIR__.'\model\event\Event.php', "w");
fwrite($event_class, $event_class_data);

$location_type_data = '
<?php

class LocationType {
    private $typeId;
    private $description;
    private $err;
    private $factory;

    public function getTypeId(){return $this->typeId;}
    public function setTypeId($typeId){$this->typeId = $typeId;}
    
    public function getDescription(){return $this->description;}
    public function setDescription($description){$this->description = $description;}

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }
    public function fetchLocationType(){
        try { 
            $query = "CALL sp_fetch_location_type();";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Policy.php", $this->wpdb->last_error, "sp_fetch_policy", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("LocationType.php", $e, "getLocationType", $this->wpdb);
        } 
        return $result;
    }

    public function setLocationType(){
        $result = $this->fetchLocationType();

        $arr = array();
        if($result) {
            try {
                foreach ($result as $k => $v) {
                    $itm = new LocationType;
                    $itm->setTypeId($v["typeId"]);
                    $itm->setDescription($v["description"]);
                    array_push($arr, $itm);
                }   
            } catch (Exception $e) {
                $this->err->addError("LocationType.php", $e, "setLocationType", $this->wpdb);
            }
        } else {
            $this->err->addError("LocationType.php", "fetchLocationType result was empty", "setLocationType", $this->wpdb);
        }

        return $arr;
    }

}
?>
';
$location_type = fopen(__DIR__.'\model\location\LocationType.php', "w");
fwrite($location_type, $location_type_data);

$time_zone_data = '
<?php

class TimeZone {
    private $zoneId;
    private $zoneName;
    private $zoneAbbr;
    private $zoneUTC;
    private $factory;
    private $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    public function getZoneId(){return $this->zoneId;}
    public function setZoneId($zoneId){$this->zoneId = $zoneId;}
    public function getZoneName(){return $this->zoneName;}
    public function setZoneName($zoneName){$this->zoneName = $zoneName;}
    public function getZoneAbbr(){return $this->zoneAbbr;}
    public function setZoneAbbr($zoneAbbr){$this->zoneAbbr = $zoneAbbr;}
    public function getZoneUTC(){return $this->zoneUTC;}
    public function setZoneUTC($zoneUTC){$this->zoneUTC = $zoneUTC;}

    public function fetchTimeZone(){
        try { 
            $query = "CALL sp_fetch_time_zone();";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("TimeZone.php", $this->wpdb->last_error, "sp_fetch_time_zone", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("TimeZone.php", $e, "fetchTimeZone", $this->wpdb);
        } 
        return $result;
        
    }

    public function setTimeZone(){
        $result = $this->fetchTimeZone();

        $arr = array();
        if($result) {
            try {
                foreach ($result as $k => $v) {
                    $itm = new TimeZone;
                    $itm->setZoneId($v["zoneId"]);
                    $itm->setZoneName($v["name"]);
                    $itm->setZoneAbbr($v["abbreviation"]);
                    $itm->setZoneUTC($v["zoneUTC"]);
                    array_push($arr, $itm);
                }   
            } catch (Exception $e) {
                $this->err->addError("TimeZone.php", $e, "setTimeZone", $this->wpdb);
            }
        } else {
            $this->err->addError("TimeZone.php", "fetchTimeZone result was empty", "setTimeZone", $this->wpdb);
        }

        return $arr;
    }
}
?>
';
$time_zone = fopen(__DIR__.'\model\location\TimeZone.php', "w");
fwrite($time_zone, $time_zone_data);

$add_event_view_data = '

<?php include get_theme_file_path(\'controller/EventController.php\'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <form name="aevForm1" id="aevForm1"  method="post" action="">
                <div class="w90 tc flx fwrap jcse" >
                    <div class="w45 p10">
                        <select id="aevCountryId" class="lft-rgt tt sos w100 gBoxShadow tc" >
                            <?php foreach ($countries as $k => $v) { ?>
                                <option value="<?php echo $v->getCountryId() ?>"><?php echo  $v->getCountryName() ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="w45 p10">
                        <select id="aevStateId" class="lft-rgt tt sos w100 gBoxShadow tc" >
                            <?php foreach ($states as $k => $v) { ?>
                                <option value="<?php echo $v->getStateId() ?>"><?php echo  $v->getStateName() ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="w45 p10">
                        <select id="aevCityId" class="lft-rgt tt sos w100 gBoxShadow tc locCitySel" >
                            <?php foreach ($cities as $k => $v) { ?>
                                <option value="<?php echo $v->getCityId() ?>"><?php echo  $v->getCityName() ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="w45 p10">
                        <select id="aevLocTypeSel" class="lft-rgt tt sos w100 gBoxShadow tc" >
                            <?php foreach ($locationTypes as $k => $v) { ?>
                                <option value="<?php echo $v->getTypeId() ?>"><?php echo  $v->getDescription() ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="w45 p10">
                        <select id="aevTimeZone" class="lft-rgt tt sos w100 gBoxShadow tc" >
                            <?php foreach ($timeZones as $k => $v) { ?>
                                <option value="<?php echo $v->getZoneId() ?>"><?php echo  $v->getZoneAbbr() ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="w45 p10">
                        <select id="aevCategories" class="lft-rgt tt sos w100 gBoxShadow tc" >
                            <?php foreach ($categories as $k => $v) { ?>
                                <option value="<?php echo $v->getCategoryId() ?>"><?php echo  $v->getCatName() ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="w45 p10">
                        <label>Is Event Recurring: </label>
                        <select name="aevIER" id="aevIER" class=" w100 gBoxShadow">
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    </div>
                    <div class="w45 p10">
                        <label>Start Date and Time: </label>
                        <input id="aevDateTime" type="datetime-local" class="gBoxShadow w100"/>
                        <div id="aevDateTime_err" class=""></div>
                    </div>
                    <div class="w45 p10">
                        <label>Title: </label>
                        <input id="aevTitle" type="text" class="gBoxShadow acceptedChar" size="50" value="" name="aevTitle"/>
                        <div id="aevTitle_err" class=""></div>
                    </div>
                    <div class="w45 p10">
                        <label>Description: </label>
                        <input id="aevDescription" type="text" class="gBoxShadow acceptedChar" size="50" value="" name="aevDescription"/>
                        <div id="aevDescription_err" class=""></div>
                    </div>
                    <div class="w45 p10">
                        <label>Link:</label>
                        <input id="aevLink" type="text" class="gBoxShadow acceptedUrl" size="50" value="" name="aevLink"/>
                        <div id="aevLink_err" class=""></div>
                    </div>
                    <div class="w45 p10">
                        <label>Video: </label>
                        <input id="aevVideo" type="text" class="gBoxShadow acceptedUrl" size="50" value="" name="aevVideo"/>
                        <div id="aevVideo_err" class=""></div>
                    </div>
                    <!--<div class="w45 p10">
                        <label>Foto: </label>
                        <input id="evFoto" type="text" class="gBoxShadow acceptedChar" size="50" value="" name="evFoto"/>
                    </div>-->
                    <div class="w45 p10">
                        <label>Button Text: </label>
                        <input id="aevButton" type="text" class="gBoxShadow acceptedChar" size="35" value="" name="aevButton"/>
                        <div id="aevButton_err" class=""></div>
                    </div>
                    <div class="w45 p10">
                        <label>Location: </label>
                        <input id="aevLocation" type="text" class="gBoxShadow acceptedChar" size="50" value="" name="aevLocation"/>
                        <div id="aevLocation_err" class=""></div>
                    </div>
                </div>
                <div class="w90 flx jcc">
                    <input id="aevAddEvent" type="submit" value="Add Event" name="aevAddEvent" class="gBoxShadow p20 w45">
                    <div id="aevSubmitMessage" class=""></div>
                </div>
        </form>

        </div>
    </div>
    </div>


';
$add_event_view = fopen(__DIR__.'\view\event\AddEvent.php', "w");
fwrite($add_event_view, $add_event_view_data);

$event_view_data = '
<div class="container">
    <div class="row pt6">
        <div class="col-0 col-md-1">
            <table class="table">
                <h2>Actions</h2>
                <tr class="d-flex flex-md-column">
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="evInsert" checked>
                            <label class="form-check-label" for="evInsert">Insert</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="evUpdate">
                            <label class="form-check-label" for="evUpdate">Update</label>
                        </div>
                    </td>
                </tr>

            </table>
        </div>
        <div id="evForm" class="col-12 col-md-11 h800 ofya rel">
            <div id="evAddEvent" class="slideLeftOuter slideLeftInner"><?php include get_theme_file_path(\'view/event/AddEvent.php\'); ?></div>            
            <div id="evUpdateEvent" class="abs slideLeftOuter"><?php include get_theme_file_path(\'view/event/UpdateEvent.php\'); ?></div>
        </div>  
    </div>
</div>
';
$event_view = fopen(__DIR__.'\view\event\EventView.php', "w");
fwrite($event_view, $event_view_data);

$update_event_data = '
<?php include get_theme_file_path(\'controller/EventUDController.php\'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="accordion" id="accordionExample">
                <?php for($i = 0; $i < count($events); $i++) { ?>
                <div class="accordion-item" id="uevForm<?php echo $events[$i]->getEventId()?>">
                    <h2 class="accordion-header">
                        <button class="accordion-button table-responsive <?php if($i != 0) echo "collapsed";?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $events[$i]->getEventId()?>" aria-expanded="<?php if($i == 0) echo "true"; else echo "false"?>" aria-controls="collapse<?php echo $events[$i]->getEventId()?>">
                            <table class="table table-striped">
                                <tbody>
                                <tr  class="d-flex flex-column flex-md-row flex-wrap">
                                    <td class="col-0 col-md-3">
                                        <h4>Event Id:</h4>
                                        <div id="uevEID" class="uevEID"><?php echo $events[$i]->getEventId();?></div>
                                    </td>
                                    <td class="col-0 col-md-3">
                                        <h4>Event Title:</h4>
                                        <div class="uevETitle" ><?php echo $events[$i]->getTitle();?></div>
                                    </td>
                                    <td class="col-0 col-md-2">
                                        <h4>Event Date:</h4>
                                        <div class="uevEDate"><?php echo $events[$i]->getEvntDate();?></div>
                                    </td>
                                    <td class="col-0 col-md-2">
                                        <h4>Event Time:</h4>
                                        <div class="uevETime"><?php echo $events[$i]->getEvntTime();?></div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </button>
                    </h2>
                    <div id="collapse<?php echo $events[$i]->getEventId()?>" class="accordion-collapse collapse <?php if($i == 0) echo "show";?>" data-bs-parent="#accordionExample">
                        <div class="accordion-body table-responsive">
                            <form id="ueSubmitForm">
                            <table class="table">
                                <tbody>
                                    <tr class="d-flex flex-column flex-md-row flex-wrap">
                                        <td class="col-0 col-md-4">
                                            <div>
                                            <h4>Country Id:</h4>
                                            <select id="uevCountryId" class="form-select locCountrySel" aria-label="country id dropdown" eId = "<?php echo $events[$i]->getEventId()?>">
                                                <?php foreach ($countries as $k => $v) { ?>
                                                    <option value="<?php echo $v->getCountryId() ?>" <?php if ($events[$i]->getCountryId() == $v->getCountryId()) echo "checked" ?>><?php echo  $v->getCountryName() ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>State Id:</h4>
                                            <select id="uevStateId" class="form-select locStateSel" aria-label="state id dropdown" eId = "<?php echo $events[$i]->getEventId()?>">
                                                <?php foreach ($states as $k => $v) { ?>
                                                    <option value="<?php echo $v->getStateId() ?>" <?php if ($events[$i]->getStateId() == $v->getStateId()) echo "checked" ?>><?php echo  $v->getStateName() ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>City Id:</h4>
                                            <select id="uevCityId" class="form-select locCitySel" aria-label="city id dropdown ">
                                                <?php foreach ($cities as $k => $v) { ?>
                                                    <option value="<?php echo $v->getCityId() ?>" <?php if ($events[$i]->getCityId() == $v->getCityId()) echo "checked" ?>><?php echo  $v->getCityName() ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>Location Type Id:</h4>
                                            <select id="uevLocTypeSel" class="form-select" aria-label="location type id dropdown">
                                                <?php foreach ($locationTypes as $k => $v) { ?>
                                                    <option value="<?php echo $v->getTypeId() ?>" <?php if ($events[$i]->getTypeId() == $v->getTypeId()) echo "checked" ?>><?php echo  $v->getDescription() ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>Time Zone Id:</h4>
                                            <select id="uevTimeZone" class="form-select" aria-label="time zone id dropdown">
                                                <?php foreach ($timeZones as $k => $v) { ?>
                                                    <option value="<?php echo $v->getZoneId() ?>" <?php if ($events[$i]->getZoneId() == $v->getZoneId()) echo "checked" ?>><?php echo  $v->getZoneAbbr() ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>Category Id:</h4>
                                            <select id="uevCategories" class="form-select" aria-label="category id dropdown">
                                                <?php foreach ($categories as $k => $v) { ?>
                                                    <option value="<?php echo $v->getCategoryId() ?>"><?php echo  $v->getCatName() ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>Start Date:</h4>
                                            <input id="uevESDate" value="<?php echo $events[$i]->getEvntDate()?>" type="text" class="form-control acceptedNumbers" placeholder="Event Start Date" aria-label="eventStartDate" aria-describedby="event start date" size="10">
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>Start Time:</h4>
                                            <input id="uevESTime" value="<?php echo $events[$i]->getEvntTime()?>" type="text" class="form-control acceptedNumbers" placeholder="Event Start Time" aria-label="eventStartTime" aria-describedby="event start time" size="5">
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>Location:</h4>
                                            <input id="uevLocation" value="<?php echo $events[$i]->getLocation()?>" type="text" class="form-control acceptedChar" placeholder="Event Location" aria-label="eventLocation" aria-describedby="event location" size="150">
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>Title:</h4>
                                            <input id="uevTitle" value="<?php echo $events[$i]->getTitle()?>" type="text" class="form-control acceptedChar" placeholder="Title" aria-label="Title" aria-describedby="title" size="50">
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>Button</h4>
                                            <input id="uevButton" value="<?php echo $events[$i]->getLinkTxt()?>" type="text" class="form-control acceptedChar" placeholder="LinkTxt" aria-label="LinkTxt" aria-describedby="linkTxt" size="25">
                                        </td>
                                        <td class="col-0 col-md-4">
                                            <h4>Link</h4>
                                            <input id="uevLink" value="<?php echo $events[$i]->getLink()?>" type="text" class="form-control acceptedUrl" placeholder="Link" aria-label="Link" aria-describedby="link" size="255">
                                            <div id="uevLink_err" class=""></div>
                                        </td>
                                        <table class="table">
                                            <tbody>
                                                <tr class="d-flex flex-column flex-md-row flex-wrap">
                                                    <td class="col-0 col-md-4">
                                                        <h4>Video</h4>
                                                        <input id="uevVideo" value="<?php echo $events[$i]->getVideo()?>" type="text" class="form-control acceptedUrl" placeholder="Video" aria-label="Video" aria-describedby="video">
                                                        <div id="uevVideo_err" class=""></div>
                                                    </td>
                                                    <td class="col-0 col-md-2">
                                                        <div class="form-check">
                                                            <h4>Is Active</h4>
                                                            <input class="form-check-input text-center" type="checkbox" value="" id="uevIsActive" <?php if($events[$i]->getIsActive()) echo "checked"?>>
                                                        </div>
                                                    </td>
                                                    <td class="col-0 col-md-2">
                                                        <div class="form-check">
                                                            <h4>Is Recurring</h4>
                                                            <input class="form-check-input text-center" type="checkbox" value="" id="uevIER" <?php if($events[$i]->getIsEventRecurring()) echo "checked"?>>
                                                        </div>
                                                    </td>
                                                    <td class="col-0 col-md-4">
                                                        <h4>Description:</h4>
                                                        <textarea id="uevDescription" class="form-control acceptedChar" aria-label="description"><?php echo $events[$i]->getDescription()?></textarea>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </tr>
                                    <tr class="d-flex justify-content-center">
                                        <td>
                                            <input id="ueUpdateEvent" type="submit" value="Update Event" name="ueUpdateEvent" class="btn btn-primary updateEvent" eId="<?php echo $events[$i]->getEventId()?>">
                                            <div id="uevSubmitMessage" class=""></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
</div>
';
$update_event = fopen(__DIR__.'\view\event\UpdateEvent.php', "w");
fwrite($update_event, $update_event_data);

$functions_data = '
<?php /*start add and update events */

$page_slug = \'add-events\'; // Slug of the Post
$new_page = array(
    \'post_type\'     => \'page\', 				// Post Type Slug eg:
    \'post_title\'    => \'Add Events\',	// Title 
    \'post_content\'  => \'[display_add_events]\',	// Content
    \'post_status\'   => \'publish\',			// Post Status
    \'post_author\'   => 1,					// Post Author ID
    \'post_name\'     => $page_slug			// Slug of the Post
);

if (!get_page_by_path( $page_slug, OBJECT, \'page\')) { // Check If Page Not Exits
    $new_page_id = wp_insert_post($new_page);
}

function fetch_event_view(){add_shortcode(\'display_add_events\', \'get_event_view\');} 
add_action(\'init\', \'fetch_event_view\');
function get_event_view() {
    ob_start(); 
    include_once get_theme_file_path(\'view/event/EventView.php\');
    wp_enqueue_style(\'event-css\', get_template_directory_uri().\'/css/Event.css\', \'\', microtime()); 
    wp_enqueue_script(\'event-js\', get_template_directory_uri().\'/js/Event.js\', NULL, microtime(), true);
    return ob_get_clean();
}
/*end add and update events */
?>
';
$functions = fopen(__DIR__.'\functions.php', "a");
fwrite($functions, $functions_data);

$Event_css_data = '
.pt6vw {padding-top:6vw;}
.w45 {width:45%;}
';
$Event_css = fopen(__DIR__.'\css\Event.css', "w");
fwrite($Event_css, $Event_css_data);

$Event_js_data = '
function changeCountry(prefix, id) {
    $.ajax({
        type:\'POST\',
        url:\'?page_id=6\',
        headers: {\'CsrfToken\': $(\'meta[name="csrf-token"]\').attr(\'content\')},
        data: {
            eventCountryChange:true,
            eventCountrySelected: $("#" + prefix + "Form" + id + " #" + prefix + \'CountryId option:selected\').val(),
        },
        success: function(result){
            $("#" + prefix + "Form" + id + " #" + prefix + "StateId").empty();
            var  appendValues = "";
            for( var i = 0; i<result[0].length; i++){
                appendValues += "<option value=" + result[0][i].stateId +">"+ result[0][i].stateName + "</option>";
            }
            $("#" + prefix + "Form" + id + " #" + prefix + "StateId").append(appendValues);
            
            appendValues = "";
            for( var i = 0; i<result[1].length; i++){
                appendValues += "<option value=" + result[1][i].cityId +">"+ result[1][i].cityName + "</option>";
            }
            $("#" + prefix + "Form" + id + " #" + prefix + "CityId").empty();
            $("#" + prefix + "Form" + id + " #" + prefix + "CityId").append(appendValues);
        }
    });
}

function changeState(prefix, id) {
    $.ajax({
        type:\'POST\',
        url:\'?page_id=6\',
        headers: {\'CsrfToken\': $(\'meta[name="csrf-token"]\').attr(\'content\')},
        data: {
            eventStateChange:true,
            eventStateSelected: $("#" + prefix + "Form" + id + " #" + prefix + \'StateId option:selected\').val(),
        },
        success: function(result){
            var  appendValues = "";
            for( var i = 0; i<result[0].length; i++){
                appendValues += "<option value=" + result[0][i].cityId +">"+ result[0][i].cityName + "</option>";
            }
            $("#" + prefix + "Form" + id + " #" + prefix + "CityId").empty();
            $("#" + prefix + "Form" + id + " #" + prefix + "CityId").append(appendValues);
        }
    });
}

$(document).ready(function(){

    //when user changes country box on update event view update states and cities
    $(\'.locCountrySel\').on(\'change\', function(e) {
        $id = $(this).attr(\'eId\');
        changeCountry("uev", $id);
    })

    //when user changes state box on update event view update cities
    $(\'.locStateSel\').on(\'change\', function(e) {
        $id = $(this).attr(\'eId\');
        changeState("uev", $id);
    })

    //when user changes country box on insert event view update states and cities
    $(\'#aevCountryId\').on(\'change\',function(e) {
        changeCountry("aev", 1);
	});

    //when user changes state box on insert event view update cities
	$(\'#aevStateId\').on(\'change\',function(e) {
		changeState("aev", 1);
	});

    //when button update is clicked on EventView, show the update event view
    $("#evUpdate").on(\'click\', function(e){
        $("#evAddEvent").removeClass("slideLeftInner");
        $("#evUpdateEvent").addClass("slideLeftInner");
        $("#evUpdateEvent").removeClass("dNo");
        $("#evInsert").prop("checked", false);
    })

    //when button insert is clicked on EventView, show the insert event view
    $("#evInsert").on(\'click\', function(e){
        $("#evAddEvent").addClass("slideLeftInner");
        $("#evUpdateEvent").removeClass("slideLeftInner");
        $("#evUpdate").prop("checked", false);
    })
    
    // add event
    $("#aevAddEvent").on(\'click\', function(e){
        e.preventDefault();
        let noErrors = true;

        if(checkContactFields()) {
            if(checkValidUrls("aev")){
                $.ajax({
                    type:\'POST\',
                    url:\'?page_id=6\',
                    dataType: \'json\',
                    headers: {\'CsrfToken\': $(\'meta[name="csrf-token"]\').attr(\'content\')},
                    data: {
                        eventAddition: "true",
                        cityId: ($("#aevLocCitySel option:selected").val()),
                        locationType: ($("#aevLocTypeSel option:selected").val()),
                        timeZone: ($("#aevTimeZone option:selected").val()),
                        categoryId: ($("#aevCategories option:selected").val()),
                        dateAndTime: ($("#aevDateTime").val()),
                        location: $("#aevLocation").val(),
                        title: $("#aevTitle").val(),
                        description: $("#aevDescription").val(),
                        link: $("#aevLink").val(),
                        video: $("#aevVideo").val(),
                        foto: $("#aevFoto").val(),
                        buttonText: $("#aevButton").val(),
                        isEventRecurring: ($("#aevIER option:selected").val()),
                    },
                    success: function(result){
                        $("#aevSubmitMessage").text("Event has been added");
                        $("#aevDateTime").val(\'\');
                        $("#aevTitle").val(\'\');
                        $("#aevDescription").val(\'\');
                        $("#aevLink").val(\'\');
                        $("#aevVideo").val(\'\');
                        $("#aevButton").val(\'\');
                        $("#aevLocation").val(\'\');
                    }
                }).done(function(response) {
                });
            }
        }
    });

    //update event
    $(".updateEvent").on("click", function(e){
        e.preventDefault();
        $id = $(this).attr(\'eId\');
        alert ("item clicked");
        if(checkValidUrls("uev")){
            $.ajax({
                type:\'POST\',
                url:\'?page_id=6\',
                dataType: \'json\',
                headers: {\'CsrfToken\': $(\'meta[name="csrf-token"]\').attr(\'content\')},
                data: {
                    eventUpdate: "true",
                    eventId:$("#uevForm" + $id + " #uevEID").text(),  
                    cityId: $("#uevForm" + $id + " #uevCityId option:selected").val(), 
                    locationType: $("#uevForm" + $id + " #uevLocTypeSel option:selected").val(),
                    timeZone: $("#uevForm" + $id + " #uevTimeZone option:selected").val(),
                    categoryId: $("#uevForm" + $id + " #uevCategories option:selected").val(),
                    date: $("#uevForm" + $id + " #uevESDate").val(),
                    time: $("#uevForm" + $id + " #uevESTime").val(),
                    location: $("#uevForm" + $id + " #uevLocation").val(),
                    title: $("#uevForm" + $id + " #uevTitle").val(),
                    description: $("#uevForm" + $id + " #uevDescription").val(),
                    link: $("#uevForm" + $id + " #uevLink").val(),
                    video: $("#uevForm" + $id + " #uevVideo").val(),
                    buttonText: $("#uevForm" + $id + " #uevButton").val(),
                    isActive: convertBoolToInt("IsActive"),
                    isEventRecurring: convertBoolToInt("IER"),
                },
                success: function(result){
                    $("#uevSubmitMessage").text("Event has been updated");
                }
            }).done(function(response) {
            });
            
        }
        
    })

});

$(\'#aevTitle\').on(\'input\',function(e){
    $(this).removeClass("box_error");
    $("#aevTitle_err").text(" ");
});
$(\'#aevDescription\').on(\'input\',function(e){
    $(this).removeClass("box_error");
    $("#aevDescription_err").text(" ");
});
$(\'#aevLink\').on(\'input\',function(e){
    $(this).removeClass("box_error");
    $("#aevLink_err").text(" ");
});
$(\'#aevVideo\').on(\'input\',function(e){
    $(this).removeClass("box_error");
    $("#aevVideo_err").text(" ");
});
$(\'#aevButton\').on(\'input\',function(e){
    $(this).removeClass("box_error");
    $("#aevButton_err").text(" ");
});
$(\'#aevLocation\').on(\'input\',function(e){
    $(this).removeClass("box_error");
    $("#aevLocation_err").text(" ");
});
$(\'#aevDateTime\').on(\'input\',function(e){
    $(this).removeClass("box_error");
    $("#aevDateTime_err").text(" ");
});

function convertBoolToInt(suffix) {
    if ($("#uevForm" + $id + " #uev"+suffix).prop("checked"))
        return 1;
    else 
        return 0;
}
function isUrlValid (url, id) {
    let givenURL ;
    try {
        givenURL = new URL (url);
    } catch (error) {
        $("#aev" + id).addClass("box_error");
        $("#aev" + id + \'_err\').addClass("val_error");
        $("#aev" + id + \'_err\').text("is not in a correct URL format");
       return false; 
    }
    return true;
  }

function checkContactFields(){

    let returnValue = true;
    let requiredFields = ["Location", "Title", "Description", "Link", "Video", "Button", "DateTime"];
    
    for (let i = 0; i < requiredFields.length; i++) {
        if ($("#aev" + requiredFields[i]).val().length <= 0) {
            $("#aev" + requiredFields[i]).addClass("box_error");
            $("#aev" + requiredFields[i] + \'_err\').addClass("val_error");
            $("#aev" + requiredFields[i] + \'_err\').text(requiredFields[i] + " is required");
            returnValue = false;
        }
    }

	return returnValue;
}

function checkValidUrls(prefix) {
    let returnValue = true;
    let requiredFields = ["Link", "Video"];

    for (let i = 0; i < requiredFields.length; i++) {
        if (!isUrlValid($("#" + prefix + requiredFields[i]).val(), requiredFields[i])) {
            $("#" + prefix + requiredFields[i]).addClass("box_error");
            $("#" + prefix  + requiredFields[i] + \'_err\').addClass("val_error");
            $("#" + prefix  + requiredFields[i] + \'_err\').text(requiredFields[i] + " must be a valid url");
            returnValue = false;
        }
    }

	return returnValue;
}
';
$Event_js = fopen(__DIR__.'\js\Event.js', "w");
fwrite($Event_js, $Event_js_data);

$factory_data = '
/*add section at top of class Factory*/ 
include_once get_theme_file_path("model/location/LocationType.php"); 
include_once get_theme_file_path("model/location/TimeZone.php"); 
include_once get_theme_file_path("model/event/Category.php"); 
include_once get_theme_file_path("model/event/Event.php"); 
/*add section at top of class Factory*/

/*add section class Factory*/ 
public static function createLocationType(){
    return new LocationType();
}

public static function createLocation(){
    return new Location();
}

public static function createTimeZone(){
    return new TimeZone();
}

public static function createEvent(){
    return new Event();
}

public static function createCategory(){
    return new Category();
}
/*add section class Factory*/
';
$factory = fopen(__DIR__.'\model\commonFunctions\Factory.php', "a");
fwrite($factory, $factory_data);
?>
