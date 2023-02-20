<?php

$three_column_footer_no_cache_data = '
<?php include_once get_theme_file_path(\'controller/FooterControllerDb.php\');  ?>
<footer class="site-footer">
    <div class="site-footer__inner">
        <div class="flx mbFlxdc">
            <div class="flx1">
                <h3 class="w80"><a href="/?>"><strong><?php echo $companyInfo[0]->getName();?></strong></a></h3>
                <?php foreach ($companyInfo as $k => $v) { 
                    if ($v->getType() == 1) { //type 1 = contact?>
                        <div class="flx">
                            <div class="ftbx30 <?php echo $v->getImg()?> cnnr"></div>
                            <a class="ml5 mt5" href="<?php echo $v->getLink()?>"><?php echo $v->getValue()?></a><br>
                        </div>
                <? }} ?>
            </div>
            <div class="flx1">
                <h3>Explorar</h3>
                <nav>
                    <ul>
                        <?php foreach ($companyInfo as $k => $v) { 
                            if ($v->getType() == 0) { //type 0 = miscellaneous?>
                                <li><a href="<?php echo $v->getLink()?>" target="_blank"><?php echo $v->getValue()?></a></li>
                        <? }} ?>
                    </ul>
                </nav>
            </div>
            <div class="flx1">
                <h3>Conectar con nosotros</h3>
                <nav>
                    <ul class="social-icons-list group">
                        <?php foreach ($companyInfo as $k => $v) { 
                            if ($v->getType() == 2) { //type 2 = contact?>
                                <li>
                                    <a href="<?php echo $v->getLink()?>" target="_blank">
                                        <div class="ftbx30 <?php echo $v->getImg()?> cnnr"></div>
                                    </a>
                                </li>
                        <? }} ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
';
$three_column_footer_view = fopen(__DIR__.'\view\ThreeColumnFooterFromDbNoCache.php', "w");
fwrite($three_column_footer_view, $three_column_footer_no_cache_data);

$three_column_footer_no_cache_function_data = '
<?php //three column footer no caching
function fetch_fwc(){add_shortcode(\'display_footer\', \'fetch_footer_with_caching\');} 
add_action(\'init\', \'fetch_fwc\');
function fetch_footer_with_caching() {
    ob_start(); 
    include_once get_theme_file_path(\'view/ThreeColumnFooterFromDbNoCache.php\'); 
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
