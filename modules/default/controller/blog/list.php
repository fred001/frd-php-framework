<?php
   $layout=$_module->getLayout("bootstrap");

   $db=app()->getDb();
   $select=$db->select();
   $select->from("blog");
   $select->order("id desc");

   $rows=$db->fetchAll($select);


   #render the template   MODULE/templates/blog/list.phtml
   $layout->content=$_module->render("blog/list",array(
      'rows'=>$rows,
   ));

   echo $layout->render();

