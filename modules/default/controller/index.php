<?php
$layout=$_module->getLayout("basic");
$layout->content=$_module->render("index",array(
));

echo $layout->render();

