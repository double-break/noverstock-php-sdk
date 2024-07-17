<?php

namespace DoubleBreak\Noverstock\Sdk;

use GuzzleHttp\Client;

class TokenProvider
{
    const ACTOR_TYPE_APP = 'APP';
    const ACTOR_TYPE_USER = 'USER';

    private array $config;
    private ?string $accessToken = null;
    private ?string $authToken = null;
    private Client $client;

    public function __construct(array $config = [])
    {
        $this->config = $config;

        if ($this->config['actor']['type'] === self::ACTOR_TYPE_APP) {
            $this->authToken = $this->config['actor']['authToken'] ?? 'invliad token';
        }


        $this->client = new Client();
    }


    /**
     * @param $actor
     * @return void
     * @throws \Exception
     */
    public function authenticate($actor): void
    {
        $actorType = strtoupper($actor['type'] ?? 'INVALID');

        if ($actorType === self::ACTOR_TYPE_APP) {
            //authenticate app using app authentication token
            throw new \Exception('Applications cannot authenticate. Use user authentication instead.');
        } elseif ($actorType === self::ACTOR_TYPE_USER) {
            $data = $this->authenticateUser($actor);
            $this->authToken = $data['authToken'];
        } else {
            throw new \Exception('Invalid authentication actor');
        }
    }


    private function authenticateUser($actor): array
    {

        $url = $this->config['authUri'] . '/authenticate';
        $response = $this->client->request('POST', $url, [
            'json' => [
                'email' => $actor['email'],
                'password' => $actor['password'],
                'scopes' => $actor['scopes'],
                'domain' => 'noverstock'
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }


    private function authorizeUser(): void
    {
        $url = $this->config['authUri'] . '/authorize';
        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->authToken
            ],
            'json' => [
                'realmUuid' => $this->config['actor']['realmUuid'],
                'scopes' => $this->config['actor']['scopes'],
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->accessToken = $data['accessToken'];
    }


    private function authorizeApp(): void
    {
        $url = $this->config['authUri'] . '/app/authorize';
        $response = $this->client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->authToken
            ],
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->accessToken = $data['accessToken'];
    }

    public function authorize() {
        if (is_null($this->authToken)) {
            $this->authenticate($this->config['actor']);
        }

        if ($this->config['actor']['type'] === self::ACTOR_TYPE_APP) {
            $this->authorizeApp();
        } elseif ($this->config['actor']['type'] === self::ACTOR_TYPE_USER) {
            $this->authorizeUser();
        }

    }

    public function getAccessToken(): string
    {
        if (is_null($this->accessToken)) {
            $this->authorize();
        }
        return $this->accessToken;
    }

}