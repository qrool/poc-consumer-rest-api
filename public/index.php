<?php
define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/vendor/autoload.php';


switch ($_SERVER['REQUEST_URI']) {
    case '/live':
        http_response_code(200);
        break;
    case '/':
    default:
        echo 'Welcome';
}
