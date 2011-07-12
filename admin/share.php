<?php 
if(isset($_GET['term'])) { 
	$term = ( function_exists( "filter_var") ) ? filter_var ( $_GET['term'], FILTER_SANITIZE_SPECIAL_CHARS)  : htmlentities($_GET['term']);
} 
if ($term = '{SHARE}') {
	$term = 'Share';	
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Share GetSimple</title>
	<style>
		.share {
			padding:20px;
			height:110px;
			width:240px;
			background:#f6f6f6;
		}
		h1 {font-size:18px;color:#111;margin:0 0 20px;font-family:georgia, garamond;font-weight:normal;text-shadow:1px 1px 0 #fff;}
	</style>
</head>
<body>
	<div class="share">
		<h1><?php echo $term; ?> GetSimple CMS:</h1>
		<div style="float:left;width:100px;" >	
			<iframe src="http://www.facebook.com/plugins/like.php?app_id=171087202956642&amp;href=http%3A%2F%2Fwww.facebook.com%2FGetSimpleCMS&amp;send=false&amp;layout=box_count&amp;width=100&amp;show_faces=false&amp;action=recommend&amp;colorscheme=light&amp;font&amp;height=90" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:90px;" allowTransparency="true"></iframe>
		</div>
		
		<div style="float:left;width:65px;" >
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
			<g:plusone size="tall" href="http://get-simple.info/" ></g:plusone>
		</div>
		
		<div style="float:left;width:60px;" >	
			<a href="http://twitter.com/share" class="twitter-share-button" data-url="http://get-simple.info/" data-text="Check out GetSimple CMS!" data-count="vertical" data-via="get_simple" data-related="buydealsin:Daily Deals">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
		</div>
	</div>
</body>
</html>