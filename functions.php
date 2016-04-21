<?php
function url($path,$params=array())
{
  //$path="/index.php/".$path;
  return app()->url($path,$params);
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
