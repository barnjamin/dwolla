<?php


class dwolla{

    private $options = array(
		"client_id"=>"",
		'client_secret'=>""
	);
    public $redirect_uri = "";
    public $permissions = array("send", "transactions", "balance", "request", "contacts", "accountinfofull");
    private $oauth_token;
    private $end_point;
    public $last_url;
    
    function __construct($a_t=null) {
	if(!empty($a_t)){
                $this->oauth_token = $a_t;
        }
    }
	
    ///returns the url that the user must visit and login and approve and be returned 
    //with a code to make the second request
    function auth_url(){  
        $scope = "";
        foreach($this->permissions as $p){
            $scope .= $p."|";
        }
        $scope = urlencode(rtrim($scope, '|'));
        
        $url = "https://www.dwolla.com/oauth/v2/authenticate?client_id=".urlencode($this->options['client_id']);
        $url .= "&response_type=code&redirect_uri=".urlencode($this->redirect_uri)."&scope=".$scope;
        return $url;
    }
    
    //Call this from the page you are redirected to with the code get parameter
    //pass in the code and the redirect_uri
    function get_oauth_token($code = null){
        $result = array('success'=>"false", "reason"=>"no code");
        if(empty($code)) return $result;
        
        $this->end_point = 'v2/token';
        $params = array_merge($this->options, array('grant_type'=>'authorization_code',
                        'code'=>$code, 'redirect_uri'=>$this->redirect_uri));
        
        $url = $this->build_url($params);
        $result = $this->execute_query('GET', $url);//MAYBE POST?
        

        $this->oauth_token = $result['access_token'];
        $_SESSION['oauth_token']=$this->oauth_token; 
        return $result;
    }
    
    function set_oauth($token=null){
        $this->oauth_token = $token;
    }
    
    
	///Register
	function create_account($data){
        $this->end_point = 'rest/Register/';
        
        if(isset($data['acceptTerms'])) $data['acceptTerms'] = "true";
        
	$url = $this->build_url($this->options);
        $data = array_merge($this->options, $data);
     
        $result = $this->execute_query('POST', $url, $data);
    	return $result;
	}

    
    //"balance" scope
    function balance(){
        $this->end_point = 'rest/balance';
        $url = $this->build_url();
        $result = $this->execute_query('GET', $url);
        return $result;
    }
    
    //"contacts" scope
    function user_contacts($params){
        $this->end_point = 'rest/contacts';
        $url = $this->build_url();
        $result = $this->execute_query('GET', $url);
        return $result;
    }
    function nearby($params=null){
        $this->end_point = 'rest/contacts/nearby';
        $url = $this->build_url($params);
        $result = $this->execute_query('GET', $url);
        return $result;
    }
    
    //"transactions" and "send" scopes
    function transactions($ep=null, $params=null, $data=null){
        $this->end_point = 'rest/transactions';
        switch($ep){
            case "details": $this->end_point .= "/".$params['id'];
                            $method = "GET";
                            break;
            case "stats":   $this->end_point .= '/stats';
                            $method = "GET";
                            break;
            case "send":    $this->end_point .= '/send';
                            $method = "POST";
                            break;
            case "request": $this->end_point .= '/request';
                            $method = "POST";
                            break;
            default:        $method = "GET";
        }
        $params = $this->options;
        $url = $this->build_url($params);
        $result = $this->execute_query($method, $url, $data);
        return $result;
    }
    
    
    //"accountinfofull" scope
    function user($user=null){
        $this->end_point = "rest/users";
        if(!empty($user)) $this->end_point .= '/'.$user;
        $url = $this->build_url($this->options);
        $result = $this->execute_query('GET', $url);
        return $result;
    }

	private function execute_query($method, $url, $data=null, $header=false){
        
        $ch = curl_init();
        
        if(!empty($data)){
            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
	curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	
	$result = curl_exec($ch);
     
	$result = json_decode($result, true);
		
	curl_close($ch);
	return $result;
	
	}
	
	/*
	*Builds the RESTful url of what youre trying to access
	*/
	private function build_url($param=null){
	
		$url = 'https://www.dwolla.com/oauth/' . $this->end_point;
		$url .= '?';
		if(!empty($param) && count($param)>0){
			foreach($param as $key=>$value){
				$url.= $key.'='.urlencode($value).'&';
			}
		}
         if(!empty($this->oauth_token)){
                $url .= "oauth_token=".urlencode($this->oauth_token);
        }else $url = rtrim($url, '&');
		
        $this->last_url = $url;
        
		return $url;
	}
	
}



?>