<?php
   /**
   * form widget
   */
   class Frd_Widget_Form extends Frd_Widget
   {
      /**
      *
      */
      function __construct()
      {
         parent::__construct();

         //$this->_js['on_form_ready']=array();
         $this->_js['on_form_submit']=array();
         $this->_js['on_form_validate']=array();
         $this->_js['on_form_invalid']=array();

      }

      /** methods for form **/
      /*
      function addJsOnFormReady($content)
      {
         $this->_js['on_form_ready'][]=$content;
      }
      */

      function addJsOnFormSubmit($content)
      {
         $this->_js['on_form_submit'][]=$content;
      }

      function getJsOnFormSubmit()
      {
         $js='';
         foreach($this->_js['on_form_submit'] as $js_on_form_submit)
         {
            $js.=$js_on_form_submit;
            $js.="\n";
         }

         return $js;
      }

      /*
      function addJsOnFormValidate($content)
      {
         $this->_js['on_form_validate'][]=$content;
      }

      function addJsOnFormInvalid($content)
      {
         $this->_js['on_form_invalid'][]=$content;
      }
      */

      /** backend method **/
      function filter()
      {
         return $this->getValue();
      }

      function checkValid()
      {
         ;

      }

      /*
      function formSaved()
      {
         ;
      }
      */

   }
