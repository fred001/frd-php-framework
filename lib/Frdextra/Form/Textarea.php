<?php
   class Frd_Form_Textarea extends Frd_Form_Field
   {

      function getField()
      {
         $this->_params['name']=$this->getName();
         $element=new Frd_Html_Element('textarea',$this->_params);

         $value=$this->getValue();

         //special ,but it is text for textarea value
         $element->appendText($value);

         $html= $element->toHtml();
         return $html;
      }
   }
