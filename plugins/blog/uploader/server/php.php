<?php

if(!function_exists('cookie_check'))
{
    include "../../../../admin/inc/common.php";
}

if(get_cookie('GS_ADMIN_USERNAME'))
{
    /**
     * Handle file uploads via XMLHttpRequest
     */
    class qqUploadedFileXhr {
        /**
         * Save the file to the specified path
         * @return boolean TRUE on success
         */
        function save($path) {    
            $input = fopen("php://input", "r");
            $temp = tmpfile();
            $realSize = stream_copy_to_stream($input, $temp);
            fclose($input);
            
            if ($realSize != $this->getSize()){            
                return false;
            }
            
            $target = fopen($path, "w");        
            fseek($temp, 0, SEEK_SET);
            stream_copy_to_stream($temp, $target);
            fclose($target);
            
            return true;
        }
        function getName() {
            return $_GET['qqfile'];
        }
        function getSize() {
            if (isset($_SERVER["CONTENT_LENGTH"])){
                return (int)$_SERVER["CONTENT_LENGTH"];            
            } else {
                throw new Exception('Getting content length is not supported.');
            }      
        }   
    }
    
    /**
     * Handle file uploads via regular form post (uses the $_FILES array)
     */
    class qqUploadedFileForm {  
        /**
         * Save the file to the specified path
         * @return boolean TRUE on success
         */
        function save($path) {
            if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
                return false;
            }
            return true;
        }
        function getName() {
            return $_FILES['qqfile']['name'];
        }
        function getSize() {
            return $_FILES['qqfile']['size'];
        }
    }
    
    class qqFileUploader {
        private $allowedExtensions = array();
        private $sizeLimit;
        private $file;
    
        function __construct(array $allowedExtensions = array(), $sizeLimit = '10M'){        
            $allowedExtensions = array_map("strtolower", $allowedExtensions);
                
            $this->allowedExtensions = $allowedExtensions;        
            $this->sizeLimit = $this->return_bytes($sizeLimit);  
    
            if (isset($_GET['qqfile'])) {
                $this->file = new qqUploadedFileXhr();
            } elseif (isset($_FILES['qqfile'])) {
                $this->file = new qqUploadedFileForm();
            } else {
                $this->file = false; 
            }
        }
        
        private function toBytes($str){
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
         * Returns array('success'=>true) or array('error'=>'error message')
         */
        function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
            if (!is_writable($uploadDirectory)){
                return array('error' => "Server error. Upload directory isn't writable.");
            }
            
            if (!$this->file){
                return array('error' => 'No files were uploaded.');
            }
            
            $size = $this->file->getSize();
            
            if ($size == 0) {
                return array('error' => 'File is empty');
            }
    
            if ($size > $this->sizeLimit) {
                return array('error' => 'File is too large');
            }
            
            $pathinfo = pathinfo($this->file->getName());
            $filename = $pathinfo['filename'];
            //$filename = md5(uniqid());
            $ext = $pathinfo['extension'];
    
            if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
                $these = implode(', ', $this->allowedExtensions);
                return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
            }
    
            if(!$replaceOldFile){
                /// don't overwrite previous files that were uploaded
                while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                    $filename .= rand(10, 99);
                }
            }
    
            if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
                return array('success'=>true, 'fileName'=>$filename, 'newFilename'=>$filename. '.' . $ext);
            } else {
                return array('error'=> 'Could not save uploaded file.' .
                    'The upload was cancelled, or server error encountered');
            }
    
        }
    
        public function return_bytes($val) {
            $val = trim($val);
            $last = strtolower($val[strlen($val)-1]);
            switch($last) {
                // The 'G' modifier is available since PHP 5.1.0
                case 'g':
                    $val *= 1024;
                case 'm':
                    $val *= 1024;
                case 'k':
                    $val *= 1024;
            }
            return $val;
        }
    }
    
    // list of valid extensions, ex. array("jpeg", "xml", "bmp")
    $allowedExtensions = array();
    // max file size in bytes
    $postSizeLimit = ini_get('post_max_size');
    $uploadSizeLimit = ini_get('upload_max_filesize');
    if($postSizeLimit < $uploadSizeLimit)
    {
        $sizeLimit = $postSizeLimit;
    }
    else
    {
        $sizeLimit = $uploadSizeLimit;
    }
    
    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
    $result = $uploader->handleUpload('../../../../data/uploads/');
    // to pass data through iframe you will need to encode all html tags
    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
}
else
{
    die("You do not have permission to access this script. Your IP has been reported to the site admin.");
}  