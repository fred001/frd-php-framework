<?php
   abstract class Frd_Paginator_Abstract
   {
      function __construct()
      {
      }

      function setParams($params)
      {
         $this->page= $params['page'];
         $this->perpage= $params['perpage'];
         $this->sortname= $params['sortname'];
         $this->sortorder= $params['sortorder'];
         $this->qtype=$params['qtype'];
         $this->qvalue=$params['qvalue'];

         $this->params=$params;
      }

      abstract function getItems();
      abstract function getItemTotal();
   }
