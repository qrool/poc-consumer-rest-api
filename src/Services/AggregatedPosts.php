<?php

namespace App\Services;

use App\Services\Dto\AggregatedPostDto;
use App\Repositories\AggregatedPostsRepository;

/**
 * Class AggregatedPosts
 * @package App\Services
 *
 * TODO: replace day,week,month, year columns with scope_from, scope_to
 *
 */
class AggregatedPosts extends Service
{
    private array $ignoreByScope =['week'=>['day', 'month'], 'month' => ['day', 'week'], 'year' => ['day', 'week', 'month']];

    /**
     * AggregatedPosts constructor.
     * @param string $storagePath
     */
    public function __construct(string $storagePath)
    {
        $this->repository = new AggregatedPostsRepository($storagePath);
    }


    /**
     * @param string $dataSource
     * @param string $scope
     * @param string $aggregatedCode
     * @param array $fieldsIgnored
     * @param array $records
     *
     * TODO: Week = 53 needs to be addressed and refactored
     */
    private function storeAggregated(string $dataSource, string $scope, string $aggregatedCode, array $fieldsIgnored, array $records): void
    {
        $result = [];

        foreach ($records as $record) {
            $result[] = new AggregatedPostDto(
                $dataSource,
                $aggregatedCode,
                $record['aggregatedValue'],
                $scope,
                isset($fieldsIgnored['day']) ? 0 : $record['day'],
                isset($fieldsIgnored['week']) ? 0 : ($record['week'] == 53 ? $record['week']-1:  $record['week']),
                isset($fieldsIgnored['month']) ? 0 : $record['month'],
                (isset($record['week']) && $record['week'] == 53) ? $record['year']-1: $record['year']
            );
        }

        $this->repository->storeOrUpdateBulk($result);
    }


    /**
     * @param string $dataSource
     * @param string $aggregatedCode
     * @param string $scope
     * @param array $posts
     */
    public function processAggregated(string $dataSource, string $aggregatedCode, string $scope, array $posts): void
    {
        $fieldsIgnored  = $this->ignoreByScope[$scope] ? array_flip($this->ignoreByScope[$scope]): [];

        $this->storeAggregated($dataSource, $scope, $aggregatedCode, $fieldsIgnored, $posts);
    }


    /**
     * READ section
     *
     * TODO: introduce caching
     */


    /**
     * @param string $dataSource
     * @param string $scope
     * @return array
     */
    public function getAverageChars(string $dataSource, string $scope): array
    {
        $aggregatedCode = 'averageChar';

        return $this->repository->getAggregatedBySourceCodeScope($dataSource, $aggregatedCode, $scope);
    }


    /**
     * @param string $dataSource
     * @param string $scope
     * @return array
     */
    public function getLongestPostByChar(string $dataSource, string $scope): array
    {
        $aggregatedCode = 'longestPostByChar';

        return $this->repository->getAggregatedBySourceCodeScope($dataSource, $aggregatedCode, $scope);
    }


    /**
     * @param string $dataSource
     * @param string $scope
     * @return array
     */
    public function getTotalPosts(string $dataSource, string $scope): array
    {
        $aggregatedCode = 'totalPosts';

        return $this->repository->getAggregatedBySourceCodeScope($dataSource, $aggregatedCode, $scope);
    }


    public function getAveragePostsPerUser(string $dataSource, string $scope): array
    {
        $aggregatedCode = 'averagePostsPerUser';

        return $this->repository->getAggregatedBySourceCodeScope($dataSource, $aggregatedCode, $scope);
    }

}