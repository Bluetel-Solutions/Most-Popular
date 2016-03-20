<?php

namespace Bluetel\MostPopular\Providers;

use DateTime;

/**
 * Interface for Most Popular Providers.
 */
interface ProviderInterface
{
    /**
     * Ascending sort
     */
    const SORT_ASC = 1;

    /**
     * Descending sort
     */
    const SORT_DESC = -1;

    /**
     * Retrieves Most Popular items from a given provider, matching parameters.
     *
     * @throws \Bluetel\MostPopular\Exceptions\ProviderFailureException Thrown if failure occurs whilst querying a provider.
     *
     * @return \Bluetel\MostPopular\Results\ResultInterface[] Returns an array of objects implementing ResultInterface.
     */
    public function getMostPopular();

    /**
     * Sets the start time for a provider query.
     *
     * @param DateTime $startTime Start time.
     *
     * @return self
     */
    public function setStartTime(DateTime $startTime);

    /**
     * Sets the end time for a provider query.
     *
     * @param DateTime $endTime End time.
     *
     * @return self
     */
    public function setEndTime(DateTime $endTime);

    /**
     * Sets a sort direction for a popularity query, useful for determining low performing items.
     *
     * @param int $sort Sort direction. When positive, assumes ascending order, when negative, assumes descending.
     *
     * @return self
     */
    public function setSort($sort = self::SORT_ASC);

    /**
     * Sets a limit for a popularity query.
     *
     * @param integer $limit Limit for popularity query.
     *
     * @return self
     */
    public function setLimit($limit = 5);

    /**
     * Set an offset for a popularity query.
     *
     * @param integer $offset Offset for popularity query.
     *
     * @return self
     */
    public function setOffset($offset = 0);
}
