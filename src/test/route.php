<?php
   require_once("init.php");
   //本测试依赖example项目的目录结构 module/default/controller/blog.php

   // 1
   $route=new Frd_Route();
   $route->addRule("/\/blog\/(\d+)/",array(
      "path"=>"default/blog",
      "id"=>":1",
   ));

   $expect=array(
      "path"=> "default/blog",
      "id"=>1,
      "module"=>"default",
      "controller"=>"blog",
      "query"=>array()
   );

   $result=$route->rewrite("/blog/1");
   assert($result == $expect);


   // 2
   $route=new Frd_Route();
   $route->addRule("/\/blog\/(\d+)/",array(
      "path"=>"default/blog/id/:1",
   ));

   $expect=array(
      "path"=> "default/blog/id/1",
      "module"=>"default",
      "controller"=>"blog",
      "query"=>array("id"=>1)
   );

   $result=$route->rewrite("/blog/1");
   assert($result == $expect);



   //3
   $route=new Frd_Route();
   $route->addRule("/(.*)/",array(
      'path'=>":0",
   ));


   $result=$route->rewrite("default/blog");
   $expect=array(
      "path"=> "default/blog",
      "module"=>"default",
      "controller"=>"blog",
      "query"=>array(),
   );

   assert($result == $expect);

   //4
   $result=$route->rewrite("default/blog/id/1");
   $expect=array(
      "path"=> "default/blog/id/1",
      "module"=>"default",
      "controller"=>"blog",
      "query"=>array("id"=>1),
   );
   assert($result == $expect);


   //echo "success";
