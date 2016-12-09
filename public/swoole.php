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

   $serv = new swoole_http_server("127.0.0.1", 9502);

   $serv->on('Request', function($request, $response) {
      //var_dump($request->get);
      //var_dump($request->post);
      //var_dump($request->cookie);
      //var_dump($request->files);
      //var_dump($request->header);

      //var_dump($request->server);
      //$response->cookie("User", "Swoole");
      //$response->header("X-Server", "Swoole");
      //$response->end("<h1>Hello Swoole!</h1>");

      foreach($request->get as $k=>$v)
      {
         $_GET[$k]=$v;
      }


      foreach($request->server as $k=>$v)
      {
         $_SERVER[strtoupper($k)]=$v;
      }


      ob_start();
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

      $content=ob_get_clean();

      var_dump($content);
      $response->end($content);

   });

   $serv->start();

