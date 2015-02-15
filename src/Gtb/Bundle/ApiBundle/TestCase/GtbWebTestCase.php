<?php

namespace Gtb\Bundle\ApiBundle\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GtbWebTestCase extends WebTestCase
{
    public function assertJsonResponse(Response $response)
    {
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }
}
