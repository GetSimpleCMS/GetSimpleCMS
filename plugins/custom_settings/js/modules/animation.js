/**
 * Animation function
 * @module modules/animation
 * @exports function animate
 */
 
define(function() {
 
  var animation = {}
    , animate = function(action, arg) { animation[action](arg) }
    , toolbarSelector = '#cs-main-toolbar';
  
  /** @function animation.remove-setting
   *  @description Animates setting removal in Knockout foreach beforeRemove callback
   */
  animation['remove-setting'] = function(element, index, array) {
    var setting = $(element)
      , time = 200;
      
    setting.animate(
      { height: '0px' }
      , time
      , function() {  setTimeout(function() { setting.remove();  }, time); }
    );
  };
  
  /** @function animation.insert-setting
   *  @description Animates setting insertion in Knockout foreach afterAdd callback
   */
  animation['insert-setting'] = function(element, index, array) {
    var setting = $(element)
      , time = 200
      , height = 30;
      
    setting.css({
      'height':'0px', 
      'border-bottom-color':'transparent'
    })
    .animate(
      { height: height + 'px' }
      , 0
      , function() {  
        // for some reason the complete callback executes immediately,
        // set manual timeout
        setTimeout(function() {
          setting.removeAttr('style');
        }, time)
      }
    );
  };
  
  /** @function animation.sticky-toolbar
   *  @description Animates the Edit mode toolbar to sticky when GSSTYLE_SBFIXED is set
   *  jQuery.scrollToFixed plugin loaded by GS backend
   *  @see https://github.com/bigspotteddog/ScrollToFixed
   *  @TODO set limit on scrolling & set abs positioning styles
   */
  animation['sticky-toolbar'] = function() {
    var toolbar = $(toolbarSelector);
    $.ScrollToFixed(toolbar);
  };
  
  return animate;
})
