<?php

namespace App\Libraries;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Alignment\LabelAlignmentRight;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Intervention\Image\ImageManagerStatic as Image;
use Imagick;
use ImagickPixel;

class QrCodeGeneratorv2
{
    /**
     * Generate QR Code dan simpan sebagai file PNG.
     *
     * Fungsi ini akan membuat QR Code berdasarkan data yang diberikan,
     * lalu menyimpannya ke dalam folder yang ditentukan dengan nama file sesuai $lastIdTabelTerkait.
     * Jika file dengan nama yang sama sudah ada, maka akan dihapus terlebih dahulu.
     *
     * @param string $lastIdTabelTerkait   ID unik yang digunakan sebagai nama file QR Code (tanpa ekstensi).
     * @param string $keteranganQrCode     Data atau keterangan yang akan di-encode ke dalam QR Code.
     * @param string $simpanFileKe         Nama folder tujuan penyimpanan file QR Code (di dalam /public/uploadsImage/).
     * @return array                       Array berisi status error dan nama file hasil generate.
     */

    public static function generateQrCode($lastIdTabelTerkait, $keteranganQrCode, $simpanFileKe)
    {
        // Ini Di Nonaktifkan Karena Tidak Menggunakan URL
        // $baseURL = sprintf(
        //     "%s://%s%s",
        //     @$_SERVER['HTTPS'] == 'on' ? 'https' : 'http',
        //     ($_SERVER['SERVER_PORT'] != "80") ? $_SERVER['HTTP_HOST'] . ":" . $_SERVER['SERVER_PORT'] : $_SERVER['HTTP_HOST'],
        //     str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME'])
        // );
        // $dataQR = $baseURL . 'panel/master/asset/detail/' . $id . '?kode=' . $kode_asset;

        $currentDir = BASEPATH;
        $flexiblePath = $currentDir . '/public/uploadsImage/' . $simpanFileKe;
        if (!is_dir($flexiblePath)) {
            mkdir($flexiblePath, 0777, true);
        }

        $dataQR = $keteranganQrCode;

        if (file_exists($flexiblePath . '/' . $lastIdTabelTerkait . '.png')) {
            unlink($flexiblePath . '/' . $lastIdTabelTerkait . '.png');
        }

        $fullPath = $flexiblePath . '/' . $lastIdTabelTerkait . '.png';

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($dataQR)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(2)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->validateResult(false)
            ->build();
        $result->saveToFile($fullPath);

        if (file_exists($fullPath)) {
            $jason['error'] = 0;
            $jason['filename'] = $lastIdTabelTerkait . '.png';
        } else {
            $jason['error'] = 1;
            $jason['filename'] = 'gagal';
        }
        return $jason;
    }
}
