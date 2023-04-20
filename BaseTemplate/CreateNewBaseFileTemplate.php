<?php

$business_dir = __DIR__.'\model\business';
if (!file_exists($business_dir)) {
    mkdir($business_dir, 0777, true);
}

$business_dir = __DIR__.'\view\policy\business';
if (!file_exists($business_dir)) {
    mkdir($business_dir, 0777, true);
}

$ajax_ncnda_data = '

';
$ajax_ncnda = fopen(__DIR__.'\ajax\AjaxCalls.php', "a");
fwrite($ajax_ncnda, $ajax_ncnda_data);

$businesses_controller_data = '

';
$businesses_controller = fopen(__DIR__.'\controller\BusinessesController.php', "w");
fwrite($businesses_controller, $businesses_controller_data);

$specific_businesses_controller_data = '

';
$specific_businesses_controller = fopen(__DIR__.'\controller\SpecificBusinessController.php', "w");
fwrite($specific_businesses_controller, $specific_businesses_controller_data);

$js_NcndaForm_data = '

';
$js_NcndaForm = fopen(__DIR__.'\js\NcndaForm.js', "w");
fwrite($js_NcndaForm, $js_NcndaForm_data);

$js_Ncnda_view_data = '

';
$js_Ncnda_view = fopen(__DIR__.'\js\NcndaView.js', "w");
fwrite($js_Ncnda_view, $js_Ncnda_view_data);

$factory_data = '

';
$factory = fopen(__DIR__.'\model\commonFunctions\Factory.php', "a");
fwrite($factory, $factory_data);

$business_contact_model_data = '

';
$business_contact_model = fopen(__DIR__.'\model\business\BusinessContact.php', "w");
fwrite($business_contact_model, $business_contact_model_data);

$businesses_model_data = '

';
$businesses_model = fopen(__DIR__.'\model\business\Businesses.php', "w");
fwrite($businesses_model, $businesses_model_data);

$country_model_data = '

';
$country_model = fopen(__DIR__.'\model\country\Country.php', "a");
fwrite($country_model, $country_model_data);

$ncnda_form_data = '

';
$ncnda_form = fopen(__DIR__.'\view\policy\NcndaForm.php', "w");
fwrite($ncnda_form, $ncnda_form_data);

$ncnda_initial_question_data = '

';
$ncnda_initial_question = fopen(__DIR__.'\view\policy\NcndaInitialQuestion.php', "w");
fwrite($ncnda_initial_question, $ncnda_initial_question_data);

$ncnda_initial_question_data = '

';
$ncnda_initial_question = fopen(__DIR__.'\view\policy\NcndaInitialQuestion.php', "w");
fwrite($ncnda_initial_question, $ncnda_initial_question_data);

?>
