<?php
require_once("functions/functions.php");
require_once("Frd/App.php");
require_once("Frd/Loader.php");
require_once("Frd/Client.php");

class Frd
{
  //flag
  protected static $inited=false;

  //object
  public static $app=null;  //App Object
  protected static $loader=null;  //loader

  //
  public static $baseurl=false;
  public static $module_path='';
  protected static $setting=false;

  //old,should remove
  protected static $current=false;

  public static function getVersion()
  {
    return "20140929-0.0.7";
  }

  //init framework
  public static function init($setting=array(),$app=false)
  {
    if( self::$inited != false)
    {
      return false;
    }

    $timezone=value_eget($setting,"timezone");
    date_default_timezone_set($timezone);


    self::$loader=new Frd_Loader();

    //1 add current lib to include path
    $frd_lib_path=dirname(dirname(__FILE__));
    self::$loader->addPath($frd_lib_path);

    $include_paths=value_get($setting,'include_paths');
    if($include_paths != false)
    {
      self::$loader->addPaths($setting['include_paths']);
      unset($setting['include_paths']);
    }

    self::$loader->autoload();


    //
    if($app === false)
    {
      $app=new Frd_App($setting);
      self::$app=$app;
    }

    $routes=value_get($setting,'routes');

    if($routes != false && is_array($routes))
    {
      $route=self::$app->getRoute();
      foreach($routes as $rule=>$params)
      {
        $route->addRule($rule,$params);
      }
    }



    /*
    if(!isset($setting['baseurl']))
    {
      $setting['baseurl']='/';
    }
    self::$setting= $setting;

     */
    /*

     */

    if(isset($setting['dbs']))
    {
      foreach($setting['dbs'] as $name=>$db_config)
      {
        self::$app->addDb($name,$db_config);
      }
    }

    require_once(dirname(__FILE__)."/functions/functions.php"); 

    //
    self::$inited=true;
  }


  /**
   * load frd lib functions
   * function are used in global  area
   */
/*
  public static function loadInterfaces($name)
  {
    require_once(dirname(__FILE__)."/interfaces/$name.php"); 
  }
*/




  /*
  public static function handlePath($path)
  {
    return rtrim($path,'/');
  }
   */

  public static function getClient()
  {
    $client=self::$app->getGlobal('_client');
    if($client == false)
    {
      $client=new Frd_Client();
      self::$app->setGlobal('_client',$client);
    }

    return $client;
  }

  public static function setCurrent($key,$value)
  {
    if(self::$current == false)
    {
      self::initCurrent();
    }

    return self::$current->set($key,$value);
  }

  public static function getCurrent($key,$default=null)
  {
    $ret= self::$current->get($key,$default);

    return self::$current->get($key,$default);
  }

  protected static function initCurrent()
  {
    self::$current=new Frd_Current();
  }



  public static function getClass()
  {
    $args=func_get_args();

    if($args == false)
    {
      throw new Exception('get class no parameter');
    }

    $str=$args[0];

    $values=explode('/',$str); 

    foreach($values as $k=>$value)
    {
      $values[$k]=ucfirst($value); 
    }

    //class __construct params
    $params=array_slice($args,1);
    $class_name=implode("_",$values);

    $class=self::_getClass($class_name,$params);

    return $class;
  }

  public static function _getClass($class_name,$params=array())
  {
    if(!is_array($params))
    {
      $params=array($params);
    }

    if(count($params) == 0)
    {
      $class=new $class_name();
    }
    else if(count($params) == 1)
    {
      $class=new $class_name($params[0]);
    }
    else if(count($params) == 2)
    {
      $class=new $class_name($params[0],$params[1]);
    }
    else if(count($params) == 3)
    {
      $class=new $class_name($params[0],$params[1],$params[2]);
    }
    else if(count($params) == 4)
    {
      $class=new $class_name($params[0],$params[1],$params[2],$params[3]);
    }
    else if(count($params) == 5)
    {
      $class=new $class_name($params[0],$params[1],$params[2],$params[3],$params[4]);
    }
    else if(count($params) == 6)
    {
      $class=new $class_name($params[0],$params[1],$params[2],$params[3],$params[4],$params[5]);
    }
    else if(count($params) == 7)
    {
      $class=new $class_name($params[0],$params[1],$params[2],$params[3],$params[4],$params[5],$params[6]);
    }
    else if(count($params) == 8)
    {
      $class=new $class_name($params[0],$params[1],$params[2],$params[3],$params[4],$params[5],$params[6],$params[7]);
    }
    else
    {
      throw new Exception("sorry , getClass only support max 8 params");
    }

    return $class;
  }

  /**
   * can be path or url
   */
}
