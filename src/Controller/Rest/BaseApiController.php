<?php


namespace App\Controller\Rest;

use Nyholm\Psr7\Factory\Psr17Factory;
use OneSignal\Config;
use OneSignal\OneSignal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class BaseApiController extends AbstractController
{
    protected const APPLICATION_ID = '';
    protected const APPLICATION_AUTH_KEY = '';
    protected const USER_AUTH_KEY = '';

    protected function getResponse(
        array $data=null, int $statusCode = Response::HTTP_OK)
    {
        $response = new JsonResponse();
        if (!is_null($data))
        {
            $result['data'] = $data;
            $response->setContent(json_encode($result));
        }
        $response->setStatusCode($statusCode);
        return $response;
    }

    protected function getContent(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (is_null($data ) && $request->getMethod() != 'GET')
            throw new BadRequestHttpException('No se han recibido los datos');

        return $data;
    }


    protected static function getOneSignal()
    {
        $config = new Config(self::APPLICATION_ID,
            self::APPLICATION_AUTH_KEY,
            self::USER_AUTH_KEY);
        $httpClient = new Psr18Client();
        $requestFactory = $streamFactory = new Psr17Factory();

        return $oneSignal = new OneSignal($config, $httpClient, $requestFactory, $streamFactory);
    }

}