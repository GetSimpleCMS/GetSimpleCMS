/**
 * State manager module
 * @descr Manages application-wide state trackers (selections, flags, toggles)
 * @module modules/state
 * @exports stateMgr
 */
 
define(['lib/knockout'], function(ko) {
  
  /** State manager properties keep track of:
   *  @prop mode - application mode (edit/manage)
   *  @prop tabSelection - selected tab (number)
   *  @prop settingSelection - selected settings (array)
   *  @prop searchFilter - cross-tab/-mode search term (string)
   *  @prop pageLoaded - page's first load (for loading tab from hash)
   */
  var stateMgr = { 
      mode: ko.observable('manage')
    ,  tabSelection: ko.observable()
    ,  settingSelection: ko.observableArray()
    ,  searchFilter: ko.observable('')
    , pageLoaded: ko.observable(false)
    , imgBrowserActive: ko.observable(false)
    , imgBrowserSetting: null
  };
  
  /**
   *  @TODO need to move this to another file
   */
  stateMgr.mode.subscribe(function() {
    var container = $('.cs-main');
    container.hide();
    container.fadeIn(600);
  });
  
  return stateMgr;
});
