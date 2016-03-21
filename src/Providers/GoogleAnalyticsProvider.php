<?php

namespace Bluetel\MostPopular\Providers;

use Bluetel\MostPopular\Exceptions;
use Bluetel\MostPopular\Results;

/**
 * Google Analytics Provider.
 *
 * @author Alex Wilson <a@ax.gy>
 */
class GoogleAnalyticsProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * GA Terms.
     *
     * @var array
     */
    private $terms = [
        'pageviews' => 'ga:pageviews',
        'title'     => 'ga:pageTitle',
        'path'      => 'ga:pagePath',
    ];

    /**
     * Filters.
     *
     * @var array
     */
    private $filters = [];

    /**
     * Metrics.
     *
     * @var array
     */
    private $metrics = [];

    /**
     * Dimensions.
     *
     * @var array
     */
    private $dimensions = [];

    /**
     * Google API PHP Client.
     *
     * @var Google_Client
     */
    private $client;

    /**
     * Google Analytics Service.
     *
     * @var Google_Service_Analytics
     */
    private $analyticsService;

    /**
     * Path to auth configuration, must be a valid service account with permissions.
     *
     * @var string
     */
    private $authConfigFile;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this
            ->addMetric($this->terms['pageviews'])
            ->addDimension($this->terms['path'])
            ->addDimension($this->terms['title']);

        return parent::__construct();
    }

    /**
     * Allows for additional filters to be passed down to Google Analytics.
     *
     * @param string $filter Allows for additional filters to be passed down.
     *
     * @return self
     */
    public function addFilter($filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Allows for additional metrics to be passed down to Google Analytics.
     *
     * @param string $metric Allows for additional metrics to be passed down.
     *
     * @return self
     */
    public function addMetric($metric)
    {
        $this->metrics[] = $metric;

        return $this;
    }

    /**
     * Allows for additional dimensions to be passed down to Google Analytics.
     *
     * @param string $dimension Allows for additional dimensions to be passed down.
     *
     * @return self
     */
    public function addDimension($dimension)
    {
        $this->dimensions[] = $dimension;

        return $this;
    }

    /**
     * Sets a path to the credential we use for Google Analytics.
     * Recommended - Brand new service account with permission to exposed properties ONLY.
     *
     * @param string $authConfigFile
     *
     * @return self
     */
    public function setAuthConfigFile($authConfigFile)
    {
        $this->authConfigFile = $authConfigFile;
        // If a client exists, we should probably kill it.
        $this->client = null;

        return $this;
    }

    /**
     * Google Analytics Profile ID.
     * Account specified above MUST have at least read-access to this.
     *
     * @param string $profileId Profile ID we are currently searching under.
     *
     * @return self
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMostPopular()
    {
        $analyticsService = $this->getAnalyticsService();

        $profileId = $this->profileId;
        if (empty($profileId)) {
            throw new Exceptions\ProviderFailureException(
                'No profile ID set!  Cannot search GA without a profile ID.',
                404
            );
        }

        // Convert View ID to the right format.
        if (0 !== strpos($this->profileId, 'ga:')) {
            $profileId = "ga:{$profileId}";
        }

        // Start date and end date must be formatted as Y-m-d.
        $startDate = $this->startTime->format('Y-m-d');
        $endDate = $this->endTime->format('Y-m-d');

        // Metrics are apparently comma separated.
        $metrics = implode(',', $this->metrics);

        // Check "optional" parameters.
        $params = [];

        // Start index is offset
        $params['start-index'] = $this->offset;

        // Max results is limit
        $params['max-results'] = $this->limit;

        // Sort by pageviews.
        $params['sort'] = (($this->sort > 0) ? '' : '-').$this->terms['pageviews'];

        if (!empty($this->dimensions)) {
            $params['dimensions'] = implode(',', $this->dimensions);
        }

        if (!empty($this->filters)) {
            $params['filters'] = implode(',', $this->filters);
        };

        try {
            $results = $analyticsService->data_ga->get(
               $profileId,
               $startDate,
               $endDate,
               $metrics,
               $params
            );
        } catch (Exception $e) {
            throw new Exceptions\ProviderFailureException(
                $e->getMessage(),
                500,
                $e
            );
        }

        return $this->mapResults($results);
    }

    /**
     * Map results from GA.
     *
     * @param array $results Maps GA results.
     *
     * @return \Bluetel\MostPopular\Results\ResultInterface[] Returns an array of objects implementing ResultInterface.
     */
    public function mapResults($results)
    {
        if (0 === $results['totalResults']) {
            return [];
        }

        // Convert column headers to something human readable.
        $headers = array_flip(array_map(function ($columnHeader) {
            return $this->tagToTerm($columnHeader['name']);
        }, $results['columnHeaders']));

        // Finally return our results.
        return array_map(function ($row) use ($headers) {
            return new Results\Result(
                $row[$headers['path']],
                $row[$headers['title']]
            );
        }, $results['rows']);
    }

    /**
     * Returns a term from a given tag.
     *
     * @param string $originalTag GA Tag
     *
     * @return string Term
     */
    protected function tagToTerm($originalTag)
    {
        foreach ($this->terms as $term => $tag) {
            if ($originalTag == $tag) {
                return $term;
            }
        }

        return $originalTag;
    }

    /**
     * @return Google_Client
     */
    private function getGoogleClient()
    {
        if (is_null($this->client)) {
            if (!class_exists('Google_Client')) {
                throw new Exceptions\ProviderFailureException('Could not find the Google_Client class', 404);
            }
            $this->client = new \Google_Client();

            $this->client->setApplicationName('Most_Popular');
            $this->client->setAuthConfig($this->authConfigFile);
        }

        return $this->client;
    }

    /**
     * @return Google_Service_Analytics
     */
    private function getAnalyticsService()
    {
        if (is_null($this->analyticsService)) {
            if (!class_exists('Google_Service_Analytics')) {
                throw new Exceptions\ProviderFailureException('Could not find the Google_Service_Analytics class', 404);
            }
            $client = $this->getGoogleClient();
            $this->client->setScopes([\Google_Service_Analytics::ANALYTICS_READONLY]);
            $this->analyticsService = new \Google_Service_Analytics($client);
        }

        return $this->analyticsService;
    }
}
