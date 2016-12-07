<?php

   $setting_default=array(
      'baseurl'=>'/',
      'module.dir'=>ROOT_PATH.'/modules',
      'module.default'=>'default',
      'module.controller.default'=>'index',

      //from common to special
      //when parse, will reverse current order
      //this is for  use method add rule more eash
      //app()->getRoute()->add(....) will add to end
      'routes'=>array(
         "/\//"=>array(
            'controller'=>"index",
         ),

         "/\/(.*)/"=>array(
            'controller'=>":1",
         ),

         /*
         "/\/([^\/]*)\/(.*)/"=>array(
            'module'=>":1",
            'controller'=>":2",
         ),
         */
      ),

      //Zend Db Adapter Params
      'dbs'=>array(
         'default'=>array(
            'disable'=>1, //optional, if not exists,is enable
            'adapter' => 'pdo_mysql',
            'host' => "localhost",
            'dbname' => "test",
            'username' => "root",
            'password' => "1223",
         )
      ),

      'layout.css'=>array(
         "/resource/package/bootstrap-3.3.2-dist/css/bootstrap.min.css",
         "/resource/css/font-awesome.min.css",
      ),

      'layout.js'=>array(
         "/resource/js/jquery.js",
         "/resource/js/bootstrap.js",
         "/resource/js/jquery.form.js",
      ),

      'website.title'=>"Frd PHP Framework",
   );
