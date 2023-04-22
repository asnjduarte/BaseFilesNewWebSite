<?php

$location_dir = __DIR__.'\model\location';
if (!file_exists($location_dir)) {
    mkdir($location_dir, 0777, true);
}

$location_controller_data = '
<?php
include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
$factory = new Factory();

//get objects of locatin (country, state, and cities)
$location = $factory->createLocation();
$countries = $location->setCountryInfo();

//set country id and fetch the state info for the country
$location->setCountryId($countries[0]->getCountryId());
$states = $location->setStateInfo();

//set state id and fetch the cities info for the state
$location->setStateId($states[0]->getStateId());
$cities = $location->setCityInfo();
?>
';
$location_controller = fopen(__DIR__.'\controller\LocationController.php', "w");
fwrite($location_controller, $location_controller_data);

$location_data = '
<?php
class Location {
    private $cityId;
    private $cityName;
    private $stateId;
    private $stateName;
    private $stateAbbr;
    private $countryId;
    private $countryName;
    private $countryAbbr;
    private $factory;
    private $err;
    
    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    public function getCityId(){return $this->cityId;}
    public function setCityId($cityId){$this->cityId = $cityId;}
    public function getCityName(){return $this->cityName;}
    public function setCityName($cityName){$this->cityName = $cityName;}
    public function getStateId(){return $this->stateId;}
    public function setStateId($stateId){
        $stateId = filter_var($stateId, FILTER_SANITIZE_NUMBER_INT);
        if ($stateId < 0 || $stateId > 10000)    
            $stateId = 1;
        $this->stateId = $stateId;
    }
    public function getStateName(){return $this->stateName;}
    public function setStateName($stateName){$this->stateName = $stateName;}
    public function getStateAbbr(){return $this->stateAbbr;}
    public function setStateAbbr($stateAbbr){$this->stateAbbr = $stateAbbr;}
    public function getCountryId(){return $this->countryId;}
    public function setCountryId($countryId){
        $countryId = filter_var($countryId, FILTER_SANITIZE_NUMBER_INT);
        if ($countryId < 0 || $countryId > 1000)    
            $countryId = 1;
        $this->countryId = $countryId;
    }
    public function getCountryName(){return $this->countryName;}
    public function setCountryName($countryName){$this->countryName = $countryName;}
    public function getCountryAbbr(){return $this->countryAbbr;}
    public function setCountryAbbr($countryAbbr){$this->countryAbbr = $countryAbbr;}

    //fetch and return array of all active countries from db
    public function fetchCountryInfo() {
        try { 
            $query = "CALL sp_fetch_active_country();";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Location.php", $this->wpdb->last_error, "sp_fetch_active_country", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("Location.php", $e, "setCountryInfo", $this->wpdb);
        } 
        return $result;
    }

    //set and return array of country objects
    public function setCountryInfo() {
        $result = $this->fetchCountryInfo();

        $arr = array();
        if($result) {
            try {
                foreach ($result as $k => $v) {
                    $itm = new Location;
                    $itm->setCountryId($v["countryId"]);
                    $itm->setCountryName($v["name"]);
                    $itm->setCountryAbbr($v["abbr"]);
                    array_push($arr, $itm);
                }   
            } catch (Exception $e) {
                $this->err->addError("Location.php", $e, "setCityInfo", $this->wpdb);
            }
        } else {
            $this->err->addError("Location.php", "fetchCityInfo result was empty", "setCityInfo", $this->wpdb);
        }
        return $arr;
    }

    //fetch and return array of states for specific country from db
    public function fetchStateInfo(){
        try { 
            $query = "CALL sp_fetch_states_by_country(\'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getCountryId()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Location.php", $this->wpdb->last_error, "sp_fetch_active_country", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("Location.php", $e, "setCountryInfo", $this->wpdb);
        } 
        return $result;
    }

    //set and return array of state objects
    public function setStateInfo(){
        $result = $this->fetchStateInfo();

        $arr = array();
        if($result) {
            try {
                foreach ($result as $k => $v) {
                    $itm = new Location;
                    $itm->setStateId($v["stateId"]);
                    $itm->setStateName($v["stateName"]);
                    $itm->setStateAbbr($v["stateAbbr"]);
                    array_push($arr, $itm);
                }   
            } catch (Exception $e) {
                $this->err->addError("Location.php", $e, "setStateInfo", $this->wpdb);
            }
        } else {
            $this->err->addError("Location.php", "fetchStateInfo result was empty", "setStateInfo", $this->wpdb);
        }

        return $arr;
    }

    //fetch and return array of cities by specific state
    public function fetchCityInfo(){
        try { 
            $query = "CALL sp_fetch_all_city_by_state(\'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getStateId()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("City.php", $this->wpdb->last_error, "sp_fetch_all_city_by_country", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("Location.php", $e, "fetchCityInfo", $this->wpdb);
        } 
        return $result;
    }

    //set and return array of city objects
    public function setCityInfo(){
        $result = $this->fetchCityInfo();

        $arr = array();
        if($result) {
            try {
                foreach ($result as $k => $v) {
                    $itm = new Location;
                    $itm->setStateId($v["stateId"]);
                    $itm->setStateName($v["stateName"]);
                    $itm->setStateAbbr($v["stateAbbr"]);
                    $itm->setCityId($v["cityId"]);
                    $itm->setCityName($v["cityName"]);
                    array_push($arr, $itm);
                }   
            } catch (Exception $e) {
                $this->err->addError("Location.php", $e, "setCityInfo", $this->wpdb);
            }
        } else {
            $this->err->addError("Location.php", "fetchCityInfo result was empty", "setCityInfo", $this->wpdb);
        }

        return $arr;
    }
}
?>
';
$location = fopen(__DIR__.'\model\location\Location.php', "w");
fwrite($location, $location_data);

$factory_data = '
/*add section at top of class Factory*/ 
include_once get_theme_file_path("model/location/Location.php"); 

public static function createLocation(){
    return new Location();
}
';
$factory = fopen(__DIR__.'\js\NcndaForm.js', "w");
fwrite($factory, $factory_data);

?>
