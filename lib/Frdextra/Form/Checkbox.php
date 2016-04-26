<?php
   class Frd_Form_Checkbox extends Frd_Form_Field
   {
      function __toString()
      {
         $params['value']='1';
         $params['type']='checkbox';
         if($this->value == true)
         {
            $this->params['checked']='checked';
         }

         $element=new Frd_Html_Element('input',$this->params);
         $html= $element->toHtml();

         return $html;
      }


      function getField()
      {
         $this->_params['type']='checkbox';
         $this->_params['name']=$this->getName();
         $this->_params['value']='y';

         if($this->getValue() == true)
         {
            $this->_params['checked']='checked';
         }

         $element=new Frd_Html_Element('input',$this->_params);
         $html= $element->toHtml();

         return $html;
      }
   }
?>
