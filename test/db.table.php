<?php
   require_once("init.php");

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
