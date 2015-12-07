<?php
$layout=$_module->getLayout("basic");


$layout->content=$_module->render("404");

echo $layout->render();
