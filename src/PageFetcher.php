<?php

namespace Seatsio;

class PageFetcher
{
    private $url;
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;
    private $pageCreator;
    private $queryParams;

    public function __construct($url, $client, $pageCreator, $queryParams = null)
    {
        $this->url = $url;
        $this->client = $client;
        $this->pageCreator = $pageCreator;
        $this->queryParams = $queryParams;
    }

    public function fetchAfter($afterId, $queryParams, $pageSize)
    {
        if ($afterId !== null) {
            $queryParams['start_after_id'] = $afterId;
        }
        return $this->fetch($queryParams, $pageSize);
    }

    public function fetchBefore($beforeId, $queryParams, $pageSize)
    {
        if ($beforeId !== null) {
            $queryParams['end_before_id'] = $beforeId;
        }
        return $this->fetch($queryParams, $pageSize);
    }

    public function fetch($queryParams, $pageSize)
    {
        if ($pageSize) {
            $queryParams['limit'] = $pageSize;
        }
        $res = $this->client->get($this->url, ['query' => array_merge($queryParams, $this->queryParams)]);
        $json = \GuzzleHttp\json_decode($res->getBody());
        $mapper = SeatsioJsonMapper::create();
        return $mapper->map($json, $this->pageCreator->__invoke());
    }

}