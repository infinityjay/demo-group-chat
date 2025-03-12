<?php

use Slim\App;

return function (App $app) {
    // Handle exceptions
    $app->addErrorMiddleware(true, true, true);
};