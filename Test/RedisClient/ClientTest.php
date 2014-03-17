<?php
/**
 * Created by PhpStorm.
 * User: miquel
 * Date: 17/03/14
 * Time: 11:38
 */

namespace Solilokiam\HttpRedisCache\Test\RedisClient;


use Solilokiam\HttpRedisCache\RedisClient\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $redisMock;

    protected function setUp()
    {
        $this->redisMock = $this->getMock('\Redis');
    }

    public function testSimpleConnect()
    {
        $client = new Client(array('host' => 'localhost'));

        $return = $client->createConnection();

        $this->assertTrue($return);
    }

    public function testReadWriteKey()
    {
        $client = new Client(array('host' => 'localhost'));

        $connection = $client->createConnection();

        $this->assertTrue($connection);

        $client->set('testkey', '1234');

        $result = $client->get('testkey');

        $this->assertEquals('1234', $result);

        $client->del('testkey');
    }

    public function testDelKey()
    {
        $client = new Client(array('host' => 'localhost'));

        $connection = $client->createConnection();

        $this->assertTrue($connection);

        $client->set('testkey', '1234');
        $client->del('testkey');

        $result = $client->get('testkey');

        $this->assertEquals(false, $result);


    }

    public function testHashGet()
    {
        $client = new Client(array('host' => 'localhost'));

        $connection = $client->createConnection();

        $this->assertTrue($connection);

        $client->hSetNx('testkey', 'testhash', 1);

        $result = $client->hGet('testkey', 'testhash');

        $this->assertEquals(1, $result);

        $client->del('testkey');
    }
}
