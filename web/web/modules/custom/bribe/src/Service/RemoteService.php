<?php

namespace Drupal\bribe\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class RemoteService
{
    /**  
     * @var \GuzzleHttp\Client  
     */
    protected Client $httpClient;

    private $promoServiceUrl;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->promoServiceUrl = $_ENV['PROMO_SERVICE_URL'];
    }
    public function list($data)
    {
        return $this->request('query',$data);
    }
    public function read($data)
    {
        return $this->request('query', $data);
    }
    public function create($data)
    {
        return $this->request('mutation', $data);
    }
    public function update($data)
    {
        return $this->request('mutation', $data);
    }

    public function delete($data)
    {
        return $this->request('mutation', $data);
    }

    protected function request($type, $data)
    {
        $reqData = $this->buildQuery($type, $data);

        try {
            $response = $this->httpClient->post($this->promoServiceUrl, [
                'json' => ['query' => $reqData],
            ]);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            return $e;
        }
    }

    protected function buildQuery($type, $data)
    {
        // Build your GraphQL query based on the type and data provided.
        // This is a simplified example; you will need to adjust it based on your API's requirements.
        if ($type === 'mutation') {
            return sprintf($data['schema'], json_encode($data['data']));
        } elseif ($type === 'query') {
            return sprintf($data['schema'], $data['data']);
        }
        return '';
    }

}