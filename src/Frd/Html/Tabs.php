<?php
   /**
   * simple tab ,only css, do not need js
   */
   class Frd_Html_Tabs extends Frd_Object
   {
      protected $style="bootstrap";

      protected $data=array();

      function __construct()
      {
         ;
      }

      /**
      *
      * @params array params or string "active"
      */
      function add($name,$content,$params=array())
      {
         if($params == 'active')
         {
            $params=array('active'=>true);
         }

         if(!isset($params['active']))
         {
            $params['active']=false;
         }


         $item=array(
            'content'=>$content,
            'params'=>$params,
         );

         $this->set($name,$item);
      }


      /** render methods **/
      function renderTitle()
      {
         echo '<ul class="nav nav-tabs">';
            foreach($this->getData() as $name=>$item)
            {
               $content=$item['content'];
               $params=$item['params'];

               if($params['active'] == true)
               {
                  echo '<li class="active"><a data-toggle="tab" href="#tab'.$name.'">'.$content.'</a></li>';
               }
               else
               {
                  echo '<li ><a data-toggle="tab" href="#tab'.$name.'">'.$content.'</a></li>';
               }
            }
            echo '</ul>';

         //content's start tag
         echo '<div class="tab-content">';
         }

         /*
         function render()
         {
            ;
         }
         */

         function start($params=array())
         {
            echo '<div style="margin-bottom: 18px;" class="tabbable">';
            }
            /*
            <div style="margin-bottom: 18px;" class="tabbable">
               */
               function startContent($name)
               {
                  if($this->has($name))
                  {
                     $item=$this->get($name);
                     $params=$item['params'];

                     if($params['active'] == true)
                     {
                        echo '<div id="tab'.$name.'" class="tab-pane active">';
                     }
                     else
                     {

                       echo '<div id="tab'.$name.'" class="tab-pane">';
                      }
                      echo '<div>';
                   }
                }

                  /**
                  * @params string $name, this is only for identifier in html 
                  */
                  function endContent($name)
                  {
                     //if($this->has($name))
                     {
                        echo '</div></div>';
                  }
               }

               function end()
               {
                  echo '</div>'; //content's close div
               echo '</div>';
         }
      }
