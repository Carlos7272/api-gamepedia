<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
//estos dos estan ok, cuando index este en la raiz descomentar....ver si el orden esta ok...googlear o probar
//$app->addRoutingMiddleware();
//$app->addErrorMiddleware();

$routeJuegos = require __DIR__ . '/../app/routeJuegos.php';
$routeJuegos($app);
$routeGeneros = require __DIR__ . '/../app/routeGeneros.php';
$routeGeneros($app);
$routePlataformas = require __DIR__ .'/../app/routePlataformas.php';
$routePlataformas($app);
$app->run();
