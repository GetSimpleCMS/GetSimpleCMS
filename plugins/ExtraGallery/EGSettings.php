<?php
class EGSettings {

	//stores settings, key as instance name in array
    private static $_settingsStorage = array();

    private static $_allowedTypes = array('text', 'textarea', 'wysiwyg', 'checkbox', 'select');


	/* 
	 * This function will load only once settings if it was loaded before
	*/
    public static function load($instanceNum, $createDefaults = false){

		//if in storage
		if ( isset(self::$_settingsStorage[$instanceNum]) ){
			return self::$_settingsStorage[$instanceNum];
		}

		//no settings create default one
		if (!file_exists(EG_SETTINGSPATH . EG_PREFIX . $instanceNum . '-settings.xml')){

			if (!$createDefaults){
				self::$_settingsStorage[$instanceNum] = null;
				return self::$_settingsStorage[$instanceNum];
			}

			//if folder doesnt exists, create one
			if (!file_exists(EG_SETTINGSPATH)){
				if (!@mkdir(EG_SETTINGSPATH, 0755, true))
					die('ExtraGallery: error during creating settings folder, check folder permissions!');
			}

			if (!@copy(GSPLUGINPATH . 'ExtraGallery/default-settings.xml', EG_SETTINGSPATH . EG_PREFIX . $instanceNum . '-settings.xml'))
				die('ExtraGallery: error during creating default settings, check folder permissions!');
		}

		//load settings
        $xml = getXML(EG_SETTINGSPATH . EG_PREFIX . $instanceNum . '-settings.xml');  

        $data = array();
        $data['tab-label'] = (string)$xml->{'tab-label'};
        $data['title-disabled'] = (string)$xml->{'title-disabled'};

        $data['languages'] = array();
        for ($i=0; $i < count($xml->languages->language); $i++) {
            $data['languages'][] = (string)$xml->languages->language[$i];
        } 

        $data['info'] = (string)$xml->{'info'};

        $data['required-width-comparator'] = (string)$xml->{'required-width-comparator'};
        $data['required-width'] = (string)$xml->{'required-width'} === '' ? '' : (int)$xml->{'required-width'};

        $data['required-width-ranges'] = array();
        for ($i=0; $i < count($xml->{'required-width-ranges'}->range); $i++) {
            $data['required-width-ranges'][] = array(0 => (int)$xml->{'required-width-ranges'}->range[$i]->from, 1 => (int)$xml->{'required-width-ranges'}->range[$i]->to);
        } 

		$data['required-height-comparator'] = (string)$xml->{'required-height-comparator'};
        $data['required-height'] = (string)$xml->{'required-height'} === '' ? '' : (int)$xml->{'required-height'}; //if empty leave

		$data['required-height-ranges'] = array();
        for ($i=0; $i < count($xml->{'required-height-ranges'}->range); $i++) {
            $data['required-height-ranges'][] = array(0 => (int)$xml->{'required-height-ranges'}->range[$i]->from, 1 => (int)$xml->{'required-height-ranges'}->range[$i]->to);
        } 


        $data['fields'] = array();
        for ($i=0; $i < count($xml->fields->field); $i++) {
			$data['fields'][$i] = array();
			$f = $xml->fields->field[$i];

			$data['fields'][$i]['label'] = (string)$f->label;
			$data['fields'][$i]['type'] = (string)$f->type;
			$data['fields'][$i]['required'] = (string) $f->required;

            if ($data['fields'][$i]['type'] == 'select')
                $data['fields'][$i]['options'] = (string)$f->options ? explode(',',(string)$f->options) : array(); //empty after explode will give one value, prevent it

        }    

        $data['thumbnails'] = array();
        for ($i=0; $i < count($xml->thumbnails->thumbnail); $i++) {
			//CAST to proper types
			$data['thumbnails'][$i] = array();
			$t = $xml->thumbnails->thumbnail[$i];

			$data['thumbnails'][$i]['enabled'] = (string)$t->enabled;
			$data['thumbnails'][$i]['required'] = (string)$t->required;
			$data['thumbnails'][$i]['label'] = (string) $t->label;
			$data['thumbnails'][$i]['width'] = (string)$t->width === '' ? '' : (int)$t->width;
			$data['thumbnails'][$i]['height'] = (string) $t->height === '' ? '' : (int) $t->height;
			$data['thumbnails'][$i]['auto-crop'] = (string) $t->{'auto-crop'};
        }  

		self::$_settingsStorage[$instanceNum] = $data;

        return self::$_settingsStorage[$instanceNum];
    }  

    public static function save($instanceNum){
        $data = self::_parse();

        if (!self::_validate($data))
            return false;

        $xml = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><extraGallery></extraGallery>');

        $xml->addChild('tab-label')->addCData($data['tab-label']);
        $xml->addChild('title-disabled', $data['title-disabled']);

        $langs = $xml->addChild('languages');
        for ($i = 0; $i < count($data['languages']); $i++) {
            $langs->addChild('language')->addCData($data['languages'][$i]);
        }

        $xml->addChild('info')->addCData($data['info']);

        $xml->addChild('required-width-comparator')->addCData($data['required-width-comparator']);
        $reqWidth = $xml->addChild('required-width'); 
		$widthRanges = $xml->addChild('required-width-ranges');

		if ($data['required-width-comparator'] == 'range'){
			for ($i = 0; $i < count($data['required-width-ranges']); $i++) {
				$rangeArray = $data['required-width-ranges'][$i];

				$range = $widthRanges->addChild('range');
				$range->addChild('from', $rangeArray[0]);
				$range->addChild('to', $rangeArray[1]);
			}		
		}
		else{
			$reqWidth->addCData($data['required-width']);
		}

		$xml->addChild('required-height-comparator')->addCData($data['required-height-comparator']);
        $reqHeight = $xml->addChild('required-height'); 
		$heightRanges = $xml->addChild('required-height-ranges');

		if ($data['required-height-comparator'] == 'range'){
			for ($i = 0; $i < count($data['required-height-ranges']); $i++) {
				$rangeArray = $data['required-height-ranges'][$i];

				$range = $heightRanges->addChild('range');
				$range->addChild('from', $rangeArray[0]);
				$range->addChild('to', $rangeArray[1]);
			}		
		}
		else{
			$reqHeight->addCData($data['required-height']);
		}



        //iterate over fields
        $fieldsNode = $xml->addChild('fields');
        for ($i = 0; $i < count($data['fields']); $i++) {
            $node = $fieldsNode->addChild('field');
            foreach ($data['fields'][$i] as $key => $value){
                $node->addChild($key)->addCData($value);
            }
        }     

        //iterate over thumbnails
        $thumbsNode = $xml->addChild('thumbnails');
        for ($i = 0; $i < count($data['thumbnails']); $i++) {
            $node = $thumbsNode->addChild('thumbnail');
            foreach ($data['thumbnails'][$i] as $key => $value){
                $node->addChild($key)->addCData($value);
            }
        }   

        $res = XMLsave($xml, EG_SETTINGSPATH . EG_PREFIX . $instanceNum . '-settings.xml');

		unset(self::$_settingsStorage[$instanceNum]); //clear old data from storage

        return $res;
    }


    //get data from post
    private static function _parse()
    {
        $data = array();
        $data['tab-label'] = $_POST['tab-label'];
        $data['title-disabled'] = (bool) @$_POST['title-disabled'];
        $data['languages'] = $_POST['languages'];

        $data['languages'] = $_POST['languages'] ? explode(',',$_POST['languages']) : array(); 
        $data['info'] = trim($_POST['info']); 


        $data['required-width-comparator'] = $_POST['required-width-comparator']; 
        $data['required-width'] = $_POST['required-width']; //do not cast to int
		$data['required-width-ranges'] = $_POST['required-width-ranges'] ?  array_map('self::_explodeRangeRow', explode(';', $_POST['required-width-ranges'])) : array(); //twice explode


		$data['required-height-comparator'] = $_POST['required-height-comparator']; 
        $data['required-height'] = $_POST['required-height'];   //do not cast to int
		$data['required-height-ranges'] = $_POST['required-height-ranges'] ? array_map('self::_explodeRangeRow', explode(';', $_POST['required-height-ranges'])) : array(); //twice explode


        $data['fields'] = array();

        for ($i=0; isset($_POST['field-'.$i.'-type']); $i++) {
            $field = array();

            $field['label'] = $_POST['field-'.$i.'-label'];
            $field['type'] = $_POST['field-'.$i.'-type'];
            $field['required'] = (bool)@$_POST['field-'.$i.'-required'];

            if ($field['type'] == 'select')
                $field['options'] = preg_replace('/\s\s+/', ',', $_POST['field-'.$i.'-options']);

            $data['fields'][] = $field;
        }  

        $data['thumbnails'] = array();

        //thumbs
        for ($i=0; $i <= 1; $i++) {
            $thumb = array();

            $thumb['enabled'] = (bool) @$_POST['thumb-'.$i.'-enabled'];
            $thumb['required'] = (bool) @$_POST['thumb-'.$i.'-required'];
            $thumb['label'] = @$_POST['thumb-'.$i.'-label'];
            $thumb['width'] = @$_POST['thumb-'.$i.'-width'];
            $thumb['height'] = @$_POST['thumb-'.$i.'-height'];
            $thumb['auto-crop'] = @$_POST['thumb-'.$i.'-auto-crop'];

            $data['thumbnails'][] = $thumb;
        }

        return $data;
    }    

    private static function _validate($data){
        if (trim($data['tab-label']) == '')
            return false;

         //validate langugges
        for ($i = 0; $i < count($data['languages']); $i++) {
            if (!preg_match('/^[a-z]+$/i',$data['languages'][$i])){
                return false;
            }
        } 
			// die();
		if ($data['required-width-comparator'] == 'range'){
			foreach((array)$data['required-width-ranges'] as $row) {
				$row = (array)$row; //cast to array if its not

				// var_dump(count($row));
				if (count($row) != 2){
					return false;
				}
				else{
					foreach($row as $value) {
						if ((int)$value < 0)
							return false;
					}
				}
			}
		}
		else{
			if ($data['required-width'] !== '' && (!ctype_digit((string)$data['required-width']) || (int)$data['required-width'] < 0))
				return false;  	
		}


		if ($data['required-height-comparator'] == 'range'){
			foreach((array)$data['required-height-ranges'] as $row) {
				// $row = (array)$row; //cast to array if its not

				// var_dump(count($row));
				if (count($row) != 2){
					return false;
				}
				else{
					foreach($row as $value) {
						if ((int)$value < 0)
							return false;
					}
				}
			}
		}
		else{
			if ($data['required-height'] !== '' && (!ctype_digit((string)$data['required-height']) || (int)$data['required-height'] < 0))
				return false;  	
		}


        //validate fields settings
        for ($i = 0; $i < count($data['fields']); $i++) {
            if (trim($data['fields'][$i]['label']) == '')
                return false; 

            if (!in_array($data['fields'][$i]['type'], self::$_allowedTypes))
                return false;
        }   

        //validate thumbnail settings
        for ($i = 0; $i < count($data['thumbnails']); $i++) {
            $thumb = $data['thumbnails'][$i];

            if (!$thumb['enabled'])
                continue;

            if (!trim($thumb['label']))
                return false;     

            //only check when thumb enabled
            if ($thumb['enabled']){
                if ($thumb['height'] !== '' && (int)$thumb['height'] <= 0)
                    return false;      

                if ($thumb['width'] !== '' && (int)$thumb['width'] <= 0)
                    return false; 

				//check for valid values
				if (!in_array($thumb['auto-crop'], array('', 'fill')))
					return false;
            }
        }
        return true;
    }


	private static function _explodeRangeRow($v){ 
		$array = explode(',', $v);
		$new = array();

		foreach ((array)$array as $item){
			array_push($new, (int)$item);
		}
		return $new;
	}


	// private static function _implodeRangeRow($v){ 
		// return implode(',', $v);
	// }
    

}