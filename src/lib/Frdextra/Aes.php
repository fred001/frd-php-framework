<?php

   /*
   $msg="hello world";

   echo "Origin:".$msg;
   echo "\n";

   $msg=  Aes::encrypt($msg);

   echo "Encrypt:".$msg;
   echo "\n";
   echo "Descrypt:".Aes::decrypt($msg);;
   echo "\n";
   */
   class Aes
   {
      public static function encrypt($content)
      {
         $key = '1234567890123456';      
         $padkey = self::pad2Length($key,16);      
         $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');      
         $iv_size = mcrypt_enc_get_iv_size($cipher);      
         $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); #IV自动生成？      
         //echo '自动生成iv的长度:'.strlen($iv).'位:'.bin2hex($iv).'<br>';      

         if (mcrypt_generic_init($cipher, self::pad2Length($key,16), $iv) != -1)      
         {      
            // PHP pads with NULL bytes if $content is not a multiple of the block size..      
            $cipherText = mcrypt_generic($cipher,self::pad2Length($content,16) );      
            mcrypt_generic_deinit($cipher);      
            mcrypt_module_close($cipher);      


            return bin2hex($cipherText);
         }      
      }

      //解密      
      public static function decrypt($cipherText)
      {
         $key = '1234567890123456';      
         $content = 'hello';      
         $padkey = self::pad2Length($key,16);      
         $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');      
         $iv_size = mcrypt_enc_get_iv_size($cipher);      
         $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); #IV自动生成？      

         //$mw = bin2hex($cipherText);      
         $mw=$cipherText;
         $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');      
         if (mcrypt_generic_init($td, $padkey, $iv) != -1)      
         {      
            $p_t = mdecrypt_generic($td, self::hexToStr($mw));      
            mcrypt_generic_deinit($td);      
            mcrypt_module_close($td);      

            $p_t = self::trimEnd($p_t);      
            return $p_t;
            return bin2hex($p_t);
         }      
      }



      //将$text补足$padlen倍数的长度      
      protected static function pad2Length($text, $padlen)
      {
         $len = strlen($text)%$padlen;      
         $res = $text;      
         $span = $padlen-$len;      
         for($i=0; $i<$span; $i++){      
            $res .= chr($span);      
         }      
         return $res;      
      }      

      //将解密后多余的长度去掉(因为在加密的时候 补充长度满足block_size的长度)      
      protected static function trimEnd($text)
      {
         $len = strlen($text);      
         $c = $text[$len-1];      
         if(ord($c) <$len)
         {
            for($i=$len-ord($c); $i<$len; $i++)
            {
               if($text[$i] != $c)
               {
                  return $text;      
               }      
            }      
            return substr($text, 0, $len-ord($c));      
         }      
         return $text;      
      }      

      //16进制的转为2进制字符串      
      protected static function hexToStr($hex)       
      {       
         $bin="";       
         for($i=0; $i<strlen($hex)-1; $i+=2)       
         {      
            $bin.=chr(hexdec($hex[$i].$hex[$i+1]));       
         }      
         return $bin;       
      } 
   }

