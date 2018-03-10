<?php
   /**
   * form's field
   */
   class Frd_Form_Field extends Frd_Widget_Form
   {
      protected $_value='';
      protected $_label='';
      protected $_style='bootstrap';
      protected $_params=array();  //array
      protected $_info='';
      protected $_invalid_info='';
      protected $_is_valid=true;

      protected $validate=null;

      function __construct($name,$value='',$label=false,$attrs=array(),$extra_info=array())
      {
         parent::__construct();

         $this->setName($name);
         $this->setValue($value);

         if($label == false && $label !== null)
         {
            $label=ucfirst($name);
         }
         $this->setLabel($label);

         $this->_params=$attrs;

         $this->validate=new Frd_Form_Validate($name);

         $this->init();

         //extra info
         $this->setExtrainfo($extra_info);
         /*
         foreach($extra_info as $k=>$v)
         {
            $this->set($k,$v);
         }
         */
      }

      function init()
      {
         ;
      }

      function setStyle($style)
      {
         $this->_style=$style; 
      }

      function getStyle()
      {
         return $this->_style;
      }


      function setParam($key,$value)
      {
         $this->_params[$key]=$value;
      }

      function getParam($key)
      {
         return $this->_params[$key];
      }

      function disable($disable=true)
      {
         if($disable == true)
         {
         $this->setParam("disabled","disabled");
         }
         else
         {
            unset($this->_params['disabled']);
         }
      }

      function readonly($readonly=true)
      {
         if($readonly == true)
         {
            $this->setParam("readonly","readonly");
         }
         else
         {
            unset($this->_params['readonly']);
         }
      }

      function setValue($value)
      {
         $this->_value=$value; 
      }

      function getValue()
      {
         return $this->_value;
      }

      function setLabel($label)
      {
         $this->_label=$label; 
      }

      function getLabel()
      {
         return $this->_label;
      }


      function setInfo($info)
      {
         $this->_info=$info;
      }


      function getInfo()
      {
         return $this->_info;
      }


      //setInvalidInfo

      /**
      * render field html
      */
      function getField()
      {
         ;
      }

      function render()
      {
         if($this->label === null)
         {
            return $this->getField();
         }

         $path=Frd::getFrdTemplatePath().'/form/field/'.$this->_style.'.php';
         $template=new Frd_Template();
         $vars=array(
            'value'=>$this->getValue(),
            'label'=>$this->getLabel(),
            'field'=>$this->getField(),
            'is_valid'=>$this->_is_valid,
            'info'=>$this->getInfo(),
            'invalid_infos'=>$this->getValidateMessages(),
         );
         //extra info
         foreach($this->getExtrainfo() as $k=>$v)
         {
            $vars[$k]=$v;
         }
         

         $html=$template->render($path,$vars);

         return $html;
      }

      function addValidate($type,$config)
      {
         $this->validate->add($type,$config);
      }

      function valid()
      {
         $value=$this->getValue();
         $this->_is_valid=$this->validate->valid($value);

         return $this->_is_valid;
      }

      function getValidateJs()
      {
         return $this->validate->renderJs();
      }

      function checkValid()
      {
         /*
         $value=$this->getValue();

         $this->_is_valid= false;
         $this->validate->addMessage("unknown error");
         */

         return true;
      }

      function getValidateMessages()
      {
         return $this->validate->getValidateMessages();
      }
   }
