<?php

namespace Bluetel\MostPopular;

class MostPopular
{
    public function getMostPopular()
    {
        $provider = new Providers\GoogleAnalyticsProvider();
        $time = new \DateTime;
        return $provider
            ->setAuthConfigFile('./service-account.json')
            ->setProfileId('ga:33408065')
            ->setStartTime($time->modify("-1 week"))
            ->getMostPopular();
    }
}
