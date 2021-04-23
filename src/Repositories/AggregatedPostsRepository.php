<?php

namespace App\Repositories;

/**
 * Class AggregatedPostsRepository
 * @package App\Repository
 */
class AggregatedPostsRepository extends Repository
{
    const TABLE = 'aggregated_posts';


    /**
     * @param object $record
     * @noinspection SqlResolve
     */
    public function storeOrUpdate(object $record): void
    {
        $query = "INSERT OR REPLACE INTO " . self::TABLE .
            "(id, data_source, aggregated_code, aggregated_value, scope, day, week, month, year) 
            VALUES ((select id from " . self::TABLE . " where 
            data_source = '" . $record->getDataSource() . "'  
            AND aggregated_code = '" . $record->getAggregatedCode() . "'  
            AND scope = '" . $record->getScope() . "'  
            AND day = '" . $record->getDay() . "'  
            AND week = '" . $record->getWeek() . "' 
            AND month = '" . $record->getMonth() . "'         
            AND year = '" . $record->getYear() . "' LIMIT 1), 
            
            '" . $record->getDataSource() . "', 
            '" . $record->getAggregatedCode() . "', 
            " . $record->getAggregatedValue() . ", 
            '" . $record->getScope() . "', 
            " . $record->getDay() . ", 
            " . $record->getWeek() . ", 
            " . $record->getMonth() . ", 
            " . $record->getYear() . ")";

        $this->pdo->exec($query);
    }


    /**
     * @param string $dataSource
     * @param string $aggregatedCode
     * @param string $scope
     * @return array
     */
    public function getAggregatedBySourceCodeScope(string $dataSource, string $aggregatedCode, string $scope):array
    {
        $query = "SELECT * FROM " . self::TABLE . " WHERE  data_source = '" . $dataSource . "' AND aggregated_code = '" . $aggregatedCode . "' AND scope = '" . $scope . "' ORDER BY year, month, week, day";

        return $this->get($query);
    }
}