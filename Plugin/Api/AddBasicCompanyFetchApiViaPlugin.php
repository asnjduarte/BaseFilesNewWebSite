<?php
/*create folders*/
$plugin_dir = __DIR__.'\plugins';
if (!file_exists($plugin_dir)) {
    mkdir($plugin_dir, 0777, true);
}

$site_url = "http://headers.local/";
$basic_api_plugin_data = '
<?php
/**
 * Plugin name: Company API
 * description: basic company api
 * version: 1.0
 * author: nathan
 * 
 */

 //api url: ' . $site_url .'wp-json/api/company
 //simply fetch data from the databases via api route
function getCompany() {
    include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
    $factory = new Factory();
    $company = $factory->createCompany();
    $companyInfo = $company->setCompanyInfo();
    $arr = array("company_name"=>$companyInfo[0]->getName());
    echo json_encode($arr);
    }
    add_action(\'rest_api_init\', function() {
        register_rest_route(\'api\', \'company\' ,[
            \'methods\'=>\'GET\',
            \'callback\' => \'getCompany\'
        ]);
    });
?>
';
$basic_api_plugin = fopen(__DIR__.'\plugins\CompanyApi.php', "w");
fwrite($basic_api_plugin, $basic_api_plugin_data);

$get_company_data = '
<?php
$api_url = \'' . $site_url . 'wp-json/api/menu\';

// Read JSON file
$json_data = file_get_contents($api_url);

// Decode JSON data into PHP array
$response_data = json_decode($json_data);

var_dump($response_data);

//echo $response_data;
include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
$factory = new Factory();
$company = $factory->createCompany();

$company_data = "";
$company_data = $response_data->company_name;

echo $company_data;
?>
';
$get_company = fopen(__DIR__.'\view\viewCompanyApiData.php', "w");
fwrite($get_company, $get_company_data);

$get_company_function_data = '
<?php //get api test
function fetch_gad(){add_shortcode(\'display_company_api\', \'fetch_company_api\');} 
add_action(\'init\', \'fetch_gad\');
function fetch_company_api() {
    ob_start(); 
    include_once get_theme_file_path(\'view/getApiData.php\'); 
    return ob_get_clean();
}
?>
';
$get_company_function = fopen(__DIR__.'\functions.php', "a");
fwrite($get_company_function, $get_company_function_data);


?>
