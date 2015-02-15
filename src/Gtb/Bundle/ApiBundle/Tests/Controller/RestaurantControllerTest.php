<?php

namespace Gtb\Bundle\ApiBundle\Tests\Controller;

use Gtb\Bundle\ApiBundle\TestCase\GtbWebTestCase;

class RestaurantControllerTest extends GtbWebTestCase
{
    public function testGetRestaurants()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', $client->getContainer()->get('router')->generate('get_restaurants'));

        $this->assertJsonResponse($client->getResponse());

//        print_r($client->getResponse());
    }
}
