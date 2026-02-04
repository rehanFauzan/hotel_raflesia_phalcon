<?php

namespace App\Traits;

use App\Libraries\Log;
use App\Modules\Defaults\Auth\Model\MainModel;
use Core\Facades\Session;

trait HeaderLaporanDynamic
{
    private $systemPdamData = null;
    private $orientationPage = null;

    /**
     * Initialize system PDAM data from the database based on session pdam_id.
     * This method should be called from the consuming class's constructor.
     */
    protected function initSystemPdamData($orientationPage)
    {
        // $pdam_id = Session::get('user')['pdam_id'];

        // if (!$pdam_id) {
            // Handle if pdam_id is not found in session
            // Log::error("pdam_id not found in session for HeaderLaporanDynamic trait.");
        //     echo "DEBUG: pdam_id not found in session for HeaderLaporanDynamic trait.<br>";
        //     return;
        // }

        $this->systemPdamData = MainModel::findFirst([
            'conditions' => 'id = :id:', // Asumsi nama kolom primary key adalah 'id'
            'bind' => ['id' => 13]
        ]);

        $this->orientationPage = $orientationPage;

        if (!$this->systemPdamData) {
            // Log::warning("No system PDAM data found for pdam_id: " . $pdam_id);
            // echo "DEBUG: No system PDAM data found for pdam_id: " . $pdam_id . "<br>";
        } else {
            // echo "DEBUG: System PDAM data found for pdam_id: " . $pdam_id . "<br>";
            // echo "DEBUG: systemPdamData: " . print_r($this->systemPdamData, true) . "<br>";
        }
    }

    // Header Pdf Versi 1
    function Header()
    {
        $versi = $this->getVersiPdam();
        switch ($versi) {
            case '1':
                if ($this->orientationPage == 'L') {
                    $this->_renderHeaderLandscapeV1();
                } else {
                    $this->_renderHeaderPortraitV1();
                }
                break;
            default:
                if ($this->orientationPage == 'L') {
                    $this->_renderHeaderLandscapeV1();
                } else {
                    $this->_renderHeaderPortraitV1();
                }
                break;
        }
    }


    private function _renderHeaderLandscapeV1()
    {
        // Ukuran A4 landscape: 29.7cm x 21cm, margin kiri-kanan 0.5cm
        $pageWidth = 29.7 - 1; // 28.7cm usable width (margin 0.5cm kiri-kanan)
        $leftMargin = 0.5;
        $rightMargin = 0.5;

        // Logo
        $imagePath = BASEPATH . '/public/external_img/' . $this->getImageHeaderPdam();
        $this->Image($imagePath, $this->getImgX(), $this->getImgY(), $this->getImgW(), $this->getImgH());
        // Logo di kiri atas, tinggi 2cm, lebar proporsional
        // $this->Image($imagePath, 5.4, 0.3, 3.3, 3.3);

        // Geser pointer ke kanan setelah logo, untuk header text
        $this->SetXY($leftMargin, 0.7);

        // Nama PDAM (besar, bold, center)
        $this->SetFont('arial', 'B', 18);
        $this->SetTextColor(0, 0, 0);
        // Cell(width, height, text, border, ln, align)
        $this->SetXY($leftMargin, 0.7);
        $this->Cell($pageWidth, 0.9, strtoupper($this->getNamaPdam()), 0, 2, 'C');

        // Alamat (center)
        $this->SetFont('arial', '', 10);
        $this->SetX($leftMargin);
        $this->Cell($pageWidth, 0.6, $this->getAlamatPdam(), 0, 2, 'C');

        // Telp/Fax/Kota (center)
        $this->SetX($leftMargin);
        $telpFax = 'Telp. ' . $this->getNoTelpPdam() . ', Fax. 021-88961608 - ' . $this->getKotaKabPdam();
        $this->Cell($pageWidth, 0.6, $telpFax, 0, 2, 'C');

        // Website (center, biru, underline)
        $this->SetX($leftMargin);
        $this->SetTextColor(0, 0, 255);
        $this->SetFont('arial', 'U', 10);
        $this->Cell($pageWidth, 0.6, 'www.pdamtirtasragen.co', 0, 2, 'C', false, 'https://tirtasragen.co.id');
        $this->SetFont('arial', '', 10);
        $this->SetTextColor(0, 0, 0);

        // Garis bawah tebal & tipis (full width, tepat di bawah header)
        $yLine = $this->GetY() + 0.2;
        $this->SetLineWidth(0.04);
        $this->Line(1.2, $yLine, 28.7 - $rightMargin, $yLine);
        $this->SetLineWidth(0.01);
        $this->Line(1.2, $yLine + 0.15, 28.7 - $rightMargin, $yLine + 0.15);

        // Spasi ke bawah sebelum konten
        $this->Ln(0.8);
    }

    
    private function _renderHeaderPortraitV1()
    {
        $imagePath = BASEPATH . '/public/external_img/' . $this->getImageHeaderPdam();
        $this->Image($imagePath, $this->getImgX(), $this->getImgY(), $this->getImgW(), $this->getImgH());

        $this->SetFont('helvetica', 'B', 19);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(2);
        $this->Cell(0, 0.6, $this->getNamaPdam(), 0, 0, 'C');
        // $this->SetFont('helvetica', 'I', 6);
        // $this->Cell(5.3, 0.5, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
        // $this->Ln();
        // $this->SetTextColor(0, 0, 0);
        // $this->Cell(17, 0.1, '', 0, 'L', 'R', 0);
        // $this->Cell(0, 0, "Dicetak pada : " . date('d-m-Y'), 0, 0, 'L');
        $this->Ln();
        $this->SetFont('helvetica', '', 11);
        $this->Cell(2);
        $this->Cell(0, 0.6, $this->getAlamatPdam(), 0, 0, 'C');
        $this->Ln();
        $this->Cell(2);
        $this->Cell(0, 0.5, 'Telp. ' . $this->getNoTelpPdam() . ", Fax. 021-88961608 - " . $this->getKotaKabPdam() ."", 0, 0, 'C');
        $this->Ln();
        $this->Cell(2);
        $this->SetTextColor(0, 0, 255);
        $this->SetFont('', 'U');
        $this->Cell(0, 0.5, 'www.tirtasragen.co.id', 0, 0, 'C', false, 'https://tirtasragen.co.id');
        $this->SetFont('', '');
        $this->SetTextColor(0, 0, 0);        
        $this->Line(0.5, 3, 20.5, 3);
        $this->Line(0.5, 3.1, 20.5, 3.1);
        $this->Ln(1.5);
    }

    /**
     * Get the 'direktori' from system PDAM data.
     * @return string|null
     */
    public function getDirektoriPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->direktori : null;
    }

    /**
     * Get the 'nama_pdam' from system PDAM data.
     * @return string|null
     */
    public function getNamaPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->nama_pdam : null;
    }

    /**
     * Get the 'nama_aplikasi' from system PDAM data.
     * @return string|null
     */
    public function getNamaAplikasiPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->nama_aplikasi : null;
    }

    /**
     * Get the 'nama_panjang_aplikasi' from system PDAM data.
     * @return string|null
     */
    public function getNamaPanjangAplikasiPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->nama_panjang_aplikasi : null;
    }

    /**
     * Get the 'pemerintah_kota_kab' from system PDAM data.
     * @return string|null
     */
    public function getPemerintahKotaKabPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->pemerintah_kota_kab : null;
    }

    /**
     * Get the 'kota_kab' from system PDAM data.
     * @return string|null
     */
    public function getKotaKabPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->kota_kab : null;
    }

    /**
     * Get the 'alamat' from system PDAM data.
     * @return string|null
     */
    public function getAlamatPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->alamat : null;
    }

    /**
     * Get the 'no_telp_pdam' from system PDAM data.
     * @return string|null
     */
    public function getNoTelpPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->no_telp_pdam : null;
    }

    /**
     * Get the 'email' from system PDAM data.
     * @return string|null
     */
    public function getEmailPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->email : null;
    }

    /**
     * Get the 'latitude' from system PDAM data.
     * @return string|null
     */
    public function getLatitudePdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->latitude : null;
    }

    /**
     * Get the 'longitude' from system PDAM data.
     * @return string|null
     */
    public function getLongitudePdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->longitude : null;
    }

    /**
     * Get the 'image_logo' from system PDAM data.
     * @return string|null
     */
    public function getImageLogoPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->image_logo : null;
    }

    /**
     * Get the 'image_header' from system PDAM data.
     * @return string|null
     */
    public function getImageHeaderPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->image_header : null;
    }

    /**
     * Get the 'image_watermark' from system PDAM data.
     * @return string|null
     */
    public function getImageWatermarkPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->image_watermark : null;
    }

    /**
     * Get the 'image_footer' from system PDAM data.
     * @return string|null
     */
    public function getImageFooterPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->image_footer : null;
    }

    /**
     * Get the 'img_x' from system PDAM data.
     * @return string|null
     */
    public function getImgX()
    {
        return $this->systemPdamData ? $this->systemPdamData->img_x : null;
    }

    /**
     * Get the 'img_y' from system PDAM data.
     * @return string|null
     */
    public function getImgY()
    {
        return $this->systemPdamData ? $this->systemPdamData->img_y : null;
    }

    /**
     * Get the 'img_w' from system PDAM data.
     * @return string|null
     */
    public function getImgW()
    {
        return $this->systemPdamData ? $this->systemPdamData->img_w : null;
    }

    /**
     * Get the 'img_h' from system PDAM data.
     * @return string|null
     */
    public function getImgH()
    {
        return $this->systemPdamData ? $this->systemPdamData->img_h : null;
    }

    /**
     * Get the 'versi' from system PDAM data.
     * @return string|null
     */
    public function getVersiPdam()
    {
        return $this->systemPdamData ? $this->systemPdamData->versi_header : null;
    }
}
