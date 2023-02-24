<?php



$test_policy_data = '

<?php
use PHPUnit\Framework\TestCase;
include_once __DIR__ . "/../model/commonFunctions/TestFactory.php";
class TestPolicy extends TestCase {
    private $policy;
    private $factory;

    //setup connections
    public function setUp():void {
        $this->factory = new TestFactory();
        $this->policy = $this->factory->createTestPolicy();
    }

    public function testSetPolicyId():void {
        $this->policy->setPolicyId(-1);
        $this->assertEquals(1, $this->policy->getPolicyId());
        
        $this->policy->setPolicyId(999999);
        $this->assertEquals(1, $this->policy->getPolicyId());

        $this->policy->setPolicyId(5);
        $this->assertEquals(5, $this->policy->getPolicyId());
    }

    public function testGetPolicyTermInfoFromDb():void {
        $this->policy->setPolicyId(1);
        $result = $this->policy->getPolicyTermInfoFromDb();
        $this->assertEquals(1, $result[0]["genericPolicyTermId"]);
        $this->assertEquals("Información que es recogida", $result[0]["policyTermName"]);
    }

    public function testGetPolicyInfoFromDb():void {
        $this->policy->setPolicyId(1);
        $result = $this->policy->getPolicyInfoFromDb();
        $this->assertEquals(1, $result[0]["policyId"]);
        $this->assertEquals("Policia de Privacidad", $result[0]["policyName"]);
    }

    public function testSetPolicyInfo():void {
        $this->policy->setPolicyId(1);
        $result = $this->policy->setPolicyInfo();
        $this->assertEquals("Policia de Privacidad", $this->policy->getPolicyName());
        $this->assertEquals("Información que es recogida", $result[0]->getPolicyTermName());
        //$this->assertEquals("La presente política de privacidad establece los términos en que Tiempos de cambio usa y protege la información que es proporcionada por sus usuarios al momento de utilizar su sitio web. Esta compañía está comprometida con la seguridad de los datos de sus usuarios. Cuando le pedimos llenar los campos de información personal con la cual usted pueda ser identificado, lo hacemos asegurando que sólo se emplea de acuerdo a los términos de este documento. Sin embargo, esta Política de Privacidad puede cambiar con el tiempo o ser actualizada por lo que le recomendamos y enfatizamos revisar continuamente esta página para asegurarse que está de acuerdo con dichos cambios.", $this->policy->getPolicyDescription());
    }
    
}
?>

';
$test_policy = fopen(__DIR__.'\unit\TestPolicy.php', "w");
fwrite($test_policy, $test_policy_data);

$test_policy_model_data = '
<?php 
class TestPolicyModel {
    private $policyId;
    private $policyName;
    private $policyDescription;
    private $policyTermName;
    private $policyTermDescription;
    private $factory;
    private $err;
    private $wpdb;

    public function __construct($conn){
        $this->wpdb = $conn;
        $this->factory = new TestFactory();
        $this->err = $this->factory->createTestLogError();
    }

    public function getPolicyId(){return $this->policyId;}
    public function getPolicyName(){return $this->policyName;}
    public function getPolicyDescription(){return $this->policyDescription;}
    public function getPolicyTermName(){return $this->policyTermName;}
    public function getPolicyTermDescription(){return $this->policyTermDescription;}
    public function setPolicyId($policyId){
        $policyId = filter_var($policyId, FILTER_SANITIZE_NUMBER_INT);
        if ($policyId < 0 || $policyId > 100000)    
            $policyId = 1;
        $this->policyId = $policyId;
    }
    public function setPolicyTermName($policyTermName){$this->policyTermName = $policyTermName;}
    public function setPolicyName($policyName){$this->policyName = $policyName;}
    public function setPolicyDescription($policyDescription){$this->policyDescription = $policyDescription;}
    public function setPolicyTermDescription($policyTermDescription){$this->policyTermDescription = $policyTermDescription;}
    
    public function getPolicyTermInfoFromDb(){
        try { 
            $policyId = $this->getPolicyId();
            $query = "CALL 	sp_fetch_policy_terms(:policyId);";
            $stmt = $this->wpdb->prepare($query);
            $stmt->bindParam(":policyId", $policyId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->err->addError("Company.php", $e, "getPolicyTermInfoFromDb", $this->wpdb);
        } 
        return $result;
    }

    public function getPolicyInfoFromDb(){
        try { 
            $policyId = $this->getPolicyId();
            $query = "CALL 	sp_fetch_policy(:policyId);";
            $stmt = $this->wpdb->prepare($query);
            $stmt->bindParam(":policyId", $policyId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->err->addError("Company.php", $e, "getPolicyInfoFromDb", $this->wpdb);
        } 
        return $result;
    }

    public function setPolicyInfo() {
        $policy = $this->getPolicyInfoFromDb();
        $this->setPolicyName($policy[0]["policyName"]);
        $this->setPolicyDescription($policy[0]["policyDescription"]);

        $termsList = array();
        $policy_terms = $this->getPolicyTermInfoFromDb();
        foreach($policy_terms as $k => $v){
            $policy = new TestPolicyModel($this->wpdb);
            $policy->setPolicyTermName($v["policyTermName"]);
            $policy->setPolicyDescription($v["policyTermDescription"]);
            array_push($termsList, $policy);
        }
        return $termsList;
    }
}
?>
';
$test_policy_model = fopen(__DIR__.'\model\company\TestPolicyModel.php', "w");
fwrite($test_policy_model, $test_policy_model_data);

$test_factory_data = '
//add items in the php and class object
include_once __DIR__ . "/../company/TestPolicyModel.php";

public static function createTestPolicy(){
    $test = new TestFactory();
    $test->connect();
    return new TestPolicyModel($test->pdo);
}
';
$test_factory = fopen(__DIR__.'\model\commonFunctions\TestFactory.php', "a");
fwrite($test_factory, $test_factory_data);

?>
