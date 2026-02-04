<?php
namespace App\Libraries;
use Rathouz\TextImage;
use Rathouz\Tools\Objects;

class SignDigital {
	
  public static function getSigndigital($text,$id_calon_pelanggan)
	{
    
		$basicImage = new TextImage\TextImage($text);
		$basicImage->setFormat(Objects\Image::JPG);
		$basicImage->setFontPath('assets/fonts/Parisienne-Regular.ttf');
		$basicImage->setFontSize(30);
		$basicImage->setWidth(100);
		$image = $basicImage->generate();

		$img = 'ttd_digital/' . $id_calon_pelanggan .'_' .$text. '.jpg';
		
		if(file_exists($img)){
			unlink($img);
		}

		
		if (!file_exists($img)) {
			file_put_contents($img, file_get_contents($image->getImagePath()));
		}

		return $img;
  }
}
