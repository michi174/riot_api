<?php
namespace RiotApi;

class Matches extends baseAPI
{
    protected $API_NAMESPACE = "match";
    
    protected $methods = array(
        "match" => array(
            "name" => "",
            "queryname" => "matchid"
        ),
        "by-puuid" => array(
            "name" => "by-puuid",
            "queryname" => "puuid"
        ),
        "timeline" => array(
            "name" => "",
            "queryname" => "matchid"
        )

    );

    public function __construct(RiotAPI $api)
    {
        parent::__construct($api);
        $this->apiVersion = "v5";
        
    }

    protected function buildApiUrl():string
    {
        $ret = null;

        if(isset($this->method))
        {
            switch($this->method){
                case "match":
                    $ret = $this->match();
                    break;
                case "by-puuid":
                    $ret = $this->byPuuid();
                    break;
                case "timeline":
                    $ret = $this->timeline();
                    break;
                default:
                    die("ERROR: Given method ".$this->method." is not supported!");
            }
        }
        else
        {
            die("Error: No method set in given URL! ".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'].debug_print_backtrace());
        }

        //die("$ret");
        return $ret;
    }

    protected function match(){
        return "https://".$this->region["routing"]
        .".".$this->apiUrl."/".$this->API_NAMESPACE
        ."/".$this->apiVersion
        ."/".strtolower(substr(strrchr(__CLASS__, "\\"), 1))
        .$this->not2SeperatorHelper("", $this->methods[$this->method]["name"]).$this->methods[$this->method]["name"]
        ."/".$this->getQueryNameString();
    }

    protected function byPuuid(){
        return "https://".$this->region["routing"]
        .".".$this->apiUrl."/".$this->API_NAMESPACE
        ."/".$this->apiVersion
        ."/".strtolower(substr(strrchr(__CLASS__, "\\"), 1))
        .$this->not2SeperatorHelper("", $this->method).$this->methods[$this->method]["name"]
        ."/".$this->getQueryNameString()
        ."/ids?start=0&count=10";        
    }

    protected function timeline(){
        return "https://".$this->region["routing"]
        .".".$this->apiUrl."/".$this->API_NAMESPACE
        ."/".$this->apiVersion
        ."/".strtolower(substr(strrchr(__CLASS__, "\\"), 1))
        .$this->not2SeperatorHelper("", $this->methods[$this->method]["name"]).$this->methods[$this->method]["name"]
        ."/".$this->getQueryNameString()
        ."/".$this->method;
    }
}

