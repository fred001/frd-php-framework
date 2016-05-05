<?php
   ini_set("display_errors",1);
   $LIB_PATH=dirname(__FILE__)."/../lib";
   set_include_path($LIB_PATH.PATH_SEPARATOR.get_include_path());

   require_once("Frd/Frd.php");
   define("ROOT_PATH",dirname(__FILE__).'/../');
   require_once(ROOT_PATH."/functions.php");

   require_once(ROOT_PATH."/test/functions.php");

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

   $setting['dbs']['default']=array(
      'adapter' => 'pdo_mysql',
      'host' => "localhost",
      'dbname' => "test",
      'username' => "root",
      'password' => "",
   );


   Frd::init($setting);


   //extra for test
   define("TEST_PATH",dirname(__FILE__));
   define("TEST_DATA_PATH",TEST_PATH."/data");

   //usage: 
   //*$cmd="ls -l  error.php";
   //*list($stdout,$stderr)= execute_cmd($cmd);
   //*echo $stdout; //cmd stdout result 
   //*var_dump($stderr);  //cmd stderr result, if no error, false
   function execute_cmd($cmd)
   {
      $descriptorspec = array(
         0 => array("pipe", "r"),  // stdin
         1 => array("pipe", "w"),  // stdout
         2 => array("pipe", "w"),  // stderr
      );

      //
      $pipes=array();

      $working_path=dirname(__FILE__);
      $env=null;
      $other_options=null;

      $process = proc_open($cmd, $descriptorspec, $pipes, $working_path,$env,$other_options);

      if($process === false)
      {
         $stdout = "execute cmd: failed";
         $stdin ="";
      }
      else
      {
         $stdout = stream_get_contents($pipes[1]);
         fclose($pipes[1]);

         $stderr = stream_get_contents($pipes[2]);
         fclose($pipes[2]);
      }

      proc_close($process);

      //
      if($stderr == false) $stderr = false ;

      return array($stdout,$stderr);
   }

