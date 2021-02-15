<?php

date_default_timezone_set('America/Lima');
use DI\Container;
use Slim\Factory\AppFactory;

require 'vendor'. DIRECTORY_SEPARATOR .'autoload.php';

define('APP_ROOT', __DIR__);

MercadoPago\SDK::setAccessToken('APP_USR-8208253118659647-112521-dd670f3fd6aa9147df51117701a2082e-677408439');
MercadoPago\SDK::setIntegratorId('dev_2e4ad5dd362f11eb809d0242ac130004');

$container = new Container();

print_r($_SERVER);

/*$container->set('url', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/');

$container->set('view', function(){
    $view = new App\Util\Twig(APP_ROOT. DIRECTORY_SEPARATOR . 'templates', [
      'debug' => true
       // 'cache' => APP_ROOT. DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'templates'
    ]);
    return $view;
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->get('/', App\Controller\AppController::class . ':home');
$app->get('/app', App\Controller\AppController::class . ':app');
$app->get('/cart', App\Controller\AppController::class . ':cart');

$app->get('/success', App\Controller\AppController::class . ':success');
$app->get('/pending', App\Controller\AppController::class . ':pending');
$app->get('/failure', App\Controller\AppController::class . ':failure');



$app->post('/api/preferences', App\Controller\ApiController::class . ':preferences');
$app->post('/api/webhook', App\Controller\ApiController::class . ':webhook');

$app->run();*/