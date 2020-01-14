<?php

namespace sockball\logistics\lib;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Cookie\CookieJar;

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
    public static function post(string $requestUrl, array $params, bool $decode = true, array $options = [])
    {
        $client = self::getClient();
        if (!empty($params))
        {
            $options = array_merge($options, ['form_params' => $params]);
        }

        $result = (string) $client->request('POST', $requestUrl, $options)->getBody();

        return $decode ? json_decode($result) : $result;
    }

    public static function createCookie(array $cookies, string $domain)
    {
        $jar = new CookieJar();

        return $jar->fromArray($cookies, $domain);
    }

    /**
     * 获取验证码携带的cookie（暂不一定通用
     *
     * @param string $url
     * @param string $method
     * @return array
     */
    public static function getCookie(string $url, string $method = 'GET')
    {
        $client = self::getClient();
        $res = $client->request($method, $url);
        $header = $res->getHeader('set-cookie')[0] ?? null;
        preg_match('/=(.*);/', $header, $match);
        $cookie = $match[1] ?? '';

        return [$cookie, (string) $res->getBody()];
    }
}
