<?php

function translate_get_plugins() {
  $plugins = array();
  $dir_handle = @opendir(GSPLUGINPATH);
  while ($filename = readdir($dir_handle)) {
    if (!is_dir(GSPLUGINPATH . $filename) && preg_match('/^(.*)\.php$/', $filename, $match)) $plugins[] = $match[1];
  }
  closedir($dir_handle);
  return $plugins;
}

function translate_get_php_files($plugin) {
  if ($plugin) {
    $files = array(GSPLUGINPATH.$plugin.'.php');
    $dir = GSPLUGINPATH.$plugin.'/';
  } else { // GetSimple
    $files = array(GSROOTPATH.'index.php');
    $dir = GSADMINPATH;
  }
  $dir_handle = @opendir($dir);
  if ($dir_handle) {
    while ($filename = readdir($dir_handle)) {
      if (!is_dir($dir.$filename) && preg_match('/^(.*)\.php$/', $filename)) $files[] = $dir.$filename;
    }
    closedir($dir_handle);
  }
  return $files;
}

function translate_get_keys_from_php_files($plugin, $files) {
  $prefix = $plugin ? '[^\'\"\/]+\/' : '';
  $keys = array();
  foreach ($files as $file) {
    $content = file_get_contents($file);
    if (preg_match_all('/\$i18n\[[\'"]'.$prefix.'([^\'\"\/]+)[\'"]\]/', $content, $matches)) {
      foreach ($matches[1] as $key) if (!in_array($key, $keys)) $keys[] = $key;
    } 
    if (preg_match_all('/\i18n(?:_r)?\([\'"]'.$prefix.'([^\'\"\/]+)[\'"]\)/', $content, $matches)) {
      foreach ($matches[1] as $key) if (!in_array($key, $keys)) $keys[] = $key;
    } 
  }
  return $keys;
}

function translate_get_languages($plugin) {
  $languages = array();
  $dir = $plugin ? GSPLUGINPATH.$plugin.'/lang/' : GSLANGPATH;
  $dir_handle = @opendir($dir);
  if ($dir_handle) {
    while ($filename = readdir($dir_handle)) {
      if (!is_dir($dir.$filename) && preg_match('/^(.*)\.php$/', $filename, $match)) $languages[] = $match[1];
    }
    closedir($dir_handle);
  }
  return $languages;
}

function translate_load_language($plugin, $language) {
  $i18n = array();
  if ($plugin) {
    @include(GSPLUGINPATH.$plugin.'/lang/'.$language.'.php');
  } else { // GetSimple
    @include(GSLANGPATH.$language.'.php');
  }
  return $i18n;
}

function translate_save_language($plugin, $language, $texts) {
  $file = $plugin ? GSPLUGINPATH.$plugin.'/lang/'.$language.'.php' : GSLANGPATH.$language.'.php';
  if (file_exists($file)) {
    if (!copy($file, GSBACKUPSPATH . 'translate/' . $plugin . '_' . $language . '.bak')) return false;
  }
  if ($plugin) {
    $f = fopen(GSPLUGINPATH.$plugin.'/lang/'.$language.'.php', "w");
  } else { // GetSimple
    $f = fopen(GSLANGPATH.$language.'.php', "w");
  }
  if (!$f) return false;
  if (!fputs($f, "<?php\n")) return false;
  fputs($f, "\$i18n = array(\n");
  $first = true;
  $mq = get_magic_quotes_gpc() || get_magic_quotes_runtime();
  foreach ($texts as $key => $text) {
    $k = str_replace("'","\'",$mq ? stripslashes($key) : $key);
    $t = str_replace('"','\"',$mq ? stripslashes($text) : $text);
    if ($first) {
      fputs($f, "    '$k' => \"$t\"\n");
      $first = false;
    } else {
      fputs($f, "  , '$k' => \"$t\"\n");
    }
  }
  fputs($f, ");");
  fclose($f);
  return true;
}

function translate_undo($plugin, $language) {
  $file = $plugin ? GSPLUGINPATH.$plugin.'/lang/'.$language.'.php' : GSLANGPATH.$language.'.php';
  return copy(GSBACKUPSPATH . 'translate/' . $plugin . '_' . $language . '.bak', $file);
}

  global $plugin_info;
  if (!isset($_POST['save']) && isset($_GET['undo'])) {
    $plugin = @$_GET['plugin'];
    $targetlang = @$_GET['target'];
    if (translate_undo($plugin, $targetlang)) {
      $msg = i18n_r('translate/UNDO_SUCCESS');
      $success = true;
    } else {
      $msg = i18n_r('translate/UNDO_FAILURE');
    }
  }  
  if (isset($_POST['save']) && isset($_POST['plugin']) && @$_POST['target']) {
    $plugin = @$_POST['plugin'];
    $sourcelang = @$_POST['source'];
    $targetlang = @$_POST['target'];
    $sourcetexts = translate_load_language($plugin, $sourcelang);
    $files = translate_get_php_files($plugin);
    $keys = translate_get_keys_from_php_files($plugin, $files);
    sort($keys);
    if (count($keys) > 0) foreach ($keys as $key) if (!array_key_exists($key, $sourcetexts)) $sourcetexts[$key] = '';
    $targettexts = array();
    if (count($sourcetexts) > 0) foreach ($sourcetexts as $key => $text) {
      if (isset($_POST['text_'.$key]) && $_POST['text_'.$key]) $targettexts[$key] = $_POST['text_'.$key];
    }

    $dir = GSBACKUPSPATH . 'translate/';
    // create directory if necessary
    if (!file_exists($dir)) {
      @mkdir(substr($dir,0,strlen($dir)-1), 0777);
      $fp = @fopen($dir . '.htaccess', 'w');
      if ($fp) {
        fputs($fp, 'Deny from all');
        fclose($fp);
      }
    }

    if (translate_save_language($plugin, $targetlang, $targettexts)) {
      $msg = i18n_r('translate/SAVE_SUCCESS').' <a href="load.php?id=translate&undo&plugin='.urlencode($plugin).'&target='.urlencode($targetlang).'">' . i18n_r('UNDO') . '</a>';
      $success = true;
    } else {
      $msg = i18n_r('translate/SAVE_FAILURE');
    }
  }
?>
		<h3 class="floated" style="float:left"><?php echo i18n_r('translate/TRANSLATE_HEADER'); ?></h3>
<?php 
  if (!isset($_REQUEST['translate'])) { 
    $plugins = translate_get_plugins();
    $plugin = @$_REQUEST['plugin'];
?>
    <p class="clear"><?php i18n('translate/TRANSLATE_DESCR'); ?></p>
    <form id="selectPlugin" action="load.php?id=translate" method="post">
    <p><?php i18n('translate/PLUGIN'); ?>
      <select name="plugin">
        <option value="" <?php echo !$plugin ? 'selected="selected"' : ''; ?> >GetSimple</option>
        <?php foreach ($plugins as $p) echo '<option value="'.htmlspecialchars($p).'" '.($p == $plugin ? 'selected="selected"' : '').">".htmlspecialchars(isset($plugin_info[$p]) ? $plugin_info[$p]['name'] : $p)."</option>"; ?>
      </select>
      <input type="submit" name="select" value="<?php i18n('translate/SELECT'); ?>"/>
    </p>
    </form>
    <script type="text/javascript">
      $(function() {
<?php if (isset($msg)) { ?>
        $('div.bodycontent').before('<div class="<?php echo $success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
	      $(".updated, .error").fadeOut(500).fadeIn(500);
<?php } ?>
      });
    </script>
<?php
    if (isset($_REQUEST['plugin'])) {
      $files = translate_get_php_files($plugin);
      $keys = translate_get_keys_from_php_files($plugin, $files);
      $numkeysincode = count($keys);
      $languages = translate_get_languages($plugin);
      if (count($keys) <= 0 && count($languages) <= 0) {
?>
    <p><strong><?php echo isset($plugin_info[$plugin]) ? $plugin_info[$plugin]['name'] : $plugin; ?></strong></p>
    <p><?php i18n('translate/DOES_NOT_SUPPORT'); ?></p>
<?php
      } else {
        sort($languages);
        $texts = array();
        $numtranslated = array();
        if (count($languages) > 0) {
          foreach ($languages as $language) {
            $texts[$language] = translate_load_language($plugin, $language);
            if (count($texts[$language]) > 0) foreach ($texts[$language] as $key => $text) {
              if (!in_array($key,$keys)) $keys[] = $key;
            }
          }
          foreach ($languages as $language) {
            $numtranslated[$language] = 0;
            if (count($keys) > 0) foreach ($keys as $key) if (array_key_exists($key, $texts[$language])) $numtranslated[$language]++;
          }
        }
?>
    <p><strong><?php echo isset($plugin_info[$plugin]) ? $plugin_info[$plugin]['name'] : $plugin; ?></strong></p>
    <p><?php i18n('translate/NUM_KEYS_FOUND_IN_CODE'); ?>: <?php echo $numkeysincode; ?></p>
    <form id="translatePlugin" action="load.php?id=translate" method="post">
    <table class="edittable highlight">
      <thead>
        <th><?php i18n('translate/LANGUAGE'); ?></th>
        <th><?php i18n('translate/NUM_TEXTS'); ?></th>
        <th><?php i18n('translate/NUM_TRANSLATED'); ?></th>
        <th><?php i18n('translate/PERCENTAGE'); ?></th>
        <th><?php i18n('translate/SOURCE'); ?></th>
        <th><?php i18n('translate/TARGET'); ?></th>
      </thead>
      <tbody>
<?php if (count($languages) > 0) foreach($languages as $language) { ?>
        <tr>
          <td><?php echo htmlspecialchars($language); ?></td>
          <td><?php echo count($texts[$language]); ?></td>
          <td><?php echo $numtranslated[$language]; ?><?php i18n('translate/OF'); ?><?php echo count($keys); ?></td>
          <td><?php echo count($keys) > 0 ? (int) (100*$numtranslated[$language]/count($keys)).'%' : '' ?></td>
          <td><input type="radio" name="source" value="<?php echo htmlspecialchars($language); ?>"/></td>
          <td><input type="radio" name="target" value="<?php echo htmlspecialchars($language); ?>"/></td>
        </tr>
<?php } ?>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td><input type="radio" name="source" value="" checked="checked"/> <?php i18n('translate/NONE'); ?></td>
          <td><input type="radio" name="target" value="" checked="checked"/> <?php i18n('translate/NEW'); ?></td>
        </tr>
      </tbody>
    </table>
    <input type="hidden" name="plugin" value="<?php echo htmlspecialchars($plugin); ?>"/>
    <input type="submit" class="submit" name="translate" value="<?php i18n('translate/TRANSLATE'); ?>"/>    
    </form>
<?php
      }
    }
  } else {
    $plugin = @$_POST['plugin'];
    $sourcelang = @$_POST['source'];
    $targetlang = @$_POST['target'];
    $sourcetexts = translate_load_language($plugin, $sourcelang);
    $targettexts = translate_load_language($plugin, $targetlang);
    $files = translate_get_php_files($plugin);
    $keysfound = translate_get_keys_from_php_files($plugin, $files);
    $keys = $keysfound;
    sort($keys);
    if (count($keys) > 0) foreach ($keys as $key) if (!array_key_exists($key, $sourcetexts)) $sourcetexts[$key] = '';
?>
		<div class="edit-nav" >
      <p>
        <?php echo i18n_r('translate/FILTER'); ?>: <input type="text" id="filter" value="" class="text" style="width:80px"/>
        <a id="showMissing" href="#" ><?php i18n('translate/SHOW_MISSING'); ?></a>
        <a id="showAll" href="#" class="current"><?php i18n('translate/SHOW_ALL'); ?></a>
      </p>
      <div class="clear" ></div>
    </div>
    <p><strong><?php echo isset($plugin_info[$plugin]) ? $plugin_info[$plugin]['name'] : $plugin; ?></strong></p>
    <p><?php i18n('translate/GRAY_CODES'); ?></p>
    <form id="translatePlugin" action="load.php?id=translate" method="post">
    <input type="hidden" name="plugin" value="<?php echo htmlspecialchars($plugin); ?>"/>
    <table id="edittrans" class="edittable highlight">
      <thead>
        <th></th>
        <th><?php i18n('translate/CODE'); ?></th>
        <th><?php echo htmlspecialchars($sourcelang); ?> <input type="hidden" name="source" value="<?php echo htmlspecialchars($sourcelang); ?>"/></th>
        <th><input type="text" class="text" style="width:4em;" name="target" value="<?php echo htmlspecialchars($targetlang); ?>"/></th>
      </thead>
      <tbody>
<?php
      $i = 1; 
      if (count($sourcetexts) > 0) foreach ($sourcetexts as $key => $text) { 
?>
        <tr>
          <td style="font-size:70%"><?php echo $i; ?></td>
          <td <?php echo !in_array($key,$keysfound) ? 'style="color:gray;font-size:90%"' : 'style="font-size:90%"'; ?>><?php echo htmlspecialchars($key); ?></td>
          <td><?php echo htmlspecialchars($text); ?></td>
          <td><textarea style="height:inherit; padding:2px; width:220px;" rows="1" class="text" name="text_<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars(@$targettexts[$key]); ?></textarea></td>
        </tr>
<?php
        $i++; 
      } 
?>
      </tbody>
    </table>
    <p id="submitline">
      <input type="submit" class="submit" name="save" value="<?php i18n('translate/SAVE'); ?>"/> 
      &nbsp;&nbsp; <?php i18n('OR'); ?> &nbsp;&nbsp;
      <a class="cancel" href="load.php?id=translate&select&plugin=<?php echo urlencode($plugin); ?>"><?php i18n('CANCEL'); ?></a> 
    </p>
    </form>
    <script type="text/javascript" src="../plugins/translate/js/jquery.autogrow.js"></script>
    <script type="text/javascript">
      function filter() {
        var m = $('#showMissing').hasClass('current');
        var s = $('#filter').val().toLowerCase();
        if (s == '' && !m) {
          $('#edittrans tbody tr').css('display', 'table-row');
        } else if (s == '' && m) {
          $('#edittrans tbody tr textarea').each(function(i,ta) {
            if ($.trim($(ta).val()) == '') $(ta).closest('tr').css('display','table-row');
          });
        } else {
          $('#edittrans tbody tr').each(function(i,tr) {
            var $ta = $(tr).find('textarea');
            if (!m || $.trim($ta.val()) == '') {
              var $td = $(tr).find('td:first').next();
              var found = $td.text().toLowerCase().indexOf(s) >= 0 || 
                          $td.next().text().toLowerCase().indexOf(s) >= 0 || 
                          $ta.val().toLowerCase().indexOf(s) >= 0;
              $(tr).css('display', found ? 'table-row' : 'none');
            }
          });
        }
      }
      function showAll() {
        $('#showMissing').removeClass('current');
        $('#showAll').addClass('current');
        filter();
      }
      function showMissing() {
        $('#edittrans tbody tr textarea').each(function(i,ta) {
          if ($.trim($(ta).val()) != '') $(ta).closest('tr').css('display','none');
        });
        $('#showAll').removeClass('current');
        $('#showMissing').addClass('current');
      }
      $(function() {
        $('#filter').keyup(filter);
        $('#showMissing').click(showMissing);
        $('#showAll').click(showAll);
        $('textarea').autogrow({ expandTolerance:0 });
<?php if (isset($msg)) { ?>
        $('div.bodycontent').before('<div class="<?php echo $success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
	      $(".updated, .error").fadeOut(500).fadeIn(500);
<?php } ?>
      });
    </script>
<?php
  }

