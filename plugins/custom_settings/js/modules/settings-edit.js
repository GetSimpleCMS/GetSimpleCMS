/**
 * Setting-list module
 * @module modules/settings-edit
 */
 
 define(['lib/knockout', 'models/setting', 'modules/animation', 'modules/globals'], function(ko, Setting, anim, globals) {
  
  // import globals & define component parameters
  var notify = globals.notifier
    , $      = globals.$
    , cName  = 'settinglist-edit'
    , cTmpl  = 'settings-edit.html'
    , buffer 
    , bufferInterval     = null
    , dataOnInit = null;

  var doNotAnimate = false;
  /** @constructor SettingList
   *  @alias modules/settings-edit
   *  @prop shiftSelIndex - keeps track of the first setting clicked with a tab key pressed.
   *                        required for multi-selection in edit-mode
   */
  function SettingList(params) {
    var self = this
      , slowLoad = params.tab.settings().length > 15;
    this.options = { 
      maxLimit: 100,
      minLimit: 0,
      maxSelect: false,
      keysActive: false
    };
    this.showLoader = ko.observable(false);
    this.tab = params.tab.tab;
    
    // IE 9- has considerably slow loading time for tabs with > 15 settings in Edit mode
    if (slowLoad) {
      buffer = params.tab.settings();
      // make a copy of the initial data, in case user switches to another tab before
      // all settings in buffer copy have been pushed back to the bound setting list
      dataOnInit = buffer.concat([]);
      // empty the bound setting list
      params.tab.settings([]);
      // show spinner in UI
      this.showLoader(true);
    }
    // if slowLoad -> assign empty array, else originally passed data
    this.items = params.tab.settings;
    
    if (slowLoad) {
      // if empty array was assigned, now gradually re-transfer the settings in buffer to the bound list.
      // if this process is interrupted, the dispose function will make sure the original items are re-assigned through dataOnInit
      doNotAnimate = true;
      bufferInterval = setInterval(function() {
        self.items.push(buffer.shift());
        if (buffer.length === 0) {
          doNotAnimate = false;
          self.showLoader(false);
          clearInterval(bufferInterval);
        }
      }, 50);
    }
    // keep track of all the selected settings
    this.selection = ko.observableArray([]);
    // keep track of all the expanded settings
    this.expanded = ko.pureComputed({
      read: function() { 
        return ko.utils.arrayFilter(self.items(), function(setting) { return setting.expanded && setting.expanded(); }) 
      },
      write: function(value) {
        ko.utils.arrayForEach(self.selection(), function(index) { 
          if (self.items()[index].expanded)
            self.items()[index].expanded(value);
        });
      }
    }, this);
    this.shiftSelIndex = ko.observable(false);
    // view-related
    this.allExpanded = ko.pureComputed(function() { 
      var items  = self.items()
        , expLen = self.expanded().length;
      return expLen > 0 && expLen === ko.utils.arrayFilter(self.selection(), function(i) {
        return items[i].data.type !== 'section-title'; 
      }).length;
    });
    this.selectButtonActive = ko.pureComputed(function() { return self.items().length && self.selection().length === self.items().length ? 'active' : ''; });
    this.expandButtonActive = ko.pureComputed(function() { 
      var items = self.items()
        , sel   = ko.utils.arrayFilter(self.selection(), function(s) { return items[s].expanded; });
      return items.length && self.expanded().length === sel.length && sel.length > 0 ? 'active' : ''; 
    });
    this.disable = {
      del     : ko.pureComputed(function() { return !self.selection().length; }),
      add     : ko.pureComputed(function() { return self.options.maxLimit === self.items().length; }),
      up      : ko.pureComputed(function() { return self.selection()[0] > 0 ? false : true; }),
      down    : ko.pureComputed(function() { return self.selection()[self.selection().length-1] < self.items().length-1 && self.selection().length !== self.items().length ? false : true; }),
      select  : ko.pureComputed(function() { return self.items().length === 0; }),
      expand  : ko.pureComputed(function() { return self.selection().length === 0; })
    };
    // sticky toolbar has to be set every time Edit mode is activated, as it is removed from the DOM.
    // will only fire if GSSTYLE_SBFIXED is set
    this.setSticky();
  }
  
  /** @function SettingList.selectAll
   *  @description (De)selects all settings
   */
  SettingList.prototype.selectAll = function(data, e) {
    var items = this.items()
      , target = e.target.nodeName === 'I' ? e.target.parentNode : e.target
      ,  selection = this.selection()
      ,  count = -1
      , arr = [];
        
    if (selection.length === items.length) {
      this.selection([]);
    } else {
      while (count++ < items.length-1)
        arr.push(count);
      this.selection(arr);
    }
  };
  
  /** @method SettingList.expandAll
   *  @descr Expands/Folds all selected settings
   */
  SettingList.prototype.expandAll = function(data, e) {
    var items     = this.items()
      , selection = this.selection()
      , flag      = false;
      
    for (var i = 0; i < selection.length; i++) {
      if (items[selection[i]].expanded && items[selection[i]].expanded() === false)
        flag = true;
    }
    this.expanded(flag);
  };
  
  /** @method SettingList.insertBefore
   *  @descr Inserts a new setting before every selected setting
   */
  SettingList.prototype.insertBefore = function(data, e) {
    var insertAt
      , i         = 0
      , selection = this.selection()
      , items     = this.items()
      , expanded  = this.expanded()
      , selIsInterrupted = false;
      
    if (!selection.length) {
      // if no setting is selected, prepend one at the start of the list
      items.unshift(new Setting['text']({type: 'text', options: []}));
    } else {
      // to check whether selection is interrupted the selection object needs to be sorted (ascending)
      selection.sort();
      for (var s = 0; s < selection.length; s++) {
        if (selection[s]+1 !== selection[s+1] && selection[s+1])
          selIsInterrupted = true;
      }
      // if the selection is not interrupted, insert n new settings AFTER
      // the last selected element (mimics Excel behavior)
      if (!selIsInterrupted) {
        insertAt = Math.max.apply(null, selection) + 1;
        for (var i = selection.length; i--; ) {
          selection[i] = insertAt;
          items.splice(insertAt, 0, new Setting['text']({}));
          insertAt += 1;
        }
      // else if the selection is interrupted, insert 1 new setting after every selected setting
      } else {
        for (var i = selection.length; i--; ) {
          insertAt = selection[i] + 1;
          selection[i] += i + 1;
          items.splice(insertAt, 0, new Setting['text']({}));
        }
      }
      // first update selection
      this.selection(selection);
    }
    // finally, update the setting list
    this.items(items);
  };
  
  /** @method SettingList.remove
   *  @descr Removes all selected settings
   */
  SettingList.prototype.remove = function() { 
    var selection = this.selection()
      , items = this.items();
      
    if (!selection.length)
      return;
      
    // first empty the selection, we have it cached
    this.selection([]);
    // remove settings on the cached array
    for ( var i = selection.length; i--;)
      items.splice(selection[i],1);
      
    // finally, update the observables
    this.items(items);
  };
  
  /** @method SettingList.replace
   *  @descr Replaces the current setting with a new one. Used when the type of a setting is changed
   */
  SettingList.prototype.replace = function(opts, e) {
    var data  = ko.toJS(opts.data)
      , items = this.items();
     
    // section titles don't have an "expanded" property, avoid JS error.
    if (data.type !== 'section-title')
      data.expanded = opts.expanded();
      
    data.type = e.options[e.selectedIndex].value;
    // we do not want the typical insert/remove animation for simply changing a type,
    // so pause animation while we modify the array
    doNotAnimate = true;
    items[ko.contextFor(e).$index()] = new Setting[data.type](data);
    this.items(items);
    doNotAnimate = false;
  };
  
  /** @method SettingList.clone
   *  @descr Clones every selected setting right underneath italics
   *  @ignore Not implemented in actual app
   */
  SettingList.prototype.clone = function() {
    var newItem
    , startAt
    , selection = this.selection()
    , selIsInterrupted = false;
        
    for (var i = selection.length; i--; ) {
      newItem = ko.toJS(this.items()[selection[i]].data);
      this.items.splice(selection[i] + 1, 0, new Setting[newItem.type](newItem));
    }
  }
  
  /** @method SettingList.moveUp
   *  @descr Moves all selected settings up by one index
   */
  SettingList.prototype.moveUp = function() {
    var moved
      , startAt
      , selection = this.selection()
      , newSel = selection.concat([])
      , minIndex = 0
      , i = 0
      , minActiveIndex = Math.min.apply(null, selection)
      , selIsInterrupted = false
      , items = this.items();
    
    if (!selection.length)
      return;
      
    function moveUpperItem() {
      moved = items.splice(startAt, 1)[0];
      items.splice(insertAt, 0, moved);
      newSel = ko.utils.arrayMap(selection, function(i) { return i-1; });
    }
    
    while (selection[i++]) {
      if (selection[i]+1 !== selection[i+1])
        selIsInterrupted = true;
    }
    
    // only do if the selected item with the lowest index is > 0.
    if (minActiveIndex !== minIndex) {
      if (!selIsInterrupted) {
        startAt = minActiveIndex - 1;
        insertAt = Math.max.apply(null, selection);
        moveUpperItem.call(this);
      } else {
        for (var i = 0; i < selection.length; i++ ) {
          startAt = selection[i] - 1;
          insertAt = selection[i];
          moveUpperItem.call(this);
        }
      }
      this.selection(newSel);
      this.items(items);
    }
  }
  
  /** @method SettingList.moveDown
   *  @descr Moves all selected settings down by one index
   */
  SettingList.prototype.moveDown = function() {
    var moved
      , startAt
      , i = 0
      , selection = this.selection()
      , newSel = selection.concat([])
      , items = this.items()
      , maxIndex = items.length - 1 || 0
      , maxActiveIndex = Math.max.apply(null, selection)
      , selIsInterrupted = false;
      
    if (!selection.length)
      return;
      
    function moveLowerItem() {
      moved = items.splice(startAt, 1)[0];
      items.splice(insertAt, 0, moved);
      newSel = ko.utils.arrayMap(selection, function(i) { return i+1; });
    }
    
    while (selection[i] > -1) {
      if (selection[i]+1 !== selection[i+1])
        selIsInterrupted = true;
      i++;
    }
    
    // only do if the selected item with the highest index is < last index in list.
    if (maxActiveIndex !== maxIndex) {
      if (!selIsInterrupted) {
        startAt = maxActiveIndex + 1;
        insertAt = Math.min.apply(null, selection);
        moveLowerItem.call(this);
      } else {
        // important to have a 'negative' for loop, else the position might be shifted
        for (var i = selection.length; i--; ) { 
          startAt = selection[i] + 1;
          insertAt = selection[i];
          moveLowerItem.call(this);
        }
      }
      this.selection(newSel);
      this.items(items);
    }
  };
  
  /** @method SettingList.keyHandler
   *  @descr Handles keyboard functionality for the settings list
   */
  SettingList.prototype.keyHandler = function(data, e) {
    // don't overwrite keyboard shortcuts when input elements are focused
    if (e.ctrlKey && e.target.nodeName !== 'INPUT') {
      // make sure the setting list is focused so keyboard events take place
      document.getElementsByClassName('cs-main')[0].getElementsByTagName('div')[1].focus();
      switch (e.keyCode) {
        case 46: // delete
          this.remove();
          break;
        case 38: // UP arrow
          this.moveUp();
          break;
        case 40: // DOWN arrow
          this.moveDown();
          break;
        case 13: // Enter
          this.insertBefore();
          break;
        case 65: // A
          e.preventDefault();
          var items = this.items()
            , newSel= [];
          if (this.selection().length !== items.length) {
            for (var i = 0; i < items.length; i++) 
              newSel.push(i);
            this.selection(newSel);
          }
          break;
      }
    }
    return true;
  };
  
  /* ------  animation methods ------ */
  
  /** @method SettingList.animateRemoval
   *  @see modules/animation
   */
  SettingList.prototype.animateRemoval = function(el) {
    if (el.nodeType === 1 && doNotAnimate === false)
      anim('remove-setting', el);
    else
      el.parentNode.removeChild(el)
  };
  
  /** @method SettingList.animateInsertion
   *  @see modules/animation
   */
  SettingList.prototype.animateInsertion = function(el) {
    if (el.nodeType === 1 && doNotAnimate === false) 
      anim('insert-setting', el);
  };
  
  /** @method SettingList.setSticky
   *  @sdescr When GSSTYLE SB_FIXED is set, make the Edit mode toolbar sticky
   */
  SettingList.prototype.setSticky = function() {
    if ($.ScrollToFixed) 
      anim('sticky-toolbar');
  };  
  
  /* ------  setting helper methods ------ */
  
  /** @method SettingList.classForSetting
   *  @descr Helper for generating the full classname attribute of a setting
   */
  SettingList.prototype.classForSetting = function(setting, index) {
    var self = this
      , className = 'setting ' + setting.data.type;
    className += setting.expanded && setting.expanded() ? ' expanded' : '';
    className += (this.selection().indexOf(index) > -1 ? ' active': '');
    return className;
  }
  
  /** @method SettingList.codeTitleForSetting
   *  @descr Helper for generating the title attribute for the code icon (per setting)
   */
  SettingList.prototype.codeTitleForSetting = function(setting) {
    return this.tab.data.lookup() + '/' + setting.data.lookup();
  };
  
  /** @method SettingList.showSettingCode
   *  @descr Helper for generating the content filter code opened through a setting's code icon
   */
  SettingList.prototype.showSettingCode = function(setting, e) {
    var msg = this.codeTitleForSetting(setting)
      , html = '<span style="font-family: Consolas, monospace;">' + msg + '<span>' + '<input type="text" id="cs-code-copy" value="<\?php get_setting(\'' + msg.replace('/', '\',\'') + '\'); ?>">';
      
    notify(html, 'notify', null, 'long');
    document.getElementById('notification-manager').getElementsByTagName('input')[0].select();
  };
  
  /* ------  component disposal ------ */
  
  /** @method SettingList.dispose
   *  @descr Makes sure the gradual setting load doesn't mess up the original data 
   *         by clearing the interval, and resetting the array to its original data
   */
  SettingList.prototype.dispose = function() {
    this.items().forEach(function(item) {
      if (item.expanded)
        item.expanded(false);
    });
    if (this.showLoader()) {
      clearInterval(bufferInterval);
      this.items(dataOnInit);
    }
  };
  
  /* ------  component registration ------ */
  
  ko.components.register(cName, {
    viewModel: SettingList,
    template: {require: 'text!templates/' + cTmpl }
  });
  
}); 
