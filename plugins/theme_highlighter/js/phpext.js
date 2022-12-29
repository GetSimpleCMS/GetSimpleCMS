(function() {
  function keywords(str) {
    var obj = {}, words = str.split(" ");
    for (var i = 0; i < words.length; ++i) obj[words[i]] = true;
    return obj;
  }
  var phpKeywords =
    keywords("abstract and array as break case catch cfunction class clone const continue declare " +
             "default do else elseif enddeclare endfor endforeach endif endswitch endwhile extends " +
             "final for foreach function global goto if implements interface instanceof namespace " +
             "new or private protected public static switch throw try use var while xor");
  var phpConfig = {name: "clike", keywords: phpKeywords, multiLineStrings: true, $vars: true, hashComments: true};

  CodeMirror.defineMode("php", function(config, parserConfig) {
    var htmlMode = CodeMirror.getMode(config, "text/html");
    var jsMode = CodeMirror.getMode(config, "text/javascript");
    var cssMode = CodeMirror.getMode(config, "text/css");
    var phpMode = CodeMirror.getMode(config, phpConfig);
    var styles = parserConfig.styles ? parserConfig.styles : {};

    function dispatch(stream, state) {
      var cur = state.stack[state.stack.length-1];
      if (cur.mode == htmlMode) {
        var style = htmlMode.token(stream, cur.state);
        if (style == "meta" && /^<\?php/.test(stream.current())) {
          state.stack.push({ name:'php', mode:phpMode, state:state.phpState, close:/^\?>/ });
        } else if (style == "tag" && stream.current() == ">" && cur.state.context) {
          if (/^script$/i.test(cur.state.context.tagName)) {
            state.stack.push({ name:'js', mode:jsMode, state:jsMode.startState(htmlMode.indent(cur.state, "")), close:/<\/\s*script\s*>/i }); 
          } else if (/^style$/i.test(cur.state.context.tagName)) {
            state.stack.push({ name:'css', mode:cssMode, state:cssMode.startState(htmlMode.indent(cur.state, "")), close:/<\/\s*style\s*>/i });
          }
        } else if (style == "string") {
          var p1 = stream.current().indexOf('<?php');
          var p2 = p1 >= 0 ? stream.current().indexOf('?>',p1+5) : -1;
          if (p2 >= 0) {
            stream.backUp(stream.current().length-p1);
            state.stack.push({ name:'php', mode:phpMode, state:state.phpState, substate:1, 
                               info:stream.current().substring(0,1), close:null });
          }
        }
        return style;
      } else if (cur.close && stream.match(cur.close, false)) {
        state.stack.pop();
        return dispatch(stream, state);
      } else if (cur.mode == phpMode) {
        if (cur.substate == 1) {
          stream.match(/^<\?php/,true);
          cur.substate = 2;
          return 'meta';
        } else if (cur.substate == 2 && stream.match(/^\?>/,true)) {
          cur.substate = 3;
          return 'meta';
        } else if (cur.substate == 3) {
          var escaped = false, next, end = false;
          while ((next = stream.next()) != null) {
            if (next == cur.info && !escaped) break;
            escaped = !escaped && next == "\\";
          }
          state.stack.pop();
          return 'string';
        } else if (cur.substate == 4 && stream.match(/^\?>/,true)) {
          state.stack.pop();
          return 'meta';
        }
        var style = cur.mode.token(stream, cur.state);
        var style2 = styles[stream.current()];
        return style2 ? style2 : style;
      } else if (stream.match(/^<\?php/,true)) {
        state.stack.push({ name:'php', mode:phpMode, state:state.phpState, substate:4, close:null });
        return 'meta';
      } else {
        return cur.mode.token(stream, cur.state);
      }
    }

    return {
      startState: function() {
        return { phpState: phpMode.startState(),
                 stack: [{ name:'html', mode:htmlMode, state: htmlMode.startState(), substate:null, info:null, close:null }] };

      },

      copyState: function(state) {
        var newStack = [];
        for (var i=0; i<state.stack.length; i++) {
          newStack.push({ name:state.stack[i].name, mode:state.stack[i].mode, 
                          state:CodeMirror.copyState(state.stack[i].mode, state.stack[i].state),
                          substate:state.substate, info:state.info, close:state.stack[i].close });
        }
        return { phpState:CodeMirror.copyState(phpMode, state.phpState), stack:newStack };
      },

      token: dispatch,

      indent: function(state, textAfter) {
        var cur = state.stack[state.stack.length-1];
        if ((cur.mode != phpMode && /^\s*<\//.test(textAfter)) ||
            (cur.mode == phpMode && /^\?>/.test(textAfter)))
          return htmlMode.indent(state.stack[0].state, textAfter);
        return cur.mode.indent(cur.state, textAfter);
      },

      electricChars: "/{}:"
    }
  });
  CodeMirror.defineMIME("application/x-httpd-php", "php");
  CodeMirror.defineMIME("text/x-php", phpConfig);
})();
