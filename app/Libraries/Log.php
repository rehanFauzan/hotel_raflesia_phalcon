<?php

namespace App\Libraries;

use App\Models\LogModel;
use Core\Facades\DB;

class Log
{
    protected $requireAuth = true;

    // public static function write($message, $data_before = NULL, $data_after = NULL, $result = NULL, $controller = "", $action = "")
    // {
    //     $session    = \Phalcon\Di::getDefault()->getShared('session');
    //     $request    = \Phalcon\Di::getDefault()->getShared('request');
    //     $session    = (object) $session->get('user');
    //     $ip         = (object) $request->getClientAddress();
    //     $url        = (object) $request->getURI();

    //     DB::insert(
    //         'system_log_akses',
    //         array(
    //             $session->id,
    //             $session->username,
    //             $session->nama,
    //             $ip->scalar,
    //             $_SERVER['SERVER_NAME'] . $url->scalar,
    //             $message,
    //             json_encode($data_before),
    //             json_encode($data_after),
    //             json_encode($result),
    //             $controller,
    //             $action
    //         ),
    //         array(
    //             "id_user",
    //             "username",
    //             "name",
    //             "ip",
    //             "url",
    //             "message",
    //             "data_before",
    //             "data_after",
    //             "response",
    //             "controller",
    //             "action"
    //         )
    //     );
    // }

    public static function write($message, $data_before = NULL, $data_after = NULL, $result = NULL, $controller = "", $action = "")
    {
        $session = \Phalcon\Di::getDefault()->getShared('session');
        $request = \Phalcon\Di::getDefault()->getShared('request');
        $session = (object) $session->get('user');
        $ip      = (object) $request->getClientAddress();
        $url     = (object) $request->getURI();

        $timestamp = date('Y-m-d H:i:s');
        $logData = [
            'timestamp'   => $timestamp,
            'user_id'     => $session->id,
            'username'    => $session->username,
            'name'        => $session->nama,
            'ip'          => $ip->scalar,
            'url'         => $_SERVER['SERVER_NAME'] . $url->scalar,
            'message'     => $message,
            'data_before' => $data_before,
            'data_after'  => $data_after,
            'response'    => $result,
            'controller'  => $controller,
            'action'      => $action
        ];
        
        // Simpan ke database
        DB::insert(
            'system_log_akses',
            array(
                $logData['user_id'],
                $logData['username'],
                $logData['name'],
                $logData['ip'],
                $logData['url'],
                $logData['message'],
                json_encode($logData['data_before']),
                json_encode($logData['data_after']),
                json_encode($logData['response']),
                $logData['controller'],
                $logData['action']
            ),
            array(
                "id_user",
                "username",
                "name",
                "ip",
                "url",
                "message",
                "data_before",
                "data_after",
                "response",
                "controller",
                "action"
            )
        );

        // Simpan ke file log (per hari)
        $logFolder = APP_PATH . '/storage/logs';
        if (!is_dir($logFolder)) {
            mkdir($logFolder, 0755, true);
        }

        $logFile = $logFolder . '/access-log-' . date('Y-m-d') . '.txt';

        $logText = "[$timestamp] {$logData['username']} ({$logData['ip']}) - {$message}\n";
        $logText .= "URL       : {$logData['url']}\n";
        $logText .= "Controller: {$controller} | Action: {$action}\n";
        $logText .= "Before    : " . json_encode($data_before, JSON_PRETTY_PRINT) . "\n";
        $logText .= "After     : " . json_encode($data_after, JSON_PRETTY_PRINT) . "\n";
        $logText .= "Response  : " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
        $logText .= str_repeat("-", 80) . "\n";

        file_put_contents($logFile, $logText, FILE_APPEND);
    }
}
