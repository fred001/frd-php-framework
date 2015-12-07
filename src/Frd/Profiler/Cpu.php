<?php
   class Frd_Profiler_Cpu
   {
      function onRequestStart() {
         $dat = getrusage();
         define('PHP_TUSAGE', microtime(true));
         define('PHP_RUSAGE', $dat["ru_utime.tv_sec"]*1e6+$dat["ru_utime.tv_usec"]);
      }


      function getCpuUsage() {
         $dat = getrusage();
         $dat["ru_utime.tv_usec"] = ($dat["ru_utime.tv_sec"]*1e6 +
         $dat["ru_utime.tv_usec"]) - PHP_RUSAGE;
         $time = (microtime(true) - PHP_TUSAGE) * 1000000;

         // cpu per request
         if($time > 0) {
            $cpu = sprintf("%01.2f", ($dat["ru_utime.tv_usec"] /
            $time) * 100);
         } else {
            $cpu = '0.00';
         }

         return $cpu;
      }
   }
