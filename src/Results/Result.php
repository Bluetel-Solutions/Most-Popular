<?php

namespace Bluetel\MostPopular\Results;

/**
 * Standard result object.
 */
class Result implements ResultInterface
{
    /**
     * {@inheritdoc}
     */
    private $identifier;

    /**
     * {@inheritdoc}
     */
    private $name;

    /**
     * {@inheritdoc}
     */
    public function __construct($identifier, $name)
    {
        $this->identifier = $identifier;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
}
