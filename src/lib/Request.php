<?php

namespace sockball\logistics\lib;

use GuzzleHttp\Client as HttpClient;
use Exception;

class Request
{
    private static $_client = null;
    private static function getClient()
    {
        if (self::$_client === null)
        {
            self::$_client = new HttpClient();
        }

        return clone self::$_client;
    }

    /**
     * @param string $requestUrl
     * @param array $params
     * @param bool $decode
     * @return Response
     */
    public static function get(string $requestUrl, array $params, bool $decode = true)
    {
        $client = self::getClient();
        $completeRequestUrl = $requestUrl . '?' . http_build_query($params);
        try {
            $result = (string) $client->request('GET', $completeRequestUrl)->getBody();
        } catch (Exception $e) {
            return self::failed($e->getMessage());
        }

        return self::success($decode ? json_decode($result) : $result);
    }

    /**
     * @param string $requestUrl
     * @param array $params
     * @param bool $decode
     * @return Response
     */
    public static function post(string $requestUrl, array $params, bool $decode = true)
    {
        $client = self::getClient();
        try {
            $result = (string) $client->request('POST', $requestUrl, [
                'form_params' => $params,
            ])->getBody();
        } catch (Exception $e) {
            return self::failed($e->getMessage());
        }

        return self::success($decode ? json_decode($result) : $result);
    }

    private static function success($raw)
    {
        return new Response($raw);
    }

    private static function failed($error)
    {
        return new Response(null, Response::RESPONSE_FAILED, $error);
    }
}
