<?php
namespace Flacox;

class CurlManager
{
    private $curlHandle;
    private $url = "https://tuleap-web.tuleap-aio-dev.docker/";
    
    public function __construct()
    {
        $this->curlHandle = curl_init();
        $this->setup();
    }
    
    public function execute()
    {
        return curl_exec($this->curlHandle);
    }
    
    public function checkHandleError()
    {
        return curl_errno($this->curlHandle);
    }
    
    public function showError()
    {
        return curl_error($this->curlHandle);
    }
    
    function setup()
    {
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_HTTPGET, true);
        curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, false);
    }
    
    public function setUrl($extensionUrl)
    {
        curl_setopt($this->curlHandle, CURLOPT_URL, $this->url.$extensionUrl);
    }
    
    public function setHeaders($id, $token)
    {
        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "X-Auth-Token: " . $token;
        $headers[] = "X-Auth-UserId: " . $id;
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $headers);
    }
    
    public function getCurlHandle()
    {
        return $this->curlHandle;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
}
?>
