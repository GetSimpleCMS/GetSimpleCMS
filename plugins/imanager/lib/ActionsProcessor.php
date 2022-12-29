<?php
namespace Imanager;

/**
 * ItemManager's ActionsProcessor
 */
class ActionsProcessor {

	public function saveSettings()
	{
		$sanitizer = imanager('sanitizer');
		$config = imanager('config');
		// Empty cache
		imanager()->getSectionCache(IM_SECTIONS_CACHE_DIR.'admin/')->expire();

		$config->admin->timeformat = isset($_POST['timeformat']) ?
			$sanitizer->text($_POST['timeformat'], array('maxLength' => 20)) : 'Y-m-d h:m:s';
		$config->admin->hide_items_menu = isset($_POST['hide_items_menu']) ?
			(int)$_POST['hide_items_menu'] : 0;
		$config->admin->hide_categories_menu = isset($_POST['hide_categories_menu']) ?
			(int)$_POST['hide_categories_menu'] : 0;
		$config->admin->hide_fields_menu = isset($_POST['hide_fields_menu']) ?
			(int)$_POST['hide_fields_menu'] : 0;
		$config->admin->hide_settings_menu = isset($_POST['hide_settings_menu']) ?
			(int)$_POST['hide_settings_menu'] : 0;
		$config->admin->catsorderby = isset($_POST['catsorderby']) ?
			$sanitizer->text($_POST['catsorderby'], array('maxLength' => 30)) : 'position';
		$config->admin->catsorder = isset($_POST['catsorder']) ?
			$sanitizer->text($_POST['catsorder'], array('maxLength' => 5)) : 'desc';
		$config->admin->catsfilter = isset($_POST['catsfilter']) ?
			(int)$_POST['catsfilter'] : 0;
		$config->admin->catsperpage = isset($_POST['catsperpage']) ?
			(int)$_POST['catsperpage'] : 20;
		$config->admin->itemsorderby = isset($_POST['itemsorderby']) ?
			$sanitizer->text($_POST['itemsorderby'], array('maxLength' => 30)) : 'position';
		$config->admin->itemsorder = isset($_POST['itemsorder']) ?
			$sanitizer->text($_POST['itemsorder'], array('maxLength' => 5)) : 'desc';
		$config->admin->itemsfilter = isset($_POST['itemsfilter']) ?
			(int)$_POST['itemsfilter'] : 1;
		$config->admin->itemsperpage = isset($_POST['itemsperpage']) ?
			(int)$_POST['itemsperpage'] : 20;
		$config->admin->itemsperpage = isset($_POST['itemsperpage']) ?
			(int)$_POST['itemsperpage'] : 20;
		$config->admin->activate_item = isset($_POST['activate_item']) ?
			(int)$_POST['activate_item'] : 0;
		$config->admin->unique_item_name = isset($_POST['unique_item_name']) ?
			(int)$_POST['unique_item_name'] : 0;
		$config->admin->unique_item_name = isset($_POST['unique_item_name']) ?
			(int)$_POST['unique_item_name'] : 0;
		$config->admin->image_expiry_date = isset($_POST['image_expiry_date']) ?
			(int)$_POST['image_expiry_date'] : 1;

		$config->save();
		MsgReporter::setClause('settings_successful_saved');
	}

}