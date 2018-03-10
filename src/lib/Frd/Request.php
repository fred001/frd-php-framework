<?php
   class Frd_Request
   {
      //var_dump($request->get);
      //var_dump($request->post);
      //var_dump($request->cookie);
      //var_dump($request->files);
      //var_dump($request->header);

      //var_dump($request->server);

      function getGet($name,$default='')
      {
         return value_get($_GET,$name,$default);
      }

      function getPost($name,$default='')
      {
         return value_get($_POST,$name,$default);
      }

      function getServer($name,$default='')
      {
         return value_get($_SERVER,$name,$default);
      }

   }
