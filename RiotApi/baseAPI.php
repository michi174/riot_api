<?php
namespace RiotApi;

use RiotApi\IRiotApis;
use RiotApi\RiotAPI;

abstract class baseAPI implements IRiotApis
{
    
    protected $API_NAMESPACE;
    protected $methods;
    protected $region;
    protected $apiVersion = "v4";
    protected $apiUrl;
    protected $method = null;
    protected $queryString = null;
    protected $params;

    public function __construct(RiotAPI $api)
    {
        $this->params = $api->params;
        $this->region = $api->getRegion();
        $this->apiUrl = $api::API_URL;
        $this->apiVersion = $api::API_VERSION;
        $this->setParam2Property("method", "method");
        
        if(!isset($this->method))
        {
            $this->method = "";
        }
    }

    /**
     * Legt einen Parameter als Eigenschaft der Klasse fest
     * 
     * @param unknown $param
     * @param unknown $prop
     */
    private function setParam2Property($param, $prop)
    {
        if(isset($this->params[$param]))
        {
            $this->{$prop} = $this->params[$param];
        }
    }
    
    protected function not2SeperatorHelper($not, $value)
    {
        if($value !== $not)
        {
            return "/";
        }
        else
        {
            return "";
        }
    }
    
    protected function getQueryNameString()
    {
        if(isset($this->params[$this->methods[$this->method]["queryname"]]))
            return $this->params[$this->methods[$this->method]["queryname"]];
            else
            {
                return "";
            }
    }
    
    public function getApiUrl() : string
    {
        //die($this->buildApiUrl());
        return $this->buildApiUrl();
    }
    
    abstract protected function buildApiUrl() : string;
}

