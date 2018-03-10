<?php
   require_once("init.php");

   #template
   $t=new Frd_Template();
   $path=TEST_DATA_PATH."/hello.phtml";

   $output=$t->render($path);
   $output=filter_template_output($output);
   assert($output == "hello");

   $t->assign("name","frd framework");
   $output=$t->render($path);
   $output=filter_template_output($output);
   assert($output == "hello frd framework");


   #subtemplate
   $t=new Frd_Template();
   $path=TEST_DATA_PATH."/main.phtml";
   $output=$t->render($path);
   $output=filter_template_output($output);
   assert($output == "<h1>Frd Framework</h1>hello frd framework");

   //echo "success";

   function filter_template_output($string)
   {
      return trim(str_replace("\n","",$string));
   }
