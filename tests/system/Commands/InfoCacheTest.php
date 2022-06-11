<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Commands;

use CodeIgniter\Cache\CacheFactory;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\StreamFilterTrait;
use Config\Services;

/**
 * @internal
 */
final class InfoCacheTest extends CIUnitTestCase
{
    use StreamFilterTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerStreamFilterClass()
            ->appendStreamOutputFilter()
            ->appendStreamErrorFilter();

        // Make sure we are testing with the correct handler (override injections)
        Services::injectMock('cache', CacheFactory::getHandler(config('Cache')));
    }

    protected function tearDown(): void
    {
        $this->removeStreamOutputFilter()->removeStreamErrorFilter();

        // restore default cache handler
        config('Cache')->handler = 'file';
    }

    protected function getBuffer()
    {
        return $this->getStreamFilterBuffer();
    }

    public function testInfoCacheErrorsOnInvalidHandler()
    {
        config('Cache')->handler = 'redis';
        cache()->save('foo', 'bar');
        command('cache:info');

        $this->assertStringContainsString('This command only supports the file cache handler.', $this->getBuffer());
    }

    public function testInfoCacheCanSeeFoo()
    {
        cache()->save('foo', 'bar');
        command('cache:info');

        $this->assertStringContainsString('foo', $this->getBuffer());
    }

    public function testInfoCacheCanSeeTable()
    {
        command('cache:info');

        $this->assertStringContainsString('Name', $this->getBuffer());
        $this->assertStringContainsString('Server Path', $this->getBuffer());
        $this->assertStringContainsString('Size', $this->getBuffer());
        $this->assertStringContainsString('Date', $this->getBuffer());
    }

    public function testInfoCacheCannotSeeFoo()
    {
        cache()->delete('foo');
        command('cache:info');

        $this->assertStringNotContainsString('foo', $this->getBuffer());
    }
}
