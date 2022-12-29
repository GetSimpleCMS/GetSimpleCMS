<?php
class EGStorage {

    /* 
    * This is for internal storage format, used by backend
    */
	//flags that all galleries are loaded, by instance in key
	private static $_internalAllLoadedByInstance = array();
	//storage for all galleries in internal format 
    //$_internalGalleries[instanceNum][galleryname]
	private static $_internalGalleries = array();
    
    
    /* 
    * This is for external storage format, stores galleries in format by langugges, used on frontend
    */
    //storage for galleries converted for frontend,
    //$_externalGalleries[instanceNum][languageindex][galleryname]
	private static $_externalGalleries = array(); 
    //flags that all galleries are loaded, by instance in key
    //_externalAllLoadedByInstance[instanceNum][languageIndex]
	private static $_externalAllLoadedByInstance = array();
	  
	//returns array of galleries when name is null or specified gallery, null when not exists
	public static function returnGallery($name = null, $instanceNum = 0) {
		$iLen = strlen($instanceNum);
		
        if(!isset(self::$_internalGalleries[$instanceNum]))
            self::$_internalGalleries[$instanceNum] = array();
	
		if ($name && self::$_internalGalleries[$instanceNum] && isset(self::$_internalGalleries[$instanceNum][$name])) {
		// nothing to do - _internalGalleries already loaded
		} else if (!@self::$_internalAllLoadedByInstance[$instanceNum]) {
			if ($dh = @opendir(EG_GALLERIESPATH)) {
				while ($filename = readdir($dh)) {
					if (substr($filename,0,strlen(EG_PREFIX) + $iLen + 1) == EG_PREFIX.$instanceNum . '-' && substr($filename,-4) == '.xml') {
						$n = substr($filename,strlen(EG_PREFIX) + $iLen + 1,-4); //find name from file name
						if (!isset(self::$_internalGalleries[$instanceNum][$n]) && (!$name || $n == $name)) { 
							// load _internalGalleries
							self::$_internalGalleries[$instanceNum][$n] = EGGallery::load($n, $instanceNum);
						}
					}
				}
				closedir($dh);
			}
		}
		
		///return all or one
		if (!$name){
			self::$_internalAllLoadedByInstance[$instanceNum] = true;
			return self::$_internalGalleries[$instanceNum];
		} else{
			return isset(self::$_internalGalleries[$instanceNum][$name]) ? self::$_internalGalleries[$instanceNum][$name] : null;
		}
	}
    
    //returning for front
    public static function returnFrontGallery($name, $language = null, $instanceNum = 0){
        $settings = EGSettings::load($instanceNum);
        $langIndex = 0;

        if ($language){
            $langIndex = array_search( $language, $settings['languages']);
            if ($langIndex  === false)
                die('ExtraGallery: Language "'.$language.'" not found');
        }

        $allLoaded = @self::$_externalAllLoadedByInstance[$instanceNum][$langIndex];

        //not loaded everything for current instance and language
        if ( !$allLoaded || ($name && !$allLoaded && !isset(self::$_externalGalleries[$instanceNum][$langIndex][$name]))  ){
            $gals = self::returnGallery($name, $instanceNum);
            
            if ($name){ //only one gallery make array, if all galleris, then already we have array
                $gals = array($gals);
            }
            
            //create key if not exists, needed when no galleries
            if (!isset(self::$_externalGalleries[$instanceNum][$langIndex])){
                self::$_externalGalleries[$instanceNum][$langIndex] = array();
            }

            foreach ($gals as $key => $gal) {  
                if (!$gal) //if exists
                    continue;

                //rewrite values, no matter 
                if (!$settings['title-disabled'])
                    $gal['title'] = $gal['title'][$langIndex];

                for ($i = 0; $i < count($gal['items']); $i++) {
                    $row = $gal['items'][$i];


                    //rewrite all fields from nested array to main array
                    if (isset($row['languages'][$langIndex])) //only if any fields exists
                        $row = array_merge($row, $row['languages'][$langIndex]);


                    $row['filename'] = str_replace(GSDATAPATH, '', GSDATAUPLOADPATH).$row['filename']; //create path from data

                    for ($t = 0; $t < isset($row['thumb-'.$t]); $t++) {
                        if ($row['thumb-'.$t]['filename'])
                            $row['thumb-'.$t]['filename'] = str_replace(GSDATAPATH, '', EG_THUMBS).$row['thumb-'.$t]['filename']; //create path from data
                    }

                    unset($row['languages']);

                    $gal['items'][$i] = $row;
                }
                self::$_externalGalleries[$instanceNum][$langIndex][$name ? $name : $key] = $gal; //store
            }
        }
        // echo $name;
        if (!$name){
			self::$_externalAllLoadedByInstance[$instanceNum][$langIndex] = true;
			return self::$_externalGalleries[$instanceNum][$langIndex];
		} else{
			return isset(self::$_externalGalleries[$instanceNum][$langIndex][$name]) ? self::$_externalGalleries[$instanceNum][$langIndex][$name] : null;
		}
    }
}