<?php
class Frd_Route
{
  protected $custom_rules=array(); //when rewrite, should do array_reverse, first insert rule firse try rewrite
  protected $custom_rules_params=array(); //when rewrite, should do array_reverse, first insert rule firse try rewrite

  /*
  function addRule($rule,$params=array())
  {
    $rule=trim($rule,"/");

    //array_unshift($this->custom_rules,$rule);
    //array_unshift($this->custom_rules_params,$params);
    $this->custom_rules[]=$rule;
    $this->custom_rules_params[]=$params;
  }
   */

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

    $success=false;
    while(count($parts) > 0)
    {
      $modules[]=array_shift($parts);

      if($app->moduleExists(implode("/",$modules)))
      {
        $count=count($parts);
        $controllers=array();

        for($i=0;$i<$count;$i++)
        {
          $controllers[]=$parts[$i];

          if($app->controllerExists(implode("/",$modules),implode("/",$controllers)) == false)
          {
            array_pop($controllers);
            break;
          }
        }

        if(count($controllers) > 0)
        {
          $success=true;
          break;
        }
      }

    }

    if($success == false)
    {
      return false;
    }
    else
    {
      foreach($controllers as $controller)
      {
        array_shift($parts);
      }

      //get query
      $query=array();
      $count=count($parts);

      for($i=0;$i<$count;$i+=2)
      {
        if(!isset($parts[$i+1]))
        {
          $query[$parts[$i]]='';
        }
        else
        {
          $query[$parts[$i]]=$parts[$i+1];
        }
      }

      $params=array(
        'module'=>implode("/",$modules),
        'controller'=>implode("/",$controllers),
        'query'=>$query,
      );

      return $params;
    }
  }

  /*
  function rewrite($path)
  {
    $path=trim($path,"/");

    $params=$this->rewriteByCustom($path);
    if($params == false)
    {
      $params=$this->rewriteByDefault($path);
    }
  


    return $params;
  }
   */

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




  protected $rules=array();

  function addRule($pattern,$params_rewrite=array())
  {
    $this->rules[]=array(
      'pattern'=>$pattern,
      'params_rewrite'=>$params_rewrite,
    );
  }

  function rewrite($path)
  {
    $rules=array_reverse($this->rules);
    foreach($rules as $rule)
    {
      $pattern=$rule['pattern'];
      $params_rewrite=$rule['params_rewrite'];

      $match=array();
      if(($ret=preg_match($pattern,$path,$match)) == false)
      {
        continue;
      }

      //var_dump($ret);
      //var_dump($pattern);
      //var_dump($path);
      //var_dump($match);

      $params=array();
      foreach($params_rewrite as $k=>$v)
      {
        $count=count($match);

        //replace :0,:1,... in value
        for($i=0;$i<$count; $i++)
        {
          $v=str_replace(":$i",$match[$i],$v);
        }

        $params[$k]=$v;
      }

      //check path ( module+controller)
      if(!isset($params['path']))
      {
        continue;
      }

      if(($path_params=$this->validPath($params['path'])) == false)
      {
        continue;
      }

      $params=array_merge($params,$path_params);


      return $params;
    }


    return array();
  }

  function validPath($path) 
  {
    //rule :  MODULE/CONTROLLER
    if($path == false) return false;

    $parts=explode("/",$path);

    $modules=array();
    $controllers=array();

    //
    $app=app();

    $success=false;
    while(count($parts) > 0)
    {
      $modules[]=array_shift($parts);

      if($app->moduleExists(implode("/",$modules)))
      {
        $count=count($parts);
        $controllers=array();

        //always try, until success ,and next is failed
        for($i=0;$i<$count;$i++)
        {
          $controllers[]=$parts[$i];


          if($app->controllerExists(implode("/",$modules),implode("/",$controllers)) == false)
          {
            if($success == true)
            {
              break;
            }

            continue;
            //array_pop($controllers);
            //break;
          }
          else
          {
            $success=true;
            //still try next 

            //break;
          }
        }
      }

      if($success == true)
      {
        break;
      }

    }


    if($success == false)
    {
      return false;
    }
    else
    {
      foreach($controllers as $controller)
      {
        array_shift($parts);
      }

      //get query
      $query=array();
      $count=count($parts);
      for($i=0;$i<$count;$i+=2)
      {
        if(!isset($parts[$i+1]))
        {
          $query[$parts[$i]]='';
        }
        else
        {
          $query[$parts[$i]]=$parts[$i+1];
        }
      }

      $params=array(
        'module'=>implode("/",$modules),
        'controller'=>implode("/",$controllers),
        'query'=>$query,
      );

      return $params;
    }
  }
}
