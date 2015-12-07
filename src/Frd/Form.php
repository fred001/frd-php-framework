<?php
   class Frd_Form extends Frd_Widget_Form
   {
      protected $render='bootstrap';
      protected $elements=array();
      protected $form_attrs=array();
      protected $hidden_elements=array();

      //protected $validater=null; //validater
      //protected $validates=array(); //validate messages

      protected $form_data=array(); //form's data

      protected $template=false; //custom template to render form

      protected $form_invalid_message=""; //form invalid message, when hidden field invalid (invalid form)

      function __construct($id="form",$attrs=array())
      {
         $this->form_attrs=array_merge(array('method'=>'post'),$attrs);
         $this->setId($id);

         //$this->render=$render;
         $this->elements=array(
            'title'=>'',
            'fields'=>array(),
            'buttons'=>array(),
         );

         //$this->validater=new Frd_Form_Validate();

         parent::__construct();

         $this->init();
      }

      function init()
      {
         //for subclass
      }

      /**
      * init form from config
      */
      function loadConfig($config)
      {
         $default=array(
            'id'=>'',
            'action'=>'',
            'style'=>'bootstrap',
            'attrs'=>'',
            'fields'=>array(),
         
            /*
            'fields'=>array(
               'type'=>'',
               'name'=>'',
               'value'=>'',
               'label'=>'',
               'attrs'=>'',
               'extra_info'=>'',
            ),
            */
         );

         $config=array_merge($default,$config);

         //handle
         if($config['id'] != false)
         {
            $this->setId($config['id']);
         }

         if($config['action'] != false)
         {
            $this->setAction($config['action']);
         }

         if($config['style'] != false)
         {
            $this->setStyle($config['style']);
         }

         $fields=$config['fields'];
         foreach($fields as $field)
         {
            $type=$field['type'];
            $name=$field['name'];
            $value=$field['value'];
            $label=$field['label'];
            $attrs=$field['attrs'];
            $extra_info=$field['extra_info'];

            if(!is_array($attrs))
            {
               $attrs=array();
            }
            if(!is_array($extra_info))
            {
               $extra_info=array();
            }

            $this->addField($type,$name,$value,$label,$attrs,$extra_info);
         }

         //validates
         if(isset($config['validates']))
         {
            foreach($config['validates'] as $name=>$v)
            {
               list($type,$config)=$v;
               $this->addValidate($name,$type,$config);
            }
         
         }


         if(isset($config['submit_button']))
         {
            $this->addSubmitButton($config['submit_button']['label']);
         }
         else
         {
            $this->addSubmitButton('save');
         }
      
      }

      function setId($id)
      {
         $this->setAttr("id",$id);
      }

      function getId()
      {
         return $this->getAttr("id");
      }
         

      function setAction($action)
      {
         $this->form_attrs['action']=$action;
      }

      function setAttr($name,$value)
      {
         $this->form_attrs[$name]=$value;
      }
      function getAttr($name)
      {
         return $this->form_attrs[$name];
      }


      function setTitle($title)
      {
         $this->elements['title']=$title;
      }

      function addField($type,$name,$value='',$label='',$attrs=array(),$extra_info=array())
      {
         $class="Frd_Form_".ucFirst($type);


         if($type == 'hidden')
         {
            $field=new $class($name,$value,null,$attrs,$extra_info);
            //$field->addValidate("required",true);
            $this->hidden_elements[$name]=$field;
            return true;
         }
         else
         {
            $field=new $class($name,$value,$label,$attrs,$extra_info);
            if(isset($extra_info['position']))
            {
               $position=$extra_info['position'];
               unset($extra_info['position']);

               if($position == 'first')
               {
                  $this->elements['fields']=array_merge(array($name=>$field),$this->elements['fields']);

               }
               else
               {
                  array_insert($this->elements['fields'],$position,array($name=>$field));
               }
            }
            else
            {
               $this->elements['fields'][$name]=$field;
            }
         }
      }

      function getField($name)
      {
         return $this->elements['fields'][$name];
      }

      function removeField($name)
      {
         if(isset($this->elements['fields']))
         {
            unset($this->elements['fields'][$name]);
         }
         else if(isset($this->hidden_elements[$name]))
         {
            unset($this->hidden_elements[$name]);
         }
      }

      /*
      function addHiddenField($name,$value)
      {
         $form=new Frd_Html_Form();
         $hidden=$form->hidden($name,$value);

         //$this->hidden_elements[$name]=$hidden->render();
         $this->hidden_elements[$name]=$hidden;
      }
      */

      function addSubmitButton($value)
      {
         $this->addButton($value,array('type'=>'submit'));
      }

      function addButton($value,$attrs=array())
      {
         $attrs['value']=$value;
         if(!isset($attrs['type']))
         {
            $attrs['type']="button";
         }

         $this->elements['buttons'][]=$attrs;
      }

      function setTemplate($template)
      {
         $this->template=$template;
      }

      function render()
      {
         $data=array(
            'form_attrs'=>$this->form_attrs,
            'hidden_fields'=>$this->hidden_elements,
            'form_invalid_message'=>$this->form_invalid_message,
         );

         foreach($this->elements as $key=>$value)
         {
            $data[$key]=$value;
         }

         if($this->getTemplate() != false)
         {
            $path=$this->getTemplate();
         }
         else
         {
            $path=Frd::getFrdTemplatePath().'/form/'.$this->render.'.php';
         }

         $template=new Frd_Template();
         $data=array_merge($this->getData(),$data);

         return $template->render($path,$data);
      }


      /** validate methods **/
      function addValidate($name,$type,$config)
      {
         $this->elements['fields'][$name]->addValidate($type,$config);
         //$this->validater->add($name,$validate);
      }

      function valid($data=array())
      {
         $is_valid=true;

         if($data != false)
         {
            $this->populate($data);
         }

         foreach($this->elements['fields'] as $name=>$field)
         {
            $ret=$this->elements['fields'][$name]->valid();
            if($ret == false)
            {
               $is_valid=false;
            }

            //custom validate method
            $ret=$this->elements['fields'][$name]->checkValid();
            if($ret == false)
            {
               $is_valid=false;
            }
         }

         //check hidden field,for hidden field, only show valid message for form
         //not for field
         foreach($this->hidden_elements as $name=>$field)
         {
            if( $field->valid() == false)
            {
               $is_valid=false;

               $this->form_invalid_message="invalid form"; 
            }
         }


         return $is_valid;
         /*
         $this->setFormData($data);

         if( $this->validater->valid($this->form_data) == false)
         {
            $this->validates=$this->validater->getValidateMessages();
            return false;
         }
         else
         {
            return true;
         }
         */
      }

      function renderJs()
      {
         //return '';
         //$classname="Frd_Form_Validate_".ucfirst($this->render);

         if(!isset($this->form_attrs['id']))
         {
            trigger_error("please set form id for js validate.");
         }

         $form_id=$this->form_attrs['id'];


         $js='var validate=new frd.validate("#'.$form_id.'");';
         $js.="\n";
         foreach($this->elements['fields'] as $name=>$element)
         {
            $js.=$element->getValidateJs();
         }

         $js.="return validate.valid();";
         $js.="\n";
         

         //get validate config
         $data=array();
         foreach($this->elements['fields'] as $name=>$field)
         {
            $data[$name]=$field->getValidates();
         }

         //var_dump($data);exit();
         //
         //$js_validate=new $classname($form_id,$data);

         //$js=$js_validate->render();

         return $js;
      }

      function filter()
      {
         foreach($this->elements['fields'] as $name=>$field)
         {
            $this->setValue($name,$field->filter());
         }

         foreach($this->hidden_elements as $name=>$field)
         {
            $this->setValue($name,$field->filter());
         }

      }

      function populate($data=array())
      {
         if($data != false)
         {
            $this->setFormData($data);
         }

         //populate
         foreach($this->form_data as $name=>$v)
         {
            if(isset($this->elements['fields'][$name]))
            {
               $this->elements['fields'][$name]->setValue($v);
            }

            if(isset($this->hidden_elements[$name]))
            {
               $this->hidden_elements[$name]->setValue($v);
            }

         }
      }

      function setFormData($data)
      {
         foreach($this->elements['fields'] as $name=>$v)
         {
            if(isset($data[$name]))
            {
               if(is_string($data[$name]))
               {
                  $this->form_data[$name]=trim($data[$name]);
               }
               else
               {
                  $this->form_data[$name]=$data[$name];
               }
            }
            /*
            else
            {
               $this->form_data[$name]='';
            }
            */
         }

         //hidden fields
         foreach($this->hidden_elements as $name=>$v)
         {
            if(isset($data[$name]))
            {
               if(is_string($data[$name]))
               {
                  $this->form_data[$name]=trim($data[$name]);
               }
               else
               {
                  $this->form_data[$name]=$data[$name];
               }
            }
            /*
            else
            {
               $this->form_data[$name]='';
            }
            */
         }
      }

      function getFormData()
      {
         return $this->form_data;
      }

      function getData()
      {
         return $this->form_data;
      }


      function getValue($name,$default=false)
      {
         if(isset($this->form_data[$name]))
         {
            return $this->form_data[$name];
         }
         else
         {
            return $default;
         }
      }

      function setValue($name,$value)
      {
         if(isset($this->elements['fields'][$name]))
         {
            $this->elements['fields'][$name]->setValue($value);

            $this->form_data[$name]=$value;
         }
         else if(isset($this->hidden_elements[$name]))
         {
            $this->hidden_elements[$name]->setValue($value);
            $this->form_data[$name]=$value;
         }
      }

      function getJsFunction()
      {
         //elements
         foreach($this->elements['fields'] as $field)
         {
            $this->addJsFunction($field->getJsFUnction());
         }

         return parent::getJsFunction();
      }

      /**
      * add submit js in on ready block
      */
      function getJsOnReady()
      {
         $js="";

         //widgets
         foreach($this->getWidgets() as $widget)
         {
            $js.=$widget->getJsOnFormSubmit();
            $js.="\n";
         }

         //elements
         foreach($this->elements['fields'] as $field)
         {
            $js.=$field->getJsOnFormSubmit();
            $js.="\n";
         }

         $id=$this->getId();
         //add js ready 
         $js="document.getElementById('$id').onsubmit=function(){
              $js
            return true;
         }";

         $this->addJsOnReady($js);

         //validate js
         //it bind submit event, should fix it first
         //$this->addJsOnReady($this->renderJs());

         //elements
         $js='';
         foreach($this->elements['fields'] as $field)
         {
            $js.=$field->getJsOnReady();
            $js.="\n";
         }
         $this->addJsOnReady($js);

         return parent::getJsOnReady();
      }

   }
