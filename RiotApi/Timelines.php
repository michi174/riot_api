<?php

namespace RiotApi;

class Timelines extends baseAPI
{

    protected $API_NAMESPACE = "match";

    protected $methods = array(
        "by-match" => array(
            "name" => "by-match",
            "queryname" => "matchId"
        )
    );

    public function __construct(RiotAPI $api)
    {
        parent::__construct($api);
    }

    protected function buildApiUrl(): string
    {
        if (isset($this->method)) {
            $url = "https://" . $this->region["platform"]
                . "." . $this->apiUrl . "/" . $this->API_NAMESPACE
                . "/" . $this->apiVersion
                . "/" . strtolower(substr(strrchr(__CLASS__, "\\"), 1))
                . "/" . $this->methods[$this->method]["name"]
                . "/" . $this->getQueryNameString();

            return $url;
        } else {
            die("Error: No method set in given URL! " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] . debug_print_backtrace());
        }
    }
}
