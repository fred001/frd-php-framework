<?php

class Frd_Route
{
  protected $custom_rules=array(); //when rewrite, should do array_reverse, first insert rule firse try rewrite
  protected $custom_rules_params=array(); //when rewrite, should do array_reverse, first insert rule firse try rewrite

  function addRule($rule,$params=array())
  {
    $rule=trim($rule,"/");

    //array_unshift($this->custom_rules,$rule);
    //array_unshift($this->custom_rules_params,$params);
    $this->custom_rules[]=$rule;
    $this->custom_rules_params[]=$params;
  }

  function rewriteByCustom($path)
  {
    if(count($this->custom_rules) == 0) return false;

    $paths=explode("/",$path);

    foreach($this->custom_rules as $rule_index => $rule)
    {
      $success=true;
      $params=array();
      $get_params=array();

      $parts=explode("/",$rule);

      //here path's part should at least < rule's part -1 
      //why -1 ?    if rule is : aa/* , path should at least "aa" , 1 is the "*"
      if(count($paths) < count($parts)-1)
      {
        continue;
      }

      foreach($paths as $k=>$v)
      {
        if($parts[$k] == "*")
        {
          $get_params=$this->paseToGetParam(array_slice($paths,$k));
          break;
        }

        if(!isset($parts[$k]))
        {
          $success=false;
          break;
        }

        if(substr($parts[$k],0,1) == ":")
        {
          $params[substr($parts[$k],1)]=$v;
        }
        else
        {
          if($parts[$k] != $v)
          {
            $success=false;
            break;
          }
        }
      }

      if($success == true)
      {
        $params=array_merge($this->custom_rules_params[$rule_index],$params);
        //handle custom_rules_params , replace :NAME to value
        foreach($params as $k=>$v)
        {
          if(substr($v,0,1) == ":")
          {
            $key=substr($v,1);
            if(isset($params[$key]))
            {
              $params[$k]=$params[$key];
            }
          }
        }

        //merge get_params
        $params=array_merge($params,$get_params);
        
        return $params;
      }
    }

    return false;
  }

  function rewriteByDefault($path)
  {
    //rule :  MODULE/CONTROLLER
    if($path == false) return false;

    $parts=explode("/",$path);

    $modules=array();
    $controllers=array();

    //
    $app=app();

    $controllers=$parts;
    $value=array_shift($controllers);
    $modules[]=$value;

    $success=false;
    while(count($modules) > 0)
    {
      if($app->moduleExists(implode("/",$modules)) && $app->controllerExists($controllers))
      {
        $success=true;
        break;
      }

      if(count($controllers) == 0)
      {
        break;
      }
      else
      {
        $value=array_shift($controllers);
        $modules[]=$value;
      }
    }

    if($success == false)
    {
      return false;
    }
    else
    {
      $params=array(
        'module'=>implode("/",$modules),
        'controller'=>implode("/",$controllers),
      );

      return $params;
    }
  }

  function rewrite($path)
  {
    $path=trim($path,"/");

    $params=$this->rewriteByCustom($path);
    if($params == false)
    {
      $params=$this->rewriteByDefault($path);
    }
  

    if($params == false)
    {
      throw new Exception("UNKNOWN URL:$path");
    }


    return $params;
  }

  //parse array to get params
  //[name,frd,age,11] => name=>frd,age=11
  function paseToGetParam($arr)
  {
    $params=array();

    $max=count($arr);

    $i=0;
    while(true)
    {
      $k=$arr[$i];

      if(isset($arr[$i+1]))
      {
        $v=$arr[$i+1];
      }
      else
      {
        $v=null;
      }

      $params[$k]=$v;

      $i+=2;
      if($i >= $max) break;
    }

    return $params;
  }
}
