<?php

namespace App\Services;

use App\Repositories\Repository;

/**
 * Class Service
 * @package App\Services
 */
class Service
{
    protected Repository $repository;
    protected string $dataSource;
}