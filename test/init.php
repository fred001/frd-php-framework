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

  $setting=array_merge($setting_default,$setting);
}
else
{
  $setting=$setting_default;
}


Frd::init($setting);


//extra for test
define("TEST_PATH",dirname(__FILE__));
define("TEST_DATA_PATH",TEST_PATH."/data");
