<?php
class QRcodeGenerator
{
    public function create($text)
    {
        include "qrcode/qrlib.php";
        $tempdir = "qrcode/";
        $file_name = $text . ".png";
        $file_path = $tempdir . $file_name;
        QRcode::png($text, $file_path, "H", 10, 2);
        return $file_path;
    }

    public function getCode($url,$text)
    {
       
        require_once "qrcode/qrlib.php";
        $tempdir = "qrcode/";
        $file_name = $text . ".png";
        $file_path = $tempdir . $file_name;
        QRcode::png($url, $file_path, "H", 10, 2);
        return $file_path;
    }
}
