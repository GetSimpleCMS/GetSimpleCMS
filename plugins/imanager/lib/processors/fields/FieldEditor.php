<?php
class FieldEditor implements FieldInterface
{
	public $properties;
	protected $tpl;

	public function __construct(TemplateEngine $tpl)
	{
		$this->tpl = $tpl;
		$this->name = null;
		$this->class = null;
		$this->id = null;
		$this->value = null;
		$this->configs = new stdClass();
	}


	public function render($sanitize=false)
	{
		if(is_null($this->name))
			return false;

		$itemeditor = $this->tpl->getTemplates('field');
		$field = $this->tpl->getTemplate('editor', $itemeditor);

		$edprop = array();
		$edprop = $this->editorproperties();
		//echo json_encode($this->id); exit();
		// $this->customize_ckeditor($this->name)
		$output = $this->tpl->render($field, array(
				'name' => $this->name,
				'class' => $this->class,
				'id' => $this->id,
				'value' => $this->value,
				'edlanguage' => $edprop['edlang'],
				'content-css' => $edprop['csspath'],
				'edheight' => $edprop['edheight'],
				'siteurl' => IM_SITE_URL,
				'toolbar' => $edprop['toolbar'],
				'edoptions' => $edprop['edoptions'],
				'setup-editor' => ''
			), true, array()
		);
		return $output;
	}


	function customize_ckeditor($editorvar)
	{
		return "
		// modify existing Link dialog
		CKEDITOR.on( 'dialogDefinition', function( ev ) {
			if ((ev.editor != " . $editorvar . ") || (ev.data.name != 'link')) return;

			// Overrides definition.
			var definition = ev.data.definition;
			definition.onFocus = CKEDITOR.tools.override(definition.onFocus, function(original) {
				return function() {
					original.call(this);
						if (this.getValueOf('info', 'linkType') == 'localPage') {
							this.getContentElement('info', 'localPage_path').select();
						}
				};
			});

			// Overrides linkType definition.
			var infoTab = definition.getContents('info');
			var content = getById(infoTab.elements, 'linkType');
			// items: " . $this->list_pages_json() . ",
			content.items.unshift(['Link to local page', 'localPage']);
			content['default'] = 'localPage';
			infoTab.elements.push({
				type: 'vbox',
				id: 'localPageOptions',
				children: [{
					type: 'select',
					id: 'localPage_path',
					label: 'Select page:',
					required: true,
					setup: function(data) {
						if ( data.localPage )
							this.setValue( data.localPage );
					}
				}]
			});
			content.onChange = CKEDITOR.tools.override(content.onChange, function(original) {
				return function() {
					original.call(this);
					var dialog = this.getDialog();
					var element = dialog.getContentElement('info', 'localPageOptions').getElement().getParent().getParent();
					if (this.getValue() == 'localPage') {
						element.show();
						if (" . $editorvar . ".config.linkShowTargetTab) {
							dialog.showPage('target');
						}
						var uploadTab = dialog.definition.getContents('upload');
						if (uploadTab && !uploadTab.hidden) {
							dialog.hidePage('upload');
						}
					}
					else {
						element.hide();
					}
				};
			});
			content.setup = function(data) {
				if (!data.type || (data.type == 'url') && !data.url) {
					data.type = 'localPage';
				}
				else if (data.url && !data.url.protocol && data.url.url) {
					if (path) {
						data.type = 'localPage';
						data.localPage_path = path;
						delete data.url;
					}
				}
				this.setValue(data.type);
			};
			content.commit = function(data) {
				data.type = this.getValue();
				if (data.type == 'localPage') {
					data.type = 'url';
					var dialog = this.getDialog();
					dialog.setValueOf('info', 'protocol', '');
					dialog.setValueOf('info', 'url', dialog.getValueOf('info', 'localPage_path'));
				}
			};
	  });</script>";
	}


	private function list_pages_json()
	{
		if(function_exists('find_i18n_url') && class_exists('I18nNavigationFrontend')) {
			$slug = isset($_GET['id']) ? $_GET['id'] : (isset($_GET['newid']) ? $_GET['newid'] : '');
			$pos = strpos($slug, '_');
			$lang = $pos !== false ? substr($slug, $pos+1) : null;
			$structure = I18nNavigationFrontend::getPageStructure(null, false, null, $lang);
			$pages = array();
			$nbsp = html_entity_decode('&nbsp;', ENT_QUOTES, 'UTF-8');
			$lfloor = html_entity_decode('&lfloor;', ENT_QUOTES, 'UTF-8');
			foreach ($structure as $page) {
				$text = ($page['level'] > 0 ? str_repeat($nbsp,5*$page['level']-2).$lfloor.$nbsp : '').cl($page['title']);
				$link = find_i18n_url($page['url'], $page['parent'], $lang ? $lang : return_i18n_default_language());
				$pages[] = array($text, $link);
			}
			return json_encode($pages);
		} else {
			return list_pages_json();
		}
	}


	private function editorproperties()
	{
		$edheight = '200px';
		if (defined('GSEDITORHEIGHT'))
			$edheight = GSEDITORHEIGHT .'px';
		$edlang = i18n_r('CKEDITOR_LANG');
		if (defined('GSEDITORLANG'))
			$edlang = GSEDITORLANG;
		$edtool = 'basic';
		if (defined('GSEDITORTOOL'))
			$edtool = GSEDITORTOOL;
		$edoptions = '';
		if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="")
			$edoptions = ", ".GSEDITOROPTIONS;

		if ($edtool == 'advanced') {
			$toolbar = "
            ['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
            '/',
            ['Styles','Format','Font','FontSize']
            ";
		} elseif ($edtool == 'basic') {
			$toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
		} else {
			$toolbar = GSEDITORTOOL;
		}

		$csspath = '';
		if (isset($TEMPLATE) && file_exists(GSTHEMESPATH . $TEMPLATE .'/editor.css'))
			$csspath = 'contentsCss: \''. suggest_site_path() .'theme/'. $TEMPLATE .'/editor.css\',';

		return array(
			'edheight' => $edheight,
			'edlang' => $edlang,
			'edtool' => $edtool,
			'edoptions' => $edoptions,
			'toolbar' => $toolbar,
			'csspath' => $csspath,
			/*'resoutput' => $result*/

		);
	}

	public function getConfigFieldtype(){}
}