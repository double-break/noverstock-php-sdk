<?php

namespace DoubleBreak\Noverstock\Sdk\Client;


class GenericClient extends AbstractClient
{


    public function request(string $method, string $url, array $params = [])
    {

        $response = parent::request($method, "{$this->baseUrl}/{$url}", $params);
        return $response;

    }
}