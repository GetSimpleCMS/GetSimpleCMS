/**
 * Search module
 * @module modules/search
 */

define(['lib/knockout', 'modules/state'], function(ko, state) {
  
  /** @constructor
   *  @alias modules/search
   *  @param {object} params - component parameters (data = selected tab's settings, searchTerm = stateManager searchFilter)
   */
  function Search(params) {
    var self        = this;
    this.data       = params.data;
    this.minLength  = params.length;
    this.searchTerm = params.search;
    
    // update the view every time the search term changes
    this.searchTerm.subscribe(this.filterSettings.bind(this));
    
    // clear search when tabSelection or mode changes
    state.tabSelection.subscribe(this.filterSettings.bind(this));
    state.mode.subscribe(this.clear.bind(this));
  }
  
  /** @method Search.filterSettings
   *  @descr Filters the current list of settings based on searchterm
   */
  Search.prototype.filterSettings = function() {
    var visibleItems = []
      , string = this.searchTerm().toLowerCase()
      ,  settings = document.querySelectorAll('.setting');
    
    // Handle show/hiding through search filter with regular DOM manipulation.
    // Avoids requiring a JS filter & gives better performance.
    ko.utils.arrayForEach(settings, function(item, i) {
      var data    = ko.dataFor(item);
      if (!data) 
        return;
      var access  = data.data.access && data.data.access()
        , label   = data.data.label().toLowerCase()
        , lookup  = data.data.lookup().toLowerCase();
        
      // don't filter hidden settings
      if (data.data.type !== 'section-title' && access === 'hidden')
        return;
        
      if (label.indexOf(string) > -1 || lookup.indexOf(string) > -1 || string.length < this.minLength) {
        settings[i].removeAttribute('style');
      } else
        settings[i].style.display = 'none';
    });
  };
  
  /** @method Search.clear
   *  @descr Clears the search term (on tab/ mode change)
   */
  Search.prototype.clear = function() {
    this.searchTerm('');  
  };
  
  /* ------  component registration ------ */
  
  ko.components.register('search-module', {
    viewModel: Search,
    template: '<i class="fa fa-search fa-flip-horizontal"></i><input type="text" data-bind="textInput: searchTerm" placeholder="{{ \'label_search\' | i18n }}">'
  });
  
});
