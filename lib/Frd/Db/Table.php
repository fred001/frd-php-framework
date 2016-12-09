<?php
/**
 * for table which only have a column as primary key (e.g. id)
 *
 *  @version 2011-12-14
 */

require_once("Zend/Db/Table.php");

class Frd_Db_Table  extends Zend_Db_Table
{
  protected $_data=array();
  protected $loaded=false;
  protected $primary='';
  protected $primary_value='';

  //protected $columns=array(); //tables


  function __construct($table,$primary='id',$columns=array())
  {
     parent::__construct();

     $this->_name=$table;
     $this->_primary=$primary; //this will change to array after load
     $this->primary=$primary;

    //$this->columns=$columns;
  }

  function getName()
  {
     return $this->_name;
  }


  /*********object functions********/
  /**
   * load recored 
   *
   * @param id int   primary key
   *
   * @return  true if load success, false if failed (not exists or exception)
   */
  function load($key)
  {
    $row=$this->fetchRow(array(
      $this->primary.'=?'=>$key
    ));
    if($row == false)
    {
       return false;
    }

    $this->_data=$row->toArray();
    $this->loaded=true;
    $this->primary_value=$key;

    return true;
  }

  protected function buildWhere($where)
  {
     $real_where=array();
     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $real_where[]=$this->_db->quoteInto($k,$v);
        }
        else
        {
           $real_where[]=$this->_db->quoteInto($k."=?",$v);
        }
     }

     return $real_where;
  }

  //load one record by where condition
  function loadWhere($where)
  {
     $real_where=$this->buildWhere($where);
     $row=$this->fetchRow($real_where);
     if($row == false)
     {
        return false;
     }

     $row=$row->toArray();
     $this->_data=$row;
     $this->loaded=true;
     $this->primary_value=$row[$this->primary];

     return true;
  }


  function __set($key,$value)
  {
     $this->_data[$key]=$value;	
     return $value;
  }

  function __get($name)
  {
    if(isset($this->_data[$name]))
    {
      return $this->_data[$name];
    }
    else
    {
      return null;
    }
  }

  function __isset($name)
  {
    return isset($this->_data[$name]);
  }

  function __unset($name)
  {
    unset($this->_data[$name]);
  }

  function get($name,$default=null)
  {
    if(isset($this->_data[$name]))
    {
      return $this->_data[$name];
    }
    else
    {
      return $default;
    }
  }

  function set($key,$value)
  {
     $this->_data[$key]=$value;	
     return $value;
  }

  function has($name)
  {
    return isset($this->_data[$name]);
  }

  function setData($data)
  {
    foreach($data as $k=>$v)  
    {
      $this->_data[$k]=$v; 
    }
  }

  function getData()
  {
    return $this->_data;
  }

  /**
   * save recored 
   *
   * @return  lastinertid  for update is 0 , for insert is the last insert id
   */
  function save()
  {
    if($this->loaded == true)
    {
      //update
      $data=$this->getData();

      $where=array(
        $this->primary.' = ?'=>$this->primary_value,
      );

      return parent::update($data,$where);
    }
    else
    {
      //insert
      $data=$this->getData();
      return $this->insert($data);
    }
  }




  //add record, if exists, edit, 
  function insertWhere($where,$data)
  {
     $real_where=$this->buildWhere($where);
     if($real_where == false)
     {
        throw Exception("insertWhere Exception: realwhere empty");
     }

     if($this->existsWhere($real_where))
     {
        return parent::update($data,$real_where);
     }
     else
     {
        return $this->insert($data);
     }
  }

  //edit column, if not exist, do nothing
  function updateWhere($where,$data)
  {
     if($this->existsWhere($where))
     {
        $real_where=$this->buildWhere($where);


        if($real_where == false)
        {
           throw Exception("updateWhere Exception: realwhere empty");
        }

        return parent::update($data,$real_where);
     }
  }

  // delete records ,if not exists , do nothing
  function deleteWhere($where)
  {
     $real_where=$this->buildWhere($where);
     return parent::delete($real_where);
  }


  // check if record exists
  function existsWhere($where)
  {
     $real_where=$this->buildWhere($where);
     $data=$this->fetchRow($real_where);

     return ($data != false);
  }



  //old methods
  /** fetch methods **/
  function getOne($where,$column)
  {
     if($where == false)
     {
        $where=array(); 
     }

     $select=$this->_db->select();
     $select->from($this->_name,$column);
     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $select->where($k,$v);
        }
        else
        {
           $select->where($k.'=?',$v);
        }
     }

     $select->limit(1);

     //echo $select;
     //exit();

     try{
        $value=$this->_db->fetchOne($select);

        //check compress
        if($value != false)
        {
           //check compress
           if( $this->columns != false)
           {
              foreach($this->columns as $name=>$v)
              {
                 if(isset($v['compress']) && $name == $column )
                 {
                    $value=$this->uncompress($value,$v['compress']);
                 }
              }
           }
        }

        return $value;
     }catch(Exception $e)
     {
        $msg=$e->getMessage().': '.$select->__toString();
        trigger_error($msg);
        return $value;
     }

  }

  function getRow($where=array())
  {
     if($where == false)
     {
        return array();
     }

     $select=$this->_db->select();
     $select->from($this->_name,'*');

     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $select->where($k,$v);
        }
        else
        {
           $select->where($k.'=?',$v);
        }
     }

     $select->limit(1);

     //echo $select;
     try{
        $row=$this->_db->fetchRow($select);

        if($row != false)
        {
           //check compress
           if( $this->columns != false)
           {
              foreach($this->columns as $name=>$v)
              {
                 if(isset($v['compress']))
                 {
                    $row[$name]=$this->uncompress($row[$name],$v['compress']);
                 }
              }
           }
        }

     }catch(Exception $e)
     {
        $msg=$e->getMessage().': '.$select->__toString();
        trigger_error($msg);
     }

     return $row;
  }

  function getAll($where=array(),$order=false)
  {
     if($where == false)
     {
        $where=array(); 
     }

     $select=$this->_db->select();
     $select->from($this->_name,'*');

     if(is_string($where))
     {
        $select->where($where);
     }
     else
     {
        foreach($where as $k=>$v)
        {
           if(strpos($k,"?") !== false)
           {
              $select->where($k,$v);
           }
           else
           {
              $select->where($k.'=?',$v);
           }
        }
     }

     if($order != false)
     {
        $select->order($order);
     }

     try{
        $rows=$this->_db->fetchAll($select);

        if($rows != false)
        {
           //check compress
           if( $this->columns != false)
           {
              foreach($rows as $k=>$row)
              {
                 foreach($this->columns as $name=>$v)
                 {
                    if(isset($v['compress']) && isset($row[$name]))
                    {
                       $rows[$k][$name]=$this->uncompress($row[$name],$v['compress']);
                    }
                 }
              }
           }
        }

     }catch(Exception $e)
     {
        $msg=$e->getMessage().': '.$select->__toString();
        trigger_error($msg);
     }


     return $rows;
  }

  function getAssoc($where=array(),$key_column=false)
  {
     if($where == false)
     {
        $where=array(); 
     }

     $select=$this->_db->select();

     if($key_column != false)
     {
        $select->from($this->_name,array($key_column,'*'));
     }
     else
     {
        $select->from($this->_name,'*');
     }

     if(is_string($where))
     {
        $select->where($where);
     }
     else
     {
        foreach($where as $k=>$v)
        {
           if(strpos($k,"?") !== false)
           {
              $select->where($k,$v);
           }
           else
           {
              $select->where($k.'=?',$v);
           }
        }
     }

     /*
     if($order != false)
     {
        $select->order($order);
     }
     */

     try{
        $rows=$this->_db->fetchAssoc($select);

        if($rows != false)
        {
           //check compress
           if( $this->columns != false)
           {
              foreach($rows as $k=>$row)
              {
                 foreach($this->columns as $name=>$v)
                 {
                    if(isset($v['compress']) && isset($row[$name]))
                    {
                       $rows[$k][$name]=$this->uncompress($row[$name],$v['compress']);
                    }
                 }
              }
           }
        }

     }catch(Exception $e)
     {
        $msg=$e->getMessage().': '.$select->__toString();
        trigger_error($msg);
     }


     return $rows;
  }

  function getPairs($where=array(),$key_column,$value_column)
  {
     if($where == false)
     {
        $where=array(); 
     }

     $select=$this->_db->select();

     $select->from($this->_name,array($key_column,$value_column));

     if(is_string($where))
     {
        $select->where($where);
     }
     else
     {
        foreach($where as $k=>$v)
        {
           if(strpos($k,"?") !== false)
           {
              $select->where($k,$v);
           }
           else
           {
              $select->where($k.'=?',$v);
           }
        }
     }

     /*
     if($order != false)
     {
        $select->order($order);
     }
     */

     try{
        $rows=$this->_db->fetchPairs($select);

        if($rows != false)
        {
           //check compress
           if( $this->columns != false)
           {
              foreach($rows as $k=>$value)
              {
                 foreach($this->columns as $name=>$v)
                 {
                    if(isset($v['compress']) && $value_column == $name)
                    {
                       $rows[$k]=$this->uncompress($value,$v['compress']);
                    }
                 }
              }
           }
        }


     }catch(Exception $e)
     {
        $msg=$e->getMessage().': '.$select->__toString();
        trigger_error($msg);
     }


     return $rows;
  }

  function update($where,$data)
  {
     return $this->updateWhere($where,$data);
  }

  function delete($id)
  {
     if(is_array($id))
     {
        $where=$id;
        $this->deleteWhere($where);
     }
     else
     {
        $where=array(
           $this->primary.'= ?' => $id
        );
     }
     return parent::delete($where);
  }

}
