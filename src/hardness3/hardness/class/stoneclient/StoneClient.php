<?php

namespace hardness;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;

require_once("Request/CriarPedido.php");
require_once("Request/FecharPedido.php");
require_once("Request/ImprimirNotaFiscal.php");
require_once("Mapper.php");
require_once("Response/Resposta.php");


class StoneClient {
    private Client $client;
    const BASE_URI = 'https://api.pagar.me';    

    public function __construct(string $apiKey)
    {        
        $this->client = new Client([            
            RequestOptions::HEADERS => [
                'Accept'     => 'application/json',
                'Content-Type' => 'application/json'
            ],
            RequestOptions::AUTH => [$apiKey,'']            
        ]);       
    }

    public function criarPedido(CriarPedido $pedido) {

        $uri = 'core/v5/orders/';        

        $body = (new Mapper)->mapCriarPedido($pedido);        
        $response = $this->enviarRequest('POST', $uri, [
            'json' => $body
        ]);
        
        return $response;
    }

    public function fecharPedido(FecharPedido $pedido) {
        $uri = "core/v5/orders/{$pedido->orderId}/closed";
        
        $body = (new Mapper)->mapFecharPedido($pedido);
        $response = $this->enviarRequest('PATCH', $uri, [
            'json' => $body
        ]);
        
        return $response;
    }

    public function imprimirNotaFiscal(ImprimirNotaFiscal $nota) {
        $uri = "posconnect/v1/orders/{$nota->orderId}/prints";

        $body = (new Mapper)->mapImprimirNota($nota);
        $response = $this->enviarRequest('POST', $uri, [
            'json' => $body
        ]);
        
        return $response;
    }

    public function obterPedido(string $orderId) {
        $uri = "core/v5/orders/{$orderId}";
        
        $response = $this->enviarRequest('GET', $uri);
        
        return $response;
    }

    private function enviarRequest($httpMethod, $uri, $options = []) : Resposta {

        $uri = self::BASE_URI . '/' . $uri;

        try  {                        
            $response = $this->client->request($httpMethod, $uri, $options);            
            return Resposta::createResponse($response->getStatusCode(), $response->getBody()->getContents());
        } catch(ClientException $clientException) {
            $response = $clientException->getResponse();            
            return Resposta::createResponse($response->getStatusCode(), $response->getBody()->getContents());
        } catch(Exception $exception) {
            throw $exception;
        }
    }
}