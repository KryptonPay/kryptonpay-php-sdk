<?php

namespace KryptonPay\Service\Api;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Collection;
use KryptonPay\Api\ApiContext;
use KryptonPay\Models\Api\Response;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Client
{
    private $method;
    private $endPoint;
    private $client;
    private $apiContext;
    private $response;
    private $url = 'https://api.kryptonpay.com.br/';

    public function __construct(ApiContext $apiContext, string $method, string $endPoint)
    {
        $this->apiContext = $apiContext;

        if ($this->apiContext->getIsSandbox()) {
            $this->url = 'https://homologacao.api.kryptonpay.com.br/';
        }

        $this->method = $method;
        $this->endPoint = $endPoint;
        $this->response = new Response();
    }

    public function call(object $data = null): object
    {
        try {
            $this->client = new GuzzleClient();
            $options['headers']['Authorization'] = sprintf('%s %s', 'Bearer', trim($this->apiContext->getApiToken()));
            $options['json'] = null;

            if ($data) {
                $options['json'] = $this->normalize($data);
            }

            return $this->handleApiReturn(
                $this->client->request($this->method, $this->url . $this->endPoint, $options)
            );
        } catch (\Exception $e) {
            return $this->handleApiError($e);
        }
    }

    public static function arrayRemoveNull($item)
    {
        if (!\is_array($item)) {
            return $item;
        }

        return (new Collection($item))
            ->reject(function ($item) {
                return null === $item;
            })
            ->flatMap(function ($item, $key) {
                return is_numeric($key)
                ? [self::arrayRemoveNull($item)]
                : [$key => self::arrayRemoveNull($item)];
            })
            ->toArray();
    }

    private function normalize(object $data): array
    {
        $serializer = new Serializer([new ObjectNormalizer()]);
        $data = self::arrayRemoveNull($serializer->normalize($data));

        foreach ($data as $key => $d) {
            if (empty($d)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    private function handleApiReturn($response): object
    {
        $return = null;
        $successCode = [200, 201, 204];
        if (\in_array($response->getStatusCode(), $successCode)) {
            $return = json_decode($response->getBody());
        }

        return $return;
    }

    private function handleApiError(Exception $e): object
    {
        switch ($e->getCode()) {
            case 400:
                $return = json_decode($e->getResponse()->getBody());
                $this->response->code = (int) $e->getCode();
                $this->response->errorCode = (int) $return->errors->errorCode;
                $this->response->messages = [$return->errors->error->message];

                return $this->response;
                break;
            case 401:
                $return = json_decode($e->getResponse()->getBody());
                $this->response->code = (int) $e->getCode();
                $this->response->errorCode = (int) $return->code;
                $this->response->messages = [$return->message];

                return $this->response;
                break;
            case 403:
                $this->response->code = (int) $e->getCode();
                $this->response->messages = ['Erro: 403'];
                unset($this->response->errorCode);

                return $this->response;
                break;
            case 404:
                $this->response->code = (int) $e->getCode();
                $this->response->messages = ['Erro: 404'];
                unset($this->response->errorCode);

                return $this->response;
                break;
            case 405:
                $this->response->code = (int) $e->getCode();
                $this->response->messages = ['Erro: 405'];
                unset($this->response->errorCode);

                return $this->response;
                break;
            case 422:
                $return = json_decode($e->getResponse()->getBody());

                $this->response->code = (int) $e->getCode();
                $this->response->messages = get_object_vars($return->errors);
                unset($this->response->errorCode);

                return $this->response;
                break;
            case 500:
                $this->response->code = (int) $e->getCode();
                $this->response->messages = ['Erro: 500'];
                unset($this->response->errorCode);

                return $this->response;
                break;
            case 503:
                $this->response->code = (int) $e->getCode();
                $this->response->messages = ['Erro: 503'];
                unset($this->response->errorCode);

                return $this->response;
                break;
            default:
                $this->response->code = (int) $e->getCode();
                $this->response->messages = ['Erro: 500'];
                unset($this->response->errorCode);

                return $this->response;
                break;
        }
    }
}
