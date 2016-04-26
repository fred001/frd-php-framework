<?php
   require_once("init.php");

   $db=app()->getDb();

   #db query
   $rows=$db->fetchAll("show tables");
   assert(count($rows) > 0);


   $select=$db->select();
   $select->from("blog");
   $rows=$db->fetchAll($select);
   assert(count($rows) > 0);

