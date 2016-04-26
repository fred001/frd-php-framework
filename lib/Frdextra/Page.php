<?php
class Frd_Page extends Frd_Template
{
  protected $layout=null;
  protected $name="";

  function setName($name)
  {
    $this->name=$name;
  }

  function getName()
  {
    return $this->name;
  }

  function setLayout($layout)
  {
    $this->layout=$layout;
  }

  function setTemplate($path)
  {
    $this->setPath($path);
  }

  function render()
  {
    if($this->layout)
    {
      $this->layout->content=parent::render();
      $this->layout->PAGE_NAME=$this->name;
      $this->layout->_page=$this;

      return $this->layout->render();
    }
    else
    {
      return parent::render();
    }
  }

}
