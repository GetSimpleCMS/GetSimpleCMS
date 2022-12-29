<?php
  global $SITEURL;
  require_once(GSPLUGINPATH.'i18n_search/viewer.class.php');
  $i18n = &$params; // alias for i18n parsing
  $slug = array_key_exists('slug',$params) ? $params['slug'] : return_page_slug();
  $showTags = array_key_exists('showTags',$params) ? $params['showTags'] : true;
  $minTagSizePercent = array_key_exists('minTagSize',$params) ? (int) $params['minTagSize'] : 100;
  $maxTagSizePercent = array_key_exists('maxTagSize',$params) ? (int) $params['maxTagSize'] : 250;
  $addTags = array_key_exists('addTags',$params) ? $params['addTags'] : '';
  $goText = @$i18n['GO'];
  $is_ajax = !isset($params['ajax']) || $params['ajax'];
  $live = $is_ajax && isset($params['live']) && $params['live'];
  $url = function_exists('find_i18n_url') ? find_i18n_url($slug,null) : find_url($slug,null);
  $method = strpos($url,'?') !== false ? 'POST' : 'GET'; // with GET the parameters are not submitted!
  $language = isset($params['lang']) ? $params['lang'] : null;
  $placeholderText = @$params['PLACEHOLDER'];

  // languages
  $reqlangs = null;
  if (function_exists('return_i18n_languages')) {
    $deflang = return_i18n_default_language();
    $languages = $language ? array($language) : return_i18n_languages();
    foreach ($languages as $lang) {
      if ($lang == $deflang) $lang = '';
      $reqlangs = $reqlangs === null ? $lang : $reqlangs.','.$lang;
    }
  }
  
  // mark
  $mark = false;
  if (file_exists(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE)) {
    $data = getXML(GSDATAOTHERPATH.I18N_SEARCH_SETTINGS_FILE);
    $mark = true && $data->mark;
  }
  
?>
<form action="<?php echo $url; ?>" method="<?php echo $method; ?>" class="search">
<?php if ($showTags && $minTagSizePercent && $maxTagSizePercent && $minTagSizePercent > 0 && $minTagSizePercent <= $maxTagSizePercent) { ?>
  <div class="tags" style="display:none">
  	<?php I18nSearchViewer::displayTagsImpl($minTagSizePercent,$maxTagSizePercent,$params); ?>
  </div>
  <input type="hidden" name="tags" value="<?php echo htmlspecialchars(@$_REQUEST['tags']); ?>"/>
  <script type="text/javascript">
    $(function() {
      var $live = $('ul.search-results.search-live');
      $('.tags').show();
      $('.tags .tag').click(function(e) {
        var $form = $(e.target).closest('form');
        var tags = <?php echo json_encode($addTags); ?>;
        $(e.target).toggleClass('selected');
        $form.find('.tags .tag.selected').each(function() { tags += ' '+$(this).text().replace(' ','_'); });
        $form.find('[name=tags]').val(tags);
        <?php if ($is_ajax) { ?>
        if (tags == '') {
          $form.find('.tags .tag').removeClass('unavailable');
        } else {
          $.ajax({
            url:<?php echo json_encode($SITEURL.'plugins/i18n_search/ajax/tags4tags.php'); ?>, 
            data:{tags:tags<?php if ($reqlangs !== null) echo ', langs:'.json_encode($reqlangs); ?>},
            success:function(data,textStatus,jqXHR) {
              $form.find('.tags .tag').each(function() { 
                if (data[$(this).text().replace(' ','_')] > 0) $(this).removeClass('unavailable'); else $(this).addClass('unavailable')
              });
            },
            error:function(jqXHR,textStatus,errorThrown) {
              $form.find('.tags .tag').removeClass('unavailable');
            }
          });
        }
        <?php } ?>
        <?php if ($live) { ?>
        if ($live.size() == 1) search_live();
        <?php } ?>
      });
      var tags = $('[name=tags]').val().split(' ');
      $('.tags .tag').each(function() { if ($.inArray($(this).text(),tags) >= 0) $(this).addClass('selected'); });
    })
  </script>
<?php } ?>
  <input type="text" name="words" class="search-words" value="<?php echo htmlspecialchars(@$_REQUEST['words']); ?>" <?php if ($placeholderText) echo 'placeholder="'.htmlspecialchars($placeholderText).'"'; ?>/>
  <input type="submit" name="search" class="search-submit" value="<?php echo @$goText; ?>" />
  <?php if ($is_ajax) { ?>
  <?php if ($live) { ?><div id="search-temp" style="display:none"></div><?php } ?>
  <script type="text/javascript" src="<?php echo $SITEURL; ?>plugins/i18n_search/js/jquery.autocomplete.min.js"></script>
  <script type="text/javascript">
    <?php if ($live) { ?>
    function search_live() {
      var $form = $('#search-temp').closest('form');
      if ($form.size() != 1) return;
      var words = $form.find('[name=words]').val();
      var tags = $form.find('[name=tags]').val();
      $('ul.search-results.search-live').next('.paging, .search-results-paging').remove();
      var data = {
        words: words,
        tags: <?php echo json_encode($addTags.' '); ?> + tags
      };
      $('#search-temp').load(<?php echo json_encode($url.' #search-results-live'); ?>, data, 
        function() {
          $lis = $('#search-temp').find('li.search-entry').remove();
          $('ul.search-results.search-live').children().remove();
          $('ul.search-results.search-live').append($lis);
          <?php if ($mark) { ?>
          $('ul.search-results.search-live li a').click(function(e) {
            href = e.target.href;
            var pos = href.indexOf('?');
            href += (pos > 0 ? '&' : '?') + 'mark=' + escape(words+' '+tags);
            e.target.href = href;
            return true;
          });
          <?php } ?>
        }
      );
    }
    <?php } ?>
    $(function () {
      var $live = $('ul.search-results.search-live');
      // add css file
      $('head').append('<link rel="stylesheet" type="text/css" href="<?php echo $SITEURL; ?>plugins/i18n_search/css/jquery.autocomplete.css"></link>');
      $('form.search input[name=words]').autocomplete(
        <?php echo json_encode($SITEURL.'plugins/i18n_search/ajax/suggest.php'.($reqlangs!==null?'?langs='.$reqlangs:'')); ?>, { 
        minChars: 1,
        max: 50,
        scroll: true,
        multiple: true,
        multipleSeparator: ' '
      })<?php if ($live) { ?>.result(search_live)<?php } ?>;
      <?php if ($live) { ?>
        $('form.search input[name=words]').bind("keyup", search_live);
      <?php } ?>
    });
  </script>
  <?php } ?>
</form>
