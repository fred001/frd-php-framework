<?php

$default_setting=array(
  'baseurl'=>'/',
  'module.dir'=>ROOT_PATH.'/modules',
  'module.default'=>'default',
  'module.controller.default'=>'index',

  //from common to special
  //when parse, will reverse current order
  //this is for  use method add rule more eash
  //app()->getRoute()->add(....) will add to end
  'routes'=>array(
    "/(.*)/"=>array(
      'path'=>":0",
    ),
  ),

  //Zend Db Adapter Params
  'dbs'=>array(
    'default'=>array(
      'disable'=>1, //optional, if not exists,is enable
      'adapter' => 'pdo_mysql',
      'host' => "localhost",
      'dbname' => "",
      'username' => "",
      'password' => "",
    )
  ),

);
