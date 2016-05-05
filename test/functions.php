<?php
   function test_db_clean()
   {
      $db=app()->getDb();
      $db->query("drop table blog");
   }

   function test_db_build()
   {
      $dbs=app()->getSetting("dbs");
      $db_setting=$dbs['default'];
      $host= $db_setting['host'];
      $username= $db_setting['username'];
      $password= $db_setting['password'];
      $dbname= $db_setting['dbname'];

      if($password)
      {
         $cmd=sprintf("mysql -h %s -u %s -P%s %s %s < data/db.sql",$host,$username,$password,$dbname);
      }
      else
      {
         $cmd=sprintf("mysql -h %s -u %s %s %s < data/db.sql",$host,$username,$password,$dbname);
      }
      execute_cmd("mysql -u root test < data/db.sql");

   }
