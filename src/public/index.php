<?php
   /*
   what does this file did ?

   1.add lib folder to include_path (for autoloader)
   2.define  ROOT_PATH  (for other place use this variable)
   3.load setting
   4.Frd::init($setting)   (init)
   5.app()->run()   (after init, the Frd_App object created ,so not can run )
   */

   ini_set("display_errors",1);
   $LIB_PATH=dirname(__FILE__)."/../lib";
   set_include_path($LIB_PATH.PATH_SEPARATOR.get_include_path());
   require_once("Frd/Frd.php");  //load core class 

   define("ROOT_PATH",dirname(__FILE__).'/../');
   
   //this file include framework's interface function 
   //such as    app(), url() ,getModule()
   require_once(ROOT_PATH."/functions.php"); 

   //read config
   require_once(ROOT_PATH."/default_setting.php");

   if(file_exists(ROOT_PATH."/local/setting.php"))
   {
      require_once(ROOT_PATH."/local/setting.php");

      $setting=array_merge($setting_default,$setting);
   }
   else
   {
      $setting=$setting_default;
   }


   Frd::init($setting);
   session_start();

   try{
      //will parse url and load correct controller 
      app()->run();
   }
   catch(Exception $e)
   {
      if($e->getMessage() == "REWRITE URL FAILED")
      {
         app()->run("/404");

         //require_once(ROOT_PATH."/error/404.php");
      }
      else
      {
         throw $e;
         //error_log($e);
         //app()->run("/error");
      }
   }
