<html>
<head>
	<script src="http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="admin/template/js/jqueryFileTree/jqueryFileTree.js"></script>
	<link rel="stylesheet" type="text/css" href="admin/template/js/jqueryFileTree/jqueryFileTree.css" media="screen" />

<script type="text/javascript">
$(document).ready( function() {
	$('#JQueryFTD_Demo').fileTree({
root: '/images/',
script: 'jqueryFileTree.php',
expandSpeed: 1000,
collapseSpeed: 1000,
multiFolder: true
}, function(file) {
alert(file);
});
	
});
</script>
	
</head>
<body>
	
<div id="JQueryFTD_Demo" class="JQueryFTD"></div>

</body>
</html>