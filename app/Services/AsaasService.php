<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AsaasService
{
    protected $apiKey;
    protected $baseUrl;
    protected $client;

    public function __construct()
    {
        $this->apiKey = env('ASAAS_API_KEY');
        $this->baseUrl = 'https://sandbox.asaas.com/api/v3';
        $this->client = new Client();
    }

    public function createPaymentLink(array $data)
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/paymentLinks", [
                'headers' => [
                    'Accept' => 'application/json',
                    'access_token' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($data),
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            // Captura erros de requisição e retorna a mensagem de erro
            if ($e->hasResponse()) {
                return json_decode($e->getResponse()->getBody()->getContents(), true);
            }
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            // Captura qualquer outro tipo de erro
            return ['error' => $e->getMessage()];
        }
    }
}