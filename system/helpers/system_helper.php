<?php

declare(strict_types=1);

namespace Core\Helpers;
use Core\Paths;
use josegonzalez\Dotenv\Loader;

if(! function_exists('load_env'))
{
    function load_env()
    {
        $root = BASEPATH;

        $files = [
            "$root/.env",
            "$root/.env.local",
        ];

        $mode = defined('ENVIRONMENT') ? ENVIRONMENT : 'development';
        if ($mode) {
            $files[] = "$root/.env.$mode";
            $files[] = "$root/.env.$mode.local";
        }

        return (new Loader($files))
            ->parse()
            ->toEnv();
    }
}

if(! function_exists('Core\\Helpers\\load_config'))
{
    function load_config($configName)
    {
        $config = [];

        $load = function($path) {
            $config = require $path;
            assert(is_array($config), "Config file '{$path}' must return array");
            return $config;
        };

        $configFiles = get_env_aware_files(Paths::Config . "/{$configName}.php");

        foreach($configFiles as $filename) {
            $config = array_merge_recursive($config, $load($filename));
        }

        return $config;
    }
}

if(! function_exists('Core\\Helpers\\get_env_aware_files'))
{
    function get_env_aware_files($path, $env = null)
    {
        $files = [];

        $lastSlash = strrpos($path, '/');
        $filename = substr($path, $lastSlash);
        $lastDot = strrpos($filename, '.');

        if(file_exists($path) && is_file($path))
            array_push($files, $path);
        
        $env = $env ?? (defined('ENVIRONMENT') ? ENVIRONMENT : false);

        if($env) {
            $file = substr($path, 0, $lastSlash + $lastDot) . '.' . $env . substr($filename, $lastDot);
            if(file_exists($file) && is_file($file))
                array_push($files, $file);

            $file = substr($path, 0, $lastSlash) . '/' . $env . $filename;
            if(file_exists($file) && is_file($file))
                array_push($files, $file);
        }

        return $files;
    }
}