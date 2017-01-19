<?php
/**
 *  and support  render without set script path first
 *  and it's  subclass can access it's method !
 *  because it has already assign  $this  to  this for template 
 *  so  $this->METHOD  can call the template's method
 *  featura:
 *    support  {VAR} 
 *    support  {VAR.Attr.attr}  the VAR can be object or array 
 * 
 * @version 0.0.1
 * @status  try
 */
//class Frd_Template  extends Frd_Object

require_once("Frd/Regexp.php");


class Frd_Template  
{
  protected $basedir='';

  protected $path=false;
  protected $vars=array();

  protected $allow_parse = true;
  //public $_auto_escape = false; //auto escape or not
  
  function disableParse()
  {
    $this->allow_parse=false;
  }

  function enableParse()
  {
    $this->allow_parse=true;
  }

  function setPath($path)
  {
    $this->path=$path;
  }

  function getPath()
  {
    return $this->path;
  }

  function setDir($basedir)
  {
    $this->basedir=rtrim($basedir,"/");
  }

  function getDir()
  {
    return $this->basedir;
  }



  function assign($k,$v=null)
  {
    if(is_array($k))
    {
      foreach($k as $kk=>$vv)
      {
        $this->vars[$kk]=$vv;
      }
    }
    else
    {
      $this->vars[$k]=$v;
    }
  }

  function clearVars()
  {
    $this->vars=array();
  }

  function __set($key,$value)
  {
    $this->vars[$key]=$value;
  }


  function __get($key)
  {
    if(isset($this->vars[$key]))
    {
      return $this->vars[$key];
    }
    else
    {
      return null;
    }
  }

  function __isset($key)
  {
    return isset($this->vars[$key]);
  }

  function getVars()
  {
    return $this->vars;
    //return $this->getData();
  }


  /**
   * render,  if want add advance tag  {if} {endif} ,like this, 
   * should rewrite _run  method, 
   * compile the template, saved as php file, 
   * then include 
   *
   */
  function render($path=false,$vars=array())
  {
    if($path == false)
    {
      $path=$this->getPath();
    }

    if($path == false)
    {
      throw new Exception("missing path for template:".$path);
    }

    //if not exists ,add default  suffix
    if(strpos($path,".") === false)
    {
      $path.=".phtml";
    }

    //if file not valid, realpath will reutn false
    if(realpath($path) !== false)
    {
      $path=realpath($path);
    }

    if($this->basedir == false)
    {
      $this->basedir =dirname($path);
    }
    else
    {
      //if not absolute path,  try relative path
      if(file_exists($path) == false)
      {
        $path=$this->basedir.'/'.ltrim($path,'/');
      }

    }

    if(file_exists($path) === false)
    {
      throw new Exception("template not exists:".$path);
    }

    if(is_readable($path) === false)
    {
      throw new Exception("template not readable:".$path);
    }

    //assign vars
    /*
    foreach($vars as $k=>$v)
    {
      $this->assign($k,$v);
    }
     */

    //extract($this->getVars());

    /*
    if(count($vars) > 0)  
    {
      extract($vars);
    }
    */

    //speical important variables
    if(isset($this->vars['_module']))
    {
      $_module=$this->vars['_module'];
    }

    ob_start();
    require $path;
    $content=ob_get_clean();
    $content=$this->handle($content);

    return $content;
  }

  function module_render($module_name,$path,$vars=array())
  {
    $module=getModule($module_name);
    $path=$module->getTemplatePath($path);

    $old_base_path=$this->getBasePath(); //after render module, restore the basepath
    $this->setBasePath($module->getTemplateFolder());

    $old_module=@$this->vars['_module'];


    if(file_exists($path) === false)
    {
      throw new Exception("template not exists:".$path);
    }

    if(is_readable($path) === false)
    {
      throw new Exception("template not readable:".$path);
    }

    //assign vars
    foreach($vars as $k=>$v)
    {
      $this->assign($k,$v);
    }

    //extract($this->getVars());

    $this->vars['_module']=$module;

    //speical important variables
    if(isset($this->vars['_module']))
    {
      $_module=$this->vars['_module'];
    }

    ob_start();
    require $path;
    $content=ob_get_clean();
    $content=$this->handle($content);

    $this->setBasePath($old_base_path);

    $this->vars['_module']=$old_module;
    return $content;
  }

  function realpath($path)
  {
    if($path == false)
    {
      $path=$this->getPath();
    }

    if($path == false)
    {
      throw new Exception("missing path for template:".$path);
    }

    //if not exists ,add default  suffix
    if(strpos($path,".") === false)
    {
      $path.=".phtml";
    }

    //if file not valid, realpath will reutn false
    if(realpath($path) !== false)
    {
      $path=realpath($path);
    }

    if($this->basedir == false)
    {
      $this->basedir =dirname($path);
    }
    else
    {
      //if not absolute path,  try relative path
      if(file_exists($path) == false)
      {
        $path=$this->basedir.'/'.ltrim($path,'/');
      }

    }

    if(file_exists($path) === false)
    {
      throw new Exception("template not exists:".$path);
    }

    if(is_readable($path) === false)
    {
      throw new Exception("template not readable:".$path);
    }

    return $path;
  }


  /**
   * this is the last step for render template
   * all php tag in template are executed , 
   * now it got result content
   * this can be rewrited, if you want do more thing
   * current , it is only replace {variable}  to real variable
   *
   */
  protected function handle($content,$vars=array())
  {
    if($this->allow_parse == false)
    {
      return $content;
    }
    //variable format
    $var_format='{%s}';

    //$vars=$this->getVars();
    //$data=$this->getData();
    //$vars=array_merge($data,$vars);

    $vars=array_merge($this->vars,$vars);

    $pattern="{([\w\.]+)}"; //match  a-zA-Z _ .
    $matches=Frd_Regexp::searchAll($content,$pattern);

    if($matches != false)
    {
      $matches=$matches[1];
      foreach($matches as $match)
      {
        if($match == false)
        {
          continue;
        }

        $values=explode(".",$match);

        //var_dump(count($values));
        //var_dump(array_key_exists($match,$vars));exit();

        //if(count($values) == 1 && isset($vars[$match]))
        if(count($values) == 1)
        {
          if(array_key_exists($match,$vars))
          {
            //for variable {variable}
            $search="{".$match."}";
            $replace=$vars[$match];
            if(is_string($replace) || is_numeric($replace) || is_bool($replace) || is_null($replace))
            {
              $content=str_replace($search,$replace,$content);
            }
          }
          else
          {
             //do not replace {VAR} to "" when can not parse
             //other place will need parse this string, like vue
            $search="{".$match."}";
            $replace='';
            //$content=str_replace($search,$replace,$content);
          }
        }
        else
        {
          //for variable {variable.attr.attr}
          $value=$values[0];
          //unset($values[0]);

          //if(!isset($vars[$value])) $vars[$value]='';

          if(isset($vars[$value]))
          //if(array_key_exists($value,$vars))
          {
            $curvalue=$vars[$value]; //current value

            $amount=count($values);
            for($i=1;$i < $amount ; $i++)
            {
              $value=$values[$i];

              //if(is_array($curvalue) && isset($curvalue[$value]))
              if(is_array($curvalue) && array_key_exists($value,$curvalue))
              {
                $curvalue=$curvalue[$value];
              }
              else if(is_object($curvalue) && ( isset($curvalue->$value) || @is_null($curvalue->$value) )  )
                ////special object //may not have the property
                //else if(is_object($curvalue) && property_exists($curvalue,$value)) 
              {
                @$curvalue=$curvalue->$value;
              }
              else
              {
                break;
              }

              //got last value
              if($i == $amount-1)
              {
                $search="{".$match."}";
                $replace=$curvalue;
                if(is_string($replace) || is_numeric($replace) || is_bool($replace) || is_null($replace))
                {
                  $content=str_replace($search,$replace,$content);
                }
              }
            }
          }

        }

      }
    }

    return $content;
  }


  function escape($value)
  {
    return htmlentities($value,ENT_QUOTES,"UTF-8");
  }

  function renderContent($content,$vars=array())
  {
    return $this->handle($content,$vars);
  }

  //function compile($path)
  /*
  function compile()
  {
    $indent="";

    //$content=$this->compileTemplate($path);
    $content=$this->compileTemplate($this->getPath());
    return $content;
  }
*/

  //the path may is subtemplate path of the main template, 
  //so this need path parameter
  /*
  protected function compileTemplate($path,$indent="")
  {
    $realpath=$this->realpath($path);
    $content=file_get_contents($realpath);

    //get render line 
    $pattern='\<\?php +echo +\$this->render\( *"(.+)" *\) *; *\?\>';

    $matches=Frd_Regexp::searchAll($content,$pattern);
    //dump($matches);
    if($matches != false)
    {
      $replaces=$matches[0];
      $render_paths=$matches[1];

      foreach($render_paths as $k=>$render_path)
      {
        $c=$this->compileTemplate($render_path,$indent."\t");

        $content=str_replace($replaces[$k],$c,$content);
      }
    }

    //2, compile other module's template
    $pattern='\<\?php +echo +\$this->module_render\((.+)\); *\?\>';
    //echo $pattern;exit();

    $matches=Frd_Regexp::searchAll($content,$pattern);
    if($matches != false)
    {
      $replaces=$matches[0];

      $count=count($matches[0]);

      for($i=0; $i< $count; $i++)
      {
        $params=explode(",",$matches[1][$i]);
        if(count($params) >= 2 ) 
        {
          $module_name=trim(trim(trim($params[0],"'"),'"'));
          $render_path=trim(trim(trim($params[1],"'"),'"'));

          if(isset($params[2]))
          {
            $render_params=trim(trim(trim($params[2],"'"),'"'));
          }
          else
          {
            $render_params=false;
          }

          //$c=$this->template_compile($render_path,$indent."\t");
          //$c=$this->module_template_compile($module_name,$render_path,$render_params);

          $module=getModule($module_name);
          $c=$module->compile($render_path);

          $c='<?php $_module=$this->getModule("'.$module_name.'"); ?>'."\n"
.$c."\n"
          .'<?php $_module=$this->_module; ?>'."\n";

          $content=str_replace($replaces[$i],$c,$content);
        }
      }
    }


    $content=""
      .$indent."<!--START:$path -->"
      ."\n"
      .$indent.$content
      ."\n"
      .$indent."<!--END:$path -->"
      ."\n";

    return $content;
  }
   */
}
