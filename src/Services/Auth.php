<?php

namespace App\Services;

use App\ApiClient;
use App\Repositories\AuthRepository;
use Throwable;

/**
 * Class Auth
 * @package App\Services
 *
 * TODO: add DTO, introduce apiClient
 */
class Auth extends Service
{
    private string $accessToken = '';
    protected array $config;

    const VALID = 3600;

    /**
     * Auth constructor.
     * @param array $config
     * @param string $storagePath
     */
    public function __construct(array $config, string $storagePath)
    {
        $this->config = $config;

        $this->repository = new AuthRepository($storagePath);
    }


    /**
     * @param string $url
     * @param array $data
     * @return array
     */
    public function requestAccessToken(string $url, array $data): array
    {
        $ApiClient = new ApiClient();

        return $ApiClient->post($url, $data);
    }


    /**
     * @param string $accessToken
     * @param string $clientId
     * @param string $dataSource
     * @param string $accessTokenName
     * @param int $expiry
     * @return mixed
     */
    private function storeToken(string $accessToken, string $clientId, string $dataSource, string $accessTokenName, int $expiry): void
    {
        $existingTokenId = $this->repository->getExistingToken($clientId, $dataSource);

        if (!$existingTokenId) {
            $this->repository->storeToken($accessToken, $clientId, $dataSource, $accessTokenName, $expiry);
        } else {
            $this->repository->updateToken($accessToken, $clientId, $dataSource, $accessTokenName, $expiry, $existingTokenId['id']);
        }
    }


    /**
     * @param string $clientId
     * @param string $dataSource
     * @return string
     */
    public function getToken(string $clientId = '', string $dataSource = ''): string
    {
        $result = "";

        try {
            if (empty($this->accessToken)) {
                $clientId = (!empty($clientId)) ? $clientId : $this->config['data']['client_id'];
                $dataSource = (!empty($dataSource)) ? $dataSource : $this->config['data_source'];

                $existingToken = $this->repository->getValidToken($clientId, $dataSource); // get token from repository if exists and is valid

                if (empty($existingToken)) {
                    $valid = (isset($this->config['valid']) && is_int($this->config['valid'])) ? $this->config['valid'] : self::VALID;

                    $requestedToken = $this->requestAccessToken($this->config['url'], $this->config['data']);

                    if (!empty($requestedToken)) {
                        $accessTokenName = $this->config['access_token_name']; // set a token name
                        $this->accessToken = $requestedToken['data'][$accessTokenName];

                        $this->storeToken($this->accessToken, $clientId, $dataSource, $accessTokenName, strtotime("now") + $valid); //store token in repository for x amount of seconds
                    } else {
                        error_log(__CLASS__ . __METHOD__ . ' - not able to obtain token'. PHP_EOL);
                    }
                } else {
                    $this->accessToken = $existingToken['access_token'];
                }
            }

            $result = $this->accessToken;
        } catch (Throwable $e) {
            error_log(__CLASS__ . __METHOD__ . ' - Captured Throwable: ' . $e->getMessage() . PHP_EOL);
        }

        return $result;
    }
}