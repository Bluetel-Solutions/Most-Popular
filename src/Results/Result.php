<?php

namespace Bluetel\MostPopular\Results;

/**
 * Standard result object.
 */
class Result implements ResultInterface
{
    /**
     * {@inheritDoc}
     */
    private $identifier;

    /**
     * {@inheritDoc}
     */
    private $name;

    /**
     * {@inheritDoc}
     */
    public function __construct($identifier, $name)
    {
        $this->identifier = $identifier;
        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
