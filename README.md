README
======

What is HttpRedisCache?
-----------------------

Redis Cache is an improvement to Symfony 2 internal http cache. All cached data is stores in Redis instead of filesystem.
This can be useful when not much spacedisk is avaliable, or you want to share the cache data across several servers.

Requirements
------------

HttpRedisCache is only supported by symfony 2.3 . Any other versions may or may not work. Redis php extension installed.

Installation
------------
Add HttpRedisCache to your application's `composer.json` file
```json
{
    "require": {
        "solilokiam/httprediscache": "dev-master"
    }
}

Install the library and it's dependencies using the following command:
```bash
$ php composer.phar update solilokiam\httprediscache
```

Activate your symfony (internal http cache)[http://symfony.com/doc/current/book/http_cache.html#symfony2-reverse-proxy]:
```php
// web/app.php
require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
// wrap the default AppKernel with the AppCache one
$kernel = new AppCache($kernel);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
```

Make `AppCache` class extend HttpRedisCache instead on HttpCache like this:
```php
// app/AppCache.php
require_once __DIR__.'/AppKernel.php';


class AppCache extends \Solilokiam\HttpRedisCache\HttpRedisCache
{

}
```

Configuration
-------------
By default this library has the following configuration:
- redis host: localhost
- redis port: 6379
- redis password: ~
- redis database: ~
- redis options: ~

You can change it adding some method implementations in `AppCache`:
```php
// app/AppCache.php
require_once __DIR__.'/AppKernel.php';


class AppCache extends \Solilokiam\HttpRedisCache\HttpRedisCache
{
    public function getConnectionParams()
    {
        return array(
            'host' => 'localhost',
            'port' => '6739',
            'password' => 'somepassword',
            'database' => 'somedatabase',
            'options' => array(
                Redis::OPT_SERIALIZER => Redis::SERIALIZER_NONE,
                Redis::OPT_PREFIX => 'myAppName:'
            )
        );
    }
}
```

The only *required* array element is host others are optional.

You can also configure key prefixes adding some implementation methods in `AppCache`:
```php
// app/AppCache.php
require_once __DIR__.'/AppKernel.php';


class AppCache extends \Solilokiam\HttpRedisCache\HttpRedisCache
{
    ...

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
    ...
}
```

Remember that you can configure other cache settings the sameway you do with default symfony internal http cache.
```php
// app/AppCache.php
use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class AppCache extends HttpCache
{
    protected function getOptions()
    {
        return array(
            'debug'                  => false,
            'default_ttl'            => 0,
            'private_headers'        => array('Authorization', 'Cookie'),
            'allow_reload'           => false,
            'allow_revalidate'       => false,
            'stale_while_revalidate' => 2,
            'stale_if_error'         => 60,
        );
    }
}
```



Author
------
- Miquel Company Rodriguez (@solilokiam)

TODO
----
- Documentation
- Tests
- Symfony 2.4 support

