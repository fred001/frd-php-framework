<?php
function file_createDir($dir,$mode=0777) 
{
  if(self::exists($dir) == false)
  {
    if( mkdir($dir,$mode,true) == false)
    {
      throw new Exception("mkdir falied: $dir");
    }
  }
  else if(self::isDir($dir) == false)
  {
    throw new Exception("file exists and not dir, can not create dir");
  }

  return true;
}
// create the folder by file path
function file_createDirByFilePath($filename)
{
  $folder=dirname($filename);
  self::createDir($folder);
}


// delete dir
function file_deleteDir($dir) 
{
  $dir = str_replace('', '/', $dir);
  $dir = substr($dir, -1) == '/' ? $dir : $dir.'/';
  if (!is_dir($dir)) 
  {
    return false;
  }

  $dirHandle = opendir($dir);
  while(false !== ($file = readdir($dirHandle))) 
  {
    if ($file == '.' || $file == '..') 
    {
      continue;
    }
    if (!is_dir($dir.$file)) 
    {
      self::delete($dir . $file);
    } 
    else 
    {
      self::deleteDir($dir . $file);
    }
  }

  closedir($dirHandle);

  return rmdir($dir);
  //return true;
  //return self::deleteDir($dir);
}

// create file
function file_createFile($path, $overWrite = false) 
{
  if (file_exists($path) && $overWrite == false) 
  {
    return false;
  }
  else if (file_exists($path) && $overWrite == true) 
  {
    self::delete(file);
  }

  //$dir = dirname($file);
  //self::createDir($dir);

  touch($path);

  return true;
}

// if file exists

function file_checkExists($path)
{
  if( file_exists($path) == false)
  {
    Frd::log("file not exists: $path");
    trigger_error("file not exists:$path");
    return false;
  }
  else
  {
    return true;
  }
}

      /*
      public static function moveDir($oldDir, $aimDir, $overWrite = false) 
      {
         $aimDir = str_replace('', '/', $aimDir);
         $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
         $oldDir = str_replace('', '/', $oldDir);
         $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
         if (!is_dir($oldDir)) {
            return false;
         }
         if (!file_exists($aimDir)) {
            FileUtil::createDir($aimDir);
         }
         @$dirHandle = opendir($oldDir);
         if (!$dirHandle) {
            return false;
         }
         while(false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
               continue;
            }
            if (!is_dir($oldDir.$file)) {
               FileUtil::moveFile($oldDir . $file, $aimDir . $file, $overWrite);
            } else {
               FileUtil::moveDir($oldDir . $file, $aimDir . $file, $overWrite);
            }
         }
         closedir($dirHandle);
         return FileUtil::unlinkDir($oldDir);
      }

      public static function moveFile($fileUrl, $aimUrl, $overWrite = false) 
      {
         if (!file_exists($fileUrl)) {
            return false;
         }
         if (file_exists($aimUrl) && $overWrite = false) {
            return false;
         } elseif (file_exists($aimUrl) && $overWrite = true) {
            FileUtil::unlinkFile($aimUrl);
         }
         $aimDir = dirname($aimUrl);
         FileUtil::createDir($aimDir);
         rename($fileUrl, $aimUrl);
         return true;
      }

      public static function copyDir($oldDir, $aimDir, $overWrite = false) 
      {
         $aimDir = str_replace('', '/', $aimDir);
         $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir.'/';
         $oldDir = str_replace('', '/', $oldDir);
         $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir.'/';
         if (!is_dir($oldDir)) {
            return false;
         }
         if (!file_exists($aimDir)) {
            FileUtil::createDir($aimDir);
         }
         $dirHandle = opendir($oldDir);
         while(false !== ($file = readdir($dirHandle))) {
            if ($file == '.' || $file == '..') {
               continue;
            }
            if (!is_dir($oldDir . $file)) {
               FileUtil::copyFile($oldDir . $file, $aimDir . $file, $overWrite);
            } else {
               FileUtil::copyDir($oldDir . $file, $aimDir . $file, $overWrite);
            }
         }
         return closedir($dirHandle);
      }

      public static function copyFile($fileUrl, $aimUrl, $overWrite = false) 
      {
         if (!file_exists($fileUrl)) {
            return false;
         }
         if (file_exists($aimUrl) && $overWrite == false) {
            return false;
         } elseif (file_exists($aimUrl) && $overWrite == true) {
            FileUtil::unlinkFile($aimUrl);
         }
         $aimDir = dirname($aimUrl);
         FileUtil::createDir($aimDir);
         copy($fileUrl, $aimUrl);
         return true;
      }
       */


//* delete
function file_delete($file) 
{
  if (file_exists($file)) 
  {
    unlink($file);
    return true;
  } 
  else 
  {
    return false;
  }
}

function file_size($size,$unit='KB')
{
  $map=array(
    "B"=>1,
    "KB"=>1024,
    "MB"=>1024*1024,
    "GB"=>1024*1024*1024,
    "TB"=>1024*1024*1024*1024,
    "PB"=>1024*1024*1024*1024*1024,
  );

  if(isset($map[$unit] ))
    $min_size=$map[$unit];
  else
    throw new Exception("Do Not Know The Unit");

  $size /= $min_size;
  return round($size,4);
}

function file_isReadable($path)
{
  if(self::exists($path) == false)
  {
    throw new Exception("file not exists");
  }

  return is_readable($path);
}

function file_checkReadable($path)
{
  if($path == false)
  {
    throw new Exception("parameter is false");
  }
  if( self::isReadable($path) == false)
  {
    throw new Exception("not readable $path");
  }
}

function file_checkWritable($path)
{
  if($path == false)
  {
    throw new Exception("parameter is false");
  }
  if( self::isWritable($path) == false)
  {
    throw new Exception("not readable $path");
  }
}

function file_isWritable($path)
{
  return is_writable($path);
} 

function file_isDir($dir)
{
  return is_dir($dir); 
}

function file_isFile($file)
{
  return is_file($file); 
}

function file_read($file)
{
  if(self::isReadable($file))
  {
    return file_get_contents($file);
  }
  else
  {
    throw new Exception("file can not read");
  }
}

function file_write($file,$contents)
{
  $ret=file_put_contents($file,$contents);

  if($ret == true)
  {
    return true;
  }
  else
  {
    return false;

  }
            /*
         if(self::isWritable($file))
         {
            file_put_contents($file,$contents);
            return true;
         }
         else
         {
            Frd::log("file not writable:[$file]");
            Frd::log("uid:".posix_getuid());
            Frd::log("gid:".posix_getgid());

            //throw new Exception("file can not write");
            return false;
         }
 */
}

// add content
function file_add($file,$content)
{
  if(self::isWritable($file))
  {
    file_put_contents($file,$content,FILE_APPEND);
  }
  else
  {
    throw new Exception("file can not write");
  }
}

//add new line
function file_addLine($file,$content)
{
  $content="\n\r".$content;

  self::add($file,$content);
}

function file_save($file,$content)
{
  if($content == false)
  {
    touch($file);
  }
  else 
  {
    if ( file_put_contents($file,$content) == false)
      throw new Exception("file can not write");
  }
}

// get filename's suffix
function file_getType($filename)
{
  if(strpos($filename,".") === false)
  {
    return "";
  }
  else
  {
    $arr=explode('.', $filename);
    return strtolower(array_pop($arr));
  }
}

// get filename's name
function file_getName($filename)
{
  if(strpos($filename,"/") === false)
  {
    return $filename;
  }
  else
  {
    $arr=explode('/', $filename);
    return strtolower(array_pop($arr));
  }
}

// get dir's file names
function file_get_dir_files($dir)
{
  $dir=rtrim($dir,"/");

  if (is_dir($dir)) 
  {
    if ($dh = opendir($dir)) 
    {
      $files=array();
      while( ($file = readdir($dh))  != false)
      {
        if ($file!="." && $file!="..") 
        {
          $files[]=realpath($dir.'/'.$file);
        }
      }

      return $files;
    }
    else
    {
      throw new Exception("can not open dir $dir"); 
    }
    closedir($dh);
  }
  else
  {
    throw new Exception("not dir"); 
  }
}

// get all dir's files in array
function file_getAllDirFiles($dir)
{
  $files=array();

  if (self::isDir($dir) == false) 
  {
    throw new Exception("not dir"); 
  }

  $dir = new DirectoryIterator($dir);
  foreach ($dir as $fileinfo) 
  {
    if(!$fileinfo->isDot()&&!$fileinfo->isFile())
    {
      $files=array_merge($files,self::getAllDirFiles($fileinfo->getPathname()));

    }

    if(!$fileinfo->isDot() && $fileinfo->isFile())
    {
      $files[]=$fileinfo->getPathname();
    }
  }

  return $files;
}

//read file content to array
function file_fileToArray($filepath)
{
  if(self::isReadable($filepath) == false)
  {
    throw new Exception("file not readable");
  }

  $arr=file($filepath);

  foreach($arr as $k=>$v)
  {
    $arr[$k]=str_replace(array("\n","\r"),array("",""),$v);
  }


  if($arr === false)
  {
    throw new Exception("fileToArray failed");
  }

  return $arr;
}

// edit file's content
function file_replaceFile($filepath,$pattern,$replace)
{
  if(self::exists($filepath) == false)
  {
    throw new Exception("not exists"); 
  }

  if(self::isFile($filepath) == false)
  {
    throw new Exception("not file"); 
  }

  if(self::isWritable($filepath) == false)
  {
    throw new Exception("not writable"); 
  }

  //edit file

  $content=self::read($filepath);

  $content=str_replace($pattern,$replace,$content);

  self::write($filepath,$content);
}

//edit file's each line
function file_replaceLine($filepath,$pattern,$replace)
{
  $rows=self::fileToArray($filepath);

  foreach($rows as $k=>$row)
  {
    $rows[$k]=str_replace($pattern,$replace,$row);
  }

  //save as file

  // $content=implode("\n\r",$rows);
  $content=implode("\n",$rows);

  return self::write($filepath,$content);
}



//remove lines in the file is it match the pattern
function file_removeLine($filepath,$pattern)
{
  $rows=self::fileToArray($filepath);


  foreach($rows as $k=>$row)
  {
    if(Frd_Regexp::isMatch($row,$pattern))
    {
      unset($rows[$k]);
    }

  }

  //save as file

  //$content=implode("\n\r",$rows);
  //$content=implode("\n\r",$rows);
  $content=implode("\n",$rows);

  self::write($filepath,$content);
}


// create file, if touch exists file, will do nothing
function file_touch($filepath,$time=false,$atime=false)
{
  if(self::exists($filepath) == false)
  {
    if(touch($filepath) == false)
    {
      throw new Exception("touch file failed"); 
    }
  }
}

// increment a filename, to make sure the file not overwrited
///var/www/html/a.txt will be  /var/www/html/a_1.txt ,and so on
function file_incrementFilename($filepath)
{
  if(self::exists($filepath) == false)
  {
    return $filepath;
  }
  else
  {
    $part1='';
    $part1_2='';
    $part2='';

    while(self::exists($filepath) ==  true )
    {
      $index=stripos($filepath,'.');  //search  .

      if($index !== false)
      {
        $part1=substr($filepath,0,$index); // do not with the .
        $part2=substr($filepath,$index);  //with the . 
      }
      else
      {
        $part1=$filepath;
        $part2="";
      }

      // now search _NUMBER  for part1
      $index=stripos($filepath,'_');  //search  .
      if($index != false)
      {
        $part1_2=substr($part1,$index+1); // do not with the _

        if( is_numeric($part1_2) )
        {
          $part1=substr($part1,0,$index+1); //with _
          $part1_2+=1;
        }
      }
      else
      {
        $part1_2="_1";
      }

      //result filepath
      $filepath=$part1.$part1_2.$part2;
    }


    return $filepath;

  }
}

//rm dir even it is not empty
function file_rrmdir($dir) 
{
  if (is_dir($dir)) 
  {
    $objects = scandir($dir);
    foreach ($objects as $object) 
    {
      if ($object != "." && $object != "..") 
      {
        if (filetype($dir."/".$object) == "dir") 
        {
          Frd_File::rrmdir($dir."/".$object); 
        }
        else 
        {
          unlink($dir."/".$object);
        }
      }
    }
    reset($objects);
    Frd_File::deleteDir($dir);
  }
} 

function file_getReadableFileSize($size, $retstring = null) 
{
  // adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
  $sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

  if ($retstring === null) { $retstring = '%01.2f %s'; }

  $lastsizestring = end($sizes);

  foreach ($sizes as $sizestring) {
    if ($size < 1024) { break; }
  if ($sizestring != $lastsizestring) { $size /= 1024; }
  }
  if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } // Bytes aren't normally fractional
  return sprintf($retstring, $size, $sizestring);
}

// get a line content from file
function file_readLine($path,$line)
{
  $arr=self::fileToArray($path);

  $amount=count($arr);
  if($line > $amount)
  {
    Frd::getLog("invalid file line number (max $amount) :$line");
  }

  return $arr[$line-1];
} 

function file_getExtension($filename)
{
  $info=explode(".",$filename);

  if(count($info) == 1)
  {
    return '';
  }
  else
  {
    return $info[count($info)-1];
  }
}
