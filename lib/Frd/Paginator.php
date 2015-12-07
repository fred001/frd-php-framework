<?php 
   class Frd_Paginator extends Frd_Object
   {
      protected $select=null; //for db paginator

      //reuqest keys
      protected $key_prefix='';

      protected $key_names=array(
         'page'=>'page',
         'perpage'=>'perpage',
         'sortname'=>'sortname',
         'sortorder'=>'sortorder',
         'qtype'=>'qtype',
         'qvalue'=>'qvalue',
      );

      protected $key_alias=array(
      
      );

      function __construct()
      {
         $default=array(
            'page'=>1,
            'perpage'=>false,
            'sortname'=>false,
            'sortorder'=>'asc',
            'qtype'=>false,
            'qvalue'=>false,
            'enable_query' => false,
            'enable_sort' => false,
            'enable_page' => true,
         );

         $this->setData($default);
      }

      function isEnablePage()
      {
         return $this->enable_page;
      }

      function isEnableSort()
      {
         return $this->enable_sort;
      }

      function isEnableQuery()
      {
         return $this->enable_query;
      }


      /**
      * set current page
      */
      function setPage($number)
      {
         $number=intval($number);
         if($number <=0 )
         {
            $number =1;
         }

         $this->set('page',$number);
      }

      function setPageCount($number)
      {
         $number=intval($number);
         if($number <=0 )
         {
            $number =10;
         }

         $this->set('perpage',$number);
      }

      function getPageCount()
      {
         return $this->get('perpage');
      }


      /**
      * set order
      *
      * @param sortname  sort column's name
      * @param sortorder sort order , must be 'asc' or 'desc','asc' is the default value
      *
      */
      function setOrder($sortname,$sortorder='asc')
      {
         $this->sortname=$sortname;

         $sortder=strtolower($sortorder);

         if($sortorder == 'desc')
         {
            $this->sortorder='desc';
         }
         else if($sortorder == 'asc')
         {
            $this->sortorder='asc';
         }
         else
         {
            $this->sortorder='desc';
         }

         $this->enable_sort=true;
      }

      /**
      * set query type and query value
      */
      function setQuery($qtype,$qvalue)
      {
         $this->qtype=$qtype;
         $this->qvalue=$qvalue; 

         $this->enable_query=true;
      }


      /**
      * $data: can be  $_GET, $_POST, $_REQUEST or other 
      */
      function setParams($data)
      {
         if(!is_array($data))
         {
            return false;
         }

         foreach($data as $k=>$v)
         {
            if(isset($this->key_alias[$k]))
            {
               $k=$this->key_alias[$k];
            }

            //build-in params

            if(isset($this->key_names[$k]))
            {
               $value=$this->key_names[$k];

               $this->$value=$v;

               continue;
            }

            //params
            $this->set($k,$v);
            
         }

         foreach($this->key_names as $key=>$key_name)
         {
            $key_name=$this->key_prefix.$key_name;

            if(isset($data[$key_name]))
            {
               $this->$key=$data[$key_name];
            }
         }

            //handle setPage/setPerPage... methods
            if($this->page != false && $this->perpage != false)
            {
               $this->enable_page = true;
            }
            else
            {
               $this->enable_page = false;
            }

            //
            if($this->sortorder != false && $this->sortname != false)
            {
               $this->enable_sort = true;
            }
            else
            {
               $this->enable_sort = false;
            }

            //

            if($this->qtype != false && $this->qvalue != false)
            {
               $this->enable_query = true;
            }
            else
            {
               $this->enable_query = false;
            }

      }

      function setPrefix($prefix)
      {
         $this->key_prefix=rtrim($prefix,"_").'_';
      }

      function setKeyAlias($keyname,$alias)
      {
         $this->key_alias[$alias]=$keyname;
      }

      function getTotal()
      {
         if($this->total !== null)
         {
            return $this->total;
         }

         $this->total=$this->getItemsTotal();

         return $this->total;
      }

      function getItemsTotal()
      {
         if($this->select !== null)
         {
            $select=clone $this->select;
            $select->reset("columns");
            $select->columns("count(*)");

            $select->reset('group');
            $select->reset('order');
            $select->reset('limitcount');
            $select->reset('limitoffset');

            $total=getDb()->fetchOne($select);

            return $total;
         }
         else
         {
            return 0;
         }
      }

      function getItems()
      {
      }

      /*
      function getList()
      {
         return $this->getItems();
      }
      */

      /*
      * get html of page link , like  'newest pre 1 2 3 ...next last'
      */

      function renderPaginator()
      {
         $this->getTotal();
         //result:
         //  <<  < CUR / TOTAL  >  >>
         $html='';

         $html.=$this->createLink('<<',1);
         $html.='&nbsp;&nbsp;';

         if($this->page > 1)
         {
            $page=$this->page-1;
         }
         else
         {
            $page=1;
         }
         $html.=$this->createLink('<',$page);
         $html.='&nbsp;&nbsp;';

         $html.=$this->createLink($this->page,$this->page);
         $html.='&nbsp;&nbsp;';
         $html.='/';
         $html.='&nbsp;&nbsp;';

         if($this->total > 1)
         {
            $page=$this->total-1;
         }
         else
         {
            $page=1;
         }

         $html.=$this->createLink($page,$page);

         $html.=$this->createLink('>',1);
         $html.='&nbsp;&nbsp;';
         $html.=$this->createLink('>>',$this->getTotal());

      }

      function createLink($content,$page,$params)
      {
         if($this->key_prefix == false)
         {
            $func="to_page";
         }
         else
         {
            $func=$this->key_prefix."to_page";
         }

         $html='<a href="#" onclick="'.$func.'('.$page.');">'.$content.'</a>';
         return $html;
      }
   }
