<?php
  global $id, $parent;
  if (isset($_GET['id'])) {
    if (($pos = strrpos($id,'_')) !== false) {
      $viewlink = find_i18n_url(substr($id,0,$pos), (string) $parent, substr($id,$pos+1));
    } else {
      $viewlink = find_i18n_url($id, (string) $parent, return_i18n_default_language());
    }
  }
?>
<script type="text/javascript">
  // <![CDATA[
  $(function() {
<?php if (isset($_GET['id'])) { ?>
    $('.edit-nav a[target=_blank]').attr('href', <?php echo json_encode($viewlink); ?>);
<?php } ?>
<?php if (!$id && isset($_GET['newid'])) { ?>
    $('#post-id').val(<?php echo json_encode($_GET['newid']); ?>);
<?php } ?>
<?php if (!$id && isset($_GET['title'])) { ?>
    $('#post-title').val(<?php echo json_encode($_GET['title']); ?>);
<?php } ?>
<?php if (!$id && isset($_GET['metak'])) { ?>
    $('#post-metak').val(<?php echo json_encode($_GET['metak']); ?>);
<?php } ?>
<?php if (!$id && isset($_GET['metad'])) { ?>
    $('#post-metad').val(<?php echo json_encode($_GET['metad']); ?>);
<?php } ?>
  });
  // ]]>
</script>
