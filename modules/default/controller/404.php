<?php
   $module=getModule();
   $layout=$module->getLayout("basic");

   $layout->content=$module->render("404");

   echo $layout->render();
