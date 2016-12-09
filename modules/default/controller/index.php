<?php

   $table=new Frd_Db_Table("message");
   $api=new Frd_Api($table);

   //$data=$api->find_all();

   $params=array('page'=>1,'page_count'=>2);
   $data=$api->find($params);


   print_r($data);

   echo 'aa';exit();



$layout=getModule()->getLayout("basic");
$layout->content=getModule()->render("index",array(
));

echo $layout->render();

