<?php 
/**
 * Load Plugin
 *
 * Displays the plugin file passed to it 
 *
 * @package GetSimple
 * @subpackage Plugins
 */


# Setup inclusions
$load['plugin'] = true;
include('inc/common.php');
login_cookie_check();

global $plugin_info;

# verify a plugin was passed to this page
if (empty($_GET['id']) || !isset($plugin_info[$_GET['id']])) {
	redirect('plugins.php');
}

# include the plugin
$plugin_id = $_GET['id'];

if(!isset($pagetitle)) $pagetitle = $plugin_info[$plugin_id]['name'];
get_template('header');

?>
	
<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
	
	<div id="maincontent">
		<div class="main">
		<?php 
			// callback
			call_gs_func_array($plugin_info[$plugin_id]['load_data']);
		?>
		</div>
	</div>
	
	<div id="sidebar" >
    <?php 
      $res = (@include('template/sidebar-'.$plugin_info[$plugin_id]['page_type'].'.php'));
      if (!$res) { 
    ?>
      <ul class="snav">
        <?php exec_action($plugin_info[$plugin_id]['page_type']."-sidebar"); ?>
      </ul>
    <?php
	}
	// call sidebar extra hook for plugin page_type
	exec_action($plugin_info[$plugin_id]['page_type']."-sidebar-extra");     
    ?>
  </div>

</div>
<?php get_template('footer'); ?>