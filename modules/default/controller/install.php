<?php


   if($_SERVER['REQUEST_METHOD'] == "POST")
   {

      $content=<<<PHP
<?php 
   \$setting=array(
      //Zend Db Adapter Params
      'dbs'=>array(
         'default'=>array(
            'disable'=>0, //optional, if not exists,is enable
            'adapter' => "{$_POST['adapter']}",
            'host' => "{$_POST['host']}",
            'dbname' => "{$_POST['dbname']}",
            'username' => "{$_POST['username']}",
            'password' => "{$_POST['password']}",
         )
      ),
   );

PHP;

if(file_exists(ROOT_PATH."/local") == false)
{
   mkdir(ROOT_PATH."/local");
}
file_put_contents(ROOT_PATH."/local/setting.php",$content);

   }
   else 
   {

      $layout=getModule()->getLayout("basic");
      $layout->content=getModule()->render("install",array());

      echo $layout->render();

   }
