<?php

// Get correct id for plugin
$thisfile = basename(__FILE__, '.php');

// Data save file
$file_mra_data_file = GSDATAOTHERPATH . 'ModernAdminSettings.xml';
$site_url = $SITEURL;
$plugin_folder = basename(GSPLUGINPATH);
$plugin_url = $site_url.$plugin_folder; 

# register this plugin
register_plugin(
	$thisfile, 
	'Modern Admin', 	
	'1.4', 		
	'Justin Y. - ChompDigital.com',
	'http://chompdigital.com/', 
	'This plugin converts the admin panel to a modern responisive layout for on the go edits. Plugin also includes custom syntax highlighting',
	'plugins',
    'file_mra_admin_process'
);

register_style ('modern_admin', $plugin_url.'/modern-admin/assets/css/style.css', '1.0', 'screen');
register_style ('color_picker', $plugin_url.'/modern-admin/assets/css/colorpicker.css', '1.0', 'screen');
register_style ('font_awesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css', '1.0', 'screen'); 
register_script('color_picker', $plugin_url.'/modern-admin/assets/js/colorpicker.js', '0.1', FALSE);
register_script('modern_admin1', $plugin_url.'/modern-admin/assets/js/plugin.js', '0.1', FALSE);

queue_style ('font_awesome', GSBACK );
queue_style ('color_picker', GSBACK );
queue_style ('modern_admin', GSBACK );  
queue_script('color_picker', GSBACK );
queue_script('modern_admin1', GSBACK );

add_action ( 'index-login', 'login_page_class' ); 
add_action ( 'header', 'modern_admin1' ); 
add_action ( 'header', 'file_mra_add_code' );
add_action ( 'plugins-sidebar', 'createSideMenu', array( $thisfile, 'Modern Admin Settings' ) ); 
add_action ( 'header', 'scroll_to_top' ); 

$file_mra_settings = file_mra_read_settings();

function login_page_class() { 
global $file_mra_settings;
?>
<script>
 $(document).ready(function(){
	 if (window.location.href.indexOf("/admin/index.php") > -1) {
      $("body").addClass("login"); 
      } 
	  
    });
	</script>
<style>
body.login, input[type="submit"] { background: #<?php echo $main_color = $file_mra_settings['main_color']; ?>!important }
h3 { color: #<?php echo $main_color = $file_mra_settings['main_color']; ?>!important }
</style>
 
 
<?php }  
// ADD VIEWPORT TAG AND ADD BODY CLASS FOR SYNTAX HIGHLIGHTING
add_action( 'theme-sidebar', 'createSideMenu', array( $thisfile, 'Edit Modern Admin', 'edit' ) );  
 function modern_admin1 () {  
	 echo '<meta name="viewport" content="width=device-width, initial-scale=1">'; 
	 
}  

function scroll_to_top() {
	echo '<div class="scroll_top"><a href="#" class="scrollToTop"><i class="fa fa-chevron-circle-up"></i> &nbsp;SCROLL TO TOP</a></div>';
}



function file_mra_add_code() {
    
    global $file_mra_settings; 
        
        if( strlen( $file_mra_settings['main_color'] ) > 0 ) {
            
 		$main_color = $file_mra_settings['main_color'];
		$sidebar    = $file_mra_settings['sidebar'];
		$layout     = $file_mra_settings['layout'];
		
        echo '
		<style> 
		.header, #footer, .edit-nav a:link, .edit-nav a:visited, input.submit,
		input.submit:focus, input.submit:hover, .uploadifyProgressBar,
		body#index, body#resetpassword, .CodeMirror-gutter, #metadata_window #menu-items, a.component:hover, .scroll_top a, body#components td b{
		background-color:#' . $main_color . '!important }
		a.power-off, a.settings , a.help { color: #' . $main_color . '!important }
		h3.floated, #index h3, #resetpassword h3,
		.wrapper a:link, .wrapper a:visited, #footer .footer-left a:hover { border-color:#' . $main_color . '!important }
		input.text:focus, select.text:focus, textarea.text:focus { border-color:#' . $main_color . '!important }
		@media only screen and ( min-width : 320px ) and ( max-width : 768px ) {
		body { background-color:#' . $main_color . '!important } 
		a.power-off, a.settings , a.help { color: #fff!important } 
		} 
		</style>'; 
            
        } 
		
	    if ( $file_mra_settings['sidebar'] == 'Sidebar Right' ) { 
		echo '
		<style> 	
		#sidebar { float: right !important }
		#maincontent, .updated, .error, .notify { float: left !important } 
		</style>';
		}  else  {
		echo '
		<style> 	
		#sidebar { float: left !important }
		#maincontent, .updated, .error, .notify { float: right !important } 
		</style>'; }
		
		if ( $file_mra_settings['layout'] == 'Full Width' ) { 
		echo '
		<style> 	
		.wrapper { width: 100% }
		#maincontent { width: 80% } 
		#sidebar { width: 20% }
		.wrapper .nav { left: 391px }
		.header h1 { left: 10px }
		.wrapper .nav li.rightnav, .wrapper #pill{ right: 60px }
		a.power-off, a.settings , a.help{ font-size: 4em; margin : 20px 25px }
		@media only screen and ( min-width : 320px ) and ( max-width : 768px ) 
		{ 
		.wrapper { padding: 0 }
		.wrapper .nav { left: -3px !important; font-size: 8px !important }
		#maincontent, #sidebar { width: 100%; float: none } 
		.header h1 { left: 0 }   
		.wrapper .nav li.rightnav, .wrapper #pill{ right: 0 }
		}
		</style>';
		}  
		
    echo "\n";
}



function file_mra_is_backend() {
    $path_parts = pathinfo( $_SERVER['PHP_SELF'] );
    if( function_exists( 'get_site_url' ) or $path_parts['basename'] == 'index.php' or $path_parts['basename'] == 'logout.php' ) {
        return false;
    } else {
        return true;
    }
}


function file_mra_admin_process() {

    global $file_mra_data_file;

    // Check for submitted data
    if( isset( $_POST['submit'] ) ) {

        // Save submitted data

        $file_mra_submitted_data['main_color'] = $_POST['main_color'];
		$file_mra_submitted_data['sidebar'] = $_POST['sidebar'];
		$file_mra_submitted_data['layout'] = $_POST['layout'];
        $result = file_mra_save_settings( $file_mra_submitted_data );

    }

    $file_mra_settings = file_mra_read_settings();

    echo '<h3>Modern Admin Settings</h3>';

    if( isset( $result ) ) {
        if( $result == true ) { 
            echo '<p class="updated" style="width: 100%; background: #DFF8D9; border: none; padding: 10px; color: #777">Settings saved.</p>';
        } elseif( $result == false ) { 
            echo '<p class="error">Error saving data. Check permissions.</p>';
        }
    }

    // file_mra_cookie_debug();

    ?> 
    <form method="post" class="modern_admin2" action="<?php echo $_SERVER ['REQUEST_URI']; ?>"onSubmit="window.location.reload()">

       <label>Select or enter color code</label><br><small>Example: 000</small>

       <p>
        <input id="colorpickerField1" name="main_color" class="text" value="<?php echo $file_mra_settings['main_color']; ?>" style="width: 100px;" />
       </p>

       <p>
       <label>Layout Type</label>
       <select name="layout" value="<?php echo $file_mra_settings['layout']; ?>">
         <option value="<?php echo $file_mra_settings['layout']; ?>"></option>
          <option value="Standard Layout">Standard Layout</option>
          <option value="Full Width">Full Width Layout</option>
        </select>
        </p>


       <p>
        <label>Sidebar Position</label>
        <select name="sidebar" value="<?php echo $file_mra_settings['sidebar']; ?>">
         <option value="<?php echo $file_mra_settings['sidebar']; ?>"></option>
         <option value="Sidebar Left">Sidebar Left</option>
         <option value="Sidebar Right">Sidebar Right</option>
        </select>  
       </p>


       <div style="clear:both; height: 10px"></div>
       <p>
         <input type="submit" id="submit" class="submit" value="<?php i18n('BTN_SAVESETTINGS'); ?>" name="submit" />
       </p>

    </form>

    <h3>Current color: #<?php echo $file_mra_settings['main_color']; ?>
     <span id="color-selector" style="display: inline-block; height: 20px; width: 20px; background: #<?php echo $file_mra_settings['main_color']; ?>"></span>
    </h3>
     <h3>Current layout: <?php echo $file_mra_settings['layout']; ?></h3>
     <h3>Current sidebar position: <?php echo $file_mra_settings['sidebar']; ?></h3>

<?php
} 


function file_mra_read_settings() {
    
    global $file_mra_data_file;
    
    if( file_exists( $file_mra_data_file ) ) {
        
        $data = getXML( $file_mra_data_file );
        $file_mra_settings['main_color'] = $data->main_color;
        $file_mra_settings['sidebar'] = $data->sidebar;
		$file_mra_settings['layout'] = $data->layout;
    } else {
        
        $file_mra_settings['main_color'] = null;
		$file_mra_settings['sidebar'] = null;
		$file_mra_settings['layout'] = null;
        file_mra_save_settings( $file_mra_settings );
        
    }
    
    
    
    $file_mra_settings['site_root'] = '/';
    
    return $file_mra_settings;
    
}


function file_mra_save_settings( $settings ) {
    
    global $file_mra_data_file;
    
    $xml = @new simpleXMLElement( '<modern_admin_settings></modern_admin_settings>' );
        
    $xml->addChild( 'main_color', $settings['main_color'] );
    $xml->addChild( 'sidebar', $settings['sidebar'] );
	$xml->addChild( 'layout', $settings['layout'] );
	
    return $xml->asXML( $file_mra_data_file );
    
}

?>
