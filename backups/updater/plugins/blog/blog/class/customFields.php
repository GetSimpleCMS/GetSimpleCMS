<?php
class customFields extends Blog 
{
  public function __construct()
  {

  }

  public function getReservedFields()
  {
    return array('slug', 'tags', 'date', 'private', 'category', 'title', 'content');
  }

  public function getCustomFields()
  {
    $cfData = getXML(BLOGCUSTOMFIELDS);
    $cf = array('options' => '', 'main' => '');
    $count_options = 0;
    $count_main = 0;
    $count_opt = 0;
    foreach($cfData->item as $custom_field)
    {
      if($custom_field->area == 'options')
      {
        $cf['options'][$count_options]['key'] = (string) $custom_field->desc;
        $cf['options'][$count_options]['label'] = (string) $custom_field->label;
        $cf['options'][$count_options]['type'] = (string) $custom_field->type;
        $cf['options'][$count_options]['value'] = (string) $custom_field->value;
        if ($custom_field->type == "dropdown") 
        {
          $count_opt = 0;
          $cf['options'][$count_options]['options'] = array();
          foreach ($custom_field->option as $option) 
          {
            $cf['options'][$count_options]['options'][] = (string) $option;
            $count_opt++;
          }
        }
        $count_options++;
      }
      elseif($custom_field->area == 'main')
      {
        $cf['main'][$count_main]['key'] = (string) $custom_field->desc;
        $cf['main'][$count_main]['label'] = (string) $custom_field->label;
        $cf['main'][$count_main]['type'] = (string) $custom_field->type;
        $cf['main'][$count_main]['value'] = (string) $custom_field->value;
        if ($custom_field->type == "dropdown") 
        {
          $count_opt = 0;
          $cf['main'][$count_main]['options'] = array();
          foreach ($custom_field->option as $option) 
          {
            $cf['main'][$count_main]['options'][] = (string) $option;
            $count_opt++;
          }
        }
        $count_main++;
      }
    }
    return $cf;
  }

  public function saveCustomFields()
  {
    if(file_exists(BLOGCUSTOMFIELDS))
    {
      if (!copy(BLOGCUSTOMFIELDS, GSBACKUPSPATH . 'other/' . BLOGCUSTOMFIELDSFILE)) return false;
    }
    $data = @new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><channel></channel>');
    for ($count=0; isset($_POST['cf_options_'.$count.'_key']); $count++) 
    {
      if ($_POST['cf_options_'.$count.'_key']) 
      {
        $counttem = $data->addChild('item');
        $counttem->addChild('area')->addCData('options');
        $counttem->addChild('desc')->addCData(htmlspecialchars(stripslashes($_POST['cf_options_'.$count.'_key']), ENT_QUOTES));
        $counttem->addChild('label')->addCData(htmlspecialchars(stripslashes($_POST['cf_options_'.$count.'_label']), ENT_QUOTES));
        $counttem->addChild('type')->addCData(htmlspecialchars(stripslashes($_POST['cf_options_'.$count.'_type']), ENT_QUOTES));
        if ($_POST['cf_options_'.$count.'_value']) 
        {
          $counttem->addChild('value')->addCData(htmlspecialchars(stripslashes($_POST['cf_options_'.$count.'_value']), ENT_QUOTES));
        }
        if ($_POST['cf_options_'.$count.'_options']) 
        {
          $options = preg_split("/\r?\n/", rtrim(stripslashes($_POST['cf_options_'.$count.'_options'])));
          foreach ($options as $option) 
          {
            $counttem->addChild('option')->addCData(htmlspecialchars($option, ENT_QUOTES));
          }
        }
      }
    }
    for ($count_main=0; isset($_POST['cf_main_'.$count_main.'_key']); $count_main++) 
    {
      if ($_POST['cf_main_'.$count_main.'_key']) 
      {
        $counttem = $data->addChild('item');
        $counttem->addChild('area')->addCData('main');
        $counttem->addChild('desc')->addCData(htmlspecialchars(stripslashes($_POST['cf_main_'.$count_main.'_key']), ENT_QUOTES));
        $counttem->addChild('label')->addCData(htmlspecialchars(stripslashes($_POST['cf_main_'.$count_main.'_label']), ENT_QUOTES));
        $counttem->addChild('type')->addCData(htmlspecialchars(stripslashes($_POST['cf_main_'.$count_main.'_type']), ENT_QUOTES));
        if ($_POST['cf_main_'.$count_main.'_value']) 
        {
          $counttem->addChild('value')->addCData(htmlspecialchars(stripslashes($_POST['cf_main_'.$count_main.'_value']), ENT_QUOTES));
        }
        if ($_POST['cf_main_'.$count_main.'_options']) 
        {
          $options = preg_split("/\r?\n/", rtrim(stripslashes($_POST['cf_main_'.$count_main.'_options'])));
          foreach ($options as $option) 
          {
            $counttem->addChild('option')->addCData(htmlspecialchars($option, ENT_QUOTES));
          }
        }
      }
    }
    if(XMLsave($data, BLOGCUSTOMFIELDS))
    {
      return true;
    }
  }

  public function deleteCustomField()
  {
    
  }
  
}