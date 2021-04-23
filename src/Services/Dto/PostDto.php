<?php

namespace App\Services\Dto;

/**
 * Class PostDto
 * @package App\Services\Dto
 */
class PostDto extends Dto
{
    private string $dataSource;
    private string $postId;
    private string $userName;
    private string $userId;
    private string $message;
    private string $type;
    private string $created;
    private int $metaChars;
    private int $metaDay;
    private int $metaWeek;
    private int $metaMonth;
    private int $metaYear;

    /**
     * PostDto constructor.
     * @param string $dataSource
     * @param string $postId
     * @param string $userName
     * @param string $userId
     * @param string $message
     * @param string $type
     * @param string $created
     * @param int $metaChars
     * @param int $metaDay
     * @param int $metaWeek
     * @param int $metaMonth
     * @param int $metaYear
     */
    public function __construct(string $dataSource, string $postId, string $userName, string $userId, string $message, string $type, string $created, int $metaChars, int $metaDay, int $metaWeek, int $metaMonth, int $metaYear)
    {
        $this->dataSource  = $dataSource;
        $this->postId      = $postId;
        $this->userName    = $userName;
        $this->userId      = $userId;
        $this->message     = $message;
        $this->type        = $type;
        $this->created     = $created;
        $this->metaChars   = $metaChars;
        $this->metaDay     = $metaDay;
        $this->metaWeek    = $metaWeek;
        $this->metaMonth   = $metaMonth;
        $this->metaYear    = $metaYear;
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
    public function getPostId(): string
    {
        return $this->postId;
    }


    /**
     * @return String
     */
    public function getUserName(): string
    {
        return $this->userName;
    }


    /**
     * @return String
     */
    public function getUserId(): string
    {
        return $this->userId;
    }


    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }


    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->created;
    }


    /**
     * @return int
     */
    public function getMetaChars(): int
    {
        return $this->metaChars;
    }


    /**
     * @return int
     */
    public function getMetaDay(): int
    {
        return $this->metaDay;
    }


    /**
     * @return int
     */
    public function getMetaWeek(): int
    {
        return $this->metaWeek;
    }


    /**
     * @return int
     */
    public function getMetaMonth(): int
    {
        return $this->metaMonth;
    }


    /**
     * @return int
     */
    public function getMetaYear(): int
    {
        return $this->metaYear;
    }


    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}