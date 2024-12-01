/**
 * Setting data models
 * @module models/setting
 */
 
 define(['lib/knockout', 'modules/globals', 'modules/state', 'modules/color-index'], function(ko, globals, state, webcolors) {
  
  // IE8 simple shim
  if (!Object.create)
    Object.create = function(proto) { return proto; };
  
  var i18n = globals.i18n
    , defaultValues = {
        'select': 0
      , 'radio': 0
      , 'icon-radio': 0
      , 'checkbox': false
      , 'icon-checkbox': false
      , 'switch': false
      , 'text': ''
      , 'textarea': ''
      , 'color': ''
      , 'image': ''
      , 'date': ''
    };
  
  /** @function rgbToHex 
   *  @descr used by Color setting for converting rgb values to hex, as required by native HTML color inputs
   */
  var rgbToHex = function(rgb) {
    var val = rgb.replace(/[rgba\(\)]*/g, '').split(/, */)
      , str = '#';
    if (val.length > 3)
      val.pop();
    while (val.length) {
      var temp = parseInt(val.shift()).toString(16);
      str += temp.length == 1 ? '0' + temp : temp;
    }
    return str;
	};
  
  /** @constructor SectionTitle
   *  @description Section titles are different from other 'settings' and do not inherit anything as they are meant for display
   */
  var SectionTitle = function(opts) {
    this.data = {
        lookup: ko.observable(opts.lookup || '')
      , label : ko.observable(opts.label || '')
      , type  : 'section-title'
    };
    this.template = {
        edit  : this.data.type + '-edit'
      , manage: this.data.type + '-manage'
    };
  };
  
  /** @constructor Base
   *  @description All setting prototypes inherit from this one, except section titles.
   *  Serves as direct prototype to: color settings
   */
  var Base = function(opts, expanded) {
  
    // data holds all the properties which are retrieved from/ to be saved to a file
    this.data = {
        lookup: ko.observable(opts.lookup || i18n('def_setting_lookup'))
      , label : ko.observable(opts.label  || i18n('def_setting_label'))
      , type  : opts.type && opts.type.replace('icon-', '') || 'text'
      , descr : ko.observable(opts.descr || '')
      , access: ko.observable(opts.access || 'normal')
      , value : ko.observable(opts.value || defaultValues[opts.type])
    };
    
    // plugin or theme settings might have default properties (in order to enable resetting values)
    if (opts.default) 
      this.data.default = opts.default;
    
    // helpers
    this.hidden = ko.pureComputed(function() { return this.data.access() === 'hidden' }, this);
    this.locked = ko.pureComputed(function() { return this.data.access() === 'locked' }, this);
    this.template = {
        edit  : this.data.type + '-edit'
      , manage: this.data.type + '-manage'
    };

    this.expanded = ko.observable(opts.expanded || false);
    this.expandIcon = ko.pureComputed(function() {
      return 'fa fa-fw fa-' + (this.expanded() ? 'minus' : 'plus') + '-square';
    }, this);
      
    // settings might store additional untracked properties which should be saved as well
    for (var prop in opts) {
      if (!this.data.hasOwnProperty(prop) && !this.hasOwnProperty(prop)) 
        this.data[prop] = opts[prop];
    }
  };
  
  /** @method Base.name & SectionTitle.name
   *  @description Generic naming function for id/name input attributes
   *  @param {string} property - a property name to append to the lookup value
   */
  Base.prototype.name = SectionTitle.prototype.name = function(property) { 
    return ko.utils.unwrapObservable(this.data.lookup) + '-' + property; 
  };
  
  /** @method Base.expand
  *   Expands a setting on icon click
  */
  Base.prototype.expand = function() {
    this.expanded(!this.expanded());
  };
  
  /** @method Base.getData & SectionTitle.getData
   *  @returns raw JS data of the setting
   */
  Base.prototype.getData = SectionTitle.prototype.getData = function() {
    var result = {}, unwrappedProp;
    for (var prop in this.data) {
      unwrappedProp = ko.utils.unwrapObservable(this.data[prop]);
      if (/boolean|number/.test(typeof unwrappedProp) || (unwrappedProp && unwrappedProp.length))
        result[prop] = this.data[prop];
    }
    return result;
  };
  
  /** @constructor Select
   *  @description Provides the basis for settings with index values (options): select, radio, icon-radio
   */
  var Select = function(opts) { 
    var self = this;
    Base.call(this, opts || {});
    this.data.options = ko.observableArray(opts.options);
    this.optionsMap = ko.pureComputed(function() {
      var opts = self.data.options()
        , obj = {};
      for (var i = 0; i < opts.length; i++) 
        obj[opts[i]] = i;
      return obj;
    });
    this.optionValue = function(item) { return self.optionsMap()[item]; };
    this.optionText = function(item) { return item; };
  };
  
  Select.prototype = Object.create(Base.prototype);
  Select.constructor = Select;
  
  /** @constructor Text
   *  @description Provides the basis for settings with potentially multilingual values: text, textarea
   *  @extends Base
   */
  var Text = function(opts) {
    Base.call(this, opts || {});
    var self = this;
    this.data.i18n = ko.observableArray();
    ko.utils.arrayForEach(globals.GS.config.i18nLangs, function(item, i) {
      self.data.i18n.push(ko.observable(opts.i18n && opts.i18n[i] ? opts.i18n[i] : ''));
    });
    this.activeLang = ko.observable(opts.i18n ? 0 : -1);
    this.i18n = ko.pureComputed({
      read: function() { return this.activeLang() > -1 ? true : false },
      write: function(value) { 
        var newArr = [];
        if (value) {
          for (var i = 0; i < globals.GS.config.i18nLangs.length; i++) {
            if (!newArr[i])
              newArr[i] = ko.observable(this.data.value());
            newArr[i](this.data.value());
          }
        // if the setting is reverted to non-i18n, set value to the first language's value
        } else
          this.data.value(this.data.i18n()[0]);
        this.data.i18n(newArr);
        this.activeLang(value ? 0 : -1); 
      } 
    }, this);
  };
  Text.prototype = Object.create(Base.prototype);
  Text.constructor = Text;
  
  /** @method Text.getData
   *  @descr overwrites Base.getData to remove i18n values in case the setting is uni-lingual
   */
  Text.prototype.getData = function() {
    var result = Base.prototype.getData.call(this);
    result.i18n = ko.toJS(this.data.i18n);
    
    if (result.i18n.join('').length === 0)
      delete result.i18n;
    else if (result.i18n.length) {
      result.value = result.i18n[0];
    }
    return result;
  };
  
  /** @constructor Checkbox
   *  @extends Base
   */
  var Checkbox = function(opts) {
    Base.call(this, opts || {});
    this.icon = ko.observable(opts.type.indexOf('icon') > -1 ? true : false);
    this.template.manage = ko.pureComputed(function() {
      return (this.icon() ? 'icon-' : '') + this.data.type + '-manage' ;
    }, this);
  };
  Checkbox.prototype = Object.create(Base.prototype);
  Checkbox.constructor = Checkbox;
  
  Checkbox.prototype.toggleValue = function() { this.data.value(!this.data.value()); };
  
  /** @constructor Color
   *  @extends Base
   */
  var Color = function(opts) {
    Base.call(this, opts || {});
    this.template.edit = 'base-edit';
    this.colorBox = ko.pureComputed({
	    read: function() {
	      var value = this.data.value().trim(), temp;
	      // handle empty color value
	      if (!value.length)
	        return '#FFFFFF';
	      // handle 3-digit hex colors
	      if (value.indexOf('#') > -1 && value.length === 4) {
	        temp = value.replace('#', '').split('');
	        return '#' + temp[0] + temp[0] + temp[1] + temp[1] + temp[2] + temp[2];
	      }
	      // handle named web colors
	      if (webcolors.hasOwnProperty(value))
	        return webcolors[value];
	      // handle rgb colors
	      if (value.match('rgb'))
	        return rgbToHex(value);
	      return value;
	    }, 
	    write: function(value) {
	      this.data.value(value.toUpperCase())
	    }
	  }, this);
  };
  Color.prototype = Object.create(Base.prototype);
  Color.constructor = Color;
  
  
  /** @constructor Date
   *  @extends Base
   */
  var DateSetting = function(opts) {
    var self = this
      , _value = ko.observable(new Date(opts.value).getTime()/1000 || null);
    Base.call(this, opts || {});
    this.template.edit = 'base-edit';
    this.data.value = ko.pureComputed({
      read: function() {
        return _value() ? self.yyyymmdd(new Date(_value()*1000)) : null;
      },
      write: function(value) {
        _value(value ? new Date(value).getTime()/1000 : null);
      }
    });
  };
  DateSetting.prototype = Object.create(Base.prototype);
  DateSetting.constructor = DateSetting;
  
  DateSetting.prototype.yyyymmdd = function(date) {
   var yyyy = date.getFullYear().toString();
   var mm = (date.getMonth()+1).toString(); // getMonth() is zero-based
   var dd  = date.getDate().toString();
   return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]); // padding
  };
  
  /** @constructor Image
   *  @extends Base
   */
  var ImageS = function(opts) {
    Base.call(this, opts || {});
    this.template.edit = 'base-edit';
  };
  ImageS.prototype = Object.create(Base.prototype);
  ImageS.constructor = ImageS;
  
  ImageS.prototype.toggleImageBrowser = function() { 
    state.imgBrowserSetting = this;
    state.imgBrowserActive(true); 
  };
  
  /** @constructor Radio
   *  @extends Select
   */
  var Radio = function(opts) {
    Select.call(this, opts || {});
    this.data.options = ko.observableArray(opts.options);
    this.icon = ko.observable(opts.type.indexOf('icon') > -1 ? true : false);
    this.template.manage = ko.pureComputed(function() {
      return (this.icon() ? 'icon-' : '') + 'radio-manage' ;
    }, this);
  };
  Radio.prototype = Object.create ? Object.create(Select.prototype) : Select.prototype;
  Radio.constructor = Radio;
  
  var exports = {
      'text'    : Text
    , 'textarea': Text
    , 'date'    : DateSetting
    , 'image'   : ImageS
    , 'color'   : Color
    , 'checkbox': Checkbox
    , 'switch'  : Checkbox
    , 'select'  : Select
    , 'radio'   : Radio
    , 'icon-radio': Radio
    , 'icon-checkbox': Checkbox
    , 'section-title': SectionTitle
  };
  
  return exports;
  
});
