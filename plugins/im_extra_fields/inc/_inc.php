<?php
include_once(dirname(__DIR__).'/controller/renderer.php');
include_once(dirname(__DIR__).'/controller/processor.php');

if(empty($processor)) {
	$processor = new Processor();
}
if(empty($parser)) {
	$parser = new Renderer();
	$parser->init($processor);
}