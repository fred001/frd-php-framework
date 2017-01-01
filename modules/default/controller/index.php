<?php
$layout=getModule()->getLayout("basic");
$layout->content=getModule()->render("index",array(
));

echo $layout->render();

