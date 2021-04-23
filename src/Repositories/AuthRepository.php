<?php

namespace App\Repositories;

/**
 * Class AuthRepository
 * @package App\Repository
 */
class AuthRepository extends Repository
{
    const TABLE = 'auth';

    /**
     * AuthRepository constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct($path);

        $this->execute('PRAGMA synchronous = OFF'); // pause off, SQLite to simply hand-off the data to the OS for writing
        $this->execute('PRAGMA journal_mode = MEMORY'); // transaction will be faster
    }


    /**
     * @param string $query
     * @return bool|int
     */
    public function execute(string $query): bool|int
    {
        return $this->pdo->exec($query);
    }


    /**
     * @param string $accessToken
     * @param string $clientId
     * @param string $dataSource
     * @param string $accessTokenName
     * @param int $expiry
     * @param int $id
     */
    public function updateToken(string $accessToken, string $clientId, string $dataSource, string $accessTokenName, int $expiry, int $id)
    {
        $stmt = $this->pdo->prepare('UPDATE ' . self::TABLE .
            ' SET client_id = :client_id, data_source = :data_source, access_token = :access_token, access_token_name = :access_token_name, expiry = :expiry WHERE id = :id');
        $stmt->bindValue(':client_id', $clientId);
        $stmt->bindValue(':data_source', $dataSource);
        $stmt->bindValue(':access_token', $accessToken);
        $stmt->bindValue(':access_token_name', $accessTokenName);
        $stmt->bindValue(':expiry', $expiry);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }


    /**
     * @param string $accessToken
     * @param string $clientId
     * @param string $dataSource
     * @param string $accessTokenName
     * @param int $expiry
     */
    public function storeToken(string $accessToken, string $clientId, string $dataSource, string $accessTokenName, int $expiry)
    {
        $stmt = $this->pdo->prepare('INSERT INTO ' . self::TABLE .
            '(client_id, data_source, access_token, access_token_name, expiry) VALUES (:client_id, :data_source, :access_token, :access_token_name, :expiry)');
        $stmt->bindValue(':client_id', $clientId);
        $stmt->bindValue(':data_source', $dataSource);
        $stmt->bindValue(':access_token', $accessToken);
        $stmt->bindValue(':access_token_name', $accessTokenName);
        $stmt->bindValue(':expiry', $expiry);
        $stmt->execute();
    }


    /**
     * @param string $clientId
     * @param string $dataSource
     * @return array
     */
    public function getValidToken(string $clientId, string $dataSource): array
    {
        $result = [];
        $existingToken = $this->getExistingToken($clientId, $dataSource);

        if (!empty($existingToken) && is_array($existingToken)) {
            $result = ($existingToken['expiry'] > strtotime("now")) ? $existingToken : $result;
        }

        return $result;
    }


    /**
     * @param string $clientId
     * @param string $dataSource
     * @return array
     */
    public function getExistingToken(string $clientId, string $dataSource): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . self::TABLE .
            ' WHERE client_id = :client_id AND data_source = :data_source LIMIT 1');
        $stmt->bindValue(':client_id', $clientId);
        $stmt->bindValue(':data_source', $dataSource);
        $stmt->execute();

        return $stmt->fetchAll()[0] ?? [];
    }
}