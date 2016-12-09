<?php
   class Frd_Api
   {
      function __construct($table)
      {
         $this->tablename=$table->getName();
         $this->table=$table;

         $this->db=app()->getDb();
      }

      function insert($params)
      {
         $table_id=$this->table->insert($params);
         if($table_id)
         {
            $data=$this->table->getRow(array("id"=>$table_id));

            return successResponse(array( 'data'=>$data));
         }
         else
         {
            return errorResponse("insert failed");
         }
      }

      function update($params)
      {
         $table_id=value_get($params,"id");
         unset($params['id']);

         $result=$this->table->updateWhere(array("id"=>$table_id),$params);

         if($result)
         {
            $data=$this->table->getRow(array("id"=>$table_id));
            return successResponse(array( 'data'=>$data));
         }
         else
         {
            return errorResponse("update failed");
         }

      }

      function get($params)
      {
         $table_id=value_get($params,'id');
         $data=$this->table->getRow($this->tablename,array("id"=>$table_id));

         if($data)
         {
            $rows=$this->search_handle_rows(array($data));
            $data=$rows[0];
         }


         return successResponse(array( 'data'=>$data));
      }

      function delete($params)
      {
         $table_id=$params['id'];

         $this->table->deleteWhere(array("id"=>$table_id));
         return successResponse();
      }

      protected function search_build_select($params)
      {
         $select=$this->db->select();

         if(isset($params['col']) && $params['cols'])
         {
            $select->from($this->tablename,explode(",",$params['cols']));
         }
         else
         {
            $select->from($this->tablename,"*");
         }



         $searchs=value_get($params,'searchs',array());
         if(!is_array($searchs)) $searchs=array();

         foreach($searchs as $k=>$v)
         {
            if(strpos($v,"%") !== false)
            {
               $select->where("$k like ?",$v);
            }
            else
            {
               $select->where("$k=?",$v);
            }
         }

         $select->order("id desc");


         return $select;
      }

      function search($params=array())
      {
         $select=$this->search_build_select($params);

         #
         $page=value_get($params,'page',1);
         $page_count=value_get($params,'page_count',10);

         $select->limitPage($page,$page_count);

         $rows=$this->db->fetchAll($select);
         if($rows)
         {
            $rows=$this->search_handle_rows($rows);
         }

         #total
         $select->reset("columns");
         $select->columns("count(*)");
         $select->reset('order');
         $select->reset('limitcount');
         $select->reset('limitoffset');

         $total=$this->db->fetchOne($select);
         $total=intval($total);

         $pagination=array(
            'total'=>$total,
            'pagecount'=>$page_count,
            'page'=>$page,
         );

         return successResponse(array(
            'data'=>$rows,
            'pagination'=>$pagination,
         ));
      }


      function search_all($params)
      {
         $select=$this->search_build_select($params);
         $rows=$this->db->fetchAll($select);
         if($rows)
         {
            $rows=$this->search_handle_rows($rows);
         }

         return successResponse(array( 'data'=>$rows));
      }

      function find_all($params=array())
      {
         $select=$this->search_build_select($params);
         $rows=$this->db->fetchAll($select);
         if($rows)
         {
            $rows=$this->search_handle_rows($rows);
         }

         return successResponse(array( 'data'=>$rows));
      }

      function find_one($params=array())
      {
         $select=$this->search_build_select($params);
         $rows=$this->db->fetchRow($select);
         if($rows)
         {
            $rows=$this->search_handle_rows($rows);
         }

         return successResponse(array( 'data'=>$rows));
      }

      function find($params=array())
      {
         $select=$this->search_build_select($params);

         #
         $page=value_get($params,'page',1);
         $page_count=value_get($params,'page_count',10);

         $select->limitPage($page,$page_count);

         $rows=$this->db->fetchAll($select);
         if($rows)
         {
            $rows=$this->search_handle_rows($rows);
         }

         #total
         $select->reset("columns");
         $select->columns("count(*)");
         $select->reset('order');
         $select->reset('limitcount');
         $select->reset('limitoffset');

         $total=$this->db->fetchOne($select);
         $total=intval($total);

         $pagination=array(
            'total'=>$total,
            'pagecount'=>$page_count,
            'page'=>$page,
         );

         return successResponse(array(
            'data'=>$rows,
            'pagination'=>$pagination,
         ));
      }

      function search_one($params)
      {
         $select=$this->search_build_select($params);
         $rows=$this->db->fetchRow($select);
         if($rows)
         {
            $rows=$this->search_handle_rows($rows);
         }

         return successResponse(array( 'data'=>$rows));
      }

      function search_handle_rows($rows)
      {
         return $rows;
      }

      //insert where
      //update where
      //delete where 
   }



