define(['modules/globals', 'lib/knockout', 'lib/filesaver', 'models/tab', 'models/setting', 'modules/state'], function(globals, ko, fs, Tab, Setting, stateMgr) {
  
  // globals import
  var i18n  = globals.i18n
    , notify= globals.notifier
    , GS    = globals.GS
    , $     = globals.$;
  
  // types translation for edit mode
  var types = [
    {value: 'text',           label: i18n('input_text')},
    {value: 'textarea',       label: i18n('input_textarea')},
    {value: 'checkbox',       label: i18n('input_checkbox')},
    {value: 'radio',          label: i18n('input_radio')},
    {value: 'select',         label: i18n('input_select')},
    {value: 'image',          label: i18n('input_image')},
    {value: 'switch',         label: i18n('input_switch')},
    {value: 'color',          label: i18n('input_color')},
    {value: 'section-title',  label: i18n('input_section_title')},
    {value: 'date',           label: i18n('input_date')}
  ];
  
  // accessLevels translation for edit mode
  var accessLevels = [
    {value: 'normal',         label: i18n('access_normal')},
    {value: 'locked',         label: i18n('access_locked')},
    {value: 'hidden',         label: i18n('access_hidden')}
  ];
  
  /**
   *  @prop {object} modules - contains all parameters sent to other components through the view
   */
  function CS(data) {
    var self = this;
    this.data = ko.observableArray(ko.utils.arrayMap(data, function(item) { return new Tab(item);  }));
    // _data stores the original data gotten from AJAX for comparison later
    this._data = this.data();
    this.tabListTemplate = ko.pureComputed(function() {
      return 'tablist-' + self.mode();
    }).extend({rateLimit: 100});
    this.config = JSON.parse(document.getElementById('cs-config').value);
    this.config.editPerm = this.config.editPerm === 'false' ? false : true;
    // used in case the admin directory is set to non-default in GS.
    this.config.adminDir = (function() {
      var l = location.href.slice(0, location.href.lastIndexOf('/')); 
      return l.slice(l.lastIndexOf('/')+1);
    }());
    this.types = types;
    this.accessLevels = accessLevels;
    this.errors = {
      actions: {
        tabRemove:        ['warn_tab_remove', 'notify', {ok: function() {singleSelectList.prototype.remove.apply(self.data);}}],
        settingsRemove:   ['This action will irrevocably delete all selected settings. Are you sure you wish to proceed?', 'notify', {ok: function() {List.prototype.remove.apply(self.data.items()[self.data.activeItem()].settings)}}],
      }
    };
    this.state = stateMgr;
    this.state.tabSelection.subscribe(function(newIndex) {
      var selTab = self.data()[newIndex]
        , hook;
      if (newIndex > -1 && selTab.tab.data.type === 'plugin') {
        hook = GS.hooks[selTab.tab.data.lookup()];
        if (hook.inited !== true && typeof hook.init === 'function') {
          hook.init();
          hook.inited = true;
        }
        if (typeof hook.update !== 'undefined')
          hook.update();
      }
    });
    this.state.tabSelection.subscribe(function(oldIndex) {
      var selTab = self.data()[oldIndex]
        , hook;
      if (oldIndex > -1 && selTab.tab.data.type === 'plugin') {
        hook = GS.hooks[selTab.tab.data.lookup()];
      if (typeof hook.dispose === 'function')
        hook.dispose();
      }
    }, null, 'beforeChange');
    this.mode = this.state.mode;
    this.modeLabel = ko.pureComputed(function() { return 'mode_' + (self.mode() === 'edit' ? 'manage' : 'edit'); });
    this.pageTitle = ko.pureComputed(function() {
      return self.state.tabSelection() > -1 && self.data()[self.state.tabSelection()] ? ko.unwrap(self.data()[self.state.tabSelection()].tab.data.label) : i18n('title');
    });
    this.modules = {
        main: {
          name: ko.computed(function() { return 'settinglist-' + self.mode() })
        , params: ko.computed(function() {
          return {
            tab: self.data()[self.state.tabSelection()]
          , state: self.mode 
          }; })
        }
      ,  nav: {
          name: 'nav'
        ,  params: this 
        }
      , search: { 
          name: 'search-module' 
        , params: { 
            data: ko.pureComputed(function() { return self.data()[self.state.tabSelection()].settings(); })
          , length: 2
          , search: self.state.searchFilter 
        }
      } 
    };
    this.selectedTab = ko.pureComputed(function() {
      return self.data()[self.state.tabSelection()];
    });
    this.exportList = ko.pureComputed(function() {
      var result = [
            {label: '  ', lookup: 'none'}
          , {label: i18n('label_sitesettings'), lookup: 'site'}
        ]
        , tabs = self.data();
        
      for (var i = 0; i < tabs.length; i++)
        result.push({ 
          label: tabs[i].tab.data.label(), 
          lookup: tabs[i].tab.data.lookup(), 
          index: i
        });
        
      return result;
    });
    this.checkPluginVersion();
  }
  
  /** @function CS.toggleMode
   *  @descr Toggles between edit/manage app modes
   */
  CS.prototype.toggleMode = function() {
    var mode = this.mode();
    this.mode(mode === 'edit' ? 'manage' : 'edit');
  };
  
  /** @function CS.addHook
   *  @descr enables other plugins to add hooks to execute for their settings
   */
  CS.prototype.addHook = function(tabLookup, hook) {
    var tab = ko.utils.arrayFirst(this.data(), function(tabObj) {
      return tabObj.tab.lookup() === tabLookup;
    });
    if (tab) {
      tab.tab.init = hook.init;
      tab.tab.update = hook.update;
    }
  };
  
  /** @function CS.showPluginInfo
   *  @descr Opens a notification with help links and meta-info
   */
  CS.prototype.showPluginInfo = function(data, e) { 
    var elem = document.getElementById('cs-plugin-info');
    e.preventDefault(); 
    notify(elem.innerHTML, 'notify', false, 'long');
  };
  
  /** @function CS.checkForNamingConflict
   *  @descr Checks whether there are duplicate tab lookups/ duplicate setting lookups in the same tab and notifies the user accordingly
   *  @returns true if there are duplicates
   */
  CS.prototype.checkForNamingConflict = function() {
    var tabs = this.data()
      , tabsLength = tabs.length
      , msg
      , settings
      , settingsLength;
    
    // first verify there is no naming conflict between tab lookups
    for (var i = 0; i < tabsLength; i++) {
      for (var j = 0; j < tabsLength; j++) {
        if (tabs[i].tab.data.lookup() === tabs[j].tab.data.lookup() && j !== i) {
          msg = i18n('error_conflict_tabs')
            .replace('%tab1%', tabs[i].tab.data.label())
            .replace('%tab2%', tabs[j].tab.data.label())
          notify(msg, 'error');
          return true;
        }
      }
      settings = tabs[i].settings();
      settingsLength = settings.length;
      // for each tab, verify there is no naming conflict between settings in the same tab
      for (var s = 0; s < settingsLength; s++) {
        for (var t = 0; t < settingsLength; t++) {
          if (settings[s].data.lookup() === settings[t].data.lookup() && s !== t) {
            msg = i18n('error_conflict_settings')
              .replace('%setting1%', settings[s].data.label())
              .replace('%setting2%', settings[t].data.label())
              .replace('%tab%', tabs[i].tab.data.label());
            notify(msg, 'error');
            return true;
          }
        }
      }
    }
  };
  
  /** @method CS.checkForChanges
   *  @descr compares a cached copy of the data received on page-load to the current data;
   *         vars prefixed with '_' refer to the cached copy
   *  @returns true if changes have taken place (cached copy !== current data)
   */
  CS.prototype.checkForChanges = function() {
    var data  = ko.toJS(this.data())
      , _data = this._data
      , tabData,      _tabData
      , settingsData, _settingsData
      , setting,      _setting;
    
    // if there are more/less tabs than at page load: data has been changed
    if (data.length !== _data.length)
      return true;
      
    for (var i = 0; i < data.length; i++) {
      // tab properties can only change in site type tabs
      if (data[i].tab.data.type === 'site') {
        tabData = data[i].tab.getData();
        _tabData = _data[i].tab.getData();
        for (var prop in tabData) {
          // if a lookup/label has changed vs page load values: data has been changed
          if (tabData[prop] !== _tabData[prop]) {
            return true;
          }
        }
      }
      settingsData = data[i].settings;
      _settingsData = _data[i].settings;
      // if there are more/less settings in the current tab than at page load: data has been changed
      if (settingsData.length !== _settingsData.length) 
        return true;
      for (var s = 0; s < settingsData.length; s++) {
        setting = settingsData[s].getData();
        _setting = _settingsData[s].getData();
        // if the tab is a site tab, all properties on a setting could have changed
        if (data[i].tab.data.type === 'site') {
          // starting in v0.6, empty properties are removed from the output.
          // As such     
          if (Object.keys(setting).length !== Object.keys(_setting).length)
            return true;
          for (var prop in setting) {    
            // this is mainly for arrays
            if (typeof setting[prop] === 'object') {
              if (setting[prop].join('') !== _setting[prop].join(''))
              return true;
            // all primitive property values
            }  else if (setting[prop] !== _setting[prop]) {
              return true;
            }
          }
        // but if it's not only the value can be changed
        } else {
          if (setting.value !== _setting.value) {
            return true;
          }
        }
      }
    }
    // if no changes were found return false
    return false;
  };
  
  CS.prototype.getJSON = function(action, callback) {
    var self = this;
    $.ajax({ url: this.config.handler, type: 'POST', data: {
      action: action, 
      path: this.config.handler, 
      requestToken: this.config.requestToken, 
      adminDir: this.config.adminDir,
      id: this.config.id }
    })
    .done(function(data, status, jqXHR) {
      if (callback && typeof callback === 'function') { 
        callback(data, status);
      }
    })
    .fail(function(jqXHR, status, error) {
      if (callback && typeof callback === 'function') { 
        callback(null, status, error);
      }
    });
  };
  
  CS.prototype.sendJSON = function(action, data, callback) {
    var self = this;
    $.ajax({ 
        url: this.config.handler
      , type: 'POST'
      , data: {  
          action: action
        ,  path: this.config.handler
        ,  requestToken: this.config.requestToken
        ,  adminDir: this.config.adminDir
        ,  id: this.config.id
        ,  data: data
      }
    })
    .done(function(data, status, jqXHR) {
      if (callback && typeof callback === 'function') { 
        callback('success');
      }
    })
    .fail(function(jqXHR, status, error) {
       console.log(data);
      if (callback && typeof callback === 'function') { 
        callback('error', error);
      }
    });
  };
  
  /** @function CS.saveData
   *  @description Checks for naming conflicts and sends output to PHP for file saving
   */
  CS.prototype.saveData = function() {
    var d = this.data()
      , self = this
      , result = {};
    
    // If no changes have taken place, do not save. If there's a naming conflict, notify error. 
    // Return in both cases
    if (this.checkForNamingConflict() || !this.checkForChanges())
      return;
      
    this.sendJSON('saveData', this.formatExport(), function(status) {
      if (status === 'success') {
        notify(i18n('SETTINGS_UPDATED'), 'updated');
        // update the cached data so the checkForChanges function compares to the latest saved data
        self._data = ko.toJS(self.data());
      } else 
        notify(i18n('error_save'), 'error');
    });
  };
  
  /** @function CS.formatExport
   *  @description Converts the data from array to object, removes view-related properties from settings
   *  @param {boolean} formatted - whether the JSON returned should be tab-indented
   *  @param {object} [data] - one or more tabs to format
   *  @returns a(n optionally formatted) JSON string
   */
  CS.prototype.formatExport = function(formatted, data) {
    var d = data || this.data()
        , result = {}
        , tabLookup;
        
    ko.utils.arrayForEach(d, function(tabEntity) {
      tabLookup = tabEntity.tab.data.lookup();
      result[tabLookup] =  {
        tab: tabEntity.tab.getData(),
        settings: ko.utils.arrayMap(tabEntity.settings(), function(setting) {
          var result = setting.getData();
          if (setting.icon && setting.icon())
            result.type = 'icon-' + result.type;
          return result;
        })
      };
    });
    return formatted ? ko.toJSON(result, null, 2) : ko.toJSON(result);
  };
  
  
  CS.prototype.formatTab = function(tabEntity) {
    var result = {
      tab     : tabEntity.tab.getData()
    , settings: ko.utils.arrayMap(tabEntity.settings(), function(setting) {
        return setting.getData();
      })
    };
    return ko.toJS(result);
  };
  
  
  CS.prototype.resetValues = function() {
    var settings = this.data()[this.state.tabSelection()].settings();
    for (var i = 0; i < settings.length; i++) {
      if (settings[i].data.hasOwnProperty('value') && settings[i].data.hasOwnProperty('default')) // make sure to exclude section titles
        settings[i].data.value(settings[i].data['default']);
    }
  }
  
  CS.prototype.checkPluginVersion = function() {
    var self = this;
    this.getJSON('loadPluginInfo', function(data) {
      var remoteInfo= $.parseJSON(data)
        , i         = self.config.pluginVer.indexOf('.')
        , i2        = remoteInfo.version.indexOf('.')
        , vLocal    = parseFloat(self.config.pluginVer.slice(0,i) + '.' + self.config.pluginVer.slice(i + 1).replace(/\./g,''))
        , vRemote   = parseFloat(remoteInfo.version.slice(0,i2) + '.' + remoteInfo.version.slice(i2 + 1).replace(/\./g,''))
        , str       = i18n('about_new_version') + ' <a href="http://get-simple.info/extend/export/10656/913/gs-custom-settings.zip">' + i18n('about_download') + ' v.' + remoteInfo.version + '</a>'
        , dlLink    = document.getElementById('cs-plugin-info').getElementsByTagName('a')[0];
      if (vRemote > vLocal) {
        notify(str, 'notify');
        dlLink.href = remoteInfo.file;
        dlLink.parentNode.className = '';
      }
    });
  };

  var saveText = function(data, fileName) {
    // IE10+, Moz FF 20+, Opera 15+, Chrome & Safaari
    if (window.Blob) {
      window.saveAs(new Blob([data], {type: "text/plain; charset=utf-8"}), fileName);
    // all older browsers
    } else {
      // the saveTextAs method only allows us to save data in ASCII format;
      // remove extension from fileName: only allowed extensions are .html, .csv and .json
      // user needs to manually rename the file
      window.saveTextAs(data, fileName.replace('.json', ''));
    }
  };
  
  /** @method CS.exportAsData
   *  @descr Export one/ multiple tabs for re-importing later (migrations, updates, etc.). Executed from the onewaySelect binding
   *  @param {string} value - the value attribute of the selected option (tab lookup or custom)
   *  @param {number|null} tabIndex - the data-index attribute of the selected option (=tab)
   */
  CS.prototype.exportAsData = function(value, tabIndex) {
    var data
      , self = this
      , selTab = this.data()[tabIndex]
      , separator= '\t'
      , fileName = 'data.json';
    
    // if the option has a tabIndex, we're handling a single tab.
    // for all these cases, the export format is the same; only the filenames need to change
    if (tabIndex) {
      data = this.formatTab(selTab);
      // in case it is a theme tab, delete the tab label, it is irrelevant
      if (value === 'theme_settings') {
        fileName = 'theme_data_' + this.config.template + '.json';
        data.tab.lookup = this.config.template;
        delete data.tab.label;
      } else {
        if (data.tab.type === 'site') {
          fileName = value + '_data.json';
        } else {
          fileName = 'plugin_data_' + data.tab.lookup + '.json';
        }
      }
    // else it is a collection of tabs. In that case, we need to loop over each one for the export
    // @TODO: add an option to export EVERYTHING vs only site tabs (currently it's the first)
    } else {
      data = {site: []};
      ko.utils.arrayForEach(this.data(), function(tabObj) {
        data.site.push(self.formatTab.call(self, tabObj));
      }, this);    
    }    
    
    saveText(ko.toJSON(data, null, separator), fileName);
  };
  
  /** @method CS.exportAsDev
   *  @descr Edit mode export for plugin & theme development; uses CS.formatTab
   */
  CS.prototype.exportAsDev = function(data, e) {
    var tabObj   = this.data()[this.state.tabSelection()]
      , target   = e.target || e.srcElement
      , type     = target.value
      , fileName = 'settings.json'
      , separator= '\t'
      , result   = this.formatTab(tabObj);
    
    // only plugin type tab exports
    result.tab.type = type;
    
    // theme settings.json files do not require a tab object
    if (type === 'theme')
      delete result.tab;
      
    // save with FileSaver.js
    saveTextAs(ko.toJSON(result, null, separator), fileName);
    
    // reset the select to blank
    target.selectedIndex = 0;
  };
  
  /* ------  import methods  ------ */
  
  /** @method CS.importData
   *  @descr Reads the file input, notifies in case of error, and calls either addNewTab or mergeTabs
   */
  CS.prototype.importData = function(data, e) {
    var findTab = this.returnTab
      , mergeTabs= this.mergeTabs
      , addNewTab= this.addTab
      , self = this
      , value = (e.target || e.srcElement).value
      , file = (e.target || e.srcElement).files[0], fRead, result
      , allTabs = this.data()
      , tabObj = {},
        sType = (function settingsType() {
          value = value.indexOf('\\') > -1 ? value.slice(value.lastIndexOf('\\')+1) : value;
          if (value.match('theme_data')) {
            return 'theme';
          } else if (value.match('plugin_data')) {
            return 'plugin';
          } else if (value.match(/\bdata.json/)) {
            return 'site';
          } else if (value.match('_data.json')) {
            return 'tab';
          }
        }());
        
    // Check for the various File API support + additional select check
    if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
      notify('error_jsonimport_invalidbrowser','error');
      return;
    }
    if (file) {
      if (!value.match('.json')) {
        notify('error_jsonimport_invalidext','error');
        return;
      }
      fRead = new FileReader();
      fRead.readAsText(file);
      fRead.onload = function() {
        if (fRead.result.length) {
          try {
            var result = JSON.parse(fRead.result);
          } catch (e) {
            notify('error_jsonimport_invalidjson', 'error');
            return;
          }
          if (self.mode() === 'manage') {
            var sidebar = document.getElementById('sidebar').getElementsByTagName('ul')[0];
            ko.cleanNode(sidebar);
            ko.applyBindingsToNode(sidebar, null, ko.contextFor(e.target || e.srcElement).$root);
          }
          switch (sType) {
            case 'theme':
              var themeTab = findTab.call(self, 'type', 'theme');
              // only allow importing settings from the current theme
              if (themeTab && result.theme === document.getElementById('site-template').value)
                mergeTabs(themeTab, result);
              else 
                notify('error_jsonimport_thememissing', 'error');
            break;
            case 'tab':
              var singleTab = findTab.call(self, result.tab.lookup);
              if (singleTab)
                mergeTabs(singleTab, result);
              else
                addNewTab.call(self, result);
            break;
            case 'site':
              for (var i = 0; i < result.site.length; i++) {
                var singleTab = findTab.call(self, result.site[i].tab.lookup);
                if (singleTab) {
                  mergeTabs(singleTab, result.site[i]);
                } else
                  addNewTab.call(self, result.site[i]);
              }
              
            break;
            case 'plugin':
              var pluginTab = findTab.call(self, result.tab.lookup);
              // only allow importing settings from an activated plugin
              if (pluginTab && pluginTab.type === 'plugin')
                mergeTabs(pluginTab, result);
              else 
                notify('error_jsonimport_pluginmissing', 'error');            
            break;
          }
        }
      };
    }
  };
  
  /** @method CS.addTab
   *  @descr Adds an imported tab if no existing tab with the same lookup exists
   */
  CS.prototype.addTab = function(tabObj) {
    var tabs   = this.data()
      , len    = tabs.length
      , tabObj = { 
        tab: new Tab(tabObj || {
            type  : 'site'
          , lookup: i18n('def_tab_lookup')
          , label : i18n('def_tab_label')
        })
        , settings: ko.observableArray(tabObj && tabObj.settings ? ko.utils.arrayMap(tabObj.settings, function(s) {
          return new Setting[s.type](s);
        }) : []) 
      };
      // make sure we append the tab before plugins/ theme settings
      while (len--)
        if (tabs[len].tab.data.type !== 'site') break;
      this.data.splice(len-2 || 0, 0, tabObj);
      this.data.valueHasMutated();
  };
  
  /** @method CS.mergeTabs
   *  @descr Merges imported tab in case the lookup collides with an existing tab's lookup
   */
  CS.prototype.mergeTabs = function(oldTab, newTab) {
    var oldS = oldTab.settings()
      ,  newS = newTab.settings
      ,  newSDict = ko.utils.arrayMap(newS, function(setting) { return setting.lookup; })
      ,  modifiableProps = ['value','type','descr','access','label','options','values']
      , currentIndex;
      
      // first loop over all current settings
      for (var i = 0; i < oldS.length; i++) {
        currentIndex = newSDict.indexOf(oldS[i].data.lookup());
        // if the new setting's lookup is already present in current settings,
        if (currentIndex > -1) { 
          var currentNewItem = ko.utils.arrayFirst(newS, function(x) {
            return x.lookup === oldS[i].data.lookup();
          });
          // don't overwrite the whole setting, replace on a per property basis
          for (var prop in oldS[i].data) {
            if (ko.unwrap(oldS[i].data[prop]) !== newS[currentIndex][prop]) {
              // all replacable properties are observables, ok to call as function
              if (/options|i18n/.test(prop) && currentNewItem[prop]) {
                var temp = [];
                ko.utils.arrayForEach(currentNewItem[prop], function(option, i) { temp.push(option); });
                currentNewItem[prop] = temp;
              }
              if (typeof oldS[i].data[prop] == 'function') 
                oldS[i].data[prop](currentNewItem[prop]);
              else 
                oldS[i].data[prop] = currentNewItem[prop];
            }
          }
          // remove replaced items' lookup from dictionary to keep only 'new' settings
          newSDict.splice(currentIndex, 1);
        }
      }
      // if there are still new settings that didn't exist yet, add them
      if (newSDict.length) {
        for (var i = 0; i < newSDict.length; i++) {
          var newSetting = ko.utils.arrayFirst(newS, function(s) {
            return s.lookup === newSDict[i];
          });
          oldTab.settings.push(new Setting[newSetting.type](newSetting));
        }
      }
      // make sure Knockout views are updated
      oldTab.settings.valueHasMutated();
  };
    
  CS.prototype.unmap = function(data) {
    var self = this
      , i18nArr
      , result = [];
    for (var item in data) {
      var obj = data[item];
      obj.tab.label = obj.tab && obj.tab.type === 'theme' ? i18n('label_themesettings') : obj.tab.label;
      obj.tab.type = obj.tab.type || 'site';
      result.push({
        tab: new Tab(obj), 
        settings: ko.observableArray(ko.utils.arrayMap(obj.settings, function(setting) {  
          return new Setting[setting.type](setting); 
        }))
      });
    }
    this.data(result);
  };
  
  /* ------  API methods  ------ */
  
  /** @method CS.returnTab
   *  @descr Returns a tab's data. API function for debugging mainly (+ for other plugins)
   */
  CS.prototype.returnTab = function(tabLookup) {
    var tabs = this.data();
    return ko.utils.arrayFirst(tabs, function(tabObj) {
      return tabObj.tab.data.lookup() === tabLookup;
    }) || false;
  };
  
  /** @method CS.returnSetting
   *  @descr Returns a setting's data. API function for debugging mainly (+ for other plugins)
   */
  CS.prototype.returnSetting = function(tabLookup, settingLookup) {
    var tabObj = ko.utils.arrayFirst(this.data(), function(tab) {
      return tab.tab.data.lookup() === tabLookup;
    });
    if (!tabObj) 
      return null;
    var settingObj = ko.utils.arrayFirst(tabObj.settings(), function(setting) {
      return setting.data.lookup() === settingLookup;
    });
    return settingObj.data;
  };
  
  
  return CS;
});
