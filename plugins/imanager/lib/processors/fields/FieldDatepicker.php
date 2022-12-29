<?php

class FieldDatepicker implements FieldInterface
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
		$format = isset($this->configs->format) ? $this->configs->format : '';

		$value = '';
		if(!empty($this->value))
		{
			switch($format)
			{
				case 'dd-mm-yy':
					$value = date('d-m-Y', $this->value);
					break;
				case 'yy/mm/dd':
					$value = date('Y/m/d', $this->value);
					break;
				case 'dd/mm/yy':
					$value = date('d/m/Y', $this->value);
					break;
				case 'yy.mm.dd':
					$value = date('Y.m.d', $this->value);
					break;
				case 'dd.mm.yy':
					$value = date('d.m.Y', $this->value);
					break;
				default:
					$value = date('Y-m-d', $this->value);
					break;
			}
		}

		$output = $this->tpl->render($textfield, array(
				'name' => $this->name,
				'class' => $this->class,
				'style' => !empty($this->style) ? ' style="'.$this->style.'" ' : '',
				'id' => 'datepicker',
				'value' => $value), true, array()
		);

		$output .= '
		<script>
			$(function() {
				$( "#datepicker" ).datepicker({
					dateFormat: "'. $format .'"
				});
			});
		</script>';



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

		$check = isset($this->configs->format) ? (string) $this->configs->format : '';

		// next, build options template: <option value=[[option]][[selected]]>[[option]]</option>

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

		// render select template <select name="[[name]]">[[options]]</select>
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

		// let's merge the pieces and return the output
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