<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$routeJuego = require __DIR__ . '/../app/routeJuego.php';
$routeJuego($app);
$app->run();
