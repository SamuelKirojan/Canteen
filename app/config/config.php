<?php
// Database configuration
// TODO: Update with your actual DB credentials
const DB_HOST = '103.174.115.217';
const DB_NAME = 'canteen';
const DB_USER = 'root';
const DB_PASS = 'SerigalaSigma99@';
const DB_CHARSET = 'utf8mb4';

// App configuration
// Using define() for computed values to avoid constant expression lint warnings
if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', dirname(__DIR__));
}
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__, 2));
}
