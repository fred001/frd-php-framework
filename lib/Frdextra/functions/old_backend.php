<?php
//check if controller exists
function rewrite_controller_exists($module,$controller)
{
  $module_folder=Frd::getSetting('module_path');

  $controller_path=$module_folder.$module.'/controller/'.$controller.".php";
  return file_exists($controller_path);
}
//@notice module最多2层， controller最多3层
function rewrite_url($url)
{
  $module_max_level=2;
  $controller_max_level=3;

  $modules=array();
  $controllers=array();

  $default_module=Frd::getSetting("module_default",'');
  $default_controller=Frd::getSetting("controller_default",'');

  $module_folder=Frd::getSetting('module_path');

  $urls=explode("/",$url);
  if($url == false || count($urls) == 0)  
  {
    if( rewrite_controller_exists($default_module,$default_controller) )
    {
      return array(
        "_module"=>trim($default_module,"/"),
        "_controller"=>trim($default_controller,"/"),
        "_params"=>array(),
      );
    }
  }
  else if(count($urls) == 1)
  {
    if(rewrite_controller_exists($default_module,$urls[0]) )
    {
      return array(
      "_module"=>trim($default_module,"/"),
        "_controller"=>trim($urls[0],"/"),
        "_params"=>array(),
      );

      return $result;
    }

    if(rewrite_controller_exists($urls[0],$default_controller) )
    {
      return array(
        "_module"=>trim($urls[0],"/"),
        "_controller"=>trim($default_controller,"/"),
        "_params"=>array(),
      );

      return $result;
    }
  }
  else if(count($urls) >= 2)
  {
    //echo $module_folder;exit();
    $module='';
    for($i=0;$i<$module_max_level;$i++)
    {
      $module.="/".$urls[$i];

      $module_path=$module_folder."".$module;
      //echo $module_path;exit();
      if(file_exists($module_path))
      {
        $controller="";
        for($j=0;$j<$controller_max_level;$j++)
        {
          if(isset($urls[$i+$j+1]))
          {
            $controller.="/".$urls[$i+$j+1];

            if(rewrite_controller_exists($module,$controller))
            {
              //get param part (after controller part)
              $param_part=array_slice($urls,$i+$j+2);
              if($param_part == false)
              {
                $_params=array();
              }
              else
              {
                //$_params=rewrite_params(explode("/",$param_part));
                $_params=rewrite_params($param_part);
              }

              $result=array(
                "_module"=>trim($module,"/"),
                "_controller"=>trim($controller,"/"),
                "_params"=>$_params,
              );

              return $result;
            }
          }
        }
      }

    }
  }

  return false;
}
function regexp_rewrite_url($path,$rules)
{
  $path=trim($path,"/");
    $params=array();

    $rewrite_params=array();
    //
    $params_parsed=array(); //IMPORTANT VARIABLE

    $result=false;
    foreach($rules as $rewrite=>$rewrite_params)
    {
      //1, rewrite to pattern
      $pattern="/:(\w*)/";
      $rewrite=str_replace("/",'\/',$rewrite);
      //echo $rewrite;exit();

      //a , get params list
      $matches=Frd_Regexp::searchAll($rewrite,$pattern);
      //echo $rewrite;
      //echo $pattern;
      //exit();
      //echo $pattern;
      //var_dump($matches);exit();
      //exit();
      //echo $rewrite;
      if(count($matches[0]) > 0)
      {
        $params=($matches[1]);
        //dump($params);exit();

        //b, parse path ,assign to params
        $rewrite=Frd_Regexp::replace($rewrite,$pattern,"([^\/]*)");
        //var_dump($rewrite);exit();
          //$rewrite="(".$rewrite.")";
        //$matches=Frd_Regexp::searchAll($path,$rewrite);
        //$matches=Frd_Regexp::searchAll($path,$rewrite);
        $matches=Frd_Regexp::search($path,$rewrite);
        $count=count($matches)-1;
        //echo $path;
        //echo $rewrite;exit();
        //dumP($count);exit();
        //dumP($matches);exit();

        if($matches == false) continue;
        //dumP($matches);exit();


        for($i=0;$i<$count;$i++)
        {
          $k= $params[$i];
          $v= $matches[$i+1];
          $params_parsed[$k]=$v;
        }

        //echo "<br/>";
        //echo $path;
        //echo "<br/>";
        //echo $matches[0];
        //echo strlen($matches[0]);
        //echo substr($path,11);exit();
          //echo $matches[0];
        $param_part=substr($path,strlen($matches[0]) );
        //var_dump($param_part);exit();
        $_params=rewrite_params(explode("/",trim($param_part,"/")));
        //var_dump($_params);exit();

        $params_parsed=array_merge($_params,$params_parsed);
        $result=true;

        //dump($params_parsed);
        //exit();
        break;
      }
    }

    if( $result == false ) return false;

    //dump($params_parsed);exit();

    //d merge parsed params and rewrite params
    $params=array_merge($params_parsed,$rewrite_params);

    //e ,handle result params,replace :xxx with other key
    foreach($params as $k=>$v)
    {
      if(strpos($v,":") === 0)
      {
        $kk=substr($v,1);
        if(isset($params[$kk]))
        {
          $params[$k]=$params[$kk];
        }
      }
    }
    //handle
    if(isset($params['_module']))
    {
      $module=$params['_module'];
      unset($params['_module']);
    }
    else
    {
      $module=false;
    }

    if(isset($params['_controller']))
    {
      $controller=$params['_controller'];
      unset($params['_controller']);
    }
    else
    {
      $controller=false;
    }


    $result=array(
      "_module"=>trim($module,"/"),
      "_controller"=>trim($controller,"/"),
      "_params"=>$params,
    );

    return $result;
}




function rewrite_params($param_part)
{
  $params=array();

  $count=count($param_part);
  for($i=0;$i<$count;$i+=2)
  {
    $k=$param_part[$i];
    if($k == false)  continue;

    if(isset($param_part[$i+1]))
    {
      $v=$param_part[$i+1];
    }
    else
    {
      $v='';
    }

    $params[$k]=$v;
  }

  return $params;
}
      function compareAssocArray($expect,$data)
      {
         if(is_array($data) == false)
         {
            return false;
         }

         foreach($expect as $k=>$v)
         {
            if(!isset($data[$k]) || $data[$k] != $v)
            {
               return false;
            }
         }

         return true;
      }

 //compare with two multi array
 //the better seems create special object ,
      function compareArraies($expect,$data)
      {
         $result=$expect;
         $row_classes=array();
         foreach($result as $k=>$row)
         {
            $exists=false;
            foreach($data as $kk=>$v)
            {
               //exists 
               if($this->compareAssocArray($row,$v))
               {
                  $exists=true;
                  $row_classes[$k]="green";

                  unset($data[$kk]); //unset this row
                  break;
               }
            }

            if($exists == false)
            {
               $row_classes[$k]="red";
            }

         }

         //merge the reset
         $result=array_merge($result,$data);

         //render
         $table=new Frd_Html_Table(array('class'=>'table table-bordered'));
         foreach($result as $k=>$v)
         {
            if(isset($row_classes[$k]))
            {
               $class=$row_classes[$k];
            }
            else
            {
               $class='gray';
            }

            $table->setRowClass($class);

            foreach($v as $vv)
            {
               $table->col($vv);
            }

            $table->nextRow();
         }

         return $table->render();
      }
/**
 * compare with two multi array
 * the better seems create special object ,
 */
      /*
        function compareAssocArraies($expect,$data)
        {
          if(is_object($expect) )
          {
            $expect=$expect->toArray();
          }

          $result=$expect;
          $row_classes=array();
          foreach($result as $k=>$row)
          {
            $exists=false;
            foreach($data as $kk=>$v)
            {
              //exists 
              if($this->compareAssocArray($row,$v))
              {
                $exists=true;
                $row_classes[$k]="green";

                unset($data[$kk]); //unset this row
                break;
              }
            }

            if($exists == false)
            {
              $row_classes[$k]="red";
            }

          }

          //merge the reset
          $result=array_merge($result,$data);

          //render
          $table=new Frd_Html_Table(array('class'=>'table table-bordered'));
          foreach($result as $k=>$v)
          {
            if(isset($row_classes[$k]))
            {
              $class=$row_classes[$k];
            }
            else
            {
              $class='gray';
            }

            $table->setRowClass($class);

            foreach($v as $vv)
            {
              $table->col($vv);
            }

            $table->nextRow();
          }

          return $table->render();
        }
       */
/*
      public static function getOrignaInfo()
      {
         return $_SERVER['HTTP_USER_AGENT'];  
      }

      public static function detachBrowser()
      {
         $agent= $_SERVER["HTTP_USER_AGENT"];

         if(stripos($agent,"MSIE 8.0")!== false )
            return "Internet Explorer 8.0";
         else if(stripos($agent,"MSIE 7.0") !== false )
            return "Internet Explorer 7.0";
         else if(stripos($agent,"MSIE 6.0") !== false)
            return "Internet Explorer 6.0";
         else if(stripos($agent,"Firefox/") !== false )
            return "Firefox";
         else if(stripos($agent,"Chrome") !== false )
            return "Google Chrome";
         else if(stripos($agent,"Safari") !== false )
            return "Safari";
         else if(stripos($agent,"Opera")  !== false )
            return "Opera";
         else 
            return'unknown';
      }

      public static function detachOs()
      {
         $agent=$_SERVER['HTTP_USER_AGENT'];  //获取客户端信息，赋值给变量

         $os=false;         //初始化$os为false

         if(stripos($agent,'win')!== false  && stripos($agent,'95')!== false )
         {
            //如果结果中含有win和95内容
            //操作系统为win95
            $os='Windows 95';    
         }
         else if(stripos($agent,'win 9x')!== false && stripos($agent,'4.90') != false)
         {
            $os='Windows ME';
         }
         else if(stripos($agent,'win') !== false  && stripos($agent,'98') !== false )
         {
            $os='Windows 98';
         }
         else if(stripos($agent,'win') !== false && stripos($agent,'nt 5.1') !== false )
         {
            $os='Windows XP';
         }
         else if(stripos($agent,'win') !== false && stripos($agent,'nt 5') !== false )
         {
            $os='Windows 2000';
         }
         else if(stripos($agent,'win') !== false  && stripos($agent,'nt 6.1') !== false )
         {
            $os='Windows 7';
         }
         else if(stripos($agent,'win') !== false && stripos($agent,'nt 6') !== false )
         {
            $os='Windows vista';
         }
         else if(stripos($agent,'win') !== false && stripos($agent,'nt') !== false )
         {
            $os='Windows NT';
         }
         else if(stripos($agent,'linux') !== false)
         {
            $os='Linux';
         }
         else if(stripos($agent,'unix') !== false)
         {
            $os='Unix';
         }
         else if(stripos($agent,'Max') !== false && stripos($agent,'PC') !== false )
         {
            $os='Macintosh';
         }
         else
         {
            $os='Unknown';
         }

         return $os;
      }


      public static function getIp()
      {
         // Get client ip address
         if ( isset($_SERVER["REMOTE_ADDR"]))
         $client_ip = $_SERVER["REMOTE_ADDR"];
         else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
         $client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
         else if ( isset($_SERVER["HTTP_CLIENT_IP"]))
         $client_ip = $_SERVER["HTTP_CLIENT_IP"];

         return $client_ip;
      }

 */




/**
 * only replace a string once
 */
/*
function str_replace_once($needle, $replace, $haystack) 
{
   $pos = strpos($haystack, $needle);

   if ($pos === false) 
   {
      return $haystack;
   }

   return substr_replace($haystack, $replace, $pos, strlen($needle));
}
 */
