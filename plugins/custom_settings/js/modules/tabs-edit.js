/**
 * Edit mode Tab-list module
 * @module modules/tabs-edit
 */

define(['lib/knockout', 'models/tab', 'modules/globals'], function(ko, Tab, globals) {
  
  // define component parameters & css-class
  var tabActiveClass = 'current'
    , cName   = 'tablist-edit'
    , cTmpl   = 'sidebar-edit'
    , i18n    = globals.i18n
    , notify  = globals.notifier;

  /** @constructor
   *  @alias module/tabs-edit
   *  @prop {array} items - A filtered + mapped array of tabs omitting tab settings and non site-type tabs
   *  @prop {array} originalItems - The original list of tabs/settings to which changes are made immediately
   */
   
  function TabList(params) {
    var self = this, 
        minLimit = this.minLimit = 0, 
        maxLimit = this.maxLimit = 100;
    this.defaults = { tab: {  
        lookup: i18n('def_tab_label').toLowerCase().replace(/\s+/g, '_')
      , label: i18n('def_tab_label')
      , type: 'site'
      }
    };
    this.mode = params.state.mode;
    this.originalItems = params.tabs;
    this.selection = params.state.tabSelection;
    this.activeProp = ko.observable('label');
    this.items = ko.pureComputed(function() {
      var result, siteTabs = ko.utils.arrayFilter(params.tabs(), function(item, i) {  
        return item.tab.data.type === 'site';
      });
      result = ko.utils.arrayMap(siteTabs, function(item) {      
        return item.tab
      });
      return result;
    }).extend({enablePausing: true});
    
    // The disable object contains computed properties which determine
    // whether a sidebar toolbar button should be disabled
    this.disable = {
      del:  ko.pureComputed(function() { return !self.items().length; }),
      add:  ko.pureComputed(function() { return !self.maxLimit === self.items().length; }),
      up:   ko.pureComputed(function() { var selection = self.selection(); return !self.items().length || selection < 1 || selection === false; }),
      down: ko.pureComputed(function() { var items = self.items(); return !items.length || self.selection() === items.length-1; })
    };
  }
  
  /** @method Tablist.select 
   *  @descr Selects a tab in the sidebar
   */
  TabList.prototype.select = function(data, e) {
    var target = e.target || e.srcElement;
    // if the user doesn't click on the tab name, focus it anyway
    // do this before updating the selection so that the tab view is updated already
    if (target.nodeName !== 'INPUT')
      target.parentNode.getElementsByTagName('input')[0].focus();

    this.selection(ko.contextFor(target).$index());
    // required to make sure the URL hash is updated
    // because Knockout by default prevents default on click bindings
    return true;
  }
  
  /* ------  tab action methods (in order)  ------ */
  
  /** @method Tablist.inserBefore
   *  @descr Inserts a new tab before the selected tab.
   */
  TabList.prototype.insertBefore = function() {
    var selection = this.selection();
    this.originalItems.splice(this.selection(),0, {tab: new Tab(this.defaults), settings: ko.observableArray()});
    if (this.selection() === -1) 
      this.selection(0);
  }
  
  /** @method   Tablist.remove
   *  @descr    Removes the selected tab.
   *  @invokes  Tablist.removeInner
   */
  TabList.prototype.remove = function(data, e) { 
    var sel    = this.selection()
      , origin = this.originalItems()
      , root   = ko.contextFor(e.target).$root
      , self   = this;
    
    // we can only remove a tab if the selection is populated
    if (sel > -1) {
      // we only need to warn the user if the tab contains any settings
      if (origin[sel].settings().length)
        notify(i18n('warn_tab_remove'), 'notify', function() { self.removeInner.call(self) });
      else
        this.removeInner();
    }    
  }
  /** @method   Tablist.remove
   *  @descr    Removes the selected tab.
   */
  TabList.prototype.removeInner = function() {
    var sel    = this.selection()
      , origin = this.originalItems;
      // If the selection is > 0, there is at least 1 tab before it,
      // set the selection to the previous tab. If there are only next tabs,
      // removing the tab will cause the selection to automatically adjust because of the splice.
      if (sel > 0)
        this.selection(sel - 1);
      origin.splice(sel, 1);
      
      // if this was the last item, remove the selection altogether
      if (this.items().length === 0)
        this.selection(-1);
  }
  
  /** @method Tablist.moveUp
   *  @descr Moves the selected tab up by one
   */
  TabList.prototype.moveUp = function() {
    var sel   = this.selection()
      , temp  = this.originalItems();
      
    if (!this.disable.up()) { 
      temp.splice(sel, 0, temp.splice(sel - 1, 1)[0]);
      this.originalItems(temp);
      this.selection(sel - 1);
    }
  }
  
  /** @method Tablist.moveDown
   *  @descr Moves the selected tab down by one
   */
  TabList.prototype.moveDown = function() {
    var sel   = this.selection()
      , temp  = this.originalItems();
      
    if (!this.disable.down()) {
      this.selection(sel + 1);
      temp.splice(sel, 0, temp.splice(sel + 1,1)[0])
      this.originalItems(temp);
    }
  }
  
  /** @method Tablist.toggleProp
   *  @descr Toggles whether tab lookups vs. labels are visible in the sidebar
   */
  TabList.prototype.toggleProp = function() {
    this.activeProp(this.activeProp() === 'label' ? 'lookup' : 'label');
  };
  
  /* ------  tab helper methods  ------ */
  
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
    return '#' + tab.data.lookup();
  };
  
  /* ------  component disposal  ------ */
  
  /** @method Tablist.dispose
   *  @descr Changes the selection if there is no active tab.
   *  Invoked automatically on component disposal by KnockoutJS
   */
  TabList.prototype.dispose = function() {    
    if (!this.items().length && this.originalItems().length)
      this.selection(0);
  };
  
  /* ------  component registration ------ */
  
  ko.components.register(cName, {
    viewModel: TabList,
    template: {require: 'text!templates/' + cTmpl + '.html' }
  });
});
