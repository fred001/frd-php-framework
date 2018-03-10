<?php
   /**
   * frd's loader
   * 
   * @version 0.0.1
   * @status  try
   */
   require_once(dirname(__FILE__)."/Frd.php");

   class Frd_Loader 
   { 
      protected $include_paths=array();  //should contain same paths as  result of get_include_path()

      function __construct()
      {
         $this->include_paths=explode(PATH_SEPARATOR ,get_include_path());
      }

      //add user's include paths - push it to make search first
      function addPaths($paths)
      {
        $realpaths=array();
        foreach($paths as $path)
        {
          $realpath=realpath($path);
          if( $realpath == false)
          {
            throw new Exception("PATH NOT EXISTS:$path");
          }

          $realpaths[]=$realpath;
        }

        $include_path=implode(PATH_SEPARATOR,$realpaths).PATH_SEPARATOR.get_include_path();
        set_include_path($include_path);

        $this->include_paths=explode(PATH_SEPARATOR ,get_include_path());
      }

      function addPath($path)
      {
        $this->addPaths(array($path));
      }

      //public static function autoload()
      public function autoload()
      {
         spl_autoload_register(array(__CLASS__, 'loadClass'));
      }

      //public static function loadClass($classname)
      public function loadClass($classname)
      {
         $loaded=false;

         $path=str_replace("_","/",$classname);
         $path=str_replace("\\","/",$path); //for namespace class
         $filename=($path.".php");

         //check lib folder
         //$include_paths=explode(PATH_SEPARATOR,get_include_path());
         foreach($this->include_paths as $dir_path)
         {
            $dir_path=realpath($dir_path);
            $path=rtrim($dir_path,"/")."/".$filename;

            if(file_exists($path))
            {
               $loaded=true;
               require_once($path);
               break;
            }
         }

         /*
         if($loaded === false )
         //&& defined('Frd'))
         {
            //check module folder
            $module_folders=array(Frd::$module_path);
            if(!is_array($module_folders)) $module_folders=array();


            $path=str_replace("_","/",$classname);
            //first character to lower, 
            //because module folder not ucfirst
            $path=lcfirst($path);
            $filename=($path.".php");


            foreach($module_folders as $module_folder)
            {
               $path=rtrim($module_folder,"/")."/".$filename;
               if(file_exists($path))
               {
                  $loaded=true;
                  require_once($path);
                  break;
               }
            }

            if($loaded == false)
            {
               $path=str_replace("_","/",$classname);
               $paths=(explode("/",$path));
               if(count($paths) > 2)
               {
                  $paths[0]=lcfirst($paths[0]);
                  $paths[1]=lcfirst($paths[1]);
               }

               $path=implode("/",$paths);

               $filename=($path.".php");

               //try 2 leve deep module folder  , like   xxx/xxx/main.php
               foreach($module_folders as $module_folder)
               {
                  $path=rtrim($module_folder,"/")."/".$filename;
                  if(file_exists($path))
                  {
                     $loaded=true;
                     require_once($path);
                     break;
                  }
               }
            }
         }
         */

         if($loaded == false)
         {
            throw new Exception("LOAD CLASS FAILED:".$classname);
         }
      }


      //aaa/bbb/cCC =>  Aaa_Bbb_CCC
      public static function pathToClass($path)
      {
         // Aa_bb_cc => Aa_Bb_Cc
         $values=explode('_',$path); 

         foreach($values as $k=>$value)
         {
            $values[$k]=ucfirst($value); 
         }

         $path=implode("",$values);

         $values=explode('/',$path); 

         foreach($values as $k=>$value)
         {
            $values[$k]=ucfirst($value); 
         }

         $class_name=implode("_",$values);

         return $class_name;
      }

      //aaa/bbb/cCC =>  Aaa/Bbb/CCC
      public static function pathToRealpath($path)
      {
         // Aa_bb_cc => Aa_Bb_Cc
         $values=explode('_',$path); 

         foreach($values as $k=>$value)
         {
            $values[$k]=ucfirst($value); 
         }

         $path=implode("",$values);


         $values=explode('/',$path); 

         foreach($values as $k=>$value)
         {
            $values[$k]=ucfirst($value); 
         }

         $realpath=implode("/",$values);


         return $realpath;
      }
   }

