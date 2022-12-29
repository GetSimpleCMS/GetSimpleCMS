<?php

/**
 * Downloads a branch from github and extracts it
 * Easily modifiable to other purpose
 */

GLOBAL $tag,$checkfile,$force,$username,$password,$exc;

// Set these dependant on your github credentials
$owner = $username = $password = $reponame = $type = $typeid = $dest = "";

//Exclusion list
// array( "files" => array() , "folders" => array() )
$exc = array(
    "files" => array("deploy.php","deploy.conf","gsconfig.php"),
    "folders" => array("plugins","users","data","theme")
);

$downloadzip   = true;  // download zip file
$removefiles   = false; // remove existing destination or overwrite, use $exc and $excdirs
$extractfiles  = true;  // extract files to destination
$copysource    = true;  // copy source files from extract dir if set
$removesource  = true;  // remove source files from extract dir if set
$removezip     = true;  // remove the zip when done
$overwrite     = true;  // do not check for already installed

// get configuration
@include_once 'deploy.conf';

if(isset($deployconfig) && is_array($deployconfig)) extract($deployconfig);

if(!isset($reponame) || empty($reponame)) die('reponame not set'); 

// set script timeout
$timeLimit = 5000;
// set script memory limit
$memlimit  = 200000;

// Init
$force    = isset($_GET['force']);
$force    = true;
$repo     = $reponame;
$tag      = $type.'-'.$typeid;

if(!isset($owner) || empty($owner)) $owner = $username; // assume user is owner

$url            = "https://github.com/$owner/$repo/archive/$typeid.zip";
$zipfilename    = $reponame."_".$type."_".$typeid.".zip";
$zipfile        = $dest.$zipfilename;

$extractdirname = "$reponame-$typeid";
$extractdir     = $dest.$extractdirname;

$checkfilename  = "lastcommit.hash";
$checkfile      = $dest.$checkfilename;

$exc['files'][] = $zipfilename; // add zip file to exclude files
$exc['files'][] = $checkfilename; // add zip file to exclude files

//////////////////////////////////////////////////////
// BEGIN
//////////////////////////////////////////////////////

set_time_limit($timeLimit);
// set_memory_limit($memlimit);

echo "<h1>Deploying repo: <b>$owner | $repo | $type | $typeid</b> to <b>$dest</b></h1><hr><Br/>";


// @todo if downloadzip false and file not exist auto download
if($downloadzip){
    fetchZip($url,$zipfile);
    $fetchstatus = $url."<Br/>";

    if( !verifyZip($zipfile)){
        // Delete the repo zip file
        $fetchstatus .= "Nothing found at url<br/>";
        $fetchstatus .= "Remove $zipfile<br/>";
        trace("Downloading",$fetchstatus,true);
        unlink($zipfile);
        die();
    }  else trace("Downloading",$fetchstatus,true);
    if( !$overwrite && checkExist() ) die('Project is already up to date');
}

if($removefiles){
    $wiperes = rmdirRecursively($dest);
    trace('Wipe deploy', $wiperes);
}

if($extractfiles){
    $unzipres = unZip($zipfile,$dest);

    if($unzipres) {
        $unzipstatus = "Extracted to ".realpath($dest).$zipfile;
    } else {
        $unzipstatus = "Extract to $dest".DIRECTORY_SEPARATOR." FAILED";
    }

    trace("Extracting archive", $unzipstatus, !$unzipres );
}

if(!empty($extractdir)){
    if($copysource){
        $copyres = copy_recursively($extractdir, $dest);
        trace("Copy $dest$extractdir to $dest",$copyres);
    }

    if($removesource){
        $wiperepores = rmdirRecursively($extractdir,true);
        trace("Remove $dest$reponame-$tag contents",$wiperepores);

        rmdir($extractdir);
    }
}

if($removezip){
    // Delete the repo zip file
    echo "Remove $zipfile</br>";
    unlink($zipfile);
}

//////////////////////////////////////////////////////
// Functions
//////////////////////////////////////////////////////


/**
 * output tracing
 */
function trace($title,$contents = '',$open = false){
    echo "<details". ($open ? ' open':'') ."><summary>$title</summary>$contents</details>";
}

function traceSuccess($title,$success){
    echo "$title: <B>" . ($success ? 'SUCCESS' : 'ERROR') . "</b></br>";
}

/**
 * fetchZip
 * Fetches a zip file saves to outfile
 *
 * @param string $url
 * @param string $file
 */
    function fetchZip($url,$file){
        GLOBAL $username,$password;

        // download the repo zip file

        $ch = curl_init($url);
        if(!$ch) die('cURL not found');

        $fp = fopen($file, 'w');
        if(!$fp) die('Cannot open file for writing: ' . $file);

        if(!empty($password)) curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);     // follow redirects
        curl_setopt($ch, CURLOPT_FILE, $fp);                    
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // disable ssl cert verification

        $data = curl_exec($ch);

        if($data === false){ die('Curl error: ' . curl_error($ch)); }

        curl_close($ch);
        fclose($fp);
    }


/**
 * @return false if zip is invalid
 */
function verifyZip($file){
    $tipsize = filesize($file);

    // verify zip saved
    if($tipsize < 60000) // 60 bytes
    {
        return false;
    }

    return true;
}

/**
 * checks that node is not already installed, handle forcing
 * @todo not saving any hashes at the moment simply node name
 */
function checkExist(){
    GLOBAL $force,$tag,$checkfile;

    if(!$force && file_exists($checkfile)){
        $lastcommit = file_get_contents($checkfile);
        if($lastcommit == $tag) return true;
    }
    
    file_put_contents($checkfile, $tag);
}

/**
 * unZip
 * unzip file to dest folder
 *
 * @param string $file filepath to zip
 * @param string $dest dest path
 * @return ziparchive open return
 */
function unZip($file,$dest){
    // unzip
    $zip = new ZipArchive;
    $res = $zip->open($file);
    if ($res !== TRUE) {
        return $res;
    }

    $res = $zip->extractTo("$dest".DIRECTORY_SEPARATOR);
    $zip->close();

    return $res;
}

/**
  * function to delete all files in a directory recursively
  * @param string $dir directory to delete
  * @param bool $noExclude skip exclusions
  */
function rmdirRecursively($dir,$noExclude=false) {
    global $exc;
    $trace = '';

    // $noExclude |= ( preg_match('/\w{0,}-\w{0,}-[0-9|a|b|c|d|e|f]{12}/',$dir) > 0);
    # var_dump($noExclude);

    $trace.= "Erase dir: " . $dir ."<Br/>";

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    // FilesystemIterator::SKIP_DOTS ./ 5.3+

    $excludeDirsNames = isset($exc["folders"]) ? $exc["folders"] : array();
    $excludeFileNames = isset($exc["files"]) ? $exc["files"] : array();

    foreach ($it as $entry) {

        if ($entry->isDir()) {
            
            // php 5.2 support for skipdot            
            if($entry->getFilename() == '.' or $entry->getFilename() == '..'){
                continue;
            }
                
            if ($noExclude || !in_array(getRootName($entry->getPathname()), $excludeDirsNames)) {
                $trace.= rmdirRecursively($entry->getPathname());
                rmdir($entry->getPathname()); // remove dir after its empty
            }
            else{
                $trace.= "Erase dir: " . $entry->getPathname() . " <mark><b>SKIPPED</b></mark><br/>";
            }
        } elseif ( $noExclude || ( !in_array($entry->getFilename(), $excludeFileNames) && !in_array(getRootName($entry->getPathname()), $excludeDirsNames)) ) {
            $trace.=  "--Erase file: " . $entry->getPathname() ."<br/>";
            unlink($entry->getPathname());
        }
        else{
            $trace.=  "--Erase file: " . $entry->getPathname() . " <mark><b>SKIPPED</b></mark><br/>";
        }        
    }

    return $trace;
}

function getRootName($path){
    $path = str_replace('.'.DIRECTORY_SEPARATOR,'',$path);
    $parts = explode(DIRECTORY_SEPARATOR,$path);
    return $parts[0];
}

/**
  * function to copy all files in a directory to another recursively
  * @param string $src path
  * @param string $dest destination path
  */
function copy_recursively($src, $dest) {
    global $exc;

    $trace = '';
    $trace .= "Copy Recursive: $src $dest<br/>";

    $excludeDirsNames = isset($exc["folders"]) ? $exc["folders"] : array();
    $excludeFileNames =isset($exc["files"]) ? $exc["files"] : array();

    if (is_dir(''.$src)){
        @mkdir($dest);
        $files = scandir($src);

        foreach ($files as $file){
            if (!in_array(getRootName($dest), $excludeDirsNames)){
                if ($file != "." && $file != ".."){
                    $trace.= copy_recursively("$src/$file", "$dest/$file");
                }    
            }
        }
    }
    else if (file_exists($src)){
        $filename = pathinfo($src, PATHINFO_FILENAME);
        //$filename = $filename[count( $filename)-2];
        if (!in_array( $filename, $excludeFileNames)){
            copy($src, $dest);
        }
    }

    return $trace;
}

echo 'Done';
?>