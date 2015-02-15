<?php

namespace Gtb\Bundle\ApiBundle\Tests\Controller;

use Gtb\Bundle\ApiBundle\TestCase\GtbWebTestCase;

class RestaurantControllerTest extends GtbWebTestCase
{
    public function testGetRestaurants()
    {
        $response = $this->makeRequest('GET', $this->getClient()->getContainer()->get('router')->generate('get_restaurants'));

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
        $this->assertArrayHasKey('max_capacity', $restaurant);
        $this->assertNotEmpty($restaurant['max_capacity']);
        $this->assertArrayHasKey('reservations', $restaurant);
        $this->assertEmpty($restaurant['reservations']);
    }

    public function testGetRestaurant()
    {
        $response = $this->makeRequest('GET', $this->getClient()->getContainer()->get('router')->generate('get_restaurant', array('id' => 1)));

        $this->assertJsonStringEqualsJsonFile(__DIR__."/../Resources/get_restaurant_no_prefix.json", $this->stripJsonPrefix($response->getContent()));
    }

    public function testPostRestaurant()
    {
        $restaurantName = "Test Restaurant";
        $restaurantMax = 231;
        $rawBody = '{"gtb_bundle_corebundle_restaurant": { "name": "'.$restaurantName.'", "maxCapacity": '.$restaurantMax.' }}';
        $response = $this->makeRequest(
            'POST',
            $this->getClient()->getContainer()->get('router')->generate('post_restaurant'),
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
            ),
            $rawBody
        );

        $this->assertJsonStringEqualsJsonFile(__DIR__."/../Resources/post_restaurant_no_prefix.json", $this->stripJsonPrefix($response->getContent()));

        $restaurant = $this->getClient()->getContainer()->get('doctrine')->getManager()->getRepository('GtbCoreBundle:Restaurant')->find(6);

        $this->assertEquals($restaurantName, $restaurant->getName());
        $this->assertEquals($restaurantMax, $restaurant->getMaxCapacity());
    }

    public function testPutRestaurant()
    {
        $restaurantName = "Modified Test Restaurant";
        $restaurantMax = 697;
        $rawBody = '{"gtb_bundle_corebundle_restaurant": { "name": "'.$restaurantName.'", "maxCapacity": '.$restaurantMax.' }}';
        $response = $this->makeRequest(
            'PUT',
            $this->getClient()->getContainer()->get('router')->generate('put_restaurant', array('id' => 6)),
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
            ),
            $rawBody
        );

        $this->assertJsonStringEqualsJsonFile(__DIR__."/../Resources/put_restaurant_no_prefix.json", $this->stripJsonPrefix($response->getContent()));

        $restaurant = $this->getClient()->getContainer()->get('doctrine')->getManager()->getRepository('GtbCoreBundle:Restaurant')->find(6);

        $this->assertEquals($restaurantName, $restaurant->getName());
        $this->assertEquals($restaurantMax, $restaurant->getMaxCapacity());
    }

    public function testDeleteRestaurant()
    {
        $restaurantName = "Modified Test Restaurant";
        $restaurantMax = 697;
        $rawBody = '{"gtb_bundle_corebundle_restaurant": { "name": "'.$restaurantName.'", "maxCapacity": '.$restaurantMax.' }}';
        $response = $this->makeRequest(
            'PUT',
            $this->getClient()->getContainer()->get('router')->generate('put_restaurant', array('id' => 6)),
            array(),
            array(),
            array(
                'CONTENT_TYPE' => 'application/json',
            ),
            $rawBody
        );

        $this->assertJsonStringEqualsJsonFile(__DIR__."/../Resources/put_restaurant_no_prefix.json", $this->stripJsonPrefix($response->getContent()));

        $restaurant = $this->getClient()->getContainer()->get('doctrine')->getManager()->getRepository('GtbCoreBundle:Restaurant')->find(6);

        $this->assertEquals($restaurantName, $restaurant->getName());
        $this->assertEquals($restaurantMax, $restaurant->getMaxCapacity());
    }
}
