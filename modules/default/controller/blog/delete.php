<?php
   $layout=$_module->getLayout("bootstrap");

   $id=$_GET['id'];
   $blog=$_module->getTable("blog");
   $blog->delete($id);

   header("location:".url("blog/list"));

