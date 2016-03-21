<?php

namespace Bluetel\MostPopular\Results;

/**
 * Standard result interface.
 */
interface ResultInterface
{
    /**
     * Results will be constructed with an identifier, and a name.
     *
     * @param string $identifier Result identifier, typically a URL or a CMS identifier.
     * @param string $name       Result name
     */
    public function __construct($identifier, $name);

    /**
     * @return int|string Returns an identifier from a Most Popular Provider, typically a URL or CMS identifier.
     */
    public function getIdentifier();

    /**
     * @return string Returns a name for the current result.
     */
    public function getName();
}
