<?php

/****** not core : can delete ,will not cause framework not work ******/
/**
 *
 * @param string  string with split char, like  AA,BB,CC,DD,
 * @return array  if string is false, return empty array
 *
 */
function explodeString($string,$split_char=",")
{
  $result=array();

  $string=trim($string);
  $values=explode($split_char,$string);

  foreach($values as $v)
  {
    $v=trim($v);

    if($v != false)
      $result[]=$v;
  }

  return $result;
}


/**
 * pick several values from array
 */
function pickArray($arr,$keys)
{
  $data=array();

  $keys=explodeString($keys);
  foreach($keys as $key)
  {
    $data[$key]=$arr[$key]; 
  }

  return $data;
}

function guid($namespace = '') 
{    
  static $guid = '';
  $uid = uniqid("", true);
  $data = $namespace;
  $data .= $_SERVER['REQUEST_TIME'];
  $data .= $_SERVER['HTTP_USER_AGENT'];

  //these are empty
  //$data .= $_SERVER['LOCAL_ADDR'];
  //$data .= $_SERVER['LOCAL_PORT'];
  $data .= $_SERVER['REMOTE_ADDR'];
  $data .= $_SERVER['REMOTE_PORT'];
  $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
  $guid = '{' .  
    substr($hash,  0,  8) .
    '-' .
    substr($hash,  8,  4) .
    '-' .
    substr($hash, 12,  4) .
    '-' .
    substr($hash, 16,  4) .
    '-' .
    substr($hash, 20, 12) .
    '}';
  return $guid;
}

function php_var_to_js($js_var_name,$php_var)
{
  $php_var=htmlentities($php_var,ENT_QUOTES,"UTF-8");

  $id="_php_var_".$js_var_name;

  echo "\n";
  echo '<!-- php var to js start -->';
  echo "\n";
  echo '<textarea style="display:none" name="'.$id.'" id="'.$id.'">'.$php_var.'</textarea>';
  echo "\n";
  echo '<script>var '.$js_var_name.'=document.getElementById("'.$id.'").value; </script>';
  echo "\n";
  echo '<!-- php var to js end -->';
  echo "\n";
}

//get value from an object or array
function value_get($data,$k,$default=null)
{
  if(is_array($data))
  {
    return _array_value_get($data,$k,$default);
  }
  else
  {
    return _object_value_get($data,$k,$default);
  }
}

function value_set($data,$k,$v)
{
  if(is_array($data))
  {
    return _array_value_set($data,$k,$v);
  }
  else
  {
    return _object_value_set($data,$k,$v);
  }
}

function value_has($data,$k)
{
  if(is_array($data))
  {
    return _array_value_set($data,$k,$v);
  }
  else
  {
    return _object_value_set($data,$k,$v);
  }
}

function value_delete($data,$k)
{
  if(is_array($data))
  {
    return _array_value_set($data,$k,$v);
  }
  else
  {
    return _object_value_set($data,$k,$v);
  }
}

//if value not exists, throw exception
function value_eget($data,$k)  //if not exists ,throw exception
{
  if(is_array($data))
  {
    return _array_value_eget($data,$k);
  }
  else
  {
    return _object_value_eget($data,$k);
  }
}

//convert value to integer
function value_geti($data,$k,$default=null )  //parse to INT
{
  if(is_array($data))
  {
    return _array_value_geti($data,$k,$default);
  }
  else
  {
    return _object_value_geti($data,$k,$default);
  }
}

//convert value to integer ,and if value == 0 ,throw exception
function value_egeti($data,$k)
{
  if(is_array($data))
  {
    return _array_value_egeti($data,$k);
  }
  else
  {
    return _object_value_egeti($data,$k);
  }
}


/**** vlaue_* functions 's array backend  ****/
function _array_value_get($data,$k,$default=null)
{
  if(isset($data[$k]))
  {
    return $data[$k];
  }
  else
  {
    return $default;
  }
}

function _array_value_set($data,$k,$v)
{
  $data[$k]=$v;
}

function _array_value_has($data,$k)
{
  return isset($data[$k]);
}

function _array_value_delete($data,$k)
{
  if( isset($data[$k]))
  {
    unset($data[$k]);
  }
}

function _array_value_eget($data,$k)  //if not exists ,throw exception
{
  if(!isset($data[$k]))
  {
    throw new Exception("VALUE_EGET - VALUE NOT EXISTS:",$k);
  }

  return $data[$k];
}

function _array_value_geti($data,$k,$default=null )  //parse to INT
{
  $value=_array_value_get($data,$k,$default);
  return intval($value);
}

function _array_value_egeti($data,$k)   //if value ==0  throw exception 
{
  $value=_array_value_geti($data,$k);
  if($value == 0)
  {
    throw new Exception("VALUE_EGETI - VALUE INVALID:$k");
  }
  return $vlaue;
}


/**** vlaue_* functions 's object backend  ****/
function _object_value_get($data,$k,$default=null)
{
  if(isset($data->$k))
  {
    return $data->$k;
  }
  else
  {
    return $default;
  }
}

function _object_value_set($data,$k,$v)
{
  $data->$k=$v;
}

function _object_value_has($data,$k)
{
  return isset($data->$k);
}

function _object_value_delete($data,$k)
{
  if( isset($data->$k))
  {
    unset($data->$k);
  }
}

function _object_value_eget($data,$k)  //if not exists ,throw exception
{
  if(!isset($data->$k))
  {
    throw new Exception("VALUE_EGET - VALUE NOT EXISTS:",$k);
  }

  return $data->$k;
}

function _object_value_geti($data,$k,$default=null )  //parse to INT
{
  $value=_object_value_get($data,$k,$default);
  return intval($value);
}

function _object_value_egeti($data,$k)   //if value ==0  throw exception 
{
  $value=_object_value_geti($data,$k);
  if($value == 0)
  {
    throw new Exception("VALUE_EGETI - VALUE INVALID:$k");
  }
  return $vlaue;
}


function is_https()
{
  if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on')
    return true;
  else
    return false;
}

function getAbsolutePath($current_file_path,$relative_path)
{
  $path=dirname($current_file_path).'/'.$relative_path;

  return $path;
}

function dump($data)
{
  if($data == false)
    var_dump($data);
  else
    print_r($data);
}

/*
function getClientIp()
{
   // Get client ip address
   if ( isset($_SERVER["REMOTE_ADDR"]))
   $client_ip = $_SERVER["REMOTE_ADDR"];
   else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
   $client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
   else if ( isset($_SERVER["HTTP_CLIENT_IP"]))
   $client_ip = $_SERVER["HTTP_CLIENT_IP"];

   return $client_ip;
}
 */


function http_post_file($url,$name,$filepath,$post_array=array())
{
  if(empty($url))
  { 
    return false;
  }


  //open connection
  $ch = curl_init();

  //set the url, number of POST vars, POST data
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_POST, true);

  //$fields_string =http_build_query($post_array);
  //curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");

  // same as <input type="file" name="file_box">
  $post = array(
    $name=>"@$filepath",
  );

  curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 

  //execute post
  $result = curl_exec($ch);

  if($result == false)
  {
    return curl_error($ch);
  }
  //close connection
  curl_close($ch);


  return $result;
}

/**
 * insert an item to array

 * @param integer|string $postition can be position or key 
 * @param array  $insert_array 
 *
 */
function array_insert (&$array, $position, $insert_array) 
{
  //for string key, find the position
  if(is_string($position))
  {
    $i=0;
    foreach($array as $k=>$v)
    {
      if($k == $position)
      {
        break;
      }

      $i++;
    }

    if($i >= count($array))
    {
      //not find
      return false;
    }

  } 

  $first_array = array_splice ($array, 0, $i+1);
  $array = array_merge ($first_array, $insert_array, $array);

  return true;
}



function nl($output=true)
{
  if($output)
  {
    echo "\n";
  }
  else
  {
    return "\n";
  }
}


//decode json string to array
function js_decode2($string)
{
  if(trim($string) == false) return array();

  $array=json_decode($string,true);

  return $array;
}






function successResponse($response=array())
{
  $default =array(
    'error'=>0,
  );

  $response=array_merge($response,$default);
  return json_encode($response);
}

function errorResponse($msg,$code=0,$data=array())
{
  $response= array(
    'error'=>1,
    'error_code'=>$code,
    'error_msg'=>$msg,
    'error_data'=>$data,
  );

  return json_encode($response);
}

function failedResponse($msg='',$data=array())
{
  $response= array(
    'error'=>1,
    'error_code'=>"SYSTEM_ERROR",
    'error_msg'=>$msg,
    'error_data'=>$data,
  );

  return json_encode($response);
}


function http_post($url,$post_array)
{
  if(empty($url)){ return false;}

  $fields_string =http_build_query($post_array);

  //open connection
  $ch = curl_init();

  //set the url, number of POST vars, POST data
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  //DANGEROUS
  //if(strpos($url,"https://iamlosing.me") === 0)
  {
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书  
  }


  //curl_setopt($ch, CURLOPT_HEADER, 0);
  //curl_setopt($ch, CURLOPT_VERBOSE, 0);
  //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");

  //execute post
  $result = curl_exec($ch);
  if($result == false)
  {
     $result=curl_error($ch);
  }

  //close connection
  curl_close($ch);

  return $result;
}

function http_get($url,$params=array())
{
   if(count($params) > 0)
   {
      $url.="?".http_build_query($params);
   }

   return file_get_contents($url);
}


function today($format="Y-m-d")
{
  return date($format);
}

function now($format="Y-m-d H:i:s")
{
  return date($format);
}



function pagination($total,$page_count,$page_current,$page_baseurl)
{
  $page_total=ceil($total/$page_count);


  $x=2; 

  $pages=array();


  for($i=$page_current-$x; $i< ($page_current+$x+1) ; $i++)
  {
    $pages[]=$i;
  }

  //filter
  foreach($pages as $k=>$page)
  {
    if($page <=0 || $page > $page_total)
    {
      unset($pages[$k]);
    }
  }

  $html='<nav>'
    .'<ul class="pagination">'
    .'<li>'
    .'<a href="'.$page_baseurl.'?page=1" aria-label="First">'
    .'<span aria-hidden="true">&laquo;</span>'
    .'</a>'
    .'</li>';

    foreach($pages as $page)
    {
      if($page == $page_current)
      {
        $class="active";
      }
      else
      {
        $class="";
      }

      $html.='<li class="'.$class.'"><a href="'.$page_baseurl.'?page='.$page.'">'.$page.'</a></li>';
    }


    $html.='<li>'
    .'<a href="'.$page_baseurl.'?page='.$page_total.'" aria-label="Last">'
    .'<span aria-hidden="true">&raquo;</span>'
    .'</a>'
    .'</li>'
    .'</ul>'
    .'</nav>';


  return $html;
}


function array_get($array,$key,$default=null)
{
  if(isset($array[$key]))
  {
    return $array[$key];
  }
  else
  {
    return $default;
  }
}

//仅仅处理值，并不验证
//handle_var($value,"trim");
//handle_var($value,"int");
//handle_var($value,"boolean");
//handle_var($value,"custom","function_name");
function handle($var,$type,$option=false)
{
  if($type == 'trim')
  {
    return trim($var);
  }
  else if($type == 'int')
  {
    return intval($var);
  }
  else if($type == 'boolean')
  {
    return (boolean) $var;
  }
  else if($type == 'custom')
  {
    $func=$option;
    return $func($var);
  }
  else
  {
    error("unkonw handle type:".$type);
  }
}


function validate($var,$type,$option='')
{
  if($type == "bg")
  {
    $number=intval($option);
    return $var >= $number;
  }
  else
  {
    error("unkonw validate type:".$type);
  }
}

