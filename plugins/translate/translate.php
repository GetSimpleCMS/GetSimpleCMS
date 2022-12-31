<?php

function translate_get_plugins() {
  $plugins = array();
  $dir_handle = @opendir(GSPLUGINPATH);
  while ($filename = readdir($dir_handle)) {
    if (!is_dir(GSPLUGINPATH . $filename) && preg_match('/^(.*)\.php$/', $filename, $match)) $plugins[] = $match[1];
  }
  closedir($dir_handle);
  sort($plugins);
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
      if ($filename != '..' && is_dir($dir.$filename)) {
        // second level directory
        $dir2 = $dir.$filename.'/';
        $dir2_handle = @opendir($dir2);
        if ($dir2_handle) {
          while ($filename = readdir($dir2_handle)) {
            if (!is_dir($dir2.$filename) && preg_match('/^(.*)\.php$/', $filename)) {
              $files[] = $dir2.$filename; 
            }
          }
        }
      } else if (preg_match('/^(.*)\.php$/', $filename)) {
        $files[] = $dir.$filename; 
      }
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

function translate_load_stats_from_transifex($plugin) {
  $username = @$_SESSION['transifex_username'];
  $password = @$_SESSION['transifex_password'];
  if (!$username || !$password) return null;
  if ($plugin) {
    $opts = array('http' => array('method'=>'GET','header'=>'Authorization: Basic '.base64_encode("$username:$password")));
    $ctx = stream_context_create($opts);
    $url = "https://www.transifex.net/api/2/project/getsimple_".$plugin."/resource/lang/";
    $handle = @fopen($url, 'r', false, $ctx);
    if ($handle) {
      $result = json_decode(stream_get_contents($handle));
      $sourcelang = $result->source_language_code;
      $url = "https://www.transifex.net/api/2/project/getsimple_".$plugin."/resource/lang/stats/";
      $handle = @fopen($url, 'r', false, $ctx);
      if ($handle) {
        $result = json_decode(stream_get_contents($handle));
        $result->$sourcelang->source = true;
        return $result;
      }
    }
  }
  return null;
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

function translate_load_language_from_transifex($plugin, $language) {
  $username = @$_SESSION['transifex_username'];
  $password = @$_SESSION['transifex_password'];
  if (!$username || !$password) return null;
  $i18n = array();
  if ($plugin) {
    $url = "https://www.transifex.net/api/2/project/getsimple_".$plugin."/resource/lang/translation/".$language."/";
    $opts = array('http' => array('method'=>'GET','header'=>'Authorization: Basic '.base64_encode("$username:$password")));
    $ctx = stream_context_create($opts);
    $handle = @fopen($url, 'r', false, $ctx);
    if ($handle) {
      $json = stream_get_contents($handle);
      $result = json_decode($json);
      $content = "?>".trim($result->content);
      if (substr($content,-2) == '?>') $content .= '<?php';
      eval($content);
    }
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
  fputs($f, "\$i18n = array(");
  $first = true;
  $mq = get_magic_quotes_gpc() || get_magic_quotes_runtime();
  foreach ($texts as $key => $text) {
    $k = str_replace("'","\'",$mq ? stripslashes($key) : $key);
    $t = str_replace('"','\"',$mq ? stripslashes($text) : $text);
    if ($first) {
      fputs($f, "\n    '$k' => \"$t\"");
      $first = false;
    } else {
      fputs($f, ",\n    '$k' => \"$t\"");
    }
  }
  fputs($f, "\n);");
  fclose($f);
  return true;
}

function translate_save_language_to_transifex($plugin, $language) {
  $username = @$_SESSION['transifex_username'];
  $password = @$_SESSION['transifex_password'];
  if (!$username || !$password) return null;
  $i18n = array();
  if ($plugin) {
    $content = file_get_contents(GSPLUGINPATH.$plugin.'/lang/'.$language.'.php');
    $url = "https://www.transifex.net/api/2/project/getsimple_".$plugin."/resource/lang/translation/".$language."/";
    $opts = array('http' => array('method'=>'PUT',
        'header'=>'Authorization: Basic '.base64_encode("$username:$password")."\r\n".
                  'Content-Type: application/json',
        'content'=>json_encode(array('content'=>$content))
    ));
    $ctx = stream_context_create($opts);
    $handle = @fopen($url, 'r', false, $ctx);
    if ($handle) return true;
  }
  return false;
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
  if ((isset($_POST['save']) || isset($_POST['save_transifex'])) && 
      isset($_POST['plugin']) && @$_POST['target']) {
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
      if (isset($_POST['save_transifex'])) {
        if (translate_save_language_to_transifex($plugin, $targetlang)) {
          $msg .= ' '.i18n_r('translate/UPLOAD_SUCCESS');
        }
      }
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
    <style type="text/css">
      .transifex { float:right; text-align:right; }
      .transifex div { display:none; background-color:white; border:solid 1px #C3C3C3; padding:3px; margin-top:5px; }
      .transifex:hover div { display:block }
      .transifex table { width:auto; margin:0; }
      .transifex table input { width:8em; }
      .transifex table td { vertical-align: baseline; }
      .wrapper table tr.subheader th { font-size: 80%; }
    </style>
    <p class="clear"><?php i18n('translate/TRANSLATE_DESCR'); ?></p>
    <form id="selectPlugin" action="load.php?id=translate" method="post">
    <div class="transifex">
      <a href="#"><?php i18n('translate/TRANSIFEX'); ?></a>
      <div>
        <table>
          <tr>
            <td><?php i18n('translate/TRANSIFEX_USERNAME'); ?></td>
            <td><input type="text" class="text" name="transifex_username"/></td>
          </tr>
          <tr>
            <td><?php i18n('translate/TRANSIFEX_PASSWORD'); ?></td>
            <td><input type="password" class="text" name="transifex_password"/></td>
          </tr>
        </table>
      </div>
    </div>
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
        if (@$_POST['transifex_username'] && @$_POST['transifex_password']) {
          $_SESSION['transifex_username'] = stripslashes($_POST['transifex_username']);
          $_SESSION['transifex_password'] = stripslashes($_POST['transifex_password']);
        }
        $stats = array();
        $sourcelang = null;
        if (@$_SESSION['transifex_username'] && @$_SESSION['transifex_password']) {
          $stats = translate_load_stats_from_transifex($plugin);
          if (count($stats) > 0) foreach ($stats as $lang => $stat) {
            if ($stat->translated_entities > 0 && !in_array($lang,$languages)) $languages[] = $lang;
            if (@$stat->source) $sourcelang = $lang;
          }
        }
        $istf = $sourcelang ? true : false;
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
    <p>
      <?php i18n('translate/NUM_KEYS_FOUND_IN_CODE'); ?>: <?php echo $numkeysincode; ?><br />
      <?php i18n('translate/NUM_KEYS_FOUND'); ?>: <?php echo count($keys); ?>
    </p>
    <form id="translatePlugin" action="load.php?id=translate" method="post">
    <input type="hidden" name="transifex_source" value="<?php echo htmlspecialchars($sourcelang); ?>"/>
    <table class="edittable highlight">
      <thead>
        <tr>
          <th><?php i18n('translate/LANGUAGE'); ?></th>
          <th <?php if ($istf) echo 'colspan="2"';?>><?php i18n('translate/NUM_TRANSLATED'); ?></th>
          <th <?php if ($istf) echo 'colspan="2"';?>><?php i18n('translate/PERCENTAGE'); ?></th>
          <th><?php i18n('translate/SOURCE'); ?></th>
          <th <?php if ($istf) echo 'colspan="2"';?>><?php i18n('translate/TARGET'); ?></th>
        </tr>
        <?php if ($istf) { ?>
          <tr class="subheader">
            <th></th>
            <th><?php i18n('translate/LOCAL'); ?></th>
            <th><?php i18n('translate/TRANSIFEX'); ?></th>
            <th><?php i18n('translate/LOCAL'); ?></th>
            <th><?php i18n('translate/TRANSIFEX'); ?></th>
            <th></th>
            <th><?php i18n('translate/LOCAL'); ?></th>
            <th><?php i18n('translate/TRANSIFEX'); ?></th>
          </tr>
        <?php } ?>
      </thead>
      <tbody>
<?php if (count($languages) > 0) foreach($languages as $language) { ?>
        <tr>
          <td><?php echo $language == $sourcelang ? '<b>'.htmlspecialchars($language).'</b>' : htmlspecialchars($language); ?></td>
          <td><?php echo count($texts[$language]); ?></td>
          <?php if ($istf) { ?><td><?php if (isset($stats->$language)) echo $stats->$language->translated_entities; ?></td><?php } ?>
          <td><?php echo count($keys) > 0 ? (int) (100*$numtranslated[$language]/count($keys)).'%' : '' ?></td>
          <?php if ($istf) { ?><td><?php if (count($keys) > 0 && isset($stats->$language)) echo ((int) (100*$stats->$language->translated_entities/count($keys))).'%'; ?></td><?php } ?>
          <td><input type="radio" name="source" value="<?php echo htmlspecialchars($language); ?>"/></td>
          <td><input type="radio" name="target" value="<?php echo ($istf ? (isset($stats->$language) ? 'local_' : 'notransifex_') : '').htmlspecialchars($language); ?>"/></td>
          <?php if ($istf) { ?>
            <td>
              <?php if (isset($stats->$language)) { ?>
              <input type="radio" name="target" value="transifex_<?php echo htmlspecialchars($language); ?>"/>
              <?php } ?>
            </td>
          <?php } ?>
        </tr>
<?php } ?>
        <tr>
          <td></td>
          <td></td>
          <?php if ($istf) { ?><td></td><?php } ?>
          <td></td>
          <?php if ($istf) { ?><td></td><?php } ?>
          <td><input type="radio" name="source" value="" checked="checked"/> <?php i18n('translate/NONE'); ?></td>
          <td><input type="radio" name="target" value="<?php if ($istf && !isset($stats->$language)) echo 'notransifex_'; ?>" checked="checked"/> <?php i18n('translate/NEW'); ?></td>
          <?php if ($istf) { ?><td></td><?php } ?>
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
    if (substr($targetlang,0,10) == 'transifex_') {
      $istf = $savetf = true;
      $targetlang = substr($targetlang,10);
      $targettexts = translate_load_language_from_transifex($plugin, $targetlang);
      $origtexts = translate_load_language($plugin, $targetlang);
      // hack to empty texts that have not been translated yet:
      $comptexts = @translate_load_language($plugin, $_POST['transifex_source']);
      foreach ($comptexts as $key => $value) {
        // if local text is empty and transifex text = transifex source text, assume not translated
        if (!@$origtexts[$key] && @$targettexts[$key] && $targettexts[$key] == $value) {
          unset($targettexts[$key]); 
        }
      }
    } else if (substr($targetlang,0,6) == 'local_') {
      $istf = $savetf = true;
      $targetlang = substr($targetlang,6);
      $targettexts = translate_load_language($plugin, $targetlang);
      $origtexts = translate_load_language_from_transifex($plugin, $targetlang);
      // hack to empty texts that have not been translated yet:
      $comptexts = @translate_load_language($plugin, $_POST['transifex_source']);
      foreach ($comptexts as $key => $value) {
        // if local text is empty and transifex text = transifex source text, assume not translated
        if (!@$targettexts[$key] && @$origtexts[$key] && $origtexts[$key] == $value) {
          unset($origtexts[$key]); 
        }
      }
    } else if (substr($targetlang,0,12) == 'notransifex_') {
      $istf = false;
      $savetf = true;
      $targetlang = substr($targetlang,12);
      $targettexts = translate_load_language($plugin, $targetlang);
    } else {
      $istf = $savetf = false;
      $targettexts = translate_load_language($plugin, $targetlang);
    }
    $files = translate_get_php_files($plugin);
    $keysfound = translate_get_keys_from_php_files($plugin, $files);
    $keys = $keysfound;
    sort($keys);
    if (count($keys) > 0) foreach ($keys as $key) if (!array_key_exists($key, $sourcetexts)) $sourcetexts[$key] = '';
?>
    <style type="text/css">
      tr.hidden { display: none; }
      tr.filtered { display: none; }
    </style>
		<div class="edit-nav" >
      <p>
        <?php echo i18n_r('translate/FILTER'); ?>: <input type="text" id="filter" value="" style="width:80px"/>
        <a id="showMissing" href="#"><?php i18n('translate/SHOW_MISSING'); ?></a>
        <?php if ($istf) { ?>
        <a id="showChanged" href="#"><?php i18n('translate/SHOW_CHANGED'); ?></a>
        <?php } ?>
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
        <th></th>
      </thead>
      <tbody>
<?php
      $i = 1; 
      if (count($sourcetexts) > 0) foreach ($sourcetexts as $key => $text) { 
        $orig = null;
        $bgcolor = null;
        if ($istf && @$origtexts[$key] != @$targettexts[$key]) {
          $orig = @$origtexts[$key];
          $bgcolor = $orig ? '#f0f0a0' : '#a0f0a0';
        };
?>
        <tr <?php if ($bgcolor) echo 'class="changed"'; ?>>
          <td style="font-size:70%"><?php echo $i; ?></td>
          <td <?php echo !in_array($key,$keysfound) ? 'style="color:gray;font-size:90%"' : 'style="font-size:90%"'; ?>><?php echo htmlspecialchars($key); ?></td>
          <td><?php echo htmlspecialchars($text); ?></td>
          <td>
            <textarea style="height:inherit; padding:2px; width:220px;<?php if ($bgcolor) echo ' background-color:'.$bgcolor; ?>" rows="1" 
                      class="text" name="text_<?php echo htmlspecialchars($key); ?>" 
                      title="<?php echo htmlspecialchars(@$orig ? $orig : ''); ?>"><?php echo htmlspecialchars(@$targettexts[$key]); ?></textarea>
          </td>
          <td <?php if ($bgcolor) echo 'class="secondarylink"'; ?>>
            <?php if ($bgcolor) { ?><a href="#" class="copy">C</a><?php } ?>
          </td>
        </tr>
<?php
        $i++; 
      } 
?>
      </tbody>
    </table>
    <p id="submitline">
      <input type="submit" class="submit" name="save" value="<?php i18n('translate/SAVE'); ?>"/> 
      <?php if ($savetf) { ?>
      <input type="submit" class="submit" name="save_transifex" value="<?php i18n('translate/SAVE_TRANSIFEX'); ?>"/>
      <?php } ?> 
      &nbsp;&nbsp; <?php i18n('OR'); ?> &nbsp;&nbsp;
      <a class="cancel" href="load.php?id=translate&select&plugin=<?php echo urlencode($plugin); ?>"><?php i18n('CANCEL'); ?></a> 
    </p>
    </form>
    <script type="text/javascript" src="../plugins/translate/js/jquery.autogrow.js"></script>
    <script type="text/javascript">
      function filter() {
        var s = $('#filter').val().toLowerCase();
        $('#edittrans tbody tr').each(function(i,tr) {
          var $ta = $(tr).find('textarea');
          var $td = $(tr).find('td:first').next();
          var found = $td.text().toLowerCase().indexOf(s) >= 0 || 
                      $td.next().text().toLowerCase().indexOf(s) >= 0 || 
                      $ta.val().toLowerCase().indexOf(s) >= 0;
          if (found) $(tr).removeClass('filtered'); else $(tr).addClass('filtered');
        });
      }
      function showAll(e) {
        if (e) e.preventDefault();
        $('#showMissing, #showChanged').removeClass('current');
        $('#edittrans tbody tr').removeClass('hidden');
        $('#showAll').addClass('current');
      }
      function showChanged(e) {
        if (e) e.preventDefault();
        $('#showAll, #showMissing').removeClass('current');
        $('#edittrans tbody tr').removeClass('hidden').filter(':not(.changed)').addClass('hidden');
        $('#showChanged').addClass('current');
      }
      function showMissing(e) {
        if (e) e.preventDefault();
        $('#showAll, #showChanged').removeClass('current');
        $('#edittrans tbody tr textarea').each(function(i,ta) {
          if ($.trim($(ta).val()) != '') {
            $(ta).closest('tr').addClass('hidden');
          } else {
            $(ta).closest('tr').removeClass('hidden');
          }
        });
        $('#showMissing').addClass('current');
      }
      $(function() {
        $('#filter').focus().keyup(filter);
        $('#showMissing').click(showMissing);
        $('#showChanged').click(showChanged);
        $('#showAll').click(showAll);
        $('textarea').autogrow({ expandTolerance:0 });
        $('a.copy').click(function(e) {
          e.preventDefault();
          var $textarea = $(e.target).closest('tr').find('textarea');
          $textarea.val($textarea.attr('title')).focus();
        });
<?php if (isset($msg)) { ?>
        $('div.bodycontent').before('<div class="<?php echo $success ? 'updated' : 'error'; ?>" style="display:block;">'+<?php echo json_encode($msg); ?>+'</div>');
	      $(".updated, .error").fadeOut(500).fadeIn(500);
<?php } ?>
      });
    </script>
<?php
  }

