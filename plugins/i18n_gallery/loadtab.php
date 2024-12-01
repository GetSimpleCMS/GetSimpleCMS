<?php 
/****************************************************
*
* @File: 		load.php
* @Package:	GetSimple
* @Action:	Displays the plugin file passed to it 	
*
*****************************************************/


// Setup inclusions
$load['plugin'] = true;

// Relative
$relative = '../';

// Include common.php
include('inc/common.php');

// Variable Settings
login_cookie_check();

// Setup this plugin
$plugin_id = $_GET['id'];
global $plugin_info;



?>

<?php get_template('header', cl($SITENAME).' &raquo; '. $plugin_info[$plugin_id]['name']); ?>
	
	<h1><a href="<?php echo $SITEURL; ?>" target="_blank" ><?php echo cl($SITENAME); ?></a> <span>&raquo;</span> <?php echo $plugin_info[$plugin_id]['name']; ?></h1>

		<?php include('template/include-nav.php'); ?>


	<?php include('template/error_checking.php'); ?>

<div class="bodycontent">
	
	<div id="maincontent">
		<div class="main">

		<?php 
			//print_r($plugin_info[$plugin_id]);
			if ($plugin_id == @$_GET['item'])
			{
				call_user_func_array($plugin_info[$plugin_id]['load_data'],array());
			}
			else if (isset($_GET['item']))
			{
				call_user_func_array($_GET['item'],array());
			}
		?>

		</div>
	</div>
	
	<div id="sidebar" >
		<ul class="snav">
			<?php exec_action($plugin_id."-sidebar"); ?>
		</ul>
		<?php exec_action($plugin_id."-sidebar-extra"); ?>
	</div>	
	
	<div class="clear"></div>
</div>
<?php get_template('footer'); ?>
