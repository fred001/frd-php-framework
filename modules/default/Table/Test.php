<?php
class Index_Table_Test extends Frd_Db_Table
{
  function __construct()
  {
     //params:  table name and primary key
    parent::__construct("test","id");
  }
}
