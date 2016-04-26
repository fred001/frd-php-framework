<?php
   require_once("init.php");

   $module=getModule("default");
   assert($module->getPath("controller/blog.php") != false);

   $o=$module->getObject("blog");

   assert(get_class($o) == "Index_Object_Blog");
