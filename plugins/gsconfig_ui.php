<?php
/*
Plugin Name: GSconfig UI
Description: UI shim for gsconfig.php. Tweak config settings directly from GS CMS.
             Requires GS Custom Settings 0.4+
Version: 0.2
Author: Kevin Van Lierde
Author URI: http://webketje.com
*/

$gsconfig_ui_file = basename(__FILE__, '.php');
i18n_merge($gsconfig_ui_file) || i18n_merge($gsconfig_ui_file, 'en_US');

register_plugin($gsconfig_ui_file,      # ID of plugin, should be filename minus php
  i18n_r('gsconfig_ui/PLUGIN_NAME'),    # Title of plugin
  '0.2.2',                              # Version of plugin
  'Kevin Van Lierde',                   # Author of plugin
  'http://webketje.com',                # Author URL
  i18n_r('gsconfig_ui/PLUGIN_DESCR'),   # Plugin Description
  'plugins'                             # Page type of plugin
);

// provide a way for other themes/ plugins to check 
// whether GSconfig UI is active and what version
define('GSCONFIG_UI', '0.2.2');

// hooks
add_action('custom-settings-load', 'gsconfig_ui_load');
add_action('custom-settings-save', 'gsconfig_ui_update');
add_action('custom-settings-render-bottom', 'custom_settings_render', array('gsconfig_ui', 'gsconfig_ui_output'));
add_action('successful-login-start', 'gsconfig_ui_setpwd');

// TODO: JS inside this function needs to be moved to a separate file, only retain PHP dynamic settings
function gsconfig_ui_output() 
{ $gs_is_wide = return_setting('gsconfig_ui', 'gs_style') > 1 ? true : false; ?>
	<style>
		#custom-toolbar-builder { transition: .5s height; width: 100%; max-width: 700px; margin-top: 20px; position: relative; left: -5000px; border: 1px solid #ccc; height: 0px; }
		#custom-toolbar-builder-toggle { position: absolute; margin-left: <?php echo $gs_is_wide ? '10' : '5'; ?>px; margin-top: <?php echo $gs_is_wide ? '4' : '14'; ?>px;}
		#toolbar-gen { padding-left: 18px; <?php echo $gs_is_wide ? '' : 'margin-left: 0px; margin-top: 10px; display: block;'; ?> }
		.manage .setting-descr code.gsconfig-constant, .ko-setting-descr code.gsconfig-constant { font-style: normal; background: none; padding: 0; }
		.manage .setting-descr, .ko-setting-descr { width: initial !important; } .ko-setting-descr { float: none !important; }
		.manage .setting div pre.ko-code, .ko-list-item input.ko-code, .ko-list-item .ko-3-5.ko-float-left span.ko-1-3 { display: none !important; }
		.manage .setting .button, .ko-list-item .button { padding: 1px 3px; margin-left: 5px; }
	</style>
	<i id="custom-toolbar-builder-toggle" class="fa fa-plus"></i>
	<input id="toolbar-gen" type="button" class="button" value="<?php i18n('gsconfig_ui/CUSTOM_TOOLBAR'); ?>" onclick="gsconfigUI.toggleToolbarGen(true, this)">
	<iframe id="custom-toolbar-builder" src="http://rawgit.com/webketje/8c949d57beffe097a770/raw/cd99027f3cd9038fae9e11f62489e0cab662d458/index.html"></iframe>
	
	<script type="text/javascript">
		var gsconfigUI = {};
		gsconfigUI.toggleToolbarGen = function(state, elem) { 
			var d = document.getElementById('custom-toolbar-builder'), 
					s = document.getElementById('custom-toolbar-builder-toggle');
			if (elem && elem.id === 'toolbar-gen')
				state = s.className === 'fa fa-minus' ? false : true;
			if (state) {
				s.className =  'fa fa-minus';
				d.style.cssText = 'border-width: 1px; height: <?php echo $gs_is_wide ? '245px' : '290px'; ?>; left: 0px;';
			} else {
				s.className = 'fa fa-plus'; 
				d.style.cssText = 'border-width: 0px;';
			}
			if (elem.nodeName === 'A')
				d.scrollIntoView();
		};
		gsconfigUI.addMissingResetButton = function() {
			var tab = ko.utils.arrayFirst(GSCS.data.items(), function(item) { item.lookup() === 'gsconfig_ui' });
			if (tab && !tab.enableReset) tab.enableReset = true;
		};
		gsconfigUI.generateSalt = function(target) {
			var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz !-^$=:|#*%~+?",
				saltgen = document.getElementById('salt-generator-output'),
				strlen = 55, randomstring = '';
			for (var i=0; i<strlen; i++) {
				var rnum = Math.floor(Math.random() * chars.length);
				randomstring += chars.substring(rnum, rnum+1);
			}
			GSCS.returnSetting('gsconfig_ui', target).value(randomstring);
		}
		gsconfigUI.appendConstants = function(value) {
			if (!value || GSCS.data.items()[value].lookup() === 'gsconfig_ui') {
				var allTabs = GSCS.data.items(), 
						settings = ko.utils.arrayFirst(allTabs, function(tab) { return tab.lookup() === 'gsconfig_ui' }).settings.items(),
						settingSelector = $('.setting').length ? '.setting' : '.ko-list-item';
				$(settingSelector).each(function() {
					var index = $(this).index(),
						  constant = settings[index].constant;
					if (settings[index].type() !== 'section-title') {
						var descr = $(this).find('p');
						if (constant !== 'GSEDITORTOOLCUSTOM') {
							if (descr.text().length) 
								descr.prepend('<code class="gsconfig-constant">(' + constant + ')</code> - ');
							else
								descr.prepend('<code class="gsconfig-constant">(' + constant + ')</code>');
							if (/GS.*SALT/.test(constant)) {
								descr.append('<input type="button" class="button" onclick="gsconfigUI.generateSalt(\'' + settings[index].lookup() + '\')" value="<?php i18n('gsconfig_ui/GEN_SALT'); ?>">');
							}
						} else {
							<?php echo !$gs_is_wide ? 'descr.append(\'<br>\');' : ''; ?>
							descr = descr[0];
							descr.appendChild(document.getElementById('custom-toolbar-builder-toggle'))
							descr.appendChild(document.getElementById('toolbar-gen'));
							descr.appendChild(document.createElement('br'));
							$(this).children().last().before(document.getElementById('custom-toolbar-builder'));
						}
					}
				});
			}
		};	
		gsconfigUI.adminDirChange = function(value) {
      document.getElementById('custom-settings-save-btn').onclick = function() {
        if (!(GLOBAL.ADMINDIR === 'admin' && (value === '' || value == 'admin'))) {
          setTimeout(function() {
            location.href = location.href.replace(GLOBAL.ADMINDIR, value.trim() ? value.trim() : 'admin');
          }, 3000);
        }
      }
    };
    gsconfigUI.init = function() {
      if (GSCS.data.items()[GSCS.data.activeItem()].lookup() === 'gsconfig_ui') {
				gsconfigUI.addMissingResetButton();
        gsconfigUI.appendConstants();
        gsconfigUI.adminDirChange();
      }
      GSCS.returnSetting('gsconfig_ui', 'gs_admin').value.subscribe(gsconfigUI.adminDirChange);
      GSCS.data.activeItem.subscribe(gsconfigUI.appendConstants);
			GSCS.data.activeItem.subscribe(gsconfigUI.addMissingResetButton);
    };
		
		addHook(gsconfigUI.init);
	</script>
	<?php 
}

// executed in hook 'successful-login-start', 
// automatically hashes the password with the last set GSLOGINSALT,
// thereby avoiding trouble with re-setting cookies 
function gsconfig_ui_setpwd() 
{
	global $password, $user_xml;
	if (file_exists($user_xml)) {
		$userFile = getXML($user_xml);
		$oldSalt = return_setting('gsconfig_ui', 'gs_login_salt_old');
		$comp = $oldSalt ? sha1($password . sha1($oldSalt)) : sha1($password);
		if ((string)$userFile->PWD === $comp) {
			$userFile->PWD = passhash($password);
			XMLsave($datau, GSUSERSPATH . strtolower($userFile->USR) . '.xml');
		}
	}
}

// on settings load, 
// 1) globalize $gsconfig for other plugins to make use of in format $gsconfig['CONSTANT']
// 2) flatten gsconfig settings to a temp file which is later retrieved for comparison in gsconfig_ui_update
// need to update this flat file to JSON for default setting retrieval
function gsconfig_ui_load() 
{
	global $gsconfig, $custom_settings;
	$settings = $gsconfig = array();
	$temp = return_setting_group('gsconfig_ui', 'gs', false);
	foreach ($temp as $l=>$s) {
		if ($s['type'] !== 'section-title')	{
			$settings[$s['constant']] = array_merge($s, array('lookup', $l));
			$gsconfig[$s['constant']] = $s['value'] === $s['default'] ? null : $s['value']; }
	}
	$settings = array_reduce($settings, 'gsconfig_ui_flatten_setting');
	file_put_contents(GSPLUGINPATH . 'gsconfig_ui/temp_data.txt', $settings);
	exec_action('gsconfig-load');
	
	$prevSalt = return_setting('gsconfig_ui', 'gs_login_salt_prev');
	if ($prevSalt !== getDef('GSLOGINSALT')) {
		$prevSalt = getDef('GSLOGINSALT');
		set_setting('gsconfig_ui', 'gs_login_salt_prev', $prevSalt);			
		customSettings::saveAllSettings($custom_settings);
	}
}

// before settings save, save to gsconfig.php
function gsconfig_ui_update() 
{
	global $gsconfig_ui_settings_presave;
	
	$ss = return_setting_group('gsconfig_ui', 'gs', false);
	$gsconfig_ui_settings_presave = array();
	$tempDataPath = GSPLUGINPATH . 'gsconfig_ui/temp_data.txt';
	foreach ($ss as $l => $s) {
		if ($s['type'] !== 'section-title')	$gsconfig_ui_settings_presave[$s['constant']] = array_merge($s, array('lookup', $l));
	}
	if (file_exists($tempDataPath))
		$comp_load = file_get_contents(GSPLUGINPATH . 'gsconfig_ui/temp_data.txt');
	$comp_save = array_reduce($gsconfig_ui_settings_presave, 'gsconfig_ui_flatten_setting');
	if (!isset($comp_load) || $comp_load !== $comp_save) {
		$rgx = '~(#* *?)(define\()(.*?),(.*)(\);)~';
		$gssf = false;
		$path = GSROOTPATH . 'gsconfig.php';
		$f = file_get_contents($path);
		$output = preg_replace_callback($rgx, 'gsconfig_ui_iterate', $f);
		$locrep = $ss['php_locale'];
		$output = preg_replace('~#* *setlocale.*?\);~', ($locrep['value'] ? 'setlocale(LC_ALL, \''. $locrep['value'] . '\');' : '#setlocale(LC_ALL, \'en_US\');'), $output);
		file_put_contents($path, $output);
	}
	if (file_exists($tempDataPath))
		unlink(GSPLUGINPATH . 'gsconfig_ui/temp_data.txt');
}

function gsconfig_ui_flatten_setting($carry, $key) 
{ 
	return $carry .= ' ' . $key['value']; 
}

// TODO: not very clean
function gsconfig_ui_iterate($match) 
{
	// $r = result, $gsconfig_ui_settings_presavei = dictionary, $m = matches, $l = lookup, $s = setting
	global $gsconfig_ui_settings_presave,  $gssf, $USR;
	$m = array(
		'full'  => $match[0],
		'hash'  => $match[1],
		'def'   => $match[2],
		'const' => $match[3],
		'val'   => str_replace('\'', '', $match[4]),
		'end'   => $match[5]
	);
	$r = $m['full'];
	$l = str_replace('\'', '', $m['const']);
	if (isset($gsconfig_ui_settings_presave[$l]) && $gsconfig_ui_settings_presave[$l]['value'] !== $m['val']) {
		$s = $gsconfig_ui_settings_presave[$l];
		switch ($s['lookup']) {
			// first batch of settings are inverted 
			// (eg default is true in gsconfig => false in settings)
			// commented out if default (with true so users can still uncomment manually)
			// $s['default'] holds the default the UI, not gsconfig.php
			// set to true if not
		  case 'gs_cdn': 
		  case 'gs_csrf':
			case 'gs_highlight':
			case 'gs_apache_check':
			case 'gs_ver_check':
			case 'gs_sitemap': 
			case 'gs_uploadify':
			case 'gs_canonical':
			case 'gs_auto_meta_descr':
				$r = $m['def'] . $m['const'] . ',true' . $m['end'];
				if ($s['value'] === $s['default'])	$r = '#' . $r;
				break;
			// only checkbox setting where false in GS  = false in the UI
			case 'gs_debug':
				$r = $m['def'] . $m['const'] . ', true' . $m['end'];
				if (!$s['value']) $r = '#' . $r;
				break;
			// only radio option
			case 'gs_editor_toolbar':
				switch ($s['value']) {
					case 0:
						$r = $m['def'] . $m['const'] . ',\'[]\'' . $m['end'];
						break;
					case 1: 
						$r = '#' . $m['def'] . $m['const'] . ',\'advanced\'' . $m['end'];
						break;
					case 2:
						$r = $m['def'] . $m['const'] . ',\'advanced\'' . $m['end'];
						break;
					case 3: 
						$r = $m['def'] . $m['const'] . ',\'[["Source","Save","NewPage","DocProps","Preview","Print","Templates"], ["Cut","Copy","Paste","PasteText","PasteFromWord","Undo","Redo"], ["Find","Replace","SelectAll","SpellChecker","Scayt"], ["Form","Checkbox","Radio","TextField","Textarea","Select","Button","ImageButton","HiddenField"], ["Bold","Italic","Underline","Strike","Subscript","Superscript","RemoveFormat"], ["NumberedList","BulletedList","Outdent","Indent","Blockquote","CreateDiv","JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock","BidiLtr","BidiRtl"], ["Link","Unlink","Anchor"], ["Image","Flash","Table","HorizontalRule","Smiley","SpecialChar","PageBreak","Iframe"], ["Styles","Format","Font","FontSize"], ["TextColor","BGColor"], ["Maximize","ShowBlocks","About"]]\'' . $m['end'];
						break;
					case 4:
						$r = $m['def'] . $m['const'] . ',\'' . return_setting('gsconfig_ui','gs_editor_toolbar_custom') . '\'' . $m['end'];
						break;
				};
				break;
			case 'gs_editor_lang':
				$r = $m['def'] . $m['const'] . ',\'' . $s['options'][$s['value']] . '\'' . $m['end'];
				if ($s['value'] === $s['default']) $r = '#' . $r;
				break;
			case 'gs_editor_height':
				$r = $m['def'] . $m['const'] . ',\'' . $s['value'] . '\'' . $m['end'];
				if ($s['value'] === $s['default']) $r = '#' . $r;
				break;
			case 'gs_chmod':
			case 'gs_autosave':
				$r = (!$s['value'] ? '#' : '') . $m['def'] . $m['const'] . ',' . $s['value'] . $m['end'];
				break;
		  // text settings commented out by default
			case 'gs_admin': 
        rename(GSADMINPATH, GSROOTPATH . $s['value'] .'/');
        $r = (!$s['value'] ? '#' : '') . $m['def'] . $m['const'] . ',\'' . $s['value'] . '\'' . $m['end'];
        break;
      case 'gs_custom_salt':
        require_once(GSADMININCPATH . 'configuration.php');
        global $SALT, $cookie_time, $cookie_name;
        $SALT = sha1($s['value']);
        kill_cookie($cookie_name);
        create_cookie();
      case 'gs_login_salt':
      case 'gs_editor_options':
      case 'gs_timezone':
      case 'gs_from_email':
        $r = (!$s['value'] ? '#' : '') . $m['def'] . $m['const'] . ',\'' . $s['value'] . '\'' . $m['end'];
        break;
		  // following 3 are not commented out by default
			case 'gs_suppress_errors':
				$r = ($s['value'] ? '' : '#') . $m['def'] . $m['const'] . ',' . ($s['value'] ? 'true' : 'false') . $m['end'];
				break;
			case 'gs_ping':
				$r = ($s['value'] ? '#' : '') . $m['def'] . $m['const'] . ',' . $s['default'] . $m['end'];
				break;
			case 'gs_image_width':
				$r = $m['def'] . $m['const'] . ',\'' . (!$s['value'] ? '200' : $s['value']) . '\'' . $m['end'];
				break;
			case 'gs_editor_height':
				$r = ($s['value'] ? '#' : '') . $m['def'] . $m['const'] . ',\'' . $s['value'] . '\'' . $m['end'];
				break;
			case 'gs_merge_lang': 
				$r = (!$s['value'] || $s['value'] === 'en_US' ? '#' : '') . $m['def'] . $m['const'] . ',\'' . $s['value'] . '\'' . $m['end'];
				break;
			case 'gs_style': 
				$options = array('', 'GSSTYLE_SBFIXED', 'implode(\',\',array(GSSTYLEWIDE,GSSTYLE_SBFIXED))','GSSTYLEWIDE');
				if (!$gssf) {
					$r = ($s['value'] === 0 ? '#' : '') . $m['def'] . $m['const'] . ',' . $options[$s['value']] . $m['end'];
					$gssf = true;
					if (function_exists('delete_cache')) 
						delete_cache();
				}
				break;
			default: 
				$r = $m['full'];
		}
	}
	return $r;
}