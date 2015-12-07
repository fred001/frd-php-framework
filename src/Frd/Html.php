<?php
   /**
   * this is html interface, for create html elements
   * include: message, dialog,button,table, and so on
   *TODO
   * now only supprt bootstrap type, 
   * in the feture should support more style

   * @version 0.0.1
   * @status  try
   */
   class Frd_Html extends Frd_Object
   {
      protected $style="bootstrap";

      function __construct($style="bootstrap")
      {
         $this->style="bootstrap";
      }

      function info($content)
      {
         $html='<div class="alert alert-info">';
         $html.=$content;
         $html.='</div>';

         return $html;
      }

      function warning($content)
      {
         $html='<div class="alert alert-block">';
         $html.=$content;
         $html.='</div>';

         return $html;
      }

      function success($content)
      {
         $html='<div class="alert alert-success">';
         $html.=$content;
         $html.='</div>';

         return $html;
      }

      function error($content)
      {
         $html='<div class="alert alert-error">';
         $html.=$content;
         $html.='</div>';

         return $html;
      }

      /**
      * simple alert 
      */ 
      function alert($content)
      {
         /*
         $html='<div class="modal" id="myModal" style="top:80%"><div class="modal-body"><p>'.$content.'</p></div><div class="modal-footer"><a href="#" class="btn" data-dismiss="modal" >Close</a></div></div>';


         jQuery(html).modal;
         */
      }

      /**
      * create link
      */
      function link($content,$href,$attrs=array())
      {
         $attrs['href']=$href;

         $a=new Frd_Html_Element("a",$attrs,$content);

         return $a;
      }
   }

