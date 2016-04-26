<?php
   class Frd_Profiler
   {
      protected $times=array();
      protected $db=false;
      function __construct()
      {
         $db=getDb();
         if($db == false)
         {
            $this->db=$db;
         }
         else
         {
            $db->getProfiler()->setEnabled(true);

            $this->db=new Frd_Profiler_Db($db);
         }
      }

      function renderTime()
      {
         if(defined(FRD_START_MICROTIME))
         {
            $this->times['start']=FRD_START_MICROTIME;
         }
         else
         {
            $this->times['start']=$_SERVER['REQUEST_TIME'];
         }

         //end
         $this->times['end']=$this->getMicroTime();

         //total
         $this->times['total']=$this->times['end']-$this->times['start'];

         //render
         //$template=new Frd_Template();
         //$path=Frd::getFrdTemplatePath("profiler/time.phtml");
         //$html=$template->render($path,array('times'=>$this->times));

         //return $html;
      }



      function renderMemory()
      {
         //add start
         $memory= array();
         //$memory['used'] = $this->getReadableFileSize(memory_get_peak_usage());
         $memory['used'] = memory_get_peak_usage();
         $memory['total'] = ini_get("memory_limit");

         //render
         $template=new Frd_Template();
         $path=Frd::getFrdTemplatePath("profiler/memory.phtml");
         $html=$template->render($path,array('memory'=>$memory));

         return $html;
      }

      function renderFileData()
      {
         $files = get_included_files();
         $fileList = array();
         $fileTotals = array(
            "count" => count($files),
            "size" => 0,
            "largest" => 0,
         );

         foreach($files as $key => $file) 
         {
            $size = filesize($file);
            $fileList[] = array(
               'name' => $file,
               'size' => Frd_File::getReadableFileSize($size),
               'byte_size'=>$size,
            );
            $fileTotals['size'] += $size;
            if($size > $fileTotals['largest']) $fileTotals['largest'] = $size;
         }


         function frd_profiler_file_cmp($file1,$file2)
         {
            return  $file2['byte_size']-$file1['byte_size'];
         }
         //sort files
         usort($fileList,"frd_profiler_file_cmp");

         $fileTotals['size'] = Frd_File::getReadableFileSize($fileTotals['size']);
         $fileTotals['largest'] = Frd_File::getReadableFileSize($fileTotals['largest']);

         //render
         $template=new Frd_Template();
         $path=Frd::getFrdTemplatePath("profiler/files.phtml");
         $html=$template->render($path,array(
            'files'=>$fileList,
            'file_totals'=>$fileTotals,
         ));

         return $html;
      }

      public function renderQueryData() 
      {
         if($this->db == false)
         {
            return '';
         }
         $this->db->initQuery();

         $queryTotals = array();
         $queryTotals['count'] = 0;
         $queryTotals['time'] = 0;
         $queries = array();

         if($this->db != '') 
         {
            $queryTotals['count'] += $this->db->queryCount;
            $queryTotals['time'] += $this->db->queryTime;

            foreach($this->db->queries as $key => $query) 
            {
               $query = $this->attemptToExplainQuery($query);
               $queryTotals['time'] += $query['time'];
               $query['time'] = $this->getReadableTime($query['time']);
               $queries[] = $query;
            }
         }

         $queryTotals['time'] = $this->getReadableTime($queryTotals['time']);

         //render
         $template=new Frd_Template();
         $path=Frd::getFrdTemplatePath("profiler/query.phtml");
         $html=$template->render($path,array(
            'queries' => $queries,
            'query_totals' => $queryTotals,
         ));

         return $html;
      }


      function attemptToExplainQuery($query) 
      {

         $sql = $query['sql'];
         $ret=$this->db->explain($sql);

         $query['explain'] = $ret;

         return $query;
      }
      public function getReadableTime($time) {
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

      public function getMicroTime() 
      {
         $time = microtime();
         $time = explode(' ', $time);
         return $time[1] + $time[0];
      }

      //
      function markTime($name)
      {
         ;
      }

      function markMemory($name)
      {
         ;
      }

      function render()
      {
         $html=$this->renderTime();
         $html.=$this->renderMemory();
         $html.=$this->renderQueryData();
         $html.=$this->renderFileData();

         return $html;
         //Frd::getPage()->addBodyHtml($html);
      }
   }
