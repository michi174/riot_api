<?php
namespace RiotApi;

class Matches extends baseAPI
{
    protected $API_NAMESPACE = "match";
    
    protected $methods = array(
        "" => array(
            "name" => "",
            "queryname" => "matchid"
        )
    );

    public function __construct(RiotAPI $api)
    {
        parent::__construct($api);
    }

    protected function buildApiUrl():string
    {
        if(isset($this->method))
        {
            return "https://".$this->region["platform"]
            .".".$this->apiUrl."/".$this->API_NAMESPACE
            ."/".$this->apiVersion
            ."/".strtolower(substr(strrchr(__CLASS__, "\\"), 1))
            .$this->not2SeperatorHelper("", $this->method).$this->methods[$this->method]["name"]
            ."/".$this->getQueryNameString();
        }
        else
        {
            die("Error: No method set in given URL! ".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'].debug_print_backtrace());
        }
    }
}

