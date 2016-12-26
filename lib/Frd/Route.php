<?php
   class Frd_Route
   {

      /*
      format:

      array(
         array(
            'pattern'=>$pattern,
            'params_rewrite'=>$params_rewrite,
         )
         array(
            'pattern'=>$pattern,
            'params_rewrite'=>$params_rewrite,
         )
      )
      */
      protected $rules=array();
      protected $controller_dir=".";

      function __construct()
      {
         /*
         $this->addRule("/",array("controller"=>"index"));
         $this->addRule("/:controller");
         $this->addRule("/:controller-1/:controller-2");
         $this->addRule("/:controller-1/:controller-2/:controller-3");
         $this->addRule("/:controller-1/:controller-2/:controller-3/:controller-4");
         $this->addRule("/:controller-1/:controller-2/:controller-3/:controller-4/:controller-5");
         */
      }


      function setControllerDir($controller_dir)
      {
         $this->controller_dir=rtrim($controller_dir,"/");
      }

      function addRule($pattern,$params_rewrite=array())
      {
         //$pattern=rtrim($pattern,"/");
         //$pattern='/'.str_replace("/","\/",$pattern).'/';

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
            $params=array();
            $query=array();


            $pattern=$rule['pattern'];
            $params_rewrite=$rule['params_rewrite'];
            $match=array();

            if(($ret=preg_match($pattern,$path,$match)) == false)
            {
               //echo 'aa';
               continue;
            }

            //var_dump($ret);
            //var_dump($pattern);
            //var_dump($path);
            //var_dump($match);
            //exit();

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


            /*
            if(!isset($params['controller']))
            {
               $controller="";
               $controller_prefix="controller-";
               foreach($params as $k=>$v)
               {
                  if(substr($k,0,strlen($controller_prefix)) === $controller_prefix)
                  {
                     $controller.=$v."/";
                     unset($params[$k]);
                  }
               }

               $params['controller']=trim($controller,"/");
            }
            */

            if(!isset($params['controller']))
            {
               continue;
            }
            //var_dump($params);exit();
            //check controller path
            $controller_path=$this->controller_dir."/".$params['controller'].".php";
            //var_dump($controller_path);
            //exit();
            if(file_exists($controller_path) == false)
            {
               throw new Exception("CONTROLLER_NOT_EXISTS:$controller_path");
               //continue;
            }


            return $params;
         }


         return false;
      }

      function rewrite_old($path)
      {
         $rules=array_reverse($this->rules);

         //var_dump($rules);exit();
         foreach($rules as $rule)
         {
            $params=array();
            $query=array();

            $pattern=$rule['pattern'];
            //var_dump($pattern);
            $params_rewrite=$rule['params_rewrite'];

            $path_parts=explode("/",$path);
            $path_part_count=count($path_parts);

            //1, 比较 字段数量， rule 不能超过 path
            $rule_parts=explode("/",$pattern);
            $rule_part_count=count($rule_parts);

            if($rule_part_count > $path_part_count) continue;

            //2 多余part 是query
            if($path_part_count > $rule_part_count)
            {
               $query_parts=array_slice($path_parts, $rule_part_count, NULL );

               $count=count($query_parts);
               //var_dump($count);
               //var_dump($query_parts);exit();
               for($i=0;$i<$count;$i+=2)
               {
                  if(!isset($query_parts[$i+1]))
                  {
                     $query[$query_parts[$i]]='';
                  }
                  else
                  {
                     $query[$query_parts[$i]]=$query_parts[$i+1];
                  }
               }
            }

            //3 比较pattern ,path,是否匹配
            for($i=0;$i<$rule_part_count;$i++)
            {
               $path_part=$path_parts[$i];
               $rule_part=$rule_parts[$i];

               if($rule_part != $path_part)
               {
                  if(strlen($rule_part) > 0 && $rule_part[0] != ':')
                  {
                     continue;
                  }
                  else
                  {
                     $params[substr($rule_part,1)]=$path_part;
                  }
               }

               //var_dump($path_part);
               //var_dump($rule_part);
            }

            foreach($params_rewrite as $k=>$v)
            {
               $params[$k]=$v;
            }

            foreach($query as $k=>$v)
            {
               $params[$k]=$v;
            }

            //$params['query']=$query;
            //merge controllers
            if(!isset($params['controller']))
            {
               $controller="";
               $controller_prefix="controller-";
               foreach($params as $k=>$v)
               {
                  if(substr($k,0,strlen($controller_prefix)) === $controller_prefix)
                  {
                     $controller.=$v."/";
                     unset($params[$k]);
                  }
               }

               $params['controller']=trim($controller,"/");
            }

            //var_dump($params);exit();
            //check controller path
            $controller_path=$this->controller_dir."/".$params['controller'].".php";
            //var_dump($controller_path);
            //exit();
            if(file_exists($controller_path) == false)
            {
               continue;
            }


            return $params;
         }


         return false;
      }
   }

   class Frd_Route2
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

      /*
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
      */

      /*
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
      */

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
            //echo $pattern;
            //echo "\n";
            //echo $path;
            if(($ret=preg_match($pattern,$path,$match)) == false)
            {
               //echo 'aa';
               continue;
            }

            //var_dump($ret);
            //var_dump($pattern);
            //var_dump($path);
            //var_dump($match);
            //exit();

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

            //var_dump($params);exit();

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

      protected function validPath($path) 
      {
         //rule :  MODULE/CONTROLLER
         if($path == false) return false;

         $parts=explode("/",$path);

         $modules=array();
         $controllers=array();

         //
         $app=Frd::$app;

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
                        array_pop($controllers);
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
            //var_dump($controllers);exit();
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
