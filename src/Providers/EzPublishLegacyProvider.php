<?php

namespace Bluetel\MostPopular\Providers;

use Bluetel\MostPopular\Results;
use Bluetel\MostPopular\Exceptions;

/**
 * Google Analytics Provider
 *
 * @author Alex Wilson <a@ax.gy>
 */
class EzPublishLegacyProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Content Classes.
     *
     * @var array
     */
    private $contentClasses = array();

    /**
     * Section ID.
     *
     * @var int
     */
    private $sectionId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setSectionId(2);
        return parent::__construct();
    }

    /**
     * Set an eZ Publish section ID.
     *
     * @param int $sectionId eZ Publish Section ID.
     *
     * @return self
     */
    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;

        return $this;
    }

    /**
     * Adds an eZ Content Class identifier to be searched for.
     *
     * @param string $contentClass
     *
     * @return self
     */
    public function addContentClass($contentClass)
    {
        $this->contentClasses[] = $contentClass;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMostPopular()
    {
        // Sanity check, we need eZ Publish classes.
        if (!class_exists("eZFunctionHandler") or !class_exists("eZViewCounter")) {
            throw new Exceptions\ProviderFailureException(
                "Cannot use the EzPublishLegacyProvider without eZ Publish.",
                404
            );
        }

        // Default to 'article'.
        $contentClasses = $this->contentClasses;
        if (empty($contentClasses)) {
            $contentClasses[] = 'article';
        }

        $limit = $this->limit;

        if (0 !== $this->offset) {
            trigger_error("Offset is not supported by EzPublishLegacyProvider", E_USER_NOTICE);
        }

        if (0 > $this->offset) {
            trigger_error("Ascending sort is not supported by EzPublishLegacyProvider", E_USER_NOTICE);
        }

        // Retrieve content classes.
        $contentClasses = \eZFunctionHandler::execute(
            'class',
            'list',
            array('class_filter' => $contentClasses)
        );

        $mostPopularArray = array();
        $names = array();
        $sortedArray = array();

        foreach ($contentClasses as $contentClass) {
            $contentObjectArray = \eZFunctionHandler::execute(
                'content',
                'view_top_list',
                array(
                    'class_id' => $contentClass->ID,
                    'section_id' => $sectionId,
                    'limit' => $limit,
                )
            );

            foreach ($contentObjectArray as $contentObject) {

                // Fetch view count of current node id.
                $mostPopularArray[$contentObject->NodeID] = $contentObject;
                $viewCount = \eZViewCounter::fetch($contentObject->NodeID);
                if (is_object($viewCount)) {
                    $names[$contentObject->NodeID] = $contentObject->attribute('name');
                    $sortedArray[$contentObject->NodeID] = $viewCount->Count; // Use as index.
                }
            }
        }

        // Reverse sort so that things are in order of view count.
        arsort($sortedArray);

        $mostPopular = array();
        foreach ($sortedArray as $nodeId => $count) {
            if (array_key_exists($nodeId, $mostPopularArray)) {
                $mostPopular[] = $mostPopularArray[$nodeId];
            }
        }

        // Finally return our results.
        return array_map(function ($node) use ($names) {
            $name = $names[$node];
            return new Results\Result(
                $node,
                $name
            );
        }, array_slice($mostPopular, 0, $limit));
    }

}
