<?php

/**
 * WordPress Core Installer - A Composer to install WordPress in a webroot subdirectory
 * Copyright (C) 2013    John P. Bloch
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace Tests\Roots\Composer\phpunit;

use Composer\Composer;
use Composer\Config;
use Composer\IO\NullIO;
use Composer\Package\Package;
use Composer\Package\RootPackage;
use Roots\Composer\WordPressCoreInstaller;
use PHPUnit\Framework\TestCase;

class WordPressCoreInstallerTest extends TestCase
{
    public function testSupports()
    {
        $composer = $this->createComposer();
        $composer->setPackage(new RootPackage('root/package', '1.0.0', '1.0.0'));
        $installer = new WordPressCoreInstaller(new NullIO(), $composer);

        $this->assertTrue($installer->supports('wordpress-core'));
        $this->assertFalse($installer->supports('not-wordpress-core'));
    }

    public function testDefaultInstallDir()
    {
        $composer = $this->createComposer();
        $composer->setPackage(new RootPackage('root/package', '1.0.0', '1.0.0'));
        $installer = new WordPressCoreInstaller(new NullIO(), $composer);
        $package   = new Package('johnpbloch/test-package', '1.0.0.0', '1.0.0');

        $this->assertEquals('wordpress', $installer->getInstallPath($package));
    }

    public function testSingleRootInstallDir()
    {
        $composer    = $this->createComposer();
        $rootPackage = new RootPackage('test/root-package', '1.0.1.0', '1.0.1');
        $composer->setPackage($rootPackage);
        $installDir = 'tmp-wp-' . rand(0, 9);
        $rootPackage->setExtra(array(
            'wordpress-install-dir' => $installDir,
        ));
        $installer = new WordPressCoreInstaller(new NullIO(), $composer);

        $this->assertEquals(
            $installDir,
            $installer->getInstallPath(
                new Package('not/important', '1.0.0.0', '1.0.0')
            )
        );
    }

    public function testArrayOfInstallDirs()
    {
        $composer    = $this->createComposer();
        $rootPackage = new RootPackage('test/root-package', '1.0.1.0', '1.0.1');
        $composer->setPackage($rootPackage);
        $rootPackage->setExtra(array(
            'wordpress-install-dir' => array(
                'test/package-one' => 'install-dir/one',
                'test/package-two' => 'install-dir/two',
            ),
        ));
        $installer = new WordPressCoreInstaller(new NullIO(), $composer);

        $this->assertEquals(
            'install-dir/one',
            $installer->getInstallPath(
                new Package('test/package-one', '1.0.0.0', '1.0.0')
            )
        );

        $this->assertEquals(
            'install-dir/two',
            $installer->getInstallPath(
                new Package('test/package-two', '1.0.0.0', '1.0.0')
            )
        );
    }

    public function testCorePackageCanDefineInstallDirectory()
    {
        $composer = $this->createComposer();
        $composer->setPackage(new RootPackage('root/package', '1.0.0', '1.0.0'));
        $installer = new WordPressCoreInstaller(new NullIO(), $composer);
        $package   = new Package('test/has-default-install-dir', '0.1.0.0', '0.1');
        $package->setExtra(array(
            'wordpress-install-dir' => 'not-wordpress',
        ));

        $this->assertEquals('not-wordpress', $installer->getInstallPath($package));
    }

    public function testCorePackageDefaultDoesNotOverrideRootDirectoryDefinition()
    {
        $composer = $this->createComposer();
        $composer->setPackage(new RootPackage('test/root-package', '0.1.0.0', '0.1'));
        $composer->getPackage()->setExtra(array(
            'wordpress-install-dir' => 'wp',
        ));
        $installer = new WordPressCoreInstaller(new NullIO(), $composer);
        $package   = new Package('test/has-default-install-dir', '0.1.0.0', '0.1');
        $package->setExtra(array(
            'wordpress-install-dir' => 'not-wordpress',
        ));

        $this->assertEquals('wp', $installer->getInstallPath($package));
    }

    public function testTwoPackagesCannotShareDirectory()
    {
        $this->jpbExpectException(
            '\InvalidArgumentException',
            'Two packages (test/bazbat and test/foobar) cannot share the same directory!'
        );
        $composer  = $this->createComposer();
        $composer->setPackage(new RootPackage('root/package', '1.0.0', '1.0.0'));
        $installer = new WordPressCoreInstaller(new NullIO(), $composer);
        $package1  = new Package('test/foobar', '1.1.1.1', '1.1.1.1');
        $package2  = new Package('test/bazbat', '1.1.1.1', '1.1.1.1');

        $installer->getInstallPath($package1);
        $installer->getInstallPath($package2);
    }

    public function testTwoPackagesCannotShareDirectoryUnlessWpCoreType()
    {
        $composer  = $this->createComposer();
        $composer->setPackage(new RootPackage('root/package', '1.0.0', '1.0.0'));
        $installer = new WordPressCoreInstaller(new NullIO(), $composer);
        $package1  = new Package('johnpbloch/wordpress', '4.9.8', '4.9.8');
        $package1->setType('wordpress-core');
        $package2  = new Package('roots/wordpress', '5.0', '5.0');
        $package2->setType('wordpress-core');

        $installer->getInstallPath($package1);
        $installer->getInstallPath($package2);

        $this->assertTrue(true); // no exceptions thrown
    }

    /**
     * @dataProvider dataProviderSensitiveDirectories
     */
    public function testSensitiveInstallDirectoriesNotAllowed($directory)
    {
        $this->jpbExpectException(
            '\InvalidArgumentException',
            '/Warning! .+? is an invalid WordPress install directory \(from test\/package\)!/',
            true
        );
        $composer  = $this->createComposer();
        $composer->setPackage(new RootPackage('root/package', '1.0.0', '1.0.0'));
        $installer = new WordPressCoreInstaller(new NullIO(), $composer);
        $package   = new Package('test/package', '1.1.0.0', '1.1');
        $package->setExtra(array( 'wordpress-install-dir' => $directory ));
        $installer->getInstallPath($package);
    }

    public function dataProviderSensitiveDirectories()
    {
        return array(
            array( '.' ),
            array( 'vendor' ),
        );
    }

    /**
     * @before
     * @afterClass
     */
    public static function resetInstallPaths()
    {
        $prop = new \ReflectionProperty('\Roots\Composer\WordPressCoreInstaller', '_installedPaths');
        $prop->setAccessible(true);
        $prop->setValue(array());
    }

    /**
     * @return Composer
     */
    private function createComposer()
    {
        $composer = new Composer();
        $config = new Config();
        $composer->setConfig($config);
        
        // Set up DownloadManager with proper Filesystem
        $downloadManager = new \Composer\Downloader\DownloadManager(
            new \Composer\IO\NullIO(),
            false,
            new \Composer\Util\Filesystem()
        );
        $composer->setDownloadManager($downloadManager);

        return $composer;
    }

    private function jpbExpectException($class, $message = '', $isRegExp = false)
    {
        $this->expectException($class);
        if ($message) {
            if ($isRegExp) {
                if (method_exists($this, 'expectExceptionMessageRegExp')) {
                    $this->expectExceptionMessageRegExp($message);
                } else {
                    $this->expectExceptionMessageMatches($message);
                }
            } else {
                $this->expectExceptionMessage($message);
            }
        }
    }
}
