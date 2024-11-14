<?php

namespace Drupal\bribe\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;

class RemoteService
{
    /**  
     * @var \GuzzleHttp\ClientInterface  
     */
    protected $httpClient;

    private $promoServiceUrl;

    public function __construct(ClientInterface $http_client)
    {
        $this->httpClient = $http_client;
        $this->promoServiceUrl = $_ENV['PROMO_SERVICE_URL'];
    }
    public function create($data)
    {
        return $this->request('mutation', $data);
    }

    public function read($data)
    {
        return $this->request('query', ['id' => $data]);
    }

    public function update($data)
    {
        return $this->request('mutation', $data);
    }

    public function delete($data)
    {
        return $this->request('mutation', ['id' => $data]);
    }

    protected function request($type, $data)
    {
        $query = $this->buildQuery($type, $data);

        try {
            $response = $this->httpClient->post($this->promoServiceUrl, [
                'json' => ['query' => $query],
            ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return NULL;
        }
    }

    protected function buildQuery($type, $data)
    {
        // Build your GraphQL query based on the type and data provided.
        // This is a simplified example; you will need to adjust it based on your API's requirements.
        if ($type === 'mutation') {
            return sprintf('mutation { createItem(input: %s) { id } }', json_encode($data));
        } elseif ($type === 'query') {
            return sprintf('query { item(id: "%s") { id name } }', $data['id']);
        }
        return '';
    }

}