<?php
   function app()
   {
      return Frd::$app;
   }

   function getModule($name="default")
   {
      return app()->getModule($name);
   }

   function url($path,$params=array())
   {
      return app()->url($path,$params);
   }

   function module()
   {
      return app()->getGlobal("_module");
   }

   function controller()
   {
      return app()->getGlobal("_controller");
   }

