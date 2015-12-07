<?php
   /**
   * widget:
   *  widget is an object, which have it's own html,css,javascript,php code
   *  and the css,html ,js can output respectively ( this will for layout or block to handle in the future
   *   widget is for extends, so the subclass can add some custom methods 
   1, Frd_Form should support widget
   2, support auto methods to bind with it's object
   $widget->addElememnt('input',$input);

   then:
   $widget->setValue('input','test');

   */
   class Frd_Widget extends Frd_Object
   {
      protected $_name='';
      protected $_html=array();
      protected $_js=array();
      protected $_css=array();

      protected $_widgets=array(); //children widgets

      protected $_template=false; //widget template

      protected $_form=null; //form code

      protected $_js_files=array();
      protected $_css_files=array();

      function __construct()
      {
         //set default layout
         $this->_js['on_ready']=array();
         $this->_js['function']=array();
         $this->setName("widget");
      }

      function setName($name)
      {
         $this->_name=$name;
      }

      function getName()
      {
         return $this->_name;
      }
      function addHtml($content)
      {
         $this->_html[]=$content;
      }

      function addCss($content)
      {
         $this->_css[]=$content;
      }

      function addJsFunction($content)
      {
         $this->_js['function'][]=$content;
      }

      function loadJsFunction()
      {
         ;
      }

      function addJsOnReady($content)
      {
         $content=rtrim($content,";").";";

         $this->_js['on_ready'][]=$content;
      }

      //get layout contents
      function getJsFunction()
      {
         $js='';
         $rows=$this->_js['function'];
         foreach($rows as $row)
         {
            $js.=$this->_getContent($row);
            $js.="\n";
         }

         //add widget js
         $widgets=$this->getWidgets();
         foreach($widgets as $widget)
         {
            $js.="/** ".$widget->getName()." **/";
            $js.="\n";
            $js.=$widget->getJsFunction();
            $js.="\n";
         }

         return $js;
      }

      function getJsOnReady()
      {
         $js='';
         $rows=$this->_js['on_ready'];
         foreach($rows as $row)
         {
            $js.=$this->_getContent($row);
            $js.="\n";
         }

         //add widget js
         $widgets=$this->getWidgets();
         foreach($widgets as $widget)
         {
            $js.="/** ".$widget->getName()." **/";
            $js.="\n";
            $js.=$widget->getJsOnReady();
            $js.="\n";
         }

         return $js;
      }

      function getHtml()
      {
         $html='';
         $rows=$this->_html;
         foreach($rows as $row)
         {
            $html.=$this->_getContent($row);
            $html.="\n";
         }

         return $html;
      }

      function getCss()
      {
         $css='';
         $rows=$this->_css;
         foreach($rows as $row)
         {
            $css.=$this->_getContent($row);
            $css.="\n";
         }

         //add widget css
         $widgets=$this->getWidgets();
         foreach($widgets as $widget)
         {
            $css.="/** ".$widget->getName()." **/";
            $css.="\n";
            $css.=$widget->getCss();
            $css.="\n";
         }

         return $css;
      }



      function setTemplate($path)
      {
         $this->_template=$path;
      }

      /**
      * render in layout 
      */
      function render()
      {
         $this->set("html",$this->getHtml());
         $this->set("css",$this->getCss());
         $this->set("js_function",$this->getJsFunction());
         $this->set("js_on_ready",$this->getJsOnReady());

         $data=$this->getData();

         $block=new Frd_Block();

         if($this->_template == false)
         {
            $path=Frd::getFrdTemplatePath().'/widget/default.phtml';
            $block->setTemplate($path);
         }
         else
         {
            $block->setTemplate($this->_template);
         }

         $block->assign($data);

         return $block->render();
      }

      /**
      * render independently
      */
      function renderSingle()
      {
         $this->set("html",$this->getHtml());
         $this->set("css",$this->getCss());
         $this->set("js_function",$this->getJsFunction());
         $this->set("js_on_ready",$this->getJsOnReady());

         $data=$this->getData();

         $block=new Frd_Block();

         if($this->_template == false)
         {
            $path=Frd::getFrdTemplatePath().'/widget/default_single.phtml';
            $block->setTemplate($path);
         }
         else
         {
            $block->setTemplate($this->_template);
         }

         $block->assign($data);

         return $block->render();
      }


      function __toString()
      {
         return $this->render();
      }

      //check if widget enable
      function enableInHtml($name,$name2)
      {
         ;
      }

      function enableInLayout($name,$name2)
      {
         ;

      }

      function isEnable()
      {
         Frd::getHtml()->getName();
         Frd::getlayout()->getName();

         //Frd::getForm()->getName();
      }


      /*** children widgets ***/
      function addWidget($widget)
      {

         $this->_widgets[$widget->getName()]=$widget;
      }

      function removeWidget()
      {
         if(isset($this->_widgets[$widget_name]))
         {
            unset($this->_widgets[$widget_name]);
         }

      }

      function getWidgets()
      {
         return $this->_widgets;

      }


      /** render methods **/
      function renderContent()
      {
         ;

      }

      function renderTemplate()
      {
         ;
      }

      function renderBlock()
      {
         ;

      }

      /**
      * get content 
      *  from string
      *  from block
      *  from file, and render it
      */
      function _getContent($object)
      {
         if(file_exists($object) )
         {
            $block=new Frd_Block();
            $block->setTemplate($object);
            $block->assign($this->getData());
            return $block->render();

         }
         else if(is_object($object) && method_exists($object,"render"))
         {
            return $object->render();
         }
         else if(is_string($object))
         {
            return $object;
         }
         else
         {
            return $object;
         }
      }

      function getJsFiles()
      {
         $scripts='';
         $rows=$this->_js_files;
         foreach($rows as $row)
         {
            //path to url
            $row=Frd::getUrl($row);

            $row='<script type="text/javascript" src="'.$row.'"></script>';
            $scripts.=$row;
            $scripts.="\n";
         }

         //add widget js
         $widgets=$this->getWidgets();
         foreach($widgets as $widget)
         {
            $scripts.="<!-- ".$widget->getName()." -->";
            $scripts.="\n";
            $scripts.=$widget->getJsFiles();
            $scripts.="\n";
         }

         return $scripts;
      }

      function getCssFiles()
      {
         $links='';
         $rows=$this->_css_files;
         foreach($rows as $row)
         {
            $row='<link href="'.$row.'" media="screen" rel="stylesheet" type="text/css" />';
            $links.=$row;
            $links.="\n";
         }

         //add widget js
         $widgets=$this->getWidgets();
         foreach($widgets as $widget)
         {
            $links.="<!-- ".$widget->getName()." -->";
            $links.="\n";
            $links.=$widget->getCssFiles();
            $links.="\n";
         }

         return $links;
      }
   }
