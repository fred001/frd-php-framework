<?php
   // filter +handle 的混合体
   /*
   $page=new Frd_Handle();
   $page->allowParams(array("name","page"));

   $page->addFilter("page","default",1);
   $page->addValidate("page","required");

   $page->addFilter("page","toint");
   $page->addValidate("page","int");
   $page->addValidate("page",">",array("value"=>0));
   $page->addValidate("page","<",array("value"=>10));


   $page->addFilter("name","trim");
   $page->addValidate("name","required");

   if($page->handle($_GET) == false)
   {
      echo $page->getErrorMsg();
      //var_dump($page->getErrorMsg());
      exit();
   }
   else
   {
      print_r($page->getData());
   }


   */



   class Frd_Handle
   {
      protected $params=array();
      protected $handle=array();

      protected $result=null;
      protected $error_msg='';
      protected $data=array();

      function allowParams($params=array())
      {
         $this->params=$params;
      }

      function addValidate($key,$name,$option=array())
      {
         $this->handle[]=array(
            'type'=>"validate",
            'key'=>$key,
            'name'=>$name,
            'option'=>$option,
         );
      }

      function addFilter($key,$name,$option=array())
      {
         $this->handle[]=array(
            'type'=>"filter",
            'key'=>$key,
            'name'=>$name,
            'option'=>$option,
         );
      }

      function handle($request)
      {
         foreach($request as $k=>$v)
         {
            if(!in_array($k,$this->params))
            {
               unset($request[$k]);
            }
         }


         foreach($this->params as $key)
         {
            if(!isset($request[$key]))
            {
               $request[$key]=null;
            }
         }

         foreach($this->handle as $k=>$row)
         {
            if($row['type'] == 'validate')
            {
               $key=$row['key'];
               $name=$row['name'];
               $option=$row['option'];

               if( $this->validate($key,$request[$key],$name,$option) == false)
               {
                  return false;
               }
            }
            else if($row['type'] == 'filter')
            {
               $key=$row['key'];
               $name=$row['name'];
               $option=$row['option'];

               if(isset($request[$key]))
               {
                  $value=$request[$key];
               }
               else
               {
                  $value=null;
               }
               $request[$key]=$this->filter($key,$value,$name,$option);
            }
         }

         $this->data=$request;

         return true;
      }

      function getData()
      {
         return $this->data;
      }

      function validate($key,$value,$name,$option=array())
      {
         if($name == "required")
         {
            if($value == false)
            {
               $this->error_msg= "$key($value) is required";
               return false;
            }
         }
         else if($name == "int")
         {
            if(is_int($value) == false)
            {
               $this->error_msg= "$key($value) should be int";
               return false;
            }
         }
         else if($name == ">")
         {
            if(($value > $option) == false)
            {
               $this->error_msg= "$key($value) should > ".$option;
               return false;
            }
         }
         else if($name == "<")
         {
            if(($value < $option) == false)
            {
               $this->error_msg= "$key($value) should < ".$option['value'];
               return false;
            }
         }


         return true;
      }

      function filter($key,$value,$name,$option=array())
      {
         if($name == "trim")
         {
            return trim($value);
         }
         else if($name == "toint")
         {
            return intval($value);
         }
         else if($name == "default")
         {
            if($value == false)
            {
               return $option;
            }
            else
            {
               return $value;
            }
         }
      }

      function getResult()
      {
         return $this->result;
      }

      function getErrorMsg()
      {
         return $this->error_msg;
      }
   }
