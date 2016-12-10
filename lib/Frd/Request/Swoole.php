<?php
   class Frd_Request_Swoole
   {
      //var_dump($request->get);
      //var_dump($request->post);
      //var_dump($request->cookie);
      //var_dump($request->files);
      //var_dump($request->header);

      //var_dump($request->server);

      public $request=null;

      function __construct($request)
      {
         $this->request=$request;
      }

      function getGet($name,$default='')
      {
         return value_get($this->request->get,strtolower($name),$default);
      }

      function getPost($name,$default='')
      {
         return value_get($this->request->post,strtolower($name),$default);
      }

      function getServer($name,$default='')
      {
         return value_get($this->request->server,strtolower($name),$default);
      }

   }
