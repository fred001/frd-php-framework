<?php
   /*
   $e=new Frd_Exception("test","test exception");

   try{
      throw new Exception("TEST");
      throw $e;
   }
   catch(Frd_Exception $e)
   {
      echo $e->getType();
   }
   catch(Exception $e)
   {
      echo $e->getMessage();
   }

   echo 'bbb';

   */

   class Frd_Exception extends Exception
   {
      protected $type="EXCEPTION";
      function __construct($type,$message)
      {
         $this->type=$type;

         parent::__construct($message);
      }

      function getType()
      {
         return $this->type;
      }
   }
