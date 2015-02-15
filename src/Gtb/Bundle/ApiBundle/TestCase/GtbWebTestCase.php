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

    /**
     * Wrapper for the \Symfony\Component\BrowserKit\Client::request() method. Documentation borrowed from Client.
     *
     * @param string $method        The request method
     * @param string $uri           The URI to fetch
     * @param array  $parameters    The Request parameters
     * @param array  $files         The files
     * @param array  $server        The server parameters (HTTP headers are referenced with a HTTP_ prefix as PHP does)
     * @param string $content       The raw body data
     * @param bool   $changeHistory Whether to update the history or not (only used internally for back(), forward(), and reload())
     *
     * @return Response
     */
    protected function requestJson($method, $uri, array $parameters = array(), array $files = array(), array $server = array(), $content = null, $changeHistory = true)
    {
        $client = $this->getClient();
        $client->request($method, $uri, $parameters, $files, $server, $content, $changeHistory);

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
