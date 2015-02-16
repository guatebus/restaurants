<?php

namespace Gtb\Bundle\ApiBundle\Tests\Controller;

use Gtb\Bundle\ApiBundle\TestCase\GtbWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RestaurantControllerTest extends GtbWebTestCase
{
    public function testGetRestaurants()
    {
        $response = $this->requestJson('GET', $this->getClient()->getContainer()->get('router')->generate('get_restaurants'));

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertJsonStringEqualsJsonFile(__DIR__."/../Resources/get_restaurants_no_prefix.json", $this->stripJsonPrefix($response->getContent()));

        //Assertions after this point are redundant to the assertJsonStringEqualsJsonFile() assertion above,
        //they are here for showcasing/reference purposes:

        $content = $this->decodeSecureJsonContent($response->getContent());

        $this->assertArrayHasKey('entities', $content);
        $restaurants = $content['entities'];

        $this->assertCount(5, $restaurants);
        $restaurant = $restaurants[4];
        $this->assertArrayHasKey('id', $restaurant);
        $this->assertNotEmpty($restaurant['id']);
        $this->assertArrayHasKey('name', $restaurant);
        $this->assertNotEmpty($restaurant['name']);
    }

    public function testGetRestaurant()
    {
        $response = $this->requestJson('GET', $this->getClient()->getContainer()->get('router')->generate('get_restaurant', array('id' => 1)));

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertJsonStringEqualsJsonFile(__DIR__."/../Resources/get_restaurant_no_prefix.json", $this->stripJsonPrefix($response->getContent()));
    }

    public function testPostRestaurant()
    {
        $restaurantName = "Test Restaurant";
        $restaurantMax = 231;
        $rawBody = '{"gtb_bundle_corebundle_restaurant": { "name": "'.$restaurantName.'", "maxCapacity": '.$restaurantMax.' }}';
        $response = $this->requestJson(
            'POST',
            $this->getClient()->getContainer()->get('router')->generate('post_restaurant'),
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
            ),
            $rawBody
        );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $this->assertJsonStringEqualsJsonFile(__DIR__."/../Resources/post_restaurant_no_prefix.json", $this->stripJsonPrefix($response->getContent()));

        $restaurant = $this->getClient()->getContainer()->get('doctrine')->getManager()->getRepository('GtbCoreBundle:Restaurant')->find(6);

        $this->assertEquals($restaurantName, $restaurant->getName());
        $this->assertEquals($restaurantMax, $restaurant->getMaxCapacity());
    }

    /**
     * @depends testPostRestaurant
     */
    public function testPutRestaurant()
    {
        $restaurantName = "Modified Test Restaurant";
        $restaurantMax = 697;
        $rawBody = '{"gtb_bundle_corebundle_restaurant": { "name": "'.$restaurantName.'", "maxCapacity": '.$restaurantMax.' }}';
        $response = $this->requestJson(
            'PUT',
            $this->getClient()->getContainer()->get('router')->generate('put_restaurant', array('id' => 6)),
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
            ),
            $rawBody
        );

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertJsonStringEqualsJsonFile(__DIR__."/../Resources/put_restaurant_no_prefix.json", $this->stripJsonPrefix($response->getContent()));

        $restaurant = $this->getClient()->getContainer()->get('doctrine')->getManager()->getRepository('GtbCoreBundle:Restaurant')->find(6);

        $this->assertEquals($restaurantName, $restaurant->getName());
        $this->assertEquals($restaurantMax, $restaurant->getMaxCapacity());
    }

    /**
     * @depends testPutRestaurant
     */
    public function testDeleteRestaurant()
    {
        $client = $this->getClient();
        $client->request('DELETE', $this->getClient()->getContainer()->get('router')->generate('delete_restaurant', array('id' => 6)));

        $this->assertEquals(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());

        $restaurant = $this->getClient()->getContainer()->get('doctrine')->getManager()->getRepository('GtbCoreBundle:Restaurant')->find(6);

        $this->assertNull($restaurant);
    }
}
