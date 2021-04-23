<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class ApiClient
 * @package App
 */
class ApiClient
{
    private object $client;
    private array $headers;
    private GuzzleException $errorResponse;
    private bool $dataOnly = false;

    /**
     * ApiClient constructor.
     */
    public function __construct()
    {
        $this->setHeaders(); //set default headers
        $this->client = new Client($this->getHeaders());
    }


    /**
     * @param string $url
     * @param array $data
     * @return false|array
     */
    public function get(string $url, array $data): false|array
    {
        try {
            $response = $this->client->get($url, ['query' => $data]);
            $response = json_decode($response->getBody()->getContents(), true);

            if ($this->dataOnly) {
                $response = $response['data'];
            }

        } catch (GuzzleException $guzzleException) {
            $this->errorResponse = $guzzleException;

            error_log(__CLASS__ . __METHOD__ . ' - ' .  $this->errorResponse);

            $response = false;
        }

        return $response;
    }


    /**
     * @param string $url
     * @param array $data
     * @return false|array
     */
    public function post(string $url, array $data): false|array
    {
        try {
            $response = $this->client->post($url, ['body' => json_encode($data), 'headers' => $this->getHeaders()]);

            $response = json_decode($response->getBody()->getContents(), true);

            if ($this->dataOnly) {
                $response = $response['data'];
            }

        } catch (GuzzleException $guzzleException) {
            $this->errorResponse = $guzzleException;

            error_log(__CLASS__ . __METHOD__ . ' - ' .  $this->errorResponse);

            $response = false;
        }

        return $response;
    }


    /**
     * @param bool $value
     * @return bool
     */
    public function setDataOnly(bool $value): bool
    {
        return $this->dataOnly = $value;
    }


    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }


    /**
     * @param array $headers
     */
    public function setHeaders(array $headers = []): void
    {
        $this->headers = (!empty($headers)) ? $headers : ['Content-Type' => 'application/json', 'Accept' => 'application/json'];
    }
}