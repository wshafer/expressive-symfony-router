<?php

namespace WShafer\Expressive\Symfony\Router\Test\Cache;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use WShafer\Expressive\Symfony\Router\Cache\Cache;

class CacheTest extends TestCase
{
    /** @var MockObject|Route */
    protected $mockRoute;

    public function setup()
    {
        $this->mockRoute =$this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConstructorNoConfig()
    {
        $cache = new Cache();

        $this->assertFalse($cache->isCacheEnabled());
        $this->assertEmpty($cache->getCacheFile());
    }

    public function testFetchCacheNoConfig()
    {
        $cache = new Cache();

        $data = $cache->fetchCache();
        $this->assertTrue(is_array($data));
        $this->assertEmpty($data);
    }

    public function testPopulateCollectionNoCache()
    {
        $mockCollection = $this->getMockBuilder(RouteCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockCollection->expects($this->never())
            ->method('add');

        $cache = new Cache();
        $cache->populateCollectionFromCache($mockCollection);
    }

    public function testAddNoCache()
    {
        $mockRoute = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cache = new Cache();
        $cache->add('testNoCache', $mockRoute);

        $this->assertFalse($cache->has('testNoCache'));
    }

    public function testWriteCacheNoCacheEnabled()
    {
        $cache = new Cache();
        $result = $cache->writeCache();
        $this->assertTrue($result);
    }

    protected function addRoute(Cache $cache, $realRoute = false)
    {
        $route = $this->mockRoute;

        if ($realRoute) {
            $route = $realRoute;
        } else {
            $route->expects($this->once())
                ->method('compile')
                ->willReturn(true);
        }

        $cache->add('testAdd', $route);
        $this->assertTrue($cache->has('testAdd'));
        $this->assertTrue($cache->doesCacheNeedsUpdate());
    }

    public function testAddCacheEnabled()
    {
        $cache = new Cache([
            Cache::CONFIG_CACHE_ENABLED => true,
            Cache::CONFIG_CACHE_FILE => '/dir/does/not/exist'
        ]);

        $this->addRoute($cache);
    }

    /**
     * @expectedException \WShafer\Expressive\Symfony\Router\Exception\InvalidCacheDirectoryException
     */
    public function testWriteCacheInvalidDir()
    {
        $cache = new Cache([
            Cache::CONFIG_CACHE_ENABLED => true,
            Cache::CONFIG_CACHE_FILE => '/dir/does/not/exist'
        ]);

        $this->addRoute($cache);
        $cache->writeCache();
    }

    /**
     * @expectedException \WShafer\Expressive\Symfony\Router\Exception\InvalidCacheDirectoryException
     */
    public function testWriteCacheDirNotWritable()
    {
        $dir = sys_get_temp_dir().'/not-writable';
        @rmdir($dir);
        mkdir($dir);
        chmod($dir, 0444);

        $cache = new Cache([
            Cache::CONFIG_CACHE_ENABLED => true,
            Cache::CONFIG_CACHE_FILE => $dir.'/cache.file'
        ]);

        $this->addRoute($cache);
        $cache->writeCache();
        chmod($dir, 0755);
        @rmdir($dir);
    }

    protected function writecache($dir, $file, Cache $cache, $realRoute = false)
    {
        @unlink($file);
        @rmdir($dir);
        mkdir($dir);
        $this->addRoute($cache, $realRoute);
        $result = $cache->writeCache();
        $this->assertTrue($result);
        $this->assertFileExists($file);
    }

    public function testWriteCacheFile()
    {
        $dir = sys_get_temp_dir().'/cache-test';
        $file = $dir.'/cache.file';

        $cache = new Cache([
            Cache::CONFIG_CACHE_ENABLED => true,
            Cache::CONFIG_CACHE_FILE => $file
        ]);

        $this->writecache($dir, $file, $cache);

        unlink($file);
        rmdir($dir);
    }

    public function testFetchCache()
    {
        $dir = sys_get_temp_dir().'/cache-test';
        $file = $dir.'/cache.file';

        $cache = new Cache([
            Cache::CONFIG_CACHE_ENABLED => true,
            Cache::CONFIG_CACHE_FILE => $file,
        ]);

        $this->writecache($dir, $file, $cache);

        $expected = unserialize(file_get_contents($file));
        $result = $cache->fetchCache();

        $this->assertEquals($expected, $result);

        unlink($file);
        rmdir($dir);
    }

    /**
     * @expectedException \WShafer\Expressive\Symfony\Router\Exception\InvalidCacheException
     */
    public function testFetchCacheEmptyFile()
    {
        $dir = sys_get_temp_dir().'/cache-test';
        $file = $dir.'/cache.file';

        @unlink($file);
        @rmdir($dir);
        mkdir($dir);

        $cache = new Cache([
            Cache::CONFIG_CACHE_ENABLED => true,
            Cache::CONFIG_CACHE_FILE => $file,
        ]);

        file_put_contents($file, '');
        $cache->fetchCache();

        unlink($file);
        rmdir($dir);
    }

    public function testInvalidateCacheNoConfig()
    {
        $dir = sys_get_temp_dir().'/cache-test';
        $file = $dir.'/cache.file';

        $cache = new Cache([
            Cache::CONFIG_CACHE_ENABLED => true,
            Cache::CONFIG_CACHE_FILE => $file,
        ]);

        $this->writecache($dir, $file, $cache);

        $cache->invalidateCacheFile();
        $this->assertFileNotExists($file);
        rmdir($dir);
    }

    public function testInvalidateCache()
    {
        $dir = sys_get_temp_dir().'/cache-test';
        $file = $dir.'/cache.file';

        @unlink($file);
        @rmdir($dir);
        mkdir($dir);

        $cache = new Cache();
        file_put_contents($file, '');

        $cache->invalidateCacheFile();

        $this->assertFileExists($file);
        unlink($file);
        rmdir($dir);
    }

    public function testPopulateCollection()
    {
        $dir = sys_get_temp_dir().'/cache-test';
        $file = $dir.'/cache.file';

        $cache = new Cache([
            Cache::CONFIG_CACHE_ENABLED => true,
            Cache::CONFIG_CACHE_FILE => $file
        ]);

        $route = new Route('/');
        $this->writecache($dir, $file, $cache, $route);

        $mockCollection = $this->getMockBuilder(RouteCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockCollection->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo('testAdd'),
                $this->equalTo($route)
            );

        $result = $cache->populateCollectionFromCache($mockCollection);
        $this->assertEquals($mockCollection, $result);

        unlink($file);
        rmdir($dir);
    }
}
