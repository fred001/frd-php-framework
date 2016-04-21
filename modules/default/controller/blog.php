<?php
   $layout=$_module->getLayout("bootstrap");

$db=app()->getDb();
$select=$db->select();
$select->from("blog");
$select->order("id desc");

$rows=$db->fetchAll($select);


$layout->content=$_module->render("blog",array(
   'rows'=>$rows,
));

echo $layout->render();

