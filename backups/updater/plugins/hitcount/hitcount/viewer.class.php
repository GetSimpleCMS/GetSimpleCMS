<?php

class HitcountViewer {
  
  public static function drawBars(&$numbers, &$dates, $unit, $w, $n, $h, $text) {
    $df = i18n_r('hitcount/DATE_FORMAT_'.strtoupper($unit));
    $to = i18n_r('hitcount/TO');
?>
    <div style="position:relative;width:<?php echo $w*$n; ?>px;height:<?php echo $h; ?>px;border-bottom:solid 1px #AFC5CF;">
      <?php for ($i=0; $i<$n; $i++) if ($numbers[$i] > 0) { ?>
      <div style="position:absolute;left:<?php echo $i*$w; ?>px;bottom:0px;width:<?php echo $w; ?>px;height:<?php echo (int) ($h*$numbers[$i]/$numbers['max']); ?>px;background-color:#AFC5CF;z-index:1"
        title="<?php echo strftime($df,$dates[$i]).': '.$numbers[$i].' '.$text; ?>"></div>
      <?php } ?>
    </div>
<?php    
  } 
  
  public static function drawNumberAndBar(&$number, $max) {
?>
    <div style="position:relative;"> &nbsp;
      <div style="position:absolute;left:0;top:0;z-index:2;width:100%;"><?php echo $number; ?></div>
      <div style="position:absolute;left:0;top:0.7em;width:<?php echo (int) (100*$number/$max); ?>%;height:0.3em;background-color:#eec6b9;z-index:1"></div>
    </div>
<?php    
  }
  
  public static function jsDate($date) {
    $dp = getdate($date);
    return 'new Date('.$dp['year'].','.($dp['mon']-1).','.$dp['mday'].',0,0,0)';
  }
  
  public static function drawChart($id, &$series, &$dates, $unit, $yAxisText) {
    global $SITEURL;
    $firstDate = $dates[0];
    $lastDate = $dates[count($dates)-1];
    $min = $firstDate - ($lastDate - $firstDate)*0.03;
    $max = $lastDate + ($lastDate - $firstDate)*0.03;
?>
  <script type="text/javascript">
    $(function() {
      var lines = [
      <?php 
        $first = true;
        foreach ($series as $name => &$numbers) {
          echo (!$first ? ', [' : '[');
          for ($i=0; $i<count($dates); $i++) echo ($i!=0 ? ', ' : '').'['.self::jsDate($dates[$i]).','.$numbers[$i].']';
          echo ']';
          $first = false;
        }
      ?>
      ];
      var plot1 = $.jqplot('<?php echo $id; ?>', lines, {
        axes: {
          xaxis: { 
            renderer: $.jqplot.DateAxisRenderer,
            tickOptions: { formatString:'<?php i18n('hitcount/DATE_FORMAT_JQPLOT'); ?>' },
            min: <?php echo self::jsDate($min); ?>,
            max: <?php echo self::jsDate($max); ?>
          },
          yaxis: {
            label: '<?php echo $yAxisText; ?>',
            rendererOptions: { forceTickAt0: true }
          }
        },
        highlighter: {
          show: true,
          sizeAdjust: 7.5
        },
        legend: {
          show: true,
          location: 'e',
          placement: 'outsideGrid'
        },
        seriesDefaults: { 
          lineWidth:2, 
          markerOptions: { size:5 }
        },
        series: [ <?php echo '{ label:"'.implode('" }, { label:"', array_keys($series)).'" }'; ?> ]
      });
    });
  </script>
<?php    
  }
  
}