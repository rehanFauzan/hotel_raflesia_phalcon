<?php

namespace App\Libraries;

class WhatsappNotification {
	
  public static function sendNotif($nomor, $text){
    $curl = curl_init();

    // curl_setopt_array($curl, array(
    //     CURLOPT_RETURNTRANSFER => 1,
    //     CURLOPT_URL => 'http://api.nusasms.com/api/v3/sendwa/plain',
    //     CURLOPT_POST => true,
    //     CURLOPT_POSTFIELDS => array(
    //         'username' => "charismapersada_api",
    //         'password' => "Chpn123456!",
    //         'text' => $text,
    //         'GSM' => $nomor,
    //         'output' => 'json'
    //     )
    // ));
    $payload = array(
              'destination' => $nomor,
              'message' => $text,
    );
    $payloadJson = json_encode($payload);
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.nusasms.com/nusasms_api/1.0/whatsapp/message',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $payloadJson,
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'APIKey: ABA069E1623F167DCBA37DEB2F6F1FAD',
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);


    $userkey = "90x54m"; //userkey lihat di zenziva
    $passkey = "b1sm1l4h!"; // set passkey di zenziva

    $url = "https://masking.zenziva.net/api/sendsms/";
    $curlHandle = curl_init();
    curl_setopt($curlHandle, CURLOPT_URL, $url);
    curl_setopt($curlHandle, CURLOPT_HEADER, 0);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
    curl_setopt($curlHandle, CURLOPT_POST, 1);
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
      'userkey' => $userkey,
      'passkey' => $passkey,
      'nohp' => $nomor,
      'pesan' => $text
    ));
    $results = json_decode(curl_exec($curlHandle), true);
    curl_close($curlHandle);

    return $response;
  }

  public static function send($nomor, $text){
    
    $curl = curl_init();
    $payload = array(
              'destination' => $nomor,
              'message' => $text,
    );
    $payloadJson = json_encode($payload);
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://devcharisma.aurorasystem.co.id/whatsapp-api/whatsapp/message',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $payloadJson,
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Secret-Key: ce73fb63f8c0e1051f13f0bf4b08cff1c58b39a4c8502618e1cf0272ae9a0fb6',
        'Content-Type: application/json'
      ),
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);

   
    // $userkey = "90x54m"; //userkey lihat di zenziva
    // $passkey = "b1sm1l4h!"; // set passkey di zenziva

    // $url = "https://masking.zenziva.net/api/sendsms/";
    // $curlHandle = curl_init();
    // curl_setopt($curlHandle, CURLOPT_URL, $url);
    // curl_setopt($curlHandle, CURLOPT_HEADER, 0);
    // curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
    // curl_setopt($curlHandle, CURLOPT_TIMEOUT, 30);
    // curl_setopt($curlHandle, CURLOPT_POST, 1);
    // curl_setopt($curlHandle, CURLOPT_POSTFIELDS, array(
    //   'userkey' => $userkey,
    //   'passkey' => $passkey,
    //   'nohp' => $nomor,
    //   'pesan' => $text
    // ));
    // $results = json_decode(curl_exec($curlHandle), true);
    // curl_close($curlHandle);

    return $response;
  }
}
