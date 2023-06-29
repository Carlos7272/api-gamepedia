<?php
use Slim\Factory\AppFactory;
use Selective\BasePath\BasePathMiddleware;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
header("Access-Control-Allow-Origin: *");
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

$routeJuegos = require __DIR__ . '/app/routeJuegos.php';
$routeJuegos($app);
$routeGeneros = require __DIR__ . '/app/routeGeneros.php';
$routeGeneros($app);
$routePlataformas = require __DIR__ . '/app/routePlataformas.php';
$routePlataformas($app);

$app->run();
