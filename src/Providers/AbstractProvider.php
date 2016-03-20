<?php

namespace Bluetel\MostPopular\Providers;

use DateTime;

/**
 * Abstract Provider
 */
abstract class AbstractProvider
{
    /**
     * Start time
     *
     * @var DateTime
     */
    protected $startTime;

    /**
     * End time
     *
     * @var DateTime
     */
    protected $endTime;

    /**
     * Limit
     *
     * @var int
     */
    protected $limit;

    /**
     * Offset
     *
     * @var int
     */
    protected $offset;

    /**
     * Sort direction
     *
     * @var int
     */
    protected $sort;

    /**
     * Constructor
     */
    public function __construct()
    {
        $date = new DateTime;
        $this
            ->setEndTime($date)
            ->setStartTime($date->modify("-1 day"))
        ;
    }

    /**
     * Sets start time for a query.
     *
     * @param DateTime $startTime
     */
    public function setStartTime(DateTime $startTime)
    {
        $this->startTime = clone($startTime);
        return $this;
    }

    /**
     * Sets end time for a query.
     *
     * @param DateTime $endTime
     */
    public function setEndTime(DateTime $endTime)
    {
        $this->endTime = clone($endTime);
        return $this;
    }

    /**
     * Sets a sort direction.
     *
     * @param int $sort Positive values are ascending, negative are descending.
     */
    public function setSort($sort = ProviderInterface::SORT_ASC)
    {
        $this->sort = $sort;
    }

    /**
     * Sets thq query limit.
     *
     * @param integer $limit
     */
    public function setLimit($limit = 5)
    {
        $this->limit = $limit;
    }

    /**
     * Sets the query offset.
     *
     * @param integer $offset
     */
    public function setOffset($offset = 0)
    {
        $this->offset = $offset;
    }
}
