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

  protected $columns=array(); //tables

  protected $created_at_field=null; //if set this field, will set this value aotomatic

  public $_module=null;  //table's module
  function __construct($table,$primary='id',$columns=array())
  {
     parent::__construct();

     $this->_name=$table;
     $this->_primary=$primary; //this will change to array after load
     $this->primary=$primary;

    $this->columns=$columns;
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

  //load one record by where condition
  function loadWhere($where)
  {
     $real_where=array();

     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $real_where[$k]=$v;
        }
        else
        {
           $real_where[$k.'=?']=$v;
        }
     }

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

      return $this->update($data,$where);
    }
    else
    {
      //insert
      //if($this->created_at_field != false)
       // $this->_data[$this->created_at_field]=date("Y-m-d H:i:s");

      $data=$this->getData();
      return $this->insert($data);
    }
  }


  function setCreatedAt($field_name='created_at')
  {
    $this->created_at_field=$field_name;
  }


  /**
  * get multi records ,and merge records 
  * each records  should has a key, which is the last parameter of this mehtod
  * it need at least 3 parameters, where1, where2, key_column_name
  */
  /*
  function getMulti()
  {
     if(func_num_args() <= 2)
     {
        trigger_error("invalid parameter: getMulti ");
     }

     $args=func_get_args();

     $key_column_name=array_pop($args);

     //query records
     $rows=array();
     foreach($args as $where)
     {
        $rows=array_merge($rows,$this->getAssoc($where,$key_column_name));
     }

     return $rows;
  }
   */


  //add record, if exists, edit, 
  function insertWhere($where,$data)
  {
     $real_where=array();

     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $real_where[$k]=$v;
        }
        else
        {
           $real_where[$k.'=?']=$v;
        }
     }

     if($this->existsWhere($real_where))
     {
        return $this->update($data,$real_where);
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
        $real_where=array();

        foreach($where as $k=>$v)
        {
           if(strpos($k,"?") !== false)
           {
              $real_where[$k]=$v;
           }
           else
           {
              $real_where[$k.'=?']=$v;
           }
        }

        return $this->update($data,$real_where);
     }
  }

  // delete records ,if not exists , do nothing
  function deleteWhere($where)
  {
     $real_where=array();

     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $real_where[$k]=$v;
        }
        else
        {
           $real_where[$k.'=?']=$v;
        }
     }

     return parent::delete($real_where);
  }


  // check if record exists
  function existsWhere($where)
  {
     $real_where=array();

     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $real_where[$k]=$v;
        }
        else
        {
           $real_where[$k.'=?']=$v;
        }
     }

     $data=$this->fetchRow($real_where);

     return ($data != false);
  }


  function delete($id)
  {
    $where=array(
      $this->primary.'= ?' => $id
    );
    return parent::delete($where);
  }

  function compress($value,$type)
  {
     if($type == 'concat')
     {
        if(is_array($value))
        {
           $value=implode(",",$value);
        }
     }
     else if($type == 'json')
     {
        if(is_array($value))
        {
           $value=json_encode($value);
        }
     }
     else if($type == 'serialize')
     {
        if(!is_string($value) && !is_numeric($value))
        {
           $value=serialize($value);
        }
     }
     else
     {
        //error
     }

     return $value;
  }

  function uncompress($value,$type)
  {
     if($type == 'concat')
     {
        $value=explode(",",$value);
     }
     else if($type == 'json')
     {
        $value=json_decode($value,true);
     }
     else if($type == 'serialize')
     {
        $value=unserialize($value);
     }
     else
     {
        //error
     }

     return $value;
  }


  //NOT USED
  //@param $mode  : insert or update
  //valid columns
  protected function handleData($mode,$data)
  {
    //1, check column required
    foreach($this->columns as $name=>$column)
    {
      if($column['required'] == true)
      {
        if(!isset($data[$name]))
        {
          throw new Exception(sprintf("miss %s when insert into %s ",$name,$this->_name));
        }
      }
    }
    //2 , auto fill
    foreach($this->columns as $name=>$column)
    {
      //only if the valud not set, then do auto fill
      if(!isset($data[$name]))
      {
        if(isset($column['auto_fill']) && is_array($column['auto_fill']) && is_array($column['auto_fill'][$mode]))
        {
          $auto_fill_config=$column['auto_fill'][$mode];
          list($type,$value)=$auto_fill_config;

          if($type == "mysql" )
          {
            $data[$name]=new Zend_Db_Expr($value);
          }
          else if($type == "string" )
          {
            $data[$name]=$value;
          }
          else if($type == "php_function" )
          {
            //$data[$name]=$value;
          }
        }
      }
    }
    
    //dump($data);exit();

    return $data;
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
  function getCol($where,$column)
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

     //$select->limit(1);

     //echo $select;
     //exit();

     try{
        $value=$this->_db->fetchCol($select);

        /*
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
        */

     }catch(Exception $e)
     {
        $msg=$e->getMessage().': '.$select->__toString();
        trigger_error($msg);
     }

     return $value;
  }


  /**
  * get multi records ,and merge records 
  * each records  should has a key, which is the last parameter of this mehtod
  * it need at least 3 parameters, where1, where2, key_column_name
  */
  function getMulti()
  {
     if(func_num_args() <= 2)
     {
        trigger_error("invalid parameter: getMulti ");
     }

     $args=func_get_args();

     $key_column_name=array_pop($args);

     //query records
     $rows=array();
     foreach($args as $where)
     {
        $rows=array_merge($rows,$this->getAssoc($where,$key_column_name));
     }

     return $rows;
  }
  
  //old method ,do not need
  function setModifiedAt($field_name='modified_at')
  {
    return false;
  }
}
