<?php

class FieldDatepicker extends FieldText implements FieldInterface
{
	/**
	 * @var int
	 */
	protected $maxLen = 255;

	/**
	 * FieldDatepicker constructor.
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
	 * @return bool|string|Template
	 */
	public function render($sanitize = false)
	{
		if(is_null($this->name)) { return false; }

		$itemeditor = $this->tpl->getTemplates('field');
		$textfield = $this->tpl->getTemplate('text', $itemeditor);

		// Set date format
		$format = isset($this->configs->format) ? imanager('sanitizer')->text($this->configs->format) : 'yy-mm-dd';

		$value = '';
		if(!empty($this->value)) {
			switch($format) {
				case 'dd-mm-yy':
					$value = @date('d-m-Y', $this->value);
					break;
				case 'yy/mm/dd':
					$value = @date('Y/m/d', $this->value);
					break;
				case 'dd/mm/yy':
					$value = @date('d/m/Y', $this->value);
					break;
				case 'yy.mm.dd':
					$value = @date('Y.m.d', $this->value);
					break;
				case 'dd.mm.yy':
					$value = @date('d.m.Y', $this->value);
					break;
				default:
					$value = @date('Y-m-d', $this->value);
					break;
			}
		}

		$output = $this->tpl->render($textfield, array(
				'name' => $this->name,
				'class' => $this->class,
				'style' => !empty($this->style) ? ' style="'.$this->style.'" ' : '',
				'id' => 'dp'.$this->id,
				'value' => $value), true, array()
		);

		$output .= '
		<script>
			$(function() {
				$( "#dp'.$this->id.'" ).datepicker({
					dateFormat: "'. $format .'"
				});
			});
		</script>';

		return $output;
	}

	/**
	 * Make that field configurable
	 *
	 * @return Template
	 */
	public function getConfigFieldtype()
	{
		$tplselect = $this->tpl->getTemplate('select', $this->tpl->getTemplates('field'));
		$tploption = $this->tpl->getTemplate('option', $this->tpl->getTemplates('field'));
		$tplinfotext = $this->tpl->getTemplate('infotext', $this->tpl->getTemplates('itemeditor'));
		$tplarea = $this->tpl->getTemplate('fieldarea', $this->tpl->getTemplates('itemeditor'));

		$option = '';
		$select = '';
		$check = isset($this->configs->format) ? (string) $this->configs->format : '';
		// Build selectable format options
		$option .= $this->tpl->render($tploption, array(
				'option' => 'yy-mm-dd',
				'selected' => (!empty($check) && $this->configs->format
						== 'yy-mm-dd') ? ' selected ' : ''
			)
		);
		$option .= $this->tpl->render($tploption, array(
				'option' => 'dd-mm-yy',
				'selected' => (!empty($check) && $this->configs->format
						== 'dd-mm-yy') ? ' selected ' : ''
			)
		);
		$option .= $this->tpl->render($tploption, array(
				'option' => 'yy/mm/dd',
				'selected' => (!empty($check) &&
						$this->configs->format == 'yy/mm/dd') ? ' selected ' : ''
			)
		);
		$option .= $this->tpl->render($tploption, array(
				'option' => 'dd/mm/yy',
				'selected' => (!empty($check) &&
						$this->configs->format == 'dd/mm/yy') ? ' selected ' : ''
			)
		);

		$option .= $this->tpl->render($tploption, array(
				'option' => 'yy.mm.dd',
				'selected' => (!empty($check) && $this->configs->format
						== 'yy.mm.dd') ? ' selected ' : ''
			)
		);
		$option .= $this->tpl->render($tploption, array(
				'option' => 'dd.mm.yy',
				'selected' => (!empty($check) && $this->configs->format
						== 'dd.mm.yy') ? ' selected ' : ''
			)
		);

		// Render select template <select name="[[name]]">[[options]]</select>
		$select = $this->tpl->render($tplselect, array(
				// NOTE: The PREFIX must always be used as a part of the field name
				'name' => self::PREFIX . 'format',
				'options' => $option,
			)
		);

		// render infotext template <p class="field-info">[[infotext]]</p>
		$infotext = $this->tpl->render($tplinfotext, array(
				'infotext' => '<i class="fa fa-info-circle"></i> Enter here your Infotext')
		);

		// Return merget template
		return $this->tpl->render($tplarea, array(
				'fieldid' =>  '',
				'label' => 'Date format',
				'infotext' => ''/*$infotext*/,
				'area-class' => 'fieldarea',
				'label-class' => '',
				'required' => '',
				'field' => $select), true
		);
	}
}