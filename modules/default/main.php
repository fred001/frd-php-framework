<?php 
class Index extends Frd_Module
{
  function getLayout($name)
  {
    $path=$this->getTemplatePath("layout/$name");
    $template=new Frd_Template();
    $template->setPath($path);


    return $template;
  }
}
