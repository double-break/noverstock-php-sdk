<?php

namespace DoubleBreak\Noverstock\Sdk;

use DoubleBreak\Noverstock\Sdk\Client\AbstractClient;

class ClientFactory
{
    private array $config;
    private TokenProvider $tokenProvider;



    private function resolveBaseUrls()
    {

        $domain = $this->config['domain'] ?? 'app.noverstock.eu';

        $baseUrls = $this->config['baseUrls'] ?? [];

        //generic
        if (empty($baseUrls['Generic'])) {
            $baseUrls['Generic'] = "https://{$domain}";
        }

        if (empty($baseUrls['Orders'])) {
            $baseUrls['Orders'] = "https://{$domain}/orders-api";
        }
        //products
        if (empty($baseUrls['Products'])) {
            $baseUrls['Products'] = "https://{$domain}/products-api";
        }
        //listings
        if (empty($baseUrls['Listings'])) {
            $baseUrls['Listings'] = "https://{$domain}/listings-api";
        }
        //portal
        if (empty($baseUrls['Portal'])) {
            $baseUrls['Portal'] = "https://{$domain}/portal-api";
        }
        //reaccess
        if (empty($baseUrls['Reaccess'])) {
            $baseUrls['Reaccess'] = "https://{$domain}/reaccess-api";
        }
        $this->config['baseUrls'] = $baseUrls;
    }


    public function __construct(array $config = [])
    {

        $dotConf = [];
        if (file_exists(__DIR__ . '/../.config')) {
            $dotConf = json_decode(file_get_contents(__DIR__ . '/../.config'), true);
        }
        $dotConf = is_array($dotConf) ? $dotConf : [];

        $this->config = array_merge($config, $dotConf);
        $this->resolveBaseUrls();
        $this->tokenProvider = new TokenProvider([
            'authUri' => $this->config['baseUrls']['Reaccess'] ?? '',
            'actor' => $this->config['actor']
        ]);
    }

    public function createClient($clientName): AbstractClient
    {
        $accessToken = $this->tokenProvider->getAccessToken();
        $fqn = 'DoubleBreak\\Noverstock\\Sdk\\Client\\' . $clientName . 'Client';

        return new $fqn([
            'baseUrl' => $this->config['baseUrls'][$clientName] ?? '',
            'accessToken' => $accessToken
        ]);
    }
}