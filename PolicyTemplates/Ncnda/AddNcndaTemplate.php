<?php

$business_dir = __DIR__.'\model\business';
if (!file_exists($business_dir)) {
    mkdir($business_dir, 0777, true);
}

$business_dir = __DIR__.'\view\policy\NCNDA\';
if (!file_exists($business_dir)) {
    mkdir($business_dir, 0777, true);
}

$ajax_ncnda_data = '
/*NCNDA items - add to ajaxCalls.php*/
//call code that downloads html file and converts to docx
if(isset($_POST["ncndaDownload"])) {
    include_once get_theme_file_path(\'view/htmlToDocView.php\');  
    $htd = new HTML_TO_DOC();
    try {
        
        $htmlContent = $_POST["htmlContent"];
        $startIndx = strpos($htmlContent, "<!--start-->");
        $endIndx = strpos($htmlContent, \'<!--end-->\');
        $htmlNewContent = substr($htmlContent, $startIndx, $endIndx - $startIndx);
        $word = $htd->createDocument($htmlNewContent, "NCNDA.docx");
        
    } catch (Exception $e) {
        $err->addError("AjaxCalls.php", $e, " ncndaDownload", $wpdb);
    }
    
    if ($word) 
        echo json_encode(array("message"=>"Word document created successfully", "index"=>$startIndx . \' \' . $endIndx));
    else 
        echo json_encode(array("message"=>"Word document not created successfully"));
}

//get businesses and business contacts for NCNDA form
if(isset($_POST["ncndaGetBusinesses"])) {
    try {

        //get array of business objects
        $business = $factory->createBusiness();
        $businesses = $business->setBusinessesInfo();

        //create array of businesses
        $bArr = array();
        foreach ($businesses as $k => $v) {array_push($bArr, array("businessId"=>$v->getBusinessId(), "bName"=>$v->getBName()));}

        //get array of business contact objects
        $businessContact = $factory->createBusinessContact();
        $businessContact->setBusinessId($businesses[0]->getBusinessId());  //set default business id of Maranatha Marketing Inc
        $businessContacts = $businessContact->setBusinessContactInfo(); //get business contacts of Maranatha Marketing

        //create array of business contacts from the objects
        $bcArr = array();
        foreach ($businessContacts as $k => $v) {array_push($bcArr, array("businessContactId"=>$v->getBusinessContactId(), "bcName"=>$v->getBcName()));}
    } catch (Exception $e) {
        $err->addError("AjaxCalls.php", $e, " ncndaGetBusinesses", $wpdb);
    }
    echo json_encode (array($bArr, $bcArr)); 
}

//get specific business and specific contact once user has selected the values for the NCNDA view to download
if(isset($_POST["ncndaGetSpecificBusinessContacts"])) {
    try {

        //get array of business contact objects
        $businessContact = $factory->createBusinessContact();
        $businessContact->setBusinessId($_POST["businessId"]);  //get business id of user\'s input
        $businessContacts = $businessContact->setBusinessContactInfo(); //get business contacts of user\'s selected business

        //create array of business contacts from the objects
        $bcArr = array();
        foreach ($businessContacts as $k => $v) {
            array_push($bcArr, array("businessContactId"=>$v->getBusinessContactId(), "bcName"=>$v->getBcName(), "bcNationality"=>$v->getBcNationality(), "bcCompanyPosition"=>$v->getBcCompanyPosition(), "bcCompanyPositionAbbr"=>$v->getBcCompanyPositionAbbr(), "bcPhone"=>$v->getBcPhone(),"bcPhone2"=>$v->getBcPhone2(), "getBcEmail"=>$v->getBcEmail(), "getBcPassport"=>$v->getBcPassport(), "getIsActive"=>$v->getIsActive()));
        }
    } catch (Exception $e) {
        $err->addError("AjaxCalls.php", $e, " ncndaGetBusinesses", $wpdb);
    }
    echo json_encode ($bcArr); 
}
/*end NCNDA items*/
';
$ajax_ncnda = fopen(__DIR__.'\ajax\AjaxCalls.php', "a");
fwrite($ajax_ncnda, $ajax_ncnda_data);

$businesses_controller_data = '
<?php 
include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
$factory = new Factory();

//get array of business company objects 
$business = $factory->createBusiness();
$businesses = $business->setBusinessesInfo();

//get array of business contact objects
$businessContact = $factory->createBusinessContact();
$businessContact->setBusinessId($businesses[0]->getBusinessId()); //set default business id
$businessContact = $businessContact->setBusinessContactInfo(); //get default business contact
?>
';
$businesses_controller = fopen(__DIR__.'\controller\BusinessesController.php', "w");
fwrite($businesses_controller, $businesses_controller_data);

$specific_businesses_controller_data = '
<?php 
include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
$factory = new Factory();
$bArr = array();
$bcArr = array();
for($i = 0; $i < $_POST["nfPeopleToAdd"]; $i++) {
    //set business id from ncndaForm.php
    $business = $factory->createBusiness();
    $business->setBusinessId($_POST["nfCompany" . $i]);

    //get specific business objects that user selected from ncndaForm.php
    $specificBusinessInfo = $business->setSpecificBusinessInfo($business->getBusinessSpecificInfoDb());
    
    //set business contact id from ncndaForm.php
    $businessContact = $factory->createBusinessContact();
    $businessContact->setBusinessContactId($_POST["nfName" . $i]);

    //get specific business contact objects that user selected from ncndaForm.php
    $specificBusinessContactInfo = $businessContact->setSpecificBusinessInfo($businessContact->getBusinessContactSpecificInfoDb());
    //echo "Count: " . $specificBusinessContactInfo->getBcName();

    //create an array instead of objects
    
    $country = $factory->createCountry();
    foreach($specificBusinessInfo as $k => $v) {
        $countryById = $country->getCountryByCity($v->getCityId());
        array_push($bArr, array("businessId"=>$v->getBusinessId(), "bName"=>$v->getBName(), "bAddr"=>$v->getBAddr(), "bAddrNum"=>$v->getBAddrNum(), "bZip"=>$v->getBZip(), "cityId"=>$v->getCityId(), "countryId"=>$countryById[$k]["countryId"], "countryName"=>$countryById[$k]["countryName"]));
    }

    foreach ($specificBusinessContactInfo as $k => $v) {
        array_push($bcArr, array("businessContactId"=>$v->getBusinessContactId(), "bcName"=>$v->getBcName(), "bcNationality"=>$v->getBcNationality(), "bcCompanyPosition"=>$v->getBcCompanyPosition(), "bcCompanyPositionAbbr"=>$v->getBcCompanyPositionAbbr(), "bcPhone"=>$v->getBcPhone(), "bcPhone2"=>$v->getBcPhone2(), "bcEmail"=>$v->getBcEmail(), "bcPassport"=>$v->getBcPassport(), "isActive"=>$v->getIsActive()));
    }
}
?>
';
$specific_businesses_controller = fopen(__DIR__.'\controller\SpecificBusinessController.php', "w");
fwrite($specific_businesses_controller, $specific_businesses_controller_data);

$js_NcndaForm_data = '
$(document).ready(function(){
    //when user changes country box on update event view update states and cities
    $(\'#niqBtn\').on(\'click\', function(e) {
        e.preventDefault();
        let peopleToAdd = parseInt($("#niqNum").val());
        var appendString = "";
        $.ajax({
            type:\'POST\',
            url:\'?page_id=6\',
            dataType: "json",
            headers: {\'Csrftoken\': $(\'meta[name="csrf-token"]\').attr(\'content\')},
            data: {
                ncndaGetBusinesses: true,
            },
            success: function(result){

                //create options for dropdown of all businesses 
                let businessesAppnd = "";
                for (let i = 0; i < result[0].length; i++) {
                    businessesAppnd += \'<option value="\'+ result[0][i]["businessId"] + \'">\'+ result[0][i]["bName"] +\'</option>\';
                }

                //create options for dropdown of all business contacts 
                let businessContAppnd = "";
                for (let i = 0; i < result[1].length; i++) {
                    businessContAppnd += \'<option value="\'+ result[1][i]["businessContactId"] + \'">\'+ result[1][i]["bcName"] +\'</option>\';
                }

                //create the labels and input fields of the users\' forms
                for(let i = 0; i < peopleToAdd; i++) {
                    appendString +=\'<div><label>Name: </label><select id="nfName\'+ i + \'" name="nfName\'+ i + \'" num=\' + i + \' class="form-select nameDropDown" aria-label="business contact dropdown">\'+ businessContAppnd + \'</select></div>\' +
                    \'<div><label>Company: </label><select id="nfCompany\'+ i + \'" name="nfCompany\'+ i + \'" num=\' + i + \' class="form-select"  onchange="changeFunc(nfCompany\'+ i + \');" aria-label="businesses dropdown">\'+ businessesAppnd + \'</select></div>\';
                                 
                }
                $("#nfForm").append(appendString);
                $("#nfPeopleToAdd").val(peopleToAdd);
            },error: function (request, status, error) {
                alert(error);
            }
        });
    });

})

//change the business contacts based on change of business
function changeFunc(val) {
    $.ajax({
        type:\'POST\',
        url:\'?page_id=6\',
        dataType: "json",
        headers: {\'Csrftoken\': $(\'meta[name="csrf-token"]\').attr(\'content\')},
        data: {
            ncndaGetSpecificBusinessContacts: true,
            businessId: $(val).val(),
        },
        success: function(result){
            //create options for dropdown of all business contacts for specific business
            let businessContAppnd = "";
            for (let i = 0; i < result.length; i++) {
                businessContAppnd += \'<option value="\'+ result[i]["businessContactId"] + \'">\'+ result[i]["bcName"] +\'</option>\';
            }
            $("#nfName" + $(val).attr("num") + " option").remove();
            $("#nfName" + $(val).attr("num")).append(businessContAppnd);
        },error: function (request, status, error) {
            alert(error);
        }
    });
    alert("clicked");
}
';
$js_NcndaForm = fopen(__DIR__.'\js\NcndaForm.js', "w");
fwrite($js_NcndaForm, $js_NcndaForm_data);

$js_Ncnda_view_data = '
$(document).ready(function(){ 
    $(\'#ncndaBtn\').on(\'click\',function(e){ 
    	e.preventDefault();
        var bodyHtml = document.getElementsByTagName(\'body\')[0].innerHTML;
        $.ajax({
            type:\'POST\',
            url:\'?page_id=6\',
            dataType: "json",
            headers: {\'Csrftoken\': $(\'meta[name="csrf-token"]\').attr(\'content\')},
            data: {
                ncndaDownload: true,
                htmlContent: bodyHtml,
            },
            success: function(result){
                alert(result.message);
                
            },error: function (request, status, error) {
                alert(error);
            }
        });
    });
});
';
$js_Ncnda_view = fopen(__DIR__.'\js\NcndaView.js', "w");
fwrite($js_Ncnda_view, $js_Ncnda_view_data);

$factory_data = '
//add to top of factory.php
include_once get_theme_file_path("model/business/Businesses.php"); 
include_once get_theme_file_path("model/business/BusinessContact.php"); 

//add inside of factory class
public static function createBusinessContact(){
    return new BusinessContact();
}
public static function createBusiness(){
    return new Businesses();
}
';
$factory = fopen(__DIR__.'\model\commonFunctions\Factory.php', "a");
fwrite($factory, $factory_data);

$business_contact_model_data = '
<?php
class BusinessContact {
    private $businessContactId;
    private $bcName;
    private $bcNationality;
    private $bcCompanyPosition;
    private $bcCompanyPositionAbbr;
    private $bcPhone;
    private $bcPhone2;
    private $bcEmail;
    private $bcPassport;
    private $businessId;
    private $isActive;
    private $factory;
    private $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    public function getBusinessContactId(){return $this->businessContactId;}
    public function setBusinessContactId($businessContactId){$this->businessContactId = $businessContactId;}
    public function getBcName(){return $this->bcName;}
    public function setBcName($bcName){$this->bcName = $bcName;}
    public function getBcNationality(){return $this->bcNationality;}
    public function setBcNationality($bcNationality){$this->bcNationality = $bcNationality;}
    public function getBcCompanyPosition(){return $this->bcCompanyPosition;}
    public function setBcCompanyPosition($bcCompanyPosition){$this->bcCompanyPosition = $bcCompanyPosition;}
    public function getBcCompanyPositionAbbr(){return $this->bcCompanyPositionAbbr;}
    public function setBcCompanyPositionAbbr($bcCompanyPositionAbbr){$this->bcCompanyPositionAbbr = $bcCompanyPositionAbbr;}
    public function getBcPhone(){return $this->bcPhone;}
    public function setBcPhone($bcPhone){$this->bcPhone = $bcPhone;}
    public function getBcPhone2(){return $this->bcPhone2;}
    public function setBcPhone2($bcPhone2){$this->bcPhone2 = $bcPhone2;}
    public function getBcEmail(){return $this->bcEmail;}
    public function setBcEmail($bcEmail){$this->bcEmail = $bcEmail;}
    public function getBcPassport(){return $this->bcPassport;}
    public function setBcPassport($bcPassport){$this->bcPassport = $bcPassport;}
    public function getBusinessId(){return $this->businessId;}
    public function setBusinessId($businessId){$this->businessId = $businessId;}
    public function getIsActive(){return $this->isActive;}
    public function setIsActive($isActive){$this->isActive = $isActive;}

    public function getBusinessContactsInfoDb() {
        try { 
            $query = "CALL 	sp_fetch_all_active_business_contacts_by_company(\'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getBusinessId()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("BusinessContact.php", $this->wpdb->last_error, "	sp_fetch_all_active_business_contacts_by_company", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("BusinessContact.php", $e, "getBusinessContactsInfoDb", $this->wpdb);
        } 
        return $result;
    }

    public function setBusinessContactInfo() {
        $result = $this->getBusinessContactsInfoDb();
        $list = array();
        if(!empty($result)) {
            foreach($result as $k => $v) {
                $businessContact = new BusinessContact();
                $businessContact->setBusinessContactId($v["businessContactId"]);
                $businessContact->setBcName($v["bcName"]);
                array_push($list,$businessContact);
            }
        } else {
            $this->err->addError("BusinessContact.php", "fetched getBusinessContactsInfoDb array is empty", "setBusinessContactInfo()", $this->wpdb);
        }
        return $list;
    }

    public function getBusinessContactSpecificInfoDb() {
        try { 
            $query = "CALL 	sp_fetch_specific_business_contact(\'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getBusinessContactId()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("BusinessContact.php", $this->wpdb->last_error, "	sp_fetch_specific_business_contact", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("BusinessContact.php", $e, "getBusinessContactSpecificInfoDb", $this->wpdb);
        } 
        return $result;
    }

    public function setSpecificBusinessInfo($result) {
        $list = array();
        try {
            if(!empty($result)) {
                foreach($result as $k => $v) {
                    $businessContact = new BusinessContact();
                    $this->err->addError("BusinessContact.php", $v["businessContactId"], "setSpecificBusinessInfo()", $this->wpdb);
                    $businessContact->setBusinessContactId($v["businessContactId"]);
                    $businessContact->setBcName($v["bcName"]);
                    $businessContact->setBcNationality($v["bcNationality"]);
                    $businessContact->setBcCompanyPosition($v["bcCompanyPosition"]);
                    $businessContact->setBcCompanyPositionAbbr($v["bcCompanyPositionAbbr"]);
                    $businessContact->setBcPhone($v["bcPhone"]);
                    $businessContact->setBcPhone2($v["bcPhone2"]);
                    $businessContact->setBcEmail($v["bcEmail"]);
                    $businessContact->setBcPassport($v["bcPassport"]);
                    $businessContact->setBusinessId($v["businessId"]);
                    $businessContact->setIsActive($v["isActive"]);
                    array_push($list,$businessContact);
                    $this->err->addError("BusinessContact.php", $businessContact->getBusinessContactId(), "setSpecificBusinessInfo()", $this->wpdb);
                }
            } else {
                $this->err->addError("BusinessContact.php", "fetched getBusinessContactSpecificInfoDb array is empty", "setSpecificBusinessInfo()", $this->wpdb);
            }
        } catch (Exception $e) {
            $this->err->addError("BusinessContact.php", $e, "setSpecificBusinessInfo()", $this->wpdb);
        }
        
        return $list;
    }

    //Insert business and return last inserted object
    public function insertBusinessContact() {
        try {
            $query = "CALL 	sp_insert_business_contact(\'%s\', \'%s\', \'%d\', \'%s\', \'%d\', \'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getBcName(), $this->getBcNationality(), $this->getBcCompanyPosition(), $this->getBcCompanyPositionAbbr(), $this->getBcPhone(), get_current_user_id()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("BusinessContact.php", $this->wpdb->last_error, "sp_insert_business_contact", $this->wpdb);   
            }
            return $result;
        } catch (Exception $e) {
            $this->err->addError("BusinessContact.php", $e, "insertBusinessContact", $this->wpdb);   
        }
    }

    //Update business and return 1 or 0 based on last update
    public function updateBusinessContact(){
        try {
            $query = "CALL 	sp_update_business_contact(\'%d\',\'%s\', \'%s\', \'%d\', \'%s\', \'%d\', \'%d\', \'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getBusinessContactId(), $this->getBcName(), $this->getBcNationality(), $this->getBcCompanyPosition(), $this->getBcCompanyPositionAbbr(), $this->getBcPhone(), get_current_user_id(), $this->getBcPhone2()), ARRAY_A);
            if($this->wpdb->last_error !== \'\'){
                $this->err->addError("BusinessContact.php", $this->wpdb->last_error, "sp_update_business_contact", $this->wpdb);   
            }
            return $result;
        } catch (Exception $e) {
            $this->err->addError("BusinessContact.php", $e, "updateBusinessContact", $this->wpdb);   
        }
    }
}
?>
';
$business_contact_model = fopen(__DIR__.'\model\business\BusinessContact.php', "w");
fwrite($business_contact_model, $business_contact_model_data);

$businesses_model_data = '
<?php
class Businesses {
    private $businessId;
    private $bName;
    private $bAddr;
    private $bAddrNum;
    private $bZip;
    private $cityId;
    private $isActive;
    private $factory;
    private $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
    }

    public function getBusinessId(){return $this->businessId;}
    public function setBusinessId($businessId){$this->businessId = $businessId;}
    public function getBName(){return $this->bName;}
    public function setBName($bName){$this->bName = $bName;}
    public function getBAddr(){return $this->bAddr;}
    public function setBAddr($bAddr){$this->bAddr = $bAddr;}
    public function getBAddrNum(){return $this->bAddrNum;}
    public function setBAddrNum($bAddrNum){$this->bAddrNum = $bAddrNum;}
    public function getBZip(){return $this->bZip;}
    public function setBZip($bZip){$this->bZip = $bZip;}
    public function getCityId(){return $this->cityId;}
    public function setCityId($cityId){$this->cityId = $cityId;}
    public function getIsActive(){return $this->isActive;}
    public function setIsActive($isActive){$this->isActive = $isActive;}

    public function getBusinessesInfoDb() {
        try { 
            $query = "CALL 	sp_fetch_all_businesses();";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Businesses.php", $this->wpdb->last_error, "	sp_fetch_all_businesses", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("Businesses.php", $e, "getBusinessesInfoDb", $this->wpdb);
        } 
        return $result;
    }

    public function setBusinessesInfo() {
        $result = $this->getBusinessesInfoDb();
        $list = array();
        if(!empty($result)) {
            foreach($result as $k => $v) {
                $businesses = new Businesses();
                $businesses->setBusinessId($v["businessId"]);
                $businesses->setBName($v["bName"]);
                array_push($list,$businesses);
            }
        } else {
            $this->err->addError("Businesses.php", "fetched getBusinessesInfoDb array is empty", "setBusinessesInfo()", $this->wpdb);
        }
        return $list;
    }

    public function getBusinessSpecificInfoDb() {
        try { 
            $query = "CALL 	sp_fetch_specific_business(\'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getBusinessId()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Businesses.php", $this->wpdb->last_error, "	sp_fetch_specific_business", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("Businesses.php", $e, "getBusinessSpecificInfoDb", $this->wpdb);
        } 
        return $result;
    }

    public function setSpecificBusinessInfo($result) {
        $list = array();
        if(!empty($result)) {
            foreach($result as $k => $v) {
                $businesses = new Businesses();
                $businesses->setBusinessId($v["businessId"]);
                $businesses->setBName($v["bName"]);
                $businesses->setBAddr($v["bAddr"]);
                $businesses->setBAddrNum($v["bAddrNum"]);
                $businesses->setBZip($v["bZip"]);
                $businesses->setCityId($v["cityId"]);
                $businesses->setIsActive($v["isActive"]);
                array_push($list,$businesses);
            }
        } else {
            $this->err->addError("Businesses.php", "fetched getBusinessSpecificInfoDb array is empty", "setSpecificBusinessInfo()", $this->wpdb);
        }
        return $list;
    }

    //Insert business and return last inserted object
    public function insertBusiness() {
        try {
            $query = "CALL 	sp_insert_business(\'%s\', \'%s\', \'%d\', \'%s\', \'%d\', \'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getBName(), $this->getBAddr(), $this->getBAddrNum(), $this->getBZip(), $this->getCityId(), get_current_user_id()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Businesses.php", $this->wpdb->last_error, "sp_insert_business", $this->wpdb);   
            }
            return $result;
        } catch (Exception $e) {
            $this->err->addError("Businesses.php", $e, "insertBusiness", $this->wpdb);   
        }
    }

    //Update business and return 1 or 0 based on last update
    public function updateBusiness(){
        try {
            $query = "CALL 	sp_update_business(\'%d\',\'%s\', \'%s\', \'%d\', \'%s\', \'%d\', \'%d\', \'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getBusinessId(), $this->getBName(), $this->getBAddr(), $this->getBAddrNum(), $this->getBZip(), $this->getCityId(), get_current_user_id(), $this->getIsActive()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Businesses.php", $this->wpdb->last_error, "sp_update_business", $this->wpdb);   
            }
            return $result;
        } catch (Exception $e) {
            $this->err->addError("Businesses.php", $e, "updateBusiness", $this->wpdb);   
        }
    }

}
?>
';
$businesses_model = fopen(__DIR__.'\model\business\Businesses.php', "w");
fwrite($businesses_model, $businesses_model_data);

$country_model_data = '
public function getCountryByCity($cityId) {
    try { 
        $query = "CALL 	sp_fetch_country_by_city(\'%d\');";
        $result = $this->wpdb->get_results($this->wpdb->prepare($query, $cityId), ARRAY_A);
        if($this->wpdb->last_error !== \'\') {
            $this->err->addError("Country.php", $this->wpdb->last_error, "sp_fetch_country_by_city", $this->wpdb);   
        }
    } catch (Exception $e) {
        $this->err->addError("Country.php", $e, "getCountryByCity", $this->wpdb);
    } 
    return $result;
}
';
$country_model = fopen(__DIR__.'\model\country\Country.php', "a");
fwrite($country_model, $country_model_data);

$ncnda_form_data = '
<?php include get_theme_file_path(\'controller/BusinessesController.php\');  ?>
<div class="container pt6">
    <div class="row">
        <div class="col">
            <form>
                <div>
                    <label>How many people are you needing to add to the NCNDA form?: </label>
                    <input id="niqNum" name="niqNum" value="" type="text" class="form-control acceptedChar" placeholder="Number of people" aria-label="Add number of people to NCNDA" aria-describedby="Number" size="2">
                </div>
                <input id="niqBtn" type="submit" value="Initial question" name="niqBtn" class="btn btn-primary">
            </form>
        </div>
    </div>
</div> 
<div class="container pt6">
    <div class="row">
        <div class="col">
            <form id="nfForm" method="POST" action="../ncnda/">
                <input id="nfUpdate" type="submit" value="Update NCNDA View" name="nfUpdate" class="btn btn-primary">
                <input type="hidden" id="nfPeopleToAdd" name="nfPeopleToAdd" value="">
            </form>
        </div>
    </div>
</div> 
';
$ncnda_form = fopen(__DIR__.'\view\policy\NCNDA\NcndaForm.php', "w");
fwrite($ncnda_form, $ncnda_form_data);

$ncnda_initial_question_data = '
<div class="container pt6">
    <div class="row">
        <div class="col">
            <form>
                <div>
                    <label>How many people are you needing to add to the NCNDA form?: </label>
                    <input name="niqNum" value="" type="text" class="form-control acceptedChar" placeholder="Number of people" aria-label="Add number of people to NCNDA" aria-describedby="Number" size="2">
                </div>
                <input id="niqBtn" type="submit" value="Initial question" name="niqBtn" class="btn btn-primary">
            </form>
        </div>
    </div>
</div> 
';
$ncnda_initial_question = fopen(__DIR__.'\view\policy\NCNDA\NcndaInitialQuestion.php', "w");
fwrite($ncnda_initial_question, $ncnda_initial_question_data);

$ncnda_view_data = '

<?php  
    $policyId = 2;
    include get_theme_file_path(\'controller/PolicyController.php\'); 
    include get_theme_file_path(\'controller/SpecificBusinessController.php\'); 
?>
<!--start-->
<div class="container pt6">
    <div class="row">
        <div class="col">
            <table class="table">
                <tbody>
                    <tr>
                        <td class="col-2"><img src="http://test.local/wp-content/uploads/2023/03/MMINC-Y-LIFE-100-10.png" class="img-fluid" alt="Logo of three sister companies"></td>
                        <td class="col-10">
                            <h4>Maranatha Marketing Inc.</h4>
                            <div>Luis L\'Hoist - CEO</div>
                            <div>PH: 52-1-415-153-5601</div>
                            <div>E: luislhoist@maranathamarketing.net</div>
                            <div>ADDR: 45615 Tamihi Way #51, Chilliwack, BC V2R 0X4, CA</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h2><?php echo $policy->getPolicyName();?></h2>
            <div>
                <div><?php echo $policyDescription?></div>
                <br/>
                <?php foreach($termsList as $k=> $v) { ?>
                    <h3><?php echo $v->getPolicyTermName();?></h3>
                    <div><?php echo $v->getPolicyTermDescription(); ?></div>
                    <br/>
                <?php } ?>
            </div>
            <?php for($i = 0; $i < $_POST["nfPeopleToAdd"]; $i++) { ?>
            <table class="table table-bordered mt-5">
                <tbody>
                    <tr>
                            <td class="col-0 col-md-3 p-5">
                                <h4>Name: </h4>
                                <div id="nvName<?php echo $i?>"><?php echo $bcArr[$i]["bcName"] ?></div>
                            </td>
                            <td class="col-0 col-md-3 p-5">
                                <h4>Passport: </h4>
                                <div id="nvPassport"></div>
                            </td>
                            <td class="col-0 col-md-3 p-5">
                                <h4>Nationality: </h4>
                                <div id="nvNationality<?php echo $i?>"><?php echo $bcArr[$i]["bcNationality"] ?></div>
                            </td>
                            <td class="col-0 col-md-3 p-5">
                                <h4>Company: </h4>
                                <div id="nvCompany<?php echo $i?>"><?php echo $bArr[$i]["bName"] ?></div>
                            </td>
                    </tr>
                    <tr>
                            <td class="col-0 col-md-3 p-5">
                                <h4>Position in Company: </h4>
                                <div id="nvPosCompany<?php echo $i?>"><?php echo $bcArr[$i]["bcCompanyPositionAbbr"] ?></div>
                            </td>
                            <td class="col-0 col-md-3 p-5">
                                <h4>Company Address: </h4>
                                <div id="nvCompanyAddress<?php echo $i?>"><?php echo $bArr[$i]["bAddr"] . " " . $bArr[$i]["bAddrNum"] ?></div>
                            </td>
                            <td class="col-0 col-md-3 p-5">
                                <h4>Telephone: </h4>
                                <div id="nvTelephone<?php echo $i?>">
                                    <?php echo $bcArr[$i]["bcPhone"] ?><br/>
                                    <?php echo $bcArr[$i]["bcPhone2"] ?>

                                </div>
                            </td>
                            <td class="col-0 col-md-3 p-5">
                                <h4>Email: </h4>
                                <div id="nvEmail<?php echo $i?>"><?php echo $bcArr[$i]["bcEmail"] ?></div>
                            </td>
                    </tr>
                    <tr>
                        <td class="col-0 col-md-3 p-5">
                            <h4>Country: </h4>
                            <div id="nvCountry<?php echo $i?>"><?php echo $bArr[$i]["countryName"] ?></div>
                        </td>
                        <td class="col-0 col-md-3 p-5">
                        <h4>Signature, Stamp, and Passport: </h4>
                        <div id="nvSignature<?php echo $i?>"></div>
                        </td>
                        <td class="col-0 col-md-3 p-5">
                        </td>
                        <td class="col-0 col-md-3 p-5">
                            <h4>Signed Date: </h4>
                            <div id="nvSignedDate<?php echo $i?>"></div>
                        </td>
                    </tr>
                
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
</div>
<!--end-->
<button id="ncndaBtn">Download</button>
';
$ncnda_view = fopen(__DIR__.'\view\policy\NcndaView.php', "w");
fwrite($ncnda_view, $ncnda_view_data);

$functions_data = '
<?php

$page_slug = \'ncnda-view\'; 
$new_page = array(
    \'post_type\'     => \'page\', 				
    \'post_title\'    => \'Ncnda Download View\',	// Title 
    \'post_content\'  => \'[display_ncnda_view]\',	// Content
    \'post_status\'   => \'publish\',			// Post Status
    \'post_author\'   => 1,					// Post Author ID
    \'post_name\'     => $page_slug			// Slug of the Post
);

if (!get_page_by_path( $page_slug, OBJECT, \'page\')) { // Check If Page Not Exits
    $new_page_id = wp_insert_post($new_page);
}

//display ncnda view
function fetch_ncnda_view(){add_shortcode(\'display_ncnda_view\', \'fetch_ncnda_vw\');} 
add_action(\'init\', \'fetch_ncnda_view\');
function fetch_ncnda_vw() {
    ob_start(); 
    include_once get_theme_file_path(\'view/policy/NcndaView.php\'); 
    wp_enqueue_script(\'ncnda-js\', get_template_directory_uri().\'/js/NcndaView.js\', NULL, microtime(), true);
    return ob_get_clean();
}


$page_slug = \'ncnda-form\'; 
$new_page = array(
    \'post_type\'     => \'page\', 				
    \'post_title\'    => \'Ncnda Download Form\',	// Title 
    \'post_content\'  => \'[display_ncnda_form]\',	// Content
    \'post_status\'   => \'publish\',			// Post Status
    \'post_author\'   => 1,					// Post Author ID
    \'post_name\'     => $page_slug			// Slug of the Post
);

if (!get_page_by_path( $page_slug, OBJECT, \'page\')) { // Check If Page Not Exits
    $new_page_id = wp_insert_post($new_page);
}

//display ncnda form
function fetch_ncnda_form(){add_shortcode(\'display_ncnda_form\', \'fetch_ncnda_frm\');} 
add_action(\'init\', \'fetch_ncnda_form\');
function fetch_ncnda_frm() {
    ob_start(); 
    include_once get_theme_file_path(\'view/policy/NcndaForm.php\'); 
    wp_enqueue_script(\'ncnda-js\', get_template_directory_uri().\'/js/NcndaForm.js\', NULL, microtime(), true);
    return ob_get_clean();
}

?>
';
$functions = fopen(__DIR__.'\functions.php', "a");
fwrite($functions, $functions_data);

?>
