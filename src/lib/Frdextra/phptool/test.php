<?php
   /**
   * only for test tools.php ,do not include and use it
   */

   require_once(dirname(__FILE__).'/tools.php');

   Frd_Start();

   Frd_Tool_Use("create_thumbnail",array(
      'path_image'=>'/home/frd/picture/23030137.jpg',
      'path_thumbnail'=>'/home/frd/1.jpg',
      //'path_thumbnail'=>'/1.jpg',
      'w'=>100,
      'h'=>100
   ));

   Frd_End();
