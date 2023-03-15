<?php

$page_slug = "politica-de-privacidad-basico";
$page_name = "Politica de privacidad";

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
$page_slug = \''. $page_slug .'\'; // Slug of the Post
$new_page = array(
    \'post_type\'     => \'page\', 				// Post Type Slug eg: \'page\', \'post\'
    \'post_title\'    => \''.$page_name.'\',	// Title of the Content
    \'post_content\'  => \'[display_basic_privacy_policy_view]\',	// Content
    \'post_status\'   => \'publish\',			// Post Status
    \'post_author\'   => 1,					// Post Author ID
    \'post_name\'     => $page_slug			// Slug of the Post
);

if (!get_page_by_path( $page_slug, OBJECT, \'page\')) { // Check If Page Not Exits
    $new_page_id = wp_insert_post($new_page);
}

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
