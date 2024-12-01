<?php

class EGGallery
{    
	/* 
	 * Validates gallery name
	*/
    public static function validateGalleryName($instanceNum, $name){
        return !file_exists(EG_GALLERIESPATH . EG_PREFIX. $instanceNum .'-'.$name .'.xml');
    }  

	/* 
	 *  Deletes gallery
	*/
    public static function delete($instanceNum, $name){
        return @unlink(realpath(EG_GALLERIESPATH . EG_PREFIX . $instanceNum . '-' . $name . '.xml'));
    }

        
    public static function load($instanceNum = 0, $name){
        $settings = EGSettings::load($instanceNum);
    
        //realpath protectes
        $xml = getXML(realpath(EG_GALLERIESPATH . EG_PREFIX . $instanceNum . '-' . $name . '.xml'));
        
        if (!$xml || !$settings)
            return null;
            
        $langCount = max(1, count($settings['languages'])); //if there are no languages, one exists
            
        $data = array();
        
        $data['name'] = (string)$xml->name;
        
        
        if (!$settings['title-disabled']){
            $data['title'] = array();
            for ($l = 0; $l < $langCount; $l++) { 
                $data['title'][$l] = (string)$xml->{'title'.'_'.$l};
            }
        }
        
        $data['items'] = array();
        if (count($xml->items->item)){
            foreach ($xml->items->item as $itemXml){
                $item = array();
                
                $item['filename'] = (string)$itemXml->filename;
                $item['width'] = (int)$itemXml->width;
                $item['height'] = (int)$itemXml->height;
                
                for ($t = 0; $t < count($settings['thumbnails']); $t++) {

                    if ( $settings['thumbnails'][$t]['enabled'] ){
                        $thumb = array();
                        $thumb['filename'] = (string)$itemXml->{'thumb-'.$t}->filename ? (string)$itemXml->{'thumb-'.$t}->filename : null;
                        $thumb['width'] = (int)$itemXml->{'thumb-'.$t}->width ? (int)$itemXml->{'thumb-'.$t}->width : null;
                        $thumb['height'] = (int)$itemXml->{'thumb-'.$t}->height ? (int)$itemXml->{'thumb-'.$t}->height : null;
                        $item['thumb-'.$t] = $thumb;
                    }
                }   
                
                $item['languages'] = array();
                
                for ($f = 0; $f < count($settings['fields']); $f++) {
                    for ($l = 0; $l < $langCount; $l++) {  //create language versions
                        $item['languages'][$l]['field-'.$f] = (string)$itemXml->{'field-'.$f.'_'.$l};
											
						if ($settings['fields'][$f]['type'] == 'checkbox')
							$item['languages'][$l]['field-'.$f] = $item['languages'][$l]['field-'.$f] ? true : false; 
                    }
                }
                
                $data['items'][] = $item;
            }
        }
        
        return $data;
    }      
    
    
    public static function save($instanceNum){
        $settings = EGSettings::load($instanceNum);
        
        $data = self::_parse($settings);
        
		$valRes = self::_validate($settings, $data);
		
        if ($valRes)
            return $valRes; //retursn validation message
            
        $langCount = max(1, count($settings['languages'])); //if there are no languages, one exists
        
        $xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><gallery></gallery>');
        
        $xml->addChild('name', $data['name']);
        
        if (!$settings['title-disabled']){
            for ($l = 0; $l < $langCount; $l++) { 
                $xml->addChild('title_'.$l)->addCData($data['title'][$l]);
            }
        }
        
        $items = $xml->addChild('items');
            
        for ($i = 0; $i < count($data['items']); $i++) {
            $rowData = $data['items'][$i];
            $item = $items->addChild('item');
            
            $item->addChild('filename', $rowData['filename']);
            $item->addChild('width', $rowData['width']);
            $item->addChild('height', $rowData['height']);
            
            for ($t = 0; $t < count($settings['thumbnails']); $t++) {
                $thumb = $item->addChild('thumb-'.$t);
            
                if ( $settings['thumbnails'][$t]['enabled'] ){
                    $thumb->addChild('filename', $rowData['thumb-'.$t]['filename']);
                    $thumb->addChild('width', $rowData['thumb-'.$t]['width']);
                    $thumb->addChild('height', $rowData['thumb-'.$t]['height']);
                }
            }   
            
            for ($f = 0; $f < count($settings['fields']); $f++) {
                for ($l = 0; $l < $langCount; $l++) {  //create language versions
                    $item->addChild('field-'.$f.'_'.$l)->addCData($rowData['languages'][$l]['field-'.$f]);

                }
            }
        }
        
        if ( !file_exists(EG_GALLERIESPATH) ){ //directory not exists, prepare one, requsivly
            mkdir(EG_GALLERIESPATH, 0755, true);
        }
               
        if (!XMLsave($xml, EG_GALLERIESPATH . EG_PREFIX . $instanceNum . '-' . $data['name'] . '.xml'))
            return 'Cannot save file';
        
        return '';
    }  

	/* 
	 *  Parses gallery data from request (POST)
	*/
    private static function _parse($settings){
    
        if (!$settings){
            die('ExtraGallery: Cannot save without settings!');
        }
        
        $langCount = max(1, count($settings['languages'])); //if there are no languages, one exists
        
        $data = array(
            'name' => $_POST['name']
        );  
        
        if (!$settings['title-disabled']){
            $data['title'] = array();
            for ($l = 0; $l < $langCount; $l++) { 
                $data['title'][$l] = $_POST['title-'.$l];
            }
        }

        $data['items'] = array();
        
        for ($i = 0; isset($_POST[$i.'-filename']); $i++) {
            $row = array();
            $row['languages'] = array();
            
            $row['filename'] = $_POST[$i.'-filename'];
            
            for ($t = 0; $t < count($settings['thumbnails']); $t++) {
                if ( $settings['thumbnails'][$t]['enabled'] )
                    $row['thumb-' . $t] = array('filename' => $_POST[$i.'-thumb-'. $t], 'width' => null, 'height' => null);
            }   
            
            for ($f = 0; $f < count($settings['fields']); $f++) {
                for ($l = 0; $l < $langCount; $l++) {  //create language versions
                    $row['languages'][$l]['field-' . $f] =  @$_POST[$i.'-'.$f.'-'.$l]; //silent error, checkbox will be not sended
					
					if ($settings['fields'][$f]['type'] == 'checkbox' && isset($_POST[$i.'-'.$f.'-'.$l]))
						$row['languages'][$l]['field-' . $f] = 1; 
                }
            }
            
            $data['items'][] = $row;
        }

        return $data;
    }
    
    //validates data, pass by referrence
    private static function _validate($settings, &$data){
	        
        require_once('EGImage.php');

        $langCount = max(1, count($settings['languages'])); //if there are no languages, one exists
        
        if (self::_empty( $data['name'] ) || !preg_match ('/[0-9a-z-]+/i', $data['name']))
            return 'Not valid gallery name!';
                
        if (!$settings['title-disabled']){
            for ($l = 0; $l < $langCount; $l++) { 
                if (self::_empty( $data['title'][$l]  ))
                    return 'Title empty!';
            }
        }
                        
        for ($i = 0; $i < count($data['items']); $i++) {
            $row = $data['items'][$i];
            
            if (!filepath_is_safe(GSDATAUPLOADPATH . $row['filename'], GSDATAUPLOADPATH))
                return 'Filename path not safe.';
                
            //fill real sizes of image
            $img = new EGImage(GSDATAUPLOADPATH . $row['filename']);
            $row['width'] =  $img->getWidth();
            $row['height'] =  $img->getHeight();
            $img->destroy();
			
			
			//check width validation
			if($settings['required-width-comparator'] == 'range' && count($settings['required-width-ranges'])){
				if (!self::_validateSize('range', $settings['required-width-ranges'], $row['width']))
					return 'Required width range not passed.';
			}
			else if ($settings['required-width'] !== ''){
				if (!self::_validateSize($settings['required-width-comparator'], $settings['required-width'], $row['width']))
					return 'Required width not passed.';
			}	

			//check height validation
			if($settings['required-height-comparator'] == 'range' && count($settings['required-height-ranges'])){
				if (!self::_validateSize('range', $settings['required-height-ranges'], $row['height']))
					return 'Required height range not passed.';
			}
			else if ($settings['required-height'] !== ''){
				if (!self::_validateSize($settings['required-height-comparator'], $settings['required-height'], $row['height']))
					return 'Required height not passed.';
			}	
			
            
            for ($t = 0; $t < count($settings['thumbnails']); $t++) {
                if ( $settings['thumbnails'][$t]['enabled'] ){
                
                    if ($settings['thumbnails'][$t]['required'] && self::_empty($row['thumb-' . $t]['filename']))
                        return 'Missing thumbnail that is required';
                        
                    if ($row['thumb-' . $t]['filename']){
                        if (!filepath_is_safe(EG_THUMBS . $row['thumb-' . $t]['filename'], EG_THUMBS))
                            return 'Thumbnail path is not safe';
                    
                        $sizes = self::_sizeFromFilename($row['thumb-' . $t]['filename']);
                        
                        if ($settings['thumbnails'][$t]['width'] && $sizes[0] != $settings['thumbnails'][$t]['width'])
                            return 'Thumbnail width not match set width.';       

                        if ($settings['thumbnails'][$t]['height'] && $sizes[1] != $settings['thumbnails'][$t]['height'])
                            return 'Thumbnail height not match set width.';  
                            
                        $row['thumb-' . $t]['width'] = $sizes[0];
                        $row['thumb-' . $t]['height'] = $sizes[1];
                    }
                }
            }   

            for ($f = 0; $f < count($settings['fields']); $f++) {
                $func = '_empty';
                
				//not required or checkbox
                if (!$settings['fields'][$f]['required'] || $settings['fields'][$f]['type'] == 'checkbox')
                    continue;
                
                if ($settings['fields'][$f]['type'] == 'wysiwyg'){
                    $func = '_wysiwygEmpty';
                }
                
                for ($l = 0; $l < $langCount; $l++) { 
                    if ( call_user_func( __CLASS__.'::'.$func ,$row['languages'][$l]['field-' . $f]) )
                        return 'Requried field is empty.';
                }

            }
            
            $data['items'][$i] = $row; //reasign after changes
        }
        
        return '';
    }

    private static function _empty($value){
        return trim($value) == '';
    }   

    private static function _wysiwygEmpty($value){
        return !preg_match ('/\s|<br\s*?\/>/im', $value);
    }
    
    //finds thumb size from its image name
    private static function _sizeFromFilename($filename){
        $slashPos = strrpos($filename, '/') + 1;
        $filename = substr($filename, $slashPos, strrpos($filename, '.'));
        $a = explode('-', $filename);
        $res = array();
			
		if (count($a) < 2)
			die ('Extra Gallery: Cannot find image size in file name.');

		$res[0] = (int)($a[count($a) - 2]); //width
		$res[1] = (int)($a[count($a) - 1]); //height
		
		return $res;
    }
	
	private static function _validateSize($comparator, $targetValue, $value){
        switch($comparator){
            case 'lte':{
                if ($value > $targetValue)
                    return false;
                break;
            }
            case 'gte':{
                if ($value < $targetValue) 
                    return false;
                break;
            }
			case 'range':{
				$result = false;
				
				//iterate over all
				foreach ($targetValue as $range) {
					$from = $range[0];
					$to = $range[1];
					
					if ($value >= $from && $value <= $to)
						$result = true;
				}
	
				if (!$result)
					return false;
                break;
            }
            default:{ //=
                if ($value != $targetValue)
                   return false;
                break;
            }
        }
        
        return true;
    }
    
}
