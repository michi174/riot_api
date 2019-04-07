<?php
namespace RiotApi;

use RiotApi\RiotAPI;

interface IRiotApis
{
    /**
     * �bergibt das Api Object an die zust�ndige API.
     * 
     * @param RiotAPI $api
     */
    public function __construct(RiotAPI $api);
    
    
    /**
     * Baut den String auf, gegen den die RIOT Api abgefragt wird.
     * 
     * @param RiotAPI $api
     * @return string RIOT Api URL
     */
    public function getApiUrl() : string;
}