<?php
/**
 * Bootstrap
 *
 * @author  Jiří Šifalda
 * @package Flame-sandbox
 *
 * @date    14.07.12
 */

require __DIR__ . '/../libs/autoload.php';

$configurator = new \Flame\Config\Configurator();
// $configurator->setDebugMode(TRUE);

$doctrineExtension = new \Flame\Doctrine\Config\Extension;
$doctrineExtension->install($configurator);

$webloaderExtension = new \WebLoader\Nette\Extension();
$webloaderExtension->install($configurator);

$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()->addDirectory(__DIR__)->register();
$configurator->addParameters(array('appDir' => __DIR__));

$configurator->addConfig(__DIR__ . '/AppBundle/config/config.neon');
$configurator->addConfig(__DIR__ . '/LinkBundle/config/config.neon');
$configurator->addConfig(__DIR__ . '/SettingBundle/config/config.neon');
$configurator->addConfig(__DIR__ . '/UserBundle/config/config.neon');
$configurator->addConfig(__DIR__ . '/PostBundle/config/config.neon');
if(file_exists($configDev = __DIR__ . '/AppBundle/config/config.dev.neon'))
	$configurator->addConfig($configDev);

$container = $configurator->createContainer();

return $container;