/**
 * Globals module
 * @module modules/globals
 * @descr GS loads some libraries by default and instantiates a GS object 
 *        in the head of the document, so it is safe to return them here
 *        Provides access to notifier, jQuery, GS object, and translations
 */
define(['modules/notifier'], function(notifier) {

  var globals = {
      GS      : window.GS || {i18n: {}}
    , i18n    : window.i18n
    , $       : window.jQuery
    , notifier: new notifier(window.i18n)
  };
  
  // the GS object is easier to 'reach' by other modules in window
  globals.GS.notifier = globals.notifier;
  
  // this object is later populated with GSCS' config
  globals.GS.config   = {};
  
  // we need 1 extra string that is composed of 2 translation strings
  globals.GS.i18n['translated_by'] =' ' + globals.GS.i18n['about_translated_by'] + ' ' + globals.GS.i18n['traductors'];
  
  return globals;
});
