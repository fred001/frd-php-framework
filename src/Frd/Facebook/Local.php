<?php
   /**
   *  local facebook, do not connect to facebook,
   *  only for develop in local
   */
   class Frd_Facebook_Local
   {
      protected $user_id='';

      protected $app_id='';
      protected $app_secret='';

      function __construct($config)
      {
         $this->app_id=$config['appId'];
         $this->app_secret=$config['secret'];


         $this->facebook=new $this->fb_class($config);
      }


      /**
      * check if user login 
      */
      function isLogin()
      {
         if( $this->facebook->getUserId() )
         {
            return true; 
         }
         else
         {
            return false; 
         }
      }

      /**
      * check if user denied auth 
      * response will be
      * : &error_reason=user_denied&error=access_denied&error_description=The+user+denied+your+request.#_=_
      */
      function isUserDenied()
      {
         if(isset($_GET['error']) && $_GET['error'] == 'access_denied')
         {
            return true;
         }

         return false;
      }

      /**
      * redirect 
      * @params string  $method  php_header|js_location|js_top
      */
      function redirect($method,$url)
      {
         switch($method)
         {
            case 'php_header':
            header("Location:$url");
            break;
            case 'js_location':
            echo '<script type="text/javascript">location.href="' . $url . '";</script>';
            break;
            case 'js_top':
            echo '<script type="text/javascript">top.location="' . $url . '";</script>';
            break;
            default:
            throw new Exception("unknown redirect method");
            break;

         }

      }
      /**
      * login 
      * @params string  $method  php_header|js_location|js_top
      */
      function login($method,$perms,$params=array())
      {
         //if has login and has permissions , do not login again
         if($perms != false)
         {
            if($this->hasPermission($perms) == true)
            {
               return true;
            }
         }
         else
         {
            if($this->isLogin() == true)
            {
               return true;
            }
         }

         $login_url=$this->getLoginUrl($perms,$params);
         $this->redirect($method,$login_url);

      }
      /**
      * request permission
      * 
      */
      function requestPermission($perms,$params=array())
      {
         $url = $this->facebook->getLoginUrl($perms,$params);

         $this->redirect("php_header",$url);
      }

      function logout($method)
      {
         if($this->isLogin() == false)
         {
            $this->user_id=false;
         }
      }

      function getLoginUrl($perms="publish_stream",$params=array())
      {
         $default=array(
            'scope' => $perms,
            'redirect_uri' => '',
            'display' => 'page',
         );

         /*
         if(!is_array($perms))
         {
            $perms=explodeString($perms);
         }
         */


         $params=array_merge($default,$params);
         //set default redirect uri
         if($params['redirect_uri'] == false)
         {
            $params['redirect_uri']=$_SERVER['HTTP_REFERER'];
         }

         $url = $this->facebook->getLoginUrl($params);

         return $url;
      }

      function getLogoutUrl()
      {
         return $this->facebook->getLogoutUrl();
      }

      /**
      * get user id
      */
      function getUserId()
      {
         return $this->user_id; 
      }

      /**
      *  get user information with api call
      */
      function getUser()
      {
         $data=array('user_id'=>$this->user_id);
         return $data;
      }

      /**
      * get exists permissions
      *
      * @return Array $data  format: array(permission1=>1,permission2=>1...)
      */
      function getPermissions()
      {
         if($this->isLogin() == false)
         {
            throw new Exception("not login when call getUser method");
         }

         $path="/".$this->getUserId()."/permissions";
         $result=$this->api($path);

         if($result['data']  ==  false)
         {
            return array();
         }

         $data=$result['data'][0];

         return $data;
      }

      /**
      * check if has permssion, can check one or multi permissions,
      * for multi permission, if one not exists, return false
      * 
      * @param  string $perms
      * 
      */
      function hasPermission($perms)
      {
         if($this->isLogin() == false)
         {
            return false;
            //throw new Exception("not login when call hasPermission method");
         }

         $permissions=$this->getPermissions();

         $perms=explodeString($perms);

         foreach($perms as $perm)
         {
            if(!isset($permissions[$perm]) || $permissions[$perm] != 1) 
            {
               return false;
            }
         }

         return true;
      }

      function getAccessToken()
      {
         return $this->facebook->getAccessToken(); 
      }

      /**
      * use short live time access token to get long live time access token
      */
      function exchangeAccessToken()
      {
         $exchange_url="https://graph.facebook.com/oauth/access_token?";
         $exchange_url.="client_id=".$this->app_id;
         $exchange_url.="&client_secret=".$this->app_secret;
         $exchange_url.="&grant_type=fb_exchange_token";
         $exchnage_url.="&fb_exchange_token=".$this->getAccessToken();

         //get access token
         $content=file_get_contents($url);

         //if success ,the result will be string ( access_token=ACCESS_TOKEN&expires=EXPIRES)
         if($content != false)
         {
            $content=explode("&",$content);
            $content=$content[0];
            $content=explode("=",$content);

            $access_token=$content[1];

            if($access_token != false)
            {
               $this->setAccessToken($access_token);
               return true;
            }
         }


         return false;
      }

      function getFacebook()
      {
         return $this->facebook;
      }

      /*
      function getExpireDate()
      {
         $signedRequest = $this->facebook->getSignedRequest();
         return $signedRequest['expires'];
         //$expiresDate = date('c', $signedRequest['expires']);
         //return ($expiresDate);
      }
      */



      function setAccessToken($token)
      {
         if($token != false)
         {
            $this->facebook->setAccessToken($token); 
         }
      }

      /**
      * api call
      */
      function api($path,$method="GET",$params=array())
      {
         if($this->isLogin() == false)
         {
            throw new Exception("not login when call fql method");
         }

         try{
            $path="/".ltrim($path,"/");
            $result=$this->facebook->api($path,$method,$params);
         }
         catch(Exception $e)
         {
            $result=$e->getResult();
            $error=$result['error'];

            $this->setError($error);

            return false;
         }

         return $result;
      }

      /**
      * fql call
      */
      function fql($fql)
      {
         if($this->isLogin() == false)
         {
            throw new Exception("not login when call fql method");
         }

         try {
            $result = $this->facebook->api(array(
               'method' => 'fql.query',
               'query' => $fql,
            ));

            return $result;
            //return successReturn('',array('data'=>$result));

         }
         catch(FacebookApiException $e)
         { 
            //error_log($e->getType());
            //error_log($e->getMessage());
            $result=$e->getResult();
            $error=$result['error_msg'];

            $this->setError($error);

            return false;
            //return errorReturn($e->getMessage());
         }
      }


      /** fql helper functions **/
      function insights($object_id,$metric,$end_date,$period)
      {
         $fql="SELECT object_id,metric,end_time,period,value";
         $fql.=" FROM insights";
         $fql.=" WHERE object_id=$object_id";
         $fql.=" AND metric='$metric' ";
         $fql.=" AND end_time=end_time_date('$end_date') ";
         $fql.=" AND period=period('$period')";

         $data=$this->fql($fql);
         return $data;
      }

      /**
      * get user's pages
      */
      function getUserPages()
      {
         $uid=$this->getUserId();

         if($uid == false)
         {
            throw new Exception("can not get user id,please login first.");
         }

         //$fql="SELECT page_id, type from page_admin WHERE uid='$uid'";
         $fql="SELECT page_url,page_id,name,pic,type,has_added_app";
         $fql.=" FROM page WHERE page_id IN ";
         $fql.="(";
         $fql.="SELECT page_id From page_admin where uid=".$uid;
         $fql.=")";
         $fql.="order by name";

         $data=$this->fql($fql);

         return $data;
      }

      /**
      * get insights  values
      *
      * @param  string since_date  start date (included)
      * @param  string until_date  end date (included)
      * @return  Array  format:   array(
         array(
            value=>
            end_time=>2011-12-12T08:00:00+0000      
         )

         */
         function insightsValues($object_id,$metric,$since_date,$until_date)
         {
            $since=strtotime($since_date);
            $until=strtotime($until_date)+86400;

            $graph="/$object_id/insights/$metric?format=json&since=$since&until=$until";

            $data=$this->api($graph);

            if(is_array($data))
            {
               $values=array();
               foreach($data['data'] as $v)
               {
                  $values[$v['period']]=$v['values']; 
               }

               return $values;
            }


            return false;
         }

         /******** action methods **********/

         /**
         * add fb action
         * 
         * @param string  $path  format:  /{USER_ID|me}/{APP NAME SPACE}:{ACTION}
         * @param array   $params
         *
         * @return  integer  action_id or false
         */
         function addAction($path,$params)
         {
            $result=$this->api($path,"POST",$params);

            if($result == false)
            {
               return false;
            }

            $action_id=$result['id'];
            return $action_id;
         }

         /**
         * get action's information
         *
         * @param integer  $action_id  
         * 
         * @return array   action's information or false for error
         */
         function getAction($action_id)
         {
            $path="/".ltrim($action_id,"/");
            $result=$this->api($path,"GET");

            return $result;
         }

         /**
         * edit action's information
         */
         function editAction($action_id,$params=array())
         {
            $path="/".ltrim($action_id,"/");
            $result=$this->api($path,"POST",$params);

            return $result;
         }

         /**
         * delete action
         *
         * @return  true for success or false
         */
         function delteAction($action_id)
         {
            $path="/".ltrim($action_id,"/");
            $result=$this->api($path,"DELETE");

            return $result;
         }

         /*
         function likeAction()
         {
            ;
         }

         function commentAction()
         {
            ;
         }
         */

         /**
         * set fb error msg
         */
         function setError($error)
         {
            $this->error=$error;
         }

         /**
         * get fb error msg: 
         * format: array(
            'message'=>..
            'type'=>...
            'code'=>...
         )
         */
         function getError()
         {
            return $this->error;
         }

         /*******************/

         function getFriends($user_id='me')
         {
            $result=$this->api("$user_id/friends","GET");

            if($result != false)
            {
               return $result['data'];
            }

            return $result;
         }

         /******* detail methods **********/
         function pagePost($fb_page_id,$params)
         {
            if($this->isLogin() == false)
            {
               throw new Exception("not login when call getUser method");
            }

            //check params
            if(!isset($params['message']))
            {
               throw new Exception("page post misssing param: message");
            }

            $path="/".$fb_page_id.'/feed';

            $result=$this->api($path,'POST',$params);

            if(isset($result['id']))
            {
               //post id
               return $result['id'];
            }
            else
            {
               return false;
            }
         }

         function pagePhoto($fb_page_id,$params)
         {
            if($this->isLogin() == false)
            {
               throw new Exception("not login when call getUser method");
            }

            //check params
            if(!isset($params['message']))
            {
               throw new Exception("page photo misssing param: message");
            }

            if(!isset($params['source']))
            {
               throw new Exception("page photo misssing param: source");
            }

            $path="/".$fb_page_id.'/photos';

            $result=$this->api($path,'POST',$params);

            return $result;
         }

         function getPages($fb_user_id)
         {
            $fql="SELECT page_url,page_id,name,pic,type,has_added_app FROM  page ";
            $fql.="WHERE page_id IN";
            $fql.="(SELECT page_id From page_admin where uid='".$fb_user_id."') ";
            //$fql.="(SELECT page_id From page_admin where uid=me()) ";
            $fql.="order by name";

            $data=$this->fql($fql);
            //handle data
            if($data == false)
            {
               throw new Exception("facebook error:".$this->getError());
               //return array();
            }

            return $data;
         }

         function pageAccessToken($fb_page_id)
         {
            if($this->isLogin() == false)
            {
               throw new Exception("not login when call getUser method");
            }


            $path="/".$fb_page_id.'?fields=access_token';

            $result=$this->api($path);

            if(isset($result['access_token']))
            {
               return $result['access_token'];
            }
            else
            {
               return false;
            }
         }

      }
