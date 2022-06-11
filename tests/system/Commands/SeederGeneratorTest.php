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

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\StreamFilterTrait;

/**
 * @internal
 */
final class SeederGeneratorTest extends CIUnitTestCase
{
    use StreamFilterTrait;

    protected function setUp(): void
    {
        $this->registerStreamFilterClass()
            ->appendStreamOutputFilter()
            ->appendStreamErrorFilter();
    }

    protected function tearDown(): void
    {
        $this->removeStreamOutputFilter()->removeStreamErrorFilter();

        $result = str_replace(["\033[0;32m", "\033[0m", "\n"], '', $this->getStreamFilterBuffer());
        $file   = str_replace('APPPATH' . DIRECTORY_SEPARATOR, APPPATH, trim(substr($result, 14)));
        if (is_file($file)) {
            unlink($file);
        }
    }

    public function testGenerateSeeder()
    {
        command('make:seeder cars');
        $this->assertStringContainsString('File created: ', $this->getStreamFilterBuffer());
        $this->assertFileExists(APPPATH . 'Database/Seeds/Cars.php');
    }

    public function testGenerateSeederWithOptionSuffix()
    {
        command('make:seeder cars -suffix');
        $this->assertStringContainsString('File created: ', $this->getStreamFilterBuffer());
        $this->assertFileExists(APPPATH . 'Database/Seeds/CarsSeeder.php');
    }
}
