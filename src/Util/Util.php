<?php
namespace App\Util;


class Util
{

    public static function obj(){
        $obj = new \stdClass();
        return $obj;
    }

    public static function success($response, $message, $data = NULL){
		    $result = array(
            'status' => 'SUCCESS',
            'message' => $message
        );
        if(!is_null($data)){
            $result['data'] = $data;
        }

        $response->getBody()->write(json_encode($result));
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }

    public static function error($response, $message, $data = NULL){
		    $result = array(
            'status' => 'ERROR',
            'message' => $message
        );
        if(!is_null($data)){
            $result['data'] = $data;
        }
        $response->getBody()->write(json_encode($result));
        return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
    }

    public static function isSuccess($body){
      if($body->status === 'SUCCESS'){
        return TRUE;
      }else{
        return FALSE;
      }
    }

    public static function getData($body){
      if(property_exists($body, 'data')){
        return $body->data;
      }else{
        throw new \Exception('Data no existe');
      }
    }

    public static function getHttpClient($client, $method, $uri, $body = array(), $company = NULL, $account = NULL){
      $headers = array();
      if(!is_null($company)){
        $headers['X-Store-Company'] = $company;
      }
      if(!is_null($account)){
        $headers['X-Store-Account'] = $account;
      }
      $response = $client->request($method, $uri, array(
          'headers' => $headers,
          'json' => $body
      ));
      if($response->getStatusCode() === 200){
        return json_decode($response->getBody());
      }else{
        throw new WebException('Una respuesta enviada ha sido distinta a 200');
      }
    }
}
