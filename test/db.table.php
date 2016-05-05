<?php
   require_once("init.php");

   test_db_build();

   #1
   $blog=new Frd_Db_Table("blog","id");
   $blog->title="test blog";
   $blog->content="test blog content";
   $id=$blog->save();

   assert($id > 0);

   #2
   $blog=new Frd_Db_Table("blog","id");
   $blog->load($id);
   $blog->title="test blog 2";
   assert($blog->save() == true);

   #3
   $blog=new Frd_Db_Table("blog","id");
   $blog->load($id);
   assert($blog->title=="test blog 2");


   $blog=new Frd_Db_Table("blog","id");
   $blog->delete($id);

   $blog=new Frd_Db_Table("blog","id");
   assert($blog->load($id) == false);

   $blog=new Frd_Db_Table("blog","id");
   $blog->title="test blog";
   $blog->content="test content";
   $id=$blog->save();

   $blog=new Frd_Db_Table("blog","id");
   $blog->load($id);
   $data=$blog->getData();

   assert($data['id'] > 0);
   assert($blog->get("notexists","NOTEXISTS") == "NOTEXISTS");

   //insertWhere,updateWhere,deleteWhere,existsWhere
   $blog=new Frd_Db_Table("blog","id");
   $exists=$blog->existsWhere(array("title"=>"test blog"));
   assert($exists == true);

   $blog=new Frd_Db_Table("blog","id");
   $blog->insertWhere(array("title"=>"test blog"),array("title"=>"title"));
   $exists=$blog->existsWhere(array("title"=>"title"));
   assert($exists == true);

   $blog=new Frd_Db_Table("blog","id");
   $blog->updateWhere(array("title"=>"title"),array("title"=>"title2"));
   $exists=$blog->existsWhere(array("title"=>"title2"));
   assert($exists == true);

   $blog=new Frd_Db_Table("blog","id");
   $blog->deleteWhere(array("title"=>"title2"));
   $exists=$blog->existsWhere(array("title"=>"title2"));
   assert($exists == false);



   #
   test_db_clean();
