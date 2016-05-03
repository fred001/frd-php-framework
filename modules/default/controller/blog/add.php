<?php
   $layout=$_module->getLayout("bootstrap");


   if(count($_POST) > 0)
   {
      //for form submit

      $blog=$_module->getTable("blog");
      $blog->title=$_POST['title'];
      $blog->content=$_POST['content'];
      $id=$blog->save();

      if($id > 0)
      {
         //success added , redirect to list page
         header("location:".url("blog/list"));
      }
      else
      {
         //add blog failed, must have error
         echo "unknown error";exit();
      }
   }
   else
   {

      $layout->content=$_module->render("blog/add",array(
      ));

      echo $layout->render();

   }
