<?php
   function app()
   {
      return Frd::$app;
   }

   function getModule($name)
   {
      return app()->getModule($name);
   }

   function url($path,$params=array())
   {
      return app()->url($path,$params);
   }

