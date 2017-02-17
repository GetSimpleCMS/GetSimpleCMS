<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/**
 * Basic Functions 
 *
 * These functions are used throughout the installation of GetSimple.
 *
 * @package GetSimple
 * @subpackage Basic-Functions
 */

/**
 * Clean URL
 *
 * @since 1.0
 *
 * @param string $text
 * @return string
 */
function clean_url($text)  { 
	$text = strip_tags(lowercase($text)); 
	$code_entities_match   = array(' ?',' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','=','.'); 
	$code_entities_replace = array('','-','-','','','','','','','','','','','','','','','','','','','','','','','',''); 
	$text = str_replace($code_entities_match, $code_entities_replace, $text); 
	$text = urlencode($text);
	$text = str_replace('--','-',$text);
	$text = rtrim($text, "-");
	return $text; 
} 

/**
 * Clean Image Name
 *
 * Mirror image of Clean URL, but it allows periods so file extentions still work
 *
 * @since 2.0
 *
 * @param string $text
 * @return string
 */
function clean_img_name($text)  {
	$text = getDef('GSUPLOADSLC',true) ? strip_tags(lowercase($text)) : strip_tags($text);
	$code_entities_match   = array(' ?',' ','--','&quot;','!','#','$','%','^','&','*','(',')','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','='); 
	$code_entities_replace = array('','-','-','','','','','','','','','','','','','','','','','','','','','',''); 
	$text = str_replace($code_entities_match, $code_entities_replace, $text); 
	$text = urlencode($text);
	$text = str_replace('--','-',$text);
	$text = str_replace('%40','@',$text); // ensure @ is not encoded
	$text = rtrim($text, "-");
	return $text; 
} 

/**
 * 7bit Text Converter
 *
 * Converts a string to a different encoding format
 *
 * @since 1.0
 *
 * @param string $text
 * @param string $from_enc
 * @return string 
 */
function to7bit($text,$from_enc="UTF-8") {
	if (function_exists('mb_convert_encoding')) {
			$text = mb_convert_encoding($text,'HTML-ENTITIES',$from_enc);
		} 
	else {
		$text = htmlspecialchars_decode(utf8_decode(htmlentities($text, ENT_COMPAT, 'utf-8', false)));
	}
	
	// replace basic latin
	// sz/ligatures, *ligatures, o/u/a/umlauts, any?
	$text = preg_replace(
			array('/&szlig;/','/&(..)lig;/',
					 '/&([aouAOU])uml;/','/&(.)[^;]*;/'),
			array('ss',"$1","$1".'e',"$1"),
			$text);
	return $text;
}


/**
 * Formats Email to HTML Style
 *
 * @since 3.1
 *
 * @global $site_link_back_url
 * @param string $message
 * @return string
 */
function email_template($message) {
	GLOBAL $site_link_back_url;
	$linkurl = $site_link_back_url;
	if(getDef('GSEMAILLINKBACK') && getDef('GSEMAILLINKBACK') !== $linkurl) $linkurl = getDef('GSEMAILLINKBACK');

	$data = '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	<style>
	 table td p {margin-bottom:15px;}
	 a img {border:none;}
	</style>
	</head>
	<body style="padding:0;margin:0;background: #f3f3f3;font-family:arial, \'helvetica neue\', helvetica, serif" >
	<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" style="padding: 0 0 35px 0; background: #f3f3f3;">
		<tr>
			<td align="center" style="margin: 0; padding: 0;">
				<center>
					<table border="0" cellpadding="0" cellspacing="0" width="580" style="border-radius:3px;">
						<tr>
							<th style="padding:15px 0 15px 20px;text-align:left;vertical-align:top;background:#171E25;border-radius:4px 4px 0 0;" >
								<a href="'.$linkurl.'"><img src="'.$linkurl.'logo.png" alt="GetSimple CMS"></a>
							</th>
						</tr>
						<tr>
							<td style="background:#fff;border-bottom:1px solid #e1e1e1;border-right:1px solid #e1e1e1;border-left:1px solid #e1e1e1;font-size:13px;font-family:arial, helvetica, sans-serif;padding:20px;line-height:22px;" >
								'.$message.'
							</td>
						</tr>
						<tr>
							<td style="padding-top:10px;font-size:10px;color:#aaa;line-height:14px;font-family:arial, \'helvetica neue\', helvetica, serif" >
								<p class="meta">This is a system-generated email, please do not reply to it. For help or questions about GetSimple, please visit our <a href="'.$site_link_back_url.'" style="color:#aaa;" >website</a>.<br />&copy; '.date('Y').' GetSimple CMS. All Rights Reserved.&nbsp;<a href="'.$linkurl.'start/privacy" style="color:#aaa;" >Privacy Policy</a>. </p>
							</td>
						</tr>
					</table>
				</center>
			</td>
		</tr>
	</table>
	</body>
	</html>
	';
	return exec_filter('email_template',$data); // @hook email_template email template
}


/**
 * Send Email, DOES NOT SANITIZE FOR YOU!
 *
 * @since 1.0
 * @uses GSFROMEMAIL
 * @uses $EMAIL
 *
 * @param string $to
 * @param string $subject
 * @param string $message
 * @return string
 */
function sendmail($to,$subject,$message) {
	
	$message = email_template($message);

	if (getDef('GSFROMEMAIL')){
		$fromemail = GSFROMEMAIL; 
	} else {
		if(!empty($_SERVER['SERVER_ADMIN']) && check_email_address($_SERVER['SERVER_ADMIN'])) $fromemail = $_SERVER['SERVER_ADMIN'];
		else $fromemail =  'noreply@'.$_SERVER['SERVER_NAME'];
	}
	
	global $EMAIL;
	$headers  ='"MIME-Version: 1.0' . PHP_EOL;
	$headers .= 'Content-Type: text/html; charset=UTF-8' . PHP_EOL;
	$headers .= 'From: '.$fromemail . PHP_EOL;
	$headers .= 'Reply-To: '.$fromemail . PHP_EOL;
	$headers .= 'Return-Path: '.$fromemail . PHP_EOL;
	
	return @mail($to,'=?UTF-8?B?'.base64_encode($subject).'?=',"$message",$headers);
	}

/**
 * Sub-Array Sort
 *
 * Sorts the passed array by a subkey
 *
 * @since 1.0
 *
 * @param array $a
 * @param string $subkey Key within the array passed you want to sort by
 * @param string $order - order 'asc' ascending or 'desc' descending
 * @param bool $natural - sort using a "natural order" algorithm
 * @return array
 */
function subval_sort($a,$subkey, $order='asc',$natural = true) {
	if (count($a) != 0 || (!empty($a))) { 
		foreach($a as $k=>$v) {
			if(isset($v[$subkey])) $b[$k] = lowercase($v[$subkey]);
		}

		if(!isset($b)) return $a;

		if($natural){
			natsort($b);
			if($order=='desc') $b = array_reverse($b,true);	
		} 
		else {
			($order=='asc')? asort($b) : arsort($b);
		}
		
		foreach($b as $key=>$val) {
			$c[$key] = $a[$key];
		}

		return $c;
	}
}

class ObjectFromXML {

    public function __construct($attributes = '[]') {
        $jsonarray = json_decode(json_encode($attributes));
        if (count($jsonarray) != 0) {
            foreach ($jsonarray as $name => $value) {
                $this->{$name} = $value;
            }
        }
    }

    public function __get($key) {
        if (isset($this->{$key})) {
            return $this->{$key};
        }
        return NULL;
    }

}

/**
 * SimpleXMLExtended Class
 *
 * Extends the default PHP SimpleXMLElement class by 
 * allowing the addition of cdata
 *
 * @since 1.0
 *
 * @param string $cdata_text
 */
class SimpleXMLExtended extends SimpleXMLElement{   

	/**
	 * add a cdata value
	 * @uses  dom_import_simplexml
	 * @param str $cdata_text value to add as cdata
	 */
	public function addCData($cdata_text){   
		$dom  = dom_import_simplexml($this);
		$cdata = $dom->ownerDocument->createCDATASection($cdata_text);
		$dom->appendChild($cdata);
	}

	/**
	 * update cdata, if empty append cdata, if 1 child remove it and append new cdata
	 * if mutiple children do nothing, something is wrong
	 *
	 * @uses  dom_import_simplexml
	 * @param  str $cdata_text value to insert as cdata
	 * @return obj             node
	 */
	public function updateCData($cdata_text){
	$node = dom_import_simplexml($this);   
		$xml   = $node->ownerDocument;
		$cdata = $xml->createCDATASection($cdata_text);
		if($node->childNodes->length == 1){
			// if exactly one child, remove and append the new cdata
			$node->removeChild($node->firstChild);
			$node->appendChild($cdata);
		} 
		else if($node->childNodes->length == 0){
			// if no children just append cdata
			$node->appendChild($cdata);
		} else {
			// node has multiple children, ignore
			// @todo exception here?
			return;
		} 
	}

/**
	 * adds a cdata child node and value
	 * @param str $nodename nodename
	 * @param str $value    teh new node value
	 */
	public function addCDataChild($nodename,$value){
		$this->addChild($nodename)->addCData($value);
	}

	/**
	 * sets a nodes value, auto detects if text or cdata node 
	 * and adds via appropriate mechanism, defaults to text
	 * @param str $value value to set
	 */
	public function setValue($value){
		if($this->nodeisCData()) $this->updateCData($value);
		else if($this->nodeisText())  $this[0] = $value;
		else if($this->getNodeType() === null) $this[0] = $value;
	}

	/**
	 * sets a nodes value if it exists, and adds node if it doesn't
	 * sets via setValue, defaults to textnode
	 * @param str $key   node id
	 * @param str $value value to set
	 */
	public function editAddChild($key,$value = ''){
		if(!$this->$key){
			$this->addChild($key,$value);
			return;
		}
		$this->$key->setValue($value);
	}

	/**
	 * sets a nodes cdata value if it exists, and adds cdata node if it doesn't
	 * @param str $key   node id
	 * @param str $value value to set
	 */
	public function editAddCData($key,$value = ''){
		if(!$this->$key){
			$this->addCDataChild($key,$value);
			return;
		}
		$this->$key->updateCData($value);
	}

	/**
	 * get the nodes type
	 * @return str returns the nodetype constant of node
	 * http://php.net/manual/en/dom.constants.php
	 */
	public function getNodeType(){
		if(!is_object($this)) return false;
		$node  = dom_import_simplexml($this);
		if($node->hasChildNodes()) return $node->firstChild->nodeType;
	}

	/**
	 * check id a node is cdata
	 * @return bool true if cdata
	 */
	public function nodeisCData(){
		return $this->getNodeType() == XML_CDATA_SECTION_NODE;
	}

	/**
	 * check id a node is text
	 * @return bool true if text
	 */
	public function nodeisText(){
		return $this->getNodeType() == XML_TEXT_NODE;
	}

}


/**
 * Is File
 * Checks if a filepath provided is indeed a file and not .||.., with inaccurate type match
 * @todo: type match uses strstr, not a very good filter
 *
 * @since 1.0
 * @uses tsl
 *
 * @param string $file
 * @param string $path
 * @param string $type Optiona, default is 'json'
 * @return bool
 */
function isFile($file, $path, $type = 'json') {
	if( is_file(tsl($path) . $file) && $file != "." && $file != ".." && (strstr($file, $type))  ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Get Files
 *
 * Returns an array of files from the passed path
 *
 * @since 1.0
 *
 * @param string $path
 * @param string $ext optional file extensions to filter
 * @return array
 */
function getFiles($path,$ext = null) {

	$handle   = opendir($path) or die("getFiles: Unable to open $path");
	$file_arr = array();

	while ($file = readdir($handle)) {
		// $file = utf8_encode($file); // @todo handle unicode in filenames on windows
		if(isset($ext)){
			$fileext = getFileExtension($file);
			if ($fileext == $ext) $file_arr[] = $file;
		}
		else {
			if ($file != '.' && $file != '..' && $file!='thumbs.db' && $file!='Thumbs.db') {
				$file_arr[] = $file;
			}
		}
	}

	closedir($handle);
	return $file_arr;
}

/**
 * get list of subdirectories
 * @param  str $path    path to dir
 * @param  str $filereq filename required for inclusion
 * @return array        array of dir names
 */
function getDirs($path,$filereq = null) {
	$handle   = opendir($path) or die("getDirs: Unable to open $path");
	$dir_arr = array();
	while ($file = readdir($handle)) {
		$curpath = $path.$file;
		if (is_dir($curpath) && $file != '.' && $file != '..') {
			if(isset($filereq) && !file_exists($curpath.'/'.$filereq)) continue;
			$dir_arr[] = $file;
		}
	}
	closedir($handle);
	return $dir_arr;
}

/**
 * Get XML Files
 * Returns an array of xml files from the passed path
 * @since 3.3.0
 * @param string $path
 * @return array
 */
function getXmlFiles($path) {
	return getFiles($path,'json');
}

/**
 * execution timer
 * 
 * @since 3.2
 * @uses $microtime_start
 * 
 * @param bool $reset resets global to timestamp
 * @return 
 */
function get_execution_time($reset=false)
{
	GLOBAL $microtime_start;
		if($reset) $microtime_start = null;
		
		if($microtime_start === null)
		{
				$microtime_start = microtime(true);
				return 0.0; 
		}    
		return round(microtime(true) - $microtime_start,5); 
}

/**
 * Get XML Data
 *
 * Turns the XML file into an object 
 *
 * @since 1.0
 *
 * @param string $file
 * @return object
 */
function getXML($file,$nocdata = true) {
	$xml = read_file($file);
	if($xml){
            try {
                $rawdata = json_decode($xml, TRUE);
                $data = new SimpleXMLExtended('<?xml version="1.0"?><item></item>', LIBXML_NOCDATA);
                array_to_xml($rawdata, $data);
		// debugLog($data);
		return $data;
            } catch (Exception $exc) {
                echo $file;
                echo $exc->getTraceAsString();
                die;
            }
	}	
}

function array_to_xml( $data, &$xml_data ) {
    foreach( $data as $key => $value ) {
        if( is_numeric($key) ){
            $key = 'item'.$key; //dealing with <0/>..<n/> issues
        }
        if( is_array($value) ) {
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild("$key",htmlspecialchars("$value"));
        }
     }
}

/**
 * get page xml shortcut
 *
 * @since 3.4
 * @param  str $id id of page
 * @return xml     xml object
 */
function getPageXML($id,$nocdata = true){
	return getXML(GSDATAPAGESPATH.$id.'.json',$nocdata);
}

/**
 * get page draft xml shortcut
 *
 * @since 3.4
 * @param  str $id id of page
 * @return xml     xml object
 */
function getDraftXML($id,$nocdata = true){
	return getXML(GSDATADRAFTSPATH.$id.'.json',$nocdata);
}

/**
 * update single pages field value and resave file
 *
 * @param  str $id    id of page
 * @param  str $field field name
 * @param  str $value value
 * @param  bool $cdata true, store as cdata, false textnode, null auto detect from destination
 * @return [type]        [description]
 */
function updatePageField($id,$field,$value,$cdata = null){
	$xml = getPageXML($id,false);
	if($cdata === true){
		$xml->addCDataChild($field,$value);
	}
	else if($cdata === false) $xml->$field = $value;
	else $xml->$field->setValue($value);

	savePageXml($xml,false);
}

/**
 * get a page obj template
 * returns an empty simplexml GS page object
 * @return obj simplexml page obj, empty
 */
function getPageObject(){
	return createPageXml('');
}

/**
 * create a page xml obj
 * will only save standard GS fields, additional fields are ignored
 * 
 * @since 3.4
 * @param  str      $title     title of page
 * @param  str      $url       optional, url slug of page, if null title is used
 * @param  array   	$data      optional, array of data fields for page
 * @param  boolean 	$overwrite optional, overwrite exisitng slugs, if false auto increments slug id
 * @return obj                 xml object of page
 */
function createPageXml($title, $url = null, $data = array(), $overwrite = false){
	GLOBAL $reservedSlugs;

	$fields = array(
		'title',
		'titlelong',
		'summary',
		'url',
		'author',
		'template',
		'parent',
		'menu',
		'menuStatus',
		'menuOrder',
		'private',
		'meta',
		'metad',
		'metarNoIndex',
		'metarNoFollow',
		'metarNoArchive',
		'content'
	);

	// setup url, falls back to title if not set
	if(!isset($url)) $url = $title;
	$url = prepareSlug($url); // prepare slug, clean it, translit, truncate

	$title = truncate($title,GSTITLEMAX); // truncate long titles

	// If overwrite is false do not use existing slugs, get next incremental slug, eg. "slug-count"
	if ( !$overwrite && (file_exists(GSDATAPAGESPATH . $url .".json") ||  in_array($url,$reservedSlugs)) ) {
		list($newfilename,$count) = getNextFileName(GSDATAPAGESPATH,$url.'.json');
		$url = $url .'-'. $count;
		// die($url.' '.$newfilename.' '.$count);
	}

	// store url and title in data, if passed in param they are ignored
	$data['url'] = $url;
	$data['title'] = $title;

	// create new xml
	$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><item></item>');
	$xml->addChild('pubDate', date('r'));

	if(isset($data['content'])) $data['content'] = exec_filter('contentsave',$data['content']); // @filer contentsave filter content in createPageXml

	foreach($fields as $field){
		$node = $xml->addChild($field);
		if(isset($data[$field])) $node->addCData($data[$field]); // saving all as cdata, probably unnecessary
	}

	// debugLog(__FUNCTION__ . ': page created with slug of ' . $xml->url);
	return $xml;
}

/**
 * save a page to xml
 *
 * @since  3.4
 * @param  obj $xml simplexmlobj of page
 * @param  bool $backup backup before overwriting
 * @return bool success
 */
function savePageXml($xml,$backup = true){
	$url = $xml->url;
	if(!isset($url) || trim($url) == '') die(__FUNCTION__ . ' empty slug');
	// backup before overwriting
	if($backup && file_exists(GSDATAPAGESPATH . $url .".json")) backup_page($url);
	return XMLsave($xml, GSDATAPAGESPATH . $url .".json");
}

/**
 * save a page to xml
 *
 * @since  3.4
 * @param  obj $xml simplexmlobj of page
 * @param  string $path path to save page data file to
 * @param  bool $backup backup before overwriting
 * @return bool success
 */
function savePageAltXml($xml,$path,$backup = true){
	$url = $xml->url;
	if(!isset($url) || trim($url) == '') die(__FUNCTION__ . ' empty slug');
	// backup before overwriting
	if($backup && file_exists($path . $url .".json")) backup_datafile($path.$url.'.json');
	return XMLsave($xml, $path . $url .".json");
}

/**
 * save a page draft to xml
 *
 * @since  3.4
 * @param  obj $xml simplexmlobj of page
 * @param  bool $backup backup before overwriting
 * @return bool success
 */
function saveDraftXml($xml,$backup = true){
	$url = $xml->url;
	if(!isset($url) || trim($url) == '') die(__FUNCTION__ . ' empty slug'); // @todo need some kind of assert here
	// backup before overwriting
	if($backup && file_exists(GSDATADRAFTSPATH . $url .".json")) backup_draft($url);
	return XMLsave($xml, GSDATADRAFTSPATH . $url .".json");
}

/**
 * publish a draft
 * @since  3.4
 * @param  str $id id of page draft to publish
 * @return bool    status
 */
function publishDraft($id){
	if(!pageHasDraft($id)) return false;
	backup_page($id); // backup live page
	backup_datafile(GSDATADRAFTSPATH.$id.'.json'); // backup draft before moving
	$status = move_file(GSDATADRAFTSPATH,GSDATAPAGESPATH,$id.'.json');
	// restore_datafile(GSDATADRAFTSPATH . $id .".json"); // debugging replays
	if($status)	updatePageField($id,'pubDate',date('r')); // update pub date
	return $status;
}

/**
 * check if a page has a draft copy
 *
 * @since 3.4
 * @param str $filepath filepath to data file
 * @return bool status
 */
function pageHasDraft($id){
	return file_exists(GSDATADRAFTSPATH . $id .".json");
}

/**
 * change draft pages slug, used when a page slug changes
 * @since  3.4
 * @param  String $id    Old page id
 * @param  String $newid New page id
 * @return bool          save status
 */
function changeDraftSlug($id,$newid){
	if(!pageHasDraft($id)) return;
	$draftXml = getDraftXML($id);
	$draftXml->url = $newid;
	delete_draft($id);
	return saveDraftXml($draftXml,false);
}

/**
 * check if a page exists.
 * check pagecache first then check page file exist
 * 
 * @since  3.4
 * @param  str $id slug id
 * @return bool     true if page exists
 */
function pageExists($id){
	GLOBAL $pagesArray;
	if(isset($pagesArray[$id])) return true;
	return file_exists(GSDATAPAGESPATH . $id .'.json');
	return false;
}

/**
 * prepare a slug to gs standads
 * sanitizes, performs translist for filename, truncates to GSFILENAMEMAX
 *
 * @since  3.4
 * @param  str $slug slug to normalize
 * @param  str $default default slug to substitute if conversion empties it
 * @return str       new slug
 */
function prepareSlug($slug, $default = 'temp'){
	$slug = truncate($slug,GSFILENAMEMAX);
	$slug = doTransliteration($slug);
	$slug = to7bit($slug, "UTF-8");
	$slug = clean_url($slug); //old way @todo what does that mean ?
	if(trim($slug) == '' && $default) return $default;
	return $slug;
}

/**
 * check if a file has a backup copy
 *
 * @since 3.4
 * @param str $filepath filepath to data file
 * @return bool status
 */
function fileHasBackup($filepath){
	$backupfilepath = getBackupFilePath($filepath);
	return file_exists($backupfilepath);
}

/**
 * save XML to file
 *
 * @since 2.0
 *
 * @param object $xml  simple xml object to save to file via asXml
 * @param string $file Filename that it will be saved as
 * @return bool
 */
function XMLsave($xml, $file) {
	if(!is_object($xml)){
		debugLog(__FUNCTION__ . ' failed to save json');
		return false;
	}	
	$data = @$xml->asXML();
        $xmltojson = simplexml_load_string($data, 'SimpleXMLExtended', LIBXML_NOCDATA);
        $data = json_encode($xmltojson); // simple convert XML to JSON
        //save_file($file.'.json', $json); // save pre-JSON file
	//if(getDef('GSFORMATjson',true)) $data = formatXmlString($data); // format xml if config setting says so
	//$data = exec_filter('xmlsave',$data); // @filter xmlsave executed before writing string to file
	$success = save_file($file, $data); // LOCK_EX ?
	return $success;
}

function XMLFormatsave($xml, $file) {
	if(!is_object($xml)){
		debugLog(__FUNCTION__ . ' failed to save json');
		return false;
	}	
	$data = @$xml->asXML();
	if(getDef('GSFORMATjson',true)) $data = formatXmlString($data); // format xml if config setting says so
	$data = exec_filter('xmlsave',$data); // @filter xmlsave executed before writing string to file
        $success = save_file($file, $data); // LOCK_EX ?
	return $success;
}

/**
 * create a director or path
 *
 * @since 3.4
 * @todo normalize slashes for windows, apache works fine, iis might not
 * @todo might need a recursive chmod also, mkdir only chmods the basedir allegedly
 *
 * @param  str  $dir          directory or path
 * @param  boolean $recursive create recursive path
 * @return bool               success, null if already exists
 */
function create_dir($path,$recursive = true){
	if(is_dir($path)) return fileLog(__FUNCTION__,true,'dir already exists',$path);
	$status = mkdir($path,getDef('GSCHMODDIR'),$recursive); // php mkdir
	return 	fileLog(__FUNCTION__. ':' . ($recursive ? ' [recursive=true] ' : ''),$status,$path);
	}

/**
 * Delete a folder, must be empty
 *
 * @param  str $path path to remove
 * @return bool       success
 */
function delete_folder($path){
	$status = rmdir($path);
	return fileLog(__FUNCTION__,$status,$path);
}

/**
 * save data to file (overwrites existing)
 * then chmod
 * @todo do we really need to chmod everytime ?
 *
 * @since  3.4
 *
 * @param  str $file filepath
 * @param  str $data data to save to file
 * @return bool success
 */
function save_file($file,$data=''){
	$status = file_put_contents($file,$data) !== false; // returns num bytes written, FALSE on failure
	fileLog(__FUNCTION__,$status,$file);
	if(getDef('GSDOCHMOD',true)) $chmodstatus = gs_chmod($file); // currently ignoring chmod failures
	return $status;
}

/**
 * read a file in
 * @param  str $file filepath to file
 * @return bool      file contents
 */
function read_file($file){
	if(!file_exists($file)){
		fileLog(__FUNCTION__,false,$file . ' not exist');
		return;
	}
	$data = file_get_contents($file); // php file_get_contents
	fileLog(__FUNCTION__,$data!==false,$file);
	return $data;
}

// alias for rename_file()
function move_file($src,$dest,$filename = null){
	fileLog(__FUNCTION__,'-','ALIAS calling rename_file');
	$status = rename_file($src,$dest,$filename);
	return $status;
}

/**
 * Rename a file (overwrites existing)
 * renames a file, moving between dirs if necessary
 *
 * @since  3.4
 *
 * @param  str  $src  filepath to rename
 * @param  str  $dest filepath destination
 * @param  str $filename optional filename will be appended to src and destf
 * @return bool           success
 */
function rename_file($src,$dest,$filename = null){
	if(isset($filename)){
		$src  .= DIRECTORY_SEPARATOR . $filename;
		$dest .= DIRECTORY_SEPARATOR . $filename;
	}
	if(!$status = rename($src,$dest)){ // php rename
		fileLog(__FUNCTION__,false,'calling copy_file & delete_file');
		$status = copy_file($src,$dest) && delete_file($src);
		return $status;
	}
	return fileLog(__FUNCTION__,$status,$src,$dest);
}

/**
 * copy a file (overwrites existing)
 *
 * @since  3.4
 *
 * @param  str  $src  filepath to copy
 * @param  str  $dest filepath destination
 * @param  str  $filename optional filename will be appended to src and destf
 * @return bool           success
 */
function copy_file($src,$dest,$filename = null){
	if(isset($filename)){
		$src  .= DIRECTORY_SEPARATOR . $filename;
		$dest .= DIRECTORY_SEPARATOR . $filename;
	}	
	$status = copy($src,$dest); // php copy
	return fileLog(__FUNCTION__,$status,$src,$dest);
}

/**
 * Deletes a file
 *
 * @since  3.4
 *
 * @param  str $file  file to delete
 * @return bool       success
 */
function delete_file($file){
	$status = unlink($file); // php unlink
	return fileLog(__FUNCTION__,$status,$file);
}

/**
 * do chmod using gs chmod constants or user
 * returns false if chmod is not available for whatever reason
 *
 * @since 3.4
 *
 * @param  str  $path  path to file or dir
 * @param  boolean $dir   is directory, default false = file
 * @param  int  $chmod chmod value
 * @return bool         success of chmod
 */
function gs_chmod($path,$chmod = null,$dir = false){
	if(!isset($chmod) || empty($chmod)){
		$chmod = $dir ? getDef('GSCHMODDIR') : getDef('GSCHMODFILE');
	}
	// chmod might be prohibited by disabled functions etc.
	if(!function_exists('chmod')) return fileLog(__FUNCTION__,false,'chmod not available',$path,$chmod);

	$status = chmod($path,$chmod); // php chmod
	return fileLog(__FUNCTION__,$status,$path,$chmod);
}

/**
 * log fileio operations
 *
 * since 3.4
 * @param  str   $operation file operation or functionname to log
 * @param  mixed $status    if bool evals to success and fail, else shows status as string
 * @param  mixed  variable length args any other arguments are outputted at end
 * @return mixed            returns status untouched, passthrough
 */
function fileLog($operation,$status = null){
	if(!getDef('GSDEBUGFILEIO',true)) return $status;
	$args = array_slice(func_get_args(),2); // grab arguments past first 2 for output
	if(is_bool($status)) $logstatus = ($status === true) ? uppercase(i18n_r('SUCCESS','SUCCESS')) : uppercase(i18n_r('FAIL','FAIL'));
	else $logstatus = (string) $status;
	$args = convertPathArgs($args);
	debugLog("&bull; fileio: [$logstatus] ".uppercase($operation).": ".implode(" - ",$args));

	return $status;
}

/**
 * convert array of file paths to relative paths to gsroot
 * @since  3.4
 * @param  array $args full filepaths
 * @return returns array of relative filepaths
 */
function convertPathArgs($args){
	foreach($args as &$arg){
		if(!is_string($arg)) continue;
		if(strpos($arg,GSROOTPATH) !== false){
			$arg = getRelPath($arg);
		}
	}
	return $args;
}

/**
 * Formated Date Output, special handling for params on windows
 *
 * @since  3.4
 * @author  cnb
 * 
 * @param  string $format    A strftime or date format
 * @param  time $timestamp   A timestamp
 * @param  bool $uselocale   if true, set and unset locale
 * @return string            returns a formated date string
  */
function formatDate($format, $timestamp = null, $uselocale = true) {
	if(!$timestamp) $timestamp = time();	

	// debugLog(__FUNCTION__.' '.$format.' '.$timestamp.' '.$uselocale);

	// if no strfttime tokens found just use date
	// @todo add a date token -> strftime token converter here
	if (strpos($format, '%') === false) {
		$date = date($format, $timestamp);
	} 
	else {
		// set locale temporarily for strfttime
		if($uselocale) setNewLocale(LC_TIME);
		
		if (hostIsWindows()) {
		  # fixes for Windows
		  $format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format); // strftime %e parameter not supported
		  $date   = utf8_encode(strftime($format, $timestamp)); // strftime returns ISO-8859-1 encoded string
		} else {
		  $date = strftime($format, $timestamp);
		}
		
		if($uselocale) restoreOldLocale(LC_TIME);
 	}

	return $date;
}

/**
 * Time Output using locale
 *
 * @since 3.4
 * @param  str $dt Date/Time String
 * @return str
 */
function output_time($dt = null) {
	if(isset($dt)) $dt = strtotime($dt);
	if(getTimeFormat()) return formatDate(getTimeFormat(),$dt);
}

/**
 * Date/Time Output using locale
 *
 * @since 1.0
 * @param string $dt Date/Time string
 * @return string
 */
function output_datetime($dt = null) {
	if(isset($dt)) $dt = strtotime($dt);
	if(getDateTimeFormat()) return formatDate(getDateTimeFormat(),$dt);
	}

/**
 * Date only Output using locale
 *
 * @since 1.0
 * @param string $dt Date/Time string
 * @return string
 */
function output_date($dt = null) {
	if(isset($dt)) $dt = strtotime($dt);
	if(getDateFormat()) return formatDate(getDateFormat(),$dt);
	}

// legacy aliases
function shtTime($dt = null){
	return output_time($dt);
}
function shtDate($dt = null){
	return output_date($dt);
}
function lngDate($dt = null){
	return output_datetime($dt);
}

/**
 * Clean Utility
 *
 * Removes slashes, removes html tags, decodes entities
 * used to clean slugs and titles
 * @since 1.0
 *
 * @param string $data
 * @return string
 */
function cl($data){
	$data = stripslashes(strip_tags(html_entity_decode($data, ENT_QUOTES, 'UTF-8')));
	//$data = preg_replace('/[[:cntrl:]]/', '', $data); //remove control characters that cause interface to choke
	return $data;
}

/**
 * Normalizes trailing slash by removing all and readding it
 *
 * @since 1.0
 *
 * @param string $path
 * @return string
 */
function tsl($path) {
	return no_tsl($path).'/';
}

/**
 * Remove all trailing slashes
 *
 * @since 3.4
 *
 * @param string $path
 * @return string
 */
function no_tsl($path) {
	$path = rtrim($path,'/');
	return $path;
}


/**
 * Remove all leading slashes
 *
 * @since 3.4
 *
 * @param string $path
 * @return string
 */
function no_lsl($path) {
	$path = ltrim($path,'/');
	return $path;
}


/**
 * Case-Insensitve In-Array
 *
 * Creates a function that PHP should already have, but doesnt
 *
 * @since 1.0
 *
 * @param string $path
 * @return string
 */
if(!function_exists('in_arrayi')) {
	function in_arrayi($needle, $haystack) {
		return in_array(lowercase($needle), array_map('lowercase', $haystack));
	}
}


/**
 * Creates Standard URL for Pages
 *
 * Default function to create the correct url structure for each front-end pages
 * 
 * @since 3.4
 * @uses $PRETTYURLS
 * @uses $PERMALINK
 * @uses tsl
 *
 * @param string $slug
 * @param string $parent
 * @param string $absolute force absolute siteurl
 * @return string
 */
function generate_url($slug, $absolute = false){
	global $PRETTYURLS;
	global $PERMALINK;

	// force slug to string in case a simpleXml object was passed ( from a page obj for example)
	$slug   = (string) $slug;
	$delim  = getDef('GSTOKENDELIM');

	if(empty($slug)) return; // empty slug

	$path   = tsl(getSiteURL($absolute));
	$url    = $path; // var to build url into

	if($slug != getDef('GSINDEXSLUG')){
		if ($PRETTYURLS == '1'){
			$url .= generate_permalink($slug);
		}
		else $url .= 'index.php?id='.$slug;
	}

	$url = exec_filter('generate_url',$url); // @filter generate_url (str) for generating urls after processing, for use with custom tokens etc
	return $url;
}

/**
 * generate permalink url from tokenized permalink structure
 * uses a very basic str_replace based token replacer, not a parser
 * TOKENS (%tokenid%)
 *  %path% - path heirarchy to slug
 *  %slug% - slug
 *  %parent% - direct parent of slug
 *
 * supports prettyurl or any other permalink structure
 * eg. ?id=%slug%&parent=%parent%&path=%path%
 * 
 * @param  (str) $slug      slug to resolve permalink for	
 * @param  (str) $permalink (optional) permalink structure
 * @return (str)            	
 */
function generate_permalink($slug, $permalink = null){
	GLOBAL $PERMALINK;
	
	$slug = (string) $slug;

	if(!isset($permalink)){
		$plink = $PERMALINK;
		if(empty($PERMALINK)) $plink = getDef('GSDEFAULTPERMALINK');
	} else $plink = $permalink;

	// replace PATH token
	if(containsToken('path',$plink)){
		// remove PARENT tokens if path, since it would be pointless and probably accidental
		// leaving in for now lets not make assumptions
		// $plink = replaceToken('parent','',$plink);
		$pagepath = getParents($slug);
		if($pagepath){
			$pagepath = implode('/',array_reverse($pagepath));
			$plink    = replaceToken('path', $pagepath, $plink);		
		} else {
			// page has no parents, remove token
			$plink = replaceToken('path', '', $plink);
		}
	} 

	// replace PARENT token
	if(containsToken('parent',$plink)){
		$parent = getParent($slug);
		$plink  = replaceToken('parent', $parent, $plink);
	}
	
	// replace SLUG token
	$plink = replaceToken('slug', $slug, $plink);
	
	$plink = str_replace('//','/',$plink); // clean up any double slashes

	// debugLog($url);
	// debugLog($plink);
	return no_lsl($plink);
}

/** 
 * LEGACY alias for generate_url, defaults to relative now
 * @deprecated
 */
function find_url($slug, $parent = '', $type = null) {
	// parent is ignored
	if(!isset($type)){
		if(!getDef('GSSITEURLREL',true)) $type = "full"; # only default to full if not GSSITEURLREL
		else $type = "relative";
	}	
	return generate_url($slug, $type == 'full');
}

/**
 * replaces a string token with value
 * @param  str $token token id
 * @param  str $value value to replace
 * @param  str $str   source string
 * @return str        new string
 */
function replaceToken($token,$value,$str, $delim = null){
	if(!isset($delim)) $delim = getDef('GSTOKENDELIM');
	return str_replace($delim.$token.$delim,$value,$str);
}

/**
 * check if a string contains a token
 * @param  str $token token id
 * @param  str $str   source string
 * @return bool       true if token found
 */
function containsToken($token,$str,$delim = null){
	if(!isset($delim)) $delim = getDef('GSTOKENDELIM');	
	return stripos($str, $delim.$token.$delim) !== false;
}

/**
 * Strip Path
 *
 * Strips all path info from a filepath or basedir
 *
 * @since 2.0
 * @author Martijn van der Ven
 *
 * @param string $path
 * @return string
 */
function strippath($path) {
	$pathparts = pathinfo($path);
	if(isset($pathparts['extension'])) return $pathparts['filename'].'.'.$pathparts['extension'];
	return $pathparts['basename'];
}

/**
 * Strip Quotes
 *
 * @since 2.0
 *
 * @param string $text
 * @return string
 */
function strip_quotes($text)  { 
	$text = strip_tags($text); 
	$code_entities_match = array('"','\'','&quot;'); 
	$text = str_replace($code_entities_match, '', $text); 
	return trim($text); 
}

/**
 * Encode Quotes
 *
 * @since 3.0
 *
 * @param string $text
 * @return string
 */
function encode_quotes($text)  { 
	$text = strip_tags($text);

	if (version_compare(PHP_VERSION, "5.2.3")  >= 0) {	
		$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
	} else {	
		$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}

	return trim($text); 
} 

/**
 * Redirect URL
 *
 * @since 3.0
 * @author schlex
 *
 * @param string $url
 * @param bool ajax force redirects if ajax
 */
function redirect($url,$ajax = false) {
	global $i18n;

	$url = var_out($url,'url'); // filter url here since it can come from alot of places, specifically redirectto user input

	// handle expired sessions for ajax requests
	if(requestIsAjax()){
		if(!cookie_check()){
		header('HTTP/1.1 401 Unauthorized');
		header('WWW-Authenticate: FormBased');
		// @note this is not a security function for ajax, just a session timeout handler
		die();
		} else if($ajax){
			header('HTTP/1.1 300 Redirect');
			// header('Location: '.$url);
			echo $url;
			// @note this is not a security function for ajax, just a session timeout handler, also uses for post redirects, new page etc.
			die();
		}
	}

	if(function_exists('exec_action')) exec_action('redirect'); // @hook redirect a redirect is occuring

	$debugredirect = getDef('GSDEBUGREDIRECTS',true);

	if (!headers_sent($filename, $linenum) && !$debugredirect) {
		header('Location: '.$url);
	} else {
		// @todo not sure this ever gets used or headers_sent is reliable ( turn output buffering off to test )
		echo "<html><head><title>".i18n_r('REDIRECT')."</title></head><body>";
		if ( !isDebug() ) {
			echo '<script type="text/javascript">';
			echo 'window.location.href="'.$url.'";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
			echo '</noscript>';
		}

		if(headers_sent()){
			echo i18n_r('ERROR').": Headers already sent in ".$filename." on line ".$linenum."<br/><br/>\n\n";
		}
		
		printf(i18n_r('REDIRECT_MSG'), $url);

		if(!isAuthPage()) {
			if (isDebug()){
				debugLog(debug_backtrace());
				outputDebugLog();
				}
			}
		
		echo "</body></html>";
	}
	
	exit;
}

/**
 * Display i18n
 *
 * Displays the default language's tranlation, but if it 
 * does not exist, it falls back to $default if set, else GSMERGELANG else {token}.
 *
 * @since 3.0
 * @author ccagle8
 * @uses GSLANGPATH
 * @uses $i18n
 * @uses $LANG
 *
 * @param string $name
 * @param bool $echo Optional, default is true
 * @param mixed $default default return value if i18n or token not exist, default:true {token}, false:null, str:string
 */
function i18n($name, $echo=true, $default = true) {
	global $i18n;
	global $LANG;

	if(isset($i18n) && isset($i18n[$name])){
			$myVar = $i18n[$name];
		}
	else if($default === true){
		$myVar = '{'.$name.'}'; // if $i18n doesnt exist yet return something
	}
	else if(is_string($default)){
		$myVar = $default;
	}
	else return;

	return echoReturn($myVar,$echo);
	}

/**
 * Return i18n
 *
 * Same as i18n, but returns instead of echos
 *
 * @since 3.0
 * @author ccagle8
 *
 * @param string $name
 */
function i18n_r($name,$default = true) {
	return i18n($name, false, $default);
}

/**
 * i18n Merge
 *
 * Merges a plugin's language file with the global $i18n language
 * This is the function that plugin developers will call to initiate the language merge
 *
 * @since 3.0
 * @author mvlcek
 * @uses i18n_merge_impl
 * @uses $i18n
 * @uses $LANG
 *
 * @param string $plugin
 * @param string $language, default=null
 * @return bool
 */
function i18n_merge($plugin, $language=null) {
	global $i18n, $LANG;
	return i18n_merge_impl($plugin, $language ? $language : $LANG, $i18n);
}

/**
 * i18n Merge Implementation
 *
 * Does the merging of a plugin's language file with the global $i18n language
 *
 * @since 3.0
 * @author mvlcek
 * @uses GSPLUGINPATH
 *
 * @param string $plugin null if merging in core langs
 * @param string $lang
 * @param string $globali18n
 * @return bool
 */
function i18n_merge_impl($plugin = '', $lang, &$globali18n) {

	$i18n = array(); // local from file
	if(!isset($globali18n)) $globali18n = array(); //global ref to $i18n

	$path     = (isset($plugin) && $plugin !=='' ? GSPLUGINPATH.$plugin.'/lang/' : GSLANGPATH);
	$filename = $path.$lang.'.php';
	$prefix   = $plugin ? $plugin.'/' : '';

	// @todo being overly safe here since we are direclty including input that can come from anywhere
	if (!file_exists($filename) || !filepath_is_safe($filename,$path)) {
		return false;
	}

	// prevent lang includes from outputing data, injections, or breaking headers using OB
	ob_start();
	include($filename); 
	ob_end_clean();	

	// if core lang and global is empty assign
	if(!$plugin && !$globali18n && count($i18n) > 0){
		$globali18n = $i18n;
	 	return true;
	}

	// replace on per key basis
	if (count($i18n) > 0){
		foreach ($i18n as $code => $text) {
			if (!array_key_exists($prefix.$code, $globali18n)) {
				$globali18n[$prefix.$code] = $text;
			}
		}
	} 
	return true;
}

/**
 * Safe AddSlashes HTML
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $text
 * @return string
 */
function safe_slash_html($text) {
	if (get_magic_quotes_gpc()==0) {
		$text = addslashes(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
	} else {
		$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
	}

	return xmlFilterChars($text);
}

/**
 * xmlFilterChars
 *
 * @since  3.3.3
 * @param  str $str string to prepare for xml cdata
 * @return str      filtered string
 */
function xmlFilterChars($str){
	$chr = getRegexUnicode();
	// filter only xml allowed characters
	return preg_replace ('/[^'.$chr['ht'].$chr['lf'].$chr['cr'].$chr['lower'].$chr['upper'].']+/u', ' ', $str);
}

/**
 * getRegexUnicode
 * defines unicode char and char ranges for use in regex filters
 *
 * @since  3.3.3
 * @param  str $id key to return from char range array
 * @return mixed     array or str if id specified of regex char strings
 */
function getRegexUnicode($id = null){
	$chars = array(
		'null'       => '\x{0000}',            // 0 null
		'ht'         => '\x{0009}',            // 9 horizontal tab
		'lf'         => '\x{000a}',            // 10 line feed
		'vt'         => '\x{000b}',            // 11 vertical tab
		'FF'         => '\x{000c}',            // 12 form feed
		'cr'         => '\x{000d}',            // 13 carriage return
		'cntrl'      => '\x{0001}-\x{0019}',   // 1-31 control codes
		'cntrllow'   => '\x{0001}-\x{000c}',   // 1-12 low end control codes
		'cntrlhigh'  => '\x{000e}-\x{0019}',   // 14-31 high end control codes
		'bom'        => '\x{FEFF}',            // 65279 BOM byte order mark
		'lower'      => '\x{0020}-\x{D7FF}',   // 32 - 55295
		'surrogates' => '\x{D800}-\x{DFFF}',   // 55296 - 57343
		'upper'      => '\x{E000}-\x{FFFD}',   // 57344 - 65533
		'nonchars'   => '\x{FFFE}-\x{FFFF}',   // 65534 - 65535
		'privateb'   => '\x{10000}-\x{10FFFD}' // 65536 - 1114109
	);

	if(isset($id)) return $chars[$id];
	return $chars;
}

/**
 * Safe StripSlashes HTML Decode
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $text
 * @return string
 */
function safe_strip_decode($text) {
	if (get_magic_quotes_gpc()==0) {
		$text = htmlspecialchars_decode($text, ENT_QUOTES);
	} else {
		$text = stripslashes(htmlspecialchars_decode($text, ENT_QUOTES));
	}
	return $text;
}

/**
 * StripSlashes HTML Decode
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $text
 * @return string
 */
function strip_decode($text) {
	$text = stripslashes(htmlspecialchars_decode($text, ENT_QUOTES));
	return $text;
}

/**
 * Safe Pathinfo Filename, pathinfo php 5.2 compatability wrapper
 *
 * for backwards compatibility for before PHP 5.2
 *
 * @since 3.0
 * @author madvic
 *
 * @todo remove shim support, min requirements is php 5.2
 * @param string $file
 * @return string
 */
function pathinfo_filename($file) {
	if (getDef('PATHINFO_FILENAME')) return pathinfo($file,PATHINFO_FILENAME);

	// php 5.2 support
	$path_parts = pathinfo($file);
	if(isset($path_parts['extension']) && ($file!='..')){
		return substr($path_parts['basename'],0 ,strlen($path_parts['basename'])-strlen($path_parts['extension'])-1);
	} else{
		return $path_parts['basename'];
	}
}

function getFileName($file){
	return pathinfo_filename($file);
}

function getFileExtension($file){
	return lowercase(pathinfo($file,PATHINFO_EXTENSION));
}

/**
 * Suggest Site Path
 *
 * Suggestion function for SITEURL variable
 *
 * @since 2.04
 * @uses $GSAMIN
 * @uses http_protocol
 * @author ccagle8
 *
 * @param bool $parts 
 * @return string
 */
function suggest_site_path($parts=false, $protocolRelative = false) {
	global $GSADMIN;
	$protocol   = $protocolRelative ? '' : http_protocol().':';
	$path_parts = pathinfo(htmlentities(getScriptFile(), ENT_QUOTES));
	$path_parts = str_replace("/".$GSADMIN, "", $path_parts['dirname']);
	$port       = ( $p=$_SERVER['SERVER_PORT'] ) != '80' && $p != '443' ? ':'.$p : '';
	
	if($path_parts == '/') {
		$fullpath = $protocol."//". htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES) . $port . "/";
	} else {
		$fullpath = $protocol."//". htmlentities($_SERVER['SERVER_NAME'], ENT_QUOTES) . $port . $path_parts ."/";
	}
		
	if ($parts) {
		return $path_parts;
	} else {
		return $fullpath;
	}
}

/**
 * Myself 
 *
 * Returns the page itself 
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param bool $echo
 * @return string
 */
function myself($echo=true) {
	if ($echo) {
		echo htmlentities(basename(getScriptFile()), ENT_QUOTES);
	} else {
		return htmlentities(basename(getScriptFile()), ENT_QUOTES);
	}
}

/**
 * Get Available Themes 
 * @todo  unused, actually returns templates for a theme it seems
 *
 * @since 2.04
 * @uses GSTHEMESPATH
 * @author ccagle8
 *
 * @param string $temp
 * @return array
 */
function get_themes($temp) {
	$themes_path   = GSTHEMESPATH . $temp .'/';
	$themes_handle = opendir($themes_path);
	while ($file   = readdir($themes_handle))	{
		if( is_file($themes_path . $file) && $file != "." && $file != ".." ) {
			$templates[] = $file;
		}
	}
	sort($templates);	
	return $templates;
}


/**
 * HTML Decode 
 *
 * @since 2.04
 * @author ccagle8
 *
 * @param string $text
 * @return string
 */
function htmldecode($text) {
	return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
}

/**
 * convert string to lower case and is multibyte-safe
 *
 * @since 2.04
 *
 * @param string $text
 * @return string converted to lowercase
 */
function lowercase($text) {
	if (function_exists('mb_convert_case')) {
		$text = mb_convert_case($text, MB_CASE_LOWER, 'UTF-8');
	} else {
		$text = strtolower($text); 
	}
	
	return $text;
}


/**
 * convert a string to UPPER CASE and is multibyte-safe
 *
 * @since 2.04
 *
 * @param string $text
 * @return string converted to UPPERCASE
 */
function uppercase($text) {
	if (function_exists('mb_convert_case')) {
		$text = mb_convert_case($text, MB_CASE_UPPER, 'UTF-8');
	} else {
		$text = strtoupper($text);
	}

	return $text;
}

/**
 * convert string to Title Case and is multibyte-safe
 *
 * @since 3.4
 *
 * @param string $text
 * @return string converted to Titlecase
 */
function titlecase($text) {
	if (function_exists('mb_convert_case')) {
		$text = mb_convert_case($text, MB_CASE_TITLE, 'UTF-8');
	} else {
		$text = ucwords($text);
	}

	return $text;
}


/**
 * Find AccessKey
 *
 * Provides a simple way to find the accesskey defined by translators as
 * accesskeys are language dependent. accesskeys are wrapped in  <em></em> tags
 * 
 * @param string $string, text from the i18n array
 * @return string
 */
function find_accesskey($string) {
	$found   = array();
	$matched = preg_match('/<em>([a-zA-Z])<\/em>/', $string, $found);
	if ($matched != 1) {
		 return null;
	}
	return strtolower($found[1]);
}

/**
 * clean ids for use as indexes
 *
 * Removes characters that don't work in URLs or IDs
 * Mostly used for filenames for slugs and user names
 * 
 * @param string $text
 * @return string
 */
function _id($text) {
	$text = to7bit($text, "UTF-8");
	$text = clean_url($text);
	$text = preg_replace('/[[:cntrl:]]/', '', $text); //remove control characters that cause interface to choke
	return lowercase($text);
}

/**
 * Defined Array
 * Checks an array of PHP constants and verifies they are defined
 * @todo  unused, what is it for ?
 * 
 * @param array $constants
 * @return bool
 */
function defined_array($constants) {
	$defined = true;
	foreach ($constants as $constant) {
		if (!defined($constant)) {
			$defined = false;
			break;
		}
	}
	return $defined;
}


/**
 * Is Folder Empty
 *
 * Check to see if a folder is empty or not
 * 
 * @param string $folder
 * @return bool true if empty
 */
function check_empty_folder($folder) {
	return folder_items($folder) == 0;
			}


/**
 * Folder Items
 *
 * Return the count of items within the given folder
 * 
 * @param string $folder
 * @return int count of folder items
 */
function folder_items($folder) {
	return count(getFiles($folder));
			}

/**
 * Validate a URL String
 * does not detect malicious injection at all!
 * 
 * @param string $u
 * @return mixed false if filter fails, str otherwise
 */
function validate_url($u) {
	return filter_var($u,FILTER_VALIDATE_URL);
}


/**
 * Format XML, adds indentation
 * 
 * @param string $xml
 * @return string xml str re-formatted with spaces and newlines
 */
function formatXmlString_legacy($xml) {  
	
	// add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
	$xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);
	
	// now indent the tags
	$token      = strtok($xml, "\n");
	$result     = '';      // holds formatted version as it is built
	$pad        = 0;       // initial indent
	$matches    = array(); // returns from preg_matches()
	
	// scan each line and adjust indent based on opening/closing tags
	while ($token !== false) : 
	
		// test for the various tag states
		
		// 1. open and closing tags on same line - no change
		if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
			$indent=0;
		// 2. closing tag - outdent now
		elseif (preg_match('/^<\/\w/', $token, $matches)) :
			$pad--;
		// 3. opening tag - don't pad this one, only subsequent tags
		elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
			$indent=1;
		// 4. no indentation needed
		else :
			$indent = 0; 
		endif;
		
		// pad the line with the required number of leading spaces
		$line    = str_pad($token, strlen($token)+$pad, ' ', STR_PAD_LEFT);
		$result .= $line . "\n"; // add to the cumulative result, with linefeed
		$token   = strtok("\n"); // get the next token
		$pad    += $indent;      // update the pad size for subsequent lines    
	endwhile; 
	
	return $result;
}

/**
   * formats the xml output readable, accepts simplexmlobject or string
   * @param mixed  $data instance of SimpleXmlObject or string
   * @return string of indented xml-elements
   */
  function formatXmlString($data){
 
	if(gettype($data) === 'object') $data = $data->asXML();

    //Format XML to save indented tree rather than one line
  	$dom = new DOMDocument('1.0');
  	$dom->preserveWhiteSpace = false;
  	$dom->formatOutput = true;
  	$dom->loadXML($data);
 
  	$ret = $dom->saveXML();
  	return $ret;
  }

/**
 * Check Server Protocol
 * 
 * Checks to see if the website should be served using HTTP or HTTPS
 *
 * @since 3.1
 * @return string
 */
function http_protocol() {
	if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
		return 'https';
	} else {
		return 'http';
	}
}

/**
 * Get File Mime-Type
 * 
 * uses finfo_open if exists, fallback to mime_content_type
 * 
 * @since 3.1
 * @param $file, absolute file path
 * @return mixed string mime type, false on failure
 */
function file_mime_type($file) {
	if (!file_exists($file)) {
		return false;
		exit;
	}
	if(function_exists('finfo_open')) {
		# http://www.php.net/manual/en/function.finfo-file.php
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimetype = finfo_file($finfo, $file);
		finfo_close($finfo);
		
	} elseif(function_exists('mime_content_type')) {
		# Deprecated: http://php.net/manual/en/function.mime-content-type.php
		$mimetype = mime_content_type($file);
	} else {
		return false;
		exit;	
	}
	return $mimetype;
}

/**
 * Check Is FrontEnd
 * Checks to see if the you are on the frontend or not
 *
 * @since 3.1
 * @return bool
 */
function is_frontend() {
	return GSBASE;
	}

/**
 * Get Installed GetSimple Version
 *
 * This will return the version of GetSimple that is installed
 *
 * @since 1.0
 * @uses GSVERSION
 *
 * @param bool $echo Optional, default is true. False will 'return' value
 * @return string Echos or returns based on param $echo
 */
function get_site_version($echo=true) {
	include(GSADMININCPATH.'configuration.php');
	if ($echo) {
		echo GSVERSION;
	} else {
		return GSVERSION;
	}
}


/**
 * Get GetSimple Language
 * 
 * @since 3.1
 * @uses $LANG
 *
 * @param bool $short return full or short lang codes
 */
function get_site_lang($short=false) {
	global $LANG;
	if ($short) {
		return preg_replace('/(?:(?<=([a-z]{2}))).*/', '', $LANG); # @todo why the complicated regex?
	} else {
		return $LANG;
	}
}

/**
 * Convert to Bytes
 * convert M/G/K byte string to bytes
 * 100M returns 100*1024*1024
 * @since 3.0
 *
 * @param $str string
 * @return string
 */
function toBytes($str){
	$val = trim($str);
	$last = strtolower($str[strlen($str)-1]);
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
	return $val;
}

/**
 * convert bytes to M/G/K string
 * @param  str  $str    size in bytes
 * @param  str $suffix G/M/K string
 * @param  boolean $outputsuffix add suffix to end of str
 * @param  int $precision precision for rounding
 * @return str new byte string
 */
function toBytesShorthand($str,$suffix = 'M',$outputsuffix = false, $precision = 2){
	$val  = trim($str);
	$suffix = strtolower($suffix);
	switch($suffix) {
		case 'g': $val /= 1024;
		case 'm': $val /= 1024;
		case 'k': $val /= 1024;
	}

	$val = round($val,(int)$precision);
	return $val. ($outputsuffix ? strtoupper($suffix.'B') : '');
}

/**
 * Remove Relative Paths, NOT TO BE USED FOR SECURITY
 * removes ../ path info from file path simply
 *
 * @since 3.1
 *
 * @param $file string
 * @return string
 */
function removerelativepath($file) {
	while(strpos($file,'../')!==false) { 
		$file = str_replace('../','',$file);
	}
	return $file;
}

/**
 * Return a directory of files and folders
 *
 * @since 3.1
 *
 * @param $directory string directory to scan
 * @param $recursive boolean whether to do a recursive scan or not. 
 * @return array or files and folders
 */
function directoryToArray($directory, $recursive = true) {
	$array_items = array();
	if ($handle = opendir($directory)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != "thumbs.db") {
				if (is_dir($directory. "/" . $file)) {
					if($recursive) {
						$array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
					}
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				} else {
					$file = $directory . "/" . $file;
					$array_items[] = preg_replace("/\/\//si", "/", $file);
				}
			}
		}
		closedir($handle);
	}
	return $array_items;
}

/**
 * Return a directory of files and folders with heirarchy and additional data
 *
 * @since 3.1.3
 *
 * @param $directory string directory to scan
 * @param $recursive boolean whether to do a recursive scan or not.
 * @param $exts array file extension include filter, array of extensions to include
 * @param $exclude bool true to treat exts as exclusion filter instead of include
 * @return multidimensional array or files and folders {type,path,name}
 */
function directoryToMultiArray($dir,$recursive = true,$exts = null,$exclude = false) {
	// $recurse is not implemented

	$result = array();
	$dir = rtrim($dir,DIRECTORY_SEPARATOR);

	$cdir = scandir($dir);
	foreach ($cdir as $key => $value)	{
		if (!in_array($value,array(".",".."))) {
			if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
					if(!$recursive) continue;
					$path =  preg_replace("#\\\|//#", "/", $dir . '/' . $value . '/');
					$result[$value] = array();
					$result[$value]['type'] = "directory";
					$result[$value]['path'] = $path;
					$result[$value]['dir'] = $value;
					$result[$value]['value'] = call_user_func(__FUNCTION__,$path,$recursive,$exts,$exclude);
			}
			else {
				$path =  preg_replace("#\\\|//#", "/", $dir . '/');
				// filetype filter
				$ext = getFileExtension($value);
				if(is_array($exts)){
				if(!in_array($ext,$exts) and !$exclude) continue;
				if($exclude and in_array($ext,$exts)) continue;
			}

			$result[$value] = array();
			$result[$value]['type'] = 'file';
			$result[$value]['path'] = $path;
			$result[$value]['value'] = $value;
			}
		}
	}

	return $result;
}

/**
 * Returns definition safely
 * 
 * @since 3.1.3
 * 
 * @param str $id 
 * @param bool $isbool treat definition as boolean and cast it
 * @return * returns definition or null if not defined
 */
function getDef($id,$isbool = false){
	if( defined($id) ) {
		if($isbool) return (bool) constant($id);
		return constant($id);
	}
}

/**
 * Alias for checking for debug constant
 * @since 3.2.1
 * @return  bool true if debug enabled
 */
function isDebug(){
	return getDef('GSDEBUG',true);
}

/**
 * check gs version is Alpha
 *
 * @since  3.3.0
 * @return boolean true if Alpha release
 */
function isAlpha(){
	return strPos(get_site_version(false),"a");
}

/**
 * check gs version is Beta
 *
 * @since  3.3.0
 * @return boolean true if beta release
 */
function isBeta(){
	return strPos(get_site_version(false),"b");
}

/**
 * Check if request is an ajax request
 * @since  3.3.0
 * @return bool true if ajax
 */
function requestIsAjax(){
	return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || isset($_GET['ajax']);
}

/**
 * check if array is multidimensional
 * @since 3.3.2
 * @param  mixed $ary
 * @return bool true if $ary is a multidimensional array
 */
function arrayIsMultid($ary){
	return is_array($ary) && ( count($ary) != count($ary,COUNT_RECURSIVE) );
}

/**
 * normalizes str or array inputs to js array strings, always returns js array string syntax
 * used for ckeditro toolbar arrays for the most part
 * 
 * @since 3.3.2
 * @param mixed $var string or array var to convert to js array syntax
 * @return str  js array string syntax
 */
function returnJsArray($var){
	
	if(!$var) return;

	if(!is_array($var)) {
		// if looks like an array string try to parse as array
		if(strrpos($var, '[') !==false){
			// normalize array strings
			$var = stripslashes($var);         // remove escaped quotes
			$var = trim(trim($var),',');       // remove trailing commas
			$var = str_replace('\'','"',$var); // replace single quotes with double (for json)
			
			$ary = json_decode($var);
			
			// add primary nest if missing
			if(!is_array($ary) || !arrayIsMultid($ary) ) $ary = json_decode('['.$var.']');
			
			// if proper array use it
			if(is_array($ary) ) $var = json_encode($ary);
			else $var = "'".trim($var,"\"'")."'"; 
		} 
		else{
			// else quote wrap string, trim to avoid double quoting
			$var = "'".trim($var,"\"'")."'";
		}	
	} 
	else {
		// convert php array to js array
		$var = json_encode($var);
	}

	return $var;
}


/**
 * Returns status of mode rewrite via apache_get_modules 
 * or custom HTTP_MOD_REWRITE env set in .htaccess
 * @return bool true if on false if not, null if unknown
 */
function hasModRewrite(){
	if(getenv('HTTP_MOD_REWRITE') == 'On') return true;
	
	if ( function_exists('apache_get_modules') ) {
		if(in_arrayi('mod_rewrite',apache_get_modules()) ) {	
			return true;
		}	
	}
}

/**
 * checks if is current page is not an install page or stylesheet
 * @return bool true if we not in an install file
 */
function notInInstall(){
	return ( get_filename_id() != 'install' && get_filename_id() != 'setup' && get_filename_id() != 'update' && get_filename_id() != 'style' );
}

/**
 * Returns a path relative to GSROOTPATH or optional root path
 * @todo  probably not fully windows drive safe, convert slashes to match
 * @since 3.4
 * @param  string $path full file path
 * @param  string $root optional root path, defaults to GSROOTPATH
 * @return string       relative file path
 */
function getRelPath($path,$root = GSROOTPATH ){
	$relpath = str_replace($root,'',$path);
	return $relpath;
}

/**
 * Returns a URI path relative to root path
 * @since 3.4
 * @param  string $path full URI path
 * @param  string $root optional root path
 * @return string       relative URI path
 */
function getRelURIPath($path,$root ){
	$relpath = str_replace($root,'',$path);
	return $relpath;
}

/**
 * get URI relative to SITEURL base
 * @since  3.4
 * @return str URI path
 */
function getRelRequestURI(){
	GLOBAL $SITEURL;
	$pathParts   = str_replace('//','/',parse_url($_SERVER['REQUEST_URI'])); # ignore double slashes in path
	$relativeURI = getRelURIPath($pathParts['path'],getRootRelURIPath($SITEURL));
	return $relativeURI;
}

/**
 * returns relative URI path or matching mask array
 *
 * @since  3.4
 * @param  mixed $mask array or string of path keys
 * @return mixed       if mask was provided returns an array mathching path values to keys or empty, else returns string of path
 */
function getURIPath($inputmask = null, $pad = false){
	$relativeURI = no_tsl(getRelRequestURI());
	$URIpathAry  = explode('/',no_tsl($relativeURI));

	if(gettype($inputmask) == 'string') $inputmask = explode('/',$inputmask);

	if($inputmask){
		// assigning path vars to key mask
		$maskCnt = count($inputmask);
		$URIcnt  = count($URIpathAry);
		$mask    = array_combine($inputmask,array_fill(0,$maskCnt,'')); # flip array with empty values so padding has indices to work with

		if($maskCnt == $URIcnt){
			// mask count matches path count			
			$mask = array_combine(array_keys($mask),$URIpathAry);
			return $mask;
		}
		else if($URIcnt > $maskCnt && in_array('%path%',$inputmask)){
			// mask contains %path% token
			// so splice it out of the path and implode it back in
			$start  = array_search('%path%',$mask);
			$length = ($URIcnt - $maskCnt) + 1;

			$URIpathAry = spliceCompressArray($URIpathAry,$start,$length);			
			$URIpathAry[$start] = implode('/',$URIpathAry[$start]);
			
			// debugLog($URIpathAry);
			// debugLog($mask);

			$mask = array_combine(array_keys($mask),$URIpathAry);			
			return $mask;
		}
		else if(($URIcnt > $maskCnt) && $pad){
			// path is larger than mask, overload if pad
			// @todo splice left OR splice right option
			// using pad right for now
			$mask = array_pad($mask,count($URIpathAry),'');
			$mask = array_combine(array_keys($mask),$URIpathAry);
			return $mask;
		}
		else {
			// Mask is larger than URI, return relativeurl as single elem array
			return array($relativeURI);
		}
	}
	else {
		// no mask specificed so simple str return
		return $relativeURI;
	}
}

function pathToAry($path){
	return explode('/',$path);
}

/**
 * compresses a range into a single element using slice
 * 
 * $test = array('one','two','three','four');
 * spliceCompressArray($test,2,2);
 * 
 * Array
 * (
 *     [0] => one
 *     [1] => Array
 *         (
 *             [0] => two
 *             [1] => three
 *         )
 * 
 *     [2] => four
 * )
 * @param  array $array    input array
 * @param  int $startidx   start index to splice at
 * @param  int $length     length of splice
 * @return array           new truncated array with range spliced out and reinserted at startidx
 */
function spliceCompressArray($array,$startidx,$length){
	$slice = array_splice($array,$startidx,$length,'');
	$array[$startidx] = $slice;
	return $array;
}


/**
 * get web root-relative url
 * parses the host:// part and removes it
 *
 * @since  3.4
 * @var str url to normalize
 */
function getRootRelURIPath($url){
  $urlparts = parse_url($url);
  $strip    = isset($urlparts['scheme']) ? $urlparts['scheme'] .':' : '';
  $strip   .=  '//';
  $strip   .= isset($urlparts['host']) ? $urlparts['host'] : '';
  $strip   .= isset($urlparts['port']) ? ':'.$urlparts['port'] : '';
  // debugLog(__FUNCTION__.' base = ' . $strip);
  if(strpos($url,$strip) === 0) return str_replace($strip,'',$url);
  return $url;
}


/**
 * returns a global, easier inline usage of readonly globals
 * @since  3.4
 * @param  str $var variable name
 * @return global
 */
function getGlobal($var) {
	global $$var;
	return $$var;
}

/** 
 * returns a page global
 * currently an alias for getGlobal, 
 * used specificly for globals used in theme_function for front end current page vars
 *
 * @since 3.4
 */
function getPageGlobal($var){
	return getGlobal($var);
}

/**
 * echo or return toggle
 * @since  3.4
 * @param str $str 
 * @param bool $echo default true, echoes or returns $str
 */
function echoReturn($str,$echo = true){
	if (!$echo) return $str;
	echo $str;
}

/**
 * clamps / normalizes an integer reference to specified value
 * @since 3.4
 * @param int &$var reference to input to be clamped
 * @param int $min minimum to enforce clamp value
 * @param int $max maximum to enforce clamp value
 * @param type $default default to set if input is not set
 */
function clamp(&$var,$min=null,$max=null,$default=null){
	if(is_numeric($var)){
		if(is_numeric($min) && $var < $min) $var = $min;
		if(is_numeric($max) && $var > $max) $var = $max;
	}
	if(isset($default)) setDefault($var,$default);
}

/**
 * set reference input to a default value if input is not set
 * does no type checking or conversions on default
 * @since 3.4
 * @param $value   reference
 * @param $default default value to set
 */
function setDefault(&$var = '',$default){
	if(!isset($var) || empty($var)) $var = $default;
}

/**
 * check if version checking is allowed via GSNOVERCHECK and not an auth page
 * @since 3.4
 * @return bool true is version check is allowed
 */
function allowVerCheck(){
	return !isAuthPage() && !getDef('GSNOVERCHECK');
}

/**
 * retrieve the version check data obj
 * @since  3.4
 * @return obj api json decoded obj on success
 */
function getVerCheck(){
	# check to see if there is a core update needed
	$data = get_api_details();
	if ($data)	{
		return json_decode($data);
	}else {
		return null;
	}
}

/**
 * include a theme template file 
 * will auto include functions.php if exists and $functions is true
 * automatically falls back to GSTEMPLATEFILE if $template_file is missing
 * 
 * @since  3.4
 * @param  str $template      template name
 * @param  str $template_file template filename, fallback to GSTEMPLATEFILE if not exist
 * @param  bool $functions    true, auto include functions.php
 */
function includeTheme($template, $template_file = GSTEMPLATEFILE, $functions = true){
	# include the functions.php page if it exists within the theme
	if ( $functions && file_exists(GSTHEMESPATH .$template."/functions.php")) {
		include_once(GSTHEMESPATH .$template."/functions.php");
	}

	# include the template and template file set within theme.php and each page
	if ( (!file_exists(GSTHEMESPATH .$template."/".$template_file)) || ($template_file == '') ) { $template_file = GSTEMPLATEFILE; }
	include(GSTHEMESPATH .$template."/".$template_file);
}

/**
 * get the current accessed script file
 * @since 3.4
 * @return str  path to script filename
 */
function getScriptFile(){
	return $_SERVER['SCRIPT_NAME'];
}

/**
 * get custom locale as defined in i18n
 * @since 3.4
 * @return str
 */
function getLocaleConfig(){
	return i18n_r("LOCALE",null);
}

/**
 * get date format as defined in i18n
 * @since 3.4
 * @return str date format string
 */
function getDateFormat(){
	return i18n_r("DATE_FORMAT",getDef('GSDATEFORMAT'));
}
/**
 * get date time format as defined in i18n
 * @since 3.4
 * @return str date time format string
 */
function getDateTimeFormat(){
	return i18n_r("DATE_AND_TIME_FORMAT",getDef('GSDATETIMEFORMAT'));
}
/**
 * get date time format as defined in i18n
 * @since 3.4
 * @return str date time format string
 */
function getTimeFormat(){
	return i18n_r("TIME_FORMAT",getDef('GSTIMEFORMAT'));
}
/**
 * get transliteration set as defined in i18n
 * @since 3.4
 * @return str
 */
function getTransliteration(){
	return i18n_r("TRANSLITERATION",null);
}

/**
 * set php locale
 * @since 3.4
 * @param str $locale a csv locale str
 * @return str set locale, false on fail
 */
function  setCustomLocale($locale,$category = LC_ALL){
	// split locale string into array, removing whitespace and empties
	if($locale) {
		$localestr = preg_split('/\s*,\s*/', trim($locale), -1, PREG_SPLIT_NO_EMPTY);
		debugLog('setting locale: ' . implode(',',$localestr));
		$result    = setlocale($category, $localestr);
		return $result;
	}
}

/**
 * save old locale to global for restore
 */
function setOldLocale($category = LC_ALL){
	GLOBAL $OLDLOCALE;
	$OLDLOCALE = setlocale($category,0);
	return $OLDLOCALE;
}

/**
 * set a new locale from i18n
 */
function setNewLocale($category = LC_ALL){
	GLOBAL $NEWLCOALE;
	setOldLocale();
	$NEWLOCALE = setCustomLocale(getLocaleConfig(),$category);
	return $NEWLOCALE;
}

/**
 * restore OLDLOCALE
 */
function restoreOldLocale($category = LC_ALL){
	GLOBAL $OLDLOCALE,$NEWLOCALE;
	$NEWLOCALE = null;
	setCustomLocale($OLDLOCALE,$category);
}

/**
 * Merge GSMERGELANG, a fallback language, into i18n
 * This is a default lang to load after the custom lang to
 * avoid empty lang tokens not found in the custom lang
 *
 * @since 3.4
 * @global $LANG
 */
function i18n_mergeDefault(){
	GLOBAL $LANG;
	// Merge in default lang to avoid empty lang tokens
	// if GSMERGELANG is undefined or false merge GSDEFAULTLANG else merge custom
	if(getDef('GSMERGELANG', true) !== false and !getDef('GSMERGELANG', true) ){
		if($LANG !=GSDEFAULTLANG)	i18n_merge(null,GSDEFAULTLANG);
	} else{
		// merge GSMERGELANG defined lang if not the same as $LANG
		if($LANG !=getDef('GSMERGELANG') ) i18n_merge(null,getDef('GSMERGELANG'));
	}
}

/**
 * get the gs editor height config
 * @since 3.4
 * @return str string with height units
 */
function getEditorHeight(){
	if (getDef('GSEDITORHEIGHT')) return getDef('GSEDITORHEIGHT') .'px';
}

/**
 * get the gs editor language
 * returns GSEDITORLANG if set, else returns i18n[CKEDITOR_LANG] if it exists
 * 
 * @since 3.4
 * @return str
 */
function getEditorLang(){
	if (getDef('GSEDITORLANG')) return getDef('GSEDITORLANG');
	else if (file_exists(GSADMINTPLPATH.'js/ckeditor/lang/'.i18n_r('CKEDITOR_LANG').'.js')){
		return i18n_r('CKEDITOR_LANG');
	}
}

/**
 * get the gs editor custom options
 * @since 3.4
 * @return str js config string
 */
function getEditorOptions(){
	if (getDef('GSEDITOROPTIONS') && trim(getDef('GSEDITOROPTIONS'))!="" ) return getDef('GSEDITOROPTIONS');
}

/**
 * get the gs editor custom toolbar
 * @since 3.4
 * @return str valid js nested array ([[ ]]) or escaped toolbar id ('toolbar_id')
 */
function getEditorToolbar(){
	if (getDef('GSEDITORTOOL')) $edtool = getDef('GSEDITORTOOL');
	if($edtool == "none") $edtool = null; // toolbar to use cke default
	// if($edtool === null) $edtool = 'null'; // not supported in cke 3.x

	// at this point $edtool should always be a valid js nested array ([[ ]]) or escaped toolbar id ('toolbar_id')
	return returnJsArray($edtool);
}

/**
 * get defined timezone from user->site->gsconfig fallbacks
 * @since 3.4
 * @return str timezone identifier
 */
function getDefaultTimezone(){
	GLOBAL $USRTIMEZONE, $SITETIMEZONE;
	if(!empty($USRTIMEZONE)) return $USRTIMEZONE;
	if(!empty($SITETIMEZONE)) return $SITETIMEZONE;
	if(getDef('GSTIMEZONE')) return getDef('GSTIMEZONE');
	return @date_default_timezone_get();
}

/**
 * set defined timezone
 *
 * @since 3.4
 * @param str timezone identifier http://us3.php.net/manual/en/timezones.php
 */
function setTimezone($timezone){
	if(isset($timezone) && ($timezone != "" || stripos($timezone, '--')) ) {
		date_default_timezone_set($timezone);
	}
}

/**
 * gets website data from GSWEBSITEFILE
 *
 * @todo use a custom schema array for extracting fields
 * @since 3.4
 * @param  boolean $returnGlobals return as obj or array of vars
 * @return mixed    depending on returnGlobals returns xml as object or a defined var array for global extraction
 */
function getWebsiteData($returnGlobals = false){
	$SITENAME    = '';
	$SITEURL     = '';
	$SITEURL_REL = '';
	$SITEURL_ABS = '';
	$ASSETURL    = '';
	$ASSETPATH   = '';

	if (file_exists(GSDATAOTHERPATH .GSWEBSITEFILE)) {
		$dataw        = getXML(GSDATAOTHERPATH .GSWEBSITEFILE,false);
		$SITENAME     = stripslashes( $dataw->SITENAME);
		$SITEURL      = trim((string) $dataw->SITEURL);
		$TEMPLATE     = trim((string) $dataw->TEMPLATE);
		$PRETTYURLS   = trim((string) $dataw->PRETTYURLS);
		$PERMALINK    = trim((string) $dataw->PERMALINK);
		$SITEEMAIL    = trim((string) $dataw->EMAIL);
		$SITETIMEZONE = trim((string) $dataw->TIMEZONE);
		$SITELANG     = trim((string) $dataw->LANG);
		$SITEUSR      = trim((string) $dataw->SITEUSR);
		$SITEABOUT    = trim((string) $dataw->SITEABOUT);
		$SAFEMODE     = trim((string) $dataw->SAFEMODE == '1');

		$SITEURL_ABS = $SITEURL;
		$SITEURL_REL = getRootRelURIPath($SITEURL);
		// $ASSETURL    = $SITEURL;

		// asseturl is root relative if GSASSETURLREL is true
		// else asseturl is scheme-less ://url if GSASSETSCHEMES is not true
		if(getDef('GSASSETURLREL',true)) $ASSETURL = $SITEURL_REL;
		else if(getDef('GSASSETSCHEMES',true) !==true) str_replace(parse_url($SITEURL, PHP_URL_SCHEME).':', '', $SITEURL);
		else $ASSETURL = $SITEURL;

		$ASSETPATH = $ASSETURL.tsl(getRelPath(GSADMINTPLPATH,GSADMINPATH));

		// SITEURL is root relative if GSSITEURLREL is true
		if(getDef('GSSITEURLREL')){
			$SITEURL = $SITEURL_REL;
		}
	}
	else {
		debugLog("website file not found " . GSDATAOTHERPATH .GSWEBSITEFILE);
	}

	if($returnGlobals) return get_defined_vars();
	return $dataw;
}

/**
 * gets user data from cookie_user.xml
 * 
 * @since 3.4
 * @todo use a custom schema array for extracting fields
 * @param  boolean $returnGlobals return as obj or array of vars
 * @return mixed    depending on returnGlobals returns xml as object or a defined var array for global extraction
 */
function getUserData($returnGlobals = false){

	if (isset($_COOKIE['GS_ADMIN_USERNAME'])) {
		$cookie_user_id = _id($_COOKIE['GS_ADMIN_USERNAME']);
		if (file_exists(GSUSERSPATH . $cookie_user_id.'.json')) {
			$datau      = getXML(GSUSERSPATH  . $cookie_user_id.'.json');
			$USR        = stripslashes($datau->USR);
			$HTMLEDITOR = (string) $datau->HTMLEDITOR;
			$USRTIMEZONE= (string) $datau->TIMEZONE;
			$USRLANG    = (string) $datau->LANG;
		} else {
			$USR = null;
		}
	} else {
		$USR = null;
	}

	unset($cookie_user_id);
	if($returnGlobals) return get_defined_vars();
	return $datau;
}

/**
 * get the super user
 * super user is either the user that installed GS or GSSUPERUSER if set
 * @since  3.4
 * @return str superuser id
 */
function getSuperUserId(){
	GLOBAL $SITEUSR;
	$usr = '';
	if(getDef('GSSUPERUSER')) $usr = getDef('GSSUPERUSER');
	if(!empty($usr)) return $usr;
	if(!empty($SITEUSR)) return $SITEUSR;
	// @todo check if only 1 user exists and make that the super user
	return;
}

/**
 * get default salt
 * auto handle GSUSECUSTOMSALT and GSAUTHFILE
 * @since  3.4
 * @return str salt
 */
function getDefaultSalt(){
	$salt = null;
	if (defined('GSUSECUSTOMSALT')) {
		// use GSUSECUSTOMSALT
		$salt = sha1(getDef('GSUSECUSTOMSALT'));
	}
	else {
		// use from GSAUTHFILE
		if (file_exists(GSDATAOTHERPATH .GSAUTHFILE)) {
			$dataa = getXML(GSDATAOTHERPATH .GSAUTHFILE);
			$salt  = stripslashes($dataa->apikey);
		}
	}

	return $salt;
}

/**
 * get the default language user->site->gsconfig->GSDEFAULTLANG->filesystem fallback
 * @since  3.4
 * @return str IETF langcode
 */
function getDefaultLang(){
	GLOBAL $USRLANG, $SITELANG;

	if(isset($USRLANG) && !empty($USRLANG))  return $USRLANG;
	if(isset($SITELANG) && !empty($SITELANG)) return $SITELANG;
	if(getDef('GSLANG')) return getDef('GSLANG');

	// get language files
	$filenames = glob(GSLANGPATH.'*.php');
	$cntlang   = count($filenames);
	if ($cntlang == 1) {
		// 1 file , assign lang to only existing file
		return basename($filenames[0], ".php");
	} elseif($cntlang > 1 && in_array(GSLANGPATH .GSDEFAULTLANG.'.php',$filenames)) {
		// prefer GSDEFAULTLANG as default if available
		return GSDEFAULTLANG;
	} elseif(isset($filenames[0])) {
		// else fallback to first lang found
		return basename($filenames[0], ".php");
	} else {
		return ''; // no languages available
	}
}

/**
 * perform transliteration conversion on string
 * @since  3.4
 * @param  str $str string to convert
 * @return str      str after transliteration replacement array ran on it
 */
function doTransliteration($str){
	if (getTransliteration() && is_array($translit=getTransliteration()) && count($translit>0)) {
		$str = str_replace(array_keys($translit),array_values($translit),$str);
	}
	return $str;
}

/**
 * output debuglog
 * @since  3.4
 */
function outputDebugLog(){
	global $GS_debug;
	echo '<h2>'.i18n_r('DEBUG_CONSOLE').'</h2><div id="gsdebug">';
	echo '<pre>';
	foreach ($GS_debug as $log){
			if(is_array($log)) print_r($log).'<br/>';
			else print($log.'<br/>');
	}
	echo '</pre>';
	echo '</div>';
}

/**
 * compress css
 * @since  3.4
 * @param  str $buffer css to compress
 * @return str         compressed css code
 */
function cssCompress($buffer) {
  $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); /* remove comments */
  $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer); /* remove tabs, spaces, newlines, etc. */
  return $buffer;
}

/**
 * get the maximum upload size as defined by php ini
 * @since  3.4
 * @return int max bytes
 */
function getMaxUploadSize(){
	$max_upload   = toBytes(ini_get('upload_max_filesize'));
	$max_post     = toBytes(ini_get('post_max_size'));
	$memory_limit = toBytes(ini_get('memory_limit'));
	$upload_mb    = min($max_upload, $max_post, $memory_limit);
	return $upload_mb;
}

/**
 * get the global siteurl
 * @since 3.4
 * @param  $absolute force absolute url
 * @return str
 */
function getSiteURL($absolute = false){
	return $absolute ? getGlobal('SITEURL_ABS') : getGlobal('SITEURL');
}

/**
 * check if host is windows
 * @since  3.4
 * @return bool true if windows
 */
function hostIsWindows(){
	return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
}

/**
 * cath output via buffering
 * @since  3.4
 * @param  str $function function to execute
 * @param  mixed $args   arguments for function
 * @return str           output of function output buffer
 */
function catchOutput($function,$args){
	ob_start();
	call_user_func_array($function,$args);
	return ob_get_clean();
}

/**
 * return tags string as array
 * explodes on delim, lowers case, and trims keyword strings
 * @since 3.4
 * @param string $str string of delimited keywords
 * @param bool	$case preserve case if true, else lower
 * @param str	$delim delimiter for splitting
 * @return array      returns array of tags
 */
function tagsToAry($str,$case = false,$delim = ','){
	if(!$case) $str = lowercase($str);
	$ary = explode($delim,$str);
	$ary = array_map('trim',$ary);
	return $ary;
}

function toggleSafeMode($enable = true){
	GLOBAL $SAFEMODE, $dataw;
	$SAFEMODE = $enable;
	backup_datafile(GSDATAOTHERPATH . GSWEBSITEFILE);
	
	if(!$dataw){
		if(file_exists(GSDATAOTHERPATH . GSWEBSITEFILE)){
			$dataw = getXML(GSDATAOTHERPATH . GSWEBSITEFILE,false);
		} else return false;	
	}
	$dataw->editAddChild('SAFEMODE',$enable ? 1 : 0);
	return XMLSave($dataw,GSDATAOTHERPATH . GSWEBSITEFILE);
}

function enableSafeMode(){
	return toggleSafeMode(true);
}	

function disableSafeMode(){
	return toggleSafeMode(false);
}	

function safemodefail($action = '',$url = ''){
	GLOBAL $SAFEMODE;
	// @todo add secfilter here to override default behavior
	if($SAFEMODE){
		redirect($url ."&error=".urlencode(i18n_r('ER_SAFEMODE_DISALLOW')));
		die();
	}	
}

/**
 * **************************************************************************** 
 * Array Helpers
 * **************************************************************************** 
 * 
 * php <php 5.6 does not support array_filter by keys and values, so we use our own methods
 * these are not backports! however
 * 
 */

/**
 * filter an array using a callback function on subarrays
 * 
 * @param  array $array        array to filter
 * @param  callable $callback  callback that returns true or false
 * @param  array $callbackargs arguments for callback function, callable(array[n],args)
 * @return array filtered array
 */
function filterArray($array,$callback,$callbackargs){
	if (function_exists($callback)){
		foreach ($array as $key => $value) {
			// filter from array if callback returns true
			if( $callback($value,$callbackargs) ){
				unset($array[$key]);
			}	
		}
		return $array;
	}
	else {
		debugLog(__FUNCTION__ . ': callback not reachable: ' . $callback);
	}
	return $array;	
}

/**
 * filter sub arrays using a callback function on keys
 * 
 * @param  array $array     array of arrays to filter
 * @param  callable $callback callback function that return true or false
 * @param  array $args     array or arguments for callback, callable(array[n]->key(array[n]),args)
 * @return array           array of arrays with select keys removed
 */
function filterSubArrayKey($array,$callback,$callbackargs){
	if (function_exists($callback)){
		foreach ($array as &$subarray) {
			foreach ($subarray as $key => $value) {
				// filter from array if callback returns true
				if( $callback($key,$callbackargs) ){
					unset($subarray[$key]);
				}
			}
		}
	} 
	else {
		debugLog(__FUNCTION__ . ': callback not reachable: ' . $callback);
	}
	return $array;
}

/**
 * matchArray
 * @param  array  $needle   input array to check
 * @param  array  $haystack subject array to check against
 * @param  boolean $keys    if true, compare $needle against $haystack keys instead of values
 * @return boolean          true if all values of needle are in array
 */
function matchArrayAll($needle,$haystack,$keys = false){
	if($keys) return count(array_intersect(array_flip($haystack),$needle)) == count($needle);
	return count(array_intersect($haystack,$needle)) == count($needle);
}

/**
 * This file is part of the array_column library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2013 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

if (!function_exists('array_column')) {

    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }

        if (!is_int($params[1])
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {

            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }

        }

        return $resultArray;
    }

}


/**
 * sends an x-frame-options heaeder
 * @since  3.4
 * @param  string $value header value to send, default `DENY`
 */
function header_xframeoptions($value = null){
	if(!isset($value)){
		if(getDef('GSNOFRAMEDEFAULT',true)) $value = getDef('GSNOFRAMEDEFAULT');
		else $value = 'DENY';
	}	
	header('X-Frame-Options: ' . $value); // FF 3.6.9+ Chrome 4.1+ IE 8+ Safari 4+ Opera 10.5+
}


/**
 * strip non printing white space from string
 * replaces various newlines and tab chars with replacement character
 * then cleans up multiple replacement characters
 * 
 * eg. strip_whitespace("Line   1\n\tLine 2\r\t\tLine 3  \r\n\t\t\tLine 4\n  "," ");
 * @since 3.3.6
 * @param  str $str     input string
 * @param  string $replace replacement character
 * @return str          new string
 */
function strip_whitespace($str,$replace = ' '){
	$chars = array("\r\n", "\n", "\r", "\t");
	$str   = str_replace($chars, $replace, $str);
	return preg_replace('/['.$replace.']+/', $replace, $str);
}

/**
 * strip shortcodes based on pattern
 * @since  3.3.6
 * @param  str $str     input string
 * @param  string $pattern regex pattern to strip
 * @return str          new string
 */
function strip_content($str, $pattern = '/[({]%.*?%[})]/'){
	if(getDef('GSCONTENTSTRIPPATTERN',true)) $pattern = getDef('GSCONTENTSTRIPPATTERN');
	return 	preg_replace($pattern, '', $str);
}

/**
 * get the chmod value for a file path
 * will check if path is a directory or file and return appropriate value
 * @since  3.4
 * @param str $path file path
 * @return chmod value
 */
function getChmodValue($path){
	if(is_dir($path)) $writeOctal = getDef('GSCHMODDIR');
	else {
		if (getDef('GSCHMODFILE')) {
			$writeOctal = getDef('GSCHMODFILE');
		}	
		else if (getDef('GSCHMOD')) {
			$writeOctal = getDef('GSCHMOD'); 
		}
		else {
			$writeOctal = 0755;
		}
	}
	return $writeOctal;
}

/** 
 * check if a file or path is writable
 * @since 3.4
 * @param  string $path file path
 * @param  str    $perms permission decimanl string to check against
 * @return boolean is writable
 */
function checkWritable($path,$perms = null){
	$writeOctal = getChmodValue($path);
	if(!isset($perms)) $perms = check_perms($path);
	// debugLog(__FUNCTION__ . ' ' . $path . ' ' . $perms .' > '. decoct($writeOctal));
	$iswritable = is_writable($path);
	$iswritable = $perms >= decoct($writeOctal);
	return $iswritable;
}


/**
 * string to boolean using custom rules
 * @since  3.4
 * @param  mixed $val input value
 * @return bool      converted boolean
 */
function strToBool($val){
	if($val == true || $val == 'true' || $val == '1') return true;
	return false;
}

/**
 * call_gs_func_array
 * wrapper for call_user_func_array
 * @since  3.4
 * @param  mixed $callable a callable
 * @param  array  $args     param_arr
 * @return mixed            callback return
 */
function call_gs_func_array($callable,$args = array()){
	$valid = false;
	// static class 
	if(is_array($callable)){
		// check for valid method
		if(count($callable) == 2){
			$obj    = $callable[0];
			$method = $callable[1];
			if(method_exists($obj,$method))	$valid = true;
		}
	}
	else if(is_closure($callable) && getDef('GSEXECANON',true)){
		// check for valid closure
		$valid = true;
	}
	else if(is_string($callable) && function_exists($callable)){
		// check for valid function
		$valid = true;
	}

	if($valid) return call_user_func_array($callable,$args);
}

/**
 * check if function is closure
 * @since  3.4
 * @param  mixed  $f function
 * @return boolean   true if closure
 */
function is_closure($func) {
    return is_object($func) && ($func instanceof Closure);
}		

/* ?> */