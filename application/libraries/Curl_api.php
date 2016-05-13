<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Curl_api {
    
    public $info;
    public $header;
    private $url;
    private $params;
    private $response;
    private $request;
    private $requestHeaders;
    private $responseHeaders;
    private $isGet;
    private $isUsingUserAgent;
    private $userAgent;

    public function __construct()
    {

//        $this->url              = $url;
//        $this->isGet            = false; 
//        $this->isUsingUserAgent = false;
//        $this->request          = $url;
//        $this->userAgent        = "Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 (.NET CLR 3.5.30729) (Prevx 3.0.5)";
    }
    
    public function setGet($isGet)
    {
        $this->isGet = $isGet;
    }
    
    public function set_url($url){
        $this->url              = $url;
        $this->isGet            = false; 
        $this->isUsingUserAgent = false;
        $this->request          = $url;
        $this->userAgent        = "Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 (.NET CLR 3.5.30729) (Prevx 3.0.5)";
    }
    
    public function setIsUsingUserAgent($isUsingUserAgent) {
        $this->isUsingUserAgent = $isUsingUserAgent;
    }
    
    public function setParams($params)
    {
        $this->params = $params;
    }

    public function setData() 
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        if ( !$this->isGet ) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $this->params); 
            
            $this->request  = $this->params;   
        } else {
            $this->request  = $this->url;
        }
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_VERBOSE, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_TIMEOUT, 80);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 80);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, array($this,'readResponseHeader'));
        
        $this->response = curl_exec($curl);
        
        if(curl_errno($curl))
		{
			//echo 'Curl error: ' . curl_error($curl) . ' - '. curl_errno($curl);
		}
        //$this->info = curl_getinfo($curl);
        $this->requestHeaders =curl_getinfo($curl,CURLINFO_HEADER_OUT); 
        curl_close($curl);
    }
    
    public function getRequest() 
    {
        return $this->request;
    }

    public function getResponse() 
    {
        return $this->response;
    }
    
    public function getResponseHeaders() 
    {
        return $this->responseHeaders;
    }
    
    public function getRequestHeaders() 
    {
        return $this->requestHeaders;
    }
    
    public function readResponseHeader($curl, $header) {
        $this->responseHeaders .= $header;
        return strlen($header);
    }

    // Other functions can be added to retrieve other information.
}
?>