<?php
/**
 * Script for composer, to symlink bootstrap lib into Bundle
 *
 * Maybe nice to convert this to a command and then reuse command in here.
 */
namespace Mazenovi\TodoMVCBundle\Composer;

use Composer\Script\Event;
use Composer\Composer;
use Composer\Package\PackageInterface;
use Symfony\Component\Filesystem\Filesystem;

class ScriptHandler
{
    public static function postInstallSymlinkTwitterBootstrap(Event $event)
    {
        $packageName = 'twitter/bootstrap';
        $symlinkName = 'bundles/twitter';
        $IO = $event->getIO();
        $composer = $event->getComposer();
        $filesystem = new Filesystem();

        $sourcePackageName = self::findPackage($packageName, $composer);
        $sourcePackagePath = $composer->getInstallationManager()->getInstallPath($sourcePackageName);

        $options = self::getOptions($event);
        $webDir = $options['symfony-web-dir'];

        $symlinkPath = realpath(getcwd() . DIRECTORY_SEPARATOR . $webDir) . DIRECTORY_SEPARATOR . $symlinkName;

        $IO->write("Checking Symlink", FALSE);
        if (!file_exists($symlinkPath) || !is_link($symlinkPath)) {
            $IO->write(" ... Creating Symlink: " . $webDir . DIRECTORY_SEPARATOR . $symlinkName, FALSE);
            $filesystem->symlink($sourcePackagePath, $symlinkPath, true);
        }
        $IO->write(" ... <info>OK</info>");        
    }

    protected static function findPackage($packageName, $composer)
    {
        $packages = $composer->getRepositoryManager()->findPackages($packageName, null);
        foreach ($packages as $package) {
            if (self::isPackageInstalled($package, $composer)) {
                return $package;
            }
        }
    }

    protected static function isPackageInstalled(PackageInterface $package, $composer) {
        foreach ($composer->getRepositoryManager()
                ->getLocalRepositories() as $repo) {
            $installer = $composer->getInstallationManager()
                                ->getInstaller($package->getType());
            return $installer->isInstalled($repo, $package);
        }
        return false;
    }

    protected static function getOptions($event)
    {
        $options = array_merge(array(
            'symfony-app-dir' => 'app',
            'symfony-web-dir' => 'web',
            'symfony-assets-install' => 'hard'
        ), $event->getComposer()->getPackage()->getExtra());

        $options['symfony-assets-install'] = getenv('SYMFONY_ASSETS_INSTALL') ?: $options['symfony-assets-install'];

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }
}
