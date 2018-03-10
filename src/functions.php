<?php
   function app()
   {
      return Frd::$app;
   }

   function getModule($name="default")
   {
      return app()->getModule($name);
   }

   function url($path,$params=array())
   {
      return app()->url($path,$params);
   }

   function module()
   {
      return app()->getGlobal("_module");
   }

   function controller()
   {
      return app()->getGlobal("_controller");
   }

   //get value from an object or array
   function value_get($data,$k,$default=null)
   {
      if(isset($data[$k]))
      {
         return $data[$k];
      }
      else
      {
         return $default;
      }
   }


   function successResponse($response=array())
   {
      $default =array(
         'error'=>0,
      );

      $response=array_merge($response,$default);
      return json_encode($response);
   }

   function errorResponse($msg,$code=0,$data=array())
   {
      $response= array(
         'error'=>1,
         'error_code'=>$code,
         'error_msg'=>$msg,
         'error_data'=>$data,
      );

      return json_encode($response);
   }

   function failedResponse($msg='',$data=array())
   {
      $response= array(
         'error'=>1,
         'error_code'=>"SYSTEM_ERROR",
         'error_msg'=>$msg,
         'error_data'=>$data,
      );

      return json_encode($response);
   }

