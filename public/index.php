<?php
// Front controller under public/
// Adjust include paths to point to app directory one level up
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Router.php';

ini_set('display_errors', '1');
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$router = new Router();
$router->dispatch();
