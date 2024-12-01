/**
 * Manage mode Tab-list module
 * @module modules/tabs-manage
 */
 
define(['lib/knockout'], function(ko) {

  // define component parameters & css-class
  var cName          = 'tablist-manage'
    , cTmpl          = 'sidebar-manage'
    , tabActiveClass = 'current';
  
  /** @constructor
   *  @alias module/tabs-manage
   */
  function TabList(params) {
    var self = this;
    this.selection = params.state.tabSelection;
    this.items     = ko.utils.arrayMap(params.tabs(), function(item) {      
      return ko.toJS(item.tab);
    });
    // will only fire successfully on first page load (intentionally)
    this.loadTabFromHash(params.state.pageLoaded);
  }
  
  /* ------  tab helper methods  ------ */
  
  /** @method Tablist.select 
   *  @descr Selects a tab in the sidebar
   */
  TabList.prototype.select = function(data, e) {
    this.selection(ko.contextFor(e.target || e.srcElement).$index());
    return true;
  }
  
  /** @method Tablist.tabClass
   *  @descr Helper function for tab item class
   */
  TabList.prototype.tabClass = function(tabIndex) {
    return tabIndex() === this.selection() ? tabActiveClass : '';
  };
  
  /** @method Tablist.tabHash
   *  @descr Helper function for tab item anchor
   */
  TabList.prototype.tabHash = function(tab) {
    return '#' + tab.data.lookup;
  };

  /** @method Tablist.loadTabFromHash
   *  @descr Loads a tab in the view from a hash in the URL. Invoked on first page load.
   */
  TabList.prototype.loadTabFromHash = function(pageLoaded) {
    var self = this,
        l = location.href.slice(location.href.indexOf('#') + 1),
        t = ko.utils.arrayFirst(this.items, function(item) { return item.data.lookup === l; }) || false,
        index = 0;
        
    // Tab should only be loaded from URL hash (if any) on first page load.
    // Because the component gets re-instantiated every time mode is changed,
    // we need to keep track of whether the page is loaded in the stateMgr.
    if (pageLoaded())
      return;
    pageLoaded(true);
    
    if (!l.match('#') && !t) {
      if (this.items.length)
        self.selection(0);
    }
    
    ko.utils.arrayForEach(this.items, function(item, i) {
      if (t && t.data.lookup === item.data.lookup)
        self.selection(i);
    });
    
  };
  
  /* ------  component disposal  ------ */
  
  /** @method Tablist.dispose
   *  @descr Changes the selection if the active tab has a plugin/theme type.
   *  Invoked automatically on component disposal by KnockoutJS
   */
  TabList.prototype.dispose = function() {
    var items = this.items,
        selection = this.selection();
        
    // sets selection initially when no tabs are created
    if (!items.length) {
      this.selection(-1);
      return;
    }
    
    // if a plugin or theme tab is selected, select the last site tab if any
    // (plugin & theme tabs are non-editable)
    if (selection > -1 && items[selection].data.type !== 'site') {
      var siteTabs = ko.utils.arrayFilter(items, function(item) {
        return item.data.type === 'site';
      });
      this.selection(siteTabs.length ? siteTabs.length-1 : -1);
    }  
  };
  
  /* ------  component registration  ------ */
  
  ko.components.register(cName, {
    viewModel: TabList,
    template: {require: 'text!templates/' + cTmpl + '.html' }
  });
});
