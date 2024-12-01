/**
 * Setting-list module (manage mode)
 * @module modules/settings-manage
 */
 
 define(['lib/knockout', 'modules/globals'], function(ko, globals) {
  
  // import globals & define component parameters
  var notify = globals.notifier
    , cName  = 'settinglist-manage'
    , cTmpl  = 'settings-manage.html';

  /** @constructor SettingList
   *  @alias modules/settings-manage
   */
  function SettingList(params) {
    this.tab = params.tab.tab;
    this.items = params.tab.settings;
  }
  
  /* ------  setting helper methods ------ */
  
  /** @method SettingList.codeTitleForSetting
   *  @descr Helper for generating the title attribute of a setting's code icon
   */
  SettingList.prototype.codeTitleForSetting = function(setting) {
    return this.tab.data.lookup() + '/' + setting.data.lookup();
  }
  
  /** @method SettingList.showSettingCode
   *  @descr Helper for generating the content filter code opened through a setting's code icon
   */
  SettingList.prototype.showSettingCode = function(setting, e) {
    var msg = this.tab.data.lookup() + '/' + setting.data.lookup()
      , html = '<span style="font-family: Consolas, monospace;">' + msg + '<span>' + '<input type="text" id="cs-code-copy" value="(% setting: ' + msg + ' %)">';;
      
    notify(html, 'notify', null, 'long');
    document.getElementById('notification-manager').getElementsByTagName('input')[0].select();
  }
  
  /* ------  component registration ------ */
  
  ko.components.register(cName, {
    viewModel: SettingList,
    template: {require: 'text!templates/' + cTmpl }
  });
}); 
