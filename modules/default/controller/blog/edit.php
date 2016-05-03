<?php
   $layout=$_module->getLayout("bootstrap");


   if(count($_POST) > 0)
   {
      //for form submit
      $id=$_POST['id'];

      $blog=$_module->getTable("blog");
      if( $blog->load($id) )
      {
         $blog->title=$_POST['title'];
         $blog->content=$_POST['content'];
         if($blog->save())
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
         echo "blog not exists: (id:$id)";
         exit();
      }

   }
   else
   {
      $id=$_GET['id'];
      $blog=$_module->getTable("blog");
      $blog->load($id);

      $layout->content=$_module->render("blog/edit",array(
         "data"=>$blog->getData(),
      ));

      echo $layout->render();

   }
