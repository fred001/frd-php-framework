<?php
   /**
   * base object , the  data is private, so subclass can not visit it directly, that's important, 
   *  if subclass also  have the attribate  $data, there will not conflict
   */
   class Frd_Object 
   {
      //main attribute
      //private $data=array(); //must be private, so will not conflict with subclass
      protected $data=array(); //must be private, so will not conflict with subclass


      function __set($key,$value)
      {
         $this->set($key,$value);
      }


      function __get($key)
      {
         return $this->get($key,null);
      }

      function __unset($key)
      {
         if($this->has($key))
         {
            unset($this->data[$key]);
         }
      }

      function __isset($key)
      {
         return $this->has($key);
      }

      function set($key,$value)
      {
        $this->data[$key]=$value;	
      }

      function get($key,$default=null)
      {
         if(isset($this->data[$key]))
         {
            return $this->data[$key];
         }
         else
         {
            return $default;
         }
      }

      function has($key)
      {
        return isset($this->data[$key]);
      }

      function clearData()
      {
         $this->data=array();
      }


      function setData($data)
      {
         if(!is_array($data))
         {
            throw new Exception("INVALID PARAM : [data] not array");
         }

         foreach($data as $k=>$v)  
         {
            $this->set($k,$v);
         }
      }

      function getData()
      {
         return $this->data;
      }

      function __call($func,$params)
      {
        if(strpos($func,"set") === 0)
        {
          if(count($params) == 1)
          {
            $key=strtolower(substr($func,3));

            return $this->set($key,$params[0]);
          }

        }
        else if(strpos($func,"get") === 0)
        {
          $key=strtolower(substr($func,3));

          //check self attr first
          if(isset($this->$key))
          {
            return $this->$key;
          }

          if(count($params) > 0)
          {
            $default=$params[0];
          }
          else
          {
            $default=null;
          }

          return $this->get($key,$default);

        }
        else if(strpos($func,"has") === 0)
        {
          $key=strtolower(substr($func,3));

          return $this->has($key);
        }
        else
        {
          throw new Exception("unknown method:".$func);
        }
      }
   }
