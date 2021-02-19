<?php
namespace App\Controller;

use  App\Util\Util;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Container\ContainerInterface;

use MercadoPago;

use Rollbar\Rollbar;
use Rollbar\Payload\Level;

class ApiController
{
    protected $container;
    protected $logger;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->logger = $container->get('logger');
    }

    public function preferences($request, $response, $args) {

        $url = $this->container->get('url');

        $body = json_decode($request->getBody());

        $this->logger->info('PREFERENCES BODY');

        $this->logger->info(json_encode($body));

        Rollbar::log(Level::info(), 'preferences body');
        Rollbar::log(Level::info(), json_encode($body));

        $preference = new MercadoPago\Preference();
        $preference->payment_methods = Util::obj();
        $preference->payment_methods->excluded_payment_methods = array();
        $excluded_payment_methods = Util::obj();
        $excluded_payment_methods->id = 'diners';
        $preference->payment_methods->excluded_payment_methods[] = $excluded_payment_methods;
        $preference->payment_methods->excluded_payment_types = array();
        $excluded_payment_types = Util::obj();
        $excluded_payment_types->id = 'atm';
        $preference->payment_methods->excluded_payment_types[] = $excluded_payment_types;
        $preference->payment_methods->installments = 6;
        

        $preference->payer = new MercadoPago\Payer();
        $preference->payer->name = 'Lalo';
        $preference->payer->surname = 'Landa';
        $preference->payer->identification = Util::obj();
        $preference->payer->identification->type = 'DNI';
        $preference->payer->identification->number = '22334445';
        $preference->payer->email = 'test_user_46542185@testuser.com';
        $preference->payer->phone = Util::obj();
        $preference->payer->phone->area_code = '52';
        $preference->payer->phone->number = '5549737300';
        $preference->payer->address = Util::obj();
        $preference->payer->address->street_name = 'Insurgentes Sur';
        $preference->payer->address->street_number = 1602;
        $preference->payer->address->zip_code = '03940';

        $items = array();
        foreach ($body->items as $key => $value) {
            $item = new MercadoPago\Item();
            $item->id = $value->code;
            $item->title = $value->name;
            $item->description = $value->description;
            $item->picture_url = $value->image;
            $item->quantity = $value->quantity;
            $item->currency_id  = $value->currency;
            $item->unit_price = $value->sale_price;
            $items[] = $item;
        }
        $preference->items = $items;

        $preference->external_reference = $body->order;

        $preference->back_urls = Util::obj();
        $preference->back_urls->success = $url . 'success';
        $preference->back_urls->pending = $url . 'pending';
        $preference->back_urls->failure = $url . 'failure';

        $preference->auto_return = 'approved';

        $preference->notification_url = $url . 'api/webhook';

        $preference->save();

        //print_r($preference);

        $resp = Util::obj();
        $resp->init_point = $preference->init_point;
        $resp->id = $preference->id;

        $this->logger->info('PREFERENCES RESULT');

        $this->logger->info(json_encode($resp, JSON_UNESCAPED_UNICODE));

        Rollbar::log(Level::info(), 'preferences result');
        Rollbar::log(Level::info(), json_encode($resp));

        return Util::success($response, 'ok', $resp);
    }

    public function webhook($request, $response, $args){
        $body = json_decode($request->getBody());

        $this->logger->info('WEBHOOK BODY JSON ENCODE');

        $this->logger->info(json_encode($body));

        Rollbar::log(Level::info(), 'WEBHOOK BODY');
        Rollbar::log(Level::info(), json_encode($request->getBody()));

        $this->logger->info('WEBHOOK BODY JSON UNESCAPE');

        $this->logger->info(json_encode($body, JSON_UNESCAPED_UNICODE));



        $params = $request->getQueryParams();

        $this->logger->info('WEBHOOKS PARAMS', $params);


        Rollbar::log(Level::info(), 'WEBHOOKS PARAMS');
        Rollbar::log(Level::info(), json_encode($params));

        /*$merchant_order = null;

        switch($params['topic']){
            case 'payment':
                $payment = MercadoPago\Payment::find_by_id($params['id']);
                // Get the payment and the corresponding merchant_order reported by the IPN.
                $merchant_order = MercadoPago\MerchantOrder::find_by_id($payment->order->id);
            break;
            case 'merchant_order':
                $merchant_order = MercadoPago\MerchantOrder::find_by_id($params['id']);
            break;
        }

        $file = fopen("webhook.txt","w");
        fwrite($file, json_encode($merchant_order, JSON_UNESCAPED_UNICODE));
        fclose($file);*/

        return Util::success($response, 'webhook ok!');
    }


}
