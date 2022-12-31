<?php

class FieldMoney extends FieldText implements FieldInterface
{
	/**
	 * FieldMoney constructor.
	 *
	 * @param TemplateEngine $tpl
	 */
	public function __construct(TemplateEngine $tpl) {
		parent::__construct($tpl);
	}

	/**
	 * Renders the field markup
	 *
	 * @param bool $sanitize
	 *
	 * @return bool|Template
	 */
	public function render($sanitize = false)
	{
		if(is_null($this->name)) { return false; }

		$itemeditor = $this->tpl->getTemplates('field');
		$textfield = $this->tpl->getTemplate('text', $itemeditor);

		// Check selected notation
		$notation = isset($this->configs->notation) ? $this->configs->notation : '';
		$value = self::toMoneyFormatRaw($this->value, $notation);

		$output = $this->tpl->render($textfield, array(
			'name' => $this->name,
			'class' => $this->class,
			'style' => !empty($this->style) ? ' style="'.$this->style.'" ' : '',
			'id' => $this->id,
			'value' => $value), true, array()
		);
		return $output;
	}

	/**
	 * A static method for converting float to current raw money format
	 *
	 * @param $number
	 *
	 * @return string
	 */
	public static function toMoneyFormatRaw($number, $notation = null) {
		if($notation == 'French notation') {
			$value = number_format(floatval($number), 2, ',', ' ');
		} elseif($notation == 'German notation') {
			$value = number_format(floatval($number), 2, ',', '.');
		} else {
			$value = number_format(floatval($number), 2, '.', '');
		}
		return $value;
	}

	/**
	 * A static method for converting float to current money format with currency
	 *
	 * @param $number
	 * @param string $currency
	 *
	 * @return string
	 */
	public static function toMoneyFormat($number, $currency = '€', $notation = null) {
		if($notation == 'French notation') {
			$value = number_format(floatval($number), 2, ',', ' ')." $currency";
		} elseif($notation == 'German notation') {
			$value = number_format(floatval($number), 2, ',', '.')." $currency";
		} else {
			$value = number_format(floatval($number), 2, '.', '')." $currency";
		}
		return $value;
	}

	/**
	 * Render config menu section
	 *
	 * @return Template
	 */
	public function getConfigFieldtype()
	{
		// OK, get the dropdown field, infotext and area templates
		$tplselect = $this->tpl->getTemplate('select', $this->tpl->getTemplates('field'));
		$tploption = $this->tpl->getTemplate('option', $this->tpl->getTemplates('field'));
		$tplinfotext = $this->tpl->getTemplate('infotext', $this->tpl->getTemplates('itemeditor'));
		$tplarea = $this->tpl->getTemplate('fieldarea', $this->tpl->getTemplates('itemeditor'));


		$option = '';
		$select = '';
		// Build options template: <option value=[[option]][[selected]]>[[option]]</option>
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
				'infotext' => '<i class="fa fa-info-circle"></i> Choose one of the available notations')
		);

		// Return merged output parts
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
