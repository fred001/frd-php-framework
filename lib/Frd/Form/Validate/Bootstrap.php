<?php
class Frd_Form_Validate_Bootstrap extends Frd_Block
{
   function __construct($id,$data)
   {
      $path=Frd::getFrdTemplatePath().'/form/validate/bootstrap.php';
      $this->setTemplate($path);

      //assign values
      $this->assign("id",$id);
      $this->assign("data",$data);
   }
}
