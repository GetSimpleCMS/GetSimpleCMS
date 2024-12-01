<?php
class FieldMoney implements FieldInterface
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
		$this->style = null;
		$this->configs = new stdClass();
	}

	public function render($sanitize=false)
	{
		if(is_null($this->name))
			return false;

		$itemeditor = $this->tpl->getTemplates('field');
		$textfield = $this->tpl->getTemplate('text', $itemeditor);

		// let's check selected notation
		$notation = isset($this->configs->notation) ? $this->configs->notation : '';
		if($notation == 'French notation')
			$value = number_format(floatval($this->value), 2, ',', ' ');
		elseif($notation == 'German notation')
			$value = number_format(floatval($this->value), 2, ',', '.');
		else
			$value = number_format(floatval($this->value), 2, '.', '');


		$output = $this->tpl->render($textfield, array(
				'name' => $this->name,
				'class' => $this->class,
				'style' => !empty($this->style) ? ' style="'.$this->style.'" ' : '',
				'id' => $this->id,
				'value' => $value), true, array()
		);
		return $output;
	}

	public function getConfigFieldtype()
	{
		/* ok, get our dropdown field, infotext and area templates */
		$tplselect = $this->tpl->getTemplate('select', $this->tpl->getTemplates('field'));
		$tploption = $this->tpl->getTemplate('option', $this->tpl->getTemplates('field'));
		$tplinfotext = $this->tpl->getTemplate('infotext', $this->tpl->getTemplates('itemeditor'));
		$tplarea = $this->tpl->getTemplate('fieldarea', $this->tpl->getTemplates('itemeditor'));


		$option = '';
		$select = '';

		// next, build options template: <option value=[[option]][[selected]]>[[option]]</option>
		$option .= $this->tpl->render($tploption, array(
				'option' => 'English notation',
				'selected' => (empty($this->configs->notation) || $this->configs->notation == 'English notation'
					) ? ' selected ' : ''
			)
		);
		$option .= $this->tpl->render($tploption, array(
				'option' => 'French notation',
				'selected' => (!empty($this->configs->notation) &&
						$this->configs->notation == 'French notation') ? ' selected ' : ''
			)
		);
		$option .= $this->tpl->render($tploption, array(
				'option' => 'German notation',
				'selected' => (!empty($this->configs->notation) &&
						$this->configs->notation == 'German notation') ? ' selected ' : ''
			)
		);

		// render select template <select name="[[name]]">[[options]]</select>
		$select = $this->tpl->render($tplselect, array(
				// NOTE: The PREFIX must always be used as a part of the field name
				'name' => self::PREFIX . 'notation',
				'options' => $option,
			)
		);

		// render infotext template <p class="field-info">[[infotext]]</p>
		$infotext = $this->tpl->render($tplinfotext, array(
				'infotext' => '<i class="fa fa-info-circle"></i> Enter here your Infotext')
		);

		// let's merge the pieces and return the output
		return $this->tpl->render($tplarea, array(
				'fieldid' =>  '',
				'label' => 'Choose notation',
				'infotext' => $infotext,
				'area-class' => 'fieldarea',
				'label-class' => '',
				'required' => '',
				'field' => $select), true
		);
	}
}