<?php
/**
 * Created by PhpStorm.
 * User: miquel
 * Date: 17/03/14
 * Time: 12:29
 */

namespace Solilokiam\HttpRedisCache\Test\Store;


use Solilokiam\HttpRedisCache\RedisClient\Client;
use Solilokiam\HttpRedisCache\Store\RedisHttpStore;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedisHttpStoreTest extends \PHPUnit_Framework_TestCase
{
    protected $store;
    protected $request;
    protected $response;

    public function setUp()
    {
        $this->store = new RedisHttpStore(array('host' => 'localhost'), 'hdr', 'ldr', 'mdr');

        $this->request = Request::create('/');

        $this->response = new Response('hello miquel', 200, array());

        $this->cleanKeys();
    }

    public function tearDown()
    {
        $this->store = null;
        $this->request = null;
        $this->request = null;

        $this->cleanKeys();
    }

    public function testReadsEmptyCacheAtKey()
    {
        $this->assertEmpty($this->getStoreMetadata('/nothing'));
    }

    public function testUnlockThatExists()
    {
        $cacheKey = $this->storeSimpleEntry();
        $this->store->lock($this->request);

        $this->assertTrue($this->store->unlock($this->request));
    }

    public function testUnlockThatDoesNotExist()
    {
        $this->assertFalse($this->store->unlock($this->request));
    }

    public function testRemoveEntriesForKeyWithPurge()
    {
        $request = Request::create('/foorequest');
        $this->store->write($request, new Response('fooresponse'));

        $metadata = $this->getStoreMetadata($request);
        $this->assertNotEmpty($metadata);

        $this->assertTrue($this->store->purge('/foorequest'));
        $this->assertEmpty($this->getStoreMetadata($request));

        $content = $this->loadContentData($metadata[0][1]['x-content-digest'][0]);
        $this->assertNotEmpty($content);

        $this->assertFalse($this->store->purge('/bar'));
    }

    public function testStoresACacheEntry()
    {
        $cacheKey = $this->storeSimpleEntry();

        $this->assertNotEmpty($this->getStoreMetadata($cacheKey));
    }

    protected function cleanKeys()
    {
        $client = new Client(array('host' => 'localhost'));

        $client->createConnection();
        $client->flushAll();
    }

    protected function storeSimpleEntry($path = null, $headers = array())
    {
        if (null === $path) {
            $path = '/test';
        }

        $this->request = Request::create($path, 'get', array(), array(), array(), $headers);
        $this->response = new Response('test', 200, array('Cache-Control' => 'max-age=420'));

        return $this->store->write($this->request, $this->response);
    }

    protected function getStoreMetadata($key)
    {
        $r = new \ReflectionObject($this->store);
        $m = $r->getMethod('getMetadata');
        $m->setAccessible(true);

        if ($key instanceof Request) {
            $m1 = $r->getMethod('getMetadataKey');
            $m1->setAccessible(true);
            $key = $m1->invoke($this->store, $key);
        }

        return $m->invoke($this->store, $key);
    }

    protected function loadContentData($key)
    {
        $r = new \ReflectionObject($this->store);
        $m = $r->getMethod('load');
        $m->setAccessible(true);

        return $m->invoke($this->store, $key);
    }


}
