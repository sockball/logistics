<?php

namespace sockball\logistics\lib;

use GuzzleHttp\Client as HttpClient;

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
     * @return \stdClass|array|string
     */
    public static function get(string $requestUrl, array $params, bool $decode = true)
    {
        $client = self::getClient();
        $completeRequestUrl = $requestUrl . '?' . http_build_query($params);
        $result = (string) $client->request('GET', $completeRequestUrl)->getBody();

        return $decode ? json_decode($result) : $result;
    }

    /**
     * @param string $requestUrl
     * @param array $params
     * @param bool $decode
     * @return \stdClass|array|string
     */
    public static function post(string $requestUrl, array $params, bool $decode = true)
    {
        $client = self::getClient();
        $result = (string) $client->request('POST', $requestUrl, [
            'form_params' => $params,
        ])->getBody();

        return $decode ? json_decode($result) : $result;
    }
}
