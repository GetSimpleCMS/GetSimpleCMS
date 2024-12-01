<?php
class EGTools {


    //removes unused croped thumbnails
    public static function cleanUnusedThumbs(){
        $instances = self::_findAllInstances();

        //find all of instances
        $usedThumbs = array();
        $allThumbs = self::_getFiles(EG_THUMBS);

        for ($i = 0; $i < count($instances); $i++) {
            $instance = $instances[$i];

            $iGalleries = EGStorage::returnGallery(null, $instance);
      
            if (!count($iGalleries))
                continue;
                
               
            foreach ($iGalleries as $name => $gal){
                if (!$gal) //might be null if no settings was created
                    continue;
                    
            
                for ($m = 0; $m < count($gal['items']); $m++) {
                    $item = $gal['items'][$m];
                    
                    //collect real saved thumbs do not loop over settings thumbs
                    //settings may be changed, and gallery might be still not updated so it holds thumbnail
                    for ($t = 0; isset($item['thumb-'.$t]); $t++){
                        if ($item['thumb-'.$t]['filename'])
                            $usedThumbs[$item['thumb-'.$t]['filename']] = true; //may have duplicates, use like hash table to prevent
                    }
                }
            }
        }
        
        $usedThumbs = array_keys($usedThumbs);
        $delThumbs = array_values(array_diff($allThumbs, $usedThumbs));

        for ($d = 0; $d < count($delThumbs); $d++) {
            unlink(EG_THUMBS.$delThumbs[$d]);
            
            //remove empty dir
            $directoryContent = scandir( dirname(EG_THUMBS.$delThumbs[$d]) );
            if (count($directoryContent) <= 2)  // checkig if there is moire than . and ..
                rmdir(dirname(EG_THUMBS.$delThumbs[$d]));
        }        
    }
    
    /* 
     * Finds instance name from provided file name without extension
    */
    public static function findInstanceNum($id){
		$pos = strrpos($id, '-');
		return $pos !== false ? (int)substr($id, $pos + 1) : 0;
    }
    
    /* 
     * Finds all instance names of ExtraGallery plugin, doesent matter that theyre enabled or disabled on plugins tab.
    */
    private static function _findAllInstances(){
        global $live_plugins;
        
        $a = array();
        
        $plugins = array_keys($live_plugins);
        
        for ($i = 0; $i < count($plugins); $i++) {
            $pluginId = substr($plugins[$i], 0, strrpos($plugins[$i], '.'));
            
            if(substr( $pluginId, 0, strlen(EG_ID) ) == EG_ID)
                $a[] = self::findInstanceNum($pluginId);
        }
        return $a;
    }

    private static function _getFiles($directory) {
        $folderContents = array(); 
        
        if (!realpath($directory)) //not exists 
            return $folderContents; 
        
        $directory = str_replace('\\', '/', realpath($directory).'/');  //replace backslashes on widows

        foreach (scandir($directory) as $folderItem) 
        { 
            if ($folderItem != "." && $folderItem != ".." && $folderItem != ".htaccess") 
            { 
                if (is_dir($directory.$folderItem.'/')) 
                { 
                    $folderContents = array_merge($folderContents, self::_getFiles( $directory.$folderItem.'/')); 
                } 
                else 
                { 
                    $folderContents[] = substr($directory.$folderItem, strlen(EG_THUMBS));
                } 
            } 
        } 
        return $folderContents; 
    }
	
}