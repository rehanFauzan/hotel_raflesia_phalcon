<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting debug...\n";

define('BASEPATH', realpath(__DIR__));
define('ENVIRONMENT', 'development');

echo "BASEPATH: " . BASEPATH . "\n";

require_once BASEPATH . '/vendor/autoload.php';
echo "Autoloader loaded\n";

function env($key = null, $fallback = null)
{
    if (!$key)
        return $_ENV;
    return isset($_ENV[$key]) ? $_ENV[$key] : $fallback;
}

try {
    echo "Trying to boot kernel...\n";
    Core\Kernel::boot();
    echo "Kernel booted successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>