<?php
/*
 *  1, db 辅助函数
 *  2，table辅助函数
 *  3， 高级辅助函数 （addWhere, editWhere,deleteWhere 等复合函数)
 */
function db_get($name='default')
{
  return Frd::getDb($name);
}


function db_select()
{
  $db=db_get();
  return $db->select();
}

function db_query($sql)
{
  $db=db_get();
  return $db->query($sql);
}

function db_execute($sql)
{
  $db=db_get();
  return $db->query($sql);
}


//@return  last_insert_id | 1  or false
function db_insert($table,$row)
{
  $db=db_get();
  $result= $db->insert($table,$row);

  if($result)
  {
    return $db->lastInsertId();
  }
  else
  {
    return $result;
  }
}

function db_to_where($where)
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

  return $real_where;
}

function db_update($table,$row,$where)
{
  $db=db_get();

  $where=db_to_where($where);
  return $db->update($table,$row,$where);
}

function db_exists($table,$where)
{
  $db=db_get();

  $select=$db->select();
  $select->from($table,'*');

  $where=db_to_where($where);
  foreach($where as  $k=>$v)
  {
    $select->where($k,$v);
  }

  return (bool) $db->fetchRow($select);
}

function db_delete($table,$where)
{
  $db=db_get();
  $where=db_to_where($where);
  return $db->delete($table,$where);
}

function db_fetchone($sql)
{
  $db=db_get();
  return $db->fetchOne($sql);
}

function db_fetchrow($sql)
{
  $db=db_get();
  return $db->fetchRow($sql);
}

function db_fetchall($sql)
{
  $db=db_get();
  return $db->fetchAll($sql);
}
//
function db_get_one($tablename,$col,$wheres)
{
  $db=db_get();

  $select=$db->select();
  $select->from($tablename,$col);

  foreach($wheres as  $k=>$v)
  {
    $select->where("$k=?",$v);
  }

  return $db->fetchOne($select);
}


function db_get_col($tablename,$col,$wheres)
{
  $db=db_get();

  $select=$db->select();
  $select->from($tablename,$col);

  foreach($wheres as  $k=>$v)
  {
    $select->where("$k=?",$v);
  }

  return $db->fetchCol($select);
}

function db_get_row($tablename,$wheres)
{
  $db=db_get();

  $select=$db->select();
  $select->from($tablename,"*");

  foreach($wheres as  $k=>$v)
  {
    $select->where("$k=?",$v);
  }

  return $db->fetchRow($select);
}

function db_get_pairs($tablename,$col_key,$col_value,$wheres)
{
  $db=db_get();

  $select=$db->select();
  $select->from($tablename,array($col_key,$col_value));

  foreach($wheres as  $k=>$v)
  {
    $select->where("$k=?",$v);
  }

  return $db->fetchPairs($select);
}

function db_get_all($tablename,$wheres)
{
  $db=db_get();

  $select=$db->select();
  $select->from($tablename,'*');

  foreach($wheres as  $k=>$v)
  {
    $select->where("$k=?",$v);
  }

  return $db->fetchAll($select);
}


//
function db_set_one($tablename,$col_key,$col_value,$wheres)
{
  $db=db_get();

  $set=array($col_key=>$col_value);
  return $db->update($tablename,$set,$where);
}


function db_set_row($tablename,$data,$wheres)
{
  $db=db_get();
  //check if reall one !

  return $db->update($tablename,$data,$where);
}


function db_set_all($tablename,$data,$wheres)
{
  $db=db_get();

  //check if reall one !

  return $db->update($tablename,$data,$where);
}

function db_delete_row($tablename,$wheres)
{
  $db=db_get();
  //check if reall one!

  return $db->delete($tablename,$wheres);
}

   /*
   function addDb($config,$name="default")
   {
      return Frd::addDb($config,$name);
   }


   function setDefaultDb($name)
   {
      Frd::setDefaultDb($name);
   }
    */
