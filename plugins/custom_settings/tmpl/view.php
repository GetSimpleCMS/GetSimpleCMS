<?php include(GSPLUGINPATH . 'custom_settings/css/dynamic.php'); ?>
<script> GS.hooks = {}; window.addHook = function(tab, fn) { GS.hooks[tab] = fn; GS.hooks[tab].inited = false; } </script>
<div class="edit-nav" data-bind="component: modules.nav"></div>
  <div id="cs-plugin-info" class="hidden">
    <strong>
      <span>{{ 'title' | i18n }}</span> 
      <span>{{ 'v. ' + config.pluginVer }}</span>
    </strong>
    <span class="hidden">(<a target="_blank"></a>)</span><br>
    <strong id="cs-translator">{{ config.lang }}</strong>
    <span>{{ 'about_translated_by' | i18n }}</span> <strong>{{ 'traductors' | i18n }}</strong>
    <br>
    <a href="http://webketje.com/projects/gs-custom-settings" id="cs-project">{{ 'about_docs' | i18n }}</a> 
  | <a href="http://get-simple.info/forums/showthread.php?tid=7099">{{ 'about_support_forum' | i18n }}</a> 
  | <a href="http://get-simple.info/extend/plugin/gs-custom-settings/913/">{{ 'about_plugin_page' | i18n }}</a> 
  | <a href="https://github.com/webketje/Gs-Custom-Settings">Github</a>
  </div>
<h3>{{ pageTitle }}</h3>
<div id="setting-search" data-bind="component: modules.search"></div>
<div id="cs-render-top"><?php exec_action('custom-settings-render-top'); ?></div>
{{ #if: state.tabSelection() > -1 }}
<div class="{{ 'cs-main ' + mode() }}" data-bind="component: modules.main"></div>
{{ /endif }}
<div id="cs-render-bottom"><?php exec_action('custom-settings-render-bottom'); ?></div>
<input type="button" class="submit" data-bind="click: saveData" value="<?php i18n('BTN_SAVESETTINGS'); ?>" id="cs-save"/>
<input type="hidden" id="cs-config" value="<?php echo self::getConfig(); ?>"/>
<span id="custom-buttons">
{{ #if: state.tabSelection() > -1 && data()[state.tabSelection()].tab.data.enableReset }}
<input type="button" class="submit" data-bind="click: resetValues" value="{{ window.i18n('label_reset') }}"/>
{{ /endif }}
</span>

<!-- ko component: {name: 'img-browser' } --><!-- /ko -->
<script type="text/javascript"> GS.i18n = <?php echo self::i18nMerge(); ?>; </script>
