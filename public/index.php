<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/config/connect.php';

$app = AppFactory::create();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);


//post routes
require __DIR__ . '/../src/routes/posts.php';

require __DIR__ . '/../src/routes/comments.php';

$app->run();

