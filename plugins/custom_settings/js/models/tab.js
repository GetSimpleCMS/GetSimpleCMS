/**
 * Tab data model
 * @module models/tab
 */
 
 define(['lib/knockout'], function(ko) {

  /** @constructor
   *  @alias module:models/tab
   *  @param opts {object}
   */
   
  function Tab(opts) {
    this.data = {
        lookup : ko.observable(opts.tab.lookup)
      , label  : ko.observable(opts.tab.label)
      ,  type   : opts.tab.type || 'site'
    };
    if (opts.tab.options)
      this.data.options = opts.tab.options;
    if (opts.tab.version)
      this.data.version = opts.tab.version;
    
    // tabs might store additional untracked properties which should be saved as well
    for (var prop in opts.tab) {
      if (!this.data.hasOwnProperty(prop)) 
        this.data[prop] = opts.tab[prop];
    }
  };
  
  /** @method Tab.getData
   *  @descr converts tab data for saving/ exports
   */
  Tab.prototype.getData = function() {
    var result = {};
    for (var prop in this.data) {
      if (!/settings|tab/.test(prop))
        result[prop] = ko.utils.unwrapObservable(this.data[prop]);
    }
    return result;
  };
  
  
  
  return Tab;
});
