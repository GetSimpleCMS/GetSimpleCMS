<?php
  global $SITEURL;
  require_once(GSPLUGINPATH.'hitcount/indexer.class.php');
  require_once(GSPLUGINPATH.'hitcount/reader.class.php');
  require_once(GSPLUGINPATH.'hitcount/viewer.class.php');
  if (isset($_GET['reindex'])) {
    $dir = @opendir(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR);
    if ($dir) {
      while ($filename = readdir($dir)) {
        if (substr($filename,0,5) == 'index') unlink(GSDATAOTHERPATH . HITCOUNT_INDEX_DIR . $filename);
      }
      closedir($dir);
    }
  } else if (isset($_GET['download'])) {
    require_once(GSPLUGINPATH.'hitcount/country.class.php');
    HitcountCountries::retrieve();
  }
  HitcountIndexer::index(); 
  # get parameters from request
  $isBlacklisted = (@$_COOKIE[HITCOUNT_BLACKLIST_COOKIE] || isset($_GET['setcookie'])) && !isset($_GET['delcookie']);
  $hasDetails = false;
  $stacked = @$_REQUEST['stacked'];
  $relative = @$_REQUEST['relative'];
  $details = isset($_REQUEST['details']);
  $type = @$_REQUEST['type'] == 'visits' ? 'visits' : 'hits';
  if (isset($_REQUEST['tab']) && $_REQUEST['tab'] != 'slug') {
    $currTab = $_REQUEST['tab'];
    $hasDetails = in_array($currTab,array('browser','lang','os'));
    $chartName = $currTab.($details && $hasDetails ? '_d' : '');
    $names = array($chartName => null);
  } else {
    $currTab = $chartName = 'slug';
    $names = array('total' => null, 'slug' => null);
  }
  $from = @$_REQUEST['from'];
  $to = @$_REQUEST['to'];
  # read data
  $reader = new HitcountReader($from, $to);
  $reader->read($names);
  $reader->sort();
  $unit = $reader->getUnit();
  $dates = $reader->getDates();
  $hits = $reader->getHits();
  $visits = $reader->getVisits();
  # export link
  $exportParams = 'tab='.$currTab;
  if ($from) $exportParams .= '&from='.$from;
  if ($to) $exportParams .= '&to='.$to;
  if ($details) $exportParams .= '&details';
  # calculate maximum
  $maxHits = $maxVisits = 0;
  foreach ($hits as $name => &$values) {
    foreach ($values as $value => &$numbers) {
      if ($numbers['total'] > $maxHits) $maxHits = $numbers['total'];
    }
  }
  foreach ($visits as $name => &$values) {
    foreach ($values as $value => &$numbers) {
      if ($numbers['total'] > $maxVisits) $maxVisits = $numbers['total'];
    }
  }
  # determine what to show in the chart
  $chartNames = null;
  if (isset($_REQUEST['data'])) {
    foreach ($_REQUEST['data'] as $nav) {
      $pos = strpos($nav,' ');
      $name = substr($nav,0,$pos);
      $value = substr($nav,$pos+1);
      if (isset($hits[$name][$value])) $chartNames[$name][$value] = true;
    }
  }
  if (!$chartNames) {
    if ($type == 'visits') $values =  &$visits[$chartName]; else $values = &$hits[$chartName];
    if (!$values) $values = array();
    foreach ($values as $value => &$numbers) {
      $chartNames[$chartName][$value] = true;
      if (count($chartNames[$chartName]) >= 3) break;
    }
  } 
  # number of values per row
  $n = count($dates);
  $w = (int) (130/$n);
  # for form:
  $maxDays = round(($reader->getMaxDate() - $reader->getMinDate())/(3600*24));
  $fromDays = max(0, round(($reader->getFromDate() - $reader->getMinDate())/(3600*24)));
  $toDays = min($maxDays, round(($reader->getToDate() - $reader->getMinDate())/(3600*24)));
  $jsFromDate =  HitcountViewer::jsDate(max($reader->getFromDate(),$reader->getMinDate()));
  $jsToDate = HitcountViewer::jsDate(min($reader->getToDate(),$reader->getMaxDate()));
  $jsMinDate = HitcountViewer::jsDate($reader->getMinDate());
  $jsMaxDate = HitcountViewer::jsDate($reader->getMaxDate());
  $titles = array();
  # country names
  if ($currTab == 'country') {
    $clines = @file(GSDATAOTHERPATH.'countries.txt');
    if ($clines) foreach ($clines as $cline) {
      if (isset($hits['country'][substr($cline,0,2)])) $titles[substr($cline,0,2)] = trim(substr($cline,3));
    }
  }
?>
  <h3 class="floated" style="float:left"><?php i18n('hitcount/TITLE_'.strtoupper($currTab)); ?></h3>
  <div class="edit-nav">
    <?php foreach (array('country','lang','browser','os','duration','referer','slug') as $tab) { ?>
    <a href="load.php?id=hitcount&amp;tab=<?php echo $tab; ?>&amp;reset" <?php if ($currTab == $tab) echo 'class="current"'; ?> >
      <?php i18n('hitcount/TAB_'.strtoupper($tab)); ?>
    </a>
    <?php } ?>
    <div class=\"clear\" style="clear:both"></div>
  </div>
  <div id="chart" style="width:100%;height:250px;margin-bottom:10px"></div>
  <form action="load.php?id=hitcount&amp;tab=<?php echo $currTab; ?>" method="POST">
    <input type="hidden" name="from" value=""/>
    <input type="hidden" name="to" value=""/>
    <input type="text" name="fromDate" id="fromDate" value="" style="float:left;width:80px;margin-bottom:5px;"/>
    <div id="dateRange" style="float:left;width:440px;margin:5px 15px;"> &nbsp; </div>
    <input type="text" name="toDate" id="toDate" value="" style="float:right;width:80px;margin-bottom:5px;"/>
    <input type="checkbox" name="relative" value="on" <?php if ($relative) echo 'checked="checked"'; ?> style="clear:both;float:left;margin:5px 5px 5px 0;"/>
    <div style="float:left;margin:5px 40px 5px 0;"><?php i18n('hitcount/RELATIVE'); ?></div>
    <input type="checkbox" name="stacked" value="on" <?php if ($stacked) echo 'checked="checked"'; ?> style="float:left;margin:5px 5px 5px 0;"/>
    <div style="float:left;margin:5px 40px 5px 0;"><?php i18n('hitcount/STACKED'); ?></div>
    <input type="radio" name="type" value="hits" <?php if ($type != "visits") echo 'checked="checked"'; ?> style="float:left;margin:5px 5px 5px 0;"/>
    <div style="float:left;margin:5px 20px 5px 0;"><?php i18n('hitcount/HITS'); ?></div>
    <input type="radio" name="type" value="visits" <?php if ($type == "visits") echo 'checked="checked"'; ?> style="float:left;margin:5px 5px 5px 0;"/>
    <div style="float:left;margin:5px 40px 5px 0;"><?php i18n('hitcount/VISITS'); ?></div>
    <?php if ($hasDetails) { ?>
    <input type="checkbox" name="details" value="on" <?php if ($details) echo 'checked="checked"'; ?> style="float:left;margin:5px 5px 5px 0;"/>
    <div style="float:left;margin:5px 40px 5px 0;"><?php i18n('hitcount/DETAILS'); ?></div>
    <?php } ?>
    <input type="submit" name="show" value="<?php i18n('hitcount/REFRESH'); ?>" style="float:right;"/>
    <div style="clear:both;margin-bottom:10px;"></div>
    <table class="edittable highlight">
      <tbody>
        <?php foreach ($hits as $name => &$values) { ?>
        <tr>
          <th colspan="2"><?php i18n('hitcount/'.strtoupper($name)); ?></th>
          <th><?php i18n('hitcount/HITS_OVER_TIME'); ?></th>
          <th><?php i18n('hitcount/VISITS_OVER_TIME'); ?></th>
          <th><?php i18n('hitcount/HITS'); ?></th>
          <th><?php i18n('hitcount/VISITS'); ?></th>
        </tr>
        <?php   foreach ($values as $value => &$numbers) { ?>
        <?php     $vnumbers = &$visits[$name][$value]; ?>
        <tr>
          <td class="secondarylink">
            <input type="checkbox" name="data[]" value="<?php echo htmlspecialchars($name).' '.htmlspecialchars($value); ?>" 
                    <?php if (@$chartNames[$name][$value]) echo 'checked="checked"'; ?> style="margin-top:3px;"/>
          </td>
          <td title="<?php echo htmlspecialchars(@$titles[$value]); ?>">
            <?php echo substr($value,0,1) == '_' ? i18n_r('hitcount/V'.strtoupper($value)) : htmlspecialchars($value); ?>
          </td>
          <td style="width:<?php echo $n*$w; ?>px;">
            <?php HitcountViewer::drawBars($numbers,$dates,$unit,$w,$n,20,i18n_r('hitcount/HITS')); ?>
          </td>
          <td style="width:<?php echo $n*$w; ?>px;">
            <?php HitcountViewer::drawBars($vnumbers,$dates,$unit,$w,$n,20,i18n_r('hitcount/VISITS')); ?>
          </td>
          <td style="width:10%;text-align:right;">
            <?php HitcountViewer::drawNumberAndBar($numbers['total'],$maxHits); ?>
          </td>
          <td style="width:10%;text-align:right;">
            <?php HitcountViewer::drawNumberAndBar($vnumbers['total'],$maxVisits); ?>
          </td>
        </tr>
        <?php   } ?>
        <?php } ?>
      </tbody>  
    </table>
    <?php if ($currTab == 'country') { ?>
    <p>This script uses the IP-to-Country Database provided by 
      <a href="http://www.webhosting.info">WebHosting.Info</a>,
      available from <a href="http://ip-to-country.webhosting.info">http://ip-to-country.webhosting.info</a>.
    </p>
    <div style="display:none;position:fixed;top:0;left:0">
      <div id="countries-dialog">
        <embed id="svg" src="<?php echo $SITEURL; ?>plugins/hitcount/svg/countries.svg" style="width:920px;height:460px" />
        <script type="text/javascript">
          // <![CDATA[
          var svg; 
          //var colors = ['#FFFFFF','#BDE3C4','#A4DBAE','#8CD199','#78C787','#61BA72','#4EAA5F','#3F924E','#2E7E3C','#266A32','#1D5226'];
          var hits = <?php echo json_encode($hits['country']); ?>;
          var visits = <?php echo json_encode($visits['country']); ?>;
          var titles = <?php echo json_encode($titles); ?>;
          function setupSVG() {
            svg = document.getElementById("svg").getSVGDocument();
            colorizeSVG(hits,"total");
          }
          function colorizeSVG(values, index) {
            var total = max = 0;
            for (var value in values) {
              total += values[value][index];
              if (values[value][index] > max) max = values[value][index];
            }
            var r, g, b, c, p, s;
            for (var value in values) {
              var e = svg.getElementById(value);
              if (e) {
                var v = values[value][index];
                if (v > 0) {
                  p = Math.floor(100*v/total);
                  r = b = 240 - Math.floor(240*v/max);
                  g = 245 - Math.floor(130*v/max);
                  c = "rgb("+r+","+g+","+b+")";
                  e.setAttribute("fill", c);
                  e.setAttribute("title", value+" - "+titles[value]+": "+v+" ("+p+"%)");
                } else {
                  e.setAttribute("fill", '#FFFFFF');
                  e.setAttribute("title", '');
                }
              }              
            }
          }
          $(function() {
            $('a#show-countries-dialog').fancybox({
              'centerOnScroll': true
            });
          });
          // ]]>
        </script>
      </div>
    </div>
    <?php } ?>
    <p class="submitline">
      <a class="cancel" href="<?php echo $SITEURL; ?>plugins/hitcount/export/csv.php?<?php echo htmlspecialchars($exportParams); ?>"><?php i18n('hitcount/EXPORT_CSV'); ?></a>
      &nbsp;
      <a class="cancel" href="<?php echo $SITEURL; ?>plugins/hitcount/export/csv.php?excel&amp;<?php echo htmlspecialchars($exportParams); ?>"><?php i18n('hitcount/EXPORT_EXCEL'); ?></a>
      <?php if ($currTab == 'country') { ?>
      &nbsp;
      <a class="cancel" id="show-countries-dialog" href="#countries-dialog"><?php i18n('hitcount/SHOW_WORLDMAP'); ?></a>
      <?php } ?>
    </p>
    <p class="submitline">
      <a class="cancel" href="load.php?id=hitcount&amp;reindex"><?php i18n('hitcount/REINDEX'); ?></a>
      &nbsp;
      <a class="cancel" href="load.php?id=hitcount&amp;download"><?php i18n('hitcount/DOWNLOAD_IP2COUNTRY'); ?></a>
      &nbsp;
      <?php if ($isBlacklisted) { ?>
        <a class="cancel" href="load.php?id=hitcount&amp;delcookie"><?php i18n('hitcount/DEL_COOKIE'); ?></a>
      <?php } else { ?>
        <a class="cancel" href="load.php?id=hitcount&amp;setcookie"><?php i18n('hitcount/SET_COOKIE'); ?></a>
      <?php } ?>
    </p>
  </form>
  <p style="text-align:center">&copy; 2011 Martin Vlcek - Please consider a <a href="http://mvlcek.bplaced.net/">Donation</a></p>
  <script type="text/javascript">
    var minDate = <?php echo $jsMinDate; ?>;
    var maxDate = <?php echo $jsMaxDate; ?>;
    function sliderChange(event,ui) {
      var fromDate = new Date(minDate.getTime() + ui.values[0]*1000*3600*24);
      var toDate = new Date(minDate.getTime() + ui.values[1]*1000*3600*24);
      $('#fromDate').datepicker('setDate',fromDate).datepicker('option','maxDate',toDate);
      $('#toDate').datepicker('setDate',toDate).datepicker('option','minDate',fromDate);
    }
    $(function() {
      $('#fromDate').datepicker({
        dateFormat:'<?php i18n('hitcount/DATE_FORMAT_JQUI'); ?>', minDate:minDate, maxDate:<?php echo $jsToDate; ?>, 
        altField:'[name=from]', altFormat:'yymmdd'
      }).datepicker('setDate', <?php echo $jsFromDate; ?>).change(function() {
        var fromDate = $('#fromDate').datepicker('getDate');
        var fromDays = Math.round((fromDate.getTime() - minDate.getTime())/(1000*3600*24));
        $('#dateRange').slider('values',0,fromDays);
        $('#toDate').datepicker('option', 'minDate', fromDate);
      });
      $('#toDate').datepicker({
        dateFormat:'<?php i18n('hitcount/DATE_FORMAT_JQUI'); ?>', minDate:<?php echo $jsFromDate; ?>, maxDate:maxDate, 
        altField:'[name=to]', altFormat:'yymmdd'
      }).datepicker('setDate', <?php echo $jsToDate; ?>).change(function() {
        var toDate = $('#toDate').datepicker('getDate');
        var toDays = Math.round((toDate.getTime() - minDate.getTime())/(1000*3600*24));
        $('#dateRange').slider('values',1,toDays);
        $('#fromDate').datepicker('option', 'maxDate', toDate);
      });
      $('#dateRange').slider({
        min:0, max:<?php echo $maxDays; ?>, values:[<?php echo $fromDays; ?>,<?php echo $toDays; ?>], range:true,
        slide: function(event,ui) {
          if (ui.values[0] > ui.values[1]) return false;
          sliderChange(event,ui);
        },
        change: sliderChange
      });
    });
  </script>
<?php
  if ($relative && (!isset($hits['total']['_TOTAL']) || !isset($hits['total']['_HUMAN']))) {
    $reader->read(array('total' => null));
  }
  if ($type == 'visits') $data = $reader->getVisits(); else $data = $reader->getHits();
  $series = array();
  if ($relative) {
    if (isset($chartNames['total'])) $baseline = &$data['total']['_TOTAL']; else $baseline = &$data['total']['_HUMAN'];
    foreach ($chartNames as $name => &$values) {
      foreach ($values as $value => $dummy) {
        $numbers = &$data[$name][$value];
        $seriesName = substr($value,0,1) == '_' ? i18n_r('hitcount/V'.strtoupper($value)) : $value;
        for ($i=0; $i<count($dates); $i++) {
          $series[$seriesName][$i] = $baseline[$i] <= 0 ? 0 : 100*$numbers[$i]/$baseline[$i];
        }
      }
    }
  } else if ($chartNames) {
    foreach ($chartNames as $name => &$values) {
      foreach ($values as $value => $dummy) {
        $numbers = &$data[$name][$value];
        $seriesName = substr($value,0,1) == '_' ? i18n_r('hitcount/V'.strtoupper($value)) : $value;
        for ($i=0; $i<count($dates); $i++) {
          $series[$seriesName][$i] = $numbers[$i];
        }
      }
    }
  }
  if ($stacked) {
    $names = array_keys($series);
    for ($k=count($names)-1; $k>0; $k--) {
      $srcNumbers = &$series[$names[$k]];
      $dstNumbers = &$series[$names[$k-1]];
      for ($i=0; $i<count($dates); $i++) {
        $dstNumbers[$i] += $srcNumbers[$i];
      }      
    }
  }
  HitcountViewer::drawChart('chart',$series,$dates,$unit,i18n_r('hitcount/'.strtoupper($type)).($relative?' (%)':''));
  