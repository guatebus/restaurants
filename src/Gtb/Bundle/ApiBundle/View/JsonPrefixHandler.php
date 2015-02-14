<?php

namespace Gtb\Bundle\ApiBundle\View;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;

/**
 * Prefix all JSON responses with a safety prefix
 *
 * @see https://docs.angularjs.org/api/ng/service/$http#security-considerations
 */
class JsonPrefixHandler
{
    /**
     * @var string
     */
    const PREFIX = ")]}',\n";

    /**
     * Create a prefixed JSON response
     *
     * @param ViewHandler $handler
     * @param View        $view
     * @param Request     $request
     * @param string      $format
     *
     * @return Response
     */
    public function createResponse(ViewHandler $handler, View $view, Request $request, $format)
    {
        $response = $handler->createResponse($view, $request, 'json');
        $response->setContent(self::PREFIX . $response->getContent());
        return $response;
    }
}
