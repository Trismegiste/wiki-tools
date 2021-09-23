<?php

// Main

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

// register commands
$application->add(new App\Command\CategoryConcat());
$application->add(new App\Command\CategoryList());
$application->add(new App\Command\DocumentRender());
$application->add(new App\Command\CategoryCompil());
$application->add(new App\Command\TemplateRender());

$application->run();
