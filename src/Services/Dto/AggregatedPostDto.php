<?php

namespace App\Services\Dto;

use JetBrains\PhpStorm\Pure;

/**
 * Class AggregatedPostDto
 * @package App\Services\Dto
 */
class AggregatedPostDto implements \JsonSerializable
{
    private string $dataSource;
    private string $aggregatedCode;
    private float $aggregatedValue;
    private string $scope;
    private int $day;
    private int $week;
    private int $month;
    private int $year;

    /**
     * AggregatedPostDto constructor.
     * @param string $dataSource
     * @param string $aggregatedCode
     * @param float $aggregatedValue
     * @param string $scope
     * @param int $day
     * @param int $week
     * @param int $month
     * @param int $year
     */
    public function __construct(string $dataSource, string $aggregatedCode, float $aggregatedValue, string $scope, int $day, int $week, int $month, int $year)
    {
        $this->dataSource       = $dataSource;
        $this->aggregatedCode   = $aggregatedCode;
        $this->aggregatedValue  = $aggregatedValue;
        $this->scope            = $scope;
        $this->day              = $day;
        $this->week             = $week;
        $this->month            = $month;
        $this->year             = $year;
    }


    /**
     * @return string
     */
    public function getDataSource(): string
    {
        return $this->dataSource;
    }


    /**
     * @return string
     */
    public function getAggregatedCode(): string
    {
        return $this->aggregatedCode;
    }


    /**
     * @return float
     */
    public function getAggregatedValue(): float
    {
        return $this->aggregatedValue;
    }


    /**
     * @return String
     */
    public function getScope(): string
    {
        return $this->scope;
    }


    /**
     * @return int
     */
    public function getDay(): int
    {
        return $this->day;
    }


    /**
     * @return int
     */
    public function getWeek(): int
    {
        return $this->week;
    }


    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }


    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }


    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}