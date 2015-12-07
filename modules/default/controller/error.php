<?php
$layout=$_module->getLayout("basic");
$layout->content=$_module->render("error");

echo $layout->render();
