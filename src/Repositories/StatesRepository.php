<?php

namespace App\Repositories;

/**
 * Class StatesRepository
 * @package App\Repository
 */
class StatesRepository extends Repository
{
    const TABLE = 'states';

    /**
     * @param string $query
     * @return bool|int
     */
    function execute(string $query): bool|int
    {
        return $this->pdo->exec($query);
    }


    /**
     * @param string $dataSource
     * @param string $dataType
     * @param string $counter
     * @param int $counterState
     * @param string $fetchedRequestID
     */
    public function storeOrUpdateState(string $dataSource, string $dataType, string $counter, int $counterState, string $fetchedRequestID): void
    {
        $query = "INSERT OR REPLACE INTO " . self::TABLE .
            "(id, data_source, data_type, counter, counter_state, request_id) 
            VALUES ((select id from " . self::TABLE . " where 
            data_source = '" . $dataSource . "' 
            AND data_type = '" . $dataType . "'            
            AND counter = '" . $counter . "'  
            AND counter_state = '" . $counterState . "' LIMIT 1), 
            
            '" . $dataSource . "', 
            '" . $dataType . "', 
            '" . $counter . "', 
            '" . $counterState . "', 
            '" . $fetchedRequestID . "')";

        $this->pdo->exec($query);
    }


    /**
     * @param string $dataSource
     * @param string $dataType
     * @param string $counter
     * @param int $counterState
     * @param string $fetchedRequestID
     */
    public function createState(string $dataSource, string $dataType, string $counter, int $counterState, string $fetchedRequestID): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO " . self::TABLE . "(data_source, data_type, counter, counter_state, request_id) VALUES (:data_source, :data_type, :counter, :counter_state, :request_id)");
        $stmt->bindValue(':data_source', $dataSource);
        $stmt->bindValue(':data_type', $dataType);
        $stmt->bindValue(':counter', $counter);
        $stmt->bindValue(':counter_state', $counterState);
        $stmt->bindValue(':request_id', $fetchedRequestID);
        $stmt->execute();
    }


    /**
     * @param string $dataSource
     * @param string $dataType
     * @param string $counter
     * @param string $fetchedRequestID
     * @return array
     */
    public function getExistingState(string $dataSource, string $dataType, string $fetchedRequestID): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE data_source = :data_source AND data_type = :data_type AND request_id = :request_id  LIMIT 1");
        $stmt->bindValue(':data_source', $dataSource);
        $stmt->bindValue(':data_type', $dataType);
        $stmt->bindValue(':request_id', $fetchedRequestID);
        $stmt->execute();

        return $stmt->fetchAll()[0] ?? [];
    }
}