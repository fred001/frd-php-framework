<?php
   class Frd_Form_Validate 
   {
      protected $name=''; //field name

      protected $validates=array();
      protected $messages=array(); //valid failed messages

      protected $invalid_messages=array(
         'required'=> "This field is required.",
         'remote'=> "Please fix this field.",
         'email'=> "Please enter a valid email address.",
         'url'=> "Please enter a valid URL.",
         'date'=> "Please enter a valid date.",
         'dateISO'=> "Please enter a valid date (ISO).",
         'number'=> "Please enter a valid number.",
         'digits'=> "Please enter only digits.",
         'creditcard'=> "Please enter a valid credit card number.",
         'equalTo'=> "Please enter the same value again.",
         'accept'=> "Please enter a value with a valid extension.",
         'maxlength'=> "Please enter no more than {0} characters.",
         'minlength'=> "Please enter at least {0} characters.",
         'rangelength'=> "Please enter a value between {0} and {1} characters long.",
         'range'=> "Please enter a value between {0} and {1}.",
         'max'=> "Please enter a value less than or equal to {0}.",
         'min'=> "Please enter a value greater than or equal to {0}.",
      );

      function __construct($name)
      {
         $this->name=$name;
      }

      function add($type,$config)
      {
         $this->validates[$type]=$config;
      }

      function getData()
      {
         return $this->validates;
      }

      function valid($value)
      {
         $valid=true;

         foreach($this->validates  as $type=>$config)
         {
            $method="valid".ucfirst(strtolower($type));

            //check if valid method exists
            if(method_exists($this,$method) == false)
            {
               trigger_error("class method not exists: $method");
            }

            //for all validate, the validate data should exists
            if($value == false)
            {
               $this->addValidateMessage("required");
               $valid=false;
               continue;
            }

            if( $this->$method($config,$value) == false)
            {
               $this->addValidateMessage($type,$config);
               $valid=false;
            }

         }
      
         return $valid;
      }

      function addValidateMessage($type=false,$value=false)
      {
         //TODO
         $msg=$this->invalid_messages[$type];

         $this->messages[]=$msg;
      }

      function addMessage($msg)
      {
         $this->messages[]=$msg;
      }

      function getValidateMessages()
      {
         return $this->messages;
      }

      /* valid methods */
      function validRequired($config,$value)
      {
         if(trim($value) == false)
         {
            return false;
         }
         else
         {
            return true;
         }
      }

      function validEmail($config,$value)
      {
         if(filter_var($value, FILTER_VALIDATE_EMAIL) == false)
         {
            return false;
         }
         else
         {
            return true;
         }
      }


      /* valid error methods */

      function renderJs()
      {
         $js='';
         foreach($this->validates as $type=>$config)
         {
            $js.="validate.add('".$this->name."','".$type."',".$config.");";
            $js.="\n";
         }

         return $js;
      }
   
   }

