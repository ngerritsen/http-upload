<?php
declare(strict_types=1);

use HttpUpload\AuthenticationMiddleware;
use HttpUpload\Extractor;
use HttpUpload\HttpUploadController;
use Psr\Container\ContainerInterface;
use Slim\App;

require 'vendor/autoload.php';

$config = require(__DIR__ . '/etc/config.php');

$app = new App($config['slim']);

$container = $app->getContainer();

$container[Extractor::class] = function () use ($config): Extractor {
    return new Extractor($config['rootDir']);
};

$container[AuthenticationMiddleware::class] = function () use ($config): AuthenticationMiddleware {
    return new AuthenticationMiddleware($config['key']);
};

$container[HttpUploadController::class] = function (ContainerInterface $container):HttpUploadController {
    return new HttpUploadController($container->get(Extractor::class));
};

$app->add(AuthenticationMiddleware::class);

$app->post('/', HttpUploadController::class . ':post');

$app->run();
