<?php

class Frd_Form_File extends Frd_Form_Field
{


  function getField()
  {
     $this->_params['type']='file';
     $this->_params['name']=$this->getName();
    $element=new Frd_Html_Element('input',$this->_params);
    $html= $element->toHtml();

    return $html;
  }
}
?>
