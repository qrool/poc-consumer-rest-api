<?php

namespace App\Repositories;

use PDO;
use Throwable;

/**
 * Class Repository
 * @package App\Repository
 */
class Repository
{
    protected PDO $pdo;

    /**
     * Repository constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        try {
            $this->pdo = new PDO($path);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Throwable $e) {
            error_log("Unable to connect to DB:" . $path, 0);

            echo $e->getMessage();
            exit;
        }
    }


    /**
     *
     */
    protected function optimize(): void
    {
        $this->pdo->exec('PRAGMA synchronous = OFF'); // pause off, SQLite to simply hand-off the data to the OS for writing
        $this->pdo->exec('PRAGMA journal_mode = MEMORY'); // transaction will be faster
    }


    /**
     * @param string $query
     * @return array
     */
    public function get(string $query): array
    {
        $res = $this->pdo->query($query);

        return $res->fetchAll();
    }


    /**
     * @param $record
     */
    public function store(object $record): void {}


    /**
     * @param $record
     */
    public function storeOrUpdate(object $record): void {}


    /**
     * @param array $records
     * @param bool $isStore
     */
    public function storeBulk(array $records, bool $isStore = true): void
    {
        if (!empty($records)) {
            $this->optimize();

            $this->pdo->beginTransaction();

            foreach ($records as $record) {
                ($isStore) ? $this->store($record) : $this->storeOrUpdate($record);

                // risk here, need to address
            }

            $this->pdo->commit();
        }
    }


    /**
     * @param array $records
     */
    public function storeOrUpdateBulk(array $records): void
    {
        $this->storeBulk($records, false);
    }
}