<?php
namespace App\Libraries;

class FirebaseNotification {

    protected static $url = 'https://fcm.googleapis.com/fcm/send';
    protected static $key = 'AAAAJEy32eY:APA91bHQnW64maXXZw8x26hzfeQmVaKNOV8KUa8R3YBa3O5KqKoCCyO8h1fSy0g0FbRle8K5XKv_HEYRDQ5JpYRC7Fwo4gX6tx_U1YKy1USXtxdub4ykgD3JsxBlpJPxz-HkI4h6uJvC';

    public function send($device_token, $id_surat , $title, $message) {
        $data['title'] = $title;
        $data['body'] = $message;
        $data['id'] = $id_surat;
        
        $payload = [
            // 'to' => '/topics/' . $topic,
            // 'registration_ids' => $device_token,
            'to' => $device_token,
            // 'priority' => $priority,
            // 'notification' => [
            //     'title' => $title,
            //     'body' => $message,
            // ],
            'data' => $data
        ];

        return $this->sendJson($payload);
    }

    public function sendJson($payload) {
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, static::$url);

        // set header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: key=" .  static::$key
        ));

        $payloadJson = json_encode($payload);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $payload;
    }
}