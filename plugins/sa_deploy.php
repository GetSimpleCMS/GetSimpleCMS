<?php

/**
* @name Plugin Name
* description of
* @version 0.1
* @author Shawn Alverson
* @link http://tablatronix.com
* @file framework.php
*/

$ALLOWINTALLS = true; // allow script to install

$PLUGIN_ID = "sa_deploy";
$SA_DEPLOY_KEYCACHE = array();

$SA_DEPLOY_USERNAME = 'GetSimpleCMS';
$SA_DEPLOY_REPO     = 'GetSimpleCMS';

function init_plugin($PLUGIN_ID){
	$thisfile = basename(__FILE__, ".php");	// Plugin File
	$name     = $PLUGIN_ID;
	$version  = "0.1";
	$author   = "tablatronix";
	$url      = "http://getsimple-cms.info";
	$desc     = "Deploy from github plugin";
	$type     = "plugins";
	$func     = "start";

	register_plugin($thisfile,$name,$version,$author,$url,$desc,$type,$func);
	
	// actions
	add_action('plugins-sidebar','createSideMenu',array($PLUGIN_ID,'Github Deploy'));
	add_action('header','sa_deploy_css');
}

if(!function_exists('debugLog')){
	function debugLog($msg){
		// echo print_r($msg,true),"<Br>";
	}
}

// init
if(function_exists('register_plugin')) init_plugin($PLUGIN_ID);
else start();

function local_i18n_r($token,$default = ''){
	GLOBAL $i18n;
	if(isset($i18n[$token])) return $i18n[$token];
	return $default;
}

function start(){

	if(!defined('IN_GS')){
		echo "<body id=\"nogs\"><html><head>";
		sa_deploy_css();
		echo "</head></html></body>";
	}

    echo '<h3 class="floated">'.local_i18n_r('SA_DEPLOY_GH','Deploy From Github');

	$config = getConfig();

	echo " <span>".$config['username'].'/'.$config['repo']."</span>";
	echo '</h3><div class="clear"></div>';

	if(isset($_REQUEST['type']) && isset($_REQUEST['typeid']) && isset($_REQUEST['key'])){
		$type    = $_REQUEST['type'];
		$typeid  = $_REQUEST['typeid'];
		$key     = $_REQUEST['key'];
		
		doDeploy($type,$typeid,$key);
		return;
	}

    displayReleases();
    echo "<br/>";
    displayBranches();
    echo "<Br/>";
    displayTags(); 
    echo "<Br/>";
}

// key cache
function addKey($key,$str){
	GLOBAL $SA_DEPLOY_KEYCACHE;
	$SA_DEPLOY_KEYCACHE[$key] = $str;
}

function getKey($str){
	// nonce this key so deploys can not be replayed or predicted
	$key = md5($str);
	addKey($key,$str);
	return $key;
}

function saveKeyCache(){

}

function loadKeyCache(){

}

function deployButton($type,$typeid){
	$id = isset($_GET['id']) ? $_GET['id'] : '';
	$url = '?id='.$id.'&amp;type='.$type.'&amp;typeid='.$typeid.'&amp;key='.getKey($typeid);
	
	$str = '<span class="deploy_button"><a class="label label-light" title="'.local_i18n_r('DEPLOY','Deploy!').'" href="'.$url.'">deploy</span></a>';
	$str .='<span class="deploy_button"><a class="label label-error" title="'.local_i18n_r('DEPLOY','Deploy!').'" href="'.$url.'">deploy</span></a>';
	return $str;
}

function displayReleases(){
	$data = getReleases();
	
	echo '<h2>'.local_i18n_r('RELEASES','Releases').'</h2>';
	
	if(isset($data['message'])){
		echo $data['message']."<br>";
		return;
	}	

	echo '<ul class="deploylist">';

	foreach($data as $item){
		$tag = $item['tag_name'];
		$name  = $item['name'];
		$url   = isset($item['html_url']) ? $item['html_url'] : '';
		$title = '<a href="'.$url.'"  title="'.local_i18n_r('INFO','Info').'" target="_blank">'.$name.'</a>';
		echo '<li>'.$title.deployButton('release',$tag) . '</li>';
		}
	echo "</ul>";
}


function displayBranches(){
	$data = getBranches();
	
	echo '<h2>'.local_i18n_r('BRANCHES','Branches').'</h2>';
	
	if(isset($data['message'])){
		echo $data['message']."<br>";
		return;
	}	

	echo '<ul class="deploylist">';


	foreach($data as $item){
		$name  = $item['name'];
		$url = 'https://github.com/GetSimpleCMS/GetSimpleCMS/tree/'.$name;
		$title = '<a href="'.$url.'" title="'.local_i18n_r('INFO','Info').'" target="_blank">'.$name.'</a>';
		echo '<li>'.$title.deployButton('branch',$name) . '</li>';;		
	}
	echo "</ul>";
}

function displayTags(){
	$data = getTags();
	
	echo '<h2>'.local_i18n_r('TAGS','Tags').'</h2>';
	
	if(isset($data['message'])){
		echo $data['message']."<br>";
		return;
	}	

	echo '<ul class="deploylist">';

	foreach($data as $item){
		$name  = $item['name'];
		$url = 'https://github.com/GetSimpleCMS/GetSimpleCMS/tree/'.$name;
		$title = '<a href="'.$url.'"  title="'.local_i18n_r('INFO','Info').'" target="_blank">'.$name.'</a>';
		echo '<li>'.$title.deployButton('tag',$name) . '</li>';			
	}
	echo "</ul>";
}

function getConfig(){
	GLOBAL $SA_DEPLOY_USERNAME,$SA_DEPLOY_REPO;
	return array("username" => $SA_DEPLOY_USERNAME,	"password" => "", "repo" => $SA_DEPLOY_REPO);
}

function getReleases(){
	$config = getConfig();
	$data = Api::call('releases', getConfig() );
	$data = json_decode($data,true);
	// debugLog($data);
	return $data;
}

function getBranches(){
	$config = getConfig();
	$data = Api::call('branches', getConfig() );
	$data = json_decode($data,true);
	// debugLog($data);
	return $data;
}

function getTags(){
	$config = getConfig();

	$data = Api::call('tags', getConfig() );
	$data = json_decode($data,true);
	// debugLog($data);
	return $data;
}

function sa_deploy_plugin(){
	start();
}

function doDeploy($type = 'branch', $typeid = "master", $key = '', $exc = array()){

	GLOBAL $SA_DEPLOY_USERNAME, $SA_DEPLOY_REPO;

	//Exclusion list
	// array( "files" => array() , "folders" => array() )
	if(!$exc) 
	$exc = array(
	    "files"   => array("tip.zip","deploy.php","lastcommit.hash","deploy.conf","data.hash","sa_deploy.php","gsconfig.php",".htaccess"),
	    "folders" => array("backups","plugins","data","sa_deploy")
	);
	
	$deployconfig = array(
		'username'   => $SA_DEPLOY_USERNAME,
		'password'   => '',
		'reponame'   => $SA_DEPLOY_REPO,
		'type'       => $type,
		'typeid'     => $typeid,
		'dest'       => '../',
		'exc'        => $exc
	);

	if(function_exists('exec_action')) exec_action('sa_deploy_predeploy');

	include('sa_deploy/deploy.php');

	redirect(myself());
	// echo "<a href=""></a>";
}


class Api{

    public static function call($action, $configs){
         return self::$action($configs);
    }

    public static function releases($configs){
    	$url = "/repos/". $configs["username"] ."/". $configs["repo"] ."/releases";
    	return self::callApiGET($url, $configs);
    }

    public static function tag($configs){
    	$url = "/repos/". $configs["username"] ."/". $configs["repo"] ."/tags/".$configs['tags'];
    	return self::callApiGET($url, $configs);
    }

    public static function tags($configs){
    	$url = "/repos/". $configs["username"] ."/". $configs["repo"] ."/tags";
    	return self::callApiGET($url, $configs);
    }

    public static function branch($configs){
    	$url = "/repos/". $configs["username"] ."/". $configs["repo"] ."/branches/".$configs['branch'];
    	return self::callApiGET($url, $configs);
    }

    public static function branches($configs){
    	$url = "/repos/". $configs["username"] ."/". $configs["repo"] ."/branches";
    	return self::callApiGET($url, $configs);
    }

    public static function milestones($configs){
    	$url = "/repos/". $configs["username"] ."/". $configs["repo"] ."/milestones";
    	return self::callApiGET($url, $configs);
    }

    public static function issues($configs){
        $url = "/repos/". $configs["username"] ."/". $configs["repo"] ."/issues";
        return self::callApiGET($url, $configs);
    }

    public static function comments($configs){
        $url = "/repos/". $configs["username"] ."/". $configs["repo"] ."/issues/". $_GET["issueid"] . "/comments";
        return self::callApiGET($url, $configs);
    }

    protected static function callApiGET($url, $configs){

        # $curl = curl_init();
        $curlUrl ="https://api.github.com" . $url;
        # $curlUrl ="http://tablatronix.com" . $url;
        // $response = cache_url($curlUrl. '?' . http_build_query($_GET),$_GET['nocache']);
        $response = cache_url($curlUrl);

        return $response;
    }

}

function cache_url($url, $nocache = FALSE) {
	// @todo do not cache messages api rate limiting
    // settings
    $cachetime = 604800; //one week
    $where = defined('GSCACHEPATH') ? GSCACHEPATH : 'sa_deploy/cache/';

    if ( !is_dir($where)) {
        // mkdir($where);
        $warning = local_i18n_r('RATELIMITED','cache not found, we are rate limited!');
        debugLog('sa_deploy: ' . $warning);
        $nocache = false;
    }

    $hash = md5($url);
    $file = "$where$hash.txt";

    $mtime = 0;
    if (file_exists($file)) {
        $mtime = filemtime($file);
    }
    $filetimemod = $mtime + $cachetime;

    // if the renewal date is smaller than now, return true; else false (no need for update)
    if ($filetimemod < time() OR $nocache) {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            #CURLOPT_HEADER         => FALSE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_USERAGENT      => 'Awesome-Octocat-App',
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT        => 30
        ));

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $data = curl_exec($ch);
        debugLog($data['message']);

		if (!$data) {
			debug_api_details("curl error number:" .curl_errno($ch));
			debug_api_details("curl error:" . curl_error($ch));
		}

        // save the file if there's data
        if ($data AND !$nocache ) {
            $status = file_put_contents($file, $data);
            if(!$status) debugLog("FAILED to save cache");
        } else $data = file_get_contents($file);
    } else {
        $data = file_get_contents($file);
    }

    return $data;
}

function sa_deploy_css(){
	$css = "
	<style>
		/* sa_deploy */

		#nogs {
			margin:20px;
		}

		ul.deploylist {
			line-height: 16px;
		}

		ul.deploylist .deploy_button{
			float: right; 
		}
		
		ul.deploylist li {
			max-width: 800px; /* max width */
			display:block;
			width:100%;
			padding:3px;
		}

		ul.deploylist li:hover {
			background: rgba(0,0,0,0.05); 			
		}

		#nogs ul.deploylist li a{
			text-decoration: none;
			color: #415A66;
			font-weight: bold;
			font-family:arial;
			font-size: 12px;
		}

		#nogs h2 {
			font-size: 18px;
			font-family: Georgia, Times, Times New Roman, serif;
			color: #777;
			margin: 0 0 20px 0;
			font-weight: normal;
		}

		#nogs h3 {
			font-size: 18px;
			font-weight: normal;
			font-family: Georgia, Times, Times New Roman, serif;
			padding: 2px 0 0 0;
			color: #CF3805;
			display: block;
			margin: 0 0 20px 0;
			font-style: italic;			
			text-shadow: 1px 1px 0 #FFF;
		}

		#nogs h3 span {
			color: #999;
			font-size: 14px;
		}		
		
		#nogs ul.deploylist .label {
			padding: 1px 6px;
			border-radius: 3px;
			text-align: center;
			margin: 0px 3px;
		}

		#nogs ul.deploylist .label-light {
			color: #AFC5CF !important;
			background: #FFF !important;
			border: 1px solid #AFC5CF !important;
			padding: 0px 5px;
		}

		#nogs ul.deploylist .label-error{
			background-color: #C00 !important;
			color: #F2F2F2;
		}

		/* hover handling */
		ul.deploylist li .deploy_button a.label-error{
			display:none;
		}

		ul.deploylist li:hover .deploy_button a.label-error{
			display:block;
		}

		ul.deploylist li .deploy_button a.label-light{
			display:block;
		}

		ul.deploylist li:hover .deploy_button a.label-light{
			display:none;
		}

	</style>

	";
	echo $css;
}

?>