<?php

namespace DoubleBreak\Noverstock\Sdk\Client;

class AbstractClient
{

    private string $accessToken;
    protected string $baseUrl;
    private \GuzzleHttp\Client $http;

    public function __construct(array $config = [])
    {
        $this->accessToken = $config['accessToken'] ?? '';
        $this->baseUrl = $config['baseUrl'] ?? '';
        $this->http = new \GuzzleHttp\Client();
    }


    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function decodeAccessToken(): array
    {
        $token = explode('.', $this->accessToken);

        return [
            'meta' => json_decode(base64_decode($token[0]), true),
            'payload' => json_decode(base64_decode($token[1]), true)
        ];
    }


    public function request(string $method, string $url, array $params = [])
    {
        //prepare url - replace {something} with $params['path']['something']
        $url = preg_replace_callback('/\{([a-zA-Z0-9_]+)}/', function ($matches) use ($params) {
            return $params['path'][$matches[1]] ?? $matches[0];
        }, $url);


        //replace double / with single /
        $url = preg_replace('/\/+/', '/', $url);
        $url = str_replace(':/', '://', $url);

        $response =  $this->http->request($method, $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            'query' => $params['query'] ?? [],
            'json' => $params['body'] ?? [],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}