<?php
namespace App\Controller;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Container\ContainerInterface;

class AppController
{
    protected $container;
    protected $logger;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->logger = $container->get('logger');
    }

    public function home($request, $response, $args) {
        return $this->container->get('view')->render($response, 'home.html', [
            'url' => $this->container->get('url')
        ]);
    }

    public function cart($request, $response, $args) {
        return $this->container->get('view')->render($response, 'cart.html', [
            'url' => $this->container->get('url')
        ]);
    }

    public function success($request, $response, $args) {
        $params = $request->getQueryParams();

        $this->logger->info('SUCCESS', $params);

     Rollbar::log(Level::info(), 'Paǵo con éxito');

       Rollbar::log(Level::info(), print_r($params, true));

        return $this->container->get('view')->render($response, 'success.html', [
            'url' => $this->container->get('url'),
            'params' => $params
        ]);
    }

    public function pending($request, $response, $args) {
        $params = $request->getQueryParams();

        $this->logger->info('PENDING');
        $this->logger->info(print_r($params, true));

        return $this->container->get('view')->render($response, 'pending.html', [
            'url' => $this->container->get('url')
        ]);
    }

    public function failure($request, $response, $args) {
        $params = $request->getQueryParams();

        $this->logger->info('FAILURE');
        $this->logger->info(print_r($params, true));
        
        return $this->container->get('view')->render($response, 'failure.html', [
            'url' => $this->container->get('url')
        ]);
    }

}
