<?php
class Frd_Form_Select extends Frd_Form_Field
{
  function __construct($name,$value='',$label=false,$attrs=array(),$extra_info=array())
  {
     if(isset($attrs['options']))
     {
        $this->setOptions($attrs['options']);
        unset($attrs['options']);
     }
     else
     {
        $this->options=array();
     }

     parent::__construct($name,$value,$label,$attrs,$extra_info);
  }

  function getField()
  {
    $selected=$this->getValue();

    $this->_params['name']=$this->getName();
    $element=new Frd_Html_Element('select',$this->_params);
    foreach($this->getOptions() as $value=>$text)
    {
      if($selected != false && $selected == $value)
        $element->add('option',array('value'=>$value,'selected'=>'selected'),$text);
      else
        $element->add('option',array('value'=>$value),$text);
    }

    $html= $element->toHtml();
    return $html;
  }

  function getOptions()
  {
    return $this->options;
  }

  /** 
  * add option
  */
  function addOption($value,$text)
  {
     $this->options[$value]=$text;
  }

  /** 
  * preappend option, add as first item
  * if the value is interger , it will be 0 !!
  */
  function preappendOption($value,$text)
  {
     $this->options=array_merge(array($value=>$text),$this->options);
  }

  /** 
  * edit option ,actually it is the same as add option
  */
  function editOption($value,$text)
  {
     $this->options[$value]=$text;
  }


  /**
  * delete option
  */
  function deleteOption($value)
  {
     if(isset($this->options[$value]))
     {
        unset($this->options[$value]);
     }
  }

}
?>
