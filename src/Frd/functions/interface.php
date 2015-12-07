<?php
//app's lib functions, for reuse
function cache_init()
{
  global_set("cache.use.name",'default');
}

//@param type "file" 
//@example  cache_init("default","file",array('folder'=>$PATH))
function cache_create($name="default",$type,$options)
{
  if($type != "file")
  {
    throw new Exception("unknown cache type");
  }

  $folder=$options['folder'];

  $frontendOptions = array(
    'lifetime' => 7200, // cache lifetime of 2 hours
    'automatic_serialization' => true
  );

  $backendOptions = array(
    'cache_dir' => $folder,
  );

  // getting a Zend_Cache_Core object
  $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

  global_set("cache.$name",$cache);
}


function cache_use($name)
{
  global_set("cache.use.name",$name);
}

function cache_get_current()
{
  $name=global_get("cache.use.name");
  $cache=global_get("cache.$name");

  return $cache;
}

function cache_save($k,$v)
{
  $cache=cache_get_current();
  return $cache->save($v,$k);
}

//return false if not exists
function cache_load($k)
{
  $cache=cache_get_current();
  return $cache->load($k);
}


