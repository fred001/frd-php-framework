<?php
ini_set("display_errors",1);
$LIB_PATH=dirname(__FILE__)."/../lib";
set_include_path($LIB_PATH.PATH_SEPARATOR.get_include_path());

require_once("Frd/Frd.php");
define("ROOT_PATH",dirname(__FILE__).'/../');
require_once(ROOT_PATH."/functions.php");

//read config
require_once(ROOT_PATH."/default_setting.php");

if(file_exists(ROOT_PATH."/local/setting.php"))
{
  require_once(ROOT_PATH."/local/setting.php");

  $setting=array_merge($default_setting,$setting);
}
else
{
  $setting=$setting_default;
}


Frd::init($setting);
session_start();

try{
  app()->run();
}
catch(Exception $e)
{
  error_log($e);
  throw $e;
}
