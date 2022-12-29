<?php
  global $id, $url, $parent;
?>
<script type="text/javascript">
  // <![CDATA[
<?php
  $pages = return_i18n_pages();
  # tags
  $tags = array();
  foreach ($pages as $page) {
    foreach (preg_split('/\s*,\s*/', trim(@$page['tags'])) as $t) $tags[$t] = true;
  }
  $tags = array_keys($tags);
  sort($tags);
  # structure
  $structure = return_i18n_page_structure(null,false,$url);
  $siblings = @$pages[''.$parent]['children'];
  if ($siblings) $myindex = array_search($url, $siblings); else $myindex = false;
  $after = $myindex !== false && $myindex > 0 ? $siblings[$myindex-1] : '';
?>
  var after = <?php echo json_encode($after); ?>;
  function changeParent() {
    $parent = $('#post-parent').val();
<?php
  $firstoption = '<option value="0">-- '.i18n_r('i18n_navigation/TOP').' --</option>';
  $page = $pages[null];
  if (isset($page['children']) && count($page['children']) > 0) {
    $children = $page['children'];
    $options = '';
    for ($i=0; $i<count($children); $i++) {
      if ($children[$i] != $url) $options .= '<option value="'.$children[$i].'">'.$pages[$children[$i]]['title'].'</option>';
    }
?>
    if ($parent == null || $parent == '') {
       $('#post-menu-order').empty().html(<?php echo json_encode($firstoption.$options); ?>);
<?php
  }
  foreach ($structure as $page) {
    $page = $pages[$page['url']];
    if (isset($page['children']) && count($page['children']) > 0) {
      $children = $page['children'];
      $options = '';
      for ($i=0; $i<count($children); $i++) {
        if ($children[$i] != $url) $options .= '<option value="'.$children[$i].'">'.$pages[$children[$i]]['title'].'</option>';
      }
?>
    } else if ($parent == <?php echo json_encode($page['url']); ?>) {
       $('#post-menu-order').empty().html(<?php echo json_encode($firstoption.$options); ?>);
<?php
    }
  }
?>
    } else {
       $('#post-menu-order').empty().html(<?php echo json_encode($firstoption); ?>);
    } 
    $('#post-menu-order').val(after);     
  }
  function changeUrl() {
    var url = $('#post-id').val();
    if ($('div.leftopt').size() > 0) { // GetSimple 3.0+
      var hide = url.indexOf('_') >= 0;
      if (hide) {
        $('#post-parent').closest('p').hide();
        $('#post-private').closest('p').hide();
        $('#post-template').closest('p').hide();
        $('#post-menu-order').hide();
        $('#post-menu-order').prev().hide();
      }
    } else {
      if (url.indexOf('_') >= 0) {
        $('#post-parent').closest('tr').attr('style','display:none');
        $('#post-private').closest('td').children().attr('style','display:none');
        //$('#post-menu-enable').attr('style','display:none');
        $('#post-menu').nextAll().attr('style','display:none');
      } else {
        $('#post-parent').closest('tr').attr('style',null);
        $('#post-private').closest('td').children().attr('style',null);
        //$('#post-menu-enable').attr('style',null);
        $('#post-menu').nextAll().attr('style',null);
      }
    }
  }
  var tags = <?php echo json_encode($tags); ?>;
  $(function() {
    $fParent = $('#post-parent');
    $fParent.empty();
    $fParent.append(<?php echo json_encode('<option value="" '.($parent == null ? 'selected' : '').'>-- '.i18n_r('NONE').' --</option>'); ?>);
<?php
  foreach ($structure as $page) {
?>
    $fParent.append(<?php echo json_encode('<option value="'.$page['url'].'" '.($parent == $page['url'] ? 'selected' : '').'>'.($page['level'] > 0 ? str_repeat("&nbsp;",5*$page['level']-2).'&lfloor;&nbsp;' : '').cl($page['title']).'</option>'); ?>);
<?php
  }
?>
    if ($('#tick').size() > 0) { // GetSimple 3.1+
      $('a.viewlink').hide();
      $('#menu-items').css('height','auto').css('padding-bottom','10px');
      $('#post-menu').attr('style','').addClass('short');
      $('#post-menu').prev().remove();
      $('#post-menu').prev().remove();
      $('#post-menu').prev().remove();
      $('#post-menu').before('<span style="text-transform:none"><label for="post-menu">' + <?php echo json_encode(i18n_r('MENU_TEXT')); ?> + '</label></span>');
      $('#post-menu-order').attr('style','clear:both;').addClass('short');
      $('#post-menu-order').before('<span style="text-transform:none;float:left;"><label for="post-menu">' + <?php echo json_encode(i18n_r('i18n_navigation/INSERT_AFTER')); ?> + '</label></span>');
    } else if ($('div.leftopt').size() > 0) { // GetSimple 3.0
      $('label[for=post-menu-enable]').nextUntil('input').remove();
      $('#menu-items').css('height','auto');
      $('#post-menu').attr('style','').addClass('short');
      $('#post-menu').prev().remove();
      $('#post-menu').prev().remove();
      $('#post-menu').prev().remove();
      $('#post-menu').before('<label for="post-menu">' + <?php echo json_encode(i18n_r('MENU_TEXT')); ?> + '</label>');
      $('#post-menu-order').attr('style','').addClass('short');
      $('#post-menu-order').before('<label for="post-menu">' + <?php echo json_encode(i18n_r('i18n_navigation/INSERT_AFTER')); ?> + '</label>');
    } else {
      $a = $('#post-menu-enable').closest('td').find('a');
      $a.after($a.html());
      $a.remove();
      $('#post-menu').closest('div').find('span').empty().html(<?php echo json_encode(i18n_r('MENU_TEXT')); ?>);
      $('#post-menu').attr('style','').after(<?php echo json_encode('<br /><span>'.i18n_r('i18n_navigation/INSERT_AFTER').'</span>'); ?>);
      $('#post-menu-order').attr('style','').before('<br />');
    }  
    $('#post-parent').change(changeParent);
    $('#post-id').change(changeUrl);
    changeParent();
    changeUrl();
    $('#post-metak').autocomplete(tags, {
      minChars: 0,
      max: 50,
      scroll: true,
      multiple: true,
      multipleSeparator: ', '
    });
  });
  // ]]>
</script>
