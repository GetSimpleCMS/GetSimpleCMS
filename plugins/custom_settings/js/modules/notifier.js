define(function() {
  
  /** GS Notification service; returns private update function, binds automatically to parent context
   *  @param {string} str - The i18n lookup to get the language string from
   *  @param {string} type - The type of message to display (updated, notify, error)
   *  @param {object} vm - If the viewmodel is not immediate parent, can be bound with this parameter
   */
   
  function Notifier(i18n) {
    var sel,
        fadeTime = 400,
        displayTime = 8, 
        appendTo = '.bodycontent', 
        icons = { 
          updated: 'check', 
          notify: 'info',
          error: 'close'
        };
        
    function update(str, type, prompt, duration) {
      var dispTime = prompt ? displayTime * 4 : displayTime, t;
      if (duration) {
        dispTime = (duration === 'short' ? 2 : (duration === 'long' ? 32 : 10));
      }
      if (t) {
        clearTimeout(t);
      }
      clean();
      sel.append('<i class="fa fa-' + icons[type] + '" style="margin-right: 10px;"></i>');
      if (typeof str === 'string') {
        sel.append(str);
      } else {
        for (var i = 0; i < str.length; i++) {
          sel.append(str[i]);
        }
      }
      if (prompt)
        sel.append('  <a href="javascript:void(0)" style="float: right; margin-left: 5px;">' + i18n('CANCEL') + '</a>');
      sel.append(' <a href="javascript:void(0)" style="float: right;">' + i18n('OK') + '</a>');
      sel.find('a').on('click', function() { sel.fadeOut(fadeTime); clean(); });
      if (prompt) {
        $(sel).children('a').eq(1).on('click', function(e) { if (prompt) prompt(); sel.fadeOut(fadeTime); e.preventDefault(); });
        $(sel).children('a').eq(0).on('click', function(e) { if (prompt) sel.fadeOut(fadeTime); });
      }
      sel.addClass(type);
      sel.fadeIn(fadeTime);
      if (!prompt) {
        t = setTimeout(function() {
          sel.fadeOut(fadeTime);
        }, dispTime*1000);
      }
    }
    function clean() {
      sel.removeClass();
      sel.html('');
    }
    function init() {
      if ($(appendTo).length)
        $(appendTo).before('<div id="notification-manager" style="box-sizing: border-box;"></div>');
      sel = $('#notification-manager');
      sel.hide();
    }
    
    init();
    return update;
  };
  
  return Notifier;
});
