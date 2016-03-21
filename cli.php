<?php

include 'vendor/autoload.php';
$mostPopularCli = new Bluetel\MostPopular\MostPopularCLI();
var_dump($mostPopularCli->getMostPopular());
die;
