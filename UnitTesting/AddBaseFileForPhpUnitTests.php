<?php

/*create folders*/
$test_dir = __DIR__.'\tests';
if (!file_exists($test_dir)) {
    mkdir($test_dir, 0777, true);
}

$test_unit_dir = __DIR__.'\tests\unit';
if (!file_exists($test_unit_dir)) {
    mkdir($test_unit_dir, 0777, true);
}

$test_model_dir = __DIR__.'\tests\model';
if (!file_exists($test_model_dir)) {
    mkdir($test_model_dir, 0777, true);
}

$test_commonFunc_dir = __DIR__.'\tests\model\commonFunctions';
if (!file_exists($test_commonFunc_dir)) {
    mkdir($test_commonFunc_dir, 0777, true);
}

$test_country_dir = __DIR__.'\tests\model\country';
if (!file_exists($test_country_dir)) {
    mkdir($test_country_dir, 0777, true);
}

$test_company_dir = __DIR__.'\tests\model\company';
if (!file_exists($test_company_dir)) {
    mkdir($test_company_dir, 0777, true);
}

$test_factory_data = '
<?php
use TestFactory as GlobalTestFactory;

include_once __DIR__ . "/../country/TestCountryModel.php";
include_once __DIR__ . "/../company/TestCompanyModel.php";
include_once __DIR__ . "/../commonFunctions/TestLogError.php";

class TestFactory {

    private $pdo;

    public function __construct(){}

    public function connect() {
        $servername = "localhost:10059";
        $database = "local";
        $username = "root";
        $password = "root";
        $charset = "utf8mb4";

        try {
            $dsn = "mysql:host=$servername;dbname=$database;charset=$charset";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo = $pdo;
            echo "Success: ";
        }
        catch (PDOException $e)
        {
            echo "Connection failed: ". $e->getMessage();
        }
    }

    public static function createTestCountry(){
        $test = new TestFactory();
        $test->connect();
        return new TestCountryModel($test->pdo);
    }

    public static function createTestCompany(){
        $test = new TestFactory();
        $test->connect();
        return new TestCompanyModel($test->pdo);
    }

    public static function createTestLogError(){
        $test = new TestFactory();
        $test->connect();
        return new TestLogError($test->pdo);
    }
}
?>
';
$test_factory = fopen(__DIR__.'\tests\model\commonFunctions\TestFactory.php', "w");
fwrite($test_factory, $test_factory_data);

$test_log_error_data = '
<?php
use PHPUnit\Framework\TestCase;

class TestLogError extends TestCase {
    private $page;
    private $err;
    private $funcName;
    private $wpdb;
    private $factory;
    
    public function __construct($conn){
        $this->wpdb = $conn;
        $this->factory = new TestFactory();
    }

    public function getPage() {return $this->page;}
    public function getErr() {return $this->err;}
    public function getFunction() {return $this->funcName;}

    public function setPage($page) {
        $page = $page;
        $this->page = $page;
    }
    public function setErr($err) {
        $err = ($err);
        $this->err = $err;
    }
    public function setFunction($funcName) {
        $funcName = ($funcName);
        $funcNameStr = "";
        if (strlen($funcName) >= 35) 
            $funcNameStr = substr($funcName, 0, 34); 
        $this->funcName = $funcNameStr;
    }
    
    private function setErrorLog($p, $e, $f) {
        $this->setPage($p);
        $this->setErr($e);
        $this->setFunction($f);
    }
    public function addError($p, $e, $func) {

        $this->setErrorLog($p, $e, $func);
        $query = "CALL sp_insert_error(:page, :error, :funct, \'%s\')";
        $stmt = $this->wpdb->prepare($query);
        $stmt->bindParam(":page", $p, PDO::PARAM_STR);
        $stmt->bindParam(":error", $e, PDO::PARAM_STR);
        $stmt->bindParam(":funct", $func, PDO::PARAM_STR);
        //$stmt->bindParam(":session", $f, PDO::PARAM_STR);
        $stmt->execute();
    }
}
?>
';
$test_log_error = fopen(__DIR__.'\tests\model\commonFunctions\TestLogError.php', "w");
fwrite($test_log_error, $test_log_error_data);

$test_company_data = '
<?php
use PHPUnit\Framework\TestCase;

class TestCompanyModel extends TestCase {
    private $countryId;
    private $name;
    private $linkName;
    private $value;
    private $img;
    private $link;
    private $type;
    private $factory;
    private $wpdb;
    private $err;

    public function __construct($conn){
        $this->wpdb = $conn;
        $this->factory = new TestFactory();
        $this->err = $this->factory->createTestLogError();
    }

    public function getCountryId(){return $this->countryId;}
    public function setCountryId($countryId){$this->countryId = $countryId;}
    public function getCompanyName(){return $this->name;}
    public function setCompanyName($name){$this->name = $name;}
    public function getLinkName(){return $this->linkName;}
    public function setLinkName($linkName){$this->linkName = $linkName;}
    public function getValue(){return $this->value;}
    public function setValue($value){$this->value = $value;}
    public function getImg(){return $this->img;}
    public function setImg($img){$this->img = $img;}
    public function getLink(){return $this->link;}
    public function setLink($link){$this->link = $link;}
    public function getType(){return $this->type;} //type is a manual value, where 1 = contact, 2 = social media, 0 = miscellaneous
    public function setType($type){$this->type = $type;}

    public function getCompanyInfoDb() {
        try { 
            $query = "CALL 	sp_fetch_company_info();";
            $stmt = $this->wpdb->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            //the insert does not like _, I need to verify this is not a bug.
            $this->err->addError("TestCompanyModel.php", $e, "getCompanyInfoDb", $this->wpdb);
        } 
        return $result;
    }

    public function setCompanyInfo() {
        $result = $this->getCompanyInfoDb();
        $list = array();
        if(!empty($result)) {
            foreach($result as $k => $v) {
                $company = $this->factory->createTestCompany();
                $company->setCompanyName($v["companyName"]);
                $company->setLinkName($v["linkName"]);
                $company->setValue($v["value"]);
                $company->setImg($v["img"]);
                $company->setLink($v["link"]);
                $company->setType($v["type"]);
                array_push($list,$company);
            }
        } else {
            $this->err->addError("Company.php", "fetched getCompanyInfoDb array is empty", "setCompanyInfo()", $this->wpdb);
        }
        return $list;
    }

    //Need to add this to the class
    public function getLinks() {
        return array (
            $this->getLinkName(),
            $this->getValue(),
            $this->getLink(),
            $this->getType(),
            $this->getImg()
        );
    }


}
?>
';
$test_company = fopen(__DIR__.'\tests\model\company\TestCompanyModel.php', "w");
fwrite($test_company, $test_company_data);

$test_country_data = '
<?php 
use PHPUnit\Framework\TestCase;
class TestCountryModel extends TestCase {
    private $countryId;
    private $name;
    private $factory;
    private $err;
    private $wpdb;

    public function __construct($conn){
        $this->wpdb = $conn;
        $this->factory = new TestFactory();
    }

    public function getCountryId(){return $this->countryId;}
    public function setCountryId($countryId){$this->countryId = $countryId;}
    public function getCountryName(){return $this->name;}
    public function setCountryName($name){$this->name = $name;}

    public function getActiveCountryDb() {
        try { 
            $query = "CALL 	sp_fetch_active_country();";
            $stmt = $this->wpdb->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->err->addError("TestCountryModel.php", $e, "getActiveCountryDb()", $this->wpdb);
        } 
        return $result;
    }

    public function setActiveCountry() {
        $result = $this->getActiveCountryDb();
        $list = array();
        if(!empty($result)) {
            foreach($result as $k => $v) {
                $country = $this->factory->createTestCountry();
                $country->setCountryId($v["countryId"]);
                $country->setCountryName($v["name"]);
                array_push($list,$country);
            }
        } else {
            $this->err->addError("TestCountryModel.php", "fetched getActiveCountryDb array is empty", "setActiveCountry()", $this->wpdb);
        }
        return $list;
    }
}
?>
';
$test_country = fopen(__DIR__.'\tests\model\country\TestCountryModel.php', "w");
fwrite($test_country, $test_country_data);

$test_country_test_data = '
<?php
use PHPUnit\Framework\TestCase;
include_once __DIR__ . "/../model/commonFunctions/TestFactory.php";
class TestCountry extends TestCase {
    private $country;
    private $factory;

    //setup connections
    public function setUp():void {
        $this->factory = new TestFactory();
        $this->country = $this->factory->createTestCountry();
    }

    public function testGetCountriesFromDb():void {
        $result = $this->country->getActiveCountryDb();
        $this->assertEquals(1, $result[0]["countryId"]);
    }

    public function testSetActiveCountry():void {
        $result = $this->country->setActiveCountry();
        $this->assertEquals(1, $result[0]->getCountryId());
        $this->assertEquals("Mexico", $result[0]->getCountryName());
    }
}
?>
';
$test_country_test = fopen(__DIR__.'\tests\unit\TestCountry.php', "w");
fwrite($test_country_test, $test_country_test_data);

$test_company_test_data = '
<?php
use PHPUnit\Framework\TestCase;
include_once __DIR__ . "/../model/commonFunctions/TestFactory.php";
class TestCompany extends TestCase {
    private $company;
    private $factory;

    //setup connections
    public function setUp():void {
        $this->factory = new TestFactory();
        $this->company = $this->factory->createTestCompany();
    }

    public function testGetCompanyFromDb():void {
        $result = $this->company->getCompanyInfoDb();
        $this->assertEquals(1, $result[0]["countryId"]);
        $this->assertEquals("Tiempos De Cambio", $result[0]["companyName"]);
    }

    public function testSetCompanyInfo():void {
        $result = $this->company->setCompanyInfo();
        $this->assertEquals("Tiempos De Cambio", $result[0]->getCompanyName());
        $this->assertEquals("Télefono", $result[0]->getLinkName());
        $this->assertEquals("479 110 6160", $result[0]->getValue());
    }

    public function testGetLinks():void {
        $result = $this->company->setCompanyInfo();
        $this->assertEquals("Télefono", $result[0]->getLinks()[0]);
        $this->assertEquals("479 110 6160", $result[0]->getLinks()[1]);
        $this->assertEquals("https://api.whatsapp.com/send?phone=4791106160", $result[0]->getLinks()[2]);
    }

}
?>
';
$test_company_test = fopen(__DIR__.'\tests\unit\TestCompany.php', "w");
fwrite($test_company_test, $test_company_test_data);
?>

?>
