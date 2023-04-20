<?php

$company = "Test Company Inc.";

$three_column_footer_no_cache_data = '
<?php include_once get_theme_file_path(\'controller/FooterControllerDb.php\');  ?>
<footer class="py-5">
        <div class="row px-5">
            <div class="col-6 col-md-4 mb-3">
                <h5>Contact us:</h5>
                <ul class="nav flex-column">
                <?php foreach ($companyInfo as $k => $v) { 
                        if ($v->getType() == 1) { //type 1 = contact?>
                            <li class="nav-item mb-2 d-flex">
                                <div class="bx30 <?php echo $v->getImg()?> cnnr"></div>
                                <a href="<?php echo $v->getLink()?>" class="nav-link p-0 text-muted px-2" target="_blank"><?php echo $v->getValue()?></a>
                            </li>
                            <br>
                    <? }} ?>
                </ul>
            </div>
            <div class="col-6 col-md-4 mb-3">
                <h5>Explore:</h5>
                <ul class="nav flex-column">
                <?php foreach ($companyInfo as $k => $v) { 
                        if ($v->getType() == 0) { //type 1 = contact?>
                            <li class="nav-item mb-2 d-flex">
                                <div class="bx30 <?php echo $v->getImg()?> cnnr"></div>
                                <a href="<?php echo $v->getLink()?>" class="nav-link p-0 text-muted px-2" target="_blank"><?php echo $v->getValue()?></a>
                            </li>
                            <br>
                    <? }} ?>
                </ul>
            </div>
            <div class="col-6 col-md-4 mb-3">
                <h5>Social media:</h5>
                <ul class="nav flex-column">
                <?php foreach ($companyInfo as $k => $v) { 
                        if ($v->getType() == 2) { //type 1 = contact?>
                            <li class="nav-item mb-2 d-flex">
                                <div class="bx30 <?php echo $v->getImg()?> cnnr"></div>
                                <a href="<?php echo $v->getLink()?>" class="nav-link p-0 text-muted px-2" target="_blank"><?php echo $v->getValue()?></a>
                            </li>
                            <br>
                    <? }} ?>
                </ul>
            </div>
        </div>
        <div class="d-flex flex-column flex-sm-row py-4 px-5 border-top">
            <p>© 2022 <?php echo $companyInfo[0]->getName()?> All rights reserved.</p>
        </div>  
</footer>
<?php wp_footer(); ?>
</body>
</html>
';
$three_column_footer_view = fopen(__DIR__.'\view\ThreeColumnFooterBootstrap.php', "w");
fwrite($three_column_footer_view, $three_column_footer_no_cache_data);

$three_column_footer_no_cache_function_data = '
<?php //three column footer no caching
function fetch_fwc(){add_shortcode(\'display_footer\', \'fetch_footer_no_caching\');} 
add_action(\'init\', \'fetch_fwc\');
function fetch_footer_no_caching() {
    ob_start(); 
    include_once get_theme_file_path(\'view/ThreeColumnFooterBootstrap.php\'); 
    return ob_get_clean();
}
?>
';
$three_column_footer_function = fopen(__DIR__.'\functions.php', "a");
fwrite($three_column_footer_function, $three_column_footer_no_cache_function_data);

$three_column_footer_no_cache_controller_data = '
<?php 
    include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
    $factory = new Factory();
    $company = $factory->createCompany();
    $companyInfo = $company->setCompanyInfo();
?>
';
$three_column_footer_controller = fopen(__DIR__.'\controller\FooterControllerDb.php', "w");
fwrite($three_column_footer_controller, $three_column_footer_no_cache_controller_data);


?>
