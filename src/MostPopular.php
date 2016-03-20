<?php

namespace Bluetel\MostPopular;

class MostPopular
{
    public function getMostPopular()
    {
        $provider = new Providers\GoogleAnalyticsProvider();
        return $provider
            ->setAuthConfigFile('./service-account.json')
            ->setProfileId('ga:33408065')
            ->getMostPopular();
    }
}
