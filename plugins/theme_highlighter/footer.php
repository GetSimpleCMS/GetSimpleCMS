<div id="colorpicker">
  <div></div>
  <form action="javascript:return false;"><input type="text" id="color" name="color" value="#000000" /></form>
</div>
<div id="colorpreview"></div>
<script type="text/javascript">
  function showSuggestions(editor, suggestions, doReplace) {
    var cur = editor.getCursor(false), token = editor.getTokenAt(cur);
    // Build the select widget
    var complete = document.createElement("div");
    complete.className = "completions";
    var sel = complete.appendChild(document.createElement("select"));
    sel.multiple = true;
    for (var i = 0; i < suggestions.length; ++i) {
      var text = suggestions[i].text;
      var opt = sel.appendChild(document.createElement("option"));
      opt.value = text;
      if (suggestions[i].category) text = suggestions[i].category + ' - ' + text;
      opt.appendChild(document.createTextNode(text));
    }
    sel.firstChild.selected = true;
    sel.size = Math.min(10, suggestions.length);
    var pos = editor.cursorCoords();
    complete.style.left = pos.x + "px";
    complete.style.top = pos.yBot + "px";
    document.body.appendChild(complete);
    // Hack to hide the scrollbar.
    if (suggestions.length <= 10)
      complete.style.width = (sel.clientWidth - 1) + "px";
    var done = false;
    function close() {
      if (done) return;
      done = true;
      complete.parentNode.removeChild(complete);
    }
    function pick() {
      if (doReplace) {
        editor.replaceRange(sel.options[sel.selectedIndex].value, 
          {line: cur.line, ch: token.start}, {line: cur.line, ch: token.end});
      } else {
        editor.replaceRange(sel.options[sel.selectedIndex].value, 
          {line: cur.line, ch: cur.ch});
      }
      close();
      setTimeout(function(){editor.focus();}, 50);
    }
    function again() {
      startAutocomplete(editor);
    }
    $(sel).blur(close);
    $(sel).dblclick(pick);
    $(sel).keydown(function(event) {
      var code = event.keyCode;
      // Enter and space
      if (code == 13 || code == 32) {event.stopPropagation(); pick();}
      // Escape
      else if (code == 27) {event.stopPropagation(); close(); editor.focus();}
      else if (code != 38 && code != 40) {close(); editor.focus(); setTimeout(again, 50);}
    });
    sel.focus();
    // Opera sometimes ignores focusing a freshly created node
    if (window.opera) setTimeout(function(){if (!done) sel.focus();}, 100);
    return true;
  }
  function showColorPicker(editor, doReplace) {
    var cur = editor.getCursor(false), token = editor.getTokenAt(cur);
    var pos = editor.cursorCoords();
    $('#colorpicker').css('left',pos.x + "px").css('top',pos.yBot + "px").show();
    var done = false;
    function close() {
      if (done) return;
      done = true;
      $('#colorpicker').hide();
    }
    function pick() {
      if (done) return;
      if (doReplace) {
        editor.replaceRange($('#colorpicker input').val(), 
          {line: cur.line, ch: token.start}, {line: cur.line, ch: token.end});
      } else {
        editor.replaceRange($('#colorpicker input').val(), 
          {line: cur.line, ch: cur.ch});
      }
      close();
    }
    $('#colorpicker input').focus().val(token.string).blur(pick).keydown(function(e) {
      if (e.keyCode == 27) {
        e.stopPropagation(); 
        close();
        setTimeout(function(){editor.focus();}, 50);
      } else if (e.keyCode == 13) {
        e.stopPropagation(); 
        pick();        
        setTimeout(function(){editor.focus();}, 50);
        return false;
      } 
    });
  }
  function toggleFullscreenEditing(editor) {
      var editorDiv = $(editor.getWrapperElement()).find('.CodeMirror-scroll');
      if (!editorDiv.hasClass('fullscreen')) {
          toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() }
          editorDiv.addClass('fullscreen').height('100%').width('100%');
          $(document.body.parentNode).css('overflow','hidden');
          editor.refresh();
      } else {
          editorDiv.removeClass('fullscreen')
          editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
          editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
          $(document.body.parentNode).css('overflow','auto');
          editor.refresh();
      }
  }
  function showColorPreview(e) {
    var text = $(e.target).text();
    if (/#[0-9a-f]{3}|#[0-9a-f]{6}/i.test(text)) {
      $('#colorpreview').css('left',e.pageX).css('top',e.pageY+10).css('background-color',text).show();
    } else {
      $('#colorpreview').hide();
    }
  }
  function hideColorPreview(e) {
    $('#colorpreview').hide();
  }
<?php if (basename($_SERVER['PHP_SELF']) == 'theme-edit.php') { ?>
  $('h3').after('<p>While editing press Ctrl-Space for suggestions, F11 for fullscreen.</p>');
<?php } else if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'i18n_specialpages') { ?>
  $('.compdiv').prepend('<p>While editing press Ctrl-Space for suggestions, F11 for fullscreen.</p>')
<?php } else if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'custom-admin-css') { ?>
  $('h3').after('<p>While editing press Ctrl-Space for suggestions, F11 for fullscreen.</p>')
<?php } else { ?>
  $('.edit-nav').after('<p>While editing press Ctrl-Space for suggestions, F11 for fullscreen.</p>');
<?php } ?>
</script>
<?php
  function theme_highlighter_load_ac($files) {
    $first = true;
    foreach ($files as $filename => $type) {
      $category = '';
      $f = fopen('../plugins/theme_highlighter/'.$filename, "r");
      while (($line = fgets($f)) !== false) {
        $line = trim($line);
        if (substr($line,0,2) == '//') {
          $category = trim(substr($line,2));
        } else if ($line != '' && (!$category || $category == 'GetSimple' || ($pos = strpos($line,'(')) === false || function_exists(substr($line,0,$pos)))) {
          if (!$first) echo ",\r\n        "; else $first = false;
          echo '{ mode:'.json_encode($type).', text:'.json_encode($line).', category:'.json_encode($category).' }';
        }
      }
      fclose($f);
    }
  }
  function theme_highlighter_load_hl($files) {
    $first = true;
    foreach ($files as $filename => $style) {
      $category = '';
      $f = fopen('../plugins/theme_highlighter/'.$filename, "r");
      while (($line = fgets($f)) !== false) {
        $line = trim($line);
        if ($line != '' && substr($line,0,2) != '//' && preg_match('/^([$A-Za-z0-9_]+)/', $line, $matches)) {
          if (!$first) echo ",\r\n        "; else $first = false;
          echo json_encode($matches[1]) . ':' . json_encode($style);
        }
      }
      fclose($f);
    }
  }

  if (basename($_SERVER['PHP_SELF']) == 'theme-edit.php') {
    $selector = '#codetext';
    $filename = @$_GET['f'];
    if (strrpos($filename,'.css') === strlen($filename)-4) {
      $mode = 'css';
    } else if (strrpos($filename,'.js') === strlen($filename)-3) {
      $mode = 'javascript';
    } else {
      $mode = 'php';
    }
  } else if (basename($_SERVER['PHP_SELF']) == 'components.php') {
    $selector = 'textarea[name^=val]';
    $mode = 'php';
  } else if (basename($_SERVER['PHP_SELF']) == 'edit.php') {
    $selector = 'textarea.cke_source';
    $mode = 'html';
  } else if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'i18n_specialpages') {
    $selector = '.compdiv textarea';
    $mode = 'php';
  } else if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'custom-admin-css') {
    $selector = '#custom_css';
    $mode = 'css';
  }
  if ($mode == 'php') {
?>
    <script type="text/javascript">
      var ac = [
        <?php theme_highlighter_load_ac(array('ac_php_getsimple.txt'=>'php','ac_php_plugins.txt'=>'php','ac_php.txt'=>'php','ac_css.txt'=>'css')); ?> 
      ];
      var hl = {
        <?php theme_highlighter_load_hl(array('ac_php.txt'=>'function','ac_php_getsimple.txt'=>'function-2','ac_php_plugins.txt'=>'function-3')); ?> 
      };
      function startAutocomplete(editor) {
        // We want a single cursor position.
        if (editor.somethingSelected()) return;
        // Find the token at the cursor
        var cur = editor.getCursor(false), token = editor.getTokenAt(cur);
        var mode = token.state.stack[token.state.stack.length-1].name;
        if (mode == 'css' && /^#/.test(token.string)) {
          showColorPicker(editor, true);
        } else {
          var s = /^[a-z_][a-z0-9_-]+$/i.test(token.string) ? $.trim(token.string).toLowerCase() : null;
          var suggestions = [];
          for (var i=0; i<ac.length; i++) {
            if (ac[i].mode == mode && (s == null || s == '' || ac[i].text.substring(0,s.length).toLowerCase() == s)) {
              suggestions.push(ac[i]);
            }
          }
          if (suggestions.length > 0) showSuggestions(editor,suggestions, s != null);
        }
      }

      $(function() {
        $('#colorpicker div').farbtastic('#color');
        $(<?php echo json_encode($selector); ?>).each(function(i,textarea) {
          var editor = CodeMirror.fromTextArea(textarea, {
            lineNumbers: true,
            matchBrackets: true,
            mode: { name:<?php echo json_encode($mode); ?>, styles:hl },
            indentUnit: 2,
            indentWithTabs: true,
            enterMode: "keep",
            tabMode: "shift",
            functions: "substr strlen",
            onKeyEvent: function(editor, e) {
              // Hook into ctrl-space
              if (e.keyCode == 32 && (e.ctrlKey || e.metaKey) && !e.altKey) {
                e.stop();
                return startAutocomplete(editor);
              }
              // Hook into F11
              if ((e.keyCode == 122) && e.type == 'keydown') {
                e.stop();
                return toggleFullscreenEditing(editor);
              }
            },
            onFocus: function(editor, e) {
              editor.refresh();
            }
          });
        });
        $('.CodeMirror-scroll span.cm-atom').live('mouseover',showColorPreview);
        $('.CodeMirror-scroll span.cm-atom').live('mouseout',hideColorPreview);
      });
    </script>
<?php
  } else {
?>
    <script type="text/javascript">
<?php if ($mode == 'css') { ?>
      var ac = [
        <?php theme_highlighter_load_ac(array('ac_css.txt'=>'css')); ?> 
      ];          
      function startAutocomplete(editor) {
        // We want a single cursor position.
        if (editor.somethingSelected()) return;
        // Find the token at the cursor
        var cur = editor.getCursor(false), token = editor.getTokenAt(cur);
        if (/^#/.test(token.string)) {
          showColorPicker(editor, true);
        } else {
          var s = /^[a-z_][a-z0-9_-]+$/i.test(token.string) ? $.trim(token.string).toLowerCase() : null;
          var suggestions = [];
          for (var i=0; i<ac.length; i++) {
            if (s == null || s == '' || ac[i].text.substring(0,s.length).toLowerCase() == s) {
              suggestions.push(ac[i]);
            }
          }
          if (suggestions.length > 0) showSuggestions(editor, suggestions, s != null);
        }
      }
<?php } ?>
      $(function() {
<?php if ($mode == 'css') { ?>
        $('#colorpicker div').farbtastic('#color');
<?php } ?>
        $(<?php echo json_encode($selector); ?>).each(function(i,textarea) {
          var editor = CodeMirror.fromTextArea(textarea, {
            lineNumbers: true,
            matchBrackets: true,
            mode: <?php echo json_encode($mode); ?>,
            indentUnit: 2,
            indentWithTabs: true,
            enterMode: "keep",
            tabMode: "shift",
            onKeyEvent: function(editor, e) {
<?php if ($mode == 'css') { ?>
              // Hook into ctrl-space
              if (e.keyCode == 32 && (e.ctrlKey || e.metaKey) && !e.altKey) {
                e.stop();
                return startAutocomplete(editor);
              }
<?php } ?>
              // Hook into F11
              if ((e.keyCode == 122) && e.type == 'keydown') {
                e.stop();
                return toggleFullscreenEditing(editor);
              }
            }
          });
        });
        $('.CodeMirror-scroll span.cm-atom').live('mouseover',showColorPreview);
        $('.CodeMirror-scroll span.cm-atom').live('mouseout',hideColorPreview);
      });
    </script>
<?php
  }


