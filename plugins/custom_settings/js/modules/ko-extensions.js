/**
 * Knockout extensions module
 * @module modules/ko-extensions
 * @descr groups a collection of smaller Knockout filters, components and bindings
 */
define(['lib/knockout', 'lib/knockout.punches.min', 'modules/globals'], function(ko, punches, globals) {
  
  var i18n = globals.i18n;
  
   /** @binding onewaySelect
    *  @descr On select, returns to the predefined value and executes parameter action.
    *         Used by Export buttons (in nav & toolbar)
    */
  ko.bindingHandlers.onewaySelect = {
    init: function(element, valueAccessor, allBindings, ctx) {
      var bindingOptions = valueAccessor(),
          currentVal = ko.utils.unwrapObservable(bindingOptions.value),
          action = bindingOptions.action, 
          fn = element.addEventListener ? 'addEventListener' : 'attachEvent';
      element[fn]('change', function(e) {
        var setVal = element.value;
        if (action && typeof action === 'function')
          action.call(ctx, setVal, element.options[element.selectedIndex].getAttribute('data-index'));
        element.selectedIndex = 0;
      }, false);
      
      element.value = currentVal;
    },
    update: function(element, valueAccessor, allBindings, ctx) {
      var bindingOptions = valueAccessor()
        , val = ko.utils.unwrapObservable(bindingOptions.optionsValue)
        , text = ko.utils.unwrapObservable(bindingOptions.optionsText)
        , options = ko.utils.unwrapObservable(bindingOptions.options);
      element.innerHTML = '';
      ko.utils.arrayForEach(options, function(option, i) {
        var optionEl;
          optionEl = document.createElement('option');
          if (option.index > -1)
            optionEl.setAttribute('data-index', option.index);
          optionEl.value = ko.unwrap(option[val]);
          element.appendChild(optionEl);
          // needs to be set afterwards as IE complains about 'invalid arguments'
          optionEl.appendChild(document.createTextNode(ko.unwrap(option[text])));
      });
      element.selectedIndex = 0;
    }
  };
  
  /** @component array-to-text
   *  @descr Live syncs an array with lines in a textarea using a pure computed. Used by options binding in Edit mode.
   */
  ko.components.register('array-to-text', {
    viewModel: function (params) {
      var self = this;
      this.data = params.data;
      this.rows = params.rows || 6;
      this.i18n = params.i18n || '';
      this.view = ko.computed({
        read: function() {
          var str = '', arr = ko.utils.unwrapObservable(this.data) || [];
          ko.utils.arrayForEach(arr, function(item) {
            str += item + '\n';
          });
          return str;
        },
        write: function(value) {
          value = value.trim().replace(/\n\s*/g,'\n');
          var newVal = value.split('\n'), arr = [];
          ko.utils.arrayForEach(newVal, function(item, index) { 
            if (item) {
              arr.push(item.trim());
            }
          });
          if (ko.isObservable(self.data)) 
            this.data(arr);
          else
            this.data = arr;
        }, 
        owner: this
      });
    },
    template: '<textarea data-bind="value: view, i18n: i18n, attr: {rows: rows}"></textarea>'
  });
  
  /** Knockout punches filters
   *  @function ko.filters.capitalize - Capitalizes a string (used for section titles)
   *  @function ko.filters.i18n - Translates a string from GS.i18n by lookup
   */
  ko.filters.capitalize = function(value) {
    var s = ko.utils.unwrapObservable(value);
    return s.length ? s.slice(0,1).toUpperCase() + s.slice(1) : '';
  };
  
  ko.filters.i18n = function(value) {
    if (typeof value === 'function')
      value = ko.unwrap(value);
      return i18n(value);
  };
  
  // activate Knockout Punches
  punches.enableAll();

});
