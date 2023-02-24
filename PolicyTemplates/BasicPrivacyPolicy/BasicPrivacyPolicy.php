<?php

$basic_privacy_policy_data = '
<?php 
$policyId = 1; //basic privacy policy
include get_theme_file_path(\'controller/PolicyController.php\');  
include get_theme_file_path(\'view/PolicyView.php\');
 ?>
';
$basic_privacy_policy = fopen(__DIR__.'\view\BasicPrivacyPolicy.php', "a");
fwrite($basic_privacy_policy, $basic_privacy_policy_data);

$basic_privacy_function_data = '
<?php //generic policy view
function fetch_bppv(){add_shortcode(\'display_basic_privacy_policy_view\', \'fetch_basic_privacy_policy_view\');} 
add_action(\'init\', \'fetch_bppv\');
function fetch_basic_privacy_policy_view() {
    ob_start(); 
    include_once get_theme_file_path(\'view/BasicPrivacyPolicy.php\'); 
    return ob_get_clean();
}
?>
';
$basic_privacy_function = fopen(__DIR__.'\functions.php', "a");
fwrite($basic_privacy_function, $basic_privacy_function_data);

?>
