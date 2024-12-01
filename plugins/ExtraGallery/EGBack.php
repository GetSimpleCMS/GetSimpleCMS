<?php

class EGBack{

    public $id;
    public $currentFile;
    public $instanceNum; //instance name
    public $pluginTitleFull;
        
    public $settings;
    
    //this is used to control when more then one instance of plugin exists
    private static $_uploadsDeletionControlled = false;

    public function __construct($id) { 
        $this->pluginId = $id;
        
        if (substr( $this->pluginId, 0, strlen(EG_ID) )!= EG_ID){
            die('ExtraGallery plugin instance files must begin with "'.EG_ID.'"!'); //if not will break cleaning unused thumbs
        }
		
		//find instance name
		$this->instanceNum = EGTools::findinstanceNum($this->pluginId);
		$this->pluginTitleFull = 'Extra Gallery' . ($this->instanceNum ? '-'. $this->instanceNum : '');

        $this->currentFile = strtolower(basename($_SERVER['PHP_SELF']));

    } 
    
    public function init(){
        # register plugin
        register_plugin(
            $this->pluginId, 
            $this->pluginTitleFull,
            '1.03', 		
            'Michał Gańko',
            'http://flexphperia.net', 
            'Extra Gallery is a backend plugin for creating galleries, with advanced features like: custom fields, thumbnails cropping, multi language content, easy image browsing. It can be installed in GS more than once (multi instances) to use it with different settings on each copy. Created galleries are available as structured PHP arrays to use in theme.',
            isset($_GET['conf']) ? 'plugins' : $this->pluginTitleFull,
            array($this, 'display') 
        );
    
        global $LANG;
        i18n_merge(EG_ID, substr($LANG,0,2)) || i18n_merge(EG_ID,'en');
        
		$this->settings = EGSettings::load( $this->instanceNum, true );
        
        //currently in plugin configuration or edition of galleries
        if ($this->currentFile == 'load.php' && @$_GET['id'] == $this->pluginId){
            add_action('header', array($this, 'onHeader') ); 
            add_action('footer', array($this, 'onFooter') ); 
        }
        
        if (!self::$_uploadsDeletionControlled){ //attach only once, first instance
            if( $this->currentFile == 'deletefile.php' && isset($_GET['file'])){ //user deleting file from uploads
                $this->_onUploadFileDelete();
            }     
            else if( $this->currentFile == 'deletefile.php' && isset($_GET['folder'])){ //user deleting folder from uploads
                $this->_onUploadFolderDelete();
            }
            self::$_uploadsDeletionControlled = true;
        }
        
        add_action( 'plugins-sidebar', 'createSideMenu', 
			array($this->pluginId, i18n_r(EG_ID.'/CONF_SIDEBAR'). ($this->instanceNum ? (' - '.$this->instanceNum) : '' ) , 'conf' ) 
		); 
		
        add_action( 'nav-tab', 'createNavTab', array( $this->pluginTitleFull, $this->pluginId, $this->settings['tab-label'], 'list' ) );
		
		add_action($this->pluginTitleFull.'-sidebar', 'createSideMenu', array($this->pluginId, i18n_r(EG_ID.'/LIST_SIDEBAR_LIST'), 'list'));
		add_action($this->pluginTitleFull.'-sidebar', 'createSideMenu', array($this->pluginId, i18n_r(EG_ID.'/LIST_SIDEBAR_NEW'), 'add'));
		add_action($this->pluginTitleFull.'-sidebar', 'createSideMenu', array($this->pluginId, i18n_r(EG_ID.'/LIST_SIDEBAR_EDIT'), 'edit', false));
    }

    public function display(){ 
        $settings = $this->settings; //local scope
        
        if (isset($_GET['list'])){
            $galleries = EGStorage::returnGallery(null, $this->instanceNum);
            asort($galleries);
			require_once('views/list.html');
        }      
        else if (isset($_GET['add']) || isset($_GET['edit'])){	
            require_once('views/edit.html');
			require_once('views/extraBrowser.html');
        }   
        else if (isset($_GET['conf'])){
            require_once('views/configuration.html');
        }  
    }   

    public function onHeader(){
	
        if (isset($_GET['add']) || isset($_GET['edit'])){
            ?>
                <script type="text/javascript" src="../plugins/ExtraGallery/js/jquery.scrollTo.min.js"></script>
                <script type="text/javascript" src="../plugins/ExtraGallery/js/jquery.sticky.js"></script>
                <script type="text/javascript" src="../plugins/ExtraGallery/js/jquery.egDialog.js"></script>
                <script type="text/javascript" src="../plugins/ExtraGallery/js/jquery.extraBrowser.js"></script>
                <script type="text/javascript" src="../plugins/ExtraGallery/js/jquery.Jcrop.min.js"></script>

				<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>

				<link rel="stylesheet" href="../plugins/ExtraGallery/css/extraBrowser.css" />
				<link rel="stylesheet" href="../plugins/ExtraGallery/css/jquery.Jcrop.css" />
            <?php     
        }  
        
        ?>
            <link rel="stylesheet" href="../plugins/ExtraGallery/css/styles.css" />
        <?php
		
		$this->_routeAction();
    }  

    public function onFooter(){
		$settings = $this->settings; //local scope

        echo '<script>';
		if (isset($_GET['list'])){
        
            require_once('views/listJS.php');
        }  
        else if (isset($_GET['add']) || isset($_GET['edit'])){
            $message = @$_GET['message'];
            $isErrorMessage = @$_GET['isError'];
            
            if (isset($_GET['edit'])){
                $name = @$_GET['name'];
                $galleryData = EGStorage::returnGallery($name, $this->instanceNum);
            }
            
            $mode = isset($_GET['edit']) && $galleryData ? 'edit' : 'add';
            
            require_once('views/editJS.php');
        }
        else if (isset($_GET['conf'])){
            $message = @$_GET['message'];
            $isErrorMessage = @$_GET['isError'];
            
            require_once('views/configurationJS.php');
        }    
        echo '</script>';
		
    }
    
    //routes action inside plugin
    private function _routeAction (){
         
        if ((isset($_GET['add']) || isset($_GET['edit'])) && isset($_GET['save'])){   
        
            $message = EGGallery::save($this->instanceNum);
            $isError = $message ? 1 : '';
            
            if (!$isError)
                $message = i18n_r(EG_ID.'/EDIT_SAVED');
                
            $delete = @$_POST['delete'];
            
            if (!$isError && $delete)
                EGGallery::delete($this->instanceNum, $delete);
                 
            EGTools::cleanUnusedThumbs();

            redirect('load.php?id='.$this->pluginId.'&edit&name='.$_POST['name'].'&message='.urlencode($message).'&isError='.$isError);
        }
        else if (isset($_GET['conf']) && @$_POST['save']){ //save settings
            if ( EGSettings::save($this->instanceNum) ){
                $message = i18n_r(EG_ID.'/CONF_SAVED');
                $isError = '';
            }
            else{
                $message = i18n_r(EG_ID.'/CONF_SAVE_ERROR');
                $isError = 1;
            }
            
            redirect('load.php?id='.$this->pluginId.'&conf&message='.urlencode($message).'&isError='.$isError);
        }     
        
    }
    
    //used when GS deletes file from uploads, delete cached admin thumb
    private function _onUploadFileDelete(){    
        $path = (isset($_GET['path'])) ? $_GET['path'] : '';
        $id = $_GET['file'];            
        $filepath = GSDATAUPLOADPATH . $path;
        $file =  $filepath . $id;

        //check that path is safe, etc.
        if(path_is_safe($filepath,GSDATAUPLOADPATH) && filepath_is_safe($file,$filepath)){
            //delete from uploads image
            @unlink(EG_ADMINTHUMBS.$path.$id);
        }	
    }
    
    //used when GS deletes empty folder from uploads
    public function _onUploadFolderDelete(){
        $path = (isset($_GET['path'])) ? $_GET['path'] : '';
        $folder = $_GET['folder'];
        $target = GSDATAUPLOADPATH . $path . $folder;
        
        //check that path is safe for uploads
        if (path_is_safe($target,GSDATAUPLOADPATH)) {
            //removes empty directory, so we believe that all thumbs from that dir was deleted when user deleted files by admin panel
            @rmdir(EG_ADMINTHUMBS.$path.$folder);
        }
    }
}
