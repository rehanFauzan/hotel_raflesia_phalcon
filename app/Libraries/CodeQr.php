<?php
namespace App\Libraries;
use Endroid\QrCode\QrCode;

class CodeQr {
	
  public static function getKode($url,$kode)
	{
		$qr = new QrCode();
		$qr ->setText($url)
        ->setSize(200)
        ->setPadding(10)
        ->setErrorCorrection('high')
        ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
        ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
        ->setLabel($kode)
        ->setLabelFontSize(15);

		$img = 'qrcode/'  .$kode. '.jpg';

		if(file_exists($img)){
			unlink($img);
		}
		
		$qr->render($img,'jpg');
		
		return $img;	

		
  }
}
