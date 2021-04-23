<?php

namespace App\Repositories;

/**
 * Class PostsRepository
 * @package App\Repository
 */
class PostsRepository extends Repository
{
    const TABLE = 'posts';

    /**
     * @param string $query
     * @return bool|int
     */
    public function execute(string $query): bool|int
    {
        return $this->pdo->exec($query);
    }


    /**
     * @param object $record
     * @noinspection SqlResolve
     */
    public function storeOrUpdate(object $record): void
    {
        $query = "INSERT OR REPLACE INTO " . self::TABLE .
            "(id, data_source, post_id, user_name, user_id , message, type, created, meta_chars, meta_day, meta_week, meta_month, meta_year) 
            VALUES ((select id from " . self::TABLE . " where 
            post_id = '" . $record->getPostId() . "' LIMIT 1), 
            
            '" . $record->getDataSource() . "', 
            '" . $record->getPostId() . "', 
            '" . $record->getUserName() . "', 
            '" . $record->getUserId() . "', 
            '" . $record->getMessage() . "', 
            '" . $record->getType() . "', 
            '" . $record->getCreated() . "',
            '" . $record->getMetaChars() . "', 
            " . $record->getMetaDay() . ", 
            " . $record->getMetaWeek() . ",          
            " . $record->getMetaMonth() . ",                       
            " . $record->getMetaYear() . ")";

        $this->pdo->exec($query);
    }

    /**
     * @param object $record
     */
    public function store(object $record): void
    {
        /** @noinspection SqlResolve */
        $stmt = $this->pdo->prepare("INSERT INTO " . self::TABLE .
            '(data_source, post_id, user_name, user_id , message, type, created, meta_chars, meta_day, meta_week, meta_month, meta_year) 
            VALUES (:data_source, :post_id, :user_name, :user_id, :message, :type, :created, :meta_chars, :meta_day, :meta_week, :meta_month, :meta_year)');

        $stmt->bindValue(':data_source', $record->getDataSource());
        $stmt->bindValue(':post_id', $record->getPostId());
        $stmt->bindValue(':user_name', $record->getUserName());
        $stmt->bindValue(':user_id', $record->getUserId());
        $stmt->bindValue(':message', $record->getMessage());
        $stmt->bindValue(':type', $record->getType());
        $stmt->bindValue(':created', $record->getCreated());
        $stmt->bindValue(':meta_chars', $record->getMetaChars());
        $stmt->bindValue(':meta_day', $record->getMetaDay());
        $stmt->bindValue(':meta_week', $record->getMetaWeek());
        $stmt->bindValue(':meta_month', $record->getMetaMonth());
        $stmt->bindValue(':meta_year', $record->getMetaYear());
        $stmt->execute();
    }


    /**
     * READ section
     */


    /**
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getAverageCharPerMonth(int $year, int $month = 0): array
    {
        $startFromMonth = ($month > 0) ? "meta_month >= " . $month . " AND" : '';

        $query = "SELECT avg(meta_chars) aggregatedValue, meta_month month, meta_year year FROM " . self::TABLE . " WHERE  " . $startFromMonth . " meta_year = " . $year . " GROUP BY meta_year, meta_month";

        return $this->get($query);
    }


    /**
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getlongestPostPerMonth(int $year, int $month = 0): array
    {
        $startFromMonth = ($month > 0) ? "meta_month >= " . $month . " AND" : "";

        $query = "SELECT max(meta_chars) aggregatedValue, meta_month month, meta_year year FROM  " . self::TABLE . "  WHERE  " . $startFromMonth . " meta_year = " . $year . " GROUP BY meta_year, meta_month";

        return $this->get($query);
    }


    /**
     * @param int $year
     * @param int $week
     * @return array
     */
    public function getTotalPostsPerWeek(int $year, int $week = 0): array
    {
        $statement = ($week > 0 && $week < 53) ? "(meta_week >= " . $week . " AND meta_week < 53 AND meta_year = " . $year . ")" : "(meta_year = " . $year . ")";
        $statement .= " OR (meta_week=53 and meta_year = " . $year + 1 . ")";

        $query = "SELECT count(post_id) aggregatedValue, meta_week week, meta_year year FROM " . self::TABLE . " WHERE " . $statement . " GROUP BY meta_year, meta_week";

        return $this->get($query);
    }


    /**
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getAveragePostsPerUserPerMonth(int $year, int $month = 0): array
    {
        $startFromMonth = ($month > 0) ? "meta_month >= " . $month . " AND" : "";

        $query = "SELECT avg(p.cc) aggregatedValue, p.meta_month month, p.meta_year year FROM 
            (SELECT meta_month, meta_year, count(id) cc FROM " . self::TABLE . " WHERE " . $startFromMonth . " meta_year = " . $year . " GROUP BY meta_month, user_id)
        as p group by p.meta_month";

        return $this->get($query);
    }
}