requirejs.config({
  baseUrl: '../plugins/custom_settings/js/'
  , paths: {
      lib: 'lib'
    , models: 'models'
    , modules: 'modules'
    , templates: '../tmpl'
  }
});

require([
    'modules/globals'
  , 'lib/knockout'
  , 'lib/knockout.punches.min'
  , 'modules/ko-extensions'
  , 'master'
  , 'modules/search'
  , 'models/tab'
  , 'modules/animation'
  , 'domReady'
  , 'text'
  , 'modules/settings-manage'
  , 'modules/tabs-manage'
  , 'modules/settings-edit'
  , 'modules/tabs-edit'
  , 'modules/image-browser'
], 
function(globals, ko, punches, cs, App, sMod, Tab, anim, domReady) {
  domReady(function() {
  
    // import globals and instantiate app
    var $    = globals.$
      , i18n = globals.i18n
      , app  = window.GSCS = new App();
  
    // it is impossible to have control over the outer sidebar HTML in GS, 
    // so we need to set the KO binding programmatically before the app is instantiated.
    document.getElementById('sidebar')
            .getElementsByTagName('ul')[0]
            .setAttribute('data-bind', 'component: {name: tabListTemplate, params: {tabs: data, state: state, mainParams: modules.main }}');
    
    globals.GS.config = app.config;
    
    // The nav component has no viewmodel; instantiate it here  
    ko.components.register('nav', { template: { require: 'text!templates/nav.html' }});
    
    $(document.body).on('click', '.setting.date .setting-field', function(e) {
      var $this = $(this);
      if (!$this.hasClass('hasDatePicker'))
        $this.datepicker({
          dateFormat: 'yy-mm-dd',
          onSelect: function(date) {
            var setting = ko.dataFor($this[0]);
            setting.data.value(date);
          }
        }).datepicker('show');
        
    });
    
  /**
   *  @see http://stackoverflow.com/questions/2790001/fixing-javascript-array-functions-in-internet-explorer-indexof-foreach-etc
   *  IE8 Array.indexOf polyfill By Bobbins
   */
  if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(find, i) {
      if (i === undefined) i = 0;
      if (i < 0) i += this.length;
      if (i < 0) i = 0;
      for (var n = this.length; i < n; i++)
        if (i in this && this[i] === find)
          return i;
      return -1;
    };
  }
  ko.utils.registerEventHandler(document.body, 'keydown', function(e) {
    if (e.ctrlKey) {
      if (e.keyCode === 70) {
        e.preventDefault();
        document.getElementById('setting-search').getElementsByTagName('input')[0].focus();
      } else if (e.keyCode === 83) {
        e.preventDefault();
        app.saveData.call(app);
      }
    }
  });
  app.getJSON('getDataFile', function(data) {
    app.unmap(JSON.parse(data).data);
    app._data = ko.toJS(app.data());
  
  // Adding the cs-loaded class to the body displays 
  // the plugin UI without showing the JS behind the scenes
  document.body.className += ' cs-loaded';
  
  $(document.body).on('mouseup', '#notification-manager input', function(e) {
    e.preventDefault();
    (e.target || e.srcElement).select();
  });
  
  // Check/Uncheck switch/icon-checkbox inputs
  $(document.body).on('click', '.setting.switch .icon-button, .setting.icon-checkbox .icon-button', function(e) {
    var value = ko.dataFor(e.target).data.value;
    value(!value());
  });
  
  // View-related functionality used to be set through KO in the prototypes,
  // but simple DOM event delegation seems more efficient. All events need to be delegated,
  // as views get regenerated all the time
  
  $(document.body).on('click', '.setting .select', function(e) {
    var context       = ko.contextFor(e.target || e.srcElement)
      ,  selectionObj  = context.$parent.selection
      ,  selection     = selectionObj()
      ,  shiftSelIndex = context.$parent.shiftSelIndex
      ,  index         = context.$index()
      ,  isSelected    = true;
        
    if ((!e.shiftKey && !e.ctrlKey)) {
      if (!selection.length) {
        selectionObj.push(index);
      } else {
        // The second condition behind the OR operator ensures that when multiple settings are selected,
        // the current setting becomes the only selected one, instead of emptying the selection.
        if (selection.indexOf(index) === -1 || (selection.indexOf(index) > -1 && selection.length > 1 ))
          isSelected = false;
        selectionObj([]);
        
        if (!isSelected)
          selectionObj.push(index);
      }
      shiftSelIndex(index);
    } else if (e.ctrlKey) {
      if (selection.indexOf(index) === -1)
        selectionObj.push(index);
      else  
        selectionObj.splice(selection.indexOf(index), 1);
      shiftSelIndex(index);
    } else if (e.shiftKey) {
      var lastSelIndex = shiftSelIndex(),
          result = [];
      if (lastSelIndex > index) {
        for (var i = 0; index + i <= lastSelIndex; i++) 
          result.push(index + i);
      } else {
        for (var i = 0; lastSelIndex + i <= index; i++) 
          result.push(lastSelIndex + i);
      }
      selectionObj(result);
    }
  });
  ko.applyBindings(app, document.body);
  window.ko = ko;
  });
  });
});
