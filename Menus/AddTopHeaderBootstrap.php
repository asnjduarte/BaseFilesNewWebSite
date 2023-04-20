<?php 

$header_func_data = '
<?php //two column header menu code
function fetch_tcm(){add_shortcode(\'display_menu\', \'fetch_two_column\');} 
add_action(\'init\', \'fetch_tcm\');
function fetch_two_column() {
    ob_start(); 
    include_once get_theme_file_path(\'view/TwoColumnHeader.php\'); 
    return ob_get_clean();
}
?>
';
$header_func = fopen(__DIR__.'\functions.php', "a");
fwrite($header_func, $header_func_data);

$header_data = '
<?php include_once get_theme_file_path(\'controller/HeaderMenuController.php\');  ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
      <?php foreach ($hmList as $k => $v) {
        if ($v->getRoleId() == 2){
            if ((0==$k%2)) {?>
            <li class="nav-item <?php if ($k==0) echo "active"?>"><a href="<?php echo $v->getLink();?>" class="nav-link" aria-label="Va a la página de <?php echo $v->getText()?>"><?php echo $v->getText()?></a></li>
      <?}}}?>
      <?php if ( is_user_logged_in() ) { 
          $current_user = wp_get_current_user(); ?> 
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo esc_html( $current_user->user_login ) ?></a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <?php $user = wp_get_current_user();$allowed_roles = array(\'editor\', \'administrator\');
                foreach ($hmList as $k=> $v) {
                  if( array_intersect($allowed_roles, $user->roles ) ) { 
                    if ($v->getRoleId() == 1) {?>
                      <a class="dropdown-item" href="<?php echo $v->getLink()?>" aria-label="Va a la página de <?php echo $v->getText()?>"><?php echo $v->getText()?></a>
              <?php }}}; ?>
              <a class="dropdown-item" href="<?php echo wp_logout_url(); ?>" aria-label="logout">Logout</a>
            </div>
          </li>
      <?php } else { ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo site_url(\'/login\') ?>">Login</a></li>
      <?php } ?>
    </ul>
  </div>
</nav>
';
$header = fopen(__DIR__.'\view\TwoColumnHeader.php', "w");
fwrite($header, $header_data);

$header_head_data = '
<div id="gBox1" class="pLoad">
<?php echo do_shortcode(\'[display_menu]\' ); ?>
<?php echo do_shortcode(\'[display_mobile_menu]\');?>
</div>
';
$header_head = fopen(__DIR__.'\header.php', "a");
fwrite($header_head, $header_head_data);

?>
