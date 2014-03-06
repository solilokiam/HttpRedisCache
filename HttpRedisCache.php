<?php
/**
 * Created by PhpStorm.
 * User: miquel
 * Date: 5/03/14
 * Time: 15:46
 */

namespace Solilokiam\HttpRedisCache;


use Solilokiam\HttpRedisCache\Store\RedisHttpStore;
use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class HttpRedisCache extends HttpCache
{
    public function createStore()
    {
        return new RedisHttpStore($this->getConnectionParams(), $this->getDigestKeyPrefix(), $this->getLockKey(
        ), $this->getMetadataKeyPrefix());
    }

    public function getConnectionParams()
    {
        return array('host' => 'localhost');
    }

    public function getDigestKeyPrefix()
    {
        return 'hrd';
    }

    public function getLockKey()
    {
        return 'hrl';
    }

    public function getMetadataKeyPrefix()
    {
        return 'hrm';
    }
}
