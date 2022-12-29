<?php

class Setup
{

	public
	$common,
	$backend;

	protected $xml;


	public function __construct()
	{
		if(file_exists(IM_CONFIG_FILE)) $this->init();
		include('inc/config.php');
	}


	public function init()
	{
		$this->xml = simplexml_load_file(IM_CONFIG_FILE);
		if(!$this->xml) return false;

		/* settings */
		$this->common = & $this->xml->common;
		$this->backend = & $this->xml->backend;
	}


	/**
	 * The method checks if the database directory exists
	 *
	 * @return bool
	 */
	private function isInstalled()
	{
		if(file_exists(ITEMDATA) && file_exists(IM_DATABASE_DIR.IM_DATABASE)){return true;}
		else return false;
	}


	/**
	 * Section key value
	 *
	 */
	public function set($sec, $key, $val)
	{
		$this->$sec->$key = $val;
	}


	public function save()
	{
		return $this->xml->asXml(IM_CONFIG_FILE);
	}


	public function setup()
	{
		$err = false;

		if(!$this->createFolderProcedure(ITEMDATA, '.htaccess', 'Deny from all'))
		{
			MsgReporter::setClause(
				'items_path_exists',
				array('itemmanager-title' => IMTITLE,
					'gsdatapath' => ITEMDATA)
			);
			$err = true;
		} else
		{
			MsgReporter::setClause(
				'directory_succesfull_created',
				array('end_path' => ITEMDATA)
			);
		}
		// Kategorien-Verzeichnis erstellen
		if(file_exists(ITEMDATA) && !$this->createFolderProcedure(IM_CATEGORY_DIR))
		{
			MsgReporter::setClause(
				'items_path_exists',
				array('itemmanager-title' => IMTITLE,
					'gsdatapath' => IM_CATEGORY_DIR)
			);
			$err = true;
		} else
		{
			MsgReporter::setClause(
				'directory_succesfull_created',
				array('end_path' => IM_CATEGORY_DIR)
			);
		}
		// Items-Verzeichnis erstellen
		if(file_exists(ITEMDATA) && !$this->createFolderProcedure(IM_ITEM_DIR))
		{
			MsgReporter::setClause(
				'items_path_exists',
				array('itemmanager-title' => IMTITLE,
					'gsdatapath' => IM_ITEM_DIR)
			);
			$err = true;
		} else
		{
			MsgReporter::setClause(
				'directory_succesfull_created',
				array('end_path' => IM_ITEM_DIR)
			);
		}
		// Settings-Verzeichnis erstellen
		if(file_exists(ITEMDATA) && !$this->createFolderProcedure(IM_SETTINGS_DIR))
		{
			MsgReporter::setClause(
				'items_path_exists',
				array('itemmanager-title' => IMTITLE,
					'gsdatapath' => IM_SETTINGS_DIR)
			);
			$err = true;
		} else
		{
			MsgReporter::setClause(
				'directory_succesfull_created',
				array('end_path' => IM_SETTINGS_DIR)
			);
		}
		// Fields-Verzeichnis erstellen
		if(file_exists(ITEMDATA) && !$this->createFolderProcedure(IM_FIELDS_DIR))
		{
			MsgReporter::setClause(
				'items_path_exists',
				array('itemmanager-title' => IMTITLE,
					'gsdatapath' => IM_FIELDS_DIR)
			);
			$err = true;
		} else
		{
			MsgReporter::setClause(
				'directory_succesfull_created',
				array('end_path' => IM_FIELDS_DIR)
			);
		}

		// Create backup directory
		if(!file_exists(IM_BACKUP_DIR) && !$this->createFolderProcedure(IM_BACKUP_DIR))
		{
			MsgReporter::setClause(
				'items_path_exists',
				array('itemmanager-title' => IMTITLE,
					'gsdatapath' => IM_BACKUP_DIR)
			);
			$err = true;
		} else
		{
			MsgReporter::setClause(
				'directory_succesfull_created',
				array('end_path' => IM_BACKUP_DIR)
			);
		}


		// Uploads Verzeichnis erstellen
		if(!file_exists(IM_UPLOAD_DIR))
		{
			if(!$this->createFolderProcedure(IM_UPLOAD_DIR, '.htaccess', 'Allow from all'))
			{
				MsgReporter::setClause('upload_path_exists',
					array('itemmanager-title' => IMTITLE,
						'end_path' => IM_UPLOAD_DIR)
				);
				$err = true;
			} else
			{
				MsgReporter::setClause('directory_succesfull_created',
					array('end_path' => IM_UPLOAD_DIR)
				);
			}
		}

		return ($err == true) ? false : true;
	}


	private function createFolderProcedure($folder, $file='', $file_contents='')
	{
		if(file_exists($folder.$file))
			return true;
		if(!mkdir($folder, 0755))
			return false;
		if(empty($file))
			return true;
		if(!$handle = fopen($folder.$file, 'w'))
			return false;
		fwrite($handle, $file_contents);
		fclose($handle);
		return true;
	}


	/* set program preferences */
	public function setupConfig(array $input=array())
	{
		$delcat = isset($input['deletecategory']) ? $input['deletecategory'] : false;

		// Software title
		$file_title = IMTITLE;

		$max_cat_name = (isset($input['maxcatname']) && intval($input['maxcatname']) > 0)
			? intval($input['maxcatname']) : 30;

		$max_field_name = (isset($input['maxfieldname']) && intval($input['maxfieldname']) > 0)
			? intval($input['maxfieldname']) : 30;

		$max_item_name = (isset($input['maxitemname']) && intval($input['maxitemname']) > 0)
			? intval($input['maxitemname']) : 50;

		$i18nsearch = !isset($input['i18nsearch']) ? 0 : 1;
		$i18nsearch_field = (!empty($input['i18nsearchfield'])) ? $input['i18nsearchfield'] : '';
		$i18nsearch_excludes = (!empty($input['i18nsearchexcludes'])) ? $input['i18nsearchexcludes'] : '';
		$i18nsearch_url = (!empty($input['i18nsearchurl'])) ? $input['i18nsearchurl'] : '';
		$i18nsearch_segment = (!empty($input['i18nsearchsegment'])) ? $input['i18nsearchsegment'] : 'name';
		$i18nsearch_content = (!empty($input['i18nsearchcontent'])) ? $input['i18nsearchcontent'] : '';


		$time_format = (!empty($input['timeformat'])) ? $input['timeformat'] : 'Y-m-d h:m:s';

		$cat_backup = !isset($input['catbackup']) ? 0 : intval($input['catbackup']);

		$field_backup = !isset($input['fieldbackup']) ? 0 : intval($input['fieldbackup']);

		$min_fieldbackup_days = !isset($input['min_fieldbackup_days']) ? 1 : intval($input['min_fieldbackup_days']);

		$cat_backupdir = empty($input['catbackupdir']) ? IM_BACKUP_DIR : $input['catbackupdir'];

		$min_catbackup_days = !isset($input['min_catbackup_days']) ? 1 : intval($input['min_catbackup_days']);

		$field_backupdir = empty($input['fieldbackupdir']) ? IM_BACKUP_DIR : $input['fieldbackupdir'];

		if(substr($cat_backupdir, -1, 1) != '/') $cat_backupdir = $cat_backupdir.'/';
		if(substr($field_backupdir, -1, 1) != '/') $field_backupdir = $field_backupdir.'/';

		$cat_filter = !isset($input['catfilter']) ? 0 : intval($input['catfilter']);
		$max_cat_perpage = empty($input['maxcatperpage']) ? 10 : intval($input['maxcatperpage']);

		$cat_order = (isset($input['catorder']) && strtolower($input['catorder']) == 'asc')
			? strtolower($input['catorder']) : 'desc';

		$cat_order_by = !empty($input['catorderby']) ?
			strtolower(imanager('sanitizer')->text($input['catorderby'])) : 'position';

		$item_order_by = !empty($input['itemorderby']) ?
			strtolower(imanager('sanitizer')->text($input['itemorderby'])) : 'position';

		$item_order = (isset($input['itemorder']) && strtolower($input['itemorder']) == 'asc')
			? strtolower($input['itemorder']) : 'desc';

		$item_filter = !isset($input['itemfilter']) ? 0 : intval($input['itemfilter']);

		$max_item_perpage = empty($input['maxitemperpage']) ? 20 : intval($input['maxitemperpage']);

		// max thumb width
		$thumbwidth = isset($input['thumbwidth']) ? (int) $input['thumbwidth'] : 200;

		$min_tmpimage_days = !isset($input['min_tmpimage_days']) ? 1 : intval($input['min_tmpimage_days']);


		// item Backup beim löschen anlegen Checkbox
		$item_backup = !isset($input['itembackup']) ? 0 : intval($input['itembackup']);
		// item Backup Verzeichnis angeben
		$item_backupdir = empty($input['itembackupdir']) ? IM_BACKUP_DIR : $input['itembackupdir'];
		// Item Backup Aufbewahrungsfrist in Tagen angeben
		$min_itembackup_days = !isset($input['min_itembackup_days']) ? 10 : intval($input['min_itembackup_days']);
		// Item Enabled
		$itemactive = !isset($input['itemactive']) ? 0 : intval($input['itemactive']);
		$unique_itemname = !isset($input['uniqueitemname']) ? 0 : intval($input['uniqueitemname']);

		// store values
		$xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8" ?><settings></settings>');

		$common_xml = $xml->addChild('common');
		$frontend_xml = $xml->addChild('frontend');
		$backend_xml = $xml->addChild('backend');
		// Set title variable
		$common_xml->addChild('title', $file_title);
		// max category name
		$common_xml->addChild('maxcatname', $max_cat_name);
		// max field name
		$common_xml->addChild('maxfieldname', $max_field_name);
		// max item name
		$common_xml->addChild('maxitemname', $max_item_name);

		$common_xml->addChild('i18nsearch', $i18nsearch);
		$common_xml->addChild('i18nsearchfield', $i18nsearch_field);
		$common_xml->addChild('i18nsearchexcludes', $i18nsearch_excludes);

		$common_xml->addChild('i18nsearch_url', $i18nsearch_url);
		$common_xml->addChild('i18nsearch_segment', $i18nsearch_segment);
		$common_xml->addChild('i18nsearch_content', $i18nsearch_content);

		// Backend

		// time format
		$backend_xml->addChild('timeformat', $time_format);
		// display categorie filter
		$backend_xml->addChild('catfilter', $cat_filter);
		// show max categories per page
		$backend_xml->addChild('maxcatperpage', $max_cat_perpage);
		// default category order
		$backend_xml->addChild('catorder', $cat_order);
		// category order by
		$backend_xml->addChild('catorderby', $cat_order_by);
		// category backups
		$backend_xml->addChild('catbackup', $cat_backup);
		// backup directory for caterories
		$backend_xml->addChild('catbackupdir', $cat_backupdir);
		$backend_xml->addChild('min_catbackup_days', $min_catbackup_days);


		// fields backups
		$backend_xml->addChild('fieldbackup', $field_backup);
		// backup directory for fields
		$backend_xml->addChild('fieldbackupdir', $field_backupdir);
		$backend_xml->addChild('min_fieldbackup_days', $min_fieldbackup_days);

		$backend_xml->addChild('itemorderby', $item_order_by);
		$backend_xml->addChild('itemorder', $item_order);
		$item_filter = !isset($this->backend->itemfilter) ? 1 : $item_filter;
		$backend_xml->addChild('itemfilter', $item_filter);
		$backend_xml->addChild('maxitemperpage', $max_item_perpage);

		$item_backup = !isset($this->backend->itembackup) ? 1 : $item_backup;
		$backend_xml->addChild('itembackup', $item_backup);

		$backend_xml->addChild('itembackupdir',$item_backupdir);
		$backend_xml->addChild('min_itembackup_days', $min_itembackup_days);
		$backend_xml->addChild('itemactive', $itemactive);
		// set to 1 on start
		$unique_itemname = !isset($this->backend->unique_itemname) ? 1 : $unique_itemname;
		$backend_xml->addChild('unique_itemname', $unique_itemname);
		$backend_xml->addChild('min_tmpimage_days', $min_tmpimage_days);

		//Set max thumb width prop
		$backend_xml->addChild('thumbwidth', $thumbwidth);

		// Save XML File
		if(XMLsave($xml, IM_CONFIG_FILE))
		{
			// initialize new settings
			$this->init();
			// recreate i18n search index
			//if($i18nsearch > 0) IManager::deleteSearchIndex();

			MsgReporter::setClause('successfull_settings_saved');
		}

		return true;
	}



	public function createBackup($path, $file, $suffix)
	{
		if(!file_exists($path.$file.$suffix)) return false;
		$stamp = time();
		if(!copy($path.$file.$suffix, IM_BACKUP_DIR.'backup_'.$stamp.'_'.$file.$suffix)) return false;
		$this->deleteOutdatedBackups($suffix);
		return true;
	}



	public function deleteOutdatedBackups($suffix)
	{
		switch ($suffix)
		{
			case '.im.fields.xml':
				$token = 'field';
				break;
			case '.im.cat.xml':
				$token = 'cat';
				break;
			case '.im.item.xml':
				$token = 'item';
				break;
			default:
				return false;
		}
		$min_days = (int)$this->backend->{'min_'.$token.'backup_days'};
		foreach(glob(IM_BACKUP_DIR.'backup_*_*'.$suffix) as $file) {
			if($this->isCacheFileExpired($file, $min_days)) { $this->removeFilename($file);}
		}
	}

	/**
	 * Is the given backup filename expired?
	 *
	 * @param string $filename
	 * @return bool
	 *
	 */
	protected function isCacheFileExpired($filename, $min_days)
	{
		if(!$mtime = @filemtime($filename)) return false;
		if(($mtime + (60 * 60 * 24 * $min_days)) < time()) {
			return true;
		}
		return false;
	}


	/**
	 * Removes just the given file
	 */
	protected function removeFilename($filename){@unlink($filename);}

}
?>