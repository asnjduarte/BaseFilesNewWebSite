<?php

$three_column_footer_with_cache_data = '

<?php include get_theme_file_path(\'controller/FooterWithCachingController.php\');  ?>
<footer class="site-footer">
    <div class="site-footer__inner">
        <div class="flx mbFlxdc">
            <div class="flx1">
                <h3 class="w80"><a href="/?>"><strong><?php 
                echo $cacheCompanyInfo->get()["Nombre"];?></strong></a></h3>
                <?php  foreach ($cacheCompanyInfo->get()["Links"] as $k => $v) { 
                    if ($v[3] == 1) { //type 1 = contact?>
                        <div class="flx">
                            <div class="bx30 <?php echo $v[4]?> cnnr"></div>
                            <a class="ml5 mt5" href="<?php echo $v[2]?>"><?php echo $v[1]?></a><br>
                        </div>
                <? }} ?>
            </div>
            <div class="flx1">
                <h3>Explorar</h3>
                <nav>
                    <ul>
                        <?php  foreach ($cacheCompanyInfo->get()["Links"] as $k => $v) { 
                            if ($v[3] == 0) { //type 0 = miscellaneous?>
                                <li><a href="<?php echo $v[2]?>" target="_blank"><?php echo $v[1]?></a></li>
                        <? }} ?>
                    </ul>
                </nav>
            </div>
            <div class="flx1">
                <h3>Conectar con nosotros</h3>
                <nav>
                    <ul class="social-icons-list group">
                        <?php foreach ($cacheCompanyInfo->get()["Links"] as $k => $v) { 
                            if ($v[3] == 2) { //type 2 = contact?>
                                <li>
                                    <a href="<?php echo $v[2]?>" target="_blank">
                                        <div class="bx30 <?php echo $v[4]?> cnnr"></div>
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
$three_column_footer_view = fopen(__DIR__.'\view\ThreeColumnFooterWithCaching.php', "w");
fwrite($three_column_footer_view, $three_column_footer_with_cache_data);

$three_column_footer_with_cache_function_data = '
<?php //three column footer with caching
function fetch_fwc(){add_shortcode(\'display_footer\', \'fetch_footer_with_caching\');} 
add_action(\'init\', \'fetch_fwc\');
function fetch_footer_with_caching() {
    ob_start(); 
    include_once get_theme_file_path(\'view/ThreeColumnFooterWithCaching.php\'); 
    return ob_get_clean();
}
?>
';
$three_column_footer_function = fopen(__DIR__.'\functions.php', "a");
fwrite($three_column_footer_function, $three_column_footer_with_cache_function_data);

$three_column_footer_with_cache_controller_data = '

<?php 
    require_once get_theme_file_path(\'/vendor/autoload.php\');
    use \Symfony\Component\Cache\Adapter\FilesystemAdapter;

    //set app.cache file
    $cachePool = new FilesystemAdapter(\'app.cache\');
    $cacheCompanyInfo = $cachePool->getItem(\'company_info\');
    
    //check if company_info is set if not, set it
    if (!$cacheCompanyInfo->isHit())
    {
        include_once get_theme_file_path(\'model/commonFunctions/Factory.php\'); 
        $factory = new Factory();
        $company = $factory->createCompany();
        $companyInfo = $company->setCompanyInfo();
        
        $arrOfKeys = array();
        foreach ($companyInfo as $key => $val) {
            array_push($arrOfKeys, $val->getLinks()) ;
        }
        
        $companyInfoValues = array(
            "Nombre"=>$companyInfo[0]->getName(), 
            "Links"=>$arrOfKeys
        );
        $cacheCompanyInfo->expiresAfter(864000); //10 days
        $cacheCompanyInfo->set($companyInfoValues);
        $cachePool->save($cacheCompanyInfo);
    } 

    //$cachePool->clear();
?>

';
$three_column_footer_controller = fopen(__DIR__.'\controller\FooterWithCachingController.php', "w");
fwrite($three_column_footer_controller, $three_column_footer_with_cache_controller_data);

$three_column_footer_with_cache_model_data = '
//Need to add this to the Company.php class
    public function getLinks() {
        return array (
            $this->getLinkName(),
            $this->getValue(),
            $this->getLink(),
            $this->getType(),
            $this->getImg()
        );
    }
';
$three_column_footer_model = fopen(__DIR__.'\model\company\Company.php', "a");
fwrite($three_column_footer_model, $three_column_footer_with_cache_model_data);


?>
