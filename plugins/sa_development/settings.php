<?php

GLOBAL $sa_dev_settings;

$sa_dev_settings = array(
	'enableb'    => '1',
	'enablef'    => '0',
	'enablefopt' => '0',
	'collapsed'  => '0',
	'theme'      => 'default'
);


_debugLog('defaults',$sa_dev_settings);

if(isset($_POST['sa_dev_settings'])){
	$input = $_POST['sa_dev_settings'];
	
	foreach($sa_dev_settings as $key => &$setting){
		if( isset($input[$key]) ) $setting = $input[$key];
	}

	_debugLog('input',$sa_dev_settings);
	//@todo: save xml
} else {
	//@todo: load xml
}


function sa_dev_isChecked($var,$value){
	echo $test;
	return sa_dev_getToggle($var,$value,'checked');
}

function sa_dev_isSelected($var,$value){
	return sa_dev_getToggle($var,$value,'selected');
}

function sa_dev_getToggle($var,$value,$toggle){
	GLOBAL $sa_dev_settings;
	_debugLog($sa_dev_settings); // gettoggle
	_debugLog($var,$value,$toggle);
	echo $sa_dev_settings[$var] == $value ? $toggle : '';
}


_debugLog();

?>

<div id="sa_dev_settings" class="sa_dev section">
	<h3>SA Development</h3>
	<div class="leftsec">
		
		<!-- For some reason this aligns perfectly with the checkbox, the inline classed one does not, is 1px below floor
		<div>
			<input name='sa_dev_settings[]' id='sa_dev_enableb' type="checkbox" style='float:left;margin-right: 8px;'>
			<label for='sa_dev_enableb'>Enable on Back</label>
		</div>
			-->		
					
		<p class="inline">
			<input name="sa_dev_settings[enableb]" id="sa_dev_enableb" type="checkbox" value="1" style="margin-right: 5px;" <?php sa_dev_isChecked('enableb','1'); ?> >
			<label for="sa_dev_enableb">Enable on Back</label>
			<br/>
			
			<input name="sa_dev_settings[enablef]" id="sa_dev_enablef" type="checkbox" value="1" style="margin-right: 5px;" <?php sa_dev_isChecked('enablef','1'); ?> >
			<label for="sa_dev_enablef">Enable on Front</label>
			<span style="display:block;margin-left:25px;color:dimgray;">
				<input type="radio" name="sa_dev_settings[enablefopt]" value="0" <?php sa_dev_isChecked('enablefopt','0'); ?> > Logged in only<br>
				<input type="radio" name="sa_dev_settings[enablefopt]" value="1" <?php sa_dev_isChecked('enablefopt','1'); ?> > Always!<br>	    
			</span>
		</p>
	</div>
	<div class="rightsec divider">
		<p>		
			<label for="sa_dev_theme">Theme</label>
			<select id="sa_dev_theme" name="sa_dev_settings[theme]" class="text" style="width:150px">
				<option value="default" <?php sa_dev_isSelected('theme','default'); ?> >Default</option>
				<option value="monokai" <?php sa_dev_isSelected('theme','monokai'); ?> >Monokai</option>
			</select>
		</p>
		<p class="inline">
			<input name="sa_dev_settings[collapsed]" id="sa_dev_collapsed" type="checkbox" value="1" style="margin-right: 5px;" <?php sa_dev_isChecked('collapsed','1'); ?> >
			<label for="sa_dev_collpased">Start Collapsed</label>
		</p>
	</div>

<div class="clear"></div>
</div>