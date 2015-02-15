<?php

namespace Gtb\Bundle\ApiBundle\TestCase;

use Gtb\Bundle\ApiBundle\View\JsonPrefixHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Gtb\Bundle\ApiBundle\Exception\JsonDecodeException;

class GtbWebTestCase extends WebTestCase
{
    protected $client;

    protected function getClient()
    {
        return $this->client ? $this->client : static::createClient();
    }

    protected function makeRequest($httpMethod, $endpoint)
    {
        $client = $this->getClient();
        $crawler = $client->request($httpMethod, $endpoint);

        $response = $client->getResponse();
        $this->assertJsonResponse($response);
        $this->assertJsonIsPrefixed($response->getContent());

        return $response;
    }

    protected function assertJsonResponse(Response $response)
    {
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    protected function assertJsonIsPrefixed($content)
    {
        $this->assertStringStartsWith(JsonPrefixHandler::PREFIX, $content);
    }

    protected function decodeSecureJsonContent($content)
    {
        $data = json_decode($this->stripJsonPrefix($content), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonDecodeException("JSON error code: ".json_last_error()." occurred when decoding");
        }

        return $data;
    }

    protected function stripJsonPrefix($content)
    {
        return substr($content, strlen(JsonPrefixHandler::PREFIX));
    }
}
