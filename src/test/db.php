<?php
   require_once("init.php");

   test_db_build();
   #



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


   $db->query("delete from blog");
   $db->insert("blog",array(
      'title'=>'test title',
      'content'=>'test content',
   ));

   $db->insert("blog",array(
      'title'=>'test title2',
      'content'=>'test content2',
   ));


   $id=$db->fetchOne("select id from blog where title ='test title'");
   assert($id > 0);

   $row=$db->fetchRow("select * from blog where title ='test title'");
   assert($row['id'] > 0);

   $rows=$db->fetchAll("select * from blog where title ='test title'");
   assert($rows[0]['id'] > 0);






   //clean env
   test_db_clean();
