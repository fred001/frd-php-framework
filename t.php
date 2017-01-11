<?php
   function ase_encrypt($data)
   {
      $privateKey = '1234567890123456';      
      $iv = '1234567890123456';      

      //加密
      $encrypted=mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$privateKey,$data,MCRYPT_MODE_CBC,$iv);
      //echo $encrypted;
      echo  base64_encode($encrypted);
   }

   //w6IbxUAapGDFaE0r9KXUBA==


   //解密
   function aes_decrypt($data)
   {

      $privateKey = '1234567890123456';      
      $iv = '1234567890123456';      

      $encryptedData=base64_decode($data);
      $decrypted=mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$privateKey,$encryptedData,MCRYPT_MODE_CBC,$iv);
      //解密出来的数据后面会出现如图所示的六个红点；这句代码可以处理掉，从而不影响进一步的数据操作
      $decrypted=rtrim($decrypted,"\0");

      return $decrypted;
   }

   //$data="hello world";
   //$data="w6IbxUAapGDFaE0r9KXUBA==";
   //echo decrypt($data);
