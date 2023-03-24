<?php
$policy_model_data = '
<?php 
class Policy {
    private $policyId;
    private $policyName;
    private $policyDescription;
    private $policyTermName;
    private $policyTermDescription;
    private $factory;
    private $err;

    public function __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->factory = new Factory();
        $this->err = $this->factory->createErr();
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
    private function setPolicyTermName($policyTermName){$this->policyTermName = $policyTermName;}
    private function setPolicyName($policyName){$this->policyName = $policyName;}
    private function setPolicyDescription($policyDescription){$this->policyDescription = $policyDescription;}
    private function setPolicyTermDescription($policyTermDescription){$this->policyTermDescription = $policyTermDescription;}
    
    private function getPolicyTermInfoFromDb(){
        try { 
            $query = "CALL 	sp_fetch_policy_terms(\'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getPolicyId()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Policy.php", $this->wpdb->last_error, "sp_fetch_policy_terms", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("Policy.php", $e, "getPolicyTermInfoFromDb", $this->wpdb);
        }
        return $result;
    }

    private function getPolicyInfoFromDb(){
        try { 
            $query = "CALL 	sp_fetch_policy(\'%d\');";
            $result = $this->wpdb->get_results($this->wpdb->prepare($query, $this->getPolicyId()), ARRAY_A);
            if($this->wpdb->last_error !== \'\') {
                $this->err->addError("Policy.php", $this->wpdb->last_error, "sp_fetch_policy", $this->wpdb);   
            }
        } catch (Exception $e) {
            $this->err->addError("Policy.php", $e, "getPolicyInfoDb", $this->wpdb);
        } 
        return $result;
    }

    public function setPolicyInfo() {
        $policy = $this->getPolicyInfoFromDb();
        $this->setPolicyName($policy[0]["policyName"]);
        $this->setPolicyDescription($policy[0]["policyDescription"]);

        $termsList = array();
        try {
            $policy_terms = $this->getPolicyTermInfoFromDb();
            foreach($policy_terms as $k => $v){
                $policy = new Policy();
                $policy->setPolicyTermName($v["name"]);
                $policy->setPolicyTermDescription($v["description"]);
                array_push($termsList, $policy);
            }
        } catch (Exception $e) {
            $this->err->addError("Policy.php", $e, "setPolicyInfo", $this->wpdb);
        }
        
        return $termsList;
    }
    
}
?>
';
$policy_model = fopen(__DIR__.'\model\company\Policy.php', "w");
fwrite($policy_model, $policy_model_data);

$generic_policy_data = '
<div class="pt3 lf1">
    <div class="w100 flx flxdc aic">
        <h1 class="tc"><?php echo $policy->getPolicyName();?></h1>
        <div class="w80">
            <div><?php echo $policy->getPolicyDescription();?></div>
            <br/>
            <?php foreach($termsList as $k=> $v) { ?>
                <h3><?php echo $v->getPolicyTermName();?></h3>
                <div><?php echo $v->getPolicyTermDescription(); ?></div>
                <br/>
            <?php } ?>
        </div>
    </div>
</div>
';
$generic_policy = fopen(__DIR__.'\view\PolicyView.php', "w");
fwrite($generic_policy, $generic_policy_data);

$generic_policy_controller_data = '
<?php include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
$factory = new Factory();
$policy = $factory->createPolicy();
$policy->setPolicyId($policyId);
$termsList = $policy->setPolicyInfo();
?>
';
$generic_policy_controller = fopen(__DIR__.'\controller\PolicyController.php', "w");
fwrite($generic_policy_controller, $generic_policy_controller_data);

$generic_factory_data = '
/*add section to the top of the Factory.php*/
include_once get_theme_file_path("model/company/Policy.php"); 

/*add section into the Factory class*/
public static function createPolicy(){
    return new Policy();
}

';
$generic_factory = fopen(__DIR__.'\model\commonFunctions\Factory.php', "a");
fwrite($generic_factory, $generic_factory_data);
?>
