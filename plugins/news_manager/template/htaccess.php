<?php if (!defined('IN_GS')) {die('you cannot load this page directly.');}

/**
 * News Manager htaccess page
 */

?>

<h3>.htaccess</h3>
<?php
  if ($NMPAGEURL == ''
    || !in_array(trim($PERMALINK), array('','%parent%/%slug%','%parent%/%slug%/','%slug%','%slug%/','%parents%/%slug%','%parents%/%slug%/'))
    || count(array_diff(array(NMPARAMPOST,NMPARAMPAGE,NMPARAMTAG,NMPARAMARCHIVE),array('post','page','tag','archive'))) > 0
    || (defined('NMNOPARAMPOST') && NMNOPARAMPOST)
    ) {
?>
<div class="error">
  Failed to generate a .htaccess sample for the current site settings
</div>
<?php } else { ?>
<p>
  <?php i18n('news_manager/HTACCESS_HELP'); ?>
  <pre style="padding: 5px; background: #f7f7f7; border: 1px solid #eee;"><?php echo $htaccess; ?></pre>
</p>
<?php } ?>
<form class="largeform" action="load.php?id=news_manager" method="post" accept-charset="utf-8">
  <p class="hint">
    <?php i18n("news_manager/GO_BACK_WHEN_DONE"); ?>
  </p>
  <p id="submit_line">
    <span>
      <input class="submit" type="submit" value="<?php i18n("news_manager/FINISHED"); ?>" />
    </span>
  </p>
</form>
