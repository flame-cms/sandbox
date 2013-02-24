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
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->addDirectory(__DIR__ . '/../libs/flame/cms')
	->register();
$configurator->addParameters(array('appDir' => __DIR__));

$configurator->addConfig(__DIR__ . '/AppModule/config/config.neon');
$configurator->addConfig(__DIR__ . '/AppModule/config/services.neon');
$configurator->addConfig(__DIR__ . '/AppModule/config/factories.neon');
if(file_exists($configDev = __DIR__ . '/AppModule/config/config.dev.neon'))
	$configurator->addConfig($configDev);

$container = $configurator->createContainer();
return $container;