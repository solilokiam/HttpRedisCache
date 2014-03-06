<?php
/**
 * Created by PhpStorm.
 * User: miquel
 * Date: 5/03/14
 * Time: 15:51
 */

namespace Solilokiam\HttpRedisCache\RedisClient;

use Redis;

class Client
{
    protected $redis;

    protected $host;
    protected $port;
    protected $password;
    protected $database;
    protected $options;


    public function __construct(array $params)
    {
        $this->redis = new Redis();
        $this->host = $params['host'];

        if (array_key_exists('port', $params)) {
            $this->port = $params['port'];
        }

        if (array_key_exists('password', $params)) {
            $this->password = $params['password'];
        }

        if (array_key_exists('database', $params)) {
            $this->database = $params['database'];
        }

        if (array_key_exists('options', $params)) {
            $this->options = $params['options'];
        }
    }

    public function __destroy()
    {
        $this->redis->close();
    }

    public function createConnection()
    {
        $success = $this->redis->connect($this->host, $this->port);

        if ($this->password != null) {
            $success &= $this->redis->auth($this->password);
        }

        if ($this->database != null) {
            $success &= $this->redis->select($this->database);
        }

        if ($this->options != null) {
            foreach ($this->options as $key => $option) {
                $success &= $this->redis->setOption($key, $option);
            }
        }

        return $success;
    }

    public function __call($name, array $arguments)
    {
        switch (strtolower($name)) {
            case 'connect':
            case 'open':
            case 'pconnect':
            case 'popen':
            case 'setoption':
            case 'getoption':
            case 'auth':
            case 'select':
                return false;
        }

        $result = call_user_func_array(array($this->redis, $name), $arguments);

        return $result;
    }
}
