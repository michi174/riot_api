<?php

namespace RiotApi;

require_once 'autoloader.func.php';

class RiotAPI
{
    //summoner: pE1cLS-RDFQAhH4Bkd8eOSXfjFF2Q61d-NE_xqIisxD3KeA;
    //puuid: N2ZYmZa7JF2oXnRUk3kFKYXPKYkqSTDngGegSgrG1GMz2iArBFbBUg8oZ54J-VMFHAQvuO98Rxt9aw

    /*
     * Your API Key here;
     */
    const API_KEY = "RGAPI-61963597-82a1-41ac-9f0a-3e006fdc9deb";


    const MAX_MULTI_REQUESTS  = 10;
    const OUTPUT_TYPE_JSON = "JSON";
    const OUTPUT_TYPE_ECHO = "ECHO";
    const OUTPUT_TYPE_ARRAY = "ARRAY";
    const API_VERSION = "v4";
    const API_URL = 'api.riotgames.com/lol';
    const DD_URL = "ddragon.leagueoflegends.com";

    private $regionEndpoints = array();
    private $region = array("region" => "euw", "platform" => "euw1", "routing" => "europe");
    private $language = "de_DE";
    private $requestResults;
    private $outputFormat = self::OUTPUT_TYPE_JSON;
    private $num_requests = 0;
    private $summoner = null;
    private $params = null;
    private $apis = array();
    private $api = null;


    public function __construct($queryString = null)
    {
        $this->setRegionEndpoints();
        $this->setApis();

        if (isset($queryString)) {
            $this->setOptions($queryString);
        }
    }

    public function __get($prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }
    }

    /**
     * Gibt die URL String der Static Data API zur�ck.
     * 
     * @param string $api
     * Kann nur CDN oder API sein.
     * @param string $version
     * Die aktuelle Version des LOL Clients
     * @param string $namespace
     * Data oder Img
     * @param string $locale
     * Sprache welche verwendet werden soll (de_DE)
     * @param string $request
     * ChampionFull.json oder Atrox.json wenn method angef�hrt wird.
     * @param string $method
     * @return string
     */
    public function getStaticData(): string
    {

        $method = "";

        if (array_key_exists("method", $this->params)) {
            if ($this->params["method"] != "") {
                $method = "/" . $this->params["method"];
            }
        }

        $url = "https://"
            . self::DD_URL
            . "/" . $this->params["api"]
            . "/" . $this->params["version"]
            . "/data"
            . "/" . $this->params["locale"]
            . $method
            . "/" . $this->params["request"] . ".json";


        //return $url;
        return $this->getJSONFromURL($url);
    }



    public function execute()
    {
        if (isset($this->api)) {
            if (array_key_exists($this->api, $this->apis)) {
                $class = $this->apis[$this->api];
                $namespace = __NAMESPACE__;

                $new = $namespace . "\\" . $class;

                $object = new $new($this);



                return $object->getApiUrl();
            } else {
                die("API is not supported!");
            }
        } else {
            die("No API given to request against!");
        }
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function getApiUrl()
    {
    }

    public function log($text)
    {
        $file = 'requests.log';
        $datetime = new \DateTime();
        $_text = date("Y-m-d H:i:s") . " - $text";
        //file_put_contents($file, $_text, FILE_APPEND | LOCK_EX);
    }




    protected function request($urls)
    {
        if (!is_array($urls)) {
            $temp_url = array();
            array_push($temp_url, $urls);
            $urls = $temp_url;
        }

        //echo(var_dump($urls));

        $curl_handles = array();
        $results = array();

        $is_busy = null;

        $multi_curl_handle = curl_multi_init();

        //var_dump($multi_curl_handle);

        //Curl handles erzeugen
        foreach ($urls as $id => $url) {
            $curl_handles[$id] = curl_init();
            curl_setopt($curl_handles[$id], CURLOPT_URL, $urls[$id]);
            curl_setopt($curl_handles[$id], CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($curl_handles[$id], CURLOPT_HEADER, true);
            curl_setopt($curl_handles[$id], CURLOPT_HTTPHEADER, array(
                "X-Riot-Token:" . self::API_KEY
            ));


            curl_multi_add_handle($multi_curl_handle, $curl_handles[$id]);

            if ($id === self::MAX_MULTI_REQUESTS - 1) {
                break;
            }
            //echo "log\n";
            //var_dump($multi_curl_handle);      

            $this->log("request against: $url\n ");
        }
        do {

            $exec_response = curl_multi_exec($multi_curl_handle, $is_busy);
            //var_dump($multi_curl_handle);
            //echo "do\n";

        } while ($is_busy > 0);

        $timeout = 2;
        $interrupt = 1;
        $duration = 0;

        $last_status_code   = 0;

        foreach ($curl_handles as $id => $handle) {

            $status_code = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
            http_response_code($status_code);

            $this->log($status_code . "\n");
            $this->log(var_export(\curl_getinfo($handle), true));

            //var_dump($status_code."\n");

            //echo $status_code;

            switch ($status_code) {
                case 200:
                    $results[$id]["body"] = curl_multi_getcontent($handle);
                    $results[$id]["status"] = $status_code;

                    break;
                case 429:
                    if ($status_code === 429) {
                        //Freeze it to reset API Request Limitations
                        sleep($interrupt);
                        $duration = $duration + $interrupt;

                        //Retry the API Request
                        $temp_result = $this->singleRequest($urls[$id]);
                        $temp_status_code = $temp_result["status"];

                        if ($temp_status_code === 200) {
                            $results[$id] = $temp_result;
                            $last_status_code = 200;
                        }
                    }
                    break;
                default:
                    $results[$id]["body"] = curl_multi_getcontent($handle);
                    $results[$id]["status"] = $status_code;
                    continue;
            }
        }

        curl_multi_close($multi_curl_handle);

        $this->requestResults = $results;
    }

    public function singleRequest($url)
    {
        $this->log("single request against: $url\n ");
        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($handle);
        $status_code = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $this->log($status_code . "\n");
        $return = array("body" => $result, "status" => $status_code);

        return $return;
    }

    protected function setOutput(RiotAPI $output)
    {
        return null;
    }

    protected function getSingleResult($resultId = 0, $format = self::OUTPUT_TYPE_JSON)
    {
        if (array_key_exists($resultId, $this->requestResults)) {
            if ($this->isJson($this->requestResults[$resultId]["body"]))
                return $this->requestResults[$resultId]["body"];
            else
                return "error";
        }
    }

    protected function buildApiUrl($apiString, array $params)
    {
    }

    public function getResult($urls)
    {
        //var_dump($urls);
        $this->request($urls);

        return $this->requestResults;
    }


    public function setRegion($region)
    {
        if (array_key_exists($region, $this->regionEndpoints)) {
            $this->region = $this->regionEndpoints[$region];
        }
    }

    public function getJSONFromURL($url)
    {
        $this->request($url);

        return $this->getSingleResult();
    }

    protected function setSummoner(Summoners $summoner)
    {
        $this->summoner = $summoner;
    }

    public function getSummoner()
    {
        return $this->summoner;
    }

    /**
     * Liest den QueryString des Browsers ein und setzt die Optionen f�r die API
     * 
     * @param String $queryString
     */
    public function setOptions(String $queryString)
    {
        $options = explode('&', $queryString);
        $_options = array();

        foreach ($options as $option) {
            $opt = explode('=', $option);
            $_options[$opt[0]] = (isset($opt[1]) ? $opt[1] : "");
        }

        $this->params = $_options;

        if (array_key_exists("api", $_options)) {
            $this->api = $this->params["api"];
        }

        foreach ($this->params as $param => $value) {
            if (property_exists($this, $param)) {
                if (method_exists($this, "set" . $param)) {
                    $this->{"set" . $param}($value);
                } else {
                    $this->{$param} = $value;
                }
            }
        }
    }

    public function getOptions($option = null)
    {
        if (isset($option))
            return $this->{$option};
    }

    public function debugInfo()
    {
        //var_dump($this);
    }

    private function isJson($JsonString)
    {
        $res = json_decode($JsonString);

        return (json_last_error() === JSON_ERROR_NONE);
    }

    private function setVersions()
    {
    }

    private function setRegionEndpoints()
    {
        $this->regionEndpoints["br"] = array("region" => "br", "platform" => "br1", "routing" => "americas");
        $this->regionEndpoints["eune"] = array("region" => "eune", "platform" => "eun1", "routing" => "europe");
        $this->regionEndpoints["euw"] = array("region" => "euw", "platform" => "euw1", "routing" => "europe");
        $this->regionEndpoints["jp"] = array("region" => "jp", "platform" => "jp1", "routing" => "asia");
        $this->regionEndpoints["kr"] = array("region" => "kr", "platform" => "kr", "routing" => "asia");
        $this->regionEndpoints["lan"] = array("region" => "lan", "platform" => "la1", "routing" => "americas");
        $this->regionEndpoints["las"] = array("region" => "las", "platform" => "la2", "routing" => "americas");
        $this->regionEndpoints["na"] = array("region" => "na", "platform" => "na1", "routing" => "americas");
        $this->regionEndpoints["oce"] = array("oce" => "eune", "platform" => "oc1", "routing" => "americas");
        $this->regionEndpoints["tr"] = array("region" => "tr", "platform" => "tr1", "routing" => "europe");
        $this->regionEndpoints["ru"] = array("region" => "ru", "platform" => "ru", "routing" => "europe");
        $this->regionEndpoints["pbe"] = array("region" => "pbe", "platform" => "pbe1");
    }

    private function setApis()
    {
        $this->apis['matchlists'] = "Matchlists";
        $this->apis['summoners'] = "Summoners";
        $this->apis['matches'] = "Matches";
        $this->apis['positions'] = "Positions";
        $this->apis['entries'] = "Entries";
        $this->apis['timelines'] = "Timelines";
    }
}
