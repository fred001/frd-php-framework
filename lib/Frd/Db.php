<?php
exit();

require_once('Zend/Db.php');
require_once('Zend/Db/Table.php');

class Frd_Db
{
  protected $adapter=null;

  function __construct($adapter,$config=array(),$default=true)
  {
    $this->adapter=Zend_Db::factory($adapter,$config);
    $this->adapter->query('set names utf8');
    $this->adapter->setFetchMode(Zend_Db::FETCH_ASSOC);

    if($default == true)
    {
      $this->setAsDefault();
    }

  }

  function setAsDefault()
  {
    Zend_Db_Table::setDefaultAdapter($this->adapter);
  }

  function execute($sql, $bind = array())
  {
    return $this->adapter->query($sql);
  }

  function __call($name,$params=array())
  {
    if(method_exists($this->adapter,$name) == false)
    {
      throw new Exception("Zend_Db method not exists: $name");
    }

    return call_user_func_array(array($this->adapter,$name),$params);
  }

  function getAdapter()
  {
    return $this->adapter;
  }

  function getName()
  {
    $config=$this->adapter->getConfig();
    return $config['dbname'];
  }
}
