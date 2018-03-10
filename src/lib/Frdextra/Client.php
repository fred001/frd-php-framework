<?php
   class Frd_Client extends Frd_Object
   {
      //request id show be unique in all requests
      //this is for identifier  LOG, ERROR, TEST , PROFILER, and so on
      protected $request_id=false;  

      //id is client id, this id should also exists in $_COOKIE
      //then the next time user visit , can be remembered
      protected $id=false;

      protected $backend=null; //backend which can save 

      function __construct()
      {
         $this->request_id=$this->uuid();
      }

      public function getId()
      {
         //session id maybe changed, so should also update it
         $this->id=session_id();

         return $this->id;
      }

      public function getSessionId()
      {
         return $this->getId();
      }


      public function getRequestId()
      {
         return $this->request_id;
      }

      protected function uuid() 
      {
         return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
         // 32 bits for "time_low"
         mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

         // 16 bits for "time_mid"
         mt_rand( 0, 0xffff ),

         // 16 bits for "time_hi_and_version",
         // four most significant bits holds version number 4
         mt_rand( 0, 0x0fff ) | 0x4000,

         // 16 bits, 8 bits for "clk_seq_hi_res",
         // 8 bits for "clk_seq_low",
         // two most significant bits holds zero and one for variant DCE1.1
         mt_rand( 0, 0x3fff ) | 0x8000,

         // 48 bits for "node"
         mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ));
      }


      function render()
      {
         return sprintf("REQUEST ID: %s | CLIENT ID: %s",$this->request_id,$this->getId());
      }


      function setBackend($backend)
      {
         $this->backend=$backend;

         $this->backend->id=$this->getId();
         $this->backend->request_id=$this->getRequestId();
      }


      function get($key,$default=null)
      {
         if($this->backend != false)
         {
            return $this->backend->$key;
         }
         else
         {
            return parent::get($key,$default);
         }
      }

      function set($key,$value)
      {
         if($this->backend != false)
         {
            return $this->backend->$key=$value;
         }
         else
         {
            return parent::set($key,$value);
         }
      }
   }
