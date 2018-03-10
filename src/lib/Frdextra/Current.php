<?php
   /**
   * for save app's current variable
   *  this variable is necessary for all modules and so on
   *  so does not need to pass them as parameter each function and methods, learn from wordpress
   * 
   *  and it can not be null, should check when get the variable
   *  but can be false
   * 
   *  NEXT: 
        share variables between requests
   *
   */
   class Frd_Current extends Frd_Object
   {
      //current variables backend
      //when have backend ,can share variable between requests
      //the key is current client's id (normally is session_id)

      protected $_backend=null;  

      /*
      function has($key)
      {
         if($this->_backend != false)
         {
            $this->backend->has($key);
         }
         else
         {
            return parent::has($key);
         }
      }

      function set($key,$value)
      {
         if($this->_backend != false)
         {
            $this->backend->set($key,$value);
         }
         else
         {
            return parent::set($key,$value);
         }
      }
      */

      function get($key,$default=null)
      {
         if($default === null && parent::has($key) == false)
         {
            Frd::error('current variable not exists: '.$key);
         }

         $value= parent::get($key,$default);

         if($value === null)
         {
            Frd::error("current variable [ $key ]== false: ".$value);
         }

         return parent::get($key,$default);
      }

      /**
      * @param string $type  file,session,db
      */
      /*
      function setBackend($type)
      {
         if($type == 'file')
         {
            $this->_backend=new Frd_Current_File();
         }
         else if($type == 'session')
         {
            $this->_backend=new Frd_Current_Session();
         }
         else if($type == 'db')
         {
            $this->_backend=new Frd_Current_Db();
         }
      }
      */
   }
