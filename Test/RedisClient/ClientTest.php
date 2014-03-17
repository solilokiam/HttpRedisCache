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
        $this->redisMock = $this->getMock('Redis');
    }

    public function testSimpleConnect()
    {
        $client = new Client(array('host' => 'localhost'));

        $this->redisMock->expects($this->once())
            ->method('connect')
            ->with($this->equalTo('localhost'), $this->equalTo(null));
    }
}
