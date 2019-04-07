<?php

namespace RiotApi;
//require 'baseAPI.php';
use RiotApi\RiotAPI;

class Summoners extends baseAPI
{
    protected $API_NAMESPACE = "summoner";
    protected $methods = array(
        "by-name" => array(
            "name" => "by-name", 
            "queryname" => "name"
        ),
        "by-account" => array(
            "name" => "by-account", 
            "queryname" => "account"
        ),
        "by-puuid" => array(
            "name" => "by-puuid", 
            "queryname" => "puuid"
        ),
        "by-summoner-id" => array(
            "name" => "", 
            "queryname" => "summoner-id"
        )
    );
    protected $queryString;
        
    
    public function __construct(RiotAPI $api)
    {
        parent::__construct($api);
    }
    
    
    protected function buildApiUrl() :string
    {
        if(isset($this->method))
        {
            return "https://".$this->region["platform"]
            .".".$this->apiUrl."/".$this->API_NAMESPACE
            ."/".$this->apiVersion
            ."/".strtolower(substr(strrchr(__CLASS__, "\\"), 1))
            ."/".$this->methods[$this->method]["name"]
            .$this->not2SeperatorHelper("by-summoner-id", $this->method)
            .$this->getQueryNameString();
        }
        else
        {
            die("Error: No method set in given URL! ".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'].debug_print_backtrace());
        }
    }
}