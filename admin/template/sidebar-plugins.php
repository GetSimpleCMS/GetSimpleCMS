<ul class="snav">
	<li><a href="plugins.php?plugin=main" <?php if($_GET['plugin']=='main') {echo 'class="current"'; } ?> accesskey="h" >Show Plugins</a></li>
	<?php 
	exec_action("plugin-sidebar");
	?>
</ul>