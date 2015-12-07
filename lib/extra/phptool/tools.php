<?php
   /**
   * frd min develop tool 

   * 
   * @version 0.1.0
   * @changelog 
   *  0.1.0  change/add functions, use Frd_ prefix
   */
   /*
     stdin = GET, POST, $argu,$argv
     stdout = echo   or  Frd_Echo
     stderr = Frd_Error(...)
     stdlog = Frd_Log(...)
   */
   ini_set('display_errors',0);

   define("FRD_TOOL",true);

   $FRD_ENV=array();
   Frd_Env("VERBOSE",true);

   function Frd_Usage()
   {
      $nr="<br/>";
      if( php_sapi_name() == 'cli' ) { $nr= "\n" ;}

      echo "Frd Tool Usage - Basic Tool ,can use anywhere to help develop $nr";
      echo "Version: 0.1.0 $nr";

      echo "Variables: $nr";
      echo '  $FRD_ENV '.$nr ;

      echo "$nr";

      echo 'Env Configs - set $FRD_ENV to config '.$nr;
      echo "  FRD_START        : false (do not set or change it )  $nr ";
      echo "  IS_CLI_SELF      : is use cli run only self .true of false, (Frd_Start set it)  $nr ";

      echo "  VERBOSE        :  show verbose log or not. (true|false) $nr ";
      echo "  RUN_MODE        : html|cli $nr ";
      echo "  ERROR_FILE_PATH : path for error  $nr ";
      echo "  LOG_FILE_PATH   : path for log $nr ";
      echo "  ERROR_OUTPUT_TARGET   : print target. ( frontend | backend | both ) $nr ";
      echo "  LOG_OUTPUT_TARGET   : print target. ( frontend | backend | both ) $nr ";

      echo "$nr";

      echo "Functions: $nr";
      echo "  Frd_Usage() : print this information $nr";
      echo "  Frd_Env()   :  set/get  config   $nr";
      echo "  Frd_Start() : call it at begin , will init necessary config  $nr";
      echo "  Frd_End()   : call it at end , will update necessary flags   $nr";
      echo "  Frd_Error() : save error message $nr";
      echo "  Frd_Log()   : save log message   $nr";
      echo "  Frd_IS_CLI_SELF()   : is call self in cli (return true or false ) $nr";
      echo "  Frd_Load_File()   : load file, use include_once  $nr";

      echo "$nr";
      echo " Cli Usage: $nr";
      echo "  php tool.php : print usage  $nr";
      echo "  php tool.php test PHP_FILE_PATH: test PHP_FILE_PATH  $nr";

   }

   //this is only for debug ,normal should not use this
   //but also can use it in cli mode
   //for cli and html, print different format
   //usage:  
   // Frd_Echo(VALUE),
   // Frd_Echo(VALUE,'no_output| false ') //do not echo ,only return
   // each var1, if is object, should have __toString method, 
   //
   function Frd_Echo($value,$output=true)
   {
      global $FRD_ENV;

      if(is_string($value) || is_numeric($value))
      {
         $msg=$value;
      }
      else if(is_array($value) )
      {
         $value=str_replace("\n","",print_r($value,true));
         $msg=$value;
      }
      else if(is_object($value) )
      {
         if(method_exists($value,"__toString"))
         {
            $msg=$value->__toString();
         }
      }
      else if(is_bool($value) )
      {
         if($value === true ) 
            $msg="true";
         else  
            $msg="false";
      }
      else
      {
         $msg=$value;
      }

      //if cli, change <br/> to "\n"
      if(isset($FRD_ENV['RUN_MODE']) && $FRD_ENV['RUN_MODE'] == 'cli')
      {
         $msg=str_replace("<br/>","\n",$msg);
      }
      else
      {
         $msg=str_replace("\n","<br/>",$msg);
      }

      //output or return
      // trun == "no_output"! , so use ===
      if($output == false || $output === "no_output" || $output === "no-output" )
      {
         return $msg;
      }
      else
      {
         echo $msg;
      }
   }

   //$FRD_ENV=array();
   //special core function use Uppder as first character
   //usage:  
   // Env $key //get  (null for not exists)
   // Env $key $value //set
   function Frd_Env()
   {
      global $FRD_ENV;

      $nums=func_num_args();
      $args=func_get_args();

      if($nums == 1)
      {
         $k=strtoupper($args[0] );

         if(isset($FRD_ENV[$k]) )
         {
            return $FRD_ENV[$k] ;
         }
         else
         {
            return null;
         }
      }
      else if($nums == 2)
      {
         $k=strtolower($args[0]);
         $v=$args[1];

         $FRD_ENV[$k]=$v;
      }
      else
      {
         Frd_Error(__FUNCTION__,"invalid params",$args);
      }
   }

   function Frd_Start()
   {
      global $FRD_ENV;

      //1, check global virable   $FRD_ENV
      if(!isset($FRD_ENV) || !is_array($FRD_ENV))
      {
         Frd_Error('global varable $FRD_ENV (array) not exists, please set it before call'. __FUNCTION__);
         exit(1);
      }

      //
      $FRD_ENV['FRD_START']= true;
      if(! isset($FRD_ENV['VERBOSE']) )
      {
         $FRD_ENV['VERBOSE']=false;
      }

      $verbose=Frd_Env("VERBOSE");


      //2, init global variable to array
      if(!is_array($FRD_ENV)) $FRD_ENV=array();

      //3, check cli or html mode, and this can config by user
      if(  ! empty($FRD_ENV["RUN_MODE"]) )
      {
         //check invalid value
         if(! is_string($FRD_ENV['RUN_MODE'] )
         || ! in_array($FRD_ENV["RUN_MODE"],array('html','cli')))
         {
            Frd_Error("invalid Frd config  RUN_MODE, should be html or cli");
            exit(1);
         }
      }
      else
      {
         if( php_sapi_name() == 'cli' )
         {
            $FRD_ENV['RUN_MODE']="cli";
         }
         else
         {
            $FRD_ENV['RUN_MODE']="html";
         }
      }
      if($verbose === true) Frd_Log(__FUNCTION__,"start","(after init basic env)");

      if($verbose == true) Frd_Log(__FUNCTION__,"set FRD_ENV RUN_MODE",$FRD_ENV['RUN_MODE']);

      //flag
      //when finished, should set it to true
      $FRD_ENV['RUN_FINISH']=false;

      if($verbose == true) Frd_Log(__FUNCTION__,"set FRD_ENV RUN_FINISH",'false');

      //is cli self
      $FRD_ENV['IS_CLI_SELF'] = Frd_IS_CLI_SELF();


      if($verbose == true) Frd_Log(__FUNCTION__,"set FRD_ENV IS_CLI_SELF",$FRD_ENV['IS_CLI_SELF']);
      if($verbose == true) Frd_Log(__FUNCTION__,"finished");

      //register end function
      register_shutdown_function('Frd_End_Check');
   }

   function Frd_End()
   {
      $verbose=Frd_Env('VERBOSE');

      global $FRD_ENV;

      if($verbose == true) Frd_Log(__FUNCTION__,"start");

      if($verbose == true) Frd_Log(__FUNCTION__,"check FRD_ENV FRD_START");
      if( empty($FRD_ENV['FRD_START']))
      {
         Frd_Error("Frd_End should call after Frd_Start");
      }

      if($verbose == true) Frd_Log(__FUNCTION__,"set FRD_ENV RUN_FINISH",true);
      $FRD_ENV['RUN_FINISH']=true;

      if($verbose == true) Frd_Log(__FUNCTION__,"finished");
   }

   //do not call it by manual
   //it is for shutdown callback function ,to check if has error
   function Frd_End_Check()
   {
      global $FRD_ENV;

      if(!isset($FRD_ENV) || !is_array($FRD_ENV))
      {
         Frd_Error(__FUNCTION__,"seems error happend");
      }

      if(Frd_Env("RUN_FINISH") !== true)
      {
         Frd_Error(__FUNCTION__,"seems error happend");
      }
   }

   //name use Error2, hope not confilct with exists function
   //usage:  
   // Error $msg ...
   //depend error path : $_ENV['ERROR_FILE_PATH']
   function Frd_Error()
   {
      global $FRD_ENV;

      if(!isset($FRD_ENV['Error_OUTPUT_TARGET']) )
      {
         $FRD_ENV['ERROR_OUTPUT_TARGET'] = "frontend";
      }

      $target=Frd_Env("ERROR_OUTPUT_TARGET");
      if($target == "backend" || $target == "both")
      {
         $path=Frd_Env("ERROR_FILE_PATH");
         if($path == false) 
         {
            Frd_Error_Core("FRD_ENV not set","ERROR_FILE_PATH");
            return false;
         }
      }

      $args=func_get_args();

      //$msg='['.date("Y-m-d H:i:s").']';
      $msg='[ERR] ';
      foreach($args as $arg)
      {
         $msg.="  ".Frd_Echo($arg,'no_output');
      }
      $msg.="\n";

      //output type
      $output_target=Frd_Env("ERROR_OUTPUT_TARGET");
      if($output_target == "frontend")
      {
         Frd_Echo($msg);
      }
      else if($output_target == "backend")
      {
         //write
         file_put_contents($path,$msg,FILE_APPEND);
      }
      else if($output_target == "both")
      {
         Frd_Echo($msg);
         file_put_contents($path,$msg,FILE_APPEND);
      }
      else
      {
         Frd_Error_Core("unknown ERROR_OUTPUT_TARGET:",$output_target);
      }
   }

   //last error way, if normal error failed,
   //this  will actually  call php function error_log, 
   //save to httpd server's log
   function Frd_Error_Core($msg)
   {
      error_log($msg);
   }

   //why not Log ? php math funciton "log"  already exists
   // Log2 $msg ...
   function Frd_Log()
   {
      global $FRD_ENV;

      if(!isset($FRD_ENV['LOG_OUTPUT_TARGET']) )
      {
         $FRD_ENV['LOG_OUTPUT_TARGET'] = "frontend";
      }

      $target=Frd_Env("LOG_OUTPUT_TARGET");
      if($target == "backend" || $target == "both")
      {
         $path=Frd_Env("LOG_FILE_PATH");
         if($path == false) 
         {
            Frd_Error_Core("ENV not set","LOG_FILE_PATH");
            return false;
         }
      }


      $args=func_get_args();

      //$msg='['.date("Y-m-d H:i:s").']';
      $msg='[LOG] ';
      foreach($args as $arg)
      {
         $msg.=Frd_Echo($arg,'no_output');
         $msg.="  ";
      }
      $msg.="\n";

      //output type
      $output_target=Frd_Env("LOG_OUTPUT_TARGET");
      if($output_target == "frontend")
      {
         Frd_Echo($msg);
      }
      else if($output_target == "backend")
      {
         //write
         file_put_contents($path,$msg,FILE_APPEND);
      }
      else if($output_target == "both")
      {
         Frd_Echo($msg);
         file_put_contents($path,$msg,FILE_APPEND);
      }
      else
      {
         Frd_Error_Core("unknown LOG_OUTPUT_TARGET:",$output_target);
      }

   }

   //call frd tool functions
   function Frd_Call($func,$params)
   {
   }

   //check if is run as cli self
   function Frd_IS_CLI_SELF()
   {
      global $argv;

      //check if $argv[0] exists, (maybe deleted )
      if(isset($argv) && is_array($argv)  && count($argv) > 0 && isset($argv[0]) )
      {
         $pos=strpos(__FILE__,$argv[0]);

         if($pos !== false && (strlen($argv[0])===(strlen(__FILE__)-$pos)))
         {
            return true;
         }

      }

      return false;
   }

   //load a php file, like require  and include ,but do more check
   function Frd_Load_File($php_file_path)
   {
      if( is_readable($php_file_path) === false)
      {
         Frd_Error(__FUNCTION__,"path not exists or not readable",$php_file_path);
         return false;
      }

      include_once($php_file_path);
   }

   //usage: 
   //*$cmd="ls -l  error.php";
   //*list($stdout,$stderr)= execute_cmd($cmd);
   //*echo $stdout; //cmd stdout result 
   //*var_dump($stderr);  //cmd stderr result, if no error, false
   function Frd_Execute_CMD($cmd)
   {
      $descriptorspec = array(
         0 => array("pipe", "r"),  // stdin
         1 => array("pipe", "w"),  // stdout
         2 => array("pipe", "w"),  // stderr
      );

      //
      $pipes=array();

      $working_path=dirname(__FILE__);
      $env=null;
      $other_options=null;

      $process = proc_open($cmd, $descriptorspec, $pipes, $working_path,$env,$other_options);

      if($process === false)
      {
         $stdout = "execute cmd: failed";
         $stdin ="";
      }
      else
      {
         $stdout = stream_get_contents($pipes[1]);
         fclose($pipes[1]);

         $stderr = stream_get_contents($pipes[2]);
         fclose($pipes[2]);
      }

      proc_close($process);

      //
      if($stderr == false) $stderr = false ;

      return array($stdout,$stderr);
   }

   //use a extra tool,will call shell 
   function Frd_Tool_Use($tool_name,$params=array())
   {
      $tool_base_dir_path=dirname(__FILE__)."/tools";
      $tools=array(
         'create_thumbnail'=>'python '.$tool_base_dir_path.'/create_thumbnail.py $path_image $path_thumbnail $w $h'
      );

      if(!isset($tools[$tool_name]))
      {
         Frd_Last_Error("tool not exists:".$tool_name);
         return false;
      }

      $cmd=$tools[$tool_name];

      //replace params keys
      $param_keys=array();
      $param_values=array();
      foreach($params as $k=>$v)
      {
         $params_keys[]='$'.$k;
         $params_values[]=$v;
      }

      $cmd=str_replace($params_keys,$params_values,$cmd);

      //execute
      list($stdout,$stderr)=Frd_Execute_CMD($cmd);

      if($stderr !== false) 
      {
         Frd_Last_Error($stderr);
         return null;
      }
      else
      {
         return $stdout;
      }
   }

   //Usage: 
   // Frd_last_Error(mix error) //set error
   // Frd_last_Error() //get
   function Frd_Last_Error($error=null)
   {
      if($error == null) 
      {
         return Frd_Env("LAST_ERROR");
      }
      else 
      {
         Frd_Error($error);
         Frd_Env("LAST_ERROR",$error);
      }
   }
   //Usage: 
   // Frd_last_Return(mix error) //set error
   // Frd_last_Return() //get
   function Frd_Last_Return($return)
   {
      if($error == null) 
      return Frd_Env("LAST_RETURN");
      else 
      Frd_Env("LAST_RETURN",$return);
   }

   //1,  catch exception handler , 
   //2, catch error 
   //3, register shutdown , if flag (RUN_FINISH) not true,  means have error
   // should keep set this value true at end of script

   //usage
   //$FRD_ENV['VERBOSE']=true;
   /*
   Frd_Start();

   Frd_End();

   Frd_Log("start");
   Frd_Error("start");

   Frd_Error_Core("Test");

   Frd_Usage();


   Frd_End();
   */

   //php tool.php : print usage
   //php tool.php test PHP_FILE_PATH : test PHP_FILE_PATH
   if( Frd_IS_CLI_SELF()  == true)
   {
      if($argc === 1)
      {
         Frd_Usage();
      }
      else 
      {
         if($argv[1] == 'usage')
         {
            Frd_Usage();
         }
         else if($argv[1] == 'test' && $argc == 3)
         {
            Frd_Start();

            Frd_Load_File($argv[2]);

            Frd_End();
         }
         else
         {
            Frd_Usage();
         }
      }
   }
