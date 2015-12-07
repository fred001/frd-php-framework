<?php

/* - - - - - - - - - - - - - - - - - - - - -

 Title : PHP Quick Profiler MySQL Class
 Author : Created by Ryan Campbell
 URL : http://particletree.com/features/php-quick-profiler/

 Last Updated : April 22, 2009

 Description : A simple database wrapper that includes
 logging of queries.

- - - - - - - - - - - - - - - - - - - - - */

class Frd_Profiler_Db 
{
   protected $db=null; //zend db query
   public $queryCount = 0;
   public $queryTime = 0;
   public $queries = array();
	
	/*------------------------------------
	          CONFIG CONNECTION
	------------------------------------*/
	
	function __construct($db)
  {
     $this->db=$db;
	}

  /**
  * get query from zend profiler
  */
  function initQuery()
  {
     $profiler = $this->db->getProfiler();

     $totalTime    = $profiler->getTotalElapsedSecs();
     $queryCount   = $profiler->getTotalNumQueries();
     $this->queryTime=$totalTime;
     $this->queryCount=$queryCount;

     //$queries=$profiler->getQueryProfiles();
     $queries = $profiler->getQueryProfiles(Zend_Db_Profiler::SELECT);

     if($queries != false) 
     {
        foreach ($queries as $query)
        {
           $sql=$query->getQuery();
           $params=$query->getQueryParams();

           foreach($params as $param)
           {
              $sql=$this->db->quoteInto($sql,$param,'string',1);
           }


           //echo get_class($query);
           $this->queries[]=array(
              'sql'=>$sql,
              'type'=>$query->getQueryType(),
              'time'=>$query->getElapsedSecs() ,
           );
        }
     }

  }
  /**
  * for query explain sql
  */
  function explain($sql)
  {
     //skip "set names utf8"
     if(strpos($sql,"set") === 0)
     {
        return false;
     }

     if(strpos($sql,"show") === 0)
     {
        return false;
     }

     $sql = 'EXPLAIN '.$sql;
     $ret=$this->db->fetchRow($sql);

     return $ret;
  }
	/*-----------------------------------
	          	DEBUGGING
	------------------------------------*/
	
  /**
  * set query from zend profiler
  */ 
  /*
  function logQuery($sql) 
  {
		$start = $this->getTime();
		$this->queryCount += 1;

		$query = array(
				'sql' => $sql,
				'time' => ($this->getTime() - $start)*1000
			);
		array_push($this->queries, $query);
	}
  */
	
  /*
  function getTime() 
  {
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$start = $time;
		return $start;
	}
  */
	
  public function getReadableTime($time) 
  {
		$ret = $time;
		$formatter = 0;
		$formats = array('ms', 's', 'm');
		if($time >= 1000 && $time < 60000) {
			$formatter = 1;
			$ret = ($time / 1000);
		}
		if($time >= 60000) {
			$formatter = 2;
			$ret = ($time / 1000) / 60;
		}
		$ret = number_format($ret,3,'.','') . ' ' . $formats[$formatter];
		return $ret;
	}
	
}

?>
