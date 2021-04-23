<?php
namespace App\Services;

use App\Repositories\StatesRepository;

/**
 * Class States
 * @package App\Model
 */
class States extends Service
{

    /**
     * States constructor.
     * @param string $storagePath
     */
    public function __construct(string $storagePath)
    {
        $this->repository = new StatesRepository($storagePath);
    }


    /**
     * @param string $dataSource
     * @param string $dataType
     * @param string $fetchedRequestID
     * @return array
     */
    public function getExistingState(string $dataSource, string $dataType, string $fetchedRequestID): array
    {
        return $this->repository->getExistingState($dataSource, $dataType,$fetchedRequestID);
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
        $this->repository->storeOrUpdateState($dataSource, $dataType, $counter, $counterState, $fetchedRequestID);
    }
}
