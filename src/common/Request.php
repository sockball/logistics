<?php

namespace sockball\logistics\common;

class Request
{
    public const CONTENT_TYPE_JSON = 'json';
    public const CONTENT_TYPE_FORM = 'form';
    public const CONTENT_TYPE_FILE = 'file';

    public function post(string $requestUrl, array $params, string $_contentType = self::CONTENT_TYPE_JSON)
    {
        $contentType = $this->getContentType($_contentType);
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: {$contentType};charset=utf-8",
                'content' => $this->getContent($_contentType, $params),
            ],
        ]);
        $result = file_get_contents($requestUrl, false, $context);

        return json_decode($result);
    }

    private function getContentType(string $contentType)
    {
        switch ($contentType)
        {
            case self::CONTENT_TYPE_JSON:
            default:
                return 'application/json';

            case self::CONTENT_TYPE_FORM:
                return 'application/x-www-form-urlencoded';

            case self::CONTENT_TYPE_FILE:
                return 'multipart/form-data';
        }
    }

    private function getContent(string $contentType, array $params)
    {
        switch ($contentType)
        {
            case self::CONTENT_TYPE_JSON:
            default:
                return json_encode($params, JSON_UNESCAPED_ENCODE);

            case self::CONTENT_TYPE_FORM:
            case self::CONTENT_TYPE_FILE:
                return http_build_query($params);
        }   
    }
}
