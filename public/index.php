<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Phalcon\Di;
use Sentry\SentrySdk;

define('BASEPATH', realpath(__DIR__ . '/..'));
define('ENVIRONMENT', 'development');

require_once BASEPATH . '/vendor/autoload.php';

function env($key = null, $fallback = null)
{
    if (!$key)
        return $_ENV;
    return isset($_ENV[$key]) ? $_ENV[$key] : $fallback;
}

try {
    ob_start();
    Core\Kernel::boot()->run();
    ob_end_flush();
} catch (PDOException $ex) {
    while (ob_get_level() > 1) ob_end_clean();
    
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/html');
    echo '<pre>';
    echo 'Database Error: ' . $ex->getMessage() . "\n";
    echo 'File: ' . $ex->getFile() . "\n";
    echo 'Line: ' . $ex->getLine() . "\n";
    echo 'Trace: ' . $ex->getTraceAsString();
    echo '</pre>';
} catch (\Exception $ex) {
    while (ob_get_level() > 1) ob_end_clean();
    
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/html');
    echo '<pre>';
    echo 'Application Error: ' . $ex->getMessage() . "\n";
    echo 'File: ' . $ex->getFile() . "\n";
    echo 'Line: ' . $ex->getLine() . "\n";
    echo 'Trace: ' . $ex->getTraceAsString();
    echo '</pre>';
}