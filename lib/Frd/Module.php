<?php 
/**
 * module ,each module's main class should extends from this class
 *
 * @version 0.0.1
 * @status  try
 */
require_once(dirname(__FILE__)."/Loader.php");

class Frd_Module
{

  //protected $_folder=''; //the module's folder,used for get other class of the module 
  protected $_name=''; // format: aa/bb/cc

  protected $module_dir='';

  //protected $_depend_modules=array(); //module can only get other module which in this list
  //protected $_module_path=false; //module main file path

  function __construct($module_dir)
  {
    $this->module_dir=$module_dir;
  }

  /*
  function setModuleDir($module_dir)
  {
    $this->module_dir=$module_dir;
  }
   */

  /*
  final function init($folder,$path)
  {
    $this->_name=$folder;

    $this->_folder=realpath(dirname($path));
    $this->_module_path=$path;
  }
   */

  function getTemplatePath($template_path)
  {
    $file=$this->module_dir."/templates/$template_path".".phtml";

    return $file;
  }

   /*
  function getTemplateFolder()
  {
    $file=$this->module_dir."/templates";
    return $file;
  }
  */

  /*
  function getTemplateDir()
  {
    $file=$this->module_dir."/templates";
    return $file;
  }
  */

  /**
   * not classname, because folder path is used for get module
   */
   /*
  function getName()
  {
    return $this->module_dir;
  }
  */

  function getPath($path=false)
  {
    if($path == false)
    {
      return $this->module_dir;
    }
    else
    {
      return $this->module_dir.'/'.ltrim($path,'/');
    }
  }

  /**
   * class are under  Model
   */
  //function getClass($path,$prefix='Model',$class_prefix=false,$params=array())
  protected function getClass($name,$prefix='Model',$params=array())
  {
    //$name=Frd_Loader::pathToClass($name);
    $realpath=Frd_Loader::pathToRealpath($name);
    $name=Frd_Loader::pathToClass($name);

    $file=$this->module_dir."/$prefix/$realpath.php";

    //get class
    require_once($file);

    //add module name for class name
    $class_name=get_class($this);
    $class_name.='_'.$prefix.'_'.$name;

    if(count($params) > 0)
    {
      $reflection = new ReflectionClass($class_name); 
      $class = $reflection->newInstanceArgs($params); 
    }
    else
    {
      $class=new $class_name();
    }


    return $class;
  }

  /**
   * support  get{KEY}  method

   getTable("blog",array())
   */
  function __call($func,$params)
  {
    if(strpos($func,"get") === 0)
    {
      $key=strtolower(substr($func,3));
      $key=ucfirst($key);


      //set module variable
      $name=$params[0];
      array_shift($params);

      //name : Blog, key: Table,$params : array
      $class= $this->getClass($name,$key,$params);
      /*
      if(count($params) >= 2)
      {
         $class= $this->getClass($params[0],$key,$key,$params[1]);
      }
      else
      {
         $class= $this->getClass($params[0],$key,$key);
      }
      */

      //$class->_module=$this;

      return $class;
    }
    else
    {
      throw new Exception("UNKNOWN MODULE  METHOD: ".$func);
    }
  }


  function getTable($name)
  {
     $file=$this->module_dir."/Table/".ucfirst($name).".php";

     if( file_exists($file) == false)
     {
        return new Frd_Db_Table($name);
     }
     else
     {
        return $this->getClass($name,"Table");
     }
  }


  function getLayout($path,$params=array())
  {
    $class= $this->getClass($path,"Layout",$params);
    $class->_module=$this;
    $class->setBasePath($this->_folder."/templates/");

    return $class;
  }


  /*
  protected function loadConfig($path)
  {
    $config_path=$this->getpath()."/config/$path".".php";

    if( file_exists($config_path) == true)
    {
      require_once($config_path);
      return $config;
    }
    else
    {
      return false;
    }
  }
  */

  //============depened module ====================
  /*
  function getDependModule()
  {
    if(!isset($this->_depend_modules[$name]))
    {
      //error,not in depend
      //throw new Exception("module get other module not depend:".$this->getName().' -> '.$name);
      throw new Exception("module get other module not depend:".$this->module_dir.' -> '.$name);
    }

    if( $this->_depend_modules[$name] == null )
    {
      $this->_depend_modules[$name] = Frd::getModule($name);
    }

    return $this->_depend_modules[$name] ;
  }
  */


  function runController($controller)
  {
    //$_module=$this;
    //$_controller=$controller;

    app()->setGlobal("_module",$this);
    app()->setGlobal("_controller",$controller);

    $path=$this->getPath("controller/$controller.php");
    if(file_exists($path) == false)
    {
      throw new Exception("CONTROLLER NOT EXISTS:".$path);
    }

    require_once($path);
  }

  function render($path,$vars=array())
  {
    $template=new Frd_Template();

    $dir=$this->module_dir."/templates";
    $template->setDir($dir);
    $template->assign($vars);
    $template->setPath($path);

    return $template->render();
  }

  /*
  function renderWithLayout($layout_path,$path,$vars=array())
  {
    $layout=new Frd_Template();
    $layout->setDir($this->getTemplateDir());
    $layout->assign($vars);
    $layout->setPath("layout/".$layout_path);

    $layout->template_content=$path;


    $template=new Frd_Template();
    $template->setDir($this->getTemplateDir());
    $template->assign($vars);
    $template->setPath($path);
    $layout->content=$template->render();

    return $layout->render();
  }
  */
}
