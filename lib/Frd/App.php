<?php
require_once("Zend/Db.php");
require_once("Frd/Route.php");
//require_once("Frd/Object.php");

class Frd_App
{
  //protected $config=array();
  protected $global=array();

  protected $dbs=array();
  protected $modules=array();

  //object
  protected $route=null;
  //protected $request=null;

  //protected $session=null;
  //protected $session_user=null; //session for user

  //settings
  protected $baseurl='';
  protected $module_dir='';
  protected $module_default='';
  protected $module_controller_default='';

  //protected $session_unique_key=false;

  //plugins
  /*
  protected $plugins=array(
    'before_run_controller'=>false,
  );
  */

  function __construct($setting)
  {
    //self::$baseurl=$setting['baseurl'];
    $module_dir=rtrim($setting['module.dir'],'/');

    //check module_dir
    if(file_exists($module_dir) == false)
    {
      throw new Exception("MODULE_DIR NOT EXISTS:$module_dir");
    }

    $this->module_dir= realpath($module_dir);

    $this->baseurl=$setting['baseurl'];

    //optional
    if(isset($setting['module.default']))
    {
      $this->module_default=$setting['module.default'];
    }

    if(isset($setting['module.controller.default']))
    {
      $this->module_controller_default=$setting['module.controller.default'];
    }

    $this->setting=$setting;

    $this->route=new Frd_Route();
    //$this->request=new Frd_Object();
  }

  /*
  function registerPlugin($name,$function)
  {
    if(!isset($this->plugins[$name]))
    {
      throw new Exception("UNKNOWN PLUGIN: $name");
    }

    $this->plugins[$name]=$function;
  }
  */

  function setBaseurl($baseurl)
  {
    $this->baseurl=$baseurl;
  }

  function getSetting($k,$default=null)
  {
     if(isset($this->setting[$k]))
     {
        return $this->setting[$k];
     }
     else
     {
        return $default;
     }
  }

  function getSettings()
  {
     return $this->setting;
  }

  function getRoute()
  {
    return $this->route;
  }


  /**
   * get $global
   *
   * @param  string $key  $global's key
   * @return Object  if $key not false, return $global->$key,else return $global
   */
  public function getGlobal($key,$default=null)
  {
     if(isset($this->global[$key]))
     {
        return $this->global[$key];
     }
     else
     {
        return $default;
     }
  }

  public function hasGlobal($key)
  {
    return isset($this->global[$key]);
  }

  public function setGlobal($key,$value)
  {
    $this->global[$key]=$value;
  }
  //------------------ db methods -------------------//
  public function addDb($name,$config)
  {
    //can not overwrite
    if(isset($this->dbs[$name]) )
    {
      throw new Exception("DB ALREADY EXISTS: $name");
    }

    $adapter=$config['adapter'];
    unset($config['adapter']);

    $db=Zend_Db::factory($adapter,$config);
    $db->setFetchMode(Zend_Db::FETCH_ASSOC);
    $db->query('set names utf8');

    //set as default
    if( $name == 'default')
    {
      Zend_Db_Table::setDefaultAdapter($db);
    }

    $this->dbs[$name]=$db;

    return $db;
  }

  //get db 
  function getDb($name='default')
  {
    //
    if( !isset($this->dbs[$name]))
    {
      throw new Exception('db not exists:'.$name);
    }

    return $this->dbs[$name];
  }

  //module methods
  /**
   * example:
   *   getModule('test','Test','param1','param2','param3'....)
   */
  public function getModule($name)
  {
    $name=trim($name,"/");

    //if has load, return loaded module
    if( isset($this->modules[$name]) )
    {
      return $this->modules[$name];
    }
    else
    {
      //load new
      $module_loaded=false;

      $file_path=$this->module_dir."/".$name."/main.php";

      if(file_exists($file_path) == false)
      {
        throw new Exception("LOAD MODULE FAILED:".$name);
      }

      require_once($file_path);
      $module_loaded=true;

      //init module

      //get module parameters
      $args=func_get_args();
      $params=array_slice($args,2);


      $class_name=$this->getClassNameFromModule($file_path);

      $reflection = new ReflectionClass($class_name); 
      $module = $reflection->newInstanceArgs(array(
        dirname($file_path),
      )); 

      if(count($params) > 0)
      {
        call_user_func_array(array($module,"init"),$params);
      }

      $this->modules[$name]=$module;

      return $module;
    }
  }


  function moduleExists($name)
  {
    $name=trim($name,"/");
    $file_path=$this->module_dir."/".$name."/main.php";


    return file_exists($file_path) ;
  }

  function controllerExists($module_name,$name)
  {
    $module_name=trim($module_name,"/");
    $name=trim($name,"/");

    $path=$this->module_dir."/".$module_name."/controller/$name.php";
    if(file_exists($path) == false)
    {
      $path=$this->module_dir."/".$module_name."/controller/$name"; //check dir
      return file_exists($path) ;
    }
    else
    {
      return true;
    }

  }


  /**
   * search class name from module's main file
   */
  protected function getClassNameFromModule($file_path)
  {
    $string=file_get_contents($file_path);

    $pattern='/class (\S*) *extends *Frd_Module/';

    $ret=Frd_Regexp::search($string,$pattern);
    if(count($ret) == 2)
    {
      $class_name=$ret[1];
    }
    else
    {
      $class_name=false;
    }

    if($class_name == false)
    {
      throw new Exception('can not get module class name from file: '.$file_path );
    }

    return $class_name;
  }
  //config
  /*
  public function setConfig($key,$value)
  {
    $this->config[$key]=$value;
  }

  public function getConfig($key,$default=null)
  {
    if(!isset($this->config[$key]))
    {
      return $default;
    }
    else
    {
      return $this->config[$key];
    }
  }

  public function setConfigs($data)
  {
    $this->config=array_merge($this->config,$data);
  }

  public function getConfigs()
  {
    return $this->config;
  }
  */

  /*** session ***/
  /*
  function startSession()
  {
    session_start();
  }

  function setSessionUniqueKey($key)
  {
    $this->session_unique_key=$key;

    if($key == false)
    {
      $this->session=&$_SESSION;
    }
    else
    {
      $this->session=&$_SESSION[$key];
    }
  }

  function setSession($k,$v)
  {
    $this->session[$k]=$v;
  }

  function getSession($k,$default=null)
  {
    if(isset($this->session[$k]))
    {
      return $this->session[$k];
    }
    else
    {
      return $default;
    }
  }

  function hasSession()
  {
    return isset($this->session[$k]);
  }

  function deleteSession()
  {
    if(isset($this->session[$k]))
    {
      unset($this->session[$k]);
    }
  }

  function clearSession()
  {
    $this->session[$k]=array();
  }

  function &getSessionNamespace($k=false)
  {
    return $this->session[$k];
  }

  function initUserSession()
  {
    if($this->session_user == false)
    {
      $this->session_user=$this->getSessionNamespace("user");
    }
  }

  function setUserSession($k,$v)
  {
    $this->initUserSession();

    $this->session_user[$k]=$v;
  }

  function getUserSession($k,$default=null)
  {
    $this->initUserSession();

    if(isset($this->session_user[$k]))
    {
      return $this->session_user[$k];
    }
    else
    {
      return $default;
    }
  }

  function hasUserSession($k)
  {
    $this->initUserSession();

    return isset($this->session_user[$k]);
  }


  function deleteUserSession($k)
  {
    $this->initUserSession();
    if(isset($this->session_user[$k]))
    {
      unset($this->session_user[$k]);
    }
  }

  function clearUserSession($k)
  {
    $this->initUserSession();

    $this->session_user=array();

  }

  */
  protected function getUrlPath($url_path)
  {
    if($url_path == false)
    {
      if(!empty($_SERVER['PATH_INFO']))
      {
        $url_path=$_SERVER['PATH_INFO'];
      }
      else if(!empty($_SERVER['REQUEST_URI']))
      {
        $url_path=$_SERVER['REQUEST_URI'];
      }
      else
      {
        $url_path="/";
      }
    }

    if(strpos($url_path,$this->baseurl) !== 0)
    {
      throw new Exception("BASEURL NOT MATCH PATH:".$this->baseurl.",".$url_path);
    }

    //drop baseurl part
    $url_path=substr($url_path,strlen($this->baseurl));

    //remove query part
    if($url_path != false)
    {
      if(strpos($url_path,"?") !== false)
      {
        $url_path=substr($url_path,0,strpos($url_path,"?"));
      }
    }

    //
    return $url_path;
  }

  public function run($url_path=false)
  {
    $path=$this->getUrlPath($url_path);

    /*
    url rewrite default:
      1, 弱 path = '' , 使用 default_module + default controller
      2， 使用path 匹配
      3， 使用  default_module+path 匹配
      4， 使用 path + default controller匹配
     */
    $default_module=$this->module_default;
    $default_controller=$this->module_controller_default ;

    if($path == false)
    {
      $path=$default_module.'/'.$default_controller;
    }

    $paths=array(
      $path,
      $default_module."/".trim($path,"/"),
      $path."/".trim($default_controller,"/"),
    );

    //var_dump($paths);exit();
    foreach($paths as $path)
    {
      $params=$this->route->rewrite($path);
      if($params != false)
      {
        break;
      }
    }

    if($params == false)
    {
      throw new Exception("REWRITE URL FAILED");
      //throw new Exception("REWRITE URL FAILED".json_encode($paths));
    }


    //var_dump($params);exit();

    unset($params['path']);
    $module=$params['module'];
    unset($params['module']);
    $controller=$params['controller'];
    unset($params['controller']);

    $_GET=array_merge($params['query'],$_GET); 
    unset($params['query']);

    $_GET=array_merge($params,$_GET); 

    //$this->request->setData($_GET);


    //
    /*
    if($this->plugins["before_run_controller"] != false)
    {
      $function=$this->plugins['before_run_controller'];

      $function($module,$controller);
    }
    */

    //
    $module=$this->getModule($module);
    $module->runController($controller);
  }

  //
  public function url($path,$params=array())
  {
    if($path != false)
    {
      $path=trim($path,"/");
    }
    $path=$this->baseurl.$path;

    $query=http_build_query($params);

    if($query == false)
    {
      return $path;
    }
    else 
    {
      if(strpos($path,"?") !== false)
      {
        return $path."&".$query;
      }
      else
      {
        return $path."?".$query;
      }
    }
  }


  //necessary functions
  /*
  public function pathToUrl($path)
  {
    $basepath=Frd::get('basepath');
    $baseurl=Frd::get('baseurl');

    $ret=strpos($path,$basepath);
    if($ret !== 0)
    {
      trigger_error("incorrect path");
    }

    //convert
    $path=ltrim(substr($path,strlen($basepath)),"/");

    $url=rtrim($baseurl,'/').'/'.$path;

    return $url;
  }

  public function urlToPath($url)
  {
    $basepath=Frd::get('basepath');
    //$baseurl=Frd::get('baseurl');

    $ret=parse_url($url);
    if($ret == false)
    {
      //tigger error
      return false;
    }

    $path=$ret['path'];

    //convert
    $path=$basepath.'/'.ltrim($path,'/');

    return $path;
  }
   */
}
