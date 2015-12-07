<?php
   /**
   * regexp helper ,for easy use regexp 
   */
   class Frd_Regexp
   {
      public static function handlePattern($pattern)
      {
         if(substr($pattern,0,1) != '/')
         {
            $pattern="/".trim($pattern,"/")."/";
         }

         return $pattern;
      }

      public static function replace($string,$pattern,$replace,$limit=-1)
      {
         //$pattern="/".trim($pattern,"/")."/";
         $pattern=self::handlePattern($pattern);

         $string=preg_replace($pattern,$replace,$string,$limit);

         return $string;
      }

      public static function search($string,$pattern)
      {
         //$pattern="/".trim($pattern,"/")."/";
         $pattern=self::handlePattern($pattern);
         $match='';


         if(preg_match($pattern,$string,$match) === false)
         {
            throw new Exception("preg_match falied ");
         }

         return $match;
      }

      public static function searchAll($string,$pattern)
      {
         $match=array();
         //$pattern="/".trim($pattern,"/")."/";
         $pattern=self::handlePattern($pattern);

         if(preg_match_all($pattern,$string,$match) === false)
         {
            throw new Exception("preg_match falied ");
         }

         return $match;
      }

      public static function isMatch($string,$pattern)
      {
         $match='';
         //$pattern="/^$pattern$/";
         //$pattern="/".trim($pattern,"/")."/";
         $pattern=self::handlePattern($pattern);

         if(preg_match($pattern,$string,$match) === false)
         {
            throw new Exception("preg_match falied ");
         }

         if($match == false)
         {
            return false; 
         }
         else
         {
            return true; 
         }
      }
   }
