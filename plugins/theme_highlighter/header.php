    <link type="text/css" rel="stylesheet" href="../plugins/theme_highlighter/css/codemirror.css">
    <script type="text/javascript" src="../plugins/theme_highlighter/js/codemirror.js"></script>
    <script type="text/javascript" src="../plugins/theme_highlighter/js/xml.js"></script>
    <script type="text/javascript" src="../plugins/theme_highlighter/js/javascript.js"></script>
    <script type="text/javascript" src="../plugins/theme_highlighter/js/css.js"></script>
    <script type="text/javascript" src="../plugins/theme_highlighter/js/clike.js"></script>
    <script type="text/javascript" src="../plugins/theme_highlighter/js/phpext.js"></script>
    <link type="text/css" rel="stylesheet" href="../plugins/theme_highlighter/css/default.css">
    <script type="text/javascript" src="../plugins/theme_highlighter/js/farbtastic.js"></script>
    <link type="text/css" rel="stylesheet" href="../plugins/theme_highlighter/css/farbtastic.css">
    <style type="text/css">
      .completions, #colorpicker, #colorpreview {
        position: absolute;
        z-index: 9001;
        overflow: hidden;
        -webkit-box-shadow: 2px 3px 5px rgba(0,0,0,.2);
        -moz-box-shadow: 2px 3px 5px rgba(0,0,0,.2);
        box-shadow: 2px 3px 5px rgba(0,0,0,.2);
      }
      .completions select {
        background: #fafafa;
        outline: none;
        border: none;
        padding: 0;
        margin: 0;
        font-family: monospace;
        max-width: 400px;
      }
      .fullscreen {
        display: block;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9000;
        margin: 0;
        padding: 0;
        border: 0px solid #BBBBBB;
        opacity: 1;
        background-color: white;
      }
      #colorpicker {
        display: none;
        border: solid 1px #AAAAAA;
        background-color: white;
      }
      #colorpicker input {
        width: 190px;
      }
      #colorpreview {
        display: none;
        border: solid 1px #AAAAAA;
        background-color: white;
        width: 100px;
        height: 30px;
      }
    </style>
<?php
    if (basename($_SERVER['PHP_SELF']) == 'theme-edit.php') {
?>
    <style type="text/css">
      .CodeMirror-scroll { height: 660px; width: /*635px*/ 100%; border: 1px solid #AAAAAA; }
      #maincontent .main .CodeMirror pre { padding-bottom: 0; overflow: hidden; }
    </style>
<?php
    } else if (basename($_SERVER['PHP_SELF']) == 'load.php' && @$_GET['id'] == 'custom-admin-css') {
?>
    <style type="text/css">
      .CodeMirror-scroll { height: 420px; width: 100%; border: 1px solid #AAAAAA; }
      #maincontent .main .CodeMirror pre { padding-bottom: 0; overflow: hidden; }
    </style>
<?php
    } else {
?>
    <style type="text/css">
      .CodeMirror-scroll { height: 200px; width: 632px; border: 1px solid #AAAAAA; }
      #maincontent .main .CodeMirror pre { padding-bottom: 0; overflow: hidden; }
    </style>
<?php
    }
  

