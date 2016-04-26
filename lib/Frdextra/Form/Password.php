<?php
class Frd_Form_Password  extends Frd_Form_Field
{
  function getField()
  {
    $this->_params['type']='password';
    $this->_params['name']=$this->getName();
    $this->_params['value']=$this->getValue();

    $element=new Frd_Html_Element('input',$this->_params);
    $html= $element->toHtml();

    return $html;
  }
}
?>
