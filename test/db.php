<?php
   require_once("init.php");

   $db=app()->getDb();

   #db query
   $rows=$db->fetchAll("show tables");
   assert(count($rows) > 0);


   #delete all data
   $db->query("delete from blog");

   #insert
   $db->insert("blog",array(
      'title'=>'test title',
      'content'=>'test content',
   ));

   $id = $db->lastInsertId();

   assert($id > 0);

   #update
   $where = $db->quoteInto('id = ?', $id);
   $db->update("blog",array(
      'title'=>'test title2',
      'content'=>'test content2',
   ),$where);

   #query

   $row=$db->fetchRow("select * from blog where id=$id");

   assert($row['title'] == "test title2");



   $select=$db->select();
   $select->from("blog");
   $rows=$db->fetchAll($select);
   assert(count($rows) > 0);

