<?php

namespace App\Services;

use App\Services\Dto\PostDto;
use App\Repositories\PostsRepository;

/**
 * Class Posts
 * @package App\Services
 */
class Posts extends Service
{

    /**
     * Posts constructor.
     * @param string $storagePath
     */
    public function __construct(string $storagePath)
    {
        $this->repository = new PostsRepository($storagePath);
    }


    /**
     * @param string $dataSource
     * @param array $fieldsMapping
     * @param array $posts
     * @return array
     */
    public function enrichPosts(string $dataSource, array $fieldsMapping, array $posts): array
    {
        $enrichedPosts = [];

        if (!empty($posts)) {
            foreach ($posts as $post) {
                if (is_object($post)) {
                    $post = (array)$post;
                }

                $enrichedPosts[] = new PostDto(
                    $dataSource,
                    $post[$fieldsMapping[0]],
                    $post[$fieldsMapping[1]],
                    $post[$fieldsMapping[2]],
                    $post[$fieldsMapping[3]],
                    $post[$fieldsMapping[4]],
                    strtotime($post[$fieldsMapping[5]]),
                    strlen($post[$fieldsMapping[3]]),
                    date("j", strtotime($post[$fieldsMapping[5]])),
                    date("W", strtotime($post[$fieldsMapping[5]])),
                    date("n", strtotime($post[$fieldsMapping[5]])),
                    date("Y", strtotime($post[$fieldsMapping[5]]))
                );
            }
        }

        return $enrichedPosts;
    }


    /**
     * @param array $posts
     */
    public function storeBulkPosts(array $posts): void
    {
        if (!empty($posts)) {
            $this->repository->storeBulk($posts, false);
        }
    }


    /**
     * READ section
     */


    /**
     * @param int $year
     * @param int $fromMonth
     * @return array
     */
    public function getAverageCharPerMonth(int $year, int $fromMonth = 0): array
    {
        return $this->repository->getAverageCharPerMonth($year, $fromMonth);
    }


    /**
     * @param int $year
     * @param int $fromMonth
     * @return array
     */
    public function getlongestPostPerMonth(int $year, int $fromMonth = 0): array
    {
        return $this->repository->getLongestPostPerMonth($year, $fromMonth);
    }


    /**
     * @param int $year
     * @param int $fromWeek
     * @return array
     */
    public function getTotalPostsPerWeek(int $year, int $fromWeek = 0): array
    {
        return $this->repository->getTotalPostsPerWeek($year, $fromWeek);
    }


    /**
     * @param int $year
     * @param int $fromMonth
     * @return array
     */
    public function getAveragePostsPerUserPerMonth(int $year, int $fromMonth = 0): array
    {
        return $this->repository->getAveragePostsPerUserPerMonth($year, $fromMonth);
    }
}