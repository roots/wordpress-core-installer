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
use Composer\Installer\InstallationManager;
use Composer\IO\NullIO;
use Composer\Package\RootPackage;
use Composer\Util\HttpDownloader;
use Composer\Util\Loop;
use Roots\Composer\WordPressCorePlugin;
use PHPUnit\Framework\TestCase;

class WordPressCorePluginTest extends TestCase
{
    public function testActivate()
    {
        $composer = new Composer();
        $config = new Config();
        $composer->setConfig($config);
        
        // Set up root package
        $rootPackage = new RootPackage('root/package', '1.0.0', '1.0.0');
        $composer->setPackage($rootPackage);
        
        // Set up DownloadManager
        $downloadManager = new \Composer\Downloader\DownloadManager(
            new NullIO(),
            false,
            new \Composer\Util\Filesystem()
        );
        $composer->setDownloadManager($downloadManager);
        
        $nullIO = new NullIO();
        $http = new HttpDownloader($nullIO, $composer->getConfig());
        $loop = new Loop($http);
        $installationManager = new InstallationManager($loop, $nullIO);
        $composer->setInstallationManager($installationManager);

        $plugin = new WordPressCorePlugin();
        $plugin->activate($composer, $nullIO);

        $installer = $installationManager->getInstaller('wordpress-core');

        $this->assertInstanceOf('\Roots\Composer\WordPressCoreInstaller', $installer);
    }
}
